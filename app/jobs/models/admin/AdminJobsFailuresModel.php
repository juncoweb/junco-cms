<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminJobsFailuresModel extends Model
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
        $this->filter(POST, ['search' => 'text']);

        // query
        if ($this->data['search']) {
            if (is_numeric($this->data['search'])) {
                $this->db->where("job_id = ?", (int)$this->data['search']);
            } else {
                $this->db->where("job_queue LIKE %?", $this->data['search']);
            }
        }
        $pagi = $this->db->paginate("
		SELECT [
		 id ,
		 job_uuid ,
		 job_queue ,
		 job_payload ,
		 job_error ,
		 created_at
		]* FROM `#__jobs_failures`
		[WHERE]
		[ORDER BY created_at DESC]");

        $rows = [];
        foreach ($pagi->fetchAll() as $row) {
            $row['created_at'] = new Date($row['created_at']);

            $rows[] = $row;
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
		 id ,
		 job_id ,
		 job_uuid ,
		 job_queue ,
		 job_payload ,
		 job_error ,
		 created_at
		FROM `#__jobs_failures`
		WHERE id = ?", $this->data['id'])->fetch() or abort();

        $data['created_at'] = new Date($data['created_at']);

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
