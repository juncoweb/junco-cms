<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Responder\ResponderBase;
use Junco\Responder\Contract\ResponderInterface;
use Psr\Http\Message\ResponseInterface;

interface TemplateInterface extends ResponderInterface
{
    /**
     * Seo
     * 
     * @param string $description
     * @param string $keywords
     */
    public function seo(string $description = '', string $keywords = '');

    /**
     * Create a meta tag
     *
     * @param array $attr An array with the attributes of the tag
     */
    public function meta(array $attr);

    /**
     * Load a text editor
     *
     * @param string $plugin The plugin with a particular editor
     */
    public function editor(string $plugin = ''): void;

    /**
     * Load style sheets
     *
     * @param string|array $css  A list of style sheets to load
     */
    public function css(string|array $css = ''): void;

    /**
     * Load javascripts resources
     *
     * @param string|array $js         A list of scripts to load
     * @param bool         $in_head
     */
    public function js(string|array $js = '', bool $in_head = false): void;

    /**
     * Load functions that will be executed when loading the page
     *
     * @param string $script    A javascript function
     */
    public function domready(string $script = ''): void;

    /**
     * Load a set of values that will be passed to the template.
     *
     * @param array|string|null $options A list of keys / values.
     */
    public function options(array|string|null $options = null): void;

    /**
     * Returns the value of a variable.
     *
     * @param string $name
     */
    public function getOption(string $name): mixed;

    /**
     * Set the pathway of the page
     *
     * @param string|array $value
     */
    public function pathway(string|array $value): void;

    /**
     * Set the title of the page
     *
     * @param array|string $title
     * @param array|string $options
     */
    public function title(array|string $title, array|string $options = []): void;

    /**
     * Hash
     *
     * @param string $value
     */
    public function hash(string $value): void;

    /**
     * Help link
     *
     * @param string $url
     */
    public function helpLink(string $url): void;
}
