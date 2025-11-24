<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

interface ZoomInterface
{
    /**
     * Group
     * 
     * @param string $content
     * 
     * @return ZoomGroup
     */
    public function group(string $content = ''): ZoomGroup;

    /**
     * Status
     * 
     * @param string $status
     * @param string $color
     * 
     * @return ZoomGroup
     */
    public function status(mixed $status, string $color = ''): ZoomGroup;

    /**
     * Date
     * 
     * @param Date $date
     * 
     * @return ZoomGroup
     */
    public function date(Date|string|null $date): ZoomGroup;

    /**
     * Group
     * 
     * @param array ...$group
     * 
     * @return void
     */
    public function columns(ZoomGroup ...$group): void;

    /**
     * Render
     * 
     * @return string
     */
    public function render(): string;
}
