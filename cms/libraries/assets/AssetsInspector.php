<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class AssetsInspector extends AssetsBasic
{
    // vars
    protected $asset_not_found = ' /* this is not found */';
    protected $compiled_file_not_found = '# Compiled file not found.';

    /**
     * Inspect all
     *
     * @param array $keys
     *
     * @return void
     */
    public function inspectAll(array $keys): void
    {
        foreach ($keys as $key) {
            $data = $this->fetch($key);

            if ($data) {
                $this->verifyCompileFileIsFound($data);
                $this->verifyAssetsAreFound($data);
                $this->seekOtherAssets($data);
                $this->storeData($key, $data);
            }
        }
    }

    /**
     * Verify
     * 
     * @param array &$data
     * 
     * @return void
     */
    protected function verifyCompileFileIsFound(array &$data): void
    {
        $target = $this->getMinifyFile($data['key']);

        if (is_file($target)) {
            $data['assets']        = str_replace($this->compiled_file_not_found, '', $data['assets']);
            $data['to_verify']    = 0;
        } else {
            $data['assets']        = $this->compiled_file_not_found . "\n" . $data['assets'];
            $data['to_verify']    = 1;
        }
    }

    /**
     * Verify
     * 
     * @param array &$data
     * 
     * @return void
     */
    protected function verifyAssetsAreFound(array &$data): void
    {
        // I clean the messages.
        $data['assets'] = str_replace($this->asset_not_found, '', $data['assets']);

        // verify
        $assets = $this->explodeAssets($data['assets']);

        if ($assets) {
            $replaces = [];

            foreach ($assets as $asset) {
                foreach ($asset as $file) {
                    if (!is_file($this->abspath . $file)) {
                        $line = $this->implodeAsset($asset);
                        $replaces[$line] = $line . $this->asset_not_found;
                        $data['to_verify']    = 1;
                    }
                }
            }

            if ($replaces) {
                $data['assets'] = strtr($data['assets'], $replaces);
            }
        }
    }

    /**
     * Seek
     * 
     * @param array &$data
     * 
     * @return void
     */
    protected function seekOtherAssets(array &$data): void
    {
        $assets    = $this->scan($data);

        foreach ($assets as $index => $asset) {
            if (false !== strpos($data['assets'], $asset)) {
                unset($assets[$index]);
            }
        }

        if ($assets) {
            $data['assets'] .= "\n#" . implode(",\n#", $assets);
            $data['to_verify'] = 1;
        }
    }

    /**
     * Scan possible resources
     * 
     * @param array $data
     * 
     * @return array
     */
    protected function scan(array $data): array
    {
        $name      = explode('-', $data['name'], 2);
        $component = $name[0];
        $index     = $name[1] ?? 'front';

        if ($index == 'myspace') {
            $index = 'my';
        }

        $pattern = $this->abspath . sprintf('app/%1$s/%3$s/%2$s.*.%3$s', $component, $index, $data['type']);
        $files   = glob($pattern);

        if (!$files) {
            return [];
        }

        $length = strlen($this->abspath);

        return array_map(function ($file) use ($length) {
            return substr($file, $length);
        }, $files);
    }
}
