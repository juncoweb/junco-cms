<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminUsersActivitiesModel extends Model
{
	// vars
	protected $db = null;


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
			'search' => 'text',
			'field' => '',
			'type' => '',
		]);

		$types = [_t('All'), 'signup', 'activation', 'login', 'autologin', 'savepwd', 'savemail', 'token'];

		// query
		if ($this->data['search']) {
			switch ($this->data['field']) {
				default:
				case 1:
					$this->db->where("u.fullname LIKE %?", $this->data['search']);
					break;
				case 2:
					if (is_numeric($this->data['search'])) {
						$this->db->where("u.id = ?", (int)$this->data['search']);
					} else {
						$this->db->where("u.username LIKE %?", $this->data['search']);
					}
					break;
				case 3:
					$this->db->where("u.email LIKE %?", $this->data['search']);
					break;
			}
		}
		if ($this->data['type'] && isset($types[$this->data['type']])) {
			$this->db->where("a.activity_type = ?", $types[$this->data['type']]);
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

		return $this->data + ['types' => $types, 'pagi' => $pagi];
	}
}
