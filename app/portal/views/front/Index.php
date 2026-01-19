<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

# plugins
$portal = snippet('portal', $snippet);
if ($plugins) {
    Plugins::get('portal', 'load', $plugins)->run($portal);
}
$html = $portal->render();

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title(_t('Portal'));
$tpl->content($html);

return $tpl->response();
