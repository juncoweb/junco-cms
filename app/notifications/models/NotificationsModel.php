<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
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
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("UPDATE `#__notifications` SET status = IF(status > 0, 0, 1) WHERE id IN (?..)", $data['id']);
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("DELETE FROM `#__notifications` WHERE id IN (?..)", $data['id']);
    }

    /**
     * Data
     */
    public function data(): array
    {
        $user_id = curuser()->getId();

        if (!$user_id) {
            return ['error' => 1];
        }

        // query
        $total = db()->query("
		SELECT COUNT(*)
		FROM `#__notifications`
		WHERE user_id = ?
		AND read_at IS NULL", $user_id)->fetchColumn();

        return [
            'total' => $total
        ];
    }
}
