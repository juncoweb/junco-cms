<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class LanguageHelper
{
    protected string $locale;

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
     * @param bool $all    It also returns those that are not selected in the configuration.
     *
     * @return array
     */
    public function getAvailables(bool $all = false): array
    {
        $availables = $all
            ? $this->scandir($this->locale)
            : app('language')->getAvailables();

        $rows = [];
        foreach ($availables as $language) {
            $file = $this->locale . $language . '/' . $language . '.json';
            $json = $this->getJsonContent($file);

            $rows[$language] = $json['name'] ?? $language;
        }

        return $rows;
    }

    /**
     * Try changing the current language
     *
     * @param string $language  The new language.
     * 
     * @return bool
     */
    public function change(string $language): bool
    {
        if (!$language) {
            return false;
        }

        if (!is_dir($this->locale . $language)) {
            return false;
        }

        if (config('language.type') == 0) { // cookie
            return app('language')->setCookie($language);
        }

        return true;
    }

    /**
     * Get Locale
     * 
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Translate
     * 
     * @param string $basename
     * @param array  $translate
     * @param string $dir
     * 
     * @return void
     */
    public function translate(string $basename, array $translate, string $dir = ''): void
    {
        $file   = $this->getTranslateFile($dir ?: SYSTEM_STORAGE . 'translate/', $basename);
        $buffer = $this->getTranslateContent($translate);

        if (false === file_put_contents($file, $buffer)) {
            throw new Exception('LanguageHelper::translate() [Error]');
        }
    }

    /**
     * Refresh
     */
    public function refresh(): void
    {
        $pattern = sprintf('%s*/LC_MESSAGES/*.mo.php', $this->locale);
        $files   = glob($pattern);

        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * Scandir
     * 
     * @param string $dir
     * 
     * @return array
     */
    protected function scandir(string $dir): array
    {
        $cdir = is_readable($dir) ? scandir($dir) : false;

        if (!$cdir) {
            return [];
        }

        $nodes = array_diff($cdir, ['.', '..']);

        return $nodes;
    }

    /**
     * Get
     * 
     * @param string $file
     * 
     * @return ?array
     */
    protected function getJsonContent(string $file): ?array
    {
        if (!is_file($file)) {
            return null;
        }

        $content = file_get_contents($file);

        if (!$content) {
            return null;
        }

        return json_decode($content, true) ?? null;
    }

    /**
     * Get
     */
    protected function getTranslateContent(array $translate): string
    {
        foreach ($translate as $i => $t) {
            $t = str_replace('\'', '\\\'', html_entity_decode($t, ENT_QUOTES));
            $translate[$i] = "_t('$t')";
        }

        return '<?php return ' . implode(' . ' . PHP_EOL, $translate) . '; ?>';
    }

    /**
     * Get
     */
    protected function getTranslateFile(string $dir, string $basename): string
    {
        is_dir($dir)
            or mkdir($dir, SYSTEM_MKDIR_MODE, true);

        return sprintf('%s%s.php', $dir, $basename);
    }
}
