<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\TriggersInterface;
use Database;

class Triggers implements TriggersInterface
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
     * Show triggers
     * 
     * @param array $where
     * 
     * @return array
     */
    public function fetchAll(array $where = []): array
    {
        if ($where) {
            foreach ($where as $field => $value) {
                if ($field == 'Name') {
                    $field = 'Trigger';
                }
                if (is_string($value)) {
                    $this->db->where("`$field` = ?", $value);
                } else {
                    $this->db->where("`$field` IN ( ?.. )", $value);
                }
            }
        }

        return $this->db->query("SHOW TRIGGERS [WHERE]")->fetchAll();
    }

    /**
     * Get trigger data
     * 
     * @param string $Trigger
     * @param array  $db_prefix_tables
     */
    public function showData(string $Trigger, array $db_prefix_tables = []): array
    {
        $query = $this->db->query("SHOW CREATE TRIGGER `$Trigger`")->fetchColumn(2);
        $query = preg_replace('@^CREATE (.*?) TRIGGER@', 'CREATE TRIGGER', $query);

        if ($db_prefix_tables) {
            $query = $this->prefixer->replaceWithUniversal($query, $db_prefix_tables);
        }

        return [
            'Name'            => $Trigger,
            'MysqlQuery'    => $query,
        ];
    }

    /**
     * Create Trigger
     * 
     * @param string $Trigger
     * @param string $Timing
     * @param string $Event
     * @param string $Table
     * @param string $Statement
     * 
     * @return int
     */
    public function create(string $Trigger, string $Timing, string $Event, string $Table, string $Statement): int
    {
        return $this->db->exec("CREATE TRIGGER `$Trigger` $Timing $Event ON `$Table` FOR EACH ROW $Statement");
    }

    /**
     * Drop Trigger
     * 
     * @param string|array $TriggerName
     * 
     * @return int
     */
    public function drop(string|array $TriggerName): int
    {
        if (is_array($TriggerName)) {
            $TriggerName = implode('`, `', $TriggerName);
        }

        return $this->db->exec("DROP TRIGGER IF EXISTS `$TriggerName`");
    }
}
