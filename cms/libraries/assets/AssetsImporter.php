<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Assets\Compilation\ScssCompiler;

class AssetsImporter extends AssetsBasic
{
    // vars
    protected AssetsBasic $from;
    protected Filesystem  $fs;

    /**
     * Import
     *
     * @param string $basepath
     * @param array  $aliases
     *
     * @return void
     */
    public function import(string $basepath, array $aliases): void
    {
        $this->from = new AssetsBasic($basepath);
        $this->fs   = new Filesystem('');

        $this->importThemes();
        $this->importDataAndMinifyFiles($aliases);
    }

    /**
     * Import themes
     * 
     * @return void
     */
    protected function importThemes(): void
    {
        $from = $this->from->getThemesPath();
        $to   = $this->getThemesPath();

        is_dir($from)
            and $this->fs->copy($from, $to);
    }

    /**
     * Import data and minify files
     * 
     * @param array $aliases
     * 
     * @return void
     */
    protected function importDataAndMinifyFiles(array $aliases): void
    {
        $srcKeys = $this->from->getAllKeysFromAliases($aliases);
        $curKeys = $this->getAllKeysFromAliases($aliases);

        if ($srcKeys) {
            $precompile     = (int)$this->config['assets.precompile'];
            $mustBeCompiled = ScssCompiler::isEnabled($precompile);

            foreach ($srcKeys as $key) {
                $compile  = $mustBeCompiled;
                $isUpdate = $this->hasDataFile($key);

                if ($compile || $isUpdate) {
                    $newData = $this->from->fetch($key);
                }

                if ($isUpdate) {
                    $this->updateData($key, $newData, $compile);
                } else {
                    $this->copyDataFile($key);
                }

                if ($compile) {
                    $this->compile($key, $newData['assets'], $this->config['assets.minify'], $this->config['assets.fixurl'], $precompile);
                } else {
                    $this->copyMinifyFile($key);
                }
            }

            $this->updateVersion();
        }

        // cleaner
        if ($curKeys) {
            $diffKeys = array_diff($curKeys, $srcKeys);

            if ($diffKeys) {
                (new AssetsStorage($this->abspath))->removeAll($diffKeys);
            }
        }
    }

    /**
     * Update data
     * 
     * @param string $key
     * @param array  &$newData
     * @param bool   &$compile
     * 
     * @return void
     */
    protected function updateData(string $key, array &$newData, bool &$compile): void
    {
        $curData = $this->fetch($key);

        if ($curData['assets'] != $curData['default_assets']) {
            $compile   = false;
            $replace   = [];
            $usrAssets = [];

            // The user modified the assets. I look for what he added!
            $curDefaultAssets = array_column($this->explodeAssets($curData['default_assets']), 0);

            foreach ($this->explodeAssets($curData['assets']) as $asset) {
                if (!in_array($asset[0], $curDefaultAssets)) {
                    $usrAssets[] = $this->implodeAsset($asset);
                } elseif (isset($asset[1])) {
                    $replace[$asset[0]] = $this->implodeAsset($asset);
                }
            }

            if ($replace) {
                $newData['assets'] = strtr($newData['assets'], $replace);
            }

            if ($usrAssets) {
                $newData['assets'] .= $this->getUserAssets($usrAssets);
            }

            if (!$compile) {
                $compile = ($replace || $usrAssets);
            }
        }

        $this->storeData($key, $newData);
    }

    /**
     * Has
     * 
     * @param string $key
     * 
     * @return bool
     */
    protected function hasDataFile(string $key): bool
    {
        return is_file($this->dataPath . $key . '.json');
    }

    /**
     * Copy
     * 
     * @param string $key
     * 
     * @return bool
     */
    protected function copyDataFile(string $key): bool
    {
        return $this->fs->copy(
            $this->from->getDataFile($key),
            $this->getDataFile($key)
        );
    }

    /**
     * Copy minify file
     * 
     * @param string $key
     * 
     * @return bool
     */
    protected function copyMinifyFile(string $key): bool
    {
        return $this->fs->copy(
            $this->from->getMinifyFile($key),
            $this->getMinifyFile($key)
        );
    }

    /**
     * Get
     * 
     * @param array $assets
     * 
     * @return string
     */
    protected function getUserAssets(array $assets): string
    {
        return ',' . PHP_EOL . PHP_EOL
            . '# user assets' . PHP_EOL
            . implode(',' . PHP_EOL, $assets);
    }
}
