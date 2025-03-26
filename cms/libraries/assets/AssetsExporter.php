<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Assets\Compilation\ScssCompiler;
use Junco\Assets\Compilation\UrlFixer;

class AssetsExporter extends AssetsBasic
{
    protected AssetsBasic $to;
    protected Filesystem  $fs;

    /**
     * Has
     *
     * @param string $extension_alias
     *
     * @return bool
     */
    public function has(string $extension_alias): bool
    {
        return glob($this->themesPath . $extension_alias . '/')
            || glob($this->dataPath . $extension_alias . '[-.]*.json');
    }

    /**
     * Export
     *
     * @param array  $aliases
     * @param string $dst_path
     *
     * @return void
     */
    public function export(string $basepath, array $aliases)
    {
        $this->to = new AssetsBasic($basepath);
        $this->fs = new Filesystem('');

        $this->exportDataAndMinifyFiles($aliases);
        $this->exportThemes($aliases);
    }

    /**
     * Export data an minify file.
     * 
     * @param array $aliases
     * 
     * @return void
     */
    protected function exportDataAndMinifyFiles(array $aliases): void
    {
        $keys = $this->getAllKeysFromAliases($aliases);

        if ($keys) {
            $this->compileFromKeys($keys, true, UrlFixer::RELATIVE, ScssCompiler::ENABLE);

            foreach ($keys as $key) {
                $this->copyDataFile($key);
                $this->copyMinifyFile($key);
            }
        }
    }

    /**
     * Copy
     * 
     * @param string $key
     * 
     * @return void
     */
    protected function copyDataFile(string $key): void
    {
        $this->fs->copy(
            $this->getDataFile($key),
            $this->to->getDataFile($key)
        );
    }

    /**
     * Copy
     * 
     * @param string $key
     * 
     * @return void
     */
    protected function copyMinifyFile(string $key): void
    {
        $this->fs->copy(
            $this->getMinifyFile($key),
            $this->to->getMinifyFile($key),
        );
    }

    /**
     * Export themes
     * 
     * @param array $aliases
     * 
     * @return void
     */
    protected function exportThemes(array $aliases): void
    {
        $aliases = $this->getAliasesWithThemes($aliases);

        if ($aliases) {
            $toThemesPath = $this->to->getThemesPath();

            foreach ($aliases as $alias) {
                $this->fs->copy(
                    $this->themesPath . $alias,
                    $toThemesPath . $alias
                );
            }
        }
    }

    /**
     * Get
     * 
     * @param array $aliases
     * 
     * @return array
     */
    protected function getAliasesWithThemes(array $aliases): array
    {
        $has = [];

        foreach ($aliases as $alias) {
            if (is_dir($this->themesPath . $alias)) {
                $has[] = $alias;
            }
        }

        return $has;
    }
}
