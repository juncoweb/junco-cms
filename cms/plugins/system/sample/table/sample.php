<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

/**
 * 
 */
function create_table($head = 1, $rows = 5, $cols = 5)
{
    $html = '';

    if ($head == 1) {
        --$rows;
        for ($i = 0; $i < $cols; $i++) {
            $html .= '<th>Header ' . $i . '</th>';
        }
        $html = '<thead><tr>' . $html . '</tr></thead>';
    }

    $html .= '<tbody>';
    for ($i = 0; $i < $rows; $i++) {
        $html .= '<tr>';
        for ($j = 0; $j < $cols; $j++) {
            if ($head == 2 && !$j) {
                $html .= '<th>Header ' . $i . '</th>';
            } else {
                $html .= '<td>' . $i . ' - ' . $j . '</td>';
            }
        }
        $html .= '</tr>';
    }
    $html .= '</tbody>';

    return $html;
}

$html = '';
$rows = [
    ['Striped / Highlight', '<table class="table table-striped table-highlight">' . create_table() . '</table>'],
    ['Bordered / Condensed', '<table class="table table-bordered table-condensed">' . create_table() . '</table>'],
    ['Horizontal th', '<table class="table table-bordered">' . create_table(2) . '</table>'],
];

foreach ($rows as $row) {
    $html .= '<div class="panel mb-4">'
        . '<div class="panel-header"><h2>' . $row[0] . '</h2></div>'
        . '<div class="panel-body">' . $row[1] . '</div></div>';
}

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'system.thirdbar']);
$tpl->title('Table');
$tpl->content($html);

return $tpl->response();
