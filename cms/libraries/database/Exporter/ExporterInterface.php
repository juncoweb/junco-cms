<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Exporter;

/**
 * Database Exporter Adapter Interface
 */
interface ExporterInterface
{
    /**
     * Constructor
     * 
     * @param array $processes
     */
    public function __construct(array $processes, object $options);

    /**
     * Used to record history of changes.
     * 
     * @param string|array $history
     * 
     * @return self
     */
    public function addHistory(string|array $history): self;

    /**
     * Render
     * 
     * @return string|array
     */
    public function render(): string|array;

    /**
     * Write
     * 
     * @param string $file
     */
    public function write(string $file): void;
}
