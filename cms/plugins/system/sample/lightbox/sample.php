<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// samples
$samples = Samples::get();
$samples
    ->js('Lightbox();')
    ->setLabel('Basic Lightbox');
$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'system.thirdbar']);
$tpl->title('Lightbox');
$tpl->content($html);

return $tpl->response();
