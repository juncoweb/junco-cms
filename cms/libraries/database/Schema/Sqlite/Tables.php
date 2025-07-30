<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Sqlite;

use Junco\Database\Schema\Interface\TablesInterface;
use Database;
use Exception;

class Tables implements TablesInterface
{
    // use
    use FieldsTrait;
    use IndexesTrait;

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
     * Has table
     * 
     * @param string $tbl_name
     * 
     * @return bool
     */
    public function has(string $tbl_name): bool
    {
        $tbl_name = $this->prefixer->forceLocalOnTableName($tbl_name);

        return (bool)$this->db->query("PRAGMA table_info($tbl_name)")->fetch();
    }

    /**
     * Get all tables
     * 
     * @return array   An associative array of all tables.
     */
    public function list(): array
    {
        return $this->db->query("
        SELECT name
        FROM sqlite_master
        WHERE type = 'table'
        AND tbl_name NOT LIKE 'sqlite_%'")->fetchAll(\Database::FETCH_COLUMN, [0 => 0]);
    }

    /**
     * Show tables
     * 
     * @param array $where
     * 
     * @return array
     */
    public function fetchAll(array $where = []): array
    {
        if ($where) {
            foreach ($where as $field => $value) {
                if ($field === 'Search') {
                    $this->db->where("tbl_name LIKE %?", $value);
                } elseif (is_string($value)) {
                    $this->db->where("`$field` = ?", $value);
                } else {
                    $this->db->where("`$field` IN ( ?.. )", $value);
                }
            }
        }
        $this->db->where("tbl_name NOT LIKE 'sqlite_%'");

        $rows = $this->db->query("SELECT * FROM sqlite_master [WHERE]")->fetchAll();

        foreach ($rows as $i => $row) {
            unset($row['sql']);
            $rows[$i] = array_merge($row, [
                'Name' => $row['tbl_name'],
                'Engine' => '',
                'Version' => '',
                'Row_format' => '',
                'Rows' => 0,
                'Avg_row_length' => 0,
                'Data_length' => 0,
                'Max_data_length' => 0,
                'Index_length' => 0,
                'Data_free' => 0,
                'Auto_increment' => NULL,
                'Create_time' => null,
                'Update_time' => NULL,
                'Check_time' => NULL,
                'Collation' => '' /* TODO ??? */,
                'Checksum' => NULL,
                'Create_options' => '',
                'Comment' => ''
            ]);
        }

        return $rows;
    }

    /**
     * Get
     * 
     * @param string $tbl_name
     * @param string $tbl_name
     * @param bool   $add_if_not_exists
     * @param bool   $add_auto_increment
     * @param bool   $set_db_prefix = false
     * 
     * @return array
     */
    public function showData(
        string $tbl_name,
        bool   $add_if_not_exists = false,
        bool   $add_auto_increment = false,
        bool   $set_db_prefix = false
    ): array {
        $local_tbl_name = $this->prefixer->forceLocalOnTableName($tbl_name);
        $data = [
            'Name' => '',
            'Fields' => [],
            'Indexes' => [],
        ];

        // query
        $data['SqliteQuery'] = $this->db->query("SELECT sql FROM sqlite_master WHERE tbl_name='$local_tbl_name'")->fetchColumn();

        if ($add_if_not_exists) {
            $data['SqliteQuery'] = $this->addIfNotExists($data['SqliteQuery']);
        }
        if (!$add_auto_increment) {
            $data['SqliteQuery'] = $this->removeAutoIncrement($data['SqliteQuery']);
        }
        if ($set_db_prefix) {
            $data['SqliteQuery'] = $this->prefixer->replaceWithUniversal($data['SqliteQuery'], $tbl_name);
            $data['Name'] = $this->prefixer->removeAllOnTableName($tbl_name);
        } else {
            $data['Name'] = $tbl_name;
        }

        // query - fields
        $rows = $this->db->query("PRAGMA table_info($local_tbl_name)")->fetchAll();

        foreach ($rows as $row) {
            $data['Fields'][$row['name']] = [
                'Type'      => $row['type'],
                'Collation' => null,
                'Null'      => $row['notnull'] ? 'NO' : 'YES',
                'Default'   => $row['dflt_value'],
                'Extra'     => '', // auto_increment or .. current ???????
                'Comment'   => '',
                // ?
                'pk'        => $row['pk'],
            ];
        }

        // query - indexes
        $rows = $this->db->query("PRAGMA index_list($local_tbl_name)")->fetchAll();
        /* header("Content-Type: text/plain");
        var_dump($rows);
        die(); */

        foreach ($rows as $row) { // TODO
            /* if (!isset($data['Indexes'][$row['Key_name']])) {
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
                $data['Indexes'][$row['Key_name']] = $Index;
            }

            $data['Indexes'][$row['Key_name']]['Columns'][] = $row['Column_name'] . ($row['Sub_part'] ?  '(' . $row['Sub_part'] . ')' : ''); */
        }

        return $data;
    }

    /**
     * Create Table
     * 
     * @param string $TableName
     * @param array  $Table
     * 
     * @return int
     */
    public function create(string $TableName, array $Table): int
    {
        // security
        $this->validateName($TableName);

        $Table['SqliteQuery'] ??= false;
        if ($Table['SqliteQuery']) {
            $Table['SqliteQuery'] = $this->prefixer->replaceWithLocal($Table['SqliteQuery']);
            return $this->db->exec($Table['SqliteQuery']);
        }

        $TableName = $this->prefixer->forceLocalOnTableName($TableName);
        $Fields    = $this->getFieldsStatement($Table['Fields']);
        $Indexes   = $this->getIndexesStatement($Table['Indexes']);
        $options   = $this->getTableOptionsStatement($Table);

        if ($Indexes) {
            $Fields .= ", $Indexes";
        }

        return $this->db->exec("CREATE TABLE IF NOT EXISTS $TableName ($Fields) $options");
    }

    /**
     * Copy Table
     * 
     * @param string $FromTableName
     * @param string $ToTableName
     * @param bool   $CopyRegisters
     * 
     * @return int
     */
    public function copy(string $FromTableName, string $ToTableName, bool $CopyRegisters = false): int
    {
        // security
        $this->validateName($ToTableName);

        return $CopyRegisters
            ? $this->db->exec("CREATE TABLE $ToTableName AS SELECT * FROM $FromTableName")
            : $this->db->exec("CREATE TABLE $ToTableName AS SELECT * FROM $FromTableName WHERE 0");
    }

    /**
     * Alter Table
     * 
     * @param string $TableName
     * @param array  $Table
     * 
     * @return int
     */
    public function alter(string $TableName, array $Table): int
    {
        /* $options = $this->getTableOptionsStatement($Table);

        return $this->db->exec("ALTER TABLE `$TableName` $options"); */
    }

    /**
     * Rename
     * 
     * @param string $FromTableName
     * @param string $ToTableName
     * 
     * @return int
     */
    public function rename(string $FromTableName, string $ToTableName): int
    {
        // security
        $this->validateName($ToTableName);

        return $this->db->exec("ALTER TABLE $FromTableName RENAME TO $ToTableName");
    }

    /**
     * Truncate
     * 
     * @param string|array $TableNames
     * 
     * @return int
     */
    public function truncate(string|array $TableNames): int
    {
        if (!is_array($TableNames)) {
            $TableNames = [$TableNames];
        }

        $sql1 = '';
        $sql2 = '';
        foreach ($TableNames as $TableName) {
            $sql1 .= "DELETE FROM $TableName;";
            $sql2 .= "UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = '$TableName';";
        }

        $this->db->exec($sql1);
        return $this->db->exec($sql2);
    }

    /**
     * Drop
     * 
     * @param string|array $TableNames
     * 
     * @return int
     */
    public function drop(string|array $TableNames): int
    {
        if (!is_array($TableNames)) {
            $TableNames = [$TableNames];
        }

        $sql = '';
        foreach ($TableNames as $TableName) {
            $sql .= "DROP TABLE IF EXISTS $TableName;";
        }

        return $this->db->exec($sql);
    }

    /**
     * Validate Table Name                       //   IT IS A TRAIT?
     * 
     * @param string $tbl_name
     * 
     * @throws \Exception
     */
    public function validateName(string $tbl_name): void
    {
        if (!preg_match('/^(#__)?[\w]+$/', $tbl_name)) {
            throw new \Exception(_t('The name is not correct.'));
        }
    }

    /**
     * Add "create table if not exists"                       //   IT IS A TRAIT?
     * 
     * @param string $sql
     * 
     * @return string
     */
    protected function addIfNotExists(string $sql): string
    {
        return preg_replace('/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $sql);
    }

    /**
     * Return AUTO_INCREMENT statement                       //   IT IS A TRAIT?
     * 
     * @param string $sql
     * 
     * @return string
     */
    protected function removeAutoIncrement(string $sql): string
    {
        return preg_replace('/AUTO_INCREMENT=\d+ /', '', $sql);
    }

    /**
     * Get Table Statement
     * 
     * @param array  $Table
     * 
     * @return string
     */
    protected function getTableOptionsStatement(array $Table): string
    {
        $sql = "";
        if (!empty($Table['Engine'])) {
            $sql .= " ENGINE = $Table[Engine]";
        }
        if (!empty($Table['Collation'])) {
            $sql .= " DEFAULT" . $this->getCollationStatement($Table['Collation']);
        }
        if (isset($Table['Comment'])) {
            $sql .= " COMMENT = " . $this->db->quote($Table['Comment']);
        }

        if (!empty($Table['Auto_increment'])) {
            $val = (int)$Table['Auto_increment'];
            $sql .= " AUTO_INCREMENT = $val";
        }

        return $sql;
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
     * Get
     * 
     * @param array  $Indexes
     * 
     * @return string
     */
    protected function getIndexesStatement(array $Indexes): string
    {
        foreach ($Indexes as $IndexName => $Index) {
            $Columns = $this->getIndexColumnsStatement($Index['Columns']);

            if ($IndexName == 'PRIMARY') {
                $Indexes[$IndexName] = "PRIMARY KEY ($Columns)";
            } else {
                $Indexes[$IndexName] = "$IndexName ($Columns)";
            }
        }

        return implode(', ', $Indexes);
    }
}
