<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\UserStatus;

class AdminUsersModel extends Model
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
    public function getIndexData()
    {
        return ['statuses' => UserStatus::getList(true)];
    }

    /**
     * Get
     */
    public function getListData()
    {
        $data = $this->filter(POST, [
            'search'  => 'text',
            'field'   => 'id',
            'role_id' => 'id',
            'order'   => 'int',
            'sort'    => 'text'
        ]);

        // query
        $sql_join = '';
        if ($data['role_id']) {
            $this->db->where("m.role_id = ?", $data['role_id']);
            $sql_join = "LEFT JOIN `#__users_roles_map` m ON ( m.user_id = u.id )";
        }
        if ($data['search']) {
            switch ($data['field']) {
                default:
                case 1:
                    $this->db->where("u.fullname LIKE %?", $data['search']);
                    $data['field'] = 1;
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
        $this->db->order($data['order'], [
            1 => 'u.fullname',
            2 => 'u.created_at',
        ], 2);
        $this->db->sort($data['sort'], 'desc');
        $pagi = $this->db->paginate("
		SELECT [
		 u.id ,
		 u.fullname ,
		 u.created_at ,
		 u.status
		]* FROM `#__users` u
		$sql_join
		[WHERE]
		[ORDER]");

        $rows = [];
        foreach ($pagi->fetchAll() as $row) {
            $row['created_at'] = new Date($row['created_at']);
            $row['status'] = $statuses[$row['status']] ??= UserStatus::{$row['status']}->fetch();
            $row['roles'] = [];

            $rows[$row['id']] = $row;
        }

        return $data + [
            'rows' => $this->setRoles($rows),
            'pagi' => $pagi,
            'roles' => $this->getRoles([_t('All roles')]),
        ];
    }

    /**
     * Get
     */
    public function getCreateData()
    {
        return [
            'title' => _t('Create'),
            'values' => null,
        ];
    }

    /**
     * Get
     */
    public function getEditData()
    {
        $data = $this->filter(POST, ['id' => 'id|array:first']);

        // query
        $data = $this->db->query("
		SELECT
		 id AS user_id,
		 fullname ,
		 username ,
		 email
		FROM `#__users`
		WHERE id = ?", $data['id'])->fetch() or abort();

        return [
            'title' => _t('Edit'),
            'values' => $data + ['role_id' => (new UsersRolesMapper)->get($data['user_id'])],
        ];
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        return $this->filter(POST, ['id' => 'id|array|required:abort']);
    }

    /**
     * Get
     */
    public function getUsersData()
    {
        $data = $this->filter(POST, ['q' => 'text']);
        $limit = 48;

        // query
        if ($data['q']) {
            $this->db->where("fullname LIKE %?", $data['q']);
        }
        $rows = $this->db->query("
		SELECT id, fullname, email
		FROM `#__users`
		[WHERE]
		LIMIT $limit")->fetchAll(Database::FETCH_NUM);

        return [
            'rows' => $rows,
            'isAll' => count($rows) !== $limit
        ];
    }

    /**
     * Get
     */
    public function getRolesData()
    {
        $data = $this->filter(POST, ['q' => 'text']);
        $limit = 48;

        // query
        if ($data['q']) {
            $this->db->where("role_name LIKE %?", $data['q']);
        }
        $rows = $this->db->query("
		SELECT id, role_name
		FROM `#__users_roles`
		[WHERE]
		LIMIT $limit")->fetchAll(Database::FETCH_NUM);

        return [
            'rows' => $rows,
            'isAll' => count($rows) !== $limit
        ];
    }

    /**
     * Get
     */
    protected function getRoles(array $base = []): array
    {
        return $this->db->query("
		SELECT id, role_name
		FROM `#__users_roles`
		ORDER BY role_name")->fetchAll(Database::FETCH_COLUMN, [0 => 1], $base);
    }

    /**
     * Set
     */
    protected function setRoles(array $rows): array
    {
        if ($rows) {
            $roles = $this->db->query("
			SELECT 
			 m.user_id, 
			 m.role_id, 
			 r.role_name
			FROM `#__users_roles_map` m
			LEFT JOIN `#__users_roles` r ON ( m.role_id = r.id )
			WHERE m.user_id IN (?..)
			ORDER BY r.role_name", array_keys($rows))->fetchAll();

            foreach ($roles as $role) {
                $rows[$role['user_id']]['roles'][$role['role_id']] = $role['role_name'];
            }
        }

        return $rows;
    }
}
