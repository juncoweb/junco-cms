<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class NotificationsModel extends Model
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
     * Status
     */
    public function status()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->safeExec("UPDATE `#__notifications` SET status = IF(status > 0, 0, 1) WHERE id IN (?..)", $this->data['id']);
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->safeExec("DELETE FROM `#__notifications` WHERE id IN (?..)", $this->data['id']);
    }
}
