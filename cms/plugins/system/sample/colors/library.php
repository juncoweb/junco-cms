<?php


function create_palette(string $type): string
{
    $tag1 = '<div>'
        . '<div class="p-2 bold">disabled-bg</div>'
        . '<div class="flex">'
        .   '<div class="p-2" $disabled-font>disabled-font</div>'
        .   '<div class="p-2" $subtle-font>subtle-font</div>'
        . '</div>'
        . '</div>';

    $tag2 = '<div>'
        . '<div class="p-2 bold">bg</div>'
        . '<div class="flex">'
        .   '<div class="p-2" $subtle-font>subtle-font</div>'
        .   '<div class="p-2" $font>font</div>'
        .   '<div class="p-2" $heading>heading</div>'
        . '</div>'
        . '</div>';
    $tag3 = '<div>'
        . '<div class="p-2 bold">active-bg</div>'
        . '<div class="flex">'
        .   '<div class="p-2" $active-font>active-font</div>'
        . '</div>'
        . '</div>';

    $tag = '<div class="p-4 text-center" $surface>'
        . '<div class="bold">surface</div>'
        . '<table class="table">'
        . '<tr>'
        .   '<td class="p-4" $disabled-bg>' . $tag1 . '</td>'
        .   '<td class="p-4" $bg>' . $tag2 . '</td>'
        .   '<td class="p-4" $active-bg>' . $tag3 . '</td>'
        . '</tr>'
        . '<tr>'
        .   '<td class="p-4" $disabled-border>disabled-border</td>'
        .   '<td class="p-4" $border>border</td>'
        .   '<td class="p-4" $active-border>active-border</td>'
        . '</tr>'
        . '</table>'
        . '</div>';


    $tag = preg_replace_callback('/\$([a-z\-]+)?/', function ($match) use ($type) {
        return 'style="background: var(--' . $type . '-' . $match[1] . '-color);"';
    }, $tag);


    return '<h2>' . ucfirst($type) . '</h2>'
        . $tag;
}

/**
 * 
 */
function create_table(string $type): string
{
    $typeClass = '';
    if ($type !== 'regular') {
        $typeClass = ' box-' . $type;
    }

    $colors = ['default', 'primary', 'secondary'];
    //$colors = array_merge($colors, ['info', 'success', 'warning', 'danger']);
    $tones = ['disabled', '', 'active'];
    $tr = '';
    //$text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
    $text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    foreach ($colors as $color) {
        $td = '';

        foreach ($tones as $tone) {
            $class = $color . ($tone ? '-' . $tone : '');
            $td .= '<td class="box-' . $class . $typeClass . '">'
                . '<div class="box-' . $class . $typeClass . '" style="border-width: 3px; border-style: solid; padding: 10px;">'
                //. '<h1>Lorem ipsum</h1>'
                . '<p>' . $class . $typeClass . '</p>'
                . '<p>' . $text . '</p>'
                . '<p class="color-subtle">' . $text . '</p>'
                . '</div>'
                . '</td>';
        }

        $tr .= '<tr>' . $td . '</tr>';
    }

    return '<h2>' . ucfirst($type) . '</h2>'
        . '<table class="table">' . $tr . '</table>';
}
