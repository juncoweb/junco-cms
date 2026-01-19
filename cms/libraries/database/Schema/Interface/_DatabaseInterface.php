<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Interface;

use Database;

interface _DatabaseInterface
{
    /**
     * Constructor
     */
    public function __construct(Database $db);

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

    /**
     * Show
     *
     * @return array
     */
    public function showData(): array;
}
