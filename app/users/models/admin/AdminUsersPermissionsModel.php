<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminUsersPermissionsModel extends Model
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
        $this->filter(POST, [
            'role_id' => 'id',
            'search' => 'text',
        ]);

        // vars
        $roles = $this->getRoles();

        //
        if (empty($roles[$this->data['role_id']])) {
            $this->data['role_id'] = array_key_first($roles);
        }

        // query
        $this->db->rows_per_page = 9999;
        $this->db->setParam($this->data['role_id']);

        if ($this->data['search']) {
            $this->db->where("e.extension_name LIKE %?|l.label_key LIKE %?|l.label_name LIKE %?", $this->data['search']);
        }
        $pagi = $this->db->paginate("
		SELECT
		 l.id ,
		 l.label_key ,
		 l.label_name ,
		 l.label_description ,
		 e.extension_name ,
		 (SELECT status
		  FROM `#__users_roles_labels_map` p
		  WHERE l.id = p.label_id
		  AND role_id = ?
		 ) AS status
		FROM `#__users_roles_labels` l
		LEFT JOIN `#__extensions` e ON ( l.extension_id = e.id )
		[WHERE]
		[ORDER BY extension_name, label_name]");

        $rows = [];
        foreach ($pagi->fetchAll() as $row) {
            if (!$row['label_name']) {
                $row['label_name'] = $row['extension_name'];

                if ($row['label_key']) {
                    $row['label_name'] .= ' - ' . ucfirst($row['label_key']);
                }
            }

            $rows[$row['id']] = $row;
        }

        return $this->data + [
            'roles' => $roles,
            'rows' => $rows
        ];
    }

    /**
     * Get
     */
    protected function getRoles()
    {
        return $this->db->safeFind("
		SELECT id, role_name
		FROM `#__users_roles`
		ORDER BY role_name")->fetchAll(Database::FETCH_COLUMN, [0 => 1]);
    }
}
