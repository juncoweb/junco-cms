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
    protected int $id = 0;

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
        $this->filter(POST, [
            'id'              => 'id',
            'developer_name'  => 'text|required',
            'project_url'     => '',
            'webstore_url'    => '',
            'webstore_token'  => '',
            'default_credits' => 'required',
            'default_license' => 'required',
        ]);

        // extract
        $this->extract('id');

        // query
        if ($this->id) {
            $this->db->safeExec("UPDATE `#__extensions_developers` SET ?? WHERE id = ? AND is_protected = 0", $this->data, $this->id);
        } else {
            $this->db->safeExec("INSERT INTO `#__extensions_developers` (??, is_protected) VALUES (??, 0)", $this->data);
        }
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['developer_id' => 'id|required:abort']);

        // query
        $this->db->safeExec("DELETE FROM `#__extensions_developers` WHERE id = ? AND is_protected = 0", $this->data['developer_id']);
    }
}
