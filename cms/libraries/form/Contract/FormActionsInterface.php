<?php

/*
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Form\Contract;

interface FormActionsInterface
{
    /**
     * Separate
     */
    public function separate(): void;

    /**
     * Button
     *
     * @param string       $control
     * @param string       $title
     * @param array|string $attr
     */
    public function button(string $control = '', string $title = '', array|string $attr = []);

    /**
     * Dropdown
     * 
     * @param string|array	$menu  		The array format is [label(, href, contol, value)]
     * @param string 		$title
     * @param array			$attr
     */
    public function dropdown(string|array|null $html = null, string $title = '', array|string $attr = []);

    /**
     * Nav
     */
    public function nav(): void;

    /**
     * Refresh
     */
    public function refresh(): void;

    /**
     * Refresh
     * 
     * @param string 		$title
     * @param string|bool	$caption
     */
    public function enter(string $title = '', string $caption = '');

    /**
     * Cancel
     * 
     * @param string $control
     */
    public function cancel(string $control = '');

    /**
     * Help
     */
    public function help();

    /**
     * Render
     */
    public function render(): string;
}
