<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Sqlite;

use Junco\Database\Schema\Interface\_DatabaseInterface;
use Database;

class _Database implements _DatabaseInterface
{
    //
    protected $db;

    /**
     * Constructor
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getEngines(): array
    {
        return [];
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getCollations(): array
    {
        return []; // TODO
    }

    /**
     * Get
     *
     * @return array
     */
    public function showData(): array
    {
        $data = $this->db->query("PRAGMA database_list")->fetch();

        return  [
            'Name' => $data['name'],
            'File' => $data['file'],
            'SqliteQuery' => '',
        ];
    }
}
