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
    $form->element($description);
    $form->separate('');
}

// elements
if ($groups) {
    foreach ($groups as $i => $group) {
        if ($group['description']) {
            $form->element($group['description']);
        }

        foreach ($group['rows'] as $row) {
            switch ($row['type']) {
                // input
                case 'input-integer':
                    $element = $form->input($row['name'], ['type' => 'number']);
                    break;

                case 'input-range':
                    $element = $form->input($row['name'], ['type' => 'range', 'min' => $row['min'], 'max' => $row['max'], 'class' => 'input-range']);
                    break;

                default:
                case 'input-text':
                    $element = $form->input($row['name']);
                    break;

                case 'input-email':
                    $element = $form->input($row['name'], ['type' => 'email']);
                    break;

                case 'input-password':
                    $element = $form->input($row['name'], ['type' => 'password', 'control-felem' => 'password']);
                    break;

                case 'input-phone':
                    $element = $form->input($row['name'], ['type' => 'tel']);
                    break;

                case 'input-url':
                    $element = $form->input($row['name'], ['type' => 'url']);
                    break;

                case 'input-color':
                    $element = $form->input($row['name'], ['type' => 'color', 'control-felem' => 'color']);
                    break;

                // select
                case 'select-integer':
                case 'select-text':
                case 'snippet';
                case 'plugin':
                    if ($row['options'] === null) {
                        $element = $form->load('settings.not_found');
                    } else {
                        $element = $form->select($row['name'], $row['options']);
                    }
                    break;

                case 'select-multiple-integer':
                case 'select-multiple-text':
                case 'plugins':
                    if ($row['options'] === null) {
                        $element = $form->load('settings.not_found');
                    } else {
                        $element = $form->suite($row['name'], $row['options']);
                    }
                    break;

                // others
                case 'textarea':
                    $element = $form->textarea($row['name'], ['auto-grow' => '']);
                    break;

                case 'boolean':
                    $element = $form->toggle($row['name']);
                    break;

                case 'json':
                    $element = $form->load('settings.json', [
                        'name' => $row['name'],
                        'options' => $row['options']
                    ]);
                    break;
            }

            $element
                ->setLabel(_t($row['label']))
                ->setHelp($row['help']);

            if ($row['restore']) {
                $element->setAction([
                    'icon'         => 'fa-solid fa-wand-magic',
                    'control-form' => 'restore',
                    'data-restore' => $row['name'],
                    'title'        => _t('Restore'),
                ]);

                if ($developer_mode) {
                    $element->setAction([
                        'checkbox' => true,
                        'name'     => 'unlock[]',
                        'value'    => $row['name'],
                        'icon'     => 'fa-solid fa-lock',
                        'icon_alt' => 'fa-solid fa-unlock-alt',
                        'title'    => _t('Lock'),
                    ]);
                }
            }
        }

        $form->separate($group['legend']);
    }
} else {
    $form->addBlock('<div class="text-center italic">' . _t('Empty list') . '<div>');
}


$html = $form->render() . '<textarea id="restore" style="display: none;">' . $restore . '</textarea>';

return sprintf($box_tag, $title, $html);
