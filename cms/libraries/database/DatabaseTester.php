<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class DatabaseTester
{
    // vars
    protected $schema = null;
    protected string $orig_prefix = '';
    protected string $test_prefix = 'test_';
    protected array  $tables      = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = config();
        $this->orig_prefix = $config->get('database.prefix');

        //
        $config->set('database.prefix', $this->test_prefix);
        $this->schema = db()->getSchema();
    }

    /**
     * 
     */
    public function useTables(string ...$tables): self
    {
        foreach ($tables as $table) {
            if (in_array($table, $this->tables)) {
                continue;
            }

            if (!$this->schema->tables()->has($table)) {
                $this->schema->tables()->copy(
                    $this->orig_prefix . $table,
                    $this->test_prefix . $table
                );
            } else {
                $this->schema->tables()->truncate($this->test_prefix . $table);
            }

            $this->tables[] = $table;
        }

        return $this;
    }

    /**
     * 
     */
    public function __destruct()
    {
        foreach ($this->tables as $table) {
            $this->schema->tables()->drop($this->test_prefix . $table);
        }

        config()->set('database.prefix', $this->orig_prefix);
        app()->unset('database');
    }
}
