<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Exporter;

use Junco\Database\Schema\Interface\Entity\DatabaseEntityInterface;
use Junco\Database\Schema\Interface\Entity\RoutineInterface;
use Junco\Database\Schema\Interface\Entity\TableInterface;
use Junco\Database\Schema\Interface\Entity\TriggerInterface;

class SqlExporter implements ExporterInterface
{
    // vars
    protected object $options;
    protected string $buffer            = '';
    protected string $EOL               = PHP_EOL; //
    protected string $delimiter_keyword = '$$';    // delimiter

    /**
     * Constructor
     * 
     * @param array $processes
     */
    public function __construct(array $processes, object $options)
    {
        $this->options = $options;

        if ($processes['Header']) {
            $this->addHeader($processes['Header']);
        }

        if ($processes['Database']) {
            $this->addDatabase($processes['Database']);
        }

        if ($processes['Routines']) {
            $this->addRoutines($processes['Routines']);
        }

        if ($processes['Tables']) {
            $this->addTables($processes['Tables']);
        }

        if ($processes['Triggers']) {
            $this->addTriggers($processes['Triggers']);
        }
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        $this->buffer = '';
    }

    /**
     * Used to record history of changes.
     * 
     * @param string|array $history
     * 
     * @return self
     */
    public function addHistory(string|array $history): self
    {
        // Unsupported
        return $this;
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
     * Add a comment header with some reference data to the buffer
     *
     * @param array $data
     * 
     * @return void
     */
    protected function addHeader(array $data = []): void
    {
        $this->buffer .= '-- Database dump' . $this->EOL;

        //
        foreach ($data['Info'] as $name => $value) {
            $this->buffer .= '-- ' . $name . ': ' . $value . $this->EOL;
        }

        $this->buffer .= '-- PHP version: ' . phpversion() . $this->EOL;
        $this->buffer .= '-- date: ' . date('Y-m-d H:i:s', time()) . $this->EOL;

        if ($data['Comment']) {
            $this->buffer .= '-- ' . str_replace('\n', $this->EOL . '-- ', $data['Comment']);
        }

        $this->buffer .= $this->EOL . $this->EOL;
    }

    /**
     * Add
     *
     * @param DatabaseEntityInterface $database
     * 
     * @return void
     */
    protected function addDatabase(DatabaseEntityInterface $database): void
    {
        $this->addTitle($database->getName());

        if (!$this->options->add_drop_database) {
            $this->buffer .= '-- ';
        }

        $this->buffer .= "DROP DATABASE `" . $database->getName() . "`;" . $this->EOL;
        $this->buffer .= $database->getCreateStatement() . ';' . $this->EOL;
        $this->buffer .= "USE `" . $database->getName() . "`;" . $this->EOL . $this->EOL;
    }

    /**
     * Add a routines to the buffer
     * 
     * @param RoutineInterface[] $routines
     * 
     * @return void
     */
    protected function addRoutines(array $routines): void
    {
        $this->addTitle('Routines');
        $this->openDelimiter();

        foreach ($routines as $routine) {
            $this->addRoutine($routine);
        }

        $this->closeDelimiter();
        $this->addSeparator();
    }

    /**
     * Add
     * 
     * @param RoutineInterface $routine
     * 
     * @return void
     */
    protected function addRoutine(RoutineInterface $routine): void
    {
        $this->addSeparator($routine->getName());

        if ($this->options->add_drop_routine) {
            $this->buffer .= sprintf("DROP %s IF EXISTS `%s`", $routine->getType(), $routine->getName());
            $this->buffer .= $this->delimiter_keyword . $this->EOL;
        }

        $this->buffer .= $routine->getCreateStatement();
        $this->buffer .= $this->delimiter_keyword . $this->EOL . $this->EOL;
    }

    /**
     * Add the tables to the buffer
     * 
     * @param TableInterface[] $tables
     * 
     * @return void
     */
    protected function addTables(array $tables): void
    {
        ksort($tables);

        foreach ($tables as $row) {
            $this->addTable($row['Table']);

            if ($row['Rows']) {
                $this->addRows($row['Rows']);
            }
        }
    }

    /**
     * Add
     * 
     * @param TableInterface $tables
     * 
     * @return void
     */
    protected function addTable(TableInterface $table): void
    {
        $query = $table->getCreateStatement();

        if (!$this->options->add_if_not_exists) {
            $query = str_replace(' IF NOT EXISTS', '', $query);
        }

        $this->addTitle($table->getName());

        if (!$this->options->add_drop_table) {
            $this->buffer .= '-- ';
        }
        $this->buffer .= "DROP TABLE IF EXISTS `" . $table->getName() . "`;" . $this->EOL;
        $this->buffer .= $query . ';' . $this->EOL . $this->EOL;
    }

    /**
     * Add registers into the buffer
     * 
     * @param array $data
     * @param bool  $use_ignore
     * @param int   $max_query_size
     */
    protected function addRows(array $data): void
    {
        $this->addSeparator("insert into `{$data['Table']}`");

        if (!$data['Rows']) {
            return;
        }

        // vars
        $IGNORE = $this->options->use_ignore ? " IGNORE" : "";
        $COLUMS = "`" . implode('`, `', array_keys($data['Rows'][0])) . "`";
        $INSERT = sprintf("INSERT%s INTO `%s` (%s) VALUES", $IGNORE, $data['Table'], $COLUMS);

        $values = [];
        $sum_size = 0;

        foreach ($data['Rows'] as $row) {
            $line = $this->getInsertValues($row);

            // extended
            if ($this->options->max_query_size) {
                $strlen     = strlen($line);
                $query_size = $sum_size + $strlen;

                if (($query_size > $this->options->max_query_size) && $values) {
                    $this->buffer .= $INSERT . $this->EOL . implode(',' . $this->EOL, $values) . ';' . $this->EOL;
                    $values = [];
                    $sum_size = $strlen;
                } else {
                    $sum_size += $strlen;
                }
            }

            $values[] = $line;
        }

        if ($values) {
            if ($this->options->max_query_size && $values) {
                $this->buffer .= $INSERT . $this->EOL . implode(',' . $this->EOL, $values) . ';' . $this->EOL;
            } else {
                foreach ($values as $value) {
                    $this->buffer .= $INSERT . ' ' . $value . ';' . $this->EOL;
                }
            }

            $this->buffer .= $this->EOL . $this->EOL;
        }
    }

    /**
     * Add triggers
     * 
     * @param array $triggers
     * 
     * @return void
     */
    protected function addTriggers($triggers): void
    {
        $comment = $this->options->add_drop_trigger
            ? ''
            : '-- ';

        $this->addTitle('Triggers');
        $this->openDelimiter();

        foreach ($triggers as $trigger) {
            $this->addTrigger($trigger, $comment);
        }

        $this->closeDelimiter();
    }

    /**
     * Add trigger
     * 
     * @param TriggerInterface $trigger
     * 
     * @return void
     */
    protected function addTrigger(TriggerInterface $trigger, string $comment): void
    {
        $this->addSeparator(sprintf('trigger `%s`', $trigger->getName()));

        $this->buffer .= $comment . sprintf("DROP TRIGGER IF EXISTS `%s`", $trigger->getName()) . $this->delimiter_keyword . $this->EOL;
        $this->buffer .= $trigger->getCreateStatement() . $this->delimiter_keyword . $this->EOL . $this->EOL;
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
     * Scape
     */
    protected function getInsertValues(array $values): string
    {
        foreach ($values as $i => $value) {
            if (is_null($value)) {
                $values[$i] = 'NULL';
            } elseif (!is_numeric($value)) {
                $values[$i] = $this->escapeString($value);
            }
        }

        return '(' . implode(', ', $values) . ')';
    }

    /**
     * Scape
     */
    protected function escapeString(string $string): string
    {
        return "'" . str_replace(
            ['\'', "\x00", "\x0a", "\x0d", "\x1a"],
            ['\'\'', '\0', '\n', '\r', '\Z'],
            $string
        ) . "'";
    }
}
