<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class LanguageHelper
{
    // vars
    protected $locale = null;

    /**
     * Constructor
     *
     * @param string $locale  Set the path to the local folder.
     */
    public function __construct(string $locale = '')
    {
        $this->locale = SYSTEM_STORAGE . ($locale ?: config('language.locale')) . '/';
    }

    /**
     * Returns the available languages
     *
     * @param bool $all It also returns those that are not selected in the configuration.
     *
     * @return array
     */
    public function getAvailables(bool $all = false): array
    {
        // vars
        $rows = [];

        if ($all) {
            $availables = scandir($this->locale);
            if ($availables) {
                $availables = array_diff($availables, ['.', '..']);
            } else {
                $availables = [];
            }
        } else {
            $availables = app('language')->getAvailables();
        }

        foreach ($availables as $language) {
            if (is_dir($this->locale . $language)) {
                $json = $this->locale . $language . '/' . $language . '.json';
                $json = is_file($json)
                    ? json_decode(file_get_contents($json), true)
                    : false;

                $rows[$language] = $json['name'] ?? $language;
            }
        }

        return $rows;
    }

    /**
     * Try changing the current language
     *
     * @param string $language  The new language.
     */
    public function change(string $language)
    {
        if (!$language) {
            $language = false;
        } elseif (!is_dir($this->locale . $language)) {
            return false;
        }
        $config = config('language');

        switch ((int)$config['language.type']) {
            case 0:
                $cookie_path = config('system.cookie_path');
                return setcookie($config['language.key'], $language, 0x7fffffff, $cookie_path);
            case 1:
                return $language;
        }
    }

    /**
     * Get Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Translate
     */
    public function translate(string $basename, array $translate, string $dir = '')
    {
        foreach ($translate as $i => $t) {
            $t = str_replace('\'', '\\\'', html_entity_decode($t, ENT_QUOTES));
            $translate[$i] = "_t('$t')";
        }

        if (!$dir) {
            $dir = SYSTEM_STORAGE . 'translate/';
        }

        is_dir($dir) or mkdir($dir, SYSTEM_MKDIR_MODE, true);

        $file   = sprintf('%s%s.php', $dir, $basename);
        $buffer = '<?php return ' . implode(' . ' . PHP_EOL, $translate) . '; ?>';
        $result = file_put_contents($file, $buffer);

        if (false === $result) {
            throw new Exception('LanguageHelper::translate() [Error]');
        }
    }

    /**
     * Refresh
     */
    public function refresh()
    {
        $files = glob(sprintf('%s*/LC_MESSAGES/*.mo.php', $this->locale));

        foreach ($files as $file) {
            unlink($file);
        }
    }
}
