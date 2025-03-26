<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;


$html = '<h2>Form</h2>'
    . '<form>'
    . '<div class="form-group"><label class="form-label">User</label><input class="input-field"></div>'
    . '<div class="form-group"><label class="form-label">Email</label><input class="input-field"></div>'
    . '</form>';

$html .= '<h2>Inline form</h2>'
    . '<form class="form-inline">'
    . '<div class="form-group"><label class="form-label">User</label> <input class="input-field"></div> '
    . '<div class="form-group"><label class="form-label">Email</label> <input class="input-field"></div>'
    . '</form>';


$html .= '<h2>Horizontal form</h2>'
    . '<form class="form-horizontal">'
    . '<div class="form-group"><label class="form-label col s2">User</label><div class="col s10"><input class="input-field"></div></div> '
    . '<div class="form-group"><label class="form-label col s2">Email</label><div class="col s10"><input class="input-field"></div></div>'
    . '</form>';

$html .= '<h2>Header</h2>'
    . '<form class="form-horizontal" id="js-form">'
    . '<div class="form-fieldset">'
    . '<div class="form-header">Header<i class="form-toggle" control-form="toggle-body"></i></div>'
    . '<div class="form-body"><div class="form-group"><label class="form-label col s2">Email</label><div class="col s10"><input class="input-field"></div></div></div>'
    . '</div>'
    . '</form>';

// template
$tpl = Template::get();
$tpl->options([
    'thirdbar' => 'form.thirdbar',
    'css' => 'assets/system.min.css,cms/snippets/form/master/default/css/form.css',
    //'js' => 'cms/scripts/form/js/elements.js,cms/scripts/system/js/controls.js,cms/scripts/system/js/form.js',
    //'domready' => 'JsForm().controls().request()'
]);
$tpl->title('Form Group');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
