<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

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
		return (bool)$this->db->safeFind("SHOW TABLE STATUS WHERE Name = ?", $this->prefixer->forceLocalOnTableName($tbl_name))->fetch();
	}

	/**
	 * Get all tables
	 * 
	 * @return array    A numeric array with all the tables in the database.
	 */
	public function list(): array
	{
		return $this->db
			->safeFind("SHOW TABLES")
			->fetchAll(\Database::FETCH_COLUMN, [0 => 0]);
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
					$this->db->where("Name LIKE %?", $value);
				} elseif (is_string($value)) {
					$this->db->where("`$field` = ?", $value);
				} else {
					$this->db->where("`$field` IN ( ?.. )", $value);
				}
			}
		}

		return $this->db->safeFind("SHOW TABLE STATUS [WHERE]")->fetchAll();
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
		$_tbl_name = $this->prefixer->forceLocalOnTableName($tbl_name);
		$data = [
			'Fields' => [],
			'Indexes' => [],
		];

		// query
		$data['MysqlQuery'] = $this->db->safeFind("SHOW CREATE TABLE `$_tbl_name`")->fetchColumn(1);

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
		$rows = $this->db->safeFind("SHOW FULL FIELDS FROM `$_tbl_name`")->fetchAll();

		foreach ($rows as $row) {
			$Field = $row['Field'];
			unset($row['Field']);
			unset($row['Key']);
			unset($row['Privileges']);
			if ($row['Default'] == 'current_timestamp()') { // mariadb to mysql
				$row['Default'] = 'CURRENT_TIMESTAMP';
			}
			$data['Fields'][$Field]	= $row;
		}

		// query - indexes
		$rows = $this->db->safeFind("SHOW INDEX FROM `$_tbl_name`")->fetchAll();

		foreach ($rows as $row) {
			if (!isset($data['Indexes'][$row['Key_name']])) {
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

			$data['Indexes'][$row['Key_name']]['Columns'][] = $row['Column_name'] . ($row['Sub_part'] ?  '(' . $row['Sub_part'] . ')' : '');
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

		$Table['MysqlQuery'] ??= false;
		if ($Table['MysqlQuery']) {
			$Table['MysqlQuery'] = $this->prefixer->replaceWithLocal($Table['MysqlQuery']);
			return $this->db->safeExec($Table['MysqlQuery']);
		}

		$TableName	= $this->prefixer->forceLocalOnTableName($TableName);
		$Fields		= $this->getFieldsStatement($Table['Fields']);
		$Indexes	= $this->getIndexesStatement($Table['Indexes']);
		$options    = $this->getTableOptionsStatement($Table);

		if ($Indexes) {
			$Fields .= ", $Indexes";
		}

		return $this->db->safeExec("CREATE TABLE IF NOT EXISTS $TableName ($Fields) $options");
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
	public function copy(string $TableName, string $FromTableName, bool $CopyRegisters = false): int
	{
		// security
		$this->validateName($TableName);

		// copy
		$result = $this->db->safeExec("CREATE TABLE `$TableName` LIKE `$FromTableName`");

		if ($CopyRegisters) {
			return $this->db->safeExec("INSERT INTO `$TableName` SELECT * FROM `$FromTableName`");
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
	public function alter(string $TableName, array $Table): int
	{
		$options = $this->getTableOptionsStatement($Table);

		return $this->db->safeExec("ALTER TABLE `$TableName` $options");
	}

	/**
	 * Rename
	 * 
	 * @param string $CurTableName
	 * @param string $NewTableName
	 */
	public function rename(string $CurTableName, string $NewTableName): void
	{
		// security
		$this->validateName($NewTableName);

		$this->db->safeExec("RENAME TABLE `$CurTableName` TO `$NewTableName`");
	}

	/**
	 * Truncate
	 * 
	 * @param string|array $TableNames
	 */
	public function truncate(string|array $TableNames): void
	{
		if (is_string($TableNames)) {
			$TableNames = [$TableNames];
		}

		foreach ($TableNames as $TableName) {
			$this->db->safeExec("TRUNCATE TABLE `$TableName`");
		}
	}

	/**
	 * Drop
	 * 
	 * @param string|array $TableNames
	 */
	public function drop(string|array $TableNames): void
	{
		if (is_array($TableNames)) {
			$TableNames = implode('`, `', $TableNames);
		}

		$this->db->safeExec("DROP TABLE IF EXISTS `$TableNames`");
	}

	/**
	 * Validate Table Name
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
