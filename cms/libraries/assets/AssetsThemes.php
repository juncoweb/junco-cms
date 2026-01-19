<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Assets\Compilation\ScssCompiler;
use Junco\Assets\Compilation\UrlFixer;

class AssetsThemes extends AssetsBasic
{
    /**
     * Returns all themes
     * 
     * @return array
     */
    public function getAll(): array
    {
        $rows = [];
        foreach ($this->scandir($this->themesPath) as $alias) {
            $dir = $this->themesPath . '/' . $alias;

            if (is_dir($dir)) {
                foreach ($this->scandir($dir) as $name) {
                    if (is_dir($dir . '/' . $name)) {
                        $rows[] = [
                            'alias' => $alias,
                            'name'  => $name,
                            'key'   => $alias . ($name !== 'default' ? '-' . $name : '')
                        ];
                    }
                }
            }
        }

        return $rows;
    }

    /**
     * Has
     * 
     * @param string $key
     * 
     * @return bool
     */
    public function has(string $key): bool
    {
        return null !== $this->getThemeFromkey($key);
    }

    /**
     * Save
     * 
     * @param string $key
     * @param string $content
     * 
     * @return bool
     */
    public function save(string $key, string $content = ''): bool
    {
        return (new Filesystem(''))->putContent(
            $this->getScssVarFile($key),
            $content
        );
    }

    /**
     * Copy
     * 
     * @param string $from
     * @param string $to
     * 
     * @return bool
     */
    public function copy(string $from, string $to = ''): bool
    {
        if ($this->getThemeFromkey($to)) {
            throw new Exception(_t('The theme already exists.'));
        }

        if ($from == $to) {
            throw new Exception(_t('Please, enter another name.'));
        }

        $from = $this->getThemePath($from);
        $to   = $this->getThemePath($to);

        return is_dir($from) and (new Filesystem(''))->copy($from, $to);
    }

    /**
     * Delete
     *
     * @param array|string $key
     * 
     * @return void
     */
    public function delete(array|string $keys): void
    {
        if (!$keys) {
            return;
        }
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        $fs = new Filesystem('');

        foreach ($keys as $key) {
            $fs->remove($this->getThemePath($key));
        }
    }

    /**
     * Get
     * 
     * @param string $key
     *
     * @return string
     */
    public function getThemePath(string $key = ''): string
    {
        $data = $this->parseKey($key);
        return $this->themesPath . $data['alias'] . '/' . $data['name'] . '/';
    }

    /**
     * Get
     * 
     * @param string $key
     * 
     * @return string
     */
    public function getScssVarFile(string $key): string
    {
        return $this->getThemePath($key) . '_variables.scss';
    }

    /**
     * Get
     * 
     * @param string $target
     * @param string $key
     * 
     * @return string
     */
    public function getThemeTarget(string $target, string $key): string
    {
        $data = $this->parseKey($key);
        return dirname($target) . '/' . $data['alias'] . '/' . $data['name'] . '/' . basename($target);
    }

    /**
     * Get
     * 
     * @param string $key
     * 
     * @return string
     */
    public function getScssVarContent(string $key): string
    {
        return file_get_contents($this->getScssVarFile($key)) ?: '';
    }

    /**
     * Compile Theme
     *
     * @param string $key
     * @param bool   $minify   Enable the minimizer.
     * @param int    $fixurl   Fix the url of the CSS files.
     * 
     * @return bool
     */
    public function compileTheme(string $key, bool $minify = false, ?int $fixurl = null): bool
    {
        $theme    = $this->getThemeFromkey($key);
        $rows     = $this->fetchAll();
        $fixurl ??= UrlFixer::DISABLE;

        if (!$theme) {
            return false;
        }

        foreach ($rows as $row) {
            if ($row['type'] === 'css') {
                $this->compile(
                    $row['key'],
                    $row['assets'],
                    $minify,
                    $fixurl,
                    ScssCompiler::ENABLE_ALL,
                    [$theme]
                );
            }
        }

        $this->updateVersion();
        return true;
    }

    /**
     * Get
     *
     * @param string $key
     * 
     * @return ?array
     */
    protected function getThemeFromkey(string $key): ?array
    {
        $data = $this->parseKey($key);

        return is_file($this->themesPath . sprintf('%s/%s/_variables.scss', $data['alias'], $data['name']))
            ? $data
            : null;
    }

    /**
     * Get
     *
     * @param string $key
     * 
     * @return array
     */
    protected function parseKey(string $key): array
    {
        $part  = explode('-', $key, 2);
        $alias = $part[0];
        $name  = $part[1] ?? 'default';

        return [
            'alias' => $alias,
            'name'  => $name,
            'key'   => $alias . ($name != 'default' ? '-' . $name : '')
        ];
    }

    /**
     * Get
     * 
     * @return array
     */
    public function scanAll(): array
    {
        $rows = ['' => _t('Default')];

        foreach ($this->getAll() as $row) {
            $rows[$row['alias'] . '/' . $row['name']] = $row['key'];
        }

        return $rows;
    }
}
