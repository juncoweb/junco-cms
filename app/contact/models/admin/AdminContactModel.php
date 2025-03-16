<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminContactModel extends Model
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
		]);

		// query
		if ($this->data['search']) {
			$this->db->where("contact_name LIKE %?|contact_message LIKE %?", $this->data['search']);
		}

		$pagi = $this->db->paginate("
		SELECT [
		 c.id ,
		 c.user_id ,
		 c.contact_name ,
		 c.contact_email ,
		 c.contact_message ,
		 c.created_at ,
		 c.status ,
		 u.fullname
		]* FROM `#__contact` c
		[LEFT JOIN `#__users` u ON ( c.user_id = u.id )]
		[WHERE]
		[ORDER BY c.created_at DESC]");

		$rows = [];
		foreach ($pagi->fetchAll() as $row) {
			$row['created_at'] = new Date($row['created_at']);
			$rows[] = $row;
		}

		return $this->data + ['rows' => $rows, 'pagi' => $pagi];
	}

	/**
	 * Get show data
	 */
	public function getShowData()
	{
		// data
		$this->filter(POST, ['id' => 'id|array|required:abort']);

		// query
		$data = $this->db->safeFind("
		SELECT
		 c.id,
		 c.user_id,
		 c.user_ip,
		 c.contact_name,
		 c.contact_email,
		 c.contact_message,
		 c.created_at,
		 c.status ,
		 u.fullname
		FROM `#__contact` c
		LEFT JOIN `#__users` u ON ( c.user_id = u.id )
		WHERE c.id = ?", $this->data['id'])->fetch() or abort();

		$data['contact_message']	= nl2br($data['contact_message']);
		$data['user_ip']			= inet_ntop($data['user_ip']);
		$data['created_at']			= new Date($data['created_at']);

		if ($data['user_id']) {
			$data['user_url'] = url('admin/users') . sprintf('#/search=%s&field=2', $data['user_id']);
		}

		return $data;
	}

	/**
	 * Get
	 */
	public function getConfirmDeleteData()
	{
		// data
		$this->filter(POST, ['id' => 'id|array|required:abort']);

		return $this->data;
	}
}
