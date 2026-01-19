<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get();

# plugins
$collector = snippet('dashboard', $snippet);
if ($plugins) {
    Plugins::get('dashboard', 'load', $plugins)?->run($collector);
}
$html = $collector->render();

$tpl->options($options);
$tpl->title(_t('Dashboard'), 'fa-solid fa-dashboard');
$tpl->content($html);

return $tpl->response();
