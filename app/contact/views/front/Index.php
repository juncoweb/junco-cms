<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// vars
$contact = snippet('contact', $snippet);

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title(_t('Contact'));
$tpl->content = $contact->render();

return $tpl->response();
