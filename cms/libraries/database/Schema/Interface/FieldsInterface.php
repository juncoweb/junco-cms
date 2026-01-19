<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Database;

interface FieldsInterface
{
    /**
     * Constructor
     */
    public function __construct(Database $db);

    /**
     * Show Fields
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return array
     */
    public function fetchAll(string $TableName, array $where = []): array;

    /**
     * Get Fields
     * 
     * @param string $TableName
     * @param array  $where
     * 
     * @return array
     */
    public function list(string $TableName, array $where = []): array;

    /**
     * Alter
     * 
     * @param string  $TableName
     * @param array	  $Fields
     * 
     * @return int
     */
    public function alter(string $TableName, array $Fields): int;

    /**
     * Drop
     * 
     * @param string		$TableName
     * @param string|array	$FieldNames
     * 
     * @return int
     */
    public function drop(string $TableName, string|array $FieldNames): int;
}
