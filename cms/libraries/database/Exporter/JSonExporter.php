<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Exporter;

use Junco\Database\Schema\Interface\Entity\RoutineInterface;
use Junco\Database\Schema\Interface\Entity\TableInterface;
use Junco\Database\Schema\Interface\Entity\TriggerInterface;

class JSonExporter implements ExporterInterface
{
    // vars
    protected $json = [];

    /**
     * Constructor
     * 
     * @param array $processes
     */
    public function __construct(array $processes, object $options)
    {
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
        $this->json = [];
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
     * 
     * @return self
     */
    public function addHistory(string|array $history): self
    {
        if (!is_array($history)) {
            $history = (array)json_decode($history, true);
        }

        $this->json['HISTORY'] = $history;

        return $this;
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
     * Add a routines to the buffer
     * 
     * @param array $routines
     * @param bool  $add_drop_routine
     * 
     * @return void
     */
    public function addRoutines(array $routines): void
    {
        array_map(function (RoutineInterface $routine) {
            $this->json[$routine->getType()][$routine->getName()] = $routine->toArray();
        }, $routines);
    }

    /**
     * Add the tables to the buffer
     * 
     * @param TableInterface[] $tables
     * 
     * @return void
     */
    public function addTables(array $tables): void
    {
        $tables = array_column($tables, 'Table');

        array_map(function (TableInterface $table) {
            $this->json['TABLE'][$table->getName()] = $table->toArray();
        }, $tables);
    }

    /**
     * Add triggers into the buffer
     * 
     * @param array $triggers
     * @param bool  $add_drop_trigger
     */
    public function addTriggers($triggers): void
    {
        array_map(function (TriggerInterface $trigger) {
            $this->json['TRIGGER'][$trigger->getName()] = $trigger->toArray();
        }, $triggers);
    }
}
