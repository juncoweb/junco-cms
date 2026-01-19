<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->enter();
//
$form->editor('body');
$form->editor('bodyw');
$form->editor('bodyq');
$form->hidden('key', 'form.editor');
$html = $form->render();

// template
$tpl = Template::get();
$tpl->editor();
$tpl->options([
    'domready' => 'JsFelem.load(document.getElementById(\'js-form\'))',
    'thirdbar' => 'form.thirdbar'
]);
$tpl->title(_t('Editor'));
$tpl->content('<div class="panel"><div class="panel-body">' . $html . '</div></div>');

return $tpl->response();
