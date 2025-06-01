<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Extensions\Enum\UpdateStatus;
use Junco\Mvc\Model;
use Junco\Extensions\Updater\Carrier;
use Junco\Extensions\Updater\Updater;
use Junco\Extensions\Installer\Installer;
use Junco\Extensions\Maintenance;
use Junco\Filesystem\UploadedFileManager;

class ExtensionsInstallerModel extends Model
{
    // vars
    protected $db = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Upload
     */
    public function upload()
    {
        // data
        $this->filter(POST, [
            'delete' => 'bool',
            'file' => 'archive|required',
        ]);

        // file
        $this->data['file']
            ->setBasedir('')
            ->moveTo((new Carrier)->getTargetPath(), UploadedFileManager::DEFAULT_NAME, true)
            ->extract($this->data['delete']);
    }

    /**
     * Find Updates
     */
    public function findUpdates()
    {
        // data
        $this->filter(POST, ['option' => 'id']);

        //
        $num_updates = (new Updater)->findUpdates();

        // response
        if ($this->data['option'] === 2) {
            return $this->getBadgeMessage();
        }

        if ($this->data['option'] === 1) {
            $message = $this->getBadgeMessage();
        } else {
            $message = $num_updates
                ? sprintf(_nt('An update is available.', 'There is available %d updates.', $num_updates), $num_updates)
                : _t('No updates available.');
        }

        throw new Exception($message, 1);
    }

    /**
     * download
     */
    public function download()
    {
        // data
        $this->filter(POST, [
            'update_id'            => 'id|required:abort',
            'download_url'        => '',
            'is_close'            => '',
            'clear'                => 'bool',
            'decompress'        => 'bool',
            'extension_key'        => '',
            '_extension_key'    => '',
        ]);

        // key
        if ($this->data['is_close'] && $this->data['extension_key'] !== $this->data['_extension_key']) {
            $this->updateExtensionKey($this->data['extension_key'], $this->data['update_id']);
        }

        //
        $updater  = new Updater;
        $filename = $updater->download($this->data);

        // clear
        if ($this->data['clear']) {
            $updater->isInstalled($this->data['update_id']);
        }

        // decompress
        if ($this->data['decompress']) {
            $updater->extract($filename);
        }
    }

    /**
     * Update
     */
    public function update()
    {
        // data
        $this->filter(POST, [
            'update_id'            => 'id|required:abort',
            'download_url'        => '',
            'is_close'            => '',
            'extension_key'        => '',
            '_extension_key'    => '',
        ]);

        // key
        if ($this->data['is_close'] && $this->data['extension_key'] !== $this->data['_extension_key']) {
            $this->updateExtensionKey($this->data['extension_key'], $this->data['update_id']);
        }

        //
        $updater  = new Updater;
        $filename = $updater->download($this->data);
        //
        $updater->isInstalled($this->data['update_id']);
        $updater->extract($filename);

        // installer
        $installer = new Installer;
        $installer->remove_package = true;
        $installer->install(pathinfo($filename, PATHINFO_FILENAME));
    }

    /**
     * Update all
     */
    public function updateAll()
    {
        // data
        $this->filter(POST, [
            'id'        => 'id|array',
            'before_at'    => 'bool'
        ]);

        $result = (new Updater)->updateAll($this->data);

        if (!$result) {
            throw new Exception(_t('Error! the task has not been realized.'));
        }
    }

    /**
     * Unzip
     */
    public function unzip()
    {
        // data
        $this->filter(POST, ['file' => '']);

        (new Carrier)->extract($this->data['file']);
    }

    /**
     * Install
     */
    public function install()
    {
        // data
        $this->filter(POST, [
            'extension_alias'    => 'array',
            'package'            => 'text',
            'copy_files'        => 'bool',
            'remove_package'    => 'bool',
            'db_import'            => 'int',
            'clean_paths'        => 'array',
            'execute_before'    => 'bool',
            'execute_after'        => 'bool',
        ]);

        // installer
        $installer                      = new Installer();
        $installer->only_selected_alias = true;
        $installer->clean_everything    = false;
        $installer->copy_files          = $this->data['copy_files'];
        $installer->remove_package      = $this->data['remove_package'];
        $installer->db_import           = $this->data['db_import'];
        $installer->execute_before      = $this->data['execute_before'];
        $installer->execute_after       = $this->data['execute_after'];
        $installer->install(
            $this->data['package'],
            $this->data['extension_alias'],
            $this->data['clean_paths']
        );
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['id' => 'array|required:abort']);

        $updater = new Updater;
        $update_id = [];

        foreach ($this->data['id'] as $id) {
            if (is_numeric($id)) {
                $update_id[] = (int)$id;
            } else {
                $updater->removePackage($id);
            }
        }

        if ($update_id) {
            $updater->delete($update_id);
        }
    }

    /**
     * Maintenance
     */
    public function maintenance()
    {
        // data
        $this->filter(POST, ['status' => 'bool']);

        $maintenance = new Maintenance;

        if ($this->data['status'] !== $maintenance->getStatus()) {
            $maintenance->toggleStatus($this->data['status']);
        }
    }

    /**
     * Get
     */
    protected function getBadgeMessage()
    {
        // query
        $rows = $this->db->safeFind("
		SELECT
		 u.update_version ,
		 e.extension_alias ,
		 e.extension_name
		FROM `#__extensions_updates` u
		LEFT JOIN `#__extensions` e ON ( u.extension_id = e.id )
		WHERE u.status = ?", UpdateStatus::available)->fetchAll();

        if ($rows) {
            $extensions = [];
            $html       = '';
            $url        = url('admin/extensions') . '#/option=1';

            foreach ($rows as $row) {
                $PackageName = $row['extension_name'] . ' ' . $row['update_version'];

                if ($row['extension_alias'] === 'system') {
                    $html = '<div class="dialog dialog-warning">'
                        . _t('The latest version of the system is available.') . ' '
                        . sprintf(_t('You can now upgrade to %s.'), '<a href="' . $url . '">' . $PackageName . '</a>')
                        . '</div>';
                } else {
                    $extensions[] = '<a href="' . $url . '">' . $PackageName . '</a>';
                }
            }

            if ($extensions) {
                $html .= '<div class="dialog dialog-warning">' . sprintf(_t('The following extensions have updates available: %s'), implode(', ', $extensions)) . '</div>';
            }
        } else {
            $html = 'null';
        }

        // cache
        cache()->set('extensions-updates#', $html, 24 * 3600);

        return $html;
    }

    /**
     * Update
     */
    protected function updateExtensionKey(string $extension_key, int $update_id)
    {
        $this->db->safeExec("
		UPDATE `#__extensions` 
		SET extension_key = ? 
		WHERE id = (SELECT extension_id FROM `#__extensions_updates` WHERE id = ?)", $extension_key, $update_id);
    }
}
