<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get();

// vars
$collector = snippet('dashboard', $snippet);

# plugins
if ($plugins) {
    Plugins::get('dashboard', 'load', $plugins)?->run($collector);
}

if ($options) {
    $tpl->options($options);
}
$tpl->title(_t('Dashboard'), 'fa-solid fa-dashboard');
$tpl->content = $collector->render();

return $tpl->response();
