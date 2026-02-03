<?php


/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database;

use Junco\Database\Exporter\ExporterInterface;
use Junco\Database\Exporter\JSonExporter;
use Junco\Database\Exporter\SqlExporter;
use Junco\Database\Schema\Interface\Entity\RoutineInterface;
use Junco\Database\Schema\Interface\Entity\TableInterface;
use Junco\Database\Schema\Interface\Entity\TriggerInterface;
use Database;
use Exception;

class Exporter
{
    // vars
    protected $db;
    protected $schema;
    protected ?object $options = null;
    protected ?Prefixer $prefixer = null;
    protected $processes = [
        'Header'   => '',
        'Database' => '',
        'Routines' => [],
        'Tables'   => [],
        'Triggers' => [],
    ];

    /**
     * Constructor
     * 
     * @param Database $db
     */
    public function __construct(?Database $db = null)
    {
        $this->db      = $db ?? db();
        $this->schema  = $this->db->getSchema();
        $this->options = $this->setOptions();
    }

    /**
     * Set
     * 
     * @param bool $add_drop_database
     * @param bool $add_drop_routine
     * @param bool $add_drop_trigger 
     * @param bool $add_drop_table   
     * @param bool $add_if_not_exists
     * @param bool $add_auto_increment
     * @param bool $use_ignore          Use insert ignore
     * @param bool $extended_inserts    Insert multiple records at once
     * @param int  $max_query_size 
     * 
     * @return self
     */
    public function setOptions(
        bool $use_universal_prefix = false,
        bool $add_drop_database    = true,
        bool $add_drop_routine     = true,
        bool $add_drop_trigger     = true,
        bool $add_drop_table       = true,
        bool $add_if_not_exists    = true,
        bool $add_auto_increment   = true,
        bool $use_ignore           = false,
        bool $extended_inserts     = true,
        int  $max_query_size       = 50000
    ): self {
        $this->options = (object)[
            'use_universal_prefix' => $use_universal_prefix,
            //
            'add_drop_database'    => $add_drop_database,
            'add_drop_routine'     => $add_drop_routine,
            'add_drop_trigger'     => $add_drop_trigger,
            // tables
            'add_drop_table'       => $add_drop_table,
            'add_if_not_exists'    => $add_if_not_exists,
            'add_auto_increment'   => $add_auto_increment,
            // rows
            'use_ignore'           => $use_ignore,
            'extended_inserts'     => $extended_inserts,
            'max_query_size'       => $max_query_size,
        ];

        if ($this->options->use_universal_prefix) {
            $this->prefixer ??= $this->db->getPrefixer()->setTables($this->getTables());
        } else {
            $this->prefixer = null;
        }

        if (!$this->options->extended_inserts) {
            $this->options->max_query_size = 0;
        }

        return $this;
    }

    /**
     * Get all tables
     * 
     * @return array An associative array of all tables.
     */
    public function getTables(): array
    {
        return $this->schema->tables()->list();
    }

    /**
     * Add a comment header with some reference data to the buffer
     *
     * @param string $Comment Additional comment.
     */
    public function addHeader(string $Comment = '')
    {
        $this->processes['Header'] = [
            'Info'    => $this->schema->getInfo(),
            'Comment' => $Comment
        ];
    }

    /**
     * Add to the buffer the creation and use of the database
     */
    public function addDatabase()
    {
        $this->processes['Database'] = $this->schema->database()->fetch();
    }

    /**
     * Add
     * 
     * @throws Exception
     * 
     * @return bool
     */
    public function addFromList(array|string $list): bool
    {
        if (!is_array($list)) {
            $list = $this->explode($list);
        }

        $routines = [];
        $tables_and_rows = [];
        $tables = [];
        $triggers = [];

        foreach ($list as $query) {
            list($Type, $Name) = $this->parseCmd($query);

            switch ($Type) {
                case 'PROCEDURE':
                case 'FUNCTION':
                    $routines[] = $Name;
                    break;

                case 'ROWS':
                    $tables_and_rows[] = $Name;
                    break;

                case 'TABLE':
                    $tables[] = $Name;
                    break;

                case 'TRIGGER':
                    $triggers[] = $Name;
                    break;

                default:
                    throw new Exception(sprintf('The database exporter cannot process the query «%s».', $query));
            }
        }

        if ($routines) {
            $this->addRoutines(['Name' => $routines]);
        }

        if ($tables_and_rows) {
            $this->addTables(['Name' => $tables_and_rows], true);
        }

        if ($tables) {
            $this->addTables(['Name' => $tables], false);
        }

        if ($triggers) {
            $this->addTriggers(['Name' => $triggers]);
        }

        return true;
    }

    /**
     * Adds routines to the buffer by opening and closing a delimiter
     * 
     * @param array $where
     * 
     * @return array
     */
    public function addRoutines(array $where = []): array
    {
        $routines = $this->schema->routines()->fetchAll($where);

        foreach ($routines as $routine) {
            $this->addRoutine($routine);
        }

        return $routines;
    }

    /**
     * Add a routine to the buffer
     * 
     * @param RoutineInterface $routine
     * 
     * @return void
     */
    public function addRoutine(RoutineInterface $routine): void
    {
        $routine = clone $routine;

        if ($this->prefixer) {
            $routine->setDefinition(
                $this->prefixer->replaceWithUniversal($routine->getDefinition())
            );
        }

        $this->processes['Routines'][] = $routine;
    }

    /**
     * Add
     * 
     * @param array $where
     * 
     * @return array
     */
    public function addTables(array $where = [], bool $add_rows = false): array
    {
        $tables = $this->schema->tables()->fetchAll($where);

        foreach ($tables as $table) {
            $this->completeTable($table);
            $this->addTable($table, $add_rows);
        }

        return $tables;
    }

    /**
     * Adds a table to the buffer
     * 
     * @param TableInterface $table
     * 
     * @return void
     */
    public function addTable(TableInterface $table, bool $add_rows = false): void
    {
        $table = clone $table;
        $current_tbl_name = $table->getName();
        $pretty_tbl_name  = $current_tbl_name;

        if ($this->prefixer) {
            $table->setName(
                $this->prefixer->putUniversalOnTableName($table->getName())
            );
            $pretty_tbl_name = $this->prefixer->removeAllOnTableName($pretty_tbl_name);
        }

        if (!$this->options->add_auto_increment) {
            $table->setAutoIncrement(0);
        }

        $this->processes['Tables'][$pretty_tbl_name] = [
            'Table' => $table,
            'Rows' => $add_rows
                ? $this->getRows($pretty_tbl_name, $current_tbl_name)
                : null
        ];
    }


    /**
     * Add triggers into the buffer
     * 
     * @param array $where
     * 
     * @return array
     */
    public function addTriggers(array $where): array
    {
        $triggers = $this->schema->triggers()->fetchAll($where);

        foreach ($triggers as $trigger) {
            $this->addTrigger($trigger);
        }

        return $triggers;
    }

    /**
     * Adds a trigger into the buffer
     * 
     * @param TriggerInterface $trigger
     * 
     * @return void
     */
    public function addTrigger(TriggerInterface $trigger): void
    {
        $trigger = clone $trigger;

        if ($this->prefixer) {
            $trigger->setTable(
                $this->prefixer->putUniversalOnTableName($trigger->getTable())
            );

            $trigger->setDefinition(
                $this->prefixer->replaceWithUniversal($trigger->getDefinition())
            );
        }

        $this->processes['Triggers'][] = $trigger;
    }

    /**
     * Get
     * 
     * @return ExporterInterface
     */
    public function getAsSQL(): ExporterInterface
    {
        return new SqlExporter($this->processes, $this->options);
    }

    /**
     * Get
     * 
     * @return ExporterInterface
     */
    public function getAsJSON(): ExporterInterface
    {
        return new JSonExporter($this->processes, $this->options);
    }

    /**
     * 
     */
    protected function explode(string $string, string $separator = ','): array
    {
        return array_map('trim', explode($separator, $string));
    }

    /**
     * Parse
     */
    protected function parseCmd(string $query): array
    {
        $query = $this->explode($query, ':');
        $Name  = $query[1] ?? $query[0];
        $Type  = isset($query[1]) ? $query[0] : 'TABLE';

        return [$Type, $Name];
    }

    /**
     * Complete table
     */
    protected function completeTable(TableInterface $table): void
    {
        $tbl_name = $table->getName();

        //
        $table->setColumns(
            $this->schema->columns()->fetchAll($tbl_name)
        );

        //
        $table->setIndexes(
            $this->schema->indexes()->fetchAll($tbl_name)
        );

        //
        $table->setForeignKeys(
            $this->schema->foreignKeys()->fetchAll($tbl_name)
        );
    }

    /**
     * Get
     */
    protected function getRows(string $pretty_tbl_name, string $current_tbl_name): array
    {
        return [
            'Table' => $pretty_tbl_name,
            'Rows' => $this->db->query("SELECT * FROM `$current_tbl_name`")->fetchAll()
        ];
    }
}
