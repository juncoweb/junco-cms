<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class SamplesModel extends Model
{
	// vars
	protected $samples = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// data
		$this->samples = new Samples;
	}

	/**
	 * Get
	 */
	public function getListData()
	{
		// data
		$this->filter(POST, [
			'search' => 'text',
			'field' => 'id|max:2',
		]);

		return $this->data + [
			'rows' => $this->samples->fetchAll($this->data['search'], $this->data['field'])
		];
	}

	/**
	 * Get
	 */
	public function getShowData()
	{
		// data
		$this->filter(GET, ['key' => 'required:abort']);

		define('IS_TEST', true);

		$file = $this->samples->getFileFromKey($this->data['key']);

		if (!is_file($file)) {
			throw new Exception('File not found: ' . $file);
		}

		return include $file;
	}

	/**
	 * Get
	 */
	public function getEditData()
	{
		// data
		$this->filter(POST, ['id' => 'array:first']);

		return [
			'values' => $this->samples->fetch($this->data['id'])
		];
	}

	/**
	 * Update
	 */
	public function update()
	{
		// data
		$this->filter(POST, [
			'key'			=> '',
			'title'			=> 'text',
			'description'	=> 'multiline',
			'image'			=> 'text',
		]);

		$this->samples->save($this->data['key'], $this->data);
	}
}
