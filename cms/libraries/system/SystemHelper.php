<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class SystemHelper
{
    /**
     * Scan plugins
     * 
     * @param string $search
     * @param array  $rows
     * 
     * @return array $rows
     */
    public static function scanPlugins(string $search, array $rows = []): array
    {
        $pattern = sprintf('cms/plugins/*/%s/*', $search);
        $regex   = sprintf('#cms/plugins/(.*?)/%s/(.*?)$#', $search);
        $cdir    = glob(SYSTEM_ABSPATH . $pattern, GLOB_ONLYDIR);

        foreach ($cdir as $dir) {
            preg_match($regex, $dir, $matches);
            $plugin            = $matches[1] . ($matches[2] == 'default' ? '' : '.' . $matches[2]);
            $rows[$plugin]    = $plugin;
        }

        return $rows;
    }

    /**
     * Scan views
     * 
     * @param string $search
     * @param array  $rows
     * 
     * @return array $rows
     */
    public static function scanSnippets(string $search, array $rows = []): array
    {
        $pattern = sprintf('{cms/snippets/%1$s/master/*/snippet.php,cms/snippets/*/%1$s/*/snippet.php}', $search);
        $regex   = sprintf('#cms/snippets/%1$s/master/(.*?)/snippet.php$|cms/snippets/(.*?)/%1$s/(.*?)/snippet.php$#', $search);
        $cdir    = glob(SYSTEM_ABSPATH . $pattern, GLOB_BRACE);

        foreach ($cdir as $file) {
            preg_match($regex, $file, $matches);

            if (isset($matches[2])) {
                $snippet        = $matches[2] . ($matches[3] == 'default' ? '' : '.' . $matches[3]);
                $rows[$snippet] = $snippet;
            } elseif ($matches[1] == 'default') {
                $rows['default'] = _t('Default');
            } else {
                $snippet        = '.' . $matches[1];
                $rows[$snippet] = $snippet;
            }
        }

        return $rows;
    }

    /**
     * Statements
     * 
     * @param bool $inherit
     * 
     * @return array
     */
    public static function getStatements(bool $inherit = false): array
    {
        return [
            0 => ($inherit ? _t('Inherit') : _t('Developer')),
            10 => '1.0',
            20 => '2.0',
            21 => '2.1'
        ];
    }
}
