<?php


/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Database\Exporter\ExporterInterface;

class DatabaseExporter
{
    // vars
    protected $schema = null;
    protected $processes = [
        'hearder'  => '',
        'database' => '',
        'routines' => [],
        'tables'   => [],
        'triggers' => [],
    ];

    //
    public    bool   $set_db_prefix        = false;    // (Boolean) only for the methods: routine, table, inserts
    protected ?array $set_db_prefix_tables = null;
    //
    public    bool   $add_drop_database    = true;
    public    bool   $add_drop_routine     = true;
    public    bool   $add_drop_trigger     = true;
    // tables
    public    bool   $add_drop_table       = true;        // insert multiple records at once
    public    bool   $add_if_not_exists    = true;     // in create table add..
    public    bool   $add_auto_increment   = true;        // in create table add..
    // registers
    public    bool   $use_ignore           = false;    // use insert ignore
    public    bool   $extended_inserts     = true;        // insert multiple records at once
    public    int    $max_query_size       = 50000;    // maximum length of values string in insert clause

    /**
     * Constructor
     * 
     * @param Database $db
     */
    public function __construct(?Database $db = null)
    {
        if ($db === null) {
            $db = db();
        }
        $this->schema = $db->getSchema();
    }

    /**
     * Routines
     * 
     * @param int $option
     *  0 - ALL
     *  1 - FUNCTION
     *  2 - PROCEDURE
     */
    public function getRoutines(int $option = 0): array
    {
        if ($option == 1) {
            $where = ['Type' => 'FUNCTION'];
        } elseif ($option == 2) {
            $where = ['Type' => 'PROCEDURE'];
        } else {
            $where = [];
        }
        return $this->schema->routines()->fetchAll($where);
    }

    /**
     * Get all tables
     * 
     * @return array    An associative array of all tables.
     */
    public function getTables(): array
    {
        return $this->schema->tables()->list();
    }

    /**
     * Get all triggers
     * 
     * @return array
     */
    public function getTriggers(): array
    {
        $Triggers = [];
        foreach ($this->schema->triggers()->fetchAll() as $Trigger) {
            $Triggers[$Trigger['Table']][] = $Trigger['Trigger'];
        }

        return $Triggers;
    }

    /**
     * Add a comment header with some reference data to the buffer
     *
     * @param string $Comment Additional comment.
     */
    public function addHeader(string $Comment = '')
    {
        $this->processes['hearder'] = [
            'Info' => $this->schema->getInfo(),
            'Comment' => $Comment
        ];
    }

    /**
     * Add to the buffer the creation and use of the database
     */
    public function addDatabase()
    {
        $this->processes['database'] = $this->schema->database()->showData();
    }

    /**
     * Add a routine to the buffer
     * 
     * @param string $Type   The values can be FUNCTION or PROCEDURE.
     * @param string $Name
     */
    public function addRoutine(string $Type = '', string $Name = '')
    {
        if ($this->set_db_prefix) {
            $this->setTables();
        }
        $data = $this->schema->routines()->showData($Type, $Name, $this->set_db_prefix_tables);

        $this->processes['routines'][] = $data;
    }

    /**
     * Adds routines to the buffer by opening and closing a delimiter
     * 
     * @param array $routines   The format must be: ['Type' => '...', 'Name' => '...']
     */
    public function addRoutines(array $routines)
    {
        foreach ($routines as $row) {
            $this->addRoutine($row['Type'], $row['Name']);
        }
    }

    /**
     * Adds a table to the buffer
     * 
     * @param string $tbl_name
     */
    public function addTable(string $tbl_name)
    {
        $this->processes['tables'][$tbl_name]['Table'] = $this->schema->tables()->showData(
            $tbl_name,
            $this->add_if_not_exists,
            $this->add_auto_increment,
            $this->set_db_prefix
        );
    }

    /**
     * Add registers into the buffer
     * 
     * @param string $tbl_name
     */
    public function addRegisters(string $tbl_name = '')
    {
        $this->processes['tables'][$tbl_name]['Registers'] = $this->schema->registers()->showData(
            $tbl_name,
            $this->set_db_prefix
        );
    }

    /**
     * Adds a trigger into the buffer
     * 
     * @param string $Trigger
     */
    public function addTrigger(string $Trigger)
    {
        if ($this->set_db_prefix) {
            $this->setTables();
        }
        $data = $this->schema->triggers()->showData($Trigger, $this->set_db_prefix_tables);

        $this->processes['triggers'][] = $data;
    }

    /**
     * Add triggers into the buffer
     * 
     * @param array $Trigger
     */
    public function addTriggers(array $Triggers)
    {
        foreach ($Triggers as $Trigger) {
            $this->addTrigger($Trigger);
        }
    }

    /**
     * Get
     * 
     * @return ExporterInterface
     */
    public function getAsMysql(): ExporterInterface
    {
        return $this->render(new Junco\Database\Exporter\MysqlExporter());
    }

    /**
     * Get
     * 
     * @return ExporterInterface
     */
    public function getAsJS(): ExporterInterface
    {
        return $this->render(new Junco\Database\Exporter\JSDataExporter());
    }

    /**
     * Render
     * 
     * @param ExporterInterface $adapter
     * 
     * @return ExporterInterface $adapter
     */
    protected function render(ExporterInterface $adapter): ExporterInterface
    {
        if ($this->processes['hearder']) {
            $adapter->addHeader($this->processes['hearder']);
        }
        if ($this->processes['database']) {
            $adapter->addDatabase($this->processes['database'], $this->add_drop_database);
        }
        if ($this->processes['routines']) {
            $adapter->addRoutines($this->processes['routines'], $this->add_drop_routine);
        }
        if ($this->processes['tables']) {
            $max_query_size = $this->extended_inserts ? $this->max_query_size : 0;
            foreach ($this->processes['tables'] as $row) {
                if (isset($row['Table'])) {
                    $adapter->addTable($row['Table'], $this->add_drop_table);
                }
                if (isset($row['Registers'])) {
                    $adapter->addRegisters($row['Registers'], $this->use_ignore, $max_query_size);
                }
            }
        }

        if ($this->processes['triggers']) {
            $adapter->addTriggers($this->processes['triggers'], $this->add_drop_trigger);
        }

        return $adapter;
    }

    /**
     * Returns the current database tables????
     */
    protected function setTables(array $tbl_names = [])
    {
        if ($tbl_names) {
            $this->set_db_prefix_tables = $tbl_names;
        } elseif ($this->set_db_prefix_tables === null) {
            $this->set_db_prefix_tables = $this->getTables();
        }

        return $this->set_db_prefix_tables;
    }
}
