<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class MenusModel extends Model
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
     * Save
     */
    public function save()
    {
        // data
        $this->filterArray(POST, [
            'extension_id'    => 'id',
            'menu_key'        => 'text',
            'menu_path'        => 'text',
            'menu_order'    => 'id|default',
            'menu_url'        => 'text',
            'menu_image'    => 'text',
            'menu_hash'        => 'text',
            'menu_params'    => '',
            'status'        => 'bool:0/1',
        ]) or abort();

        $this->filter(POST, [
            'is_edit'    => '',
            'id'        => 'id|array|only_if:is_edit|required:abort',
        ]);

        // validate
        foreach ($this->data_array as $i => $row) {
            if (!$row['extension_id']) {
                throw new Exception(_t('Please, fill in the extension.') . sprintf(' (%d)', ++$i));
            }
            if (!$row['menu_key']) {
                throw new Exception(_t('Please, fill in the key.') . sprintf(' (%d)', ++$i));
            }
            if (!$row['menu_path']) {
                throw new Exception(_t('Please, fill in the name.') . sprintf(' (%d)', ++$i));
            }
        }

        // query
        if ($this->data['is_edit']) {
            $menu_path    = [];
            foreach ($this->data_array as $i => $row) {
                $menu_path[] = $row['menu_path'];
            }

            $this->db->safeExecAll("UPDATE `#__menus` SET ??, menu_default_path = IF(is_distributed = 1, ?, menu_default_path) WHERE id = ?", $this->data_array, $menu_path, $this->data['id']);
        } else {
            $this->db->safeExecAll("INSERT INTO `#__menus` (??, menu_default_path) VALUES (??, menu_path)", $this->data_array);
        }

        // query
        $rows = $this->db->safeFind(
            "
		SELECT
		 e.extension_alias ,
		 m.menu_default_path ,
		 m.menu_path
		FROM `#__menus` m
		LEFT JOIN `#__extensions` e ON ( m.extension_id = e.id )
		WHERE e.id IN (?..)",
            array_column($this->data_array, 'extension_id')
        )->fetchAll(Database::FETCH_NUM);

        $translates = [];
        foreach ($rows as $row) {
            $_text = explode('|', $row[1]);
            $_text = array_pop($_text);

            if ($_text != ';') {
                $translates[$row[0]][] = $_text;
            }

            if ($row[1] != $row[2]) {
                $_text = explode('|', $row[2]);
                $_text = array_pop($_text);

                if ($_text != ';') {
                    $translates[$row[0]][] = $_text;
                }
            }
        }

        foreach ($translates as $alias => $translate) {
            (new LanguageHelper)->translate('menus.' . $alias, $translate);
        }
    }

    /**
     * Toggle
     */
    public function status()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->safeExec("UPDATE `#__menus` SET status = IF(status > 0, 0, 1) WHERE id IN (?..)", $this->data['id']);
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->safeExec("DELETE FROM `#__menus` WHERE id IN (?..)", $this->data['id']);
    }

    /**
     * Toggle
     */
    public function lock()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // security
        $this->db->safeFind("
		SELECT COUNT(*)
		FROM `#__menus` m
		LEFT JOIN `#__extensions` e ON ( m.extension_id = e.id )
		LEFT JOIN `#__extensions_developers` d ON ( e.developer_id = d.id )
		WHERE m.id IN (?..)
		AND d.is_protected <> 0", $this->data['id'])->fetchColumn() and abort();

        // query
        $this->db->safeExec("UPDATE `#__menus` SET is_distributed = IF(is_distributed > 0, 0, 1) WHERE id IN ( ?.. )", $this->data['id']);
    }

    /**
     * Store
     */
    public function maker()
    {
        // data
        $this->filter(POST, [
            'extension_id'        => 'id|required',
            'menu_title'        => '',
            'menu_subcomponent'    => '',
            'menu_keys'            => 'array|required',
            'menu_folder'        => 'in:Contents,Media,More,Security,Site spaces,System,Templates,Tools,User spaces,Usys|required:abort',
            'menu_image'        => 'text',
        ]);

        $extension = $this->getExtension($this->data['extension_id']) or abort();

        if (!$this->validateSubcomponent($this->data['menu_subcomponent'])) {
            throw new Exception(sprintf(_t('The «%s» is incorrect.'), _t('Component')));
        }

        //
        $data = [];
        foreach ($this->data['menu_keys'] as $key) {
            $component = $extension['alias'];

            if ($this->data['menu_subcomponent']) {
                $component .= '.' . $this->data['menu_subcomponent'];
            }

            if ($key == 'settings-Default') {
                $menu_url = sprintf('admin/settings,key=%s', $component);
            } else {
                switch ($key) {
                    case 'backend-Default':
                    case 'dashboard':
                        $menu_url = 'admin/%s,';
                        break;
                    case 'frontend-Main':
                    case 'sitemap-Default':
                        $menu_url = '/%s,';
                        break;
                    case 'my-Default':
                        $menu_url = 'my/%s,';
                }

                $menu_url = sprintf($menu_url, $component);
            }

            if (!$this->data['menu_title']) {
                $this->data['menu_title'] = $extension['name'] ?: $extension['alias'];
            }

            $data[] = [
                'menu_key'        => $key,
                'menu_path'        => (in_array($key, ['backend-Default', 'settings-Default']) ? $menu_folder . '|' : '') . $this->data['menu_title'],
                'menu_order'    => in_array($key, ['frontend-Main', 'my-Default', 'profile-Default']) ? 10 : 0,
                'menu_url'        => $menu_url,
                'menu_image'    => in_array($key, ['backend-Default', 'frontend-Main']) ? '' : ($menu_image ?: 'fa-solid fa-file-lines'),
                'menu_hash'        => $extension['alias'] . ($this->data['menu_subcomponent'] ? '-' . $this->data['menu_subcomponent'] : ''),
                'menu_params'    => '',
                'status'        => 1
            ];
        }

        $xdata = null;
        $data = [
            'data' => $data,
            'extension_id' => $extension['id'],
            'extension_alias' => $extension['alias']
        ];

        Plugins::get('xdata', 'import', 'menus')->run($xdata, $data);
    }

    /**
     * Get
     */
    protected function getExtension(int $extension_id): false|array
    {
        return $this->db->safeFind("
		SELECT
		 id,
		 extension_alias AS alias,
		 extension_name AS name
		FROM `#__extensions`
		WHERE id = ?", $extension_id)->fetch();
    }

    /**
     * Validate
     */
    protected function validateSubcomponent(string $subcomponent): bool
    {
        if (!$subcomponent) {
            return true;
        }

        return preg_match('/^[a-z][a-z0-9]*$/', $subcomponent);
    }
}
