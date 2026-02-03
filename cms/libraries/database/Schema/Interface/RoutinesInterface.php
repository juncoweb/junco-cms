<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Junco\Database\Schema\Interface\Entity\RoutineInterface;
use Database;

interface RoutinesInterface
{
    /**
     * Constructor
     */
    public function __construct(Database $db);

    /**
     * Has
     * 
     * @param string $Type
     * @param string $Name
     * 
     * @return bool
     */
    public function has(string $Type, string $Name): bool;

    /**
     * Fetch
     * 
     * @param array $where
     * @param bool  $optimize   It does not add the parameters to improve performance.
     * 
     * @return RoutineInterface[]
     */
    public function fetchAll(array $where = [], bool $optimize = false): array;

    /**
     * Fetch
     * 
     * @param string $Name
     * 
     * @return ?RoutineInterface
     */
    public function fetch(string $Name): ?RoutineInterface;

    /**
     * Create
     * 
     * @param RoutineInterface $Routine
     * 
     * @return int
     */
    public function create(RoutineInterface $Routine): int;

    /**
     * Drop
     * 
     * @param string $Type
     * @param string $Name
     * 
     * @return int
     */
    public function drop(string $Type, string $Name): int;

    /**
     * New
     * 
     * @param string $Type
     * @param string $Name
     * 
     * @return RoutineInterface
     */
    public function newRoutine(string $Type, string $Name): RoutineInterface;

    /**
     * From
     * 
     * @param array $Data
     * 
     * @return ?RoutineInterface
     */
    public function from(array $Data): ?RoutineInterface;
}
