<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class ExtensionsChangesModel extends Model
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
            'id'                 => 'id',
            'change_description' => 'text|required',
            'is_compatible'      => 'bool:0/1',
            'extension_id'       => 'id|only_if_not:id|required:abort'
        ]);

        // extract
        $this->extract('id');

        // query
        if ($this->id) {
            $this->db->exec("UPDATE `#__extensions_changes` SET ?? WHERE id = ? AND status = 0", $this->data, $this->id);
        } else {
            $this->db->exec("INSERT INTO `#__extensions_changes` (??) VALUES (??)", $this->data);
        }
    }


    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("DELETE FROM `#__extensions_changes` WHERE id IN (?..) AND status = 0", $this->data['id']);
    }
}
