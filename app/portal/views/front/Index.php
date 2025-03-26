<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// vars
$view = snippet('portal', $snippet);

# plugins
if ($plugins) {
    Plugins::get('portal', 'load', $plugins)->run($view);
}

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title(_t('Portal'));
$tpl->content = $view->render();

return $tpl->response();
