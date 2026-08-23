<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Language\Translators;

class Native implements TranslatorInterface
{
    protected array  $translates;
    protected string $basepath;

    /**
     * Constructor
     */
    public function __construct(
        string $language,
        string $domain,
        string $locale,
        string $codeset = ''
    ) {
        $this->basepath = SYSTEM_STORAGE . sprintf('%s/%s/LC_MESSAGES/%s', $locale, $language, $domain);

        //
        $this->translates = $this->include();
        $this->translates['Singulars']    ??= [];
        $this->translates['Plurals']      ??= [];
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
        return $this->translates['Singulars'][$message] ?? $message;
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
        if (isset($this->translates['Plural-Forms'])) {
            $index = $this->translates['Plural-Forms']($n);

            if (isset($this->translates['Plurals'][$singular][$index])) {
                return $this->translates['Plurals'][$singular][$index];
            }
        }

        return ($n != 1) ? $plural : $singular;
    }

    /**
     * Include
     */
    protected function include(): array
    {
        $file = sprintf('%s.mo.php', $this->basepath);

        is_file($file)
            or $this->write($file);

        return include $file;
    }

    /**
     * Write
     */
    protected function write(string $file): void
    {
        file_put_contents($file, '<?php return ' . var_export($this->read(), true) . '; ?>');
    }

    /**
     * Read
     */
    protected function read(): array
    {
        $file = sprintf('%s.po', $this->basepath);
        $content = is_readable($file)
            ? file_get_contents($file)
            : false;

        if (!$content) {
            return [];
        }

        $content = $this->sanitize($content);

        return [
            'Singulars'    => $this->getSingulars($content),
            'Plurals'      => $this->getPlurals($content),
            'Plural-Forms' => $this->getPluralForms($content)
        ];
    }

    /**
     * Sanitize
     */
    protected function sanitize(string $content): string
    {
        // normalize EOL
        // joins long text strings
        // double quotation marks are no longer escaped characters
        $content = str_replace(["\r\n", "\r", "\"\n\"", '\\"'], ["\n", "\n", '', '"'], $content);

        // I remove the fuzzy translations
        return preg_replace('/^#, (.*?)fuzzy(?s:.*?)msgstr(?:\[\d\])? "(?:.+?)/m', '', $content);
    }

    /**
     * Get
     */
    protected function getSingulars(string $content): array
    {
        preg_match_all('/^'
            . 'msgid "(.+?)"' . '\R'
            . 'msgstr "(.+?)"' . '\R'
            . '/m', $content, $matches);

        return array_combine($matches[1], $matches[2]);
    }

    /**
     * Get
     */
    protected function getPlurals(string $content): array
    {
        preg_match_all('/^'
            . 'msgid "(.+?)"' . '\R'
            . 'msgid_plural "(.+?)"' . '\R'
            . '((?:msgstr\[\d\] "(?:.+?)"\R){1,})'
            . '/m', $content, $matches, PREG_SET_ORDER);

        $plurals = [];

        foreach ($matches as $match) {
            preg_match_all('%"(.+?)"%m', $match[3], $_matches);
            $plurals[$match[1]] = $_matches[1];
        }

        return $plurals;
    }

    /**
     * Get
     */
    protected function getPluralForms(string $content): ?string
    {
        $pattern = '/Plural-Forms: nplurals=\d; plural\s*=\s*(.*?);/';

        if (!preg_match($pattern, $content, $match)) {
            return null;
        }

        return 'function(int $n) {'
            .    ' $plural = ' . str_replace('n', '$n', $match[1]) . ';'
            .    ' return is_bool($plural) ? (int)$plural : $plural;'
            . ' }';
    }
}
