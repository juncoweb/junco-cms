<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Language\Translators;

class Gettext implements TranslatorInterface
{
    /**
     * Constructor
     */
    public function __construct(
        string $language,
        string $domain,
        string $locale,
        string $codeset = ''
    ) {
        if (!$codeset) {
            $codeset = 'utf8';
        }

        if (DIRECTORY_SEPARATOR == "\\") {        // windows
            putenv("LC_ALL={$language}");
            //putenv("LC_MESSAGES={$language}");
        } else {                                // linux
            setlocale(LC_ALL, $language . '.' . $codeset);
            //setlocale(LC_MESSAGES, $language);
        }

        bindtextdomain($domain, SYSTEM_STORAGE . $locale);
        textdomain($domain);
        bind_textdomain_codeset($domain, $codeset);
    }

    /**
     * Lookup a message in the current domain
     * 
     * @param string $message
     * 
     * @return string
     */
    public function gettext(string $message): string
    {
        return gettext($message);
    }

    /**
     * Plural version of gettext
     * 
     * @param string $message
     * @param string $plural
     * @param int    $n
     * 
     * @return string
     */
    public function ngettext(string $singular, string $plural, int $n): string
    {
        return ngettext($singular, $plural, $n);
    }
}
