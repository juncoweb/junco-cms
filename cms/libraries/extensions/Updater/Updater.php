<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\Updater;

use Junco\Extensions\Updater\Carrier;
use Junco\Extensions\Installer\Installer;
use Junco\Extensions\Installer\Unpackager;
use Database;
use Exception;
use Filesystem;
use Junco\Extensions\Enum\ExtensionStatus;
use Junco\Extensions\Enum\UpdateStatus;

class Updater extends Carrier
{
    // vars
    protected Database    $db;
    protected ?Filesystem $fs = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->db = db();
    }

    /**
     * Find Updates
     */
    public function findUpdates(): int
    {
        // vars
        $webstore    = [];
        $locals      = [];
        $updates     = [];
        $synchronize = [];

        // query - extensions
        $extensions = $this->db->safeFind("
		SELECT
		 e.id ,
		 e.developer_id ,
		 e.extension_alias ,
		 e.extension_version ,
		 e.status ,
		 d.developer_name ,
		 d.webstore_url
		FROM `#__extensions` e
		LEFT JOIN `#__extensions_developers` d ON ( e.developer_id = d.id )
		WHERE d.webstore_url <> ''")->fetchAll();

        foreach ($extensions as $row) {
            $webstore[$row['webstore_url']][$row['developer_id']] ??= [
                'name'       => $row['developer_name'],
                'extensions' => [],
            ];

            $webstore[$row['webstore_url']][$row['developer_id']]['extensions'][$row['extension_alias']] = [
                'version' => $row['extension_version'],
                'status'  => $row['status'],
            ];

            $locals[$row['developer_name']][$row['extension_alias']] = [
                'id'      => $row['id'],
                'version' => $row['extension_version'],
                'status'  => $row['status'],
            ];
        }

        foreach ($webstore as $webstore_url => $developers) {
            $json = $this->getWebstoreData($webstore_url, array_values($developers));

            if (empty($json['developers']) || !is_array($json['developers'])) {
                continue;
            }

            foreach ($json['developers'] as $developer) {
                if (
                    !empty($developer['name'])
                    && !empty($developer['extensions'])
                    && is_string($developer['name'])
                    && is_array($developer['extensions'])
                ) {
                    foreach ($developer['extensions'] as $alias => $remote) {
                        $local = $locals[$developer['name']][$alias] ?? false;

                        if ($local) {
                            if (
                                !empty($remote['version'])
                                && version_compare($local['version'], $remote['version']) < 0
                            ) {
                                $updates[] = [
                                    'extension_id'   => $local['id'],
                                    'update_version' => $remote['version'],
                                    'released_at'    => $remote['released_at'] ?? null,
                                ];
                            }
                            if (
                                !empty($remote['status'])
                                && $local['status'] != $remote['status']
                                && ExtensionStatus::isValid($remote['status'])
                            ) {
                                $synchronize[] = [$remote['status'], $local['id']];
                            }
                        }
                    }
                }
            }
        }

        if ($updates) {
            $this->storeUpdates($updates);
        }

        if ($synchronize) {
            $this->synchronize($synchronize);
        }

        return count($updates);
    }

    /**
     * Update all
     * 
     * @param array $filter
     * 
     * @return bool
     */
    public function updateAll(array $filter = []): bool
    {
        // query
        $updates = $this->getUpdates($filter);

        if (!$updates) {
            return true;
        }

        $this->getPackages($updates);

        if ($this->canInstall($updates)) {
            $this->installAll($updates);
        }

        return $this->handleErrors($updates);
    }

    /**
     * Delete
     */
    public function delete(int|array $id): void
    {
        if (!is_array($id)) {
            $id = [$id];
        }

        $this->db->safeExec("DELETE FROM `#__extensions_updates` WHERE id IN ( ?.. )", $id);
    }

    /**
     * Delete
     */
    public function removePackage(string $package): bool
    {
        $this->fs ??= new Filesystem($this->target);
        return $this->fs->remove($package);
    }

    /**
     * 
     */
    public function isInstalled(int|array $update_id): void
    {
        if (!$update_id) {
            return;
        }

        if (!is_array($update_id)) {
            $update_id = [$update_id];
        }

        $this->db->safeExec("
        UPDATE `#__extensions_updates`
        SET status = ?
        WHERE id IN (?..)", UpdateStatus::installed, $update_id);
    }

    /**
     * Errors
     */
    protected function handleErrors(array $updates): bool
    {
        $result = true;

        foreach ($updates as $update) {
            if (!$update['is_installed']) {
                $result = false;

                // query
                $stmt ??= $this->db->prepare("UPDATE `#__extensions_updates` SET has_failed = 1, failure_msg = ? WHERE id = ?");
                $this->db->safeExec($stmt, $update['failure_msg'], $update['id']);

                // log
                app('logger')->alert($update['failure_msg']);
            }
        }

        return $result;
    }

    /**
     * Get
     */
    protected function getUpdates(array $filter): array
    {
        // query
        if (!empty($filter['before_at'])) {
            $this->db->where("u.released_at < DATE_SUB(NOW(), INTERVAL ? HOUR)", $filter['before_at']);
        } elseif (!empty($filter['id'])) {
            $this->db->where("u.id IN (?..)", $filter['id']);
        }
        $this->db->where("u.status = ?", UpdateStatus::available);

        $rows = $this->db->safeFind("
		SELECT
         u.id ,
         u.update_version ,
         e.extension_alias ,
         e.extension_name ,
         e.extension_key ,
         e.extension_version ,
         d.webstore_url
        FROM `#__extensions_updates` u
        LEFT JOIN `#__extensions` e ON ( u.extension_id = e.id )
        LEFT JOIN `#__extensions_developers` d ON ( e.developer_id = d.id )
        [WHERE]
        ORDER BY u.released_at")->fetchAll();

        foreach ($rows as $i => $row) {
            $rows[$i]['package']      = sprintf('%s_%s', $row['extension_alias'], $row['update_version']);
            $rows[$i]['is_installed'] = false;
        }

        return $rows;
    }

    /**
     * Get
     * 
     * @param array $updates
     */
    protected function getPackages(array &$updates): void
    {
        foreach ($updates as &$update) {
            if ($this->existsPackage($update['package'])) {
                continue;
            }

            if (!$this->existsZippedPackage($update['package'])) {
                $package = pathinfo($this->download(
                    $this->getServerData($update)
                ), PATHINFO_FILENAME);

                if ($package !== $update['package']) {
                    $this->updatePackage($update, $package);
                }
            }

            $this->extract($update['package'] . '.zip');
        }
    }

    /**
     * Exists
     * 
     * @param string $package
     * 
     * @return bool
     */
    protected function existsPackage(string $package): bool
    {
        return is_dir($this->target . $package);
    }

    /**
     * Get
     * 
     * @param string $package
     * 
     * @return bool
     */
    protected function existsZippedPackage(string $package): bool
    {
        return is_file($this->target . $package . '.zip');
    }

    /**
     * Get
     * 
     * @param array  $update
     * @param string $package
     * 
     * @return void
     */
    protected function updatePackage(array &$update, string $package): void
    {
        $update['package']        = $package;
        $update['update_version'] = explode('_', $package)[1];
    }

    /**
     * Get
     * 
     * @param array &$updates
     */
    protected function canInstall(array &$updates): bool
    {
        $unpackager = new Unpackager();
        $result = true;

        foreach ($updates as $i => $update) {
            try {
                if ($unpackager->isAssistedOnly($update['package'])) {
                    $result = false;
                }
            } catch (Exception $e) {
                $updates[$i]['failure_msg'] = $e->getMessage();
            }
        }

        return $result;
    }

    /**
     * Get
     * 
     * @param array &$updates
     */
    protected function installAll(array &$updates): void
    {
        $installer = new Installer();
        $installer->remove_package = true;
        $update_id = [];

        do {
            $are_installed = false;
            $are_skipped   = false;

            foreach ($updates as $i => $update) {
                if (!$update['is_installed']) {
                    try {
                        $installer->install($update['package']);
                        $updates[$i]['is_installed'] = true;
                        $update_id[] = $update['id'];
                        $are_installed = true;
                    } catch (Exception $e) {
                        $updates[$i]['failure_msg'] = $e->getMessage();
                        $are_skipped = true;
                    }
                }
            }
        } while ($are_installed && $are_skipped);

        $this->isInstalled($update_id);
    }

    /**
     * Store
     */
    protected function storeUpdates(array $updates): void
    {
        // query
        $has = $this->db->safeFind("
		SELECT extension_id, id
		FROM `#__extensions_updates`
		WHERE extension_id IN (?..)
		AND status = ?", array_column($updates, 'extension_id'), UpdateStatus::available)->fetchAll(Database::FETCH_COLUMN, [0 => 1]);

        foreach ($updates as $update) {
            $update_id = $has[$update['extension_id']] ?? 0;

            if ($update_id) {
                $stmt_1 ??= $this->db->prepare("UPDATE `#__extensions_updates` SET update_version = ?, released_at = ? WHERE id = ?");
                $this->db->safeExec($stmt_1, $update['update_version'], $update['released_at'], $update_id);
            } else {
                $stmt_2 ??= $this->db->prepare("
				INSERT INTO `#__extensions_updates` (extension_id, update_version, released_at, status) 
				VALUES (?, ?, ?, ?)");

                $this->db->safeExec($stmt_2, $update['extension_id'], $update['update_version'], $update['released_at'], UpdateStatus::available);
            }
        }
    }

    /**
     * Synchronize
     */
    protected function synchronize(array $extensions): void
    {
        $stmt = $this->db->prepare("UPDATE `#__extensions` SET status = ? WHERE id = ?");

        foreach ($extensions as $data) {
            $this->db->safeExec($stmt, $data);
        }
    }
}
