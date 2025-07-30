<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Extensions\Updater\Carrier;
use Junco\Extensions\Installer\Installer;

class ExtensionsWebstoreModel extends Model
{
    // vars
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Get
     */
    public function getListData()
    {
        // data
        $this->filter(POST, [
            'search' => 'text',
            'page'   => 'id',
        ]);

        try {
            $json = (new Carrier)->getListData($this->data['search'], $this->data['page']);
        } catch (Exception $e) {
            return ['error' => $e->getMessage() ?: 'Error!'];
        }

        // query
        $pagi = new Pagination();
        $pagi->num_rows = $json['num_rows'];
        $pagi->rows_per_page = $json['rows_per_page'];
        $pagi->calculate();

        $rows = [];
        if (!empty($json['rows'])) {
            $has = $this->getInstalledExtensions(array_column($json['rows'], 'alias'));

            foreach ($json['rows'] as $row) {
                $row['is_installed'] = in_array($row['alias'], $has);
                $row['details_url']  = $json['base_url'] . $row['details_url'];
                $row['image']        = $json['base_image'] . $row['image'];

                $rows[] = $row;
            }
        }

        return $this->data + [
            'pagi' => $pagi,
            'rows' => $rows
        ];
    }

    /**
     * Get
     */
    public function getConfirmDownloadData()
    {
        // data
        $this->filter(POST, ['extension_id' => 'id|required:abort']);

        //
        $data = (new Carrier)->getServerData($this->data['extension_id']);
        return [
            'is_close' => $data['is_close'],
            'title' => $data['extension_name'],
            'values' => [
                'extension_id'    => $this->data['extension_id'],
                'extension_alias' => $data['extension_alias'],
                'download_url'    => $data['download_url'],
                'extension_key'   => $data['extension_key'],
                '_extension_key'  => $data['extension_key'],
                'is_close'        => 1,
                'install'         => true
            ]
        ];
    }

    /**
     * download
     */
    public function download()
    {
        // data
        $this->filter(POST, [
            'download_url'    => '',
            'is_close'        => '',
            'install'         => '',
            'extension_key'   => '',
            'extension_alias' => '',
        ]);

        // vars
        $carrier  = new Carrier;
        $filename = $carrier->download($this->data);

        // extract archive
        if ($this->data['install']) {
            $carrier->extract($filename);

            // installer
            $installer = new Installer();
            $installer->remove_package = true;
            $installer->install(pathinfo($filename, PATHINFO_FILENAME));

            if ($this->data['is_close']) {
                $this->db->exec("
                UPDATE `#__extensions`
                SET extension_key = ?
                WHERE extension_alias = ?", $this->data['extension_key'], $this->data['extension_alias']);
            }
        }
    }

    /**
     * Get
     */
    protected function getInstalledExtensions(?array $extension_id = null)
    {
        // query
        return $this->db->query("
		SELECT extension_alias
		FROM `#__extensions`
		WHERE extension_alias IN (?..)", $extension_id)->fetchAll(Database::FETCH_COLUMN);
    }
}
