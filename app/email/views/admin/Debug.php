<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();

// actions
$fac = $form->getActions();
$fac->enter();

// elements
$form->setValues($values);
//
$form->select('transport', [
    '' => _t('System'),
    'null' => 'Null',
    'mail' => 'Mail',
    'smtp' => 'Smtp',
])->setLabel(_t('Transport'));
$form->select('message_type', [
    'html+plain'    => 'Html + Plain',
    'html'          => 'Html',
    'html+to-plain' => 'Html + to Plain',
    'plain'         => 'Plain',
])->setLabel(_t('Message'));
$form->separate();
//
$form->input('subject')->setLabel(_t('Subject'));
$form->input('to')->setLabel(_t('To'));
$form->textarea('message_plain')->setLabel('Plain');
$form->editor('message_html', 'Html');
$html = $form->render();

// tabs
$tabs = Tabs::get();
$tabs->tab(_t('Form'), $html);
$tabs->tab(_t('Code'), '<div id="email-code"></div>');
$tabs->tab(_t('Debug'), '<div id="email-debug"></div>');

$html = '<p><a href="' . url('admin/email') . '" class="btn btn-small"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> ' . _t('Back') . '</a></p>';
$html .= '<div style="max-width: 1000px;">' . $tabs->render() . '</div>';

// template
$tpl = Template::get();
$tpl->editor();
$tpl->js('assets/email-admin.min.js');
$tpl->domready('Email.debug();');
$tpl->title(_t('Code'), 'fa-solid fa-code');
$tpl->content($html);

return $tpl->response();
