<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get();
$tpl->js('assets/language.min.js');
$tpl->title(_t('Language'));
$tpl->content = $this->content();

return $tpl->response();
