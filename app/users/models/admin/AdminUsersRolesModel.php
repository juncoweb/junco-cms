<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminUsersRolesModel extends Model
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
        $data = $this->filter(POST, ['search' => 'text']);

        // query
        if ($data['search']) {
            $this->db->where("role_name LIKE %?", $data['search']);
        }
        $pagi = $this->db->paginate("
		SELECT [
		 id ,
		 role_name
		]* FROM `#__users_roles`
		[WHERE]
		[ORDER BY role_name]");

        $rows = [];
        foreach ($pagi->fetchAll() as $row) {
            $rows[] = $row;
        }

        return $data + [
            'rows' => $rows,
            'pagi' => $pagi
        ];
    }

    /**
     * Get
     */
    public function getCreateData()
    {
        // data
        $this->filter(POST, ['num_rows' => 'int|min:1|default:1']);

        return [
            'title' => _t('Create'),
            'values' => ['autoload' => true]
        ];
    }

    /**
     * Get
     */
    public function getEditData()
    {
        // data
        $data = $this->filter(POST, ['id' => 'id|array:first|required:abort']);

        // query
        $data = $this->db->query("
		SELECT
		 id AS role_id ,
		 role_name ,
		 role_description
		FROM `#__users_roles`
		WHERE id = ?", $data['id'])->fetch() or abort();

        return [
            'title' => _t('Edit'),
            'values' => $data
        ];
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        // data
        return $this->filter(POST, ['id' => 'id|array|required:abort']);
    }
}
