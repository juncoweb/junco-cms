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
        $data = $this->filter(POST, ['search' => 'text']);

        // query
        if ($data['search']) {
            if (is_numeric($data['search'])) {
                $this->db->where("job_id = ?", (int)$data['search']);
            } else {
                $this->db->where("job_queue LIKE %?", $data['search']);
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

        return $data + [
            'rows' => $rows,
            'pagi' => $pagi
        ];
    }

    /**
     * Get
     */
    public function getShowData()
    {
        $input = $this->filter(POST, ['id' => 'id|array:first|required:abort']);

        // query
        $data = $this->db->query("
		SELECT
		 id ,
		 job_id ,
		 job_uuid ,
		 job_queue ,
		 job_payload ,
		 job_error ,
		 created_at
		FROM `#__jobs_failures`
		WHERE id = ?", $input['id'])->fetch() or abort();

        return $data;
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        return $this->filter(POST, ['id' => 'id|array|required:abort']);
    }
}
