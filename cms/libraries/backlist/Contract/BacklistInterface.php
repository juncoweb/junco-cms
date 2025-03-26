<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Backlist\Contract;

use Pagination;

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
     * Table header
     * 
     * @param string|array|null $content
     * @param ?array            $options
     */
    public function th(string|array|null $content = '', ?array $options = null);

    /**
     * Cell
     * 
     * @param string $html
     */
    public function td(?string $html = '');

    /**
     * Body
     * 
     * @param string $html_1
     */
    public function body(string $html_1 = '', $html_2 = true, $length = 140);

    /**
     * Is repeated
     */
    public function isRepeated($value, bool $return_bool = false);

    /**
     * Labels
     * 
     * @param array $labels
     */
    public function setLabels(array $labels);

    /**
     * Label
     * 
     * @param string ...$labels
     */
    public function setLabel(string ...$labels);

    /**
     * Set index
     * 
     * @param string $value
     */
    public function setIndex(string $value);

    /**
     * Check header
     */
    public function check_h();

    /**
     * Check
     * 
     * @param string $id
     * @param bool   $is_enabled
     */
    public function check(string $id = '', bool $is_enabled = true);

    /**
     * Up down header
     */
    public function up_down_h();

    /**
     * Up down
     */
    public function up_down($up, $down);

    /**
     * Link header
     *
     * @param string|array $content
     * @param array        $data
     */
    public function link_h(string|array $content = '', array $data = []);

    /**
     * Link
     *
     * @param string|array $data
     * @param bool         $is_enabled
     */
    public function link(string|array|null $data = null, $is_enabled = true);

    /**
     * Search header
     *
     * @param string $content
     * @param array  $data
     */
    public function search_h(string $content = '', array $data = []);

    /**
     * Search
     *
     * @param string|array|null $data
     * @param ?string           $caption
     * @param bool              $is_enabled
     */
    public function search(string|array|null $data = null, ?string $caption = null, bool $is_enabled = true);

    /**
     * Button h
     *
     * @param string|array $control
     * @param string       $title
     * @param string       $icon
     */
    public function button_h(string|array $control = '', string $title = '', string $icon = '');

    /**
     * Button
     *
     * @param array $data
     */
    public function button(array $data = [], bool $is_enabled = true);

    /**
     * Status header
     *
     * @param string $control
     */
    public function status_h(string|false $control = '');

    /**
     * Status
     *
     * @param string $index
     */
    public function status(string $index);

    /**
     * Hidden
     * 
     * @param string $name
     * @param string $value
     */
    public function hidden(string $name, string $value = '');

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
