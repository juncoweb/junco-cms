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
    protected TranslatorInterface $translator;
    protected array  $availables;
    protected int    $num_availables = 0;
    protected string $language;
    protected string $key;
    protected int    $type;
    protected array  $normalize;

    /**
     * Constructor
     */
    public function __construct(string $domain = '', string $locale = '')
    {
        $config = config('language');

        $this->key        = $config['language.key'];
        $this->type       = $config['language.type'];
        $this->availables = $config['language.availables'] ?: [];
        $this->normalize  = $config['language.normalize'] ?: [];

        if (!$this->availables) { // default
            $this->language   = 'en_GB';
            $this->availables = [$this->language];
            $this->translator = new None();
            return;
        }

        $this->num_availables = count($this->availables);

        if ($this->num_availables == 1) {
            $language = $this->availables[0];
        } elseif ($this->type == 1) {
            $language = $this->findInUrl();
        } else {
            $language = $this->findInCookie();
        }

        if (!$domain) {
            $domain = $config['language.default_domain'];
        }

        if (!$locale) {
            $locale = $config['language.locale'];
        }

        if ($config['language.use_gettext'] && function_exists('gettext')) {
            $this->translator = new Gettext($language, $domain, $locale, $config['language.codeset']);
        } else {
            $this->translator = new Native($language, $domain, $locale);
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
        if ($this->type == 1 && $this->num_availables > 1) {
            return [
                'key' => $this->key,
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
     * @param string $singular
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
        $language = Filter::input(GET, $this->key);

        if (!$language) {
            $supported = $this->getSupportedLanguages();
            $language = $this->findInRoute($supported);

            if (!$language) {
                $language = $this->negotiate($supported);
            }
        }

        return $this->normalize($language);
    }

    /**
     * Find in route.
     */
    protected function findInRoute(array $supported): ?string
    {
        return router()->lookupLanguage($supported);
    }

    /**
     * Find in Cookie.
     */
    protected function findInCookie(): string
    {
        $cookieLanguage = cookie($this->key);
        $language = '';

        if ($cookieLanguage) {
            $language = $cookieLanguage;
        } else {
            $supported = $this->getSupportedLanguages();
            $language = $this->negotiate($supported);
        }

        $language = $this->normalize($language);

        if ($language != $cookieLanguage) {
            $this->setCookie($language);
        }

        return $language;
    }

    /**
     * Normalize
     * 
     * @param $language
     * 
     * @return string
     */
    protected function normalize(?string $language): string
    {
        if (
            $language
            && $this->normalize
            && isset($this->normalize[$language])
        ) {
            $language = $this->normalize[$language];
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

        return setcookie($this->key, $language, 0x7fffffff, $cookie_path);
    }

    /**
     * Determine which language out of an available set the user prefers most
     *
     * @see: http://www.php.net/manual/en/function.http-negotiate-language.php
     * 
     * @param array  $supported
     * 
     * @return string
     */
    protected function negotiate(array $supported): string
    {
        if (!$supported) {
            return '';
        }

        $header = $this->getHttpAcceptLanguage();

        if (!$header) {
            return '';
        }

        $bestlang = '';
        $bestqval = 0;

        // standard  for HTTP_ACCEPT_LANGUAGE is defined under
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
        // pattern to find is therefore something like this:
        //    1#( language-range [ ";" "q" "=" qvalue ] )
        // where:
        //    language-range  = ( ( 1*8ALPHA *( "-" 1*8ALPHA ) ) | "*" )
        //    qvalue         = ( "0" [ "." 0*3DIGIT ] )
        //            | ( "1" [ "." 0*3("0") ] )
        $pattern = "/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?" .
            "(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i";

        preg_match_all($pattern, $header, $hits, PREG_SET_ORDER);

        foreach ($hits as $arr) {
            // read data from the array of this hit
            $langprefix = strtolower($arr[1]);

            if (!empty($arr[3])) {
                $langrange = strtolower($arr[3]);
                $language  = $langprefix . '-' . $langrange;
            } else {
                $language  = $langprefix;
            }

            $qvalue = !empty($arr[5]) ? floatval($arr[5]) : 1.0;

            if (in_array($language, $supported) && ($qvalue > $bestqval)) { // find q-maximal language
                $bestlang = $language;
                $bestqval = $qvalue;
            } elseif (in_array($langprefix, $supported) && (($qvalue * 0.9) > $bestqval)) {
                // if no direct hit, try the prefix only but decrease q-value by 10% (as http_negotiate_language does)
                $bestlang = $langprefix;
                $bestqval = $qvalue * 0.9;
            }
        }

        return $bestlang;
    }

    /**
     * Get
     */
    protected function getHttpAcceptLanguage(): string
    {
        return request()?->getServerParams()['HTTP_ACCEPT_LANGUAGE'] ?? '';
    }

    /**
     * Get
     */
    protected function getSupportedLanguages(): array
    {
        $supported = $this->availables;

        if ($this->normalize) {
            $supported = array_merge($supported, array_keys($this->normalize));
        }

        return $supported;
    }
}
