<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class FrontUsysAccountModel extends Model
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
    public function getIndexData()
    {
        // query
        $data = $this->db->query("
		SELECT
		 id ,
		 fullname ,
		 username ,
		 email
		FROM `#__users`
		WHERE id = ?", curuser()->getId())->fetch();

        return [
            'values' => $data,
            'options' => config('usys.account_options')
        ];
    }
}
