<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Assets\Compilation;

use ScssPhp\ScssPhp\Compiler;

require_once SYSTEM_ABSPATH . 'cms/libraries/scssphp/scss.inc.php';

class ScssCompiler
{
    // const
    const DISABLE    = -1;
    const ENABLE     = 1;
    const ENABLE_ALL = 2;

    // vars
    protected string   $abspath;
    protected Compiler $compiler;

    /**
     * Constructor
     */
    public function __construct(?string $abspath = null)
    {
        $this->abspath = $abspath ?? SYSTEM_ABSPATH;
        $this->compiler = new Compiler();
    }

    /**
     * Set Import Paths
     * 
     * @param string $path
     */
    public function setImportPaths(string $path): self
    {
        $this->compiler->setImportPaths($this->abspath . $path);
        return $this;
    }

    /**
     * Compile From String
     * 
     * @param string $input  The stylesheet content.
     */
    public function compileFromString(string $input): string
    {
        return $this->compiler->compileString($input)->getCss();
    }

    /**
     * Compile From File
     * 
     * @param string $scssFile  The stylesheet file path.
     * 
     * @return string The stylesheet content.
     */
    public function compileFromFile(string $scssFile, string $cssFile = ''): string
    {
        $this->setImportPaths(dirname($scssFile));

        $content = $this->getContent($scssFile);
        $content = $this->compiler->compileString($content)->getCss();

        if ($cssFile) {
            $this->putContent($cssFile, $content);
        }

        return $content;
    }

    /**
     * Get content
     * 
     * @param string $file
     * 
     * @return string
     */
    protected function getContent(string $file): string
    {
        return file_get_contents($this->abspath . $file) ?: '';
    }

    /**
     * Put content
     * 
     * @param string $file
     * @param string $data
     * 
     * @return bool
     */
    protected function putContent(string $file, string $data): bool
    {
        return false !== file_put_contents($this->abspath . $file, $data);
    }

    /**
     * Get
     */
    public static function getOptions(): array
    {
        return [
            self::DISABLE    => _t('Disable'),
            self::ENABLE     => _t('Enable'),
            self::ENABLE_ALL => _t('Enable') . ' + ' . _t('Themes')
        ];
    }

    /**
     * Returns true if the option passed enables URL fixing.
     * 
     * @param int $option
     */
    public static function isEnabled(int $option): bool
    {
        return in_array($option, [
            self::ENABLE,
            self::ENABLE_ALL
        ]);
    }

    /**
     * Get
     */
    public static function compileThemes(int $value): bool
    {
        return self::ENABLE_ALL === $value;
    }
}
