<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminExtensionsUpdatesModel extends Model
{
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Get list data
     */
    public function getListData()
    {
        // data
        $this->filter(POST, ['search' => 'text']);

        // query
        if ($this->data['search']) {
            $this->db->where("e.extension_alias LIKE %?|e.extension_name LIKE %?", $this->data['search']);
        }
        $this->db->where("u.status IN ('canceled', 'installed')");

        $pagi = $this->db->paginate("
		SELECT [
		 u.id,
		 u.update_version,
		 u.released_at,
		 u.has_failed,
		 u.status ,
         e.extension_alias ,
         e.extension_name
		]* FROM `#__extensions_updates` u
		LEFT JOIN `#__extensions` e  ON ( u.extension_id = e.id )
		[WHERE]
		[ORDER BY u.created_at DESC]");

        $rows = [];
        foreach ($pagi->fetchAll() as $row) {
            if (!$row['extension_name']) {
                $row['extension_name'] = $row['extension_alias'];
            }

            $rows[] = $row;
        }

        return $this->data + ['rows' => $rows, 'pagi' => $pagi];
    }
}
