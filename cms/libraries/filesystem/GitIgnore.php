<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

class GitIgnore
{
    protected string $dirpath;
    protected string $dirpath_p;
    protected ?self  $previous;
    protected bool   $ignore_all    = false;
    protected array  $ignore        = [];
    protected array  $not_ignore    = [];

    /**
     * Read
     * 
     * @param string $dir
     * 
     * @return void
     */
    public function __construct(string $dirpath, ?self $previous = null)
    {
        $this->dirpath   = rtrim($dirpath, '\/');
        $this->dirpath_p = preg_quote($this->dirpath) . '[\\\\/]';
        $this->previous  = $previous;
    }

    /**
     * Set
     * 
     * @param string $file
     * 
     * @return self
     */
    public function setRulesFromFile(string $file = ''): self
    {
        $file = $file
            ? '/' . pathinfo($file, PATHINFO_BASENAME)
            : '/.gitignore';

        return $this->setRules(
            file_get_contents($this->dirpath . $file)
        );
    }

    /**
     * Set
     * 
     * @param array|string $rules
     * 
     * @return self
     */
    public function setRules(array|string $rules): self
    {
        if (!is_array($rules)) {
            $rules = $this->explode($rules);
        }

        foreach ($rules as $rule) {
            $rule = trim($rule);

            if (!$rule || $rule[0] == '#') {
                continue;
            }

            if ($rule == '*') {
                $this->ignore_all = true;
            } else {
                if ($rule[0] == '!') {
                    $rule = ltrim(substr($rule, 1));
                    $action = 'not_ignore';
                } else {
                    $action = 'ignore';
                }

                $this->{$action}[] = $this->getPattern($rule);
            }
        }

        return $this;
    }

    /**
     * Explode the rules in plain text.
     * 
     * @param array|string $rules
     * 
     * @return array
     */
    protected function explode(string $rules): array
    {
        $rules = str_replace(["\r\n", "\r"], "\n", $rules); // normalize

        return explode("\n", $rules);
    }

    /**
     * Get
     * 
     * @param string $rule
     * 
     * @return string
     */
    protected function getPattern(string $rule): string
    {
        if (pathinfo($rule, PATHINFO_EXTENSION)) {
            return $this->getFilePattern($rule);
        }

        if (
            false !== strpos($rule, '/')
            || false !== strpos($rule, '*')
        ) {
            return $this->getFolderPattern($rule);
        }

        return $this->getNamePattern($rule);
    }

    /**
     * Returns a pattern for file rules.
     * 
     * @return string
     */
    protected function getFilePattern(string $rule): string
    {
        // /name.file
        // lib/name.file
        // **/lib/name.file
        $subfolders = '';

        // *.file
        // name.file
        if (false === strpos($rule, '/')) {
            $subfolders = '([^\/]+[\\\\/])*';
        }

        return '#^' . $this->dirpath_p . $subfolders . $this->sanitizeRulePattern($rule) . '$#';
    }

    /**
     * Returns a pattern for folder rules.
     * 
     * @return string
     */
    protected function getFolderPattern(string $rule): string
    {
        // name/
        // **/name
        // /lib/**/name
        // *name/
        return '#^' . $this->dirpath_p . $this->sanitizeRulePattern($rule) . '([\\\\/]|$)#';
    }

    /**
     * Returns a pattern for the rule "name"
     * 
     * @return string
     */
    protected function getNamePattern(string $rule): string
    {
        $subfolders = "([^\/]+[\\\\/])*";
        $folder = "([\\\\/]|$)";
        $file = "(\.[^.]+$)";

        return '#^' . $this->dirpath_p . "{$subfolders}{$rule}($folder|$file)#";
    }

    /**
     * Prepare
     * 
     * @return string
     */
    protected function sanitizeRulePattern(string $rule, string $trim = ''): string
    {
        if ($trim === '') {
            $rule = trim($rule, '\/');
        } elseif ($trim === 'l') {
            $rule = ltrim($rule, '\/');
        }

        return strtr($rule, [
            '**/' => '([^/]+[\\\\/])*',
            '*' => '[^/]*',
            '/' => '[\\\\/]',
            '.' => '\.',
            '?' => '.',
            '[!' => '[^',
        ]);
    }

    /**
     * Returns true if the file should be ignored
     * 
     * @param string $node
     * 
     * @return bool
     */
    public function isIgnored(string $node): bool
    {
        $result = $this->previous?->isIgnored($node) ?? false;

        if (!$result && $this->shouldBeIgnored($node)) {
            $result = true;
        }

        if ($result && $this->shouldNotBeIgnored($node)) {
            $result = false;
        }

        return $result;
    }

    /**
     * Is ignored
     * 
     * @param string $node
     * 
     * @return bool
     */
    protected function shouldBeIgnored(string $node): bool
    {
        if ($this->ignore_all) {
            return true;
        }

        foreach ($this->ignore as $pattern) {
            if (preg_match($pattern, $node)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check that the node should not be ignored.
     * 
     * @param string $node
     * 
     * @return bool
     */
    protected function shouldNotBeIgnored(string $node): bool
    {
        foreach ($this->not_ignore as $pattern) {
            if (preg_match($pattern, $node)) {
                return true;
            }
        }

        return false;
    }
}
