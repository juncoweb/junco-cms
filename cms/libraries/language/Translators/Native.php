<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Language\Translators;

class Native implements TranslatorInterface
{
    // vars
    protected $translates    = null;
    protected $locale        = '';
    protected $language        = '';
    protected $domain        = '';

    /**
     * Constructor
     */
    public function __construct(
        string $language,
        string $domain,
        string $locale,
        string $codeset = ''
    ) {
        $this->language = $language;
        $this->domain    = $domain;
        $this->locale    = $locale;

        // set
        $this->translates = $this->include();
        $this->translates['Messages']['Singulars'] ??= [];
        $this->translates['Messages']['Plurals'] ??= [];
        $this->translates['Plural-Forms'] ??= null;

        if (!is_callable($this->translates['Plural-Forms'])) {
            $this->translates['Plural-Forms'] = function (int $n) {
                return $n != 1 ? 1 : 0;
            };
        }
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
        return $this->translates['Messages']['Singulars'][$message] ?? $message;
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
        if (isset($this->translates['Plural-Forms'])) {
            $index = $this->translates['Plural-Forms']($n);

            if (isset($this->translates['Messages']['Plurals'][$singular][$index])) {
                return $this->translates['Messages']['Plurals'][$singular][$index];
            }
        }

        return ($n != 1) ? $plural : $singular;
    }

    /**
     * Include
     */
    protected function include()
    {
        $file = SYSTEM_STORAGE . sprintf('%s/%s/LC_MESSAGES/%s.mo.php', $this->locale, $this->language, $this->domain);

        is_file($file) or $this->write($file);

        return include $file;
    }

    /**
     * Write
     */
    protected function write(string $file)
    {
        file_put_contents($file, '<?php return ' . var_export($this->read(), true) . '; ?>');
    }

    /**
     * Read
     */
    protected function read()
    {
        $file = SYSTEM_STORAGE . sprintf('%s/%s/LC_MESSAGES/%s.po', $this->locale, $this->language, $this->domain);
        $contents = is_file($file) ? file_get_contents($file) : false;

        if ($contents) {
            $contents = str_replace(["\r\n", "\r", "\"\n\"", '\\"'], ["\n", "\n", '', '"'], $contents);
            $translates = [
                'Messages' => [
                    'Singulars' => [],
                    'Plurals' => [],
                ]
            ];

            if (preg_match('%Plural-Forms: nplurals=\d; plural\s*=\s*(.*?);%', $contents, $match)) {
                $eval = str_replace('n', '$n', $match[1]);
                $translates['Plural-Forms'] = 'function(int $n) { $plural = ' . $eval . '; return is_bool($plural) ? (int)$plural : $plural; }';
            }

            // I remove the fuzzy translations
            $contents = preg_replace(
                '%'
                    . '^#, (.*?)fuzzy'
                    . '(?s:.*?)'
                    . 'msgstr(?:\[\d\])? "(?:.+?)%m',
                '',
                $contents
            );

            // singulars
            preg_match_all('%^'
                . 'msgid "(.+?)"' . '\R'
                . 'msgstr "(.+?)"' . '\R'
                . '%m', $contents, $matches);

            $translates['Messages']['Singulars'] = array_combine($matches[1], $matches[2]);

            // plurals
            preg_match_all('%^'
                . 'msgid "(.+?)"' . '\R'
                . 'msgid_plural "(.+?)"' . '\R'
                . '((?:msgstr\[\d\] "(?:.+?)"\R){1,})'
                . '%m', $contents, $matches, PREG_SET_ORDER);

            $plurals = [];
            foreach ($matches as $match) {
                preg_match_all('%"(.+?)"%m', $match[3], $_matches);
                $plurals[$match[1]] = $_matches[1];
            }

            $translates['Messages']['Plurals'] = $plurals;
        } else {
            $translates = [];
        }

        return $translates;
    }
}
