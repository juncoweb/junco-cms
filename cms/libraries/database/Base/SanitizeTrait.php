<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base;

trait SanitizeTrait
{
    /**
     * Sanitize
     * 
     * @param string $query
     * 
     * @return void
     */
    protected function sanitizeQuery(string &$query): void
    {
        // Remove backticks from query (MySQL-style identifiers)
        $query = str_replace('`', '', $query);

        // Convert MySQL-style LIMIT offset, count to PostgreSQL-style LIMIT count OFFSET offset
        $query = preg_replace_callback('/(?<Limit>LIMIT)\s+(?<Value>\d+)\s*,\s*(?<Offset>\d+)/', function ($match) {
            if (isset($match['Limit'])) {
                return "LIMIT $match[Offset] OFFSET $match[Value]";
            }
        }, $query);
        //die($query);
    }

    /**
     * Sanitize
     * 
     * @param string $query
     * 
     * @return void
     */
    protected function sanitizeQueryToNativePg(string &$query): void
    {
        $this->sanitizeQuery($query);

        $count = 0;
        $query = preg_replace_callback('/(?<Holder>\?)/', function ($match) use (&$count) {
            if (isset($match['Holder'])) {
                return '$' . (++$count);
            }
        }, $query);
        //die($query);
    }
}
