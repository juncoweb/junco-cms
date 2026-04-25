<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Schema;

interface SchemaInterface
{
    /**
     * Get
     * 
     * @return array
     */
    public function getInfo(): array;

    /**
     * Database
     * 
     * @return DatabaseInfoInterface
     */
    public function database(): DatabaseInfoInterface;

    /**
     * Tables
     * 
     * @return TablesInterface
     */
    public function tables(): TablesInterface;

    /**
     * Columns
     * 
     * @return ColumnsInterface
     */
    public function columns(): ColumnsInterface;

    /**
     * ForeignKeys
     * 
     * @return ForeignKeysInterface
     */
    public function foreignKeys(): ForeignKeysInterface;

    /**
     * Indexes
     * 
     * @return IndexesInterface
     */
    public function indexes(): IndexesInterface;

    /**
     * Routines
     * 
     * @return RoutinesInterface
     */
    public function routines(): RoutinesInterface;

    /**
     * Triggers
     * 
     * @return TriggersInterface
     */
    public function triggers(): TriggersInterface;
}
