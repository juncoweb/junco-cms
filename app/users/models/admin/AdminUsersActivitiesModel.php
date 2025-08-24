<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\ActivityType;
use Junco\Users\UserActivity;

class AdminUsersActivitiesModel extends Model
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
     * Get
     */
    public function getListData()
    {
        // data
        $data = $this->filter(POST, [
            'search' => 'text',
            'field'  => '',
            'type'   => 'enum:users.activity_type',
        ]);

        // query
        if ($data['search']) {
            switch ($data['field']) {
                default:
                case 1:
                    $this->db->where("u.fullname LIKE %?", $data['search']);
                    break;
                case 2:
                    if (is_numeric($data['search'])) {
                        $this->db->where("u.id = ?", (int)$data['search']);
                    } else {
                        $this->db->where("u.username LIKE %?", $data['search']);
                    }
                    break;
                case 3:
                    $this->db->where("u.email LIKE %?", $data['search']);
                    break;
            }
        }
        if ($data['type']) {
            $this->db->where("a.activity_type = ?", $data['type']);
        }
        $pagi = $this->db->paginate("
		SELECT [
		 a.id ,
		 a.user_ip ,
		 a.activity_type ,
		 a.activity_code ,
		 a.activity_context ,
		 a.created_at ,
		 t.token_selector ,
		 t.modified_at ,
		 t.status ,
		 u.fullname
		]* FROM `#__users_activities` a
		LEFT JOIN `#__users` u ON ( a.user_id = u.id )
		[LEFT JOIN `#__users_activities_tokens` t ON ( t.activity_id = a.id )]
		[WHERE]
		[ORDER BY a.created_at DESC]");

        $rows = [];
        foreach ($pagi->fetchAll() as $row) {
            if (!$row['fullname']) {
                $row['fullname'] = inet_ntop($row['user_ip']);
            }

            $row['created_at']  = new Date($row['created_at']);
            $row['modified_at'] = $row['modified_at']
                ? $row['created_at']->formatInterval($row['modified_at'])
                : '';
            $row['message'] = $messages[$row['activity_code']] ??= UserActivity::getMessage($row['activity_code']);
            /* $row['activity_type'] = $row['activity_type']
                ? ($types[$row['activity_type']] ??= ActivityType::get($row['activity_type']))->title()
                : null; */

            $rows[$row['id']] = $row;
        }

        return [
            ...$data,
            'type'  => $this->data['type']?->name,
            'types' => ActivityType::getList([_t('All')]),
            'pagi'  => $pagi,
            'rows'  => $rows
        ];
    }
}
