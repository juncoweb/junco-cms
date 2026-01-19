<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\RoutinesInterface;
use Database;

class Routines implements RoutinesInterface
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
     * Fetch
     * 
     * @param array $param
     * 
     * @return array
     */
    public function fetchAll(array $where = []): array
    {
        // query
        $this->db->where("ROUTINE_SCHEMA = DATABASE()");

        if ($where) {
            foreach ($where as $field => $value) {
                switch ($field) {
                    case 'Search':
                        $value = "^[a-z]*" . ucfirst($value) . ".*";
                        $this->db->where("`ROUTINE_NAME` REGEXP ?", $value);
                        break;
                    case 'Type':
                        $this->db->where("`ROUTINE_TYPE = ?", $value);
                        break;
                }
            }
        }

        return $this->db->query("
		SELECT
		 ROUTINE_SCHEMA AS Db,
		 ROUTINE_NAME AS Name,
		 ROUTINE_TYPE AS Type,
		 DEFINER AS Definer,
		 LAST_ALTERED AS Modified,
		 CREATED AS Created,
		 SECURITY_TYPE AS Security_type,
		 ROUTINE_COMMENT AS Comment,
		 CHARACTER_SET_CLIENT AS character_set_client,
		 COLLATION_CONNECTION AS collation_connection
		FROM `information_schema`.`ROUTINES`
		[WHERE]
		ORDER BY ROUTINE_NAME")->fetchAll();
    }

    /**
     * Get
     * 
     * @param string $Type   The values can be FUNCTION or PROCEDURE.
     * @param string $Name
     */
    public function showData(string $Type = '', string $Name = '', array $db_prefix_tables = []): array
    {
        // query
        $query = $this->db->query("SHOW CREATE $Type `$Name`")->fetchColumn(2);
        $query = preg_replace('/^CREATE (?:.*?) (PROCEDURE|FUNCTION)/', 'CREATE $1', $query);

        if ($db_prefix_tables) {
            $query = $this->prefixer->replaceWithUniversal($query, $db_prefix_tables);
        }

        return [
            'Type'            => $Type,
            'Name'            => $Name,
            'MysqlQuery'    => $query,
        ];
    }

    /**
     * Create
     * 
     * @param string $Type
     * @param string $RoutineName
     * @param array  $Routine
     * 
     * @return void
     */
    public function create(string $Type, string $RoutineName, array $Routine): void
    {
        $Routine['MysqlQuery'] = $this->prefixer->replaceWithLocal($Routine['MysqlQuery']);

        $this->db->exec("DROP $Type IF EXISTS `$RoutineName`");
        $this->db->exec($Routine['MysqlQuery']);
    }

    /**
     * Drop
     * 
     * @param string $Type
     * @param string $RoutineName
     * 
     * @return void
     */
    public function drop(string $Type, string $RoutineName): int
    {
        return $this->db->exec("DROP $Type IF EXISTS `$RoutineName`");
    }
}
