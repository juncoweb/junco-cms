<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminJobsModel extends Model
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
			$this->db->where("job_queue LIKE %?", $this->data['search']);
		}
		$pagi = $this->db->paginate("
		SELECT [
		 id,
		 job_queue ,
		 num_attempts ,
		 reserved_at ,
		 available_at ,
		 (SELECT COUNT(*) FROM `#__jobs_failures` WHERE job_id = j.id) AS num_failures
		]* FROM `#__jobs` j
		[WHERE]
		[ORDER BY available_at DESC]");

		$rows = [];
		if ($pagi->num_rows) {
			$failure_url = url('admin/jobs.failures') . '#/search=%d';

			foreach ($pagi->fetchAll() as $row) {
				$row['available_at'] = new Date($row['available_at']);
				$row['failure_url'] = sprintf($failure_url, $row['id']);

				$rows[] = $row;
			}
		}

		return $this->data + [
			'rows' => $rows,
			'pagi' => $pagi
		];
	}

	/**
	 * Get
	 */
	public function getShowData()
	{
		// data
		$this->filter(POST, ['id' => 'id|array:first|required:abort']);

		// query
		$data = $this->db->safeFind("
		SELECT
		 id,
		 job_queue ,
		 job_payload ,
		 num_attempts ,
		 reserved_at ,
		 available_at ,
		 created_at
		FROM `#__jobs`
		WHERE id = ?", $this->data['id'])->fetch() or abort();

		$data['available_at'] = new Date($data['available_at']);
		$data['created_at']   = new Date($data['created_at']);

		if ($data['reserved_at']) {
			$data['reserved_at'] = new Date($data['reserved_at']);
		}

		return $data;
	}
}
