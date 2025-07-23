<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class MyNotificationsModel extends Model
{
    // vars
    protected $db;
    protected int $user_id;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
        $this->user_id = curuser()->id;
    }

    /**
     * Get
     */
    public function getListData()
    {
        // data
        $this->filter(POST, ['search' => 'text']);

        // query
        $this->db->where("user_id = ?", $this->user_id);

        if ($this->data['search']) {
            $this->db->where("notification_message LIKE %?", $this->data['search']);
        }
        $pagi = $this->db->paginate("
		SELECT [
		 id ,
		 notification_id ,
		 notification_type ,
		 notification_message ,
		 created_at ,
		 read_at
		]* FROM `#__notifications`
		[WHERE]
		[ORDER BY created_at DESC]");

        $rows = [];
        if ($pagi->num_rows) {
            foreach ($pagi->fetchAll() as $row) {
                $row['created_at'] = new Date($row['created_at']);

                $rows[] = $row;
            }

            $this->setUrl($rows);
        }

        return $this->data + [
            'rows' => $rows,
            'pagi' => $pagi
        ];
    }

    /**
     * Get
     */
    public function getShowData()
    {
        //
        $this->markAsRead();

        // query
        $pagi = $this->db->paginate("
		SELECT [
		 id ,
		 notification_id ,
		 notification_type ,
		 notification_message
		]* FROM `#__notifications`
		WHERE user_id = ?
		AND read_at IS NULL", $this->user_id);

        $num_notifications = 0;
        $rows = [];

        if ($pagi->num_rows) {
            if ($pagi->num_rows > $pagi->rows_per_page) {
                $num_notifications = $pagi->num_rows - $pagi->rows_per_page;
            }

            $rows = $pagi->fetchAll();
            $this->setUrl($rows);
        }

        return [
            'num_notifications' => $num_notifications,
            'rows' => $rows
        ];
    }

    /**
     * 
     */
    protected function setUrl(array &$rows): void
    {
        $url = [];

        foreach ($rows as $i => $row) {
            $type = $row['notification_type'];
            $url[$type] ??= Plugin::get('notification', 'url', $type)?->run() ?? '';

            $rows[$i]['url'] = $url[$type]
                ? strtr($url[$type], ['{id}' => $row['notification_id']])
                : '';
        }
    }

    /**
     * 
     */
    protected function markAsRead()
    {
        $this->db->safeExec("UPDATE `#__notifications` SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL", $this->user_id);
    }
}
