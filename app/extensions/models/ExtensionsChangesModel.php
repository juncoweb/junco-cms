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
            'id'                 => 'id',
            'change_description' => 'text|required',
            'is_compatible'      => 'bool:0/1',
            'extension_id'       => 'id|only_if_not:id|required:abort'
        ]);

        // slice
        $change_id = $this->slice($data, 'id');

        // query
        if ($change_id) {
            $this->db->exec("UPDATE `#__extensions_changes` SET ?? WHERE id = ? AND status = 0", $data, $change_id);
        } else {
            $this->db->exec("INSERT INTO `#__extensions_changes` (??) VALUES (??)", $data);
        }
    }


    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("DELETE FROM `#__extensions_changes` WHERE id IN (?..) AND status = 0", $data['id']);
    }
}
