<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Assets\Compilation;

use Exception;

class CssVarParser
{
    // vars
    protected string $abspath;
    protected string $basepath  = '';
    protected array  $variables = [];
    protected int    $offset    = 0;
    protected string $content   = '';
    protected string $buffer    = '';

    /**
     * Constructor
     */
    public function __construct(?string $abspath = null)
    {
        $this->abspath = $abspath ?? SYSTEM_ABSPATH;
    }

    /**
     * String
     * 
     * @param string $content  The stylesheet content.
     */
    public function addString(string $content)
    {
        $this->content = $this->clean($content);
        $start = 0;

        while ($token = $this->getNextMatch('/;|{/')) {
            switch ($token) {
                case '{':
                    if ($this->hasVariables()) {
                        $scope = trim($this->getContentUpToHereFrom($start));
                        $this->find($scope);
                        $start = $this->offset;
                        break;
                    }
                    break 2;

                case ';':
                    $this->offset++;
                    $start = $this->offset;
                    break;
            }
        }

        $this->buffer .= $this->replace($this->content);
    }

    /**
     * File
     * 
     * @param string $file  The stylesheet file path.
     * 
     * @return string The stylesheet
     */
    public function addFile(string $file): void
    {
        $content = $this->getContent($file);

        if ($content) {
            $this->addString($content);
        }
    }

    /**
     * Get
     */
    public function getCss(): string
    {
        return $this->buffer;
    }

    /**
     * Get
     */
    public function getVariables(): string
    {
        $output = [];
        foreach ($this->variables as $scope => $variables) {
            $output[] = $scope . '{' . implode(';', $variables) . '}';
        }

        return implode(' ', $output);
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
     * Clean
     *
     * @param string $content
     * 
     * @return string
     */
    protected function clean(string $content): string
    {
        return preg_replace(['/\/\*(.*?)\*\/|[\r\n\t]/s', '/\s{2,}/'], ' ', $content);
    }

    /**
     * File
     * 
     * @param string $file  The stylesheet file path.
     * 
     * @return bool
     */
    public function hasVariables(): bool
    {
        return false !== strpos($this->content, '--', $this->offset);
    }

    /**
     * File
     * 
     * @param string $file  The stylesheet file path.
     * 
     * @return bool
     */
    public function getNextMatch(string $pattern): ?string
    {
        if (!preg_match($pattern, $this->content, $match, PREG_OFFSET_CAPTURE, $this->offset)) {
            return null;
        }

        $match = $match[1] ?? $match[0];
        $this->offset = $match[1];

        return $match[0];
    }

    /**
     * File
     * 
     * @param string $file  The stylesheet file path.
     * 
     * @return string The stylesheet
     */
    public function find(string $scope)
    {
        $this->offset++;
        $braces = 0;
        $start = 0;

        while ($token = $this->getNextMatch('/[^(]\s*(--)|\{|\}/')) {
            switch ($token) {
                case  '{':
                    $braces++;
                    $this->offset++;
                    break;

                case  '}':
                    $this->offset++;
                    if (!$braces) {
                        return;
                    }
                    $braces--;
                    break;

                case '--':
                    $start = $this->offset;
                    $this->offset += 2;

                    $token = $this->getNextMatch('/;|\}/');
                    $start = $this->addVariable($scope, $start);
                    $this->offset++;

                    if ($token === '}') {
                        return;
                    }

                    break;
            }
        }
    }

    /**
     * 
     */
    protected function addVariable(string $scope, int $start): int
    {
        $this->variables[$scope][] = $this->getContentUpToHereFrom($start);
        return 0;
    }

    /**
     * 
     */
    protected function getContentUpToHereFrom(int $start): string
    {
        return substr($this->content, $start, $this->offset - $start);
    }

    /**
     * Error
     * 
     * @param string $expecting    The expected character
     * 
     * @throws Exception
     */
    protected function error(string $expecting)
    {
        $unexpected = $this->getNextString();
        $content    = substr($this->content, 0, $this->offset);
        $lines      = preg_split('/\n/', $content);
        $line       = count($lines);
        $position   = strlen($lines[$line - 1]);
        $replaces   = [
            "\s" => '\s',
            "\r" => '\r',
            "\n" => '\n',
            "\t" => '\t',
        ];

        throw new Exception(sprintf(
            'ParseError: syntax error, unexpected token "%s", expecting "%s" in line %d, position %d',
            strtr($unexpected, $replaces),
            $expecting,
            $line,
            $position
        ));
    }

    /**
     * Get
     * 
     * @param int $length
     * 
     * @return string
     */
    protected function getNextPosition(string $token): int
    {
        $len = strpos($this->content, $token, $this->offset);

        if ($len === false) {
            $this->error($token);
        };

        return $len;
    }

    /**
     * Get
     * 
     * @param int $length
     * 
     * @return string
     */
    protected function getNextString(int $length = 1): string
    {
        return substr($this->content, $this->offset, $length) ?: '';
    }

    /**
     * Replace
     * 
     * @param string $content
     * 
     * @return string
     */
    protected function replace(string $content): string
    {
        $replaces = [];
        foreach ($this->variables as $scope) {
            foreach ($scope as $var) {
                $replaces[] = $var;
            }
        }

        $content = str_replace($replaces, '', $content);
        $content = preg_replace(['/(?:;(?:\s|\t|\n|\r)*)+/', '/{\s*;\s*/'], [';', '{'], $content);

        return $content;
    }
}
