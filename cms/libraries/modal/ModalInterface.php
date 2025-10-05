<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Modal;

use Junco\Responder\Contract\ResponderInterface;

interface ModalInterface extends ResponderInterface
{
    /**
     * Type
     * 
     * @param string $type
     * 
     * @return void
     */
    public function type(string $type): void;

    /**
     * Size
     * 
     * @param string $size
     * 
     * @return void
     */
    public function size(string $size): void;

    /**
     * Button
     * 
     * @param string $control
     * @param string $title
     * @param string $caption
     * 
     * @return void
     */
    public function button(string $control = '', string $title = '', string $caption = ''): void;

    /**
     * Enter
     * 
     * @param string $title
     * @param string $caption
     * 
     * @return void
     */
    public function enter(string $title = '', string $caption = ''): void;

    /**
     * Close
     * 
     * @param string $title
     * @param string $caption
     * 
     * @return void
     */
    public function close(string $title = '', string $caption = ''): void;

    /**
     * Form
     * 
     * @param string $id
     * 
     * @return modal_form
     */
    public function form(string $id = ''): \modal_form;

    /**
     * Set the pathway of the page
     *
     * @param string|array $value
     * 
     * @return void
     */
    public function pathway(string|array $value): void;

    /**
     * Set the title
     *
     * @param string|array $title
     * @param string       $icon
     * 
     * @return void
     */
    public function title($title, string $icon = ''): void;

    /**
     * Help link
     *
     * @param string $url
     * 
     * @return void
     */
    public function helpLink(string $url): void;

    /**
     * Footer
     *
     * @param string $url
     * 
     * @return void
     */
    public function footer(string $html = ''): void;
}
