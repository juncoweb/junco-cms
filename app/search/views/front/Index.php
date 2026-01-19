<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// vars
$url = router()->getUrlForm('/search');

// html
$input = '<div class="input-icon-group input-large">'
    . '<input type="text" id="search" name="q" value="' . $search . '" autocomplete="off" class="input-field"/>'
    . '<button type="submit" class="input-icon"><i class="fa-solid fa-magnifying-glass"></i></button>'
    . '</div>';

$html = '<form id="search-form" action="' . $url['action'] . '">' . "\n"
    . $url['hidden']
    . "\t" . $input . "\n"
    . ($engines->num_rows > 1 ? '<div id="se-engines" class="se-engines">' . $engines->render() . '</div>' : '')
    . '</form>' . "\n";

$html = '<div class="se-header">' . $html . '</div>' . "\n\n";

// results
$html .= "\t" . '<h2 class="se-title">' . _t('Results') . '</h2>' . "\n"
    . "\t" . '<div id="se-results" class="se-results">' . "\n"
    .  '<div>' . ($engines->num_rows ? $engines->results($search) : _t('Error. No search engines.')) . '</div>'
    . '</div>' . "\n\n";

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title(_t('Search'));
$tpl->content($html);

return $tpl->response();
