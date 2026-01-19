<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

interface MenusInterface
{
    /**
     * Constructor
     * 
     * @param string $key
     */
    public function __construct(string $key = '');

    /**
     * Set to show only rows with edges.
     * 
     * @param bool $value
     * 
     * @return self
     */
    public function setWithEdges(bool $value = true): self;

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string;
}
