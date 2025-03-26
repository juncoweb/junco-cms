<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;


$elements = [
    /**
     * Input
     */
    'Input' => [
        ['.input-color', '<input type="file" class="input-field input-primary">'],

        ['.input-color', '<input class="input-field" control-felem="color">'],

        ['.input-field', '<select class="input-field"> <option value="">Default select</option></select>'],

        [
            '.input-group',
            '<div class="input-group">'
                .  '<input type="text" class="input-field" placeholder="Input">'
                .  '<span class="btn"><input type="checkbox" class="input-checkbox"></span>'
                . '</div>'
        ],

        [
            '.input-group',
            '<div class="input-group">'
                .  '<span class="btn"><i class="fa-solid fa-user"></i></span>'
                .  '<input type="text" class="input-field" placeholder="Input">'
                .  '<span class="btn"><i class="fa">@</i></span>'
                . '</div>'
        ],

        [
            '.input-group',
            '<div class="input-group">'
                .  '<span class="btn"><i class="fa-solid fa-user"></i></span>'
                .  '<input type="text" class="input-field" placeholder="Input 1">'
                .  '<input type="text" class="input-field" placeholder="Input 2">'
                . '</div>'
        ],

        [
            '.input-group',
            '<div class="input-group">'
                .  '<span class="btn"><i class="fa-solid fa-user"></i></span>'
                .  '<textarea class="input-field" placeholder="Textarea" control-felem="auto-grow" rows="1"></textarea>'
                . '</div>'
        ],

        [
            '.input-icon',
            '<div class="input-icon-group">'
                . '<input class="input-field" placeholder="Search...">'
                . '<button class="input-icon" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>'
                . '</div>'
        ],

        [
            '.input-icon',
            '<div class="input-icon-group">'
                . '<input class="input-field" placeholder="Search...">'
                . '<div class="input-icon"><i class="fa-solid fa-user"></i></div>'
                . '</div>'
        ],

        ['.input-icon .input-primary .input-small', '<div class="input-icon-group input-primary input-small">'
            . '<span class="input-icon"><i class="fa-solid fa-user"></i></span>'
            . '<input class="input-field" placeholder="Search...">'
            . '</div>'],

        ['.input-icon .input-large', '<div class="input-icon-group input-large">'
            . '<span class="input-icon"><i class="fa-solid fa-user"></i></span>'
            . '<input class="input-field" placeholder="Search...">'
            . '</div>'],

    ],
];

// print
$html = '';
foreach ($elements as $title => $rows) {
    //$html .= '<h2>' . $title . '</h2>';
    $html .= '<table class="test-table">';
    foreach ($rows as $row) {
        $html .= '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td width="30%">' . htmlentities($row[1]) . '</td></tr>';
    }
    $html .= '</table>';
}

// template
$tpl = Template::get();
$tpl->options([
    'domready' => "JsFelem.load('#content')",
    'thirdbar' => 'form.thirdbar'
]);
$tpl->title('Form Elements');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
