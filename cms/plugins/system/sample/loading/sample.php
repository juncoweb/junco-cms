<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// samples
$samples = Samples::get();
$samples
    ->js('JsLoading(true);')
    ->setLabel('Show loading indicator');

$samples
    ->js('JsLoading(false);')
    ->setLabel('Hide loading indicator');

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'system.thirdbar']);
$tpl->title('Spinner');
$tpl->content($html);

return $tpl->response();
