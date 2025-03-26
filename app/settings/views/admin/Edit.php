<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// vars
$text = [
    'delete'            => _t('Delete'),
    'name'                => _t('Name'),
    'history'            => _t('History'),
    'help'                => _t('Help'),
    'autoload'            => _t('Autoload'),
    'translate'            => _t('Translate'),
    'reload_on_change'    => _t('Reload on change'),

];
$header_tag = '%d. %s ' . _t('Group') . ' %s ' . _t('Order') . ' %s %s';
$statuses = [_t('Only developer mode'), _t('Public')];
$types = [
    _t('Integer') => [
        'input-integer'                => _t('Input (Integer)'),
        'input-range'                => _t('Range'),
        'select-integer'            => _t('Selector (Integer)'),
        'select-multiple-integer'    => _t('Selector multiple (Integer)'),
    ],
    _t('Text') => [
        'input-text'                => _t('Input (Text)'),
        'input-email'                => _t('Email'),
        'input-password'            => _t('Password'),
        'input-phone'                => _t('Phone'),
        'input-url'                    => _t('Url'),
        'input-color'                => _t('Color'),
        'select-text'                => _t('Selector (Text)'),
        'select-multiple-text'        => _t('Multiple selector (Text)'),
        'textarea'                    => _t('Textarea'),
    ],
    _t('Others') => [
        'boolean'                    => _t('Boolean'),
        'list'                        => _t('List'),
        'json'                        => _t('JSON'),
    ],
    _t('Framework') => [
        'plugin'                    => _t('Plugin'),
        'plugins'                    => _t('Plugins'),
        'snippet'                    => _t('Snippet'),
    ],
];

// form
$form = Form::get();

// actions
$fac = $form->getActions();
$fac->enter();
$fac->cancel();

// elements
$form->setValues($data);
$form->input('title')->setLabel(_t('Title'));
$form->input('groups')->setLabel(_t('Groups'));
$form->textarea('description')->setLabel(_t('Description'));
$form->hidden('key');
$form->separate();

$i = 0;
foreach ($data['rows'] as $row) {
    $form->setDeep('[' . $i . ']');
    $form->setValues($row);

    $form->input('label', ['class' => 'input-inline']);
    $element_1 = $form->getLastElement();

    $form->input('group', ['maxlength' => 2, 'class' => 'input-inline', 'style' => 'width:50px']);
    $element_2 = $form->getLastElement();

    $form->input('ordering', ['maxlength' => 2, 'class' => 'input-inline', 'style' => 'width:50px']);
    $element_3 = $form->getLastElement();

    $form->checkbox('delete')->setLabel('<i class="fa-solid fa-trash-can" aria-hidden="true"><div class="visually-hidden">' . $text['delete'] . '</div></i>');
    $element_4 = '<span title="' . $text['delete'] . '" class="ml-4">' . $form->getLastElement() . '</span>';

    $form->header(sprintf($header_tag, $i + 1, $element_1, $element_2, $element_3, $element_4), false);
    $form->group(
        $form->select('type', $types, ['style' => 'width: 80%']),
        $form->input('name')->setLabel($text['name']),
        $form->input('history', ['placeholder' => $text['history']])
    );

    $form->textarea('help', ['auto-grow' => ''])->setLabel($text['help']);
    $form->checkbox('autoload')->setLabel($text['autoload']);
    $form->checkbox('translate')->setLabel($text['translate']);
    $form->checkbox('reload_on_change')->setLabel($text['reload_on_change']);
    $form->radio('status', $statuses);
    $form->hidden('id');
    $form->separate();
    $i++;
}

// modal
$modal = Modal::get();
$modal->title([_t('Settings'), _t('Manager')]);
$modal->content = $form->render();

return $modal->response();
