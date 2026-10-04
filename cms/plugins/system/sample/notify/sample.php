<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// samples
$samples = Samples::get();
$samples
    ->js('JsNotify({"message":"Hello world!","target":"#notify_1"})')
    ->setLabel('Basic Notify 1')
    ->setContext('<div id="notify_1" role="alert" class="notify-box"></div>');

$samples
    ->js('JsNotify({"message":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.","target":"#notify_2"})')
    ->setLabel('Basic Notify 2')
    ->setContext('<div id="notify_2" role="alert" class="notify-box"></div>');

$samples
    ->js('JsNotify.creator("#notify_3").notify("Hello world!")')
    ->setLabel('Notify Creator')
    ->setContext('<div id="notify_3" role="alert" class="notify-box"></div>');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'system.thirdbar']);
$tpl->title('Notify');
$tpl->content($html);

return $tpl->response();
