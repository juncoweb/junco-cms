<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class ExtensionsDevelopersModel extends Model
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
        $data = $this->filter(POST, [
            'id'              => 'id',
            'developer_name'  => 'text|required',
            'project_url'     => '',
            'webstore_url'    => '',
            'webstore_token'  => '',
            'default_credits' => 'required',
            'default_license' => 'required',
        ]);

        // slice
        $developer_id = $this->slice($data, 'id');

        // query
        if ($developer_id) {
            $this->db->exec("UPDATE `#__extensions_developers` SET ?? WHERE id = ? AND is_protected = 0", $data, $developer_id);
        } else {
            $this->db->exec("INSERT INTO `#__extensions_developers` (??, is_protected) VALUES (??, 0)", $data);
        }
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['developer_id' => 'id|required:abort']);

        // query
        $this->db->exec("DELETE FROM `#__extensions_developers` WHERE id = ? AND is_protected = 0", $data['developer_id']);
    }
}
