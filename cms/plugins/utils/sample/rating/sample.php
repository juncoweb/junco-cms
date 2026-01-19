<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// samples
$samples = Samples::get();

// 1
$rating = Form::getElements()->load('utils.rating', ['name' => 'rating']);
$samples
    ->html($rating)
    ->setLabel('Element');

// 2
$samples
    ->colors('<div>{{ caption }} ' . snippet('rating', 'utils')->render(3, ['color' => 'rating-{{ color }}']) . '</div>')
    ->setLabel('Colors')
    ->setFull();

// 3
$samples
    ->sizes('<div>{{ caption }} ' . snippet('rating', 'utils')->render(3, ['size' => 'rating-{{ size }}']) . '</div>')
    ->setLabel('Sizes')
    ->setFull();

$html = $samples->render();

// template
$tpl = Template::get();
$tpl->options([
    'css' => 'cms/scripts/utils/css/rating.css',
    'js' => 'cms/scripts/utils/js/rating.js',
    'domready' => 'JsRating({onSelect: function() { console.log(this.value); return true; }})'
]);
$tpl->title('Rating', 'fa-solid fa-star');
$tpl->content($html);

return $tpl->response();
