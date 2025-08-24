<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get();

# plugins
$dashboard = snippet('dashboard', $snippet);
if ($plugins) {
    Plugins::get('dashboard', 'load', $plugins)?->run($dashboard);
}

$tpl->options($options);
$tpl->title([curuser()->getName(), _t('My space')]);
$tpl->content = $dashboard->render();

return $tpl->response();
