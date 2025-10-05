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
     * @param $status
     * 
     * @return ZoomGroup
     */
    public function status($status): ZoomGroup;

    /**
     * Date
     * 
     * @param Date $date
     * 
     * @return ZoomGroup
     */
    public function date(Date $date): ZoomGroup;

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
