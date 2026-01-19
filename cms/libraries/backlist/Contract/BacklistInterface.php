<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Contract;

use Junco\Backlist\Contract\ButtonInterface;
use Junco\Backlist\Contract\ColumnInterface;
use Junco\Backlist\Contract\ControlInterface;
use Junco\Backlist\Contract\LinkInterface;
use Junco\Backlist\Contract\FiltersInterface;

interface BacklistInterface
{
    /**
     * Filters
     * 
     * @param string $snippet
     * 
     * @return FiltersInterface
     */
    public function getFilters(string $snippet = ''): FiltersInterface;

    /**
     * Set
     * 
     * @param array $rows
     * 
     * @return void
     */
    public function setRows(array $rows): void;

    /**
     * Labels
     * 
     * @param string $name
     * 
     * @return void
     */
    public function setLabels(string $name): void;

    /**
     * Fix
     *
     * @param string       $name
     * @param string       $name
     * @param array|string $formats
     * 
     * @return void
     */
    public function fixDate(string $name, string $date_format, array|string $formats = ''): void;

    /**
     * Fix
     *
     * @param string $name
     * @param string $separator
     * 
     * @return void
     */
    public function fixList(string $name, string $separator = ', '): void;

    /**
     * Fix
     *
     * @param string $name
     */
    public function fixEnum(string $name = 'status', ?array $options = null): void;

    /**
     * Fix
     *
     * @param string  $name
     * @param ?string $replace
     * 
     * @return void
     */
    public function fixRepeats(string $name, ?string $replace = null): void;

    /**
     * Apply
     *
     * @param string 
     * 
     * @return void
     */
    public function apply(callable $fn): void;

    /**
     * Column
     * 
     * @param string $column
     * 
     * @return ColumnInterface
     */
    public function column(string $column = ''): ColumnInterface;

    /**
     * Link
     *
     * @param string $url
     * 
     * @return LinkInterface
     */
    public function link(string $url = ''): LinkInterface;

    /**
     * Control
     *
     * @param string $control
     * 
     * @return ControlInterface
     */
    public function control(string $control): ControlInterface;

    /**
     * Button
     *
     * @param string $control
     * 
     * @return ButtonInterface
     */
    public function button(string $control = ''): ButtonInterface;

    /**
     * Search
     *
     * @param string $column
     * @param string $value
     * @param string $field
     * 
     * @return SearchInterface
     */
    public function search(string $column, string $value, string $field = ''): SearchInterface;

    /**
     * Check
     * 
     * @param string $value
     * @param string $index
     * 
     * @return void
     */
    public function check(string $value = ':id', string $index = ''): void;

    /**
     * Up
     * 
     * @param string $control
     * @param string $name
     * 
     * @return void
     */
    public function up(string $control = '', string $name = ''): void;

    /**
     * Down
     * 
     * @param string $control
     * @param string $name
     * 
     * @return void
     */
    public function down(string $control = '', string $name = ''): void;

    /**
     * Status
     * 
     * @param string $control
     * @param string $name
     * 
     * @return void
     */
    public function status(string $control = '', string $name = 'status'): void;

    /**
     * Hidden
     * 
     * @param string $name
     * @param string $value
     * 
     * @return void
     */
    public function hidden(string $name, string $value = ''): void;

    /**
     * Render
     * 
     * @param string $pagi
     * @param string $empty_list
     * 
     * @return string
     */
    public function render(string $pagi = '', string $empty_list = ''): string;
}
