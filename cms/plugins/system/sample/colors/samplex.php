<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

include 'library.php';

//
$html = create_palette('regular');
$html .= create_palette('solid');
$html .= create_table('regular');
$html .= create_table('solid');

// template
$tpl = Template::get();
$tpl->options([
    'css' => 'cms/scripts/system/css/test-colors.css',
    'thirdbar' => 'system.thirdbar'
]);
$tpl->title('Colors');
$tpl->content($html);

return $tpl->response();
