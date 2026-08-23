<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
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
     * @param ?string $date
     * @param bool    $toLocal
     * 
     * @return ZoomGroup
     */
    public function date(?string $date, bool $toLocal = true): ZoomGroup;

    /**
     * Image
     * 
     * @param ?string $src
     * @param string  $alt
     * 
     * @return ZoomGroup
     */
    public function image(?string $src, string $alt = ''): ZoomGroup;

    /**
     * Currency
     * 
     * @param float $value
     * 
     * @return ZoomGroup
     */
    public function currency(float $value): ZoomGroup;

    /**
     * Group
     * 
     * @param ZoomGroup ...$group
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
