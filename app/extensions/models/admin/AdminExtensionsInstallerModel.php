<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Extensions\Updater\Carrier;
use Junco\Extensions\Installer\Unpackager;
use Junco\Extensions\Maintenance;

class AdminExtensionsInstallerModel extends Model
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
     * Get
     */
    public function getListData()
    {
        $indexes = [];

        // query
        $rows = $this->db->safeFind("
		SELECT
		 u.id ,
		 u.update_version ,
		 u.has_failed ,
		 e.extension_name ,
		 e.extension_alias
		FROM `#__extensions_updates` u
		LEFT JOIN `#__extensions` e ON ( u.extension_id = e.id )
		WHERE u.status = 'available'")->fetchAll();

        foreach ($rows as $index => $row) {
            $indexes[$row['extension_alias'] . '_' . $row['update_version']] = $index;
            $rows[$index] = [
                'id'            => $row['id'],
                'caption'        => $row['extension_name'],
                'step'            => 0,
                'has_failed'    => $row['has_failed'],
            ];
        }

        // query 2
        $dirpath    = (new Carrier)->getTargetPath();
        $extensions    = ['zip', 'rar'];

        foreach ($this->scandir($dirpath) as $element) {
            if (is_dir($dirpath . $element)) {
                $index = $indexes[$element] ?? null;
                $row = [
                    'id'            => $element,
                    'caption'        => $element,
                    'step'            => 2,
                    'has_failed'    => 0,
                ];

                if ($index !== null) {
                    $rows[$index] = $row;
                } else {
                    $rows[] = $row;
                    /* $indexes[$element] = array_push($rows, $row) - 1; */
                }
            } else {
                $info = pathinfo($element);
                $index = $indexes[$info['filename']] ?? null;
                $row = [
                    'id'            => $element,
                    'caption'        => $element,
                    'step'            => 1,
                    'has_failed'    => 0,
                ];

                if ($index !== null) {
                    $rows[$index] = $row;
                } elseif (!empty($info['extension']) && in_array($info['extension'], $extensions)) {
                    $indexes[$info['filename']] = array_push($rows, $row) - 1;
                }
            }
        }

        return ['rows' => $rows];
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        // data
        $this->filter(POST, ['id' => 'array|required:abort']);

        return $this->data;
    }

    /**
     * Get
     */
    public function getConfirmDownloadData()
    {
        // data
        $this->filter(POST, ['id' => 'id|array:first|required:abort']);

        // query
        $update = $this->db->safeFind("
		SELECT
		 u.id ,
		 u.extension_id ,
		 u.update_version ,
		 e.extension_alias ,
		 e.extension_name ,
		 e.extension_key ,
		 d.webstore_url
		FROM `#__extensions_updates` u
		LEFT JOIN `#__extensions` e ON ( u.extension_id = e.id )
		LEFT JOIN `#__extensions_developers` d ON ( e.developer_id = d.id )
		WHERE u.id = ?", $this->data['id'])->fetch() or abort();

        $json = (new Carrier)->getServerData($update);

        return [
            'is_close' => $json['is_close'],
            'values' => [
                'update_id'            => $update['id'],
                'download_url'        => $json['download_url'],
                'extension_key'     => $json['extension_key'],
                '_extension_key'    => $json['extension_key'],
                'clear'                => true,
                'decompress'        => true
            ]
        ];
    }

    /**
     * Get
     */
    public function getConfirmUpdateData()
    {
        // data
        $this->filter(POST, ['id' => 'id|array:first|required:abort']);

        // query
        $update = $this->db->safeFind("
		SELECT
		 u.id ,
		 u.extension_id ,
		 u.update_version ,
		 e.extension_alias ,
		 e.extension_name ,
		 e.extension_key ,
		 d.webstore_url
		FROM `#__extensions_updates` u
		LEFT JOIN `#__extensions` e ON ( u.extension_id = e.id )
		LEFT JOIN `#__extensions_developers` d ON ( e.developer_id = d.id )
		WHERE u.extension_id = ?
		AND u.status = 'available'
		ORDER BY u.released_at
		LIMIT 1", $this->data['id'])->fetch() or abort();

        $json = (new Carrier)->getServerData($update);

        return [
            'is_close' => $json['is_close'],
            'values' => [
                'update_id'        => $update['id'],
                'download_url'    => $json['download_url'],
                'extension_key'  => $json['extension_key'],
                '_extension_key' => $json['extension_key'],
            ]
        ];
    }

    /**
     * Get
     */
    public function getConfirmInstallData()
    {
        // data
        $this->filter(POST, ['id' => 'array:first|required:abort']);

        // installer
        $installer = new Unpackager();
        $installer->debug = true;

        return $this->data + $installer->getData($this->data['id']) + [
            'token' => FormSecurity::getToken()
        ];
    }

    /**
     * Get
     */
    public function getConfirmUnzipData()
    {
        // data
        $this->filter(POST, ['id' => 'array:first|required:abort']);

        return $this->data;
    }

    /**
     * Get
     */
    public function getConfirmUpdateAllData()
    {
        // data
        $this->filter(POST, ['id' => 'id|array']);

        // query
        if ($this->data['id']) {
            $this->db->where("id IN (?..)", $this->data['id']);
        }
        $this->db->where("status = 'available'");

        $num_updates = $this->db->safeFind("
		SELECT COUNT(*)
		FROM `#__extensions_updates`
		[WHERE]")->fetchColumn();

        return $this->data + ['num_updates' => $num_updates];
    }

    /**
     * Get confirm maintenance data
     */
    public function getConfirmMaintenanceData()
    {
        return ['status' => (new Maintenance)->getStatus()];
    }

    /**
     * Get show failure data
     */
    public function getShowFailureData()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $data = $this->db->safeFind("
		SELECT
		 u.failure_msg ,
		 u.update_version ,
		 e.extension_alias ,
		 e.extension_name
		FROM `#__extensions_updates` u
		LEFT JOIN `#__extensions` e ON ( u.extension_id = e.id )
		WHERE u.id = ?", $this->data['id'])->fetch() or abort();

        return $data;
    }

    /**
     * Scandir
     * 
     * @param string $dir
     * 
     * @return array
     */
    protected function scandir(string $dir): array
    {
        $cdir = is_readable($dir) ? scandir($dir) : false;

        if ($cdir) {
            $nodes = array_diff($cdir, ['.', '..']);
            rsort($nodes);
            //var_dump($nodes);
            return $nodes;
        }

        return [];
    }
}
