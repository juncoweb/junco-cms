<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class FrontContactModel extends Model
{
	// vars
	protected $db = null;
	protected $config = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->db = db();
		$this->config = config('contact');
	}

	/**
	 * Get
	 */
	public function getIndexData()
	{
		return [
			'snippet' => $this->config['contact.snippet'],
			'options' => $this->config['contact.options']
		];
	}

	/**
	 * Get
	 */
	public function getMessageData()
	{
		return [
			'options' => $this->config['contact.options']
		];
	}
}
