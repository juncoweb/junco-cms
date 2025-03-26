<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
    $rows['theme']['options'] = (new AssetsThemes)->scanAll();
    $rows['header_color']['options'] = [
        'default' => 'Default',
        'primary' => 'Primary',
        'secondary' => 'Secondary',
        'info' => 'Info',
        'warning' => 'Warning',
        'success' => 'Success',
        'danger' => 'Danger'
    ];
    $rows['mainbar']['plugins'] = 'widget';
    $rows['sidebar']['plugins'] = 'widget';
};
