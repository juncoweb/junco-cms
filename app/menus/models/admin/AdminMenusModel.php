<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminMenusModel extends Model
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
        // data
        $this->filter(POST, [
            'search'    => 'text',
            'field'        => 'int|min:1|max:2|default:1',
            'menu_key'    => '',
        ]);

        // query
        if ($this->data['menu_key']) {
            $this->db->where("m.menu_key = ?", $this->data['menu_key']);
            $this->db->rows_per_page = 999;
        }
        if ($this->data['search']) {
            switch ($this->data['field']) {
                case 1:
                    $this->db->where("m.menu_path LIKE %?", $this->data['search']);
                    break;
                case 2:
                    $this->db->where("e.extension_alias LIKE %?|e.extension_name LIKE %?", $this->data['search']);
                    break;
            }
        }
        $pagi = $this->db->paginate("
		SELECT [
		 m.id ,
		 m.menu_key ,
		 m.menu_path ,
		 m.menu_order as ordering ,
		 m.is_distributed ,
		 m.status ,
		 e.extension_name ,
		 d.is_protected
		]* FROM `#__menus` m
		LEFT JOIN `#__extensions` e ON ( m.extension_id = e.id )
		LEFT JOIN `#__extensions_developers` d ON ( e.developer_id = d.id )
		[WHERE]
		[ORDER BY m.menu_key, m.menu_path]");

        $rows = [];
        foreach ($pagi->fetchAll() as $row) {
            $path                = explode('|', $row['menu_path']);
            $row['depth']        = count($path) - 1;
            $row['menu_name']    = $path[$row['depth']];
            $rows[]    = $row;
        }

        if ($this->data['menu_key']) {
            $rows = Nestedset::sort($rows);
        }

        return $this->data + [
            'menu_keys' => $this->getMenuKeys(),
            'pagi' => $pagi,
            'rows' => $rows
        ];
    }

    /**
     * Get
     */
    public function getCreateData()
    {
        // data
        $this->filter(POST, ['num_rows' => 'int|min:1|default:1']);

        return [
            'title' => _t('Create'),
            'values' => array_fill(0, $this->data['num_rows'], null),
            'extensions' => $this->getExtensions(),
            'is_edit' => false,
        ];
    }

    /**
     * Get
     */
    public function getEditData()
    {
        // data
        $this->filter(POST, ['id' => 'array|required:abort']);

        // query
        $data = $this->db->safeFind("
		SELECT
		 id ,
		 extension_id ,
		 menu_key ,
		 menu_path ,
		 menu_order ,
		 menu_url ,
		 menu_image ,
		 menu_hash ,
		 menu_params ,
		 status ,
		 is_distributed
		FROM `#__menus`
		WHERE id IN (?..)
		ORDER BY menu_path, menu_order", $this->data['id'])->fetchAll() or abort();

        return [
            'title' => _t('Edit'),
            'values' => $data,
            'extensions' => $this->getExtensions(),
            'is_edit' => true,
        ];
    }

    /**
     * Get
     */
    public function getCopyData()
    {
        // data
        $this->filter(POST, ['id' => 'array|required:abort']);

        // query
        $data = $this->db->safeFind("
		SELECT
		 extension_id ,
		 menu_key ,
		 menu_path ,
		 menu_order ,
		 menu_url ,
		 menu_image ,
		 menu_hash ,
		 menu_params ,
		 status ,
		 is_distributed
		FROM `#__menus`
		WHERE id IN (?..)
		ORDER BY menu_path, menu_order", $this->data['id'])->fetchAll() or abort();

        return [
            'title' => _t('Copy'),
            'values' => $data,
            'extensions' => $this->getExtensions(),
            'is_edit' => false,
        ];
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        return $this->data;
    }

    /**
     * Get
     */
    protected function getMenuKeys()
    {
        return $this->db->safeFind("
		SELECT menu_key
		FROM `#__menus`
		GROUP BY menu_key
		ORDER BY menu_key")->fetchAll(Database::FETCH_COLUMN, [0 => 0], [_t('All keys')]);
    }

    /**
     * Get
     */
    protected function getExtensions()
    {
        // extensions
        return $this->db->safeFind("
		SELECT id, extension_name
		FROM `#__extensions`
		ORDER BY extension_name")->fetchAll(Database::FETCH_COLUMN, [0 => 1], ['--- ' . _t('Select') . ' ---']);
    }
}
