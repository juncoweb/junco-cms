<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Exporter;

/**
 * Database Schema Mysqli Adapter
 */
class MysqlExporter implements ExporterInterface
{
	// vars
	protected $buffer				= '';
	public    $EOL					= PHP_EOL;	//
	public    $delimiter_keyword	= '$$';		// delimiter

	/**
	 * Add a comment header with some reference data to the buffer
	 *
	 * @param array  $data
	 * @param string $comment Additional comment.
	 */
	public function addHeader(array $data = []): void
	{
		$this->buffer .= '-- Database dump' . $this->EOL;
		//
		foreach ($data['Info'] as $name => $value) {
			$this->buffer .= '-- ' . $name . ': ' . $value . $this->EOL;
		}
		$this->buffer .= '-- PHP version: ' . phpversion() . $this->EOL;
		$this->buffer .= '-- date: ' . date('Y-m-d H:i:s', time()) . $this->EOL;
		//
		if ($data['Comment']) {
			$this->buffer .= '-- ' . str_replace('\n', $this->EOL . '-- ', $data['Comment']);
		}
		$this->buffer .= $this->EOL . $this->EOL;
	}

	/**
	 * Add
	 *
	 * @param array $database
	 * @param bool  $add_drop_database
	 */
	public function addDatabase(array $database, bool $add_drop_database = false): void
	{
		$this->addTitle($database['Name']);
		$this->buffer .= ($add_drop_database ? "" : "-- ") . "DROP DATABASE `" . $database['Name'] . "`;" . $this->EOL;
		$this->buffer .= $database['MysqlQuery'] . ';' . $this->EOL;
		$this->buffer .= "USE `" . $database['Name'] . "`;" . $this->EOL . $this->EOL;
	}

	/**
	 * Add a routines to the buffer
	 * 
	 * @param array $routines
	 * @param bool  $add_drop_routine
	 */
	public function addRoutines(array $routines, bool $add_drop_routine = false): void
	{
		$this->addTitle('Routines');
		$this->openDelimiter();

		foreach ($routines as $data) {
			$this->addSeparator($data['Name']);

			if ($add_drop_routine) {
				$this->buffer .= "DROP {$data['Type']} IF EXISTS `{$data['Name']}`" . $this->delimiter_keyword . $this->EOL;
			}

			$this->buffer .= $data['MysqlQuery'] . $this->delimiter_keyword . $this->EOL . $this->EOL;
		}

		$this->closeDelimiter();
		$this->addSeparator();
	}

	/**
	 * Add the tables to the buffer
	 * 
	 * @param array $tables
	 * @param bool  $add_drop_table
	 */
	public function addTable(array $table, bool $add_drop_table = false): void
	{
		$this->addTitle($table['Name']);
		$this->buffer .= ($add_drop_table ? '' : '-- ') . "DROP TABLE IF EXISTS `" . $table['Name'] . "`;" . $this->EOL;
		$this->buffer .= $table['MysqlQuery'] . ';' . $this->EOL . $this->EOL;
	}

	/**
	 * Add registers into the buffer
	 * 
	 * @param array $data
	 * @param bool  $use_ignore
	 * @param int   $max_query_size
	 */
	public function addRegisters(array $data, bool $use_ignore = false, int $max_query_size = 0): void
	{
		$this->addSeparator("insert into `{$data['Table']}`");

		if (!$data['Rows']) {
			return;
		}

		// vars
		$insert = "INSERT "
			. ($use_ignore ? "IGNORE " : "")
			. "INTO `" . $data['Table'] . "` "
			. "(`" . implode('`, `', array_keys($data['Rows'][0])) . "`) "
			. "VALUES";
		$values		= [];
		$sum_size 	= 0;

		foreach ($data['Rows'] as $row) {
			$partial = [];
			foreach ($row as $value) {
				if (is_null($value)) {
					$value = 'NULL';
				} elseif (!is_numeric($value)) {
					$value = '\'' . str_replace(['\'', "\x00", "\x0a", "\x0d", "\x1a"], ['\'\'', '\0', '\n', '\r', '\Z'], $value) . '\'';
				}
				$partial[] = $value;
			}

			$line = '(' . implode(', ', $partial) . ')';

			// extended
			if ($max_query_size) {
				$strlen     = strlen($line);
				$query_size = $sum_size + $strlen;

				if (($query_size > $max_query_size) && $values) {
					$this->buffer .= $insert . $this->EOL . implode(',' . $this->EOL, $values) . ';' . $this->EOL;
					$values = [];
					$sum_size   = $strlen;
				} else {
					$sum_size  += $strlen;
				}
			}

			$values[] = $line;
		}

		if ($values) {
			if ($max_query_size && $values) {
				$this->buffer .= $insert . $this->EOL . implode(',' . $this->EOL, $values) . ';' . $this->EOL;
			} else {
				foreach ($values as $value) {
					$this->buffer .= $insert . ' ' . $value . ';' . $this->EOL;
				}
			}

			$this->buffer .= $this->EOL . $this->EOL;
		}
	}

	/**
	 * Add triggers into the buffer
	 * 
	 * @param array $triggers
	 * @param bool  $add_drop_trigger
	 */
	public function addTriggers($triggers, bool $add_drop_trigger = false): void
	{
		$this->addTitle('Triggers');
		$this->openDelimiter();

		foreach ($triggers as $trigger) {
			$this->addSeparator("trigger `{$trigger['Name']}`");

			$this->buffer .= ($add_drop_trigger ? '' : '-- ') . "DROP TRIGGER IF EXISTS `{$trigger['Name']}`" . $this->delimiter_keyword . $this->EOL;
			$this->buffer .= $trigger['MysqlQuery'] . $this->delimiter_keyword . $this->EOL . $this->EOL;
		}

		$this->closeDelimiter();
	}

	/**
	 * Used to record history of changes.
	 * 
	 * @param string|array $history
	 */
	public function addHistory(string|array $history): void
	{
		// Unsupported
	}

	/**
	 * Render
	 * 
	 * @return string
	 */
	public function render(): string
	{
		return $this->buffer;
	}

	/**
	 * Write
	 * 
	 * @param string $dirpath
	 * @param string $basename
	 * 
	 * @throws Exception
	 */
	public function write(string $file): void
	{
		if (false === file_put_contents($file, $this->buffer)) {
			throw new \Exception(_t('Failed to write the target file.'));
		}
	}

	/**
	 * Add to the buffer a comment that can work as a title of a possible block
	 *
	 * @param string $title Additional text.
	 */
	protected function addTitle(string $title = '')
	{
		$this->buffer .= '-- ' . $this->EOL
			. '-- ' . $title . $this->EOL
			. '-- ' . $this->EOL . $this->EOL;
	}

	/**
	 * Add a comment to the buffer that can work as a separator of possible blocks
	 *
	 * @param string $title Additional text.
	 */
	protected function addSeparator(string $title = '')
	{
		$this->buffer .= '-- ' . $title
			. (($count = strlen($title)) < 46 ? ' ' . str_repeat('-', 45 - $count) : '')
			. $this->EOL . $this->EOL;
	}

	/**
	 * Open delimiter
	 */
	protected function openDelimiter()
	{
		$this->buffer .= 'DELIMITER ' . $this->delimiter_keyword . $this->EOL . $this->EOL;
	}

	/**
	 * Close delimiter
	 */
	protected function closeDelimiter()
	{
		$this->buffer .= 'DELIMITER ;' . $this->EOL . $this->EOL;
	}

	/**
	 * Destruct
	 */
	public function __destruct()
	{
		$this->buffer = '';
	}
}
