<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\Compiler;

use Junco\Extensions\Components;
use Junco\Extensions\XData\XDataManager;
use Filesystem;
use Database;
use Exception;
use Plugins;
use DatabaseExporter;
use Archive;

class Compiler
{
    const STORAGE_NAME_FORMAT       = 0;
    const DISTRIBUTION_NAME_FORMAT  = 1;
    //
    const OUTPUT_FOLDER = 0;
    const OUTPUT_FILE   = 1;
    const OUTPUT_BOTH   = 2;

    // vars
    protected Database   $db;
    protected Filesystem $fs;
    protected Components $components;
    protected array      $config;
    //
    protected array  $json          = [];
    protected array  $package       = [];
    protected array  $extensions    = [];

    // package type
    protected bool   $is_system     = false;
    protected bool   $is_installer  = false; // In case the system is being compiled, this changes the packaging to an installer.
    protected bool   $has_queries   = false; // This activates the functions to export queries.

    // directories
    protected string $abspath       = '';
    protected string $dstpath       = ''; // Destination directory
    protected string $inspath       = ''; // This directory contains the installer and / or installation files.
    protected string $syspath       = '';
    protected string $sqlpath       = ''; // The database structures are stored in this directory.

    // settings
    public    int    $package_name_format = self::STORAGE_NAME_FORMAT;
    public    int    $output              = self::OUTPUT_FOLDER;
    public    bool   $get_install_package = false;
    public    array  $plugins             = [];

    /**
     *  Construct
     */
    public function __construct()
    {
        $this->db           = db();
        $this->fs           = new Filesystem('');
        $this->components   = new Components();
        $this->config       = config('extensions');
        $this->abspath      = SYSTEM_ABSPATH;
    }

    /**
     * Compile
     * 
     * @param int $package_id
     * 
     * @return void
     */
    public function compile(int $package_id): void
    {
        $this->package = $this->getPackage($package_id);

        // settings
        $this->is_system = ($this->package['extension_alias'] == 'system');
        $this->is_installer = ($this->is_system && $this->get_install_package);
        $this->json = [
            'developer'       => $this->getDeveloper($this->package['developer_id']),
            'package_alias'   => $this->package['extension_alias'],
            'min_php_version' => $this->config['extensions.min_php_version'],
            'max_php_version' => $this->config['extensions.max_php_version'],
            'summary'         => [],
            'extensions'      => []
        ];

        // extensions
        $this->extensions = $this->getExtensions($this->package['id'], $this->package['developer_id']);

        // take
        $this->makeDirectories();
        $this->runBeforePlugins();
        $this->compileExtensions();

        // system
        if ($this->is_system) {
            $this->compileSystem();
        }

        // finalize
        $this->saveChangelog();
        $this->saveInstallFile();
        $this->saveReadme();
        $this->moveExecutables();

        if ($this->isFileAOutput($this->output)) {
            $this->compressPackage();
        }

        if (!$this->isFolderAOutput($this->output)) {
            $this->remove();
        }
    }

    /**
     * Get
     */
    protected function getPackage(int $package_id): array
    {
        $package = $this->db->query("
		SELECT
		 id ,
		 developer_id ,
		 extension_alias ,
		 extension_name AS name,
		 extension_version AS version
		FROM `#__extensions`
		WHERE id = ?", $package_id)->fetch() or abort();

        return $package;
    }

    /**
     * Get developer
     * 
     * @param int $developer_id
     * 
     * @return array
     */
    protected function getDeveloper(int $developer_id): array
    {
        $developer = $this->db->query("
		SELECT
		 developer_name ,
		 project_url ,
		 webstore_url
		FROM `#__extensions_developers`
		WHERE id = ?", $developer_id)->fetch() or abort();

        return $developer;
    }

    /**
     * Get extensions
     * 
     * @param int $package_id
     * @param int $developer_id
     * 
     * @return array
     */
    protected function getExtensions(int $package_id, int $developer_id): array
    {
        $summary = [
            'extension_version' => [],
            'extension_credits' => [],
            'extension_license' => [],
            'extension_require' => [],
        ];

        $extensions = $this->db->query("
		SELECT
		 id ,
		 extension_alias ,
		 extension_name ,
		 extension_version ,
		 extension_credits ,
		 extension_license ,
		 extension_abstract ,
		 extension_require ,
		 components ,
		 db_queries ,
		 db_history ,
		 xdata
		FROM `#__extensions`
		WHERE ( id = ? OR package_id = ? )
		AND developer_id = ?
		ORDER BY extension_name", $package_id, $package_id, $developer_id)->fetchAll(Database::FETCH_ASSOC, 'id');

        if ($this->is_system && !$this->is_installer) {
            $this->removeInstall($extensions);
        }

        foreach ($extensions as $extension_id => $extension) {
            unset($extension['id']);

            // validate & prepare summary
            foreach ($summary as $key => $value) {
                if (!$extension[$key]) {
                    throw new Exception(sprintf(_t('In the extension «%s», the install value «%s» is empty.'), $extension['extension_alias'], $key));
                }

                if (!in_array($extension[$key], $value)) {
                    $summary[$key][] = $extension[$key];
                }
            }
            if ($extension['db_queries']) {
                $this->has_queries = true;
            }

            $extensions[$extension_id] = $extension;
        }

        // I save the summary in the json
        foreach ($summary as $key => $value) {
            $this->json['summary'][$key] = implode(', ', $value);

            // I recycle the summary - save $has_one
            $summary[$key] = count($value) == 1;
        }

        // I save the extensions data in the json
        foreach ($extensions as $extension) {
            $data = $extension;

            foreach ($summary as $key => $has_one) {
                if ($has_one) {
                    unset($data[$key]);
                }
            }

            if ($data['extension_name'] == $data['extension_alias']) {
                unset($data['extension_name']);
            }
            if (!$data['db_queries']) {
                unset($data['db_queries']);
            }
            if (!$data['xdata']) {
                unset($data['xdata']);
            }

            unset($data['db_history']);
            unset($data['extension_alias']);

            $this->json['extensions'][$extension['extension_alias']] = $data;
        }

        return $extensions;
    }

    /**
     * Remove install
     * 
     * @param array &$extensions
     * 
     * @return void
     */
    protected function removeInstall(array &$extensions): void
    {
        $extension_id = 0;

        foreach ($extensions as $extension) {
            if ($extension['extension_alias'] == 'install') {
                $extension_id = $extension['id'];
                break;
            }
        }

        if ($extension_id) {
            unset($extensions[$extension_id]);
        }
    }

    /**
     * Make destination folders
     * 
     * @return void
     */
    protected function makeDirectories(): void
    {
        $this->dstpath = SYSTEM_STORAGE . $this->config['extensions.compiler_path'] . $this->getFolderName() . '/';
        $this->inspath = $this->is_installer
            ? $this->dstpath . 'app/install/'
            : $this->dstpath;

        // mkdir
        $this->fs->mkdir($this->dstpath);

        if ($this->is_system) {
            $this->syspath = $this->dstpath . ($this->is_installer ? '' : 'system/');
            $this->fs->mkdir($this->syspath);
        }

        if ($this->has_queries) {
            $this->sqlpath = $this->inspath . $this->config['extensions.sql_path'];
            $this->fs->mkdir($this->sqlpath);
        }
    }

    /**
     * Get
     * 
     * @return string
     */
    protected function getFolderName(): string
    {
        if ($this->package_name_format == self::DISTRIBUTION_NAME_FORMAT) {
            if ($this->is_installer) {
                return sprintf('JuncoCMS_%s', $this->package['version']);
            }

            return sprintf('%s_%s', $this->package['extension_alias'], $this->package['version']);
        }

        return sprintf('%s %s_%s', date('Y-m-d', time()), $this->package['name'], $this->package['version']);
    }

    /**
     * Run Before Plugins
     * 
     * @return void
     */
    protected function runBeforePlugins(): void
    {
        if ($this->plugins) {
            $result = true;
            Plugins::get('compiler', 'before', $this->plugins)?->run($result, $this->extensions, $this->package['id']);
        }
    }

    /**
     * Compile
     * 
     * @return void
     */
    protected function compileExtensions(): void
    {
        $xdm = new XDataManager($this->dstpath, $this->is_installer);

        foreach ($this->extensions as $extension_id => $row) {
            if ($row['db_queries']) {
                $this->compileDatabase($row['db_queries'], $row['extension_alias'], $row['db_history'] ?? '');
            }

            if ($row['components']) {
                $this->compileFiles($row['components'], $row['extension_alias']);
            }

            if ($row['xdata']) {
                $xdm->add($row['xdata'], $row['extension_alias'], $extension_id);
            }
        }

        $xdm->exec('export');
    }

    /**
     * Export Database
     * 
     * @param string $db_queries
     * @param string $extension_alias
     * @param string $db_history
     * 
     * @return void
     */
    protected function compileDatabase(string $db_queries, string $extension_alias, string $db_history): void
    {
        $export = new DatabaseExporter();
        $export->set_db_prefix      = true;
        $export->add_drop_routine   = true;
        $export->add_drop_table     = false;
        $export->add_if_not_exists  = true;
        $export->add_auto_increment = false;
        $export->use_ignore         = true;

        foreach (explode(',', $db_queries) as $cmd) {
            $cmd  = explode(':', trim($cmd));
            $Name = $cmd[1] ?? $cmd[0];
            $Type = isset($cmd[1]) ? $cmd[0] : 'TABLE';

            switch ($Type) {
                case 'ROWS':
                case 'TABLE':
                    $export->addTable($Name);
                    break;

                case 'TRIGGER':
                    $export->addTrigger($Name);
                    break;

                case 'PROCEDURE':
                case 'FUNCTION':
                    $export->addRoutine($Type, $Name);
                    break;

                default:
                    throw new Exception(sprintf('In the extension «%s», one of the parameters db_queries is unknown', $extension_alias));
            }
        }

        $jsdata = $export->getAsJS();
        $sqldata = $export->getAsMysql();

        if ($db_history) {
            $jsdata->addHistory($db_history);
        }

        $jsdata->write($this->sqlpath . $extension_alias . '.json');
        $sqldata->write($this->sqlpath . $extension_alias . '.sql');
    }

    /**
     * Compile Files
     * 
     * @param string $components
     * @param string $extension_alias
     * 
     * @return void
     */
    protected function compileFiles(string $components, string $extension_alias): void
    {
        $directories = $this->components->fetchAll($extension_alias, $components);

        foreach ($directories as $key => $dir) {
            $src = $this->abspath . $dir['local'];
            $dst = $this->dstpath . $dir['source'];

            if (in_array($key, ['m', 'v'])) {
                $this->fs->copyDirWithIgnores($src, $dst);
            } else {
                $this->fs->copy($src, $dst);
            }
        }
    }

    /**
     * Compile system
     */
    protected function compileSystem(): void
    {
        $system = $this->str2Arr($this->config['extensions.system_extra']);

        foreach ($system as $node) {
            $this->fs->copy($this->abspath . $node, $this->syspath . $node);
        }

        if ($this->is_installer) { // copy initial files
            foreach ($this->str2Arr($this->config['extensions.system_install_extra']) as $node) {
                $this->fs->copy(
                    $this->abspath . ($node == 'bootstrap.php' ? '_bootstrap.php' : $node),
                    $this->syspath . $node
                );
            }
        } else {
            $this->json['system_path'] = $this->config['extensions.system_path'];
            $this->json['system'] = $system;
        }
    }

    /**
     * String to array
     */
    protected function str2Arr(string $input): array
    {
        return array_filter(
            explode(',', str_replace(["\s", "\t", "\n", "\r"], '', $input))
        );
    }

    /**
     * Save changelog
     */
    protected function saveChangelog(): void
    {
        // query -> changelog
        $rows = $this->db->query("
		SELECT
		 extension_id ,
		 change_description
		FROM `#__extensions_changes`
		WHERE extension_id IN (?..)
		AND status = 1", array_keys($this->extensions))->fetchAll();

        $changelog = [];
        $current_id = 0;

        foreach ($rows as $row) {
            if ($current_id != $row['extension_id']) {
                $current_id  = $row['extension_id'];
                $extension   = $this->extensions[$row['extension_id']];
                //
                $changelog[] = '';
                $changelog[] = $extension['extension_name'] . ' v' . $extension['extension_version'];
                $changelog[] = '=======================================================';
            }

            $changelog[] = '- ' . $row['change_description'];
        }

        // save
        $file   = $this->inspath . 'CHANGELOG.TXT';
        $buffer = implode(PHP_EOL, $changelog);

        if (false === file_put_contents($file, $buffer)) {
            throw new Exception(_t('Error! the task has not been realized.'));
        }
    }

    /**
     * Save Install Json
     */
    protected function saveInstallFile(): void
    {
        $file   = $this->inspath . $this->config['extensions.install_file'];
        $buffer = json_encode($this->json, JSON_PRETTY_PRINT);

        if (false === file_put_contents($file, $buffer)) {
            throw new Exception(_t('Error! the task has not been realized.'));
        }
    }

    /**
     * Save readme
     * 
     * @return void
     */
    protected function saveReadme(): void
    {
        $file = 'README.html';
        $from = SYSTEM_STORAGE . sprintf('readme/%s/%s', $this->package['extension_alias'], $file);

        is_file($from)
            and $this->fs->copy($from, $this->inspath . $file);
    }

    /**
     * Executables
     * 
     * @return void
     */
    protected function moveExecutables(): void
    {
        $aliases  = array_column($this->extensions, 'extension_alias');
        $fromPath = SYSTEM_STORAGE . 'executables/';
        $toPath   = $this->inspath . 'executables/';

        foreach ($aliases as $alias) {
            $from = $fromPath . $alias;

            is_dir($from)
                and $this->fs->copy($from, $toPath . $alias);
        }
    }

    /**
     * Is
     * 
     * @return bool
     */
    protected function isFileAOutput(int $output): bool
    {
        return in_array($output, [self::OUTPUT_FILE, self::OUTPUT_BOTH]);
    }

    /**
     * Is
     * 
     * @return bool
     */
    protected function isFolderAOutput(int $output): bool
    {
        return in_array($output, [self::OUTPUT_FOLDER, self::OUTPUT_BOTH]);
    }

    /**
     * Compress package
     * 
     * @return void
     */
    protected function compress(): void
    {
        $file = rtrim($this->dstpath, '\\/') . '.zip';
        $archive = new Archive('');

        if ($this->is_installer) {
            $archive->compress($file, $this->dstpath);
        } else {
            $info = pathinfo($file);
            $dir = $info['dirname'] . '/';
            $folder = $info['filename'];

            $archive->compress($file, $dir, [$folder]);
        }
    }

    /**
     * Remove
     * 
     * @return void
     */
    protected function remove(): void
    {
        $this->fs->remove($this->dstpath);
    }

    /**
     * Compress package
     * 
     * @return void
     */
    protected function compressPackage(): void
    {
        $file    = rtrim($this->dstpath, '\\/') . '.zip';
        $archive = new Archive('');

        if ($this->is_installer) {
            $archive->compress($file, $this->dstpath);
        } else {
            $info   = pathinfo($file);
            $dir    = $info['dirname'] . '/';
            $folder = $info['filename'];

            $archive->compress($file, $dir, [$folder]);
        }
    }

    /**
     * Get
     * 
     * @return array
     */
    public static function getOutputs(): array
    {
        return [
            self::OUTPUT_FOLDER => _t('Folder'),
            self::OUTPUT_FILE => _t('File'),
            self::OUTPUT_BOTH => _t('Both'),
        ];
    }

    /**
     * Get
     * 
     * @return array
     */
    public static function nameFormats(): array
    {
        return [
            self::STORAGE_NAME_FORMAT => _t('Storage'),
            self::DISTRIBUTION_NAME_FORMAT => _t('Distribution')
        ];
    }
}
