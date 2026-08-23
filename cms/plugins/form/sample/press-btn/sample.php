<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// samples
$samples = Samples::get();
$samples
    ->html('<div class="btn-group" role="group">'
        . '<label class="btn">Button 1<input type="checkbox" autocomplete="off" class="input-hidden"/></label>'
        . '<label class="btn">Button 2<input type="checkbox" autocomplete="off" class="input-hidden"/></label>'
        . '<label class="btn">Button 3<input type="checkbox" autocomplete="off" class="input-hidden"/></label>'
        . '</div>')
    ->setLabel('.btn checkbox.input-hidden');

//
$samples
    ->html('<div class="btn-group" role="group">'
        . '<label class="btn">Button 1<input type="radio" name="test" class="input-hidden"/></label>'
        . '<label class="btn">Button 2<input type="radio" name="test" class="input-hidden"/></label>'
        . '<label class="btn">Button 3<input type="radio" name="test" class="input-hidden"/></label>'
        . '</div>')
    ->setLabel('.btn radio.input-hidden');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'form.thirdbar']);
$tpl->title('Press Button');
$tpl->content($html);

return $tpl->response();
