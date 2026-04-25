<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Schema;

use Junco\Database\Base\Entity\DatabaseInterface;
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
     * @return ?DatabaseInterface
     */
    public function fetch(): ?DatabaseInterface;

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
