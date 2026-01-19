<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Nestedset
{
    /**
     * Convert a simple array into multidimensional.
     *
     * @param array $rows  It can be a simple array or a result of the database
     */
    public static function toNestedArrays(array $rows): array
    {
        if (!$rows) {
            return [];
        }

        // vars
        $partial  = [[]];
        $depth    = 0;
        $right_id = [];

        foreach ($rows as $row) {
            $row['depth']      = $depth;
            $partial[$depth][] = $row;

            if (($row['right_id'] - $row['left_id']) > 1) { // the node nests children
                $right_id[$depth] = $row['right_id'];        // I store the value
                $partial[++$depth] = [];                    // Initialize

            } elseif ($depth > 0 && $row['right_id'] == ($right_id[$depth - 1] - 1)) {
                $right_id[$depth] = $row['right_id'];         // store value

                while ($depth > 0 && $right_id[$depth] == ($right_id[$depth - 1] - 1)) { // I start to nest the partial
                    $i = count($partial[$depth - 1]) - 1;
                    $partial[$depth - 1][$i]['edge'] = $partial[$depth];
                    $depth--;
                }
            }
        }

        return $partial[$depth];
    }

    /**
     * Convert a simple array into multidimensional from its depth
     *
     * @param array $rows  It can be a simple array or a result of the database.
     * 
     * @return array
     */
    public static function toNestedArraysFromDepth(array $rows): array
    {
        if (!$rows) {
            return [];
        }

        $total   = count($rows);
        $depth   = $rows[0]['depth'];
        $partial = [[]];
        $a       = 0;

        array_push($rows, ['depth' => $depth]);

        foreach ($rows as $row) {
            if ($row['depth'] > $depth) { // this is a child
                $depth++;
                $partial[$depth] = [];
            } elseif ($row['depth'] < $depth) { // parents nesting
                while ($row['depth'] < $depth) {
                    $i = count($partial[$depth - 1]) - 1;
                    $partial[$depth - 1][$i]['edge'] = $partial[$depth];
                    $depth--;
                }
            }

            if (($a++) < $total) {
                $partial[$depth][] = $row;
            }
        }

        return $partial[$depth];
    }

    /**
     * Convert a multidimensional array into simple
     *
     * @param array $rows    The input array
     * 
     * @return array
     */
    public static function toList(array $rows): array
    {
        $output = [];

        if ($rows) {
            foreach ($rows as $row) {
                $output[] = $row;
                if (!empty($row['edge'])) {
                    $output = array_merge($output, self::toList($row['edge']));
                }
            }
        }

        return $output;
    }

    /**
     * Convert a multidimensional array into simple
     *
     * @param array $rows    The input array
     * 
     * @return array
     */
    public static function toListWithUpDown(array $rows): array
    {
        if ($rows) {
            $start        = min(array_column($rows, 'left_id'));
            $end        = max(array_column($rows, 'right_id'));
            $left_id    = [];
            $right_id    = [];

            foreach ($rows as $i => $row) {
                $rows[$i]['up']        = $row['left_id'] != $start;
                $rows[$i]['down']    = $row['right_id'] != $end;
                $has_children        = ($row['right_id'] - $row['left_id']) > 1;

                if ($has_children) {
                    $right_id[] = $row['right_id'] - 1;
                    $left_id[]  = $row['left_id'] + 1;
                }
                if (in_array($row['right_id'], $right_id)) {
                    $rows[$i]['down'] = false;
                }
                if (in_array($row['left_id'], $left_id)) {
                    $rows[$i]['up'] = false;
                }
            }
        }

        return $rows;
    }

    /**
     * Nested arrays sort through the field "ordering"
     *
     * @param array $rows
     * 
     * @return array
     */
    public static function sort(array $rows, string $column_name = 'ordering'): array
    {
        if ($rows) {
            if (isset($rows[0]['depth'])) {
                $rows = self::toNestedArraysFromDepth($rows);
            } else {
                $rows = self::toNestedArrays($rows);
            }

            $rows = self::toList(self::sortNestedArrays($rows, $column_name));
        }

        return $rows;
    }

    /**
     * Nested arrays sort through the field "ordering"
     *
     * @param array $rows
     * @param string $column_name
     * 
     * @return array
     */
    public static function sortNestedArrays(array $rows, string $column_name = 'ordering'): array
    {
        usort($rows, function ($a, $b) use ($column_name) {
            if ($a[$column_name] == $b[$column_name]) {
                return 0;
            }

            return ($a[$column_name] < $b[$column_name]) ? -1 : 1;
        });

        foreach ($rows as $i => $row) {
            if (!empty($row['edge'])) {
                $rows[$i]['edge'] = self::sortNestedArrays($row['edge'], $column_name);
            }
        }

        return $rows;
    }
}
