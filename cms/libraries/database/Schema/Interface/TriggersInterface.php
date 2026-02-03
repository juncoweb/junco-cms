<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Junco\Database\Schema\Interface\Entity\TriggerInterface;
use Database;

interface TriggersInterface
{
    /**
     * Constructor
     */
    public function __construct(Database $db);

    /**
     * Has
     * 
     * @param string $Name
     * 
     * @return bool
     */
    public function has(string $Name): bool;

    /**
     * Fetch all
     * 
     * @param array $where
     * 
     * @return array
     */
    public function fetchAll(array $where = []): array;

    /**
     * Fetch
     * 
     * @param string $Name
     * 
     * @return ?TriggerInterface
     */
    public function fetch(string $Name): ?TriggerInterface;

    /**
     * Create
     * 
     * @param TriggerInterface $trigger
     * 
     * @return int
     */
    public function create(TriggerInterface $trigger): int;

    /**
     * Drop
     * 
     * @param string|array $Name
     * 
     * @return int
     */
    public function drop(string|array $Name): int;

    /**
     * New
     * 
     * @param string $Name
     * 
     * @return TriggerInterface
     */
    public function newTrigger(string $Name): TriggerInterface;

    /**
     * From
     * 
     * @param array $Data
     * 
     * @return ?TriggerInterface
     */
    public function from(array $Data): ?TriggerInterface;
}
