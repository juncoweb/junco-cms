<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\Installer;

use Junco\Extensions\Components;
use Junco\Extensions\Extensions;
use Junco\Database\Schema\Interface\SchemaInterface;
use Database;
use Exception;
use Junco\Extensions\Enum\UpdateStatus;

class Unpackager
{
    // const
    protected const IMPORT_FROM_SQL         = 0;
    protected const IMPORT_FROM_JSON        = 1;
    protected const IMPORT_FROM_JSON_MIRROR = 2;
    protected const NOT_IMPORT              = 3;

    // conf.
    public bool $debug      = false;
    public bool $copy_files = true;

    // vars
    protected Database $db;
    protected SchemaInterface $schema;
    protected Components $components;
    protected bool   $is_installer;
    protected string $dstpath;
    protected string $srcpath       = '';
    protected string $mainpath      = '';
    protected array  $error         = [];
    protected bool   $error_fatal   = false;
    //
    protected array $developer = [
        'developer_name' => '',
        'project_url'    => '',
        'webstore_url'   => '',
    ];
    protected array $summary = [
        'extension_version' => '',
        'extension_credits' => '',
        'extension_license' => '',
        'extension_require' => '',
    ];
    protected int    $developer_id      = 0;
    protected bool   $update_developer  = false;
    protected array  $extensions_1      = [];
    protected array  $extensions_2      = [];
    protected array  $maj_version       = [];
    protected bool   $has_system        = false;
    protected array  $system            = [];
    protected string $syspath           = '';
    protected array  $default_sequence  = ['f', 's', 'd'];
    protected array  $sequence          = [];

    /**
     * Constructor
     * 
     * @param bool $is_installer
     */
    public function __construct(bool $is_installer = false)
    {
        $this->db           = db();
        $this->schema       = $this->db->getSchema();
        $this->components   = new Components();
        $this->is_installer = $is_installer;
        $this->dstpath      = SYSTEM_ABSPATH;
    }

    /**
     * Unpack
     * 
     * @param string $package
     * 
     * @return bool
     */
    protected function unpack(string $package): bool
    {
        if (!$this->setPath($package)) {
            return false;
        }

        $json = $this->getJsonContent();
        if ($json === false) {
            return false;
        }

        $this->setDeveloper($json['developer']);
        $this->setSummary($json['summary']);

        if (!$this->setExtensions($json['extensions'])) {
            return false;
        }

        // security
        $this->security();

        // System
        if ($this->has_system && !$this->is_installer) {
            $this->setSystem($json);
        }

        // Sequence
        $this->sequence = $this->setSequence($json['sequence'] ?? []);

        return true;
    }

    /**
     * Set path
     * 
     * @param string $package
     * 
     * @return bool
     */
    protected function setPath(string $package): bool
    {
        if ($this->is_installer) {
            $this->copy_files = false;
            $this->srcpath    = $this->dstpath;
            $this->mainpath   = $this->dstpath . 'app/install/';
        } else {
            if (!$package) {
                return $this->fatal('The package is empty.');
            }

            $this->srcpath =
                $this->mainpath = SYSTEM_STORAGE . config('extensions.installer_path') . $package . '/';
        }

        return true;
    }

    /**
     * Get Json Content
     * 
     * @return array|false
     */
    protected function getJsonContent(): array|false
    {
        $file = $this->mainpath . config('extensions.install_file');
        if (!is_file($file)) {
            return $this->fatal('The installation file was not found.');
        }

        $json = file_get_contents($file);
        if (!$json) {
            return $this->fatal('Failed to read the installation file.');
        }

        $json = json_decode($json, true);
        if (!$json) {
            return $this->fatal('Failed to decode the installation file.');
        }
        if (!isset($json['developer'])) {
            return $this->fatal(_t('The install value «%s» is empty.'), 'developer');
        }
        if (!isset($json['summary'])) {
            return $this->fatal(_t('The install value «%s» is empty.'), 'summary');
        }
        if (!isset($json['extensions'])) {
            return $this->fatal(_t('The install value «%s» is empty.'), 'extensions');
        }
        if (
            isset($json['min_php_version'])
            && version_compare($json['min_php_version'], PHP_VERSION) == 1
        ) {
            return $this->fatal(_t('The extension requires a php version equal to or greater than «%s»'), $json['min_php_version']);
        }
        if (
            !empty($json['max_php_version'])
            && version_compare($json['max_php_version'], PHP_VERSION) < 1
        ) {
            return $this->fatal(_t('The extension requires a php version lower than «%s»'), $json['max_php_version']);
        }

        return $json;
    }

    /**
     * Set Developer
     * 
     * @param array $developer
     * 
     * @return void
     */
    protected function setDeveloper(array $developer): void
    {
        foreach (array_keys($this->developer) as $key) {
            $value = $developer[$key] ?? null;

            if (!$value) {
                $this->fatal(_t('The install value «%s» is empty.'), $key);
            }

            $this->developer[$key] = $value;
        }
    }

    /**
     * Set summary
     * 
     * @param array $summary
     * 
     * @return void
     */
    protected function setSummary(array $summary): void
    {
        foreach (array_keys($this->summary) as $key) {
            $value = $summary[$key] ?? null;

            if (!$value) {
                $this->fatal(_t('The install value «%s» is empty.'), $key);
            }

            $this->summary[$key] = $value;
        }
    }

    /**
     * Set Extensions
     */
    protected function setExtensions(array $extensions): bool
    {
        $this->extensions_1 = [];
        $this->extensions_2 = [];
        $this->maj_version = [];
        $baseRow = [
            'extension_alias'       => '',
            'extension_name'        => '',
            'extension_version'     => $this->summary['extension_version'],
            'extension_credits'     => $this->summary['extension_credits'],
            'extension_license'     => $this->summary['extension_license'],
            'extension_abstract'    => '',
            'extension_require'     => $this->summary['extension_require'],
            'components'            => '',
            'db_queries'            => '',
            'db_history'            => false,
            'xdata'                 => '',
        ];
        $pk_version  = [];
        $db_version  = $this->getCurrentVersions();

        // prepare
        foreach ($extensions as $alias => $row) {
            $row['extension_alias'] = $alias;
            $row['status'] = -1;

            if (empty($row['extension_name'])) {
                $row['extension_name'] = $alias;
            }

            $this->extensions_1[$alias] = $row;

            $row = array_merge($baseRow, $row);
            $this->extensions_2[] = $row;
            $pk_version[$alias]   = $row['extension_version'];
        }
        $this->has_system = isset($this->extensions_1['system']);

        // validate
        foreach ($this->extensions_2 as $row) {
            if (!$row['extension_alias']) {
                return $this->fatal(_t('The install value «%s» is empty.'), 'extension_alias');
            }
            if (!Extensions::validate($row['extension_alias'])) {
                return $this->fatal(_t('The extension alias «%s» is invalid.'), $row['extension_alias']);
            }
            if (!$row['extension_name']) {
                return $this->fatal(_t('The install value «%s» is empty.'), 'extension_name');
            }
            if (!$row['extension_version']) {
                $this->fatal(_t('The install value «%s» is empty.'), 'extension_version');
            }
            if (isset($db_version[$row['extension_alias']])) {
                if (version_compare($db_version[$row['extension_alias']], $row['extension_version']) > 0) {
                    $this->fatal(_t('The extension «%s» is outdated.'), $row['extension_alias']);
                }
                if ((int)$db_version[$row['extension_alias']] < (int)$row['extension_version']) {
                    $this->maj_version[] = $row['extension_alias'];
                }
            }
            if (!$row['extension_credits']) {
                $this->fatal(_t('The install value «%s» is empty.'), 'extension_credits');
            }
            if (!$row['extension_license']) {
                $this->fatal(_t('The install value «%s» is empty.'), 'extension_license');
            }
            if (!$row['extension_require']) {
                $this->fatal(_t('The install value «%s» is empty.'), 'extension_require');
            }

            // requires
            $requires = $this->parseRequires($row['extension_require']);

            foreach ($requires as $req_alias => $req_version) {
                if ($req_alias !== $row['extension_alias']) {
                    $cur_version = $pk_version[$req_alias] ?? $db_version[$req_alias] ?? false;

                    if (!$cur_version) {
                        $this->fatal(
                            _t('The extension «%s» requires the extension «%s».'),
                            $row['extension_alias'],
                            $req_alias
                        );
                    } elseif ((int)$cur_version < (int)$req_version) {
                        $this->fatal(
                            _t('The extension «%s» requires the extension «%s» in a version «%s».'),
                            $row['extension_alias'],
                            $req_alias,
                            (int)$req_version
                        );
                    } elseif (version_compare($cur_version, $req_version) < 0) {
                        $this->fatal(
                            _t('The extension «%s» requires the extension «%s» in a version «%s» or higher.'),
                            $row['extension_alias'],
                            $req_alias,
                            $req_version
                        );
                    }
                }
            }

            // components
            if ($this->copy_files && $row['components']) {
                $keys = str_split($row['components']);
                $rows = $this->components->fetchAll($row['extension_alias'], $keys);

                if (count($keys) != count($rows)) {
                    $this->fatal(
                        _t('Falied open extension «%s». Missing components: «%s»'),
                        $row['extension_alias'],
                        implode('|', array_diff($keys, array_keys($rows)))
                    );
                }

                foreach ($rows as $row) {
                    is_dir($this->srcpath . $row['source'])
                        or $this->fatal(_t('Falied open dir «%s».'), $row['source']);
                }
            }
        }

        return true;
    }

    /**
     * System
     */
    protected function setSystem(array $json): void
    {
        if (isset($json['system']) && is_array($json['system'])) {
            $this->system = $json['system'];
        }

        $this->syspath = $this->mainpath . ($json['system_path'] ?? '');

        foreach ($this->system as $path) {
            if (!file_exists($this->syspath . $path)) {
                $this->fatal(_t('Falied open file «%s».'), $path);
            }
        }
    }

    /**
     * Sequence
     */
    protected function setSequence(string|array $sequence): array
    {
        if (!$sequence) {
            return $this->default_sequence;
        }

        if (!is_array($sequence)) {
            $sequence = str_split($sequence);
        }

        $sequence = array_unique($sequence);
        $has = [];

        foreach ($sequence as $cmd) {
            if (in_array($cmd, $this->default_sequence)) {
                $has[] = $cmd;
            }
        }

        foreach ($this->default_sequence as $cmd) {
            if (!in_array($cmd, $has)) {
                $has[] = $cmd;
            }
        }

        return $has;
    }

    /**
     * Security
     */
    protected function security(): void
    {
        $security = true;

        // I see that the table is installed
        if ($this->is_installer) {
            $security = $this->schema->tables()->has('extensions_developers');
        }

        if ($security) {
            // query - developers
            $developer = $this->db->query("
			SELECT
			 id ,
			 project_url ,
			 webstore_url
			FROM `#__extensions_developers`
			WHERE developer_name = ?", $this->developer['developer_name'])->fetch();

            if ($developer) {
                $this->developer_id    = $developer['id'];

                if (
                    $developer['project_url'] != $this->developer['project_url']
                    || $developer['webstore_url'] != $this->developer['webstore_url']
                ) {
                    $this->alert(_t('The developer data has modified.'));
                    $this->update_developer = true;
                }
            }

            // query - extensions
            $rows = $this->db->query("
			SELECT extension_alias, developer_id
			FROM `#__extensions`
			WHERE extension_alias IN (?..)", array_keys($this->extensions_1))->fetchAll(Database::FETCH_COLUMN, [0 => 1]);

            foreach ($rows as $extension_alias => $developer_id) {
                $this->extensions_1[$extension_alias]['status'] = (int)($developer_id == $this->developer_id);

                if (!$this->extensions_1[$extension_alias]['status']) {
                    $this->alert(_t('The «%s» extension developer doesn\'t match the registered.'), $extension_alias);
                }
            }
        }
    }

    /**
     * Get
     */
    protected function getCurrentVersions(): array
    {
        if (!$this->schema->tables()->has('extensions')) {
            return [];
        }

        return $this->db->query("
		SELECT extension_alias, extension_version
		FROM `#__extensions`")->fetchAll(Database::FETCH_COLUMN, [0 => 1]);
    }

    /**
     * Get
     */
    protected function majorUpdateVerification(): void
    {
        if (!$this->maj_version) {
            return;
        }

        $rows = $this->db->query("
		SELECT
		 extension_alias ,
		 extension_name ,
		 extension_require
		FROM `#__extensions`
		WHERE extension_alias NOT IN (?..)
		AND id NOT IN (SELECT extension_id FROM `#__extensions_updates` WHERE status = ?)
		ORDER BY extension_name", array_keys($this->extensions_1), UpdateStatus::available)->fetchAll();

        $has = [];
        foreach ($rows as $row) {
            $requires = $this->parseRequires($row['extension_require']);

            foreach ($this->maj_version as $alias) {
                if (isset($requires[$alias])) {
                    $has[$row['extension_alias']] = $row['extension_name'];
                }
            }
        }

        if ($has) {
            $this->alert(
                _t('This will be a major update, please make sure you have the updates for the following extensions: %s'),
                implode(', ', $has)
            );
        }
    }

    /**
     * Cleaner
     */
    protected function getPathsToBeCleared(): array
    {
        $keys = $this->components->getCleanables();
        $paths = [];

        foreach ($this->extensions_2 as $row) {
            $rows = $this->components->fetchAll($row['extension_alias'], $keys);

            foreach ($rows as $row) {
                is_dir($this->dstpath . $row['source'])
                    and $this->cleaner($paths, $row['source']);
            }
        }

        return $paths;
    }

    /**
     * Cleaner
     * 
     * @param array  $paths
     * @param string $dir
     * 
     * @return void
     */
    protected function cleaner(array &$paths, string $dir): void
    {
        if (is_dir($this->srcpath . $dir)) {
            foreach (scandir($this->dstpath . $dir) as $node) {
                if ($node == '.' || $node == '..') {
                    continue;
                }

                $node = $dir . $node;

                if (is_dir($this->dstpath . $node)) {
                    $this->cleaner($paths, $node . '/');
                } elseif (!is_file($this->srcpath . $node)) {
                    $paths[] = $node;
                }
            }
        } else {
            $paths[] = $dir;
        }
    }

    /**
     * Executables
     */
    protected function getExecutables(): array
    {
        // vars
        $cdir = glob($this->mainpath . 'executables/*/*.php');
        $executables = [];

        if ($cdir) {
            foreach ($cdir as $file) {
                $info = pathinfo($file);

                switch ($info['filename']) {
                    case 'before':
                        $executables['before'][] = basename($info['dirname']);
                        break;
                    case 'after':
                        $executables['after'][] = basename($info['dirname']);
                        break;
                }
            }
        }

        return $executables;
    }

    /**
     * Readme
     * 
     * @return string
     */
    protected function getReadmeContent(): string
    {
        return $this->getFileContents($this->mainpath . 'README.html');
    }

    /**
     * Changelog
     * 
     * @return string
     */
    protected function getChangelogContent(): string
    {
        return nl2br(strip_tags($this->getFileContents($this->mainpath . 'CHANGELOG.TXT')));
    }

    /**
     * Changelog
     * 
     * @return string
     */
    protected function getFileContents(string $file): string
    {
        return is_readable($file)
            ? file_get_contents($file)
            : '';
    }

    /**
     * Get
     * 
     * @return array
     */
    protected function getDbImport(): array
    {
        return [
            self::IMPORT_FROM_SQL => 'SQL',
            self::IMPORT_FROM_JSON => 'JSON',
            self::IMPORT_FROM_JSON_MIRROR => 'JSON + ' . _t('Drop nonexistent columns'),
            self::NOT_IMPORT => _t('None')
        ];
    }

    /**
     * Data
     * 
     * @return array
     */
    public function getData(string $package): array
    {
        $this->unpack($package);
        $this->majorUpdateVerification();
        return [
            'package'       => $package,
            'error'         => $this->error,
            'error_fatal'   => $this->error_fatal,
            'readme'        => $this->getReadmeContent(),
            'developer'     => $this->developer,
            'summary'       => $this->summary,
            'extensions'    => $this->extensions_1,
            'cleaner_paths' => $this->getPathsToBeCleared(),
            'executables'   => $this->getExecutables(),
            'changelog'     => $this->getChangelogContent(),
            'db_import'     => $this->getDbImport(),
        ];
    }

    /**
     * Get
     * 
     * @param string $package
     * 
     * @return bool
     */
    public function isAssistedOnly(string $package): bool
    {
        $result = $this->setPath($package);
        if ($result === false) {
            return false;
        }

        $json = $this->getJsonContent();
        if ($json === false) {
            return false;
        }

        return !empty($json['assisted_only']);
    }

    /**
     * Fatal
     * 
     * @param string $message
     * @param mixed  ...$args
     * 
     * @throws Exception
     * 
     * @return false
     */
    protected function fatal(string $message, ...$args): false
    {
        if ($args) {
            $message = vsprintf($message, $args);
        }

        if (!$this->debug) {
            throw new Exception($message);
        }

        $caller = debug_backtrace();
        $this->error_fatal = true;
        $this->error[] = [
            'type'    => 'fatal',
            'message' => $message,
            'line'    => $caller[0]['line']
        ];

        return false;
    }

    /**
     * Alert
     * 
     * @param string $message
     * @param mixed  ...$args
     * 
     * @return false
     */
    protected function alert(string $message, ...$args): false
    {
        if ($args) {
            $message = vsprintf($message, $args);
        }

        $caller = debug_backtrace();
        $this->error[] = [
            'type'    => 'alert',
            'message' => $message,
            'line'    => $caller[0]['line']
        ];

        return false;
    }

    /**
     * Parse requires
     * 
     * @param string $requires
     * 
     * @return array
     */
    protected function parseRequires(string $requires): array
    {
        $requires = explode(',', $requires);
        $parsed = [];

        foreach ($requires as $req) {
            $req = explode(':', $req, 2);

            if (isset($req[1])) {
                $version = trim($req[1]);
                $name = trim($req[0]);
            } else {
                $version = trim($req[0]);
                $name = 'system';
            }

            $parsed[$name] = $version;
        }

        return $parsed;
    }
}
