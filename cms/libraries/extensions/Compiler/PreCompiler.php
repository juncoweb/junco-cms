<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\Compiler;

use Junco\Extensions\Components;
use Junco\Extensions\Extensions;
use Junco\Extensions\XData\XDataManager;
use Junco\Extensions\Compiler\PluginCollector;
use Database;
use Exception;

class PreCompiler
{
    // vars
    protected $db;
    protected string $update_requires;
    protected string $update_versions;
    //
    protected ?array $package               = null;
    protected array  $verifiedChanges       = [];
    protected array  $allChanges            = [];
    protected array  $extensions            = [];
    protected bool   $get_install_package   = false;
    protected array  $messages = [
        'errors'  => [],
        'repairs' => [],
        'changes' => [],
        'updates' => [],
    ];
    //
    protected $stmtIC = null;
    protected $stmtUE = null;

    /**
     * Constructor
     * 
     * @param string $update_versions (no, yes)
     * @param string $update_requires (no, yes)
     */
    public function __construct(string $update_versions, string $update_requires)
    {
        $this->db = db();
        $this->update_versions = $update_versions;
        $this->update_requires = $update_requires;
    }

    /**
     * Gets the data of a package and returns its status.
     * 
     * @param int $package_id
     * 
     * @return int
     */
    public function getPackage(int $package_id): int
    {
        try {
            $this->getPackageData($package_id);
            $this->getExtensions();

            // I verify the requires
            $this->verifyRequires();

            // update versions
            $this->verifyUpdateVersions();

            // repairs
            $this->makeRepairs();
        } catch (Exception $e) {
            $code = $e->getCode();
            if ($code == 0) {
                $this->messages['errors'][] = $e->getMessage();
            }

            return $code;
        }

        return -3;
    }

    /**
     * Get
     */
    public function getInstallPackage(): bool
    {
        return $this->get_install_package;
    }

    /**
     * Get
     */
    public function getUpdates(): array
    {
        return $this->messages['updates'];
    }

    /**
     * Get
     */
    public function getRepairs(): array
    {
        return $this->messages['repairs'];
    }

    /**
     * Get
     */
    public function getPlugins(): array
    {
        $plugins = config('extensions.compiler_plugins');

        if ($plugins) {
            return (new PluginCollector($plugins))->get();
        }

        return [];
    }

    /**
     * Get
     */
    public function getChanges(): array
    {
        return $this->messages['changes'];
    }

    /**
     * Get
     */
    public function getErrors(): array
    {
        return $this->messages['errors'];
    }

    /**
     * Gets the data of a package and returns its status.
     * 
     * @param int $package_id
     * 
     * @return void
     */
    protected function getPackageData(int $package_id): void
    {
        // query
        $data = $this->db->safeFind("
		SELECT
		 id ,
		 developer_id ,
		 extension_alias ,
		 extension_name AS name ,
		 extension_require
		FROM `#__extensions`
		WHERE id = ?
		AND package_id = -1", $package_id)->fetch() or abort();

        if ($data['extension_alias'] == 'system') {
            $this->get_install_package = true;
        }

        $this->package = $data;
        $this->allChanges[$data['developer_id']] ??= $this->getAllChanges();
    }

    /**
     * Checks for changes in any required extension and reports them.
     * 
     * @param string $requires
     * 
     * @return void
     */
    protected function verifyPendingChangesInRequires(string $requires): void
    {
        $requires = $this->parseRequires($requires);
        $recursion = [];

        foreach (array_keys($requires) as $req_name) {
            if ($req_name == $this->package['extension_alias']) {
                continue;
            }
            if (!in_array($req_name, $this->verifiedChanges)) {
                $this->verifiedChanges[] = $req_name;

                $depth_requires = $this->verifyPendingChanges($req_name);
                if ($depth_requires) {
                    $recursion[] = $depth_requires;
                }
            }
        }

        if ($recursion) {
            $this->verifyPendingChangesInRequires(
                implode(',', array_unique($recursion))
            );
        }
    }

    /**
     * Verify
     * 
     * @param string $req_name
     * 
     * @return ?string
     */
    protected function verifyPendingChanges(string $req_name): ?string
    {
        $current = $this->allChanges[$this->package['developer_id']][$req_name] ?? null;

        if ($current) {
            if ($current['has_change']) {
                $this->messages['errors']['non-compiled'][] = $current['extension_alias'];
            }

            return $current['extension_require'];
        }

        return null;
    }

    /**
     * Get
     * 
     * @return array
     */
    protected function getAllChanges(): array
    {
        return $this->db->safeFind("
		SELECT
		 e.extension_alias ,
		 e.extension_version ,
		 e.extension_require ,
		 (SELECT COUNT(*) FROM `#__extensions_changes` c WHERE  c.extension_id = e.id AND c.status = 0) AS has_change
		FROM `#__extensions` e
		WHERE e.developer_id = ?
		AND e.package_id = -1", $this->package['developer_id'])
            ->fetchAll(Database::FETCH_ASSOC, 'extension_alias');
    }

    /**
     * Gets the data of a package and returns its status.
     * 
     * @return void
     */
    public function getExtensions(): void
    {
        // query - extensions
        $extensions = $this->db->safeFind(
            "
		SELECT
		 id ,
		 extension_alias ,
		 extension_name ,
		 extension_version ,
		 extension_require ,
		 components ,
		 db_queries ,
		 xdata
		FROM `#__extensions`
		WHERE ( id = ? OR package_id = ? )
		AND developer_id = ?",
            $this->package['id'],
            $this->package['id'],
            $this->package['developer_id']
        )->fetchAll();

        $this->extensions = [];
        foreach ($extensions as $extension) {
            $extension['extension_require_arr'] = $this->parseRequires($extension['extension_require']);
            $this->extensions[$extension['id']] = $extension;
        }
    }

    /**
     * Verify requires
     */
    protected function verifyRequires(): void
    {
        // Verify for pending changes in the extension requires
        foreach ($this->extensions as $row) {
            $this->verifyPendingChangesInRequires($row['extension_require']);
        }

        $ask = false;
        foreach ($this->extensions as $row) {
            $requires = [];
            foreach ($row['extension_require_arr'] as $req_name => $req_version) {
                $cur_version = $this->allChanges[$this->package['developer_id']][$req_name]['extension_version'] ?? null;

                if ($cur_version === null) {
                    continue;
                }
                if (
                    $req_name != $this->package['extension_alias'] // If the extension requires its own package, it will be discussed later.
                    && $req_version != $cur_version
                ) {
                    $this->notifyChanges($req_name, $cur_version);

                    if ($this->update_requires == 'yes') {
                        $req_version = $cur_version;

                        $this->addChanges($row['id'], sprintf('Updated require «%s» to version «%s»', $req_name, $cur_version));
                    } elseif ($this->update_requires != 'no') {
                        $ask = true;
                    }
                }

                $requires[$req_name] = $req_version;
            }

            $extension_require = $this->renderRequires($requires);

            if ($row['extension_require'] != $extension_require) {
                $set = [
                    'extension_require' => $extension_require
                ];

                $this->updateExtension($set, $row['id']);
                $this->notifyRepairs($set, $row['extension_alias']);
            }
        }

        if ($ask) {
            throw new Exception('update-requires-question', -1);
        }
    }

    /**
     * Verify updates
     */
    protected function verifyUpdateVersions(): void
    {
        // query - new versions
        $updates = $this->db->safeFind("
		SELECT
		 extension_id ,
		 MIN(is_compatible)
		FROM `#__extensions_changes`
		WHERE extension_id IN ( ?.. )
		AND status = 0
		GROUP BY extension_id", array_keys($this->extensions))
            ->fetchAll(Database::FETCH_COLUMN, [0 => 1]);

        if ($updates) {
            // The package will be compatible depending on its annexes.
            $updates[$this->package['id']] = !in_array(0, $updates);

            // message
            $this->notifyUpdates($updates);

            if ($this->update_versions == 'yes') {
                $this->updateVersions($updates);
            } elseif ($this->update_versions != 'no') {
                throw new Exception('update-versions-question', -2);
            }
        }
    }

    /**
     * Update versions
     * 
     * @param array $updates
     */
    protected function updateVersions(array $updates): void
    {
        foreach ($updates as $extension_id => $is_compatible) {
            $version = $this->incrementVersion($this->extensions[$extension_id]['extension_version'], !$is_compatible);

            // query - update
            $this->updateExtension(['extension_version' => $version], $extension_id);
            $this->addChanges($extension_id, sprintf('Updated to version «%s»', $version), 0, 3);

            // hack
            if ($extension_id == $this->package['id']) {
                $a = $this->extensions[$this->package['id']]['extension_alias'];
                $this->allChanges[$this->package['id']][$a]['extension_version'] = $version;
                $this->extensions[$this->package['id']]['extension_version'] = $version;
            }
        }

        $this->db->safeExec("
		UPDATE `#__extensions_changes`
		SET status = status + 1
		WHERE extension_id IN (?..) 
		AND status < 2", array_keys($this->extensions));
    }

    /**
     * Repairs
     */
    protected function makeRepairs(): void
    {
        $package_name    = $this->package['extension_alias'];
        $package_version = $this->extensions[$this->package['id']]['extension_version'];
        $components      = new Components();
        $xdm             = new XDataManager;

        foreach ($this->extensions as $row) {
            $set = [];

            // requires
            $requires = $row['extension_require_arr'];
            if (
                isset($requires[$package_name])
                && $requires[$package_name] != $package_version
            ) {
                $requires[$package_name] = $package_version;
                $set['extension_require'] = $this->renderRequires($requires);
            }

            // components
            $has = implode(array_keys(
                $components->getDirectories($row['extension_alias'])
            ));

            if ($row['components'] != $has) {
                $set['components'] = $has;
            }

            // queries
            $queries = implode(',', Extensions::getQueries($row['extension_alias']));

            if ($row['db_queries'] != $queries) {
                $set['db_queries'] = $queries;
            }

            // xdata
            $has = implode(',', $xdm->find($row['id'], $row['extension_alias']));

            if ($row['xdata'] != $has) {
                $set['xdata'] = $has;
            }

            if ($set) {
                $this->updateExtension($set, $row['id']);
                $this->notifyRepairs($set, $row['extension_alias']);
            }
        }
    }

    /**
     * Parse require.
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


    /**
     * Render requires.
     * 
     * @return array
     */
    protected function renderRequires(array $requires): string
    {
        foreach ($requires as $req_name => $req_version) {
            $requires[$req_name] = ($req_name == 'system')
                ? $req_version
                : $req_name . ':' . $req_version;
        }

        return implode(',', $requires);
    }

    /**
     * Increment version.
     * 
     * @param string $version
     * @param bool   $is_major
     * 
     * @return string
     */
    protected function incrementVersion(string $version, bool $is_major): string
    {
        $version = explode('.', $version);
        if ($is_major) {
            $version[0] = (int)$version[0] + 1;
            $version[1] = 0;
        } else {
            $version[1] = (int)($version[1] ?? 0) + 1;
        }

        return implode('.', $version);
    }

    /**
     * Get current versions
     * 
     * @return array
     */
    protected function getCurrentVersionsOfRequires(): array
    {
        $extension_alias = [];

        foreach ($this->extensions as $row) {
            foreach (array_keys($row['extension_require_arr']) as $req_name) {
                $extension_alias[$req_name] = null;
            }
        }
        if (isset($extension_alias[$this->package['extension_alias']])) {
            unset($extension_alias[$this->package['extension_alias']]);
        }
        if (!$extension_alias) {
            return [];
        }

        // query - I am looking for the version of the requires
        return $this->db->safeFind("
		SELECT
		 extension_alias ,
		 extension_version
		FROM `#__extensions`
		WHERE extension_alias IN (?..)", array_keys($extension_alias))
            ->fetchAll(Database::FETCH_COLUMN, [0 => 1]);
    }

    /**
     * Add message
     * 
     * @param array $updates
     */
    protected function notifyUpdates(array $updates): void
    {
        foreach (array_keys($updates) as $extension_id) {
            $this->messages['updates'][] = $this->extensions[$extension_id]['extension_name'];
        }
    }

    /**
     * Add changes
     * 
     * @param int    $extension_id
     * @param string $description
     * @param int    $is_compatible = 1
     * @param int    $status = 0
     */
    protected function addChanges(int $extension_id, string $description, int $is_compatible = 1, int $status = 0): void
    {
        $this->stmtIC ??= $this->db->prepare("
		INSERT INTO `#__extensions_changes` (extension_id, change_description, is_compatible, status)
		VALUES (?, ?, ?, ?)");

        $this->db->safeExec($this->stmtIC, $extension_id, $description, $is_compatible, $status);
    }

    /**
     * Notify
     * 
     * @param string $req_name
     * @param string $cur_version
     */
    protected function notifyChanges(string $req_name, string $cur_version): void
    {
        $this->messages['changes'][$req_name] = $cur_version;
    }

    /**
     * Update
     * 
     * @param array $set
     * @param int $id
     */
    protected function updateExtension(array $set, int $id): void
    {
        $this->stmtUE ??= $this->db->prepare("
		UPDATE `#__extensions`
		SET extension_version = ?, extension_require = ?, components = ?, db_queries  = ?, xdata = ?
		WHERE id = ?");

        $this->db->safeExec(
            $this->stmtUE,
            $set['extension_version'] ?? $this->extensions[$id]['extension_version'],
            $set['extension_require'] ?? $this->extensions[$id]['extension_require'],
            $set['components'] ?? $this->extensions[$id]['components'],
            $set['db_queries'] ?? $this->extensions[$id]['db_queries'],
            $set['xdata'] ?? $this->extensions[$id]['xdata'],
            $id
        );
    }

    /**
     * Notify
     * 
     * @param array  $set
     * @param string $name
     */
    protected function notifyRepairs(array $set, string $name): void
    {
        $this->messages['repairs'][] = [
            'name' => $name,
            'set' => $set
        ];
    }
}
