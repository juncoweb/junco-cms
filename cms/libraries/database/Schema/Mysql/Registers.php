<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\RegistersInterface;
use Database;

class Registers implements RegistersInterface
{
    //
    protected $db;
    protected $prefixer;

    /**
     * Constructor
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->prefixer = $db->getPrefixer();
    }

    /**
     * Get
     * 
     * @param string $tbl_name
     * @param bool   $set_db_prefix
     * 
     * @return array
     */
    public function showData(string $tbl_name, bool $set_db_prefix = false): array
    {
        // query
        $rows = $this->db->query("SELECT * FROM `$tbl_name`")->fetchAll();

        if ($set_db_prefix) {
            $tbl_name = $this->prefixer->putUniversalOnTableName($tbl_name);
        }

        return [
            'Table' => $tbl_name,
            'Rows'    => $rows
        ];
    }
}
