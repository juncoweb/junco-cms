<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Assets\Compilation\CssCompiler;
use Junco\Assets\Compilation\JsCompiler;

class AssetsBasic
{
    // vars
    protected array        $config;
    protected string       $abspath;
    protected string       $dataPath;
    protected string       $themesPath;
    protected string       $minifyFile;
    protected ?JsCompiler  $jsCompiler = null;
    protected ?CssCompiler $cssCompiler = null;

    /**
     * Constructor
     */
    public function __construct(string $abspath = '')
    {
        $this->config = config('assets');
        if ($abspath) {
            $this->abspath    = $abspath;
            $this->minifyFile = 'assets/%s.min.%s';
            $this->dataPath   = $abspath . 'storage/assets/data/';
            $this->themesPath = $abspath . 'storage/assets/themes/';
        } else {
            $this->abspath    = SYSTEM_ABSPATH;
            $this->minifyFile = $this->config['assets.minify_file'];
            $this->dataPath   = SYSTEM_STORAGE . $this->config['assets.data_path'];
            $this->themesPath = SYSTEM_STORAGE . 'assets/themes/';
        }
    }

    /**
     * Returns the asset data.
     *
     * @param string $key
     *
     * @return ?array
     */
    public function fetch(string $key): ?array
    {
        $data = $this->getJsonFromFile($this->dataPath . $key . '.json');

        if (!$data) {
            return null;
        }

        $info = pathinfo($key);
        return [
            'key'            => $key,
            'name'           => $info['filename'],
            'type'           => $info['extension'],
            'assets'         => $data['assets'] ?? '',
            'default_assets' => $data['default_assets'] ?? '',
            'to_verify'      => $data['to_verify'] ?? 0,
        ];
    }

    /**
     * Returns data for all assets.
     * 
     * @param ?callable $filter
     *
     * @return array
     */
    public function fetchAll(?callable $filter = null): array
    {
        $rows = [];

        foreach ($this->scandir($this->dataPath) as $node) {
            $info = pathinfo($node);

            if ($info['extension'] === 'json') {
                $row = $this->fetch($info['filename']);

                if ($row && (!$filter || $filter($row))) {
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }

    /**
     * Get assets data.
     * 
     * @param array  $aliases
     *
     * @return array
     */
    public function getAllKeysFromAliases(array $aliases): array
    {
        if (!$aliases) {
            return [];
        }

        $keys = [];
        foreach ($this->scandir($this->dataPath) as $file) {
            $info = pathinfo($file);

            if ($info['extension'] == 'json') {
                $key = $info['filename'];

                if (!in_array($this->getExtensionAliasFromKey($key), $aliases)) {
                    continue;
                }

                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * Compile
     *
     * @param string $key         The key with which the compilation is identified. It consists of «extension_alias[-name].(js|css)»
     * @param string $sheet       A comma-separated list of absolute paths to resources.
     * @param bool   $minify      Enable the minimizer.
     * @param int    $fixurl      Fix the url of the CSS files.
     * @param int    $precompile  For each style sheet, try to find and run the precompiler.
     *
     * @return bool
     */
    public function compile(
        string $key,
        string $sheet,
        bool   $minify,
        int    $fixurl,
        int    $precompile,
        array  $themes = []
    ): bool {
        $files     = $this->getAllFilesFromSheet($sheet, true);
        $target    = $this->getMinifyFile($key, false);
        $extension = pathinfo($target, PATHINFO_EXTENSION);

        if ($extension == 'js') {
            $this->jsCompiler ??= new JsCompiler($this->abspath);
            return $this->jsCompiler->compile($target, $files, $minify);
        } elseif ($extension == 'css') {
            $this->cssCompiler ??= new CssCompiler($this->abspath);
            return $this->cssCompiler->compile($target, $files, $minify, $fixurl, $precompile, $themes);
        }

        throw new Exception("Unsupported compile extension '{$extension}'");
    }

    /**
     * Compile from keys.
     *
     * @param array $keys
     * @param bool  $minify,
     * @param int   $fixurl,
     * @param int   $precompile
     *
     * @return void
     */
    public function compileFromKeys(
        array $keys,
        bool  $minify,
        int   $fixurl,
        int   $precompile
    ) {
        foreach ($keys as $key) {
            $data = $this->fetch($key);

            if ($data) {
                $this->compile(
                    $key,
                    $data['assets'],
                    $minify,
                    $fixurl,
                    $precompile
                );
            }
        }

        $this->updateVersion();
    }

    /**
     * Get minify file.
     *
     * @param string $key
     *
     * @return string
     */
    public function getMinifyFile(string $key, bool $absolute = true): string
    {
        $info = pathinfo($key);
        return ($absolute ? $this->abspath : '') . sprintf($this->minifyFile, $info['filename'], $info['extension']);
    }

    /**
     * Get file
     *
     * @param string $key
     *
     * @return string
     */
    public function getDataFile(string $key): string
    {
        return $this->dataPath . $key . '.json';
    }

    /**
     * Get
     *
     * @return string
     */
    public function getThemesPath(): string
    {
        return $this->themesPath;
    }

    /**
     * It update version control of compiled assets.
     * 
     * @param bool $enable
     * 
     * @return void
     */
    public function updateVersion(?bool $enable = null): void
    {
        $data = [];
        if ($enable === null) {
            $enable = $this->config['assets.version_control'];
        }

        if ($enable) {
            $dir     = pathinfo($this->minifyFile, PATHINFO_DIRNAME) . '/';
            $absdir = $this->abspath . $dir;

            foreach ($this->scandir($absdir) as $file) {
                $data[$dir . $file] = base_convert(filemtime($absdir . $file), 10, 36);
            }
        }

        (new Settings('template'))->update(['version_control' => $data]);
    }

    /**
     * Get all files from sheet.
     *
     * @param string  $sheet
     * @param bool    $import    If it is verified that one of the resources belongs to a compilation, it will recursively import the original files of the same ones.
     *
     * @return array
     */
    protected function getAllFilesFromSheet(string $sheet, bool $import = false): array
    {
        $pattern = $this->getMinifyPattern();
        $files   = [];

        foreach ($this->explodeAssets($sheet) as $asset) {
            $file = $asset[1] ?? $asset[0];

            if ($import && preg_match($pattern, $file, $match)) {
                $data = $this->fetch($match[1] . '.' . $match[2]);

                if ($data) {
                    $files = array_merge($files, $this->getAllFilesFromSheet($data['assets']));
                }
            } else {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Clear
     *
     * @param  string $sheet
     *
     * @return string $sheet
     */
    protected function clear(string $sheet)
    {
        return preg_replace(
            '%
			/\*.*?\*/		# comments
			|(?m://.*?$)	#
			|(?m:\#.*?$)	#
			|[\r\n\s\t]+	# specials chars
			%sx',
            '',
            $sheet
        );
    }

    /**
     * Explode
     *
     * @param string $sheet
     *
     * @return array
     */
    protected function explodeAssets(string $sheet): array
    {
        $sheet = $this->clear($sheet);

        if (!$sheet) {
            return [];
        }

        $assets = array_filter(explode(',', $sheet));

        return array_map(fn($row) => explode(':', $row, 2), $assets);
    }

    /**
     * Explode
     *
     * @param array $asset
     *
     * @return string
     */
    protected function implodeAsset(array $asset): string
    {
        return implode(':', $asset);
    }

    /**
     * Get
     */
    protected function getExtensionAliasFromKey(string $key): string
    {
        return preg_split('/\W/', $key, 2)[0];
    }

    /**
     * Get minify pattern.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getMinifyPattern(): string
    {
        return '#^' . sprintf($this->minifyFile, '([\w-]+)', '(js|css|php)') . '$#';
    }

    /**
     * Get
     *
     * @param string $file
     *
     * @return array|false
     */
    protected function getJsonFromFile(string $file): array|false
    {
        $data = is_readable($file)
            ? file_get_contents($file)
            : false;

        if (!$data) {
            return false;
        }

        return json_decode($data, true) ?? false;
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
        $cdir = is_readable($dir)
            ? scandir($dir)
            : false;

        if (!$cdir) {
            return [];
        }

        return array_diff($cdir, ['.', '..']);
    }

    /**
     * Store data
     *
     * @param string $key
     * @param array  $data
     * 
     * @throws Exception
     *
     * @return void
     */
    protected function storeData(string $key, array $data): void
    {
        $file = $this->getDataFile($key);
        $data = json_encode($this->sanitizeData($data), JSON_PRETTY_PRINT);

        if (false === file_put_contents($file, $data)) {
            throw new Exception(_t('Error! the task has not been realized.'));
        }
    }

    /**
     * Sanitizes the data
     * 
     * @param array $data
     *
     * @return array
     */
    protected function sanitizeData(array $data): array
    {
        return array_intersect_key($data, [
            'assets'         => null,
            'default_assets' => null,
            'to_verify'      => null
        ]);
    }
}
