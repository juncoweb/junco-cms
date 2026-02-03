<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Junco\Database\Schema\Interface\Entity\DatabaseEntityInterface;
use Database;

interface DatabaseInfoInterface
{
    /**
     * Constructor
     */
    public function __construct(Database $db);

    /**
     * Fetch
     *
     * @return ?DatabaseEntityInterface
     */
    public function fetch(): ?DatabaseEntityInterface;

    /**
     * Get
     * 
     * @return array
     */
    public function getEngines(): array;

    /**
     * Get
     * 
     * @return array
     */
    public function getCollations(): array;
}
