<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Assets\Compilation;

use Exception;

class Minifier
{
    // vars
    protected $files = [];

    /**
     * Add one file.
     *
     * @param string $file
     */
    public function add(string $file)
    {
        $this->files[] = $file;
    }

    /**
     * Minify the added files.
     *
     * @param string $target
     */
    public function minify(string $target = '')
    {
        $output = '';

        if ($this->files) {
            $type = pathinfo($target ?: $this->files[0], PATHINFO_EXTENSION);

            foreach ($this->files as $file) {
                $input = file_get_contents($file);

                if (false === $input) {
                    throw new Exception('The minifier could not read the file.');
                }

                // security
                if ($type != pathinfo($file, PATHINFO_EXTENSION)) {
                    throw new Exception('The minifier has failed.');
                }

                switch ($type) {
                    case 'css':
                        $output .= $this->css($input);
                        break;

                    case 'js':
                        $output .= $this->js($input);
                        break;
                }
            }

            $this->files = [];
        }

        if (!$target) {
            return $output;
        }

        if (false === file_put_contents($target, $output)) {
            throw new Exception('The minifier could not write the file.');
        }
    }

    /**
     * Make the styles minification.
     *
     * @param string $input
     * 
     * @return string
     */
    public function css(string $input): string
    {
        return preg_replace([
            // I replace comments EOL TAB with space
            '#\/\*(.*?)\*\/|[\r\n\t]#s',
            // I remove contiguous spaces
            '#\s*([\{\}>~:;,]|\!important)\s*#',
            // ltrim | remove multiples spaces
            '#^\s*|(\s)\s+#',
            // I remove the semicolon before the parenthesis
            '#;}#',
        ], [
            " ",
            "$1",
            "$1",
            "}",
        ], $input);
    }

    /**
     * Make the minification of the javascript.
     *
     * @param string $input
     */
    public function js(string $input)
    {
        // strip BOM
        if (substr($input, 0, 3) == "\xef\xbb\xbf") {
            $input = substr($input, 3);
        }

        $output   = str_replace(["\r\n", "\r"], "\n", $input); // normalize
        $i        = 0;
        $replaces = [];
        $pattern  = '#'
            // replace string with key
            . '(["\'`])(?:\\\\.|[^\\\\])*?\1' . '|'
            // replace /* ... */ with white-spaces
            . '(?s:\/\*.*?\*\/)' . '|'
            // replace regex with key
            . '(?<=[=:.\(&!|,])(?:\s*)(\/[^*\/](?:\\\\.|[^\\\\])*?\/[gimuy]*)' . '|'
            // replace // ... with \n
            . '\/\/.*?([\n]|$)'
            . '#';

        $output = preg_replace_callback($pattern, function ($match) use (&$i, &$replaces) {
            if (isset($match[3])) {
                // replace single line comment (two slashes)
                return "\n";
            } elseif (isset($match[2])) {
                // replace regex
                $k = '"' . (++$i) . '"';
                $replaces[$k] = $match[2];

                return $k;
            } elseif (isset($match[1])) {
                // replace string
                if ($match[0] == $match[1] . $match[1]) {
                    return $match[0];
                }

                $k = $match[1] . (++$i) . $match[1];
                $replaces[$k] = $match[0];
                return $k;
            } else {
                // replace multi-line comment
                return ' ';
            }
        }, $output);

        if (null === $output) {
            throw new Exception('The minifier has failed.');
        }

        $output = preg_replace([
            // It replaces all other control characters (including tab) with spaces
            '#[^\n \S]+#',
            // the multiple spaces and linefeeds are removed.
            '#\s*\n+\s*#',
            '# {2,}#',
            // It omits spaces except when a space is preceded and followed
            // by a non-ASCII character or by an ASCII letter or digit,
            // or by one of these characters: \ $ _
            '# (?![\w\\\\$]|[^\x20-\x7F])#',
            '#(?<![\w\\\\$]|[^\x20-\x7F]) #',
            // A linefeed is not omitted if it precedes a non-ASCII character or an ASCII
            // letter or digit or one of these characters: \ $ _ { [ ( + -
            '#\n(?![\w\\\\${[(+-]|[^\x20-\x7F])#',
            // and if it follows a non-ASCII character or an ASCII letter or digit or one
            // of these characters: \ $ _ } ] ) + - " '
            '#(?<![\w\\\\$}\])+\-"\']|[^\x20-\x7F])\n#',
        ], [
            ' ',
            "\n",
            ' ',
            '',
        ], $output);

        if (null === $output) {
            throw new Exception('The minifier has failed.');
        }

        return strtr($output, $replaces);
    }
}
