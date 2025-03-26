<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

if (!empty($error)) {
    return '<p class="dialog dialog-warning mt-8">' . _t('Error! The section not found.') . '</p>';
}

$box_tag = '<div class="layout-title-group">'
    . '<div class="layout-image" aria-hidden="true"><i class="fa-solid fa-gear"></i></div>'
    . '<div class="layout-title"><h1>' . _t('Settings') . ' &gt; %s</h1></div>'
    . '</div>%s';

if (!empty($home)) {
    $tiles = Tiles::get();
    $tiles->line([
        ['href' => url('admin/settings', ['key' => 'site']), 'icon' => 'fa-solid fa-globe', 'caption' => _t('Site')],
        ['href' => url('admin/settings', ['key' => 'system']), 'icon' => 'fa-solid fa-gears', 'caption' => _t('System')],
        ['href' => url('admin/settings', ['key' => 'usys']), 'icon' => 'fa-solid fa-user', 'caption' => _t('Usys')],
        ['href' => url('admin/settings', ['key' => 'contact']), 'icon' => 'fa-solid fa-envelope', 'caption' => _t('Contact')],
        ['href' => url('admin/settings', ['key' => 'search']), 'icon' => 'fa-solid fa-magnifying-glass', 'caption' => _t('Search')],
    ]);
    $tiles->separate(_t('Shortcuts'));

    return sprintf($box_tag, _t('Home'), '<div class="mt-8">' . $tiles->render() . '</div>');
}

// form
$form = Form::get('', 'settings-form');
$form->setValues($values);
$form->hidden('__key');

// actions
$fac = $form->getActions();
$fac->enter();
$fac->separate();
$fac->refresh();
if ($developer_mode) {
    $fac->dropdown([
        ['control' => 'prepare', 'label' => _t('Create'), 'icon' => 'fa-solid fa-plus'],
        ['control' => 'edit', 'label' => _t('Edit'), 'icon' => 'fa-solid fa-pencil'],
        ['control' => 'delete', 'label' => _t('Delete'), 'icon' => 'fa-solid fa-trash-can'],
    ]);
}

if ($keys) {
    foreach ($keys as $i => $row) {
        $keys[$i] = $row['selected']
            ? '<span class="btn btn-primary btn-solid">' . $row['label'] . '</span>'
            : '<a href="' . $row['url'] . '" control-form="load" class="btn btn-outline">' . $row['label'] . '</a>';
    }

    $form->addBlock('<div class="text-right"><div class="btn-group btn-small">' . implode($keys) . '</div></div>');
}

if ($warning) {
    foreach ($warning as $i => $message) {
        $warning[$i] = '<div class="dialog dialog-warning"><b>' . _t('Warning!') . '</b> ' . $message . '</div>';
    }

    $form->addBlock(implode($warning));
}

//
if ($description) {
    $form->addRow(['help' => $description]);
    $form->separate('');
}

// elements
$restore_tag = '<a href="javascript:void(0)" control-form="restore" role="button" title="' . ($t = _t('Restore')) . '" aria-label="' . $t . '" data-restore="%s" class="btn-inline"><i class="fa-solid fa-wand-magic"></i></a>';
if ($developer_mode) {
    $restore_tag .= ' <a href="javascript:void(0)" control-form="unlock" role="button" title="' . ($t = _t('Lock')) . '" aria-label="' . $t . '" class="btn-inline"><input type="checkbox" name="unlock[]" value="%1$s" style="display: none;"/><i class="fa-solid fa-lock"></i></a>';
}

foreach ($groups as $i => $group) {
    if ($group['description']) {
        $form->addRow(['help' => $group['description']]);
    }

    foreach ($group['rows'] as $row) {
        switch ($row['type']) {
            // input
            case 'input-integer':
                $form->input($row['name'], ['type' => 'number']);
                break;

            case 'input-range':
                $form->input($row['name'], ['type' => 'range', 'min' => $row['min'], 'max' => $row['max'], 'class' => 'input-range']);
                break;

            default:
            case 'input-text':
                $form->input($row['name']);
                break;

            case 'input-email':
                $form->input($row['name'], ['type' => 'email']);
                break;

            case 'input-password':
                $form->input($row['name'], ['type' => 'password', 'control-felem' => 'password']);
                break;

            case 'input-phone':
                $form->input($row['name'], ['type' => 'tel']);
                break;

            case 'input-url':
                $form->input($row['name'], ['type' => 'url']);
                break;

            case 'input-color':
                $form->input($row['name'], ['type' => 'color', 'control-felem' => 'color']);
                break;

            // select
            case 'select-integer':
            case 'select-text':
            case 'snippet';
            case 'plugin':
                if ($row['options'] === null) {
                    $form->load('settings.not_found');
                } else {
                    $form->select($row['name'], $row['options']);
                }
                break;

            case 'select-multiple-integer':
            case 'select-multiple-text':
            case 'plugins':
                if ($row['options'] === null) {
                    $form->load('settings.not_found');
                } else {
                    $form->suite($row['name'], $row['options']);
                }
                break;

            // others
            case 'textarea':
                $form->textarea($row['name'], ['auto-grow' => '']);
                break;

            case 'boolean':
                $form->toggle($row['name']);
                break;

            case 'json':
                $form->load('settings.json', [
                    'name' => $row['name'],
                    'options' => $row['options']
                ]);
                break;
        }

        if ($row['restore']) {
            $row['restore'] = sprintf($restore_tag, $row['name']);
        }

        $element = $form->getLastElement();
        $element->setLabel(_t($row['label']));
        $element->setHelp($row['help']);
        $form->addRow([
            'label' => $element->getLabel(),
            'content' => $element->render(),
            'button' => $row['restore'],
            'help' => $element->getHelp()
        ]);
    }

    $form->separate($group['legend']);
}

$html = $form->render() . '<textarea id="restore" style="display: none;">' . $restore . '</textarea>';

return sprintf($box_tag, $title, $html);
