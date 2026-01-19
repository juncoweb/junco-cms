<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Email;

interface EmailMessageInterface
{
    /**
     * Title
     * 
     * @param string $title
     */
    public function title(string $title): void;

    /**
     * Legend
     * 
     * @param string $legend
     */
    public function legend(string $legend): void;

    /**
     * Line
     * 
     * @param string $text
     */
    public function line(string $text, ...$args): void;

    /**
     * Codelink
     * 
     * @param string $url
     */
    public function codelink(string $url): void;

    /**
     * Legal
     * 
     * @param string $text
     */
    public function legal(string $text = ''): void;

    /**
     * Body
     * 
     * @param string $html
     * @param string $plain
     */
    public function body(string $html, string $plain = ''): void;

    /**
     * Get plain message
     */
    public function getPlain(): string;

    /**
     * Get html message
     */
    public function getHtml(): string;
}
