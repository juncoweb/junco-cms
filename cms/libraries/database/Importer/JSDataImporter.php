<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Importer;

use Junco\Database\Schema\Interface\SchemaInterface;
use Database;

class JSDataImporter
{
    // vars
    protected  SchemaInterface    $schema;
    protected  array            $CurTables;
    protected  string            $prefix;

    // settings
    public     bool $drop_nonexistent_columns = false;

    /**
     * Constructor
     * 
     * @param \Database $db
     */
    public function __construct(?Database $db = null)
    {
        if ($db === null) {
            $db = db();
        }

        $this->schema    = $db->getSchema();
        $this->prefix    = $db->getPrefix();
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
                case 'TRIGGER':
                    $this->importRoutine($Type, $data);
                    break;

                case 'TABLE':
                    $this->importTable($data);
                    break;
            }
        }
    }

    /**
     * Import Routines
     *
     * @param string $Type
     * @param array  $Routines
     */
    protected function importRoutine(string $Type, array $Routines): void
    {
        foreach ($Routines as $RoutineName => $Routine) {
            $this->schema->routines()->create($Type, $RoutineName, $Routine);
        }
    }

    /**
     * Import Table
     *
     * @param array  $Tables
     */
    protected function importTable(array $Tables): void
    {
        foreach ($Tables as $TableName => $Table) {
            $TableName        = $this->prefix . $TableName;
            $CurTableName    = $this->getCurTableName($TableName, $Table);

            if ($CurTableName) {
                if ($CurTableName !== $TableName) {
                    $this->schema->tables()->rename($CurTableName, $TableName);
                }
                $CurTable  = $this->schema->tables()->showData($TableName);

                $this->compareFields($TableName, $Table['Fields'], $CurTable);
                $this->compareIndexes($TableName, $Table['Indexes'], $CurTable['Indexes']);
            } else {
                $this->schema->tables()->create($TableName, $Table);
            }
        }
    }

    /**
     * Compare
     *
     * @param string $TableName
     * @param array  $Fields
     * @param array  $CurTable
     * 
     * @return void
     */
    protected function compareFields(string $TableName, array $Fields, array $CurTable): void
    {
        $doNotDrop = [];

        // add/change
        $AlterFields = [];
        foreach ($Fields as $FieldName => $Field) {
            $doNotDrop[]    = $FieldName;
            $Def            = $Field['Describe'];
            $CurDef           = $CurTable['Fields'][$FieldName] ?? false;

            if (
                !$CurDef
                || $CurDef['Type'] != $Def['Type']
                || $CurDef['Null'] != $Def['Null']
                || $CurDef['Default'] !== $Def['Default']
                || $CurDef['Extra'] != $Def['Extra']
            ) {
                if ($CurDef) {
                    $Def['ChangeField'] = $FieldName;
                } else {
                    $Def['ChangeField'] = $this->getCurFielName($Field, $CurTable);

                    if ($Def['ChangeField']) {
                        $doNotDrop[] = $Def['ChangeField'];
                    }
                }
                $AlterFields[$FieldName] = $Def;
            }
        }
        if ($AlterFields) {
            $this->schema->fields()->alter($TableName, $AlterFields);
        }

        // drop
        if ($this->drop_nonexistent_columns) {
            $curFields = array_keys($CurTable['Fields']);
            $drop = array_diff($curFields, $doNotDrop);

            foreach ($drop as $CurFieldName) {
                $this->schema->fields()->drop($TableName, $CurFieldName);
            }
        }
    }

    /**
     * Compare
     *
     * @param string $TableName
     * @param array  $Indexes
     * @param array  $CurIndexes
     * 
     * @return void
     */
    protected function compareIndexes(string $TableName, array $Indexes, array $CurIndexes): void
    {
        // drop
        foreach ($CurIndexes as $Key_name => $CurIndex) {
            if (!isset($Indexes[$Key_name]) && $Key_name != 'PRIMARY') {
                $this->schema->indexes()->drop($TableName, $Key_name, $CurIndex);
            }
        }

        // add
        foreach ($Indexes as $Key_name => $Index) {
            $Index['Type'] ??= 'INDEX'; // hack

            if (
                !isset($CurIndexes[$Key_name])
                || ($Key_name !== 'PRIMARY' && $Index['Type'] !== ($CurIndexes[$Key_name]['Type'] ?? 'INDEX'))
                || array_diff($Index['Columns'], $CurIndexes[$Key_name]['Columns'])
            ) {
                $this->schema->indexes()->create($TableName, $Key_name, $Index);
            }
        }
    }

    /**
     * Get
     *
     * @param string $TableName
     * @param array  $Table
     * 
     * @return string
     */
    protected function getCurTableName(string $TableName, array $Table): string
    {
        if (in_array($TableName, $this->CurTables)) {
            return $TableName;
        }
        if (!empty($Table['History'])) {
            foreach ($Table['History'] as $TableName) {
                if (in_array($this->prefix . $TableName, $this->CurTables)) {
                    return $this->prefix . $TableName;
                }
            }
        }

        return '';
    }

    /**
     * Get
     *
     * @param string $TableName
     * @param array  $Table
     * 
     * @return string
     */
    protected function getCurFielName(array $Field, array $CurTable): string
    {
        if (!empty($Field['History'])) {
            foreach ($Field['History'] as $HistoryFieldName) {
                if (isset($CurTable['Fields'][$HistoryFieldName])) {
                    return $HistoryFieldName;
                }
            }
        }

        return '';
    }
}
