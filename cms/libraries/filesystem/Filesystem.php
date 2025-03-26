<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Filesystem\GitIgnore;

class Filesystem
{
    // const

    // vars
    private string $basepath;
    private int    $mode;

    /**
     * Constructor
     *
     * @param string $basepath
     */
    public function __construct(?string $basepath = null)
    {
        $this->basepath = $basepath ?? SYSTEM_ABSPATH;
        $this->mode     = config('system.mkdir_mode');
    }

    /**
     * Set mode
     *
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Scan directory
     *
     * @param string $dir
     * 
     * @return array
     */
    public function scandir(string $dir = '', array $ignore = []): array
    {
        if (is_dir($this->basepath . $dir)) {
            $cdir = scandir($this->basepath . $dir);

            if ($cdir !== false) {
                $cdir = array_diff($cdir, ['.', '..', ...$ignore]);

                if ($cdir) {
                    return $cdir;
                }
            }
        }

        return [];
    }

    /**
     * Make directory.
     *
     * @param string $dir
     * @param ?int   $mode
     *
     * @return bool
     */
    public function mkdir(string $dir, ?int $mode = null): bool
    {
        return is_dir($this->basepath . $dir)
            or mkdir($this->basepath . $dir, $mode ?? $this->mode, true);
    }

    /**
     * Returns the file contents.
     * 
     * @param string $file
     * 
     * @return string
     */
    public function getContent(string $file): string
    {
        return file_get_contents($this->basepath . $file) ?: '';
    }

    /**
     * Write the contents of a file.
     *
     * @param string    $file
     * @param mixed     $data
     * @param int       $flags
     * @param ?resource $context
     * 
     * @return bool
     */
    public function putContent(string $file, mixed $data = '', int $flags = 0, $context = null): bool
    {
        return $this->mkdir(dirname($file))
            and (false !== file_put_contents($this->basepath . $file, $data, $flags, $context));
    }

    /**
     * Copy file or directory.
     *
     * @param string $from
     * @param string $to
     *
     * @return bool
     */
    public function copy(string $from, string $to): bool
    {
        if (is_dir($this->basepath . $from)) {
            if (!$this->mkdir($to)) {
                return false;
            }

            if ($nodes = $this->scandir($from)) {
                $this->sanitizeDir($from);
                $this->sanitizeDir($to);

                foreach ($nodes as $node) {
                    $this->copy($from . $node, $to . $node);
                }
            }

            return true;
        }

        return $this->mkdir(dirname($to))
            and copy($this->basepath . $from, $this->basepath . $to);
    }

    /**
     * Copy dir width .gitignore rules
     * 
     * @param string $from
     * @param string $to
     * 
     * @return void
     */
    public function copyDirWithIgnores(string $from, string $to, ?GitIgnore $gitignore = null): void
    {
        if (is_file($from . '.gitignore')) {
            $gitignore = (new GitIgnore($from, $gitignore))->setRulesFromFile();
        }

        $nodes = $this->scandir($from, ['.gitignore']);

        foreach ($nodes as $node) {
            if ($gitignore?->isIgnored($from . $node) ?? false) {
                continue;
            }

            if (is_dir($from . $node)) {
                $node .= '/';
                $this->copyDirWithIgnores($from . $node, $to . $node, $gitignore);
            } else {
                $this->copy($from . $node, $to . $node);
            }
        }
    }

    /**
     * Rename
     *
     * @param string $from
     * @param string $to
     * 
     * @return bool
     */
    public function rename(string $from, string $to): bool
    {
        if (is_dir($this->basepath . $from)) {
            if (is_dir($this->basepath . $to)) {
                if ($from == $to) {
                    return true;
                }

                return $this->copy($from, $to)
                    and $this->remove($from);
            }

            return rename($this->basepath . $from, $this->basepath . $to);
        }

        return is_file($this->basepath . $from)
            and rename($this->basepath . $from, $this->basepath . $to);
    }

    /**
     * Remove file or directory.
     *
     * @param string $node
     *
     * @return bool
     */
    public function remove(string $node): bool
    {
        if (is_file($this->basepath . $node)) {
            return unlink($this->basepath . $node);
        } elseif (is_dir($this->basepath . $node)) {
            $this->sanitizeDir($node);

            foreach ($this->scandir($node) as $has) {
                $this->remove($node . $has);
            }

            return rmdir($this->basepath . $node);
        }

        return false;
    }

    /**
     * Remove directory if is empty
     *
     * @param string $dir
     * 
     * @return bool
     */
    public function removeDirIfEmpty(string $dir, array $ignore = []): bool
    {
        if (!is_dir($this->basepath . $dir)) {
            return false;
        }

        $this->sanitizeDir($dir);
        $remove = true;

        foreach ($this->scandir($dir, $ignore) as $node) {
            if (is_dir($this->basepath . $dir . $node)) {
                if (!$this->removeDirIfEmpty($dir . $node, $ignore)) {
                    $remove = false;
                }
            } else {
                $remove = false;
            }
        }

        return $remove
            ? $this->remove($dir)
            : false;
    }

    /**
     * Permissions
     *
     * @param string $node
     * @param int    $mode
     * @param int    $option
     *  0: do not include subfolders
     *  1: include only subfolders
     *  2: include subfolders and sub-files
     *  3: only files and sub-files
     *
     * @return bool
     */
    public function chmod(string $node, int $mode, int $option = 0): bool
    {
        $is_dir = is_dir($this->basepath . $node);
        $result = ($option == 3 && $is_dir)
            ? true
            : chmod($this->basepath . $node, $mode);

        // recursive options
        if (
            $result
            && $is_dir
            && $option > 0
            && $option < 4
        ) {
            if ($cdir = $this->scandir($node)) {
                $this->sanitizeDir($node);

                foreach ($cdir as $has) {
                    if (is_dir($this->basepath . $node . $has)) {
                        if (!$this->chmod($node . $has, $mode, $option)) {
                            return false;
                        }
                    } elseif ($option > 1) {
                        if (!chmod($this->basepath . $node . $has, $mode)) {
                            return false;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Sanitize directory separator.
     *
     * @param string $dir
     * 
     * @return void
     */
    public function sanitizeDir(string &$dir, string $separator = DIRECTORY_SEPARATOR): void
    {
        $dir = rtrim($dir, '\\/');

        if ($dir) {
            $dir .= $separator;
        }
    }
}
