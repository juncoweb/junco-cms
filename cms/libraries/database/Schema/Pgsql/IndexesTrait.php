<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

trait IndexesTrait
{
    /**
     * Get Index Columns
     * 
     * @param array  $Columns
     * 
     * @return string
     */
    protected function getIndexColumnsStatement(array $Columns): string
    {
        foreach ($Columns as $i => $Column) {
            if (is_array($Column)) {
                $Column = "`" . $Column['Column_name'] . "`";

                if (!empty($Column['Sub_part'])) {
                    $Column .= "(" . $Column['Sub_part'] . ")";
                };
            } else {
                $Column = "`" . $Column . "`";
            }
            $Columns[$i] = $Column;
        }

        return implode(", ", $Columns);
    }
}
