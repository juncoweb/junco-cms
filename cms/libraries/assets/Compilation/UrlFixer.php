<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Assets\Compilation;

class UrlFixer
{
    // const
    const DISABLE  = -1;
    const RELATIVE = 0;
    const ABSOLUTE = 1;

    // vars
    protected string $site_baseurl;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->site_baseurl = config('site.baseurl');
    }

    /**
     * Fix the url of a resource.
     *
     * @param string $content Css or Javascript content to fix.
     * @param string $source  Path to the current css file.
     * @param string $target  Path to the target css file.
     * @param int    $format  Final format of the path.
     *
     * @return string $content
     */
    public function fromString(string $content, string $source, string $target, int $format = self::RELATIVE): string
    {
        if ($format == self::DISABLE) {
            return $content;
        }

        $replaces = $this->getReplaces($content);

        if (!$replaces) {
            return $content;
        }

        $source = dirname($source) . '/';

        foreach ($replaces as $search => $replace) {
            $replace = $this->realpath($source . $search);

            if ($format == self::ABSOLUTE) {
                $replace = $this->site_baseurl . $replace;
            } else {
                $replace = $this->getRelativePath($replace, $target);
            }

            $replaces[$search] = $replace;
        }

        return strtr($content, $replaces);
    }

    /**
     * Get
     * 
     * @param string $content
     *
     * @return array
     */
    protected function getReplaces(string $content): array
    {
        $replaces = [];

        if (preg_match_all('/url\(([^\(]+)\)/', $content, $matches)) {
            foreach ($matches[1] as $url) {
                if ($url = $this->sanitizeUrl($url)) {
                    $replaces[$url] = null;
                }
            }
        }

        return $replaces;
    }

    /**
     * Get
     * 
     * @return array
     */
    public static function getOptions(): array
    {
        return [
            self::DISABLE  => _t('No'),
            self::RELATIVE => _t('Relative'),
            self::ABSOLUTE => _t('Absolute'),
        ];
    }

    /**
     * Returns true if the option passed enables URL fixing.
     * 
     * @param int $option
     * 
     * @return bool
     */
    public static function isEnabled(int $option): bool
    {
        return in_array($option, [self::RELATIVE, self::ABSOLUTE]);
    }

    /**
     * Sanitize
     * 
     * @param string $url
     *
     * @return string
     */
    protected function sanitizeUrl(string $url): string
    {
        $url = trim($url, '"\'');

        if (filter_var($url, FILTER_VALIDATE_URL) || substr($url, 0, 5) == 'data:') {
            return '';
        }

        return $url;
    }

    /**
     * Change any relative path to an absolute path.
     * 
     * @see http://php.net/manual/en/function.realpath.php
     * 
     * @param string $path
     * 
     * @return string
     */
    public function realpath(string $path): string
    {
        $parts = explode('/', $path);
        $absolutes = [];

        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }

            if ('..' == $part) {
                array_pop($absolutes);
            } elseif ($part) {
                $absolutes[] = $part;
            }
        }

        return implode('/', $absolutes);
    }

    /**
     * Change any relative path to an absolute path.
     * 
     * @param string $from
     * @param string $to
     * 
     * @return string
     */
    protected function getRelativePath(string $from, string $to): string
    {
        $from  = explode('/', $from);
        $to    = explode('/', $to);
        $total = count($to);
        $min   = min($total, count($from)) - 1;

        for (
            $i = 0;
            $i < $min && $from[$i] == $to[$i];
            $i++
        ) {
            unset($from[$i]);
        }

        return str_repeat('../', $total - $i - 1) . implode('/', $from);
    }
}
