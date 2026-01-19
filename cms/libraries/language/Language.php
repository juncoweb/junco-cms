<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Language\Translators\Gettext;
use Junco\Language\Translators\Native;
use Junco\Language\Translators\None;
use Junco\Language\Translators\TranslatorInterface;

/**
 * Language
 * 
 * @require <Router>
 */
class Language
{
    // vars
    protected $translator;
    protected $language;
    //
    protected $config         = null;
    protected $availables     = [];
    protected $num_availables = 0;

    /**
     * Constructor
     */
    public function __construct(string $domain = '', string $locale = '')
    {
        $this->config = config('language');

        if ($this->config['language.availables']) {
            $this->availables     = $this->config['language.availables'];
            $this->num_availables = count($this->availables);

            if ($this->num_availables == 1) {
                $language = $this->availables[0];
            } elseif ($this->config['language.type'] == 1) {
                $language = $this->findInUrl();
            } else {
                $language = $this->findInCookie();
            }
            if (!$domain) {
                $domain = $this->config['language.default_domain'];
            }
            if (!$locale) {
                $locale = $this->config['language.locale'];
            }

            if ($this->config['language.use_gettext'] && function_exists('gettext')) {
                $this->translator = new Gettext($language, $domain, $locale, $this->config['language.codeset']);
            } else {
                $this->translator = new Native($language, $domain, $locale);
            }
        } else {
            // default
            $language         = 'en_GB';
            $this->availables = [$language];
            $this->translator = new None();
        }

        $this->language = $language;
    }

    /**
     * Get Availables languages
     * 
     * @return array
     */
    public function getAvailables(): array
    {
        return $this->availables;
    }

    /**
     * Get current language
     * 
     * @return string
     */
    public function getCurrent(): string
    {
        return $this->language;
    }

    /**
     * Get Url Lang (Used by the url builder)
     * 
     * @return ?array
     */
    public function getUrlLang(): ?array
    {
        if ($this->config['language.type'] == 1 && $this->num_availables > 1) {
            return [
                'key' => $this->config['language.key'],
                'value' => $this->language
            ];
        }
        return null;
    }

    /**
     * Get translator
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
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
        return $this->translator->gettext($message);
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
        return $this->translator->ngettext($singular, $plural, $n);
    }

    /**
     * Find in Url.
     */
    protected function findInUrl(): string
    {
        $language = Filter::input(GET, $this->config['language.key']);

        if (!$language) {
            $language = $this->findInRoute();

            if (!$language && $this->config['language.negotiate']) {
                $language = $this->negotiate();
            }
        }

        return $this->normalize($language);
    }

    /**
     * Find in route.
     */
    protected function findInRoute(): ?string
    {
        $availables = $this->availables;

        if ($this->config['language.normalize']) {
            $availables = array_merge($availables, array_keys($this->config['language.normalize'] ?: []));
        }

        return router()->lookupLanguage($availables);
    }

    /**
     * Find in Cookie.
     */
    protected function findInCookie(): string
    {
        $cookieLanguage = cookie($this->config['language.key']);

        if ($cookieLanguage) {
            $language = $cookieLanguage;
        } elseif ($this->config['language.negotiate']) {
            $language = $this->negotiate();
        } else {
            $language = '';
        }

        $language = $this->normalize($language);

        if ($language != $cookieLanguage) {
            $this->setCookie($language);
        }

        return $language;
    }

    /**
     * Option to modify (and normalize) the language.
     */
    protected function normalize(?string $language): string
    {
        if (
            $language
            && $this->config['language.normalize']
            && isset($this->config['language.normalize'][$language])
        ) {
            $language = $this->config['language.normalize'][$language];
        }

        if (!$language || !in_array($language, $this->availables)) {
            $language = $this->availables[0];
        }

        return $language;
    }

    /**
     * Set
     */
    public function setCookie(string $language): bool
    {
        $cookie_path = config('system.cookie_path');

        return setcookie($this->config['language.key'], $language, 0x7fffffff, $cookie_path);
    }

    /**
     * Determine which language out of an available set the user prefers most.
     *
     * @see: http://www.php.net/manual/en/function.http-negotiate-language.php
     */
    protected function negotiate(): string
    {
        // vars
        $server    = request()->getServerParams();
        $accept    = $server['HTTP_ACCEPT_LANGUAGE'] ?? '';
        $negotiate = $this->config['language.negotiate'] ?: [];
        $bestlang  = '';
        $bestqval  = 0;

        // standard  for HTTP_ACCEPT_LANGUAGE is defined under
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
        // pattern to find is therefore something like this:
        //    1#( language-range [ ";" "q" "=" qvalue ] )
        // where:
        //    language-range  = ( ( 1*8ALPHA *( "-" 1*8ALPHA ) ) | "*" )
        //    qvalue         = ( "0" [ "." 0*3DIGIT ] )
        //            | ( "1" [ "." 0*3("0") ] )
        preg_match_all(
            "/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?" .
                "(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i",
            $accept,
            $hits,
            PREG_SET_ORDER
        );

        foreach ($hits as $arr) {
            // read data from the array of this hit
            $langprefix = strtolower($arr[1]);
            if (!empty($arr[3])) {
                $langrange = strtolower($arr[3]);
                $language = $langprefix . '-' . $langrange;
            } else {
                $language = $langprefix;
            }

            $qvalue = !empty($arr[5]) ? floatval($arr[5]) : 1.0;

            if (in_array($language, $negotiate) && ($qvalue > $bestqval)) { // find q-maximal language
                $bestlang = $language;
                $bestqval = $qvalue;
            } else if (in_array($langprefix, $negotiate) && (($qvalue * 0.9) > $bestqval)) {
                // if no direct hit, try the prefix only but decrease q-value by 10% (as http_negotiate_language does)
                $bestlang = $langprefix;
                $bestqval = $qvalue * 0.9;
            }
        }

        return $bestlang;
    }
}
