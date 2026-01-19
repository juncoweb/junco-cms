<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Exporter;

/**
 * Database Exporter JS Adapter
 */
class JSDataExporter implements ExporterInterface
{
    // vars
    protected $json    = [];

    /**
     * Add a comment header with some reference data to the buffer
     *
     * @param array  $data
     */
    public function addHeader(array $data = []): void {}

    /**
     * Add
     *
     * @param array $data
     * @param bool  $add_drop_database
     */
    public function addDatabase(array $data, bool $add_drop_database = false): void {}

    /**
     * Add a routines to the buffer
     * 
     * @param array $routines
     * @param bool  $add_drop_routine
     */
    public function addRoutines(array $routines, bool $add_drop_routine = false): void
    {
        foreach ($routines as $data) {
            $this->json[$data['Type']][$data['Name']] = [
                'Query'            => $data['MysqlQuery'],
                'MysqlQuery'    => $data['MysqlQuery'],
                'History'        => [],
            ];
        }
    }

    /**
     * Add the tables to the buffer
     * 
     * @param array $tables
     * @param bool  $add_drop_table
     */
    public function addTable(array $table, bool $add_drop_table = false): void
    {
        $Fields = [];
        foreach ($table['Fields'] as $Name => $Field) {
            $Fields[$Name]    = [
                'History' => [],
                'Describe' => $Field
            ];
        }

        $this->json['TABLE'][$table['Name']] = [
            'Query'            => $table['MysqlQuery'],
            'MysqlQuery'    => $table['MysqlQuery'],
            'History'        => [],
            'Fields'        => $Fields,
            'Indexes'        => $table['Indexes']
        ];
    }

    /**
     * Add registers into the buffer
     * 
     * @param array $data
     * @param bool  $use_ignore
     * @param int   $max_query_size
     */
    public function addRegisters(array $data, bool $use_ignore = false, int $max_query_size = 0): void {}

    /**
     * Add triggers into the buffer
     * 
     * @param array $triggers
     * @param bool  $add_drop_trigger
     */
    public function addTriggers($triggers, bool $add_drop_trigger = false): void
    {
        foreach ($triggers as $trigger) {
            $this->json['TRIGGER'][$trigger['Name']] = [
                'Query'            => $trigger['MysqlQuery'],
                'MysqlQuery'    => $trigger['MysqlQuery'],
                'History'        => [],
            ];
        }
    }

    /**
     * Render
     * 
     * @return array
     */
    public function render(): array
    {
        return $this->json;
    }

    /**
     * Used to record history of changes.
     * 
     * @param string|array $history
     */
    public function addHistory(string|array $history): void
    {
        if (!is_array($history)) {
            $history = (array)json_decode($history, true);
        }
        if (isset($history['TABLE'])) {
            $prefixer = db()->getPrefixer();
            $Tables = [];
            foreach ($history['TABLE'] as $TableName => $data) {
                $Tables[$prefixer->removeAllOnTableName($TableName)] = $data;
            }
            $history['TABLE'] = $Tables;
        }
        $this->json = array_merge_recursive($this->json, $history);
    }

    /**
     * Write
     * 
     * @param string $file
     */
    public function write(string $file): void
    {
        $buffer = json_encode($this->json, JSON_PRETTY_PRINT);

        if (false === file_put_contents($file, $buffer)) {
            throw new \Exception(_t('Failed to write the target file.'));
        }
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        $this->json = [];
    }
}
