<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;


$elements = [
    /**
     * Button
     */
    'Button' => [
        [
            '.btn-group',
            '<div class="btn-group"><a href="javascript:void(0)" class="btn">Button 1</a><a href="javascript:void(0)" class="btn">Button 2</a></div>'
        ],
        [
            '.btn-large',
            '<div class="btn-group btn-large"><a href="javascript:void(0)" class="btn">Button 1</a><a href="javascript:void(0)" class="btn">Button 2</a></div>'
        ],
        [
            '.btn-small',
            '<div class="btn-group btn-small"><a href="javascript:void(0)" class="btn">Button 1</a><a href="javascript:void(0)" class="btn">Button 2</a></div>'
        ],
        [
            '.btn-group',
            '<div class="btn-group btn-small">'
                . '<a href="javascript:void(0)" class="btn">Button</a>'
                . '<div class="btn-group">'
                . '<button control-felem="select" class="btn dropdown-toggle">All classes</button>'
                . '<div class="dropdown-menu" style="display: none;"><input type="hidden" name="class" value="0"><ul><li data-select-value="0" class="selected"><a href="javascript:void(0)"><i></i>All classes</a></li><li data-select-value="1"><a href="javascript:void(0)"><i></i>Administrator</a></li><li data-select-value="15"><a href="javascript:void(0)"><i></i>Default</a></li></ul></div>'
                . '</div>'
                //. '<a href="javascript:void(0)" class="btn">Button</a>'
                . '</div>'
        ],
        [
            '.btn-group (2)',
            '<div class="btn-group" control-felem="select">'
                .     '<input class="btn"/>'
                .     '<button type="submit" class="btn" data-select-label>All classes</button>'
                .     '<button class="btn dropdown-toggle"></button>'
                .     '<div class="dropdown-menu" style="display: none;"><input type="hidden" name="class" value="0"><ul><li data-select-value="0" class="selected"><a href="javascript:void(0)"><i></i>All classes</a></li><li data-select-value="1"><a href="javascript:void(0)"><i></i>Administrator</a></li><li data-select-value="15"><a href="javascript:void(0)"><i></i>Default</a></li></ul></div>'
                .   '</div>'
        ],
        [
            '.btn-press (simple)',
            '<div class="btn-group"><label class="btn btn-press" control-felem="press">Button 1<input type="checkbox" /></label></div>'
        ],
        [
            '.btn-press (multiple)',
            '<div class="btn-group"><label class="btn btn-press" control-felem="press">Button 1<input type="radio" name="test" /></label><label class="btn btn-press" control-felem="press">Button 2<input type="radio" name="test" /></label></div>'
        ],
    ],
];



// print
$html = '';
foreach ($elements as $title => $rows) {
    //$html .= '<h2>' . $title . '</h2>';
    $html .= '<table class="test-table">';
    foreach ($rows as $row) {
        $html .= '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' . htmlentities($row[1]) . '</td></tr>';
    }
    $html .= '</table>';
}

// template
$tpl = Template::get();
$tpl->options([
    'domready' => "JsFelem.load('#content')",
    'thirdbar' => 'form.thirdbar'
]);
$tpl->title('Button group');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
