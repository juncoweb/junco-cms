<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class MenusModel extends Model
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
     * Save
     */
    public function save()
    {
        $data_array = $this->filterArray(POST, [
            'extension_id' => 'id',
            'menu_key'     => 'text',
            'menu_path'    => 'text',
            'menu_order'   => 'id|default',
            'menu_url'     => 'text',
            'menu_image'   => 'text',
            'menu_hash'    => 'text',
            'menu_params'  => '',
            'status'       => 'bool:0/1',
        ]) or abort();

        $data = $this->filter(POST, [
            'is_edit' => '',
            'id'      => 'id|array|only_if:is_edit|required:abort',
        ]);

        // validate
        foreach ($data_array as $i => $row) {
            if (!$row['extension_id']) {
                return $this->unprocessable(_t('Please, fill in the extension.') . sprintf(' (%d)', ++$i));
            }
            if (!$row['menu_key']) {
                return $this->unprocessable(_t('Please, fill in the key.') . sprintf(' (%d)', ++$i));
            }
            if (!$row['menu_path']) {
                return $this->unprocessable(_t('Please, fill in the name.') . sprintf(' (%d)', ++$i));
            }
        }

        // query
        if ($data['is_edit']) {
            $menu_path = [];
            foreach ($data_array as $i => $row) {
                $menu_path[] = $row['menu_path'];
            }

            $this->db->execAll("UPDATE `#__menus` SET ??, menu_default_path = IF(is_distributed = 1, ?, menu_default_path) WHERE id = ?", $data_array, $menu_path, $data['id']);
        } else {
            $this->db->execAll("INSERT INTO `#__menus` (??, menu_default_path) VALUES (??, menu_path)", $data_array);
        }

        // translate
        $translates = $this->getTranslates(array_column($data_array, 'extension_id'));

        foreach ($translates as $alias => $translate) {
            (new LanguageHelper)->translate('menus.' . $alias, $translate);
        }
    }

    /**
     * Status
     */
    public function status()
    {
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("UPDATE `#__menus` SET status = IF(status > 0, 0, 1) WHERE id IN (?..)", $data['id']);
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("DELETE FROM `#__menus` WHERE id IN (?..)", $data['id']);
    }

    /**
     * Lock
     */
    public function lock()
    {
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // security
        $this->db->query("
		SELECT COUNT(*)
		FROM `#__menus` m
		LEFT JOIN `#__extensions` e ON ( m.extension_id = e.id )
		LEFT JOIN `#__extensions_developers` d ON ( e.developer_id = d.id )
		WHERE m.id IN (?..)
		AND d.is_protected <> 0", $data['id'])->fetchColumn() and abort();

        // query
        $this->db->exec("UPDATE `#__menus` SET is_distributed = IF(is_distributed > 0, 0, 1) WHERE id IN ( ?.. )", $data['id']);
    }

    /**
     * Get
     */
    protected function getTranslates(array $extension_id): array
    {
        $rows = $this->db->query("
		SELECT
		 e.extension_alias ,
		 m.menu_default_path ,
		 m.menu_path
		FROM `#__menus` m
		LEFT JOIN `#__extensions` e ON ( m.extension_id = e.id )
		WHERE e.id IN (?..)", $extension_id)->fetchAll();

        $translates = [];
        foreach ($rows as $row) {
            $parts = explode('|', $row['menu_default_path']);
            $_text = array_pop($parts);

            if ($_text != ';') {
                $translates[$row['extension_alias']][] = $_text;
            }

            if ($row['menu_default_path'] != $row['menu_path']) {
                $parts = explode('|', $row['menu_path']);
                $_text = array_pop($parts);

                if ($_text != ';') {
                    $translates[$row['extension_alias']][] = $_text;
                }
            }
        }

        return $translates;
    }
}
