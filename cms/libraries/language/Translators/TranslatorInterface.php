<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Language\Translators;

interface TranslatorInterface
{
    /**
     * Lookup a message in the current domain
     * 
     * @param string $message
     * 
     * @return string
     */
    public function gettext(string $message): string;

    /**
     * Plural version of gettext
     * 
     * @param string $message
     * @param string $plural
     * @param int    $n
     * 
     * @return string
     */
    public function ngettext(string $singular, string $plural, int $n): string;
}
