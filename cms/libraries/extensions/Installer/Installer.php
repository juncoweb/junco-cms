<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\Installer;

use Junco\Extensions\Installer\Unpackager;
use Junco\Extensions\XData\XDataManager;
use DatabaseImporter;
use Filesystem;

class Installer extends Unpackager
{
    // vars
    public bool  $only_selected_alias  = false;
    public bool  $clean_everything     = false;
    public bool  $remove_package       = false;
    public bool  $execute_before       = true;
    public bool  $execute_after        = true;
    public int   $db_import            = self::IMPORT_FROM_JSON;
    //
    protected Filesystem $fs;
    protected DatabaseImporter $importer;
    protected ?XDataManager $xdm = null;
    protected array $extensions = [];

    /**
     * Constructor
     * 
     * @param bool $is_installer
     */
    public function __construct(bool $is_installer = false)
    {
        parent::__construct($is_installer);

        $this->fs       = new Filesystem('');
        $this->importer = new DatabaseImporter();
    }

    /**
     * Install the package
     * 
     * @param string $package
     * @param array  $extension_alias
     * @param array  $clean_paths
     * 
     * @return void
     */
    public function install(string $package = '', array $extension_alias = [], array $clean_paths = []): void
    {
        // initialize
        $this->unpack($package);
        $this->getExtensions($extension_alias);
        $this->xdm = new XDataManager($this->srcpath, $this->is_installer);

        // before
        if ($this->execute_before && !$this->is_installer) {
            $this->execute('before');
        }

        // install
        foreach ($this->sequence as $cmd) {
            switch ($cmd) {
                case 'f':
                    $this->copyFiles();
                    break;

                case 's':
                    $this->replicateDatabase();
                    break;

                case 'd':
                    $this->importData();
                    break;
            }
        }

        // cleaner
        if ($this->clean_everything) {
            $clean_paths = $this->getPathsToBeCleared();
        }
        if ($clean_paths) {
            $this->clean($clean_paths);
        }

        // after
        if ($this->execute_after) {
            $this->execute('after');
        }

        // remove package
        if ($this->remove_package && !$this->is_installer) {
            $this->fs->remove($this->srcpath);

            $zipFile = substr($this->srcpath, 0, -1) . '.zip';

            is_file($zipFile)
                and $this->fs->remove($zipFile);
        }

        cache()->clear();
    }

    /**
     * Get
     * 
     * @param array $extension_alias
     * 
     * @return void
     */
    protected function getExtensions(array $extension_alias): void
    {
        $this->extensions = $this->extensions_2;

        if ($this->only_selected_alias) {
            $this->extensions = array_values(
                array_filter(
                    $this->extensions,
                    fn($row) => in_array($row['extension_alias'], $extension_alias)
                )
            );
        }

        if (!$this->extensions) {
            $this->fatal(_t('Please, select the extensions.'));
        }
    }

    /**
     * Execute
     * 
     * @param string $action
     * 
     * @return void
     */
    protected function execute(string $action): void
    {
        $cdir = glob($this->mainpath . "executables/*/{$action}.php");

        if ($cdir) {
            foreach ($cdir as $file) {
                self::include($file);
            }
        }
    }

    /**
     * Copy
     */
    protected function copyFiles(): void
    {
        if ($this->copy_files) {
            $this->copyComponents();

            if ($this->system) {
                $this->copySystem();
            }
        }
    }

    /**
     * Copy
     */
    protected function copyComponents(): void
    {
        foreach ($this->extensions as $row) {
            if ($row['components']) {
                $directories = $this->components->fetchAll($row['extension_alias'], $row['components']);

                foreach ($directories as $dir) {
                    $this->fs->copy(
                        $this->srcpath . $dir['source'],
                        $this->dstpath . $dir['local']
                    );
                }
            }
        }
    }

    /**
     * Copy
     */
    protected function copySystem(): void
    {
        foreach ($this->system as $node) {
            $this->fs->copy(
                $this->syspath . $node,
                $this->dstpath . $node
            );
        }

        $this->system = []; // free
    }

    /**
     * Database
     */
    protected function replicateDatabase(): void
    {
        if ($this->databaseIsImported($this->db_import)) {
            $this->importDatabase();
        }

        // store
        if (!$this->developer_id) {
            $this->storeDeveloper();
        } elseif ($this->update_developer) {
            $this->updateDeveloper();
        }

        $this->storeExtensions();

        if ($this->is_installer) {
            $this->storeAdminRole();
        }
    }

    /**
     * 
     */
    protected function databaseIsImported(int $value): bool
    {
        return in_array($value, [
            self::IMPORT_FROM_SQL,
            self::IMPORT_FROM_JSON,
            self::IMPORT_FROM_JSON_MIRROR
        ]);
    }

    /**
     * Import Database
     */
    protected function importDatabase(): void
    {
        $queries = [];
        foreach ($this->extensions as $row) {
            if ($row['db_queries']) {
                $queries[] = $row['extension_alias'];
            }
        }

        if ($queries) {
            $this->importer->drop_nonexistent_columns = ($this->db_import === self::IMPORT_FROM_JSON_MIRROR);

            $sqlpath   = $this->mainpath . config('extensions.sql_path');
            $extension = ($this->db_import === self::IMPORT_FROM_SQL)
                ? '.sql'
                : '.json';

            foreach ($queries as $extension_alias) {
                $this->importer->fromFile($sqlpath . $extension_alias . $extension);
            }
        }
    }

    /**
     * Store developer
     */
    protected function storeDeveloper(): void
    {
        $this->db->safeExec("INSERT INTO `#__extensions_developers` (??) VALUES (??)", $this->developer);
        $this->developer_id = $this->db->lastInsertId();
    }

    /**
     * Update developer
     */
    protected function updateDeveloper(): void
    {
        $this->db->safeExec(
            "UPDATE `#__extensions_developers` SET ?? WHERE id = ?",
            $this->developer,
            $this->developer_id
        );
    }

    /**
     * Store the extensions in the database.
     */
    protected function storeExtensions(): void
    {
        foreach ($this->extensions as $row) {
            $this->db->safeExec("INSERT INTO `#__extensions` (??) VALUES (??) ON DUPLICATE KEY UPDATE ??", [
                'extension_alias'       => $row['extension_alias'],
                'developer_id'          => $this->developer_id,
                'extension_name'        => $row['extension_name'],
                'extension_version'     => $row['extension_version'],
                'extension_credits'     => $row['extension_credits'],
                'extension_license'     => $row['extension_license'],
                'extension_abstract'    => $row['extension_abstract'],
                'extension_require'     => $row['extension_require'],
                'components'            => $row['components'],
                'db_queries'            => $row['db_queries'],
                'xdata'                 => $row['xdata'],
            ]);
        }
    }

    /**
     * Admin Role
     */
    protected function storeAdminRole(): void
    {
        $role_id = config('install.admininstrator_role_id') ?: 1;
        $this->db->safeExec("INSERT INTO `#__users_roles` (??) VALUES (??) ON DUPLICATE KEY UPDATE ??", [
            'id'        => $role_id,
            'role_name' => 'Administrator'
        ]);
    }

    /**
     * XData
     */
    protected function importData(): void
    {
        foreach ($this->extensions as $row) {
            if ($row['xdata']) {
                $this->xdm->add($row['xdata'], $row['extension_alias']);
            }
        }

        $this->xdm->exec('import');
    }

    /**
     * Clean
     */
    protected function clean(array $paths): void
    {
        foreach ($paths as $path) {
            $this->fs->remove($this->dstpath . $path);
        }
    }

    /**
     * 
     */
    protected static function include($file)
    {
        include $file;
    }
}
