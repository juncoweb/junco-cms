<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema;

use Junco\Database\Schema\Interface\SchemaInterface;

/**
 * Database Schema Postgres Adapter
 */
class PgsqlSchema implements SchemaInterface
{
    //
    protected $db;
    protected $prefixer;

    /**
     * Constructor
     */
    public function __construct(\Database $db)
    {
        $this->db = $db;
        $this->prefixer = $db->getPrefixer();
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getInfo(): array
    {
        return $this->db->getInfo();
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getEngines(): array
    {
        return $this->db
            ->query("SHOW ENGINES")
            ->fetchAll(\Database::FETCH_COLUMN, ['Engine' => 'Engine']);
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getCollations(): array
    {
        $collations = $this->db->query("SELECT * FROM pg_collation")->fetchAll();

        foreach ($collations as $i => $row) {
            $collations[$i] = [
                'Collation' => $row['collname'],
                'Charset' => explode('.', $row['collname'], 2)[1] ?? '',
            ];
        }

        return $collations;
    }

    /**
     * Has table
     * 
     * @param string $tbl_name
     * 
     * @return bool
     */
    public function hasTable(string $tbl_name): bool {}

    /**
     * Get all tables
     * 
     * @return array    An associative array of all tables.
     */
    public function getTables(): array {}

    /**
     * Show tables
     * 
     * @param array $where
     * 
     * @return array
     */
    public function showTables(array $where = []): array
    {
        if ($where) {
            foreach ($where as $field => $value) {
                if ($field == 'Search') {
                    $this->db->where("Name LIKE %?", $value);
                } elseif (is_string($value)) {
                    $this->db->where("`$field` = ?", $value);
                } else {
                    $this->db->where("`$field` IN ( ?.. )", $value);
                }
            }
        }

        return $this->db->query("SHOW TABLE STATUS [WHERE]")->fetchAll();
    }

    /**
     * Get Fields
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return array
     */
    public function getFields(string $TableName, array $where = []): array
    {
        if ($where) {
            foreach ($where as $field => $value) {
                if (is_string($value)) {
                    $this->db->where("`$field` = ?", $value);
                } else {
                    $this->db->where("`$field` IN ( ?.. )", $value);
                }
            }
        }

        return $this->db->query("DESCRIBE `$TableName` [WHERE]")->fetchAll(\Database::FETCH_COLUMN, [0 => 0]);
    }

    /**
     * Show fields
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return array
     */
    public function showFields(string $TableName, array $where = []): array
    {
        if ($where) {
            foreach ($where as $field => $value) {
                if ($field == 'CollationIsNot') {
                    $this->db->where("Collation IS NOT NULL");
                    $this->db->where("Collation <> ?", $value);
                } elseif (is_string($value)) {
                    $this->db->where("`$field` = ?", $value);
                } else {
                    $this->db->where("`$field` IN ( ?.. )", $value);
                }
            }
        }

        return $this->db->query("SHOW FULL FIELDS FROM `$TableName` [WHERE]")->fetchAll();
    }

    /**
     * Show Indexes
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return array
     */
    public function showIndexes(string $TableName, array $where = []): array
    {
        if ($where) {
            foreach ($where as $field => $value) {
                if (is_string($value)) {
                    $this->db->where("`$field` = ?", $value);
                } else {
                    $this->db->where("`$field` IN ( ?.. )", $value);
                }
            }
        }

        return $this->db->query("SHOW INDEX FROM `$TableName` [WHERE]")->fetchAll();
    }

    /**
     * Show triggers
     * 
     * @param array $where
     * 
     * @return array
     */
    public function showTriggers(array $where = []): array
    {
        $Triggers = [];
        $rows = $this->db->query("SHOW TRIGGERS")->fetchAll();

        foreach ($rows as $row) {
            $Triggers[$row['Table']][] = $row['Trigger'];
        }

        return $Triggers;
    }

    /**
     * Routines
     * 
     * @param array $where
     */
    public function showRoutines(array $where = []): array
    {
        // query
        $this->db->where("Db = DATABASE()");



        return $this->db->query("SHOW PROCEDURE STATUS [WHERE]")->fetchAll();
    }

    /**
     * Get
     *
     * @return array
     */
    public function getDatabaseData(): array
    {
        $db_name = $this->db->query("SELECT DATABASE()")->fetchColumn();
        $query = $this->db->query("SHOW CREATE DATABASE `$db_name`")->fetchColumn(1);

        return  [
            'Name'            => $db_name,
            'MysqlQuery'    => $query,
        ];
    }

    /**
     * Get
     * 
     * @param string $tbl_name
     * string $tbl_name
     * bool   $add_if_not_exists
     * bool   $add_auto_increment
     * bool   $set_db_prefix = false
     * 
     * @return array
     */
    public function getTableData(
        string $tbl_name,
        bool   $add_if_not_exists = false,
        bool   $add_auto_increment = false,
        bool   $set_db_prefix = false
    ): array {
        $data = [
            'Fields' => [],
            'Indexes' => [],
        ];

        // query
        $data['MysqlQuery'] = $this->db->query("SHOW CREATE TABLE `$tbl_name`")->fetchColumn(1);

        if ($add_if_not_exists) {
            $data['MysqlQuery'] = preg_replace('/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $data['MysqlQuery']);
        }
        if (!$add_auto_increment) {
            $data['MysqlQuery'] = preg_replace('/AUTO_INCREMENT=\d+ /', '', $data['MysqlQuery']);
        }
        if ($set_db_prefix) {
            $data['MysqlQuery'] = $this->prefixer->replaceWithUniversal($data['MysqlQuery'], $tbl_name);
            $data['Name'] = $this->prefixer->removeAllOnTableName($tbl_name);
        } else {
            $data['Name'] = $tbl_name;
        }

        // query - fields
        $rows = $this->db->query("SHOW FULL FIELDS FROM `$tbl_name`")->fetchAll();

        foreach ($rows as $row) {
            $Field = $row['Field'];
            unset($row['Field']);
            unset($row['Key']);
            unset($row['Privileges']);
            if ($row['Default'] == 'current_timestamp()') { // mariadb to mysql
                $row['Default'] = 'CURRENT_TIMESTAMP';
            }
            $data['Fields'][$Field]    = $row;
        }

        // query - indexes
        $rows = $this->db->query("SHOW INDEX FROM `$tbl_name`")->fetchAll();

        foreach ($rows as $row) {
            if (!isset($Indexes[$row['Key_name']])) {
                $Index = ['Columns' => []];
                if ($row['Key_name'] != 'PRIMARY') {
                    if ($row['Index_type'] == 'FULLTEXT') {
                        $Index['Type'] = 'FULLTEXT';
                    } elseif ($row['Non_unique'] == '0') {
                        $Index['Type'] = 'UNIQUE';
                    } else {
                        $Index['Type'] = 'INDEX';
                    }
                }
                $Indexes[$row['Key_name']] = $Index;
            }

            $data['Indexes'][$row['Key_name']]['Columns'][] = $row['Column_name'] . ($row['Sub_part'] ?  '(' . $row['Sub_part'] . ')' : '');
        }

        return $data;
    }

    /**
     * Get
     * 
     * @param string $tbl_name
     * @param bool   $set_db_prefix
     * 
     * @return array
     */
    public function getRegistersData(string $tbl_name, bool $set_db_prefix = false): array
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

    /**
     * Get trigger data
     * 
     * @param string $Trigger
     * @param array  $db_prefix_tables
     */
    public function getTriggerData(string $Trigger, array $db_prefix_tables = []): array
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
     * Get
     * 
     * @param string $Type   The values can be FUNCTION or PROCEDURE.
     * @param string $Name
     */
    public function getRoutineData(string $Type = '', string $Name = '', array $db_prefix_tables = []): array
    {
        // query
        $query = $this->db->query("SHOW CREATE $Type `$Name`")->fetchColumn(2);
        $query = preg_replace('/^CREATE (.*?) PROCEDURE/', 'CREATE PROCEDURE', $query);

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
     * Create table
     * 
     * @param string $TableName
     * @param array  $Table
     * 
     * @return int
     */
    public function createTable(string $TableName, array $Table): int
    {
        // security
        $this->validateTableName($TableName);

        $Table['PgsqlQuery'] ??= false;
        if ($Table['PgsqlQuery']) {
            $Table['PgsqlQuery'] = $this->prefixer->replaceWithLocal($Table['PgsqlQuery']);
            return $this->db->exec($Table['MysqlQuery']);
        }

        $TableName    = $this->prefixer->forceLocalOnTableName($TableName);
        $Fields        = $this->getFieldsStatement($Table['Fields']);
        $Indexes    = $this->getIndexesStatement($Table['Indexes']);
        //$options    = $this->getTableOptionsStatement($Table);

        if ($Indexes) {
            $Fields .= ", $Indexes";
        }

        return $this->db->exec("CREATE TABLE IF NOT EXISTS $TableName ($Fields)");
    }

    /**
     * Copy Table
     * 
     * @param string $TableName
     * @param string $FromTableName
     * @param bool   $CopyRegisters
     * 
     * @return int
     */
    public function copyTable(string $TableName, string $FromTableName, bool $CopyRegisters = false): int
    {
        // security
        $this->validateTableName($TableName);

        // copy
        $result = $this->db->exec("CREATE TABLE `$TableName` LIKE `$FromTableName`");

        if ($CopyRegisters) {
            return $this->db->exec("INSERT INTO `$TableName` SELECT * FROM `$FromTableName`");
        }
        return $result;
    }

    /**
     * Alter Table
     * 
     * @param string $TableName
     * @param array  $Table
     * 
     * @return int
     */
    public function alterTable(string $TableName, array $Table): int
    {
        //$options = $this->getTableOptionsStatement($Table);

        //return $this->db->exec("ALTER TABLE `$TableName` $options");
        return 0;
    }

    /**
     * Rename
     * 
     * @param string $CurTableName
     * @param string $NewTableName
     */
    public function renameTable(string $CurTableName, string $NewTableName): void
    {
        $this->db->exec("RENAME TABLE `$CurTableName` TO `$NewTableName`");
    }

    /**
     * Truncate
     * 
     * @param string|array $TableNames
     */
    public function truncateTable(string|array $TableNames): void
    {
        if (is_string($TableNames)) {
            $TableNames = [$TableNames];
        }

        foreach ($TableNames as $TableName) {
            $this->db->exec("TRUNCATE TABLE `$TableName`");
        }
    }

    /**
     * Drop
     * 
     * @param string|array $TableNames
     */
    public function dropTable(string|array $TableNames): void
    {
        if (is_array($TableNames)) {
            $TableNames = implode('`, `', $TableNames);
        }

        $this->db->exec("DROP TABLE IF EXISTS `$TableNames`");
    }

    /**
     * Alter Fields
     * 
     * @param string  $TableName
     * @param array	  $Fields
     * 
     * @return int
     */
    public function alterFields(string $TableName, array $Fields): int
    {
        $sql = [];
        foreach ($Fields as $FieldName => $Field) {
            if (!empty($Field['ChangeField'])) {
                $head = "CHANGE `$Field[ChangeField]` `$FieldName`";
            } elseif (!empty($Field['ModifyField'])) {
                $head = "MODIFY `$FieldName`";
            } else {
                $head = "ADD `$FieldName`";
            }

            $sql[] = $head . $this->getFieldStatement($Field);
        }
        $sql = implode(', ', $sql);

        return $this->db->exec("ALTER TABLE `$TableName` $sql");
    }

    /**
     * Drop Fields
     * 
     * @param string		$TableName
     * @param string|array	$FieldName
     * 
     * @return int
     */
    public function dropFields(string $TableName, string|array $FieldName): int
    {
        return $this->db->exec("ALTER TABLE `$TableName` DROP COLUMN `$FieldName`");
    }

    /**
     * Add Index
     * 
     * @param string $TableName
     * @param string $IndexName
     * @param array  $Index
     * 
     * @return int
     */
    public function addIndex(string $TableName, string $IndexName, array $Index): int
    {
        if ($IndexName == 'PRIMARY') {
            $IndexName = "PRIMARY KEY";
        } else {
            $IndexName = "$Index[Type] `$IndexName`";
        }
        $Columns = implode("`,`", $Index['Columns']);

        return $this->db->exec("ALTER TABLE `$TableName` ADD $IndexName (`$Columns`)");
    }

    /**
     * Drop Index
     * 
     * @param string $TableName
     * @param string $IndexName
     * @param array  $Index
     * 
     * @return int
     */
    public function dropIndex(string $TableName, string $IndexName, array $Index = []): int
    {
        if ($IndexName == 'PRIMARY') {
            return $this->db->exec("ALTER TABLE `$TableName` DROP PRIMARY KEY");
        }

        $this->db->exec("ALTER TABLE `$TableName` DROP $Index[Type] `$IndexName`");
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
    public function createTrigger(string $Trigger, string $Timing, string $Event, string $Table, string $Statement): int
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
    public function dropTrigger(string|array $TriggerName): int
    {
        if (is_array($TriggerName)) {
            $TriggerName = implode('`, `', $TriggerName);
        }

        return $this->db->exec("DROP TRIGGER IF EXISTS `$TriggerName`");
    }

    /**
     * Create Routine
     * 
     * @param string $Type
     * @param string $RoutineName
     * @param array  $Routine
     * 
     * @return void
     */
    public function createRoutine(string $Type, string $RoutineName, array $Routine): void
    {
        $Routine['MysqlQuery'] = $this->prefixer->replaceWithLocal($Routine['MysqlQuery']);

        $this->db->exec("DROP $Type IF EXISTS `$RoutineName`");
        $this->db->exec($Routine['MysqlQuery']);
    }

    /**
     * Validate Table Name
     * 
     * @param string $tbl_name
     * 
     * @throws \Exception
     */
    public function validateTableName(string $tbl_name): void
    {
        if (!preg_match('/^[\w]+$/', $tbl_name)) {
            throw new \Exception(_t('The name is not correct.'));
        }
    }

    /**
     * Get Field Statement
     * 
     * @param array  $Fields
     * 
     * @return string
     */
    protected function getFieldsStatement(array $Fields): string
    {
        foreach ($Fields as $FieldName => $Field) {
            $Fields[$FieldName] = "$FieldName " . $this->getFieldStatement($Field['Describe']);
        }

        return implode(', ', $Fields);
    }

    /**
     * Get table fields
     * 
     * @param array  $Fields
     * 
     * @return string
     */
    protected function getFieldStatement(array $Field): string
    {

        $sql = $this->getType($Field);

        if (!empty($Field['Collation'])) {
            //$sql .= " COLLATE $Field[Collation]";
        }
        if ($Field['Null'] ==  'NO') {
            $sql .= " NOT NULL";
        }
        if ($Field['Default'] !== null) {
            if (!in_array($Field['Default'], ['NULL', 'CURRENT_TIMESTAMP']) && !is_numeric($Field['Default'])) {
                $Field['Default'] = $this->db->quote($Field['Default']);
            }

            $sql .= " DEFAULT $Field[Default]";
        }
        if (!empty($Field['Extra'])) {
            if ($Field['Extra'] == 'DEFAULT_GENERATED') {
                //$sql .= " GENERATED ALWAYS AS (current_timestamp) STORED";
            } elseif (!in_array($Field['Extra'], ['auto_increment'])) {
                $sql .= " $Field[Extra]";
            }
        }
        if (!empty($Field['Comment'])) {
            $sql .= " COMMENT " . $this->db->quote($Field['Comment']);
        }
        if (!empty($Field['Position'])) {
            $sql .= ($Field['Position'] == 'FIRST')
                ? " $Field[Position]"
                : " AFTER `$Field[Position]`";
        }

        return $sql;
    }

    /**
     * Get Type
     * 
     * @param string $mysql_type
     * 
     * @return array
     */
    protected function getType(array $Field): string
    {
        if (!preg_match('/^([a-z]+)\s*(\(.*?\))?/', $Field['Type'], $match)) {
            return $Field['Type'];
        }
        $type      = $match[1];
        $precision = $match[2] ?? '';

        switch ($type) {
            // integer
            case 'tinyint':
                $type = 'smallint';
                // break;
            case 'smallint':
            case 'int':
            case 'bigint':
                if ($Field['Extra'] == 'auto_increment') {
                    return substr($type, 0, -3) . 'serial';
                }
                return $type;
                // decimal
            case 'float':
                return 'real' . $precision;
            case 'double':
                return 'double precision' . $precision;
            case 'decimal':
                if ($type == 'decimal' && $precision == '(19,2)') {
                    return 'money';
                }
                return 'decimal' . $precision;
                // text
            case 'varchar':
            case 'mediumtext':
                return 'varchar' . $precision;
            case 'longtext':
                return 'text';
                // date
            case 'date':
            case 'time':
                return $type;
            case 'datetime':
                return 'timestamp';
                // bit
            case 'varbinary':
            case 'image':
            case 'blob':
            case 'longblob':
                return 'bytea';
                // shape
            case 'linestring':
                return 'line';
        }

        return $Field['Type'];
    }

    /**
     * Get table fields
     * 
     * @param array  $Indexes
     * 
     * @return string
     */
    protected function getIndexesStatement(array $Indexes): string
    {
        foreach ($Indexes as $IndexName => $Index) {
            $Columns = "`" . implode('`, `', $Index['Columns']) . "`";

            if ($IndexName == 'PRIMARY') {
                $Indexes[$IndexName] = "PRIMARY KEY ($Columns)";
            } else {
                $Indexes[$IndexName] = "$IndexName ($Columns)";
            }
        }

        return implode(', ', $Indexes);
    }
}
