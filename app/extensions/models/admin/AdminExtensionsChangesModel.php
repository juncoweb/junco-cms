<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminExtensionsChangesModel extends Model
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
	public function getIndexData()
	{
		// data
		$this->filter(POST, [
			'id' => 'id|array:first|required:abort'
		]);

		$title = $this->db->safeFind("
		SELECT
		 IF (extension_name, extension_name, extension_alias)
		FROM `#__extensions`
		WHERE id = ?", $this->data['id'])->fetchColumn() or abort();

		return [
			'title' => $title,
			'data' => ['extension_id' => $this->data['id']]
		];
	}

	/**
	 * Get
	 */
	public function getListData()
	{
		// data
		$this->filter(POST, ['extension_id' => 'id|required:abort']);

		// query
		$this->db->where("extension_id = ?", $this->data['extension_id']);
		$pagi = $this->db->paginate("
		SELECT [id, change_description, created_at, status]*
		FROM `#__extensions_changes`
		[WHERE]
		[ORDER BY created_at DESC]");

		return $this->data + ['pagi' => $pagi];
	}

	/**
	 * Get
	 */
	public function getCreateData()
	{
		// data
		$this->filter(POST, ['extension_id' => 'id|required:abort']);

		return [
			'title' => _t('Create'),
			'values' => [
				'is_compatible' => true,
				'extension_id' => $this->data['extension_id']
			],
		];
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
		 extension_id ,
		 change_description ,
		 is_compatible
		FROM `#__extensions_changes`
		WHERE id = ?", $this->data['id'])->fetch() or abort();

		return [
			'title' => _t('Edit'),
			'values' => $data,
		];
	}

	/**
	 * Get
	 */
	public function getConfirmDeleteData()
	{
		// data
		$this->filter(POST, ['id' => 'id|array|required:abort']);

		return $this->data['id'];
	}
}
