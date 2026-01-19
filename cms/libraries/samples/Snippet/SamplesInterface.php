<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Samples\Snippet;

interface SamplesInterface
{
    /**
     * Html
     * 
     * @param string $code
     * 
     * @return HtmlSample
     */
    public function html(string $code = ''): HtmlSample;

    /**
     * Js
     * 
     * @param string $code
     * 
     * @return JsSample
     */
    public function js(string $code = ''): JsSample;

    /**
     * Colors
     * 
     * @param string $code
     * 
     * @return ColorsSample
     */
    public function colors(string $code = ''): ColorsSample;

    /**
     * Sizes
     * 
     * @param string $code
     * 
     * @return SizesSample
     */
    public function sizes(string $code = ''): SizesSample;

    /**
     * Separate
     * 
     * @param string $title
     * 
     * @return void
     */
    public function separate(string $title = ''): void;

    /**
     * Render
     * 
     * @return bool $full
     * 
     * @return string
     */
    public function render(bool $full = false): string;
}
