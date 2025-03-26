<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
    if ($rows['delivery']['value']) {
        $rows['delivery']['value'] = implode('|', array_map(function ($value) {
            return is_array($value) ? implode('+', $value) : $value;
        }, $rows['delivery']['value']));
    }

    if ($rows['delivery']['default_value']) {
        $rows['delivery']['default_value'] = implode('|', array_map(function ($value) {
            return is_array($value) ? implode('+', $value) : $value;
        }, $rows['delivery']['default_value']));
    }

    $rows['links']['options'] = ['title', 'color', 'icon', 'url'];
};
