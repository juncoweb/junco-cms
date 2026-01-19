<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Assets\Compilation;

use AssetsThemes;
use Filesystem;
use Plugin;

class CssCompiler
{
    // vars
    protected string        $abspath;
    protected Filesystem    $fs;
    protected ?UrlFixer     $urlFixer     = null;
    protected ?ScssCompiler $scssCompiler = null;
    protected ?Plugin       $minifier     = null;
    //protected ?cssVarParser	$cssCompiler	= null;
    protected ?AssetsThemes $themes       = null;
    protected ?array        $themesList   = null;

    /**
     * Constructor
     */
    public function __construct(?string $abspath = null)
    {
        $this->abspath      = $abspath ?? SYSTEM_ABSPATH;
        $this->fs           = new Filesystem($this->abspath);
        $this->scssCompiler = new ScssCompiler($this->abspath);
        $this->urlFixer     = new UrlFixer;
    }

    /**
     * Compile
     *
     * @param string $target    The file where the compiled file will be saved.
     * @param array  $files     An array with all the files to compile.
     * @param bool   $minify    Enable the minimizer.
     * @param int    $fixurl    Fix the url of the CSS files.
     *  -1 No
     *   0 Relative
     *   1 Absolute
     * @param int    $precompile  For each style sheet, try to find and run the precompiler.
     *  -1 Disable  : in scss case, try to find the css. Never use the precompile.
     *   1 Enable   : in css case, try to find the scss and precompile
     *   2 Complete : force to find the scss and precompile
     * @param array $themes
     *
     * @return bool
     */
    public function compile(
        string $target,
        array  $files,
        bool   $minify,
        int    $fixurl,
        int    $precompile,
        array  $themes = []
    ): bool {
        $rows = $this->getCssFilesAsRows($files);

        if ($this->scssCompiler::isEnabled($precompile)) {
            $this->getScssFiles($rows);
        }

        $result = $this->compileCss($target, $rows, $minify, $fixurl);

        if ($result && $this->scssCompiler::compileThemes($precompile)) {
            $this->compileThemes($target, $rows, $minify, $fixurl, $themes);
        }

        return $result;
    }

    /**
     * Get
     *
     * @param array $files
     *
     * @return array
     */
    protected function getCssFilesAsRows(array $files): array
    {
        $rows = [];
        foreach ($files as $file) {
            if ($this->isCssFile($file)) {
                $rows[] = [
                    'css' => $file,
                    'scss' => null
                ];
            }
        }

        return $rows;
    }

    /**
     * Get
     *
     * @param array &$rows
     *
     * @return void
     */
    protected function getScssFiles(array &$rows): void
    {
        foreach ($rows as $i => $row) {
            $scssFile = $this->getScssFileFromCss($row['css']);

            if ($scssFile) {
                $rows[$i]['scss'] = $scssFile;
            }
        }
    }

    /**
     * Get
     *
     * @param string $file
     *
     * @return bool
     */
    protected function isCssFile(string $file): bool
    {
        return is_file($this->abspath . $file)
            && pathinfo($file, PATHINFO_EXTENSION) == 'css';
    }

    /**
     * Compile css files
     *
     * @param string $target  The file where the compiled file will be saved.
     * @param array  $rows    An array with all the files to compile.
     * @param bool   $minify
     * @param int    $fixurl  Fix the url of the CSS files.
     *
     * @return bool
     */
    protected function compileCss(
        string $target,
        array  $rows,
        bool   $minify,
        int    $fixurl
    ): bool {
        $buffer = '';

        foreach ($rows as $row) {
            if ($row['scss']) {
                $this->scssCompiler->compileFromFile($row['scss'], $row['css']);
            }

            $content = $this->fs->getContent($row['css']);
            $buffer .= $this->urlFixer->fromString($content, $row['css'], $target, $fixurl);
        }

        /* @TODO: Do not delete!!!!!!!!

			$this->cssVarParser ??= new CssVarParser;
			$this->cssVarParser->addString($this->getCssVariables());
			$this->cssVarParser->addString($buffer);
			$buffer = $this->cssVarParser->getCss();
			$variables = $this->cssVarParser->getVariables();
		*/

        $result = $this->fs->putContent($target, $buffer);

        if ($result && $minify) {
            $this->minify($target);
        }

        return $result;
    }

    /**
     * Compile
     *
     * @return bool
     */
    protected function compileThemes(
        string $target,
        array  $rows,
        bool   $minify,
        int    $fixurl,
        array  $themes = []
    ): void {
        $themes = $this->getThemes($themes);

        if (!$themes) {
            return;
        }

        foreach ($themes as $theme) {
            $newTarget  = $this->themes->getThemeTarget($target, $theme['key']);
            $varContent = $this->themes->getScssVarContent($theme['key']);
            $buffer     = '';

            foreach ($rows as $row) {
                $file    = $row['scss'] ?? $row['css'];
                $content = $this->fs->getContent($file);

                if ($row['scss']) {
                    $dirpath = pathinfo($row['scss'], PATHINFO_DIRNAME);
                    $content = $this->scssCompiler
                        ->setImportPaths($dirpath)
                        ->compileFromString($varContent . $content);
                }

                $buffer .= $this->urlFixer->fromString($content, $file, $newTarget, $fixurl);
            }

            $this->fs->putContent($newTarget, $buffer);

            if ($minify) {
                $this->minify($newTarget);
            }
        }
    }

    /**
     * Get
     * 
     * @param array $themes
     *
     * @return array
     */
    protected function getThemes(array $themes): array
    {
        $this->themes ??= new AssetsThemes($this->abspath);

        if ($themes) {
            return $themes;
        }

        return ($this->themesList ??= $this->themes->getAll());
    }

    /**
     * Get
     * 
     * @param string $file
     * 
     * @return ?string
     */
    protected function getScssFileFromCss(string $file): ?string
    {
        $info = pathinfo($file);
        $file = sprintf('%s/sass/%s.scss', dirname($info['dirname']), $info['filename']);

        return is_file($this->abspath . $file)
            ? $file
            : null;
    }

    /**
     * Minify
     *
     * @param string $file
     *
     * @return void
     */
    protected function minify(string $file): void
    {
        $this->minifier ??= Plugin::get('minifier', 'minify', config('assets.cssmin_plugin'));
        $this->minifier?->run($this->abspath . $file);
    }
}
