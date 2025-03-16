<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminExtensionsDevelopersModel extends Model
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
		$this->filter(POST, ['search' => 'text']);

		// query
		if ($this->data['search']) {
			$this->db->where("developer_name LIKE %?", $this->data['search']);
		}
		$pagi = $this->db->paginate("
		SELECT
		 id ,
		 developer_name ,
		 is_protected
		FROM `#__extensions_developers`
		[WHERE]
		[ORDER BY developer_name]");

		return $this->data + ['pagi' => $pagi];
	}

	/**
	 * Get
	 */
	public function getEditData()
	{
		// data
		$this->filter(POST, ['id' => 'id|array:first|required:abort']);

		// query
		$data = $this->db->safeFind("
		SELECT
		 id ,
		 developer_name ,
		 project_url ,
		 webstore_url ,
		 webstore_token ,
		 default_credits ,
		 default_license ,
		 is_protected
		FROM `#__extensions_developers`
		WHERE id = ?", $this->data['id'])->fetch() or abort();

		return [
			'title' => _t('Edit'),
			'values' => $data,
			'is_protected' => $data['is_protected'],
		];
	}

	/**
	 * Get
	 */
	public function getDeleteData()
	{
		// data
		$this->filter(POST, ['id' => 'id|array:first|required:abort']);

		// security
		$security = $this->db->safeFind(
			"
		SELECT
		 COUNT(*) AS total,
		 (SELECT COUNT(*) FROM `#__extensions` WHERE developer_id = ?) AS num_extensions
		FROM `#__extensions_developers`
		WHERE id = ?
		AND is_protected = 1",
			$this->data['id'],
			$this->data['id']
		)->fetch();

		if ($security['total']) {
			throw new Exception(_t('You are trying to delete a protected item.'));
		}
		if ($security['num_extensions']) {
			throw new Exception(_t('Please, remove all extensions from this developer.'));
		}

		return $this->data;
	}
}
