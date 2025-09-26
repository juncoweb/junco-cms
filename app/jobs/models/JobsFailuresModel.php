<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class JobsFailuresModel extends Model
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
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("UPDATE `#__jobs_failures` SET status = IF(status > 0, 0, 1) WHERE id IN (?..)", $data['id']);
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("DELETE FROM `#__jobs_failures` WHERE id IN (?..)", $data['id']);
    }
}
