<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class JobsFailuresModel extends Model
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
     * Delete
     */
	public function delete()
	{
		$data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("DELETE FROM `#__jobs_failures` WHERE id IN (?..)", $data['id']);
	}
}
