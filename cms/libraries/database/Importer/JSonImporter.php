<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Importer;

use Junco\Database\Prefixer;
use Junco\Database\Schema\Interface\SchemaInterface;
use Junco\Database\Schema\Interface\Entity\ForeignKeyInterface;
use Junco\Database\Schema\Interface\Entity\IndexInterface;
use Junco\Database\Schema\Interface\Entity\TableInterface;
use Database;

class JSonImporter
{
    // vars
    protected  SchemaInterface $schema;
    protected  Prefixer        $prefixer;
    protected  array           $CurTables;

    // settings
    public     bool $drop_nonexistent_columns = false;

    /**
     * Constructor
     * 
     * @param \Database $db
     */
    public function __construct(?Database $db = null)
    {
        $db ??= db();

        $this->schema    = $db->getSchema();
        $this->prefixer  = $db->getPrefixer();
        $this->CurTables = $this->schema->tables()->list();
    }

    /**
     * Import
     *
     * @param string|array $json
     */
    public function import(string|array $json): void
    {
        if (is_string($json)) {
            $json = (array)json_decode($json, true);
        }

        foreach ($json as $Type => $data) {
            switch ($Type) {
                case 'FUNCTION':
                case 'PROCEDURE':
                    $this->importRoutines($Type, $data);
                    break;

                case 'TABLE':
                    $this->importTables($data, $json['HISTORY'] ?? []);
                    break;

                case 'TRIGGER':
                    $this->importTriggers($data);
                    break;
            }
        }
    }

    /**
     * Import
     *
     * @param string $Type
     * @param array  $Routines
     */
    protected function importRoutines(string $Type, array $Routines): void
    {
        $schemaRoutines = $this->schema->routines();

        foreach ($Routines as $RoutineData) {
            $routine = $schemaRoutines->from($RoutineData);

            $schemaRoutines->create($routine);
        }
    }

    /**
     * Import
     *
     * @param array $Tables
     */
    protected function importTables(array $Tables, array $History): void
    {
        $schemaTables = $this->schema->tables();

        foreach ($Tables as $TableData) {
            $NewTable     = $schemaTables->from($TableData);
            $OriTableName = $NewTable->getName();

            //
            $NewTableName = $this->prefixer->forceLocalOnTableName($NewTable->getName());
            $NewTable->setName($NewTableName);

            $CurTableName = $this->getCurTableName($NewTableName, $History['Tables'][$OriTableName] ?? []);

            if ($CurTableName) {
                if ($CurTableName !== $NewTableName) {
                    $schemaTables->rename($CurTableName, $NewTableName);
                }
                //
                $NewIndexes     = $NewTable->getIndexes();
                $NewForeignKeys = $NewTable->getForeignKeys();

                // reset
                $NewTable->setIndexes();
                $NewTable->setForeignKeys();

                $this->compareColumns($NewTable, $History['Columns'][$OriTableName] ?? []);
                $this->compareIndexes($NewTableName, $NewIndexes);
                $this->compareForeignKeys($NewTableName, $NewForeignKeys);
            } else {
                $schemaTables->create($NewTable);
            }
        }
    }

    /**
     * Import
     *
     * @param array $Triggers
     */
    protected function importTriggers(array $Triggers): void
    {
        $schemaTriggers = $this->schema->triggers();

        foreach ($Triggers as $TriggerData) {
            $trigger = $schemaTriggers->from($TriggerData);

            $schemaTriggers->create($trigger);
        }
    }

    /**
     * Compare
     *
     * @param TableInterface $NewTable
     * @param array          $History
     * 
     * @return void
     */
    protected function compareColumns(TableInterface $NewTable, array $History): void
    {
        $TableName      = $NewTable->getName();
        $NewColumns     = $NewTable->getColumns();
        $doNotDrop      = [];
        //
        $CurColumns     = $this->getCurrentColumns($TableName);
        $CurColumnNames = array_keys($CurColumns);

        //
        foreach ($NewColumns as $NewColumn) {
            $NewColumnName = $NewColumn->getName();

            if (isset($CurColumns[$NewColumnName])) {
                $doNotDrop[] = $NewColumnName;

                $NewColumn->setAction('MODIFY');
            } else {
                $CurColumnName = $this->getCurColumnName($CurColumnNames, $History[$NewColumnName] ?? []);

                if ($CurColumnName) {
                    $doNotDrop[] = $CurColumnName;

                    $NewColumn->setAction('CHANGE', $CurColumnName);
                } else {
                    $NewColumn->setAction('ADD');
                }
            }
        }

        $this->schema->tables()->alter(
            $NewTable->setColumns($NewColumns)
        );

        // drop
        if ($this->drop_nonexistent_columns) {
            $ColumnNames = array_diff($CurColumnNames, $doNotDrop);

            if ($ColumnNames) {
                $this->schema->columns()->drop($TableName, $ColumnNames);
            }
        }
    }

    /**
     * Compare
     *
     * @param string $NewTableName
     * @param array  $NewIndexes
     * 
     * @return void
     */
    protected function compareIndexes(string $NewTableName, array $NewIndexes): void
    {
        $NewIndexNames = $this->getIndexNames($NewIndexes);
        $schemaIndexes = $this->schema->indexes();
        $CurIndexes    = $schemaIndexes->fetchAll($NewTableName);

        // drop
        foreach ($CurIndexes as $CurIndex) {
            $CurIndexName = $CurIndex->getName();

            if ($CurIndexName != 'PRIMARY' && !in_array($CurIndexName, $NewIndexNames)) {
                $schemaIndexes->drop($NewTableName, $CurIndexName);
            }
        }

        // create
        foreach ($NewIndexes as $NewIndex) {
            $schemaIndexes->create($NewIndex);
        }
    }

    /**
     * Get
     *
     * @param IndexInterface[]
     * 
     * @return array
     */
    protected function getIndexNames(array $Indexes): array
    {
        return array_map(
            fn(IndexInterface $index) => $index->getName(),
            $Indexes
        );
    }

    /**
     * Compare
     *
     * @param string $NewTableName
     * @param array  $NewForeignKeys
     * 
     * @return void
     */
    protected function compareForeignKeys(string $NewTableName, array $NewForeignKeys): void
    {
        $schemaForeignKeys  = $this->schema->foreignKeys();
        $CurForeignKeys     = $schemaForeignKeys->fetchAll($NewTableName);
        $NewForeignKeyNames = $this->getForeignKeyNames($NewForeignKeys);
        $CurForeignKeyNames = $this->getForeignKeyNames($CurForeignKeys);

        // drop
        if ($CurForeignKeyNames) {
            $OldForeignKeyNames = array_diff($CurForeignKeyNames, $NewForeignKeyNames);

            if ($OldForeignKeyNames) {
                $schemaForeignKeys->drop($NewTableName, $OldForeignKeyNames);
            }
        }

        // create
        $CurForeignKeys = array_combine($CurForeignKeyNames, $CurForeignKeys);

        foreach ($NewForeignKeys as $NewForeignKey) {
            $NewForeignKeyName = $NewForeignKey->getName();
            $CurForeignKey     = $CurForeignKeys[$NewForeignKeyName] ?? null;

            if ($CurForeignKey) {
                if (!$CurForeignKey->isEqual($NewForeignKey)) {
                    $schemaForeignKeys->drop($NewTableName, $NewForeignKeyName);
                    $schemaForeignKeys->create($NewForeignKey);
                }
            } else {
                $schemaForeignKeys->create($NewForeignKey);
            }
        }
    }

    /**
     * Get
     *
     * @param ForeignKeyInterface[]
     * 
     * @return array
     */
    protected function getForeignKeyNames(array $ForeignKeys): array
    {
        return array_map(
            fn(ForeignKeyInterface $fk) => $fk->getName(),
            $ForeignKeys
        );
    }

    /**
     * Get
     *
     * @param string $NewTableName
     * @param array  $History
     * 
     * @return ?string
     */
    protected function getCurTableName(string $NewTableName, array $History): ?string
    {
        if (in_array($NewTableName, $this->CurTables)) {
            return $NewTableName;
        }

        foreach ($History as $TableName) {
            $TableName = $this->prefixer->forceLocalOnTableName($TableName);

            if (in_array($TableName, $this->CurTables)) {
                return $TableName;
            }
        }

        return null;
    }

    /**
     * Get
     *
     * @param string $TableName
     * 
     * @return array
     */
    protected function getCurrentColumns(string $TableName): array
    {
        $columns = $this->schema->columns()->fetchAll($TableName);
        $rows = [];

        foreach ($columns as $column) {
            $rows[$column->getName()] = $column;
        }

        return $rows;
    }

    /**
     * Get
     *
     * @param array $CurColumnNames
     * @param array $History
     * 
     * @return ?string
     */
    protected function getCurColumnName(array $CurColumnNames, array $history): ?string
    {
        if ($history) {
            foreach ($CurColumnNames as $CurColumnName) {
                if (in_array($CurColumnName, $history)) {
                    return $CurColumnName;
                }
            }
        }

        return null;
    }
}
