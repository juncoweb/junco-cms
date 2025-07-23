<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Importer;

use Database;

class MysqlImporter
{
    // vars
    protected $db;
    protected $prefixer;

    /**
     * Constructor
     * 
     * @param Database $db
     */
    public function __construct(?Database $db = null)
    {
        if ($db === null) {
            $db = db();
        }
        $this->db = $db;
        $this->prefixer = $db->getPrefixer();
    }

    /**
     * Import
     *
     * @param string $buffer
     */
    public function import(string $buffer): void
    {
        $queries                = [];
        $i_start                = 0;
        $i_end                    = 0;
        $delimiter_keyword_esc    = ';';
        $delimiter_keyword        = ';';
        $delimiter_length        = 1;
        $quit_comments            = 1;

        do {
            $regexp = '%\'|"|`|#|--(?=\s)|\/\*|DELIMITER |' . $delimiter_keyword_esc . '%';
            $flag = preg_match($regexp, $buffer, $matches, PREG_OFFSET_CAPTURE, $i_end);

            if ($flag) {
                switch ($matches[0][0]) {
                    case '--':
                    case '#':
                    case '/*':
                        $i_end = ($matches[0][0] == '/*')
                            ? strpos($buffer, '*/', $matches[0][1] + 2) + 2
                            : strpos($buffer, "\n", $matches[0][1] + 1) + 1;

                        if ($i_end < $matches[0][1]) {
                            $i_end = strlen($buffer);
                        }

                        if ($quit_comments) {
                            $buffer = substr($buffer, 0, $matches[0][1]) . substr($buffer, $i_end);
                            $i_end    = $matches[0][1];
                        }
                        break;

                    // quotes
                    case '\'':
                    case '"':
                    case '`':
                        $i_end = strpos($buffer, $matches[0][0], $matches[0][1] + 1) + 1;
                        break;

                    /*case '(':
						$i_end = strpos($buffer, ')', $matches[0][1] + 1) + 1;
					break;*/

                    case 'DELIMITER ':
                        preg_match('%DELIMITER ([^\s]+)%', $buffer, $matches_, PREG_OFFSET_CAPTURE, $matches[0][1]);
                        $delimiter_keyword        = $matches_[1][0];
                        $delimiter_keyword_esc    = preg_quote($delimiter_keyword, '%');
                        $delimiter_length        = strlen($delimiter_keyword);
                        $i_start                =                                         // here a security is necessary
                            $i_end                = $matches_[1][1] + $delimiter_length;
                        $quit_comments            = $quit_comments ? 0 : 1;
                        break;

                    case $delimiter_keyword:
                        $query = trim(substr($buffer, $i_start, $matches[0][1] - $i_start));
                        if ($query) {
                            $queries[] = $this->prefixer->replaceWithLocal($query);
                        }

                        $i_start    =
                            $i_end    = $matches[0][1] + $delimiter_length;
                        break;
                }
            }
        } while ($flag);

        $query = trim(substr($buffer, $i_start));
        if ($query) {
            $queries[] = $this->prefixer->replaceWithLocal($query);
        }

        foreach ($queries as $query) {
            $this->db->safeExec($query);
        }
    }
}
