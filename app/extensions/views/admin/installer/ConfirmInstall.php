<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// tabs
$tabs = Tabs::get('', 'installer-tabs');

// Main
if (!$error) {
    $html = '<div class="dialog dialog-success">' . sprintf(_t('The installation package «%s» has been opened successfully.'), $package) . '</div>';
} else {
    $html = '<div class="dialog dialog-danger">' . _t('The installation package has the following alerts:') . '</div>';
    $css = ['alert' => 'color-yellow', 'fatal' => 'color-red'];

    foreach ($error as $row) {
        $html .= sprintf(
            '<p><i class="fa-solid fa-triangle-exclamation %s" title="%s"></i> %d. %s</p>',
            $css[$row['type']],
            $row['type'],
            $row['line'],
            $row['message']
        );
    }
}
$tabs->tab(_t('Main'), $html);

// Readme
if ($readme) {
    $tabs->tab(_t('Readme'), $readme);
}

if (!$error_fatal) {
    // Developer
    $translate = [
        'developer_name' => _t('Name'),
        'project_url'    => _t('Project URL'),
        'webstore_url'   => _t('Update URL'),
    ];

    $html = '';
    foreach ($developer as $key => $value) {
        $html .= '<tr>'
            .   '<th class="text-nowrap table-auto">' . ($translate[$key] ?? $key) . ':</th>'
            .   '<td>' . ($value ?: '<span class="color-red">-</span>') . '</td>'
            . '</tr>';
    }
    $tabs->tab(_t('Developer'), '<table class="table table-striped"><tbody>' . $html . '</tbody></table>');

    // Extensions
    $legend_tag = '<label for="extensions%s"><input type="checkbox" id="extensions%d" name="extension_alias[]" value="%s"%s class="input-checkbox"/> %s</label>';
    $html       = '';
    $count      = 0;
    $statuses = [
        -1 => [' checked', 'success', 'Install'],
        0 => [' disabled', 'danger', 'Denied'],
        1 => [' checked', 'info', 'Update'],
    ];
    $translate = [
        'extension_alias'    => _t('Alias'),
        'extension_abstract' => _t('Abstract'),
        'components'         => _t('Components'),
        'db_queries'         => _t('SQL queries'),
        'xdata'              => 'XDATA',
        'extension_version'  => _t('Version'),
        'extension_credits'  => _t('Credits'),
        'extension_license'  => _t('License'),
        'extension_require'  => _t('Requirements'),
        'status'             => _t('Status'),
    ];

    // summary
    foreach ($summary as $key => $value) {
        $html .= '<tr>'
            .   '<th class="text-nowrap table-auto">' . ($translate[$key] ?? $key) . ':</th>'
            .   '<td>' . ($value ?: '<span class="color-red">-</span>') . '</td>'
            . '</tr>';
    }

    $html = '<fieldset class="form-fieldset">'
        . '<legend>' . _t('Summary') . '</legend>'
        . '<table class="table table-striped"><tbody>' . $html . '</tbody></table></fieldset>';

    foreach ($extensions as $row) {
        $legend = sprintf($legend_tag, $count, $count, $row['extension_alias'], $statuses[$row['status']][0], $row['extension_name']);

        unset($row['extension_name']);
        if (empty($row['extension_abstract'])) {
            unset($row['extension_abstract']);
        }
        if (isset($row['db_queries'])) {
            $row['db_queries'] = implode(', ', explode(',', $row['db_queries']));
        }

        //
        $html .= '<fieldset class="form-fieldset">';
        $html .= '<legend>' . $legend . '</legend>';
        $html .= '<table class="table table-striped"><tbody>';
        foreach ($row as $key => $value) {
            if ($key == 'status') {
                $value = '<div class="badge badge-' . $statuses[$value][1] . ' badge-small">' . $statuses[$value][2] . '</div>';
            }
            $html .= '<tr><th class="text-nowrap table-auto">' . ($translate[$key] ?? $key) . ':</th><td>' . $value . '</td></tr>';
        }
        $html .= '</tbody></table>';
        $html .= '</fieldset>';
        ++$count;
    }

    $tabs->tab(_t('Extensions'), $html ?: '<span class="italic">' . _t('Empty list') . '</span>');

    // Cleaner
    if ($cleaner_paths) {
        $felem = Form::getElements();
        $felem->setValues(['clean_paths' => $cleaner_paths]);
        $html = $felem->checkboxList('clean_paths', array_combine($cleaner_paths, $cleaner_paths), ['check-all' => true]);

        $tabs->tab(_t('Cleaner'), $html);
    }

    // Settings
    $form = Form::get('', false);
    $form->setValues([
        'copy_files'     => true,
        'db_import'      => 1,
        'execute_before' => true,
        'execute_after'  => true,
        'remove_package' => true,
    ]);
    //
    $form->toggle('copy_files')->setLabel(_t('Copy'));
    $form->separate(_t('Files'));
    //
    $form->select('db_import', $db_import)
        ->setLabel(_t('Import'))
        ->setHelp(_t('Select an option to import the databases.'));
    $form->separate(_t('Database'));
    //
    if ($executables) {
        foreach ($executables as $action => $content) {
            $form->checkbox("execute_{$action}")->setLabel($action . ' <span class="color-subtle-default">(' . implode(', ', $content) . ')</span>');
        }

        $form->separate(_t('Executables'));
    }
    //
    $form->toggle('remove_package')
        ->setLabel(_t('Remove'))
        ->setHelp(_t('The package will be removed from the installer.'));
    $form->separate(_t('Package'));
    //
    $tabs->tab('<i class="fa-solid fa-gear"></i>', $form->render());
}

// Changelog
if ($changelog) {
    $tabs->tab('<i class="fa-solid fa-file-lines" title="' . _t('Changelog') . '"></i>', $changelog);
}

$html = '<form id="js-form">'
    . $tabs->render()
    . '<input type="hidden" name="package" value="' . $package . '">'
    . $token
    . '</form>';


// modal
$modal = Modal::get();
if (!$error_fatal) {
    $modal->enter(_t('Install'));
}
$modal->close();
$modal->title([_t('Installer'), $id]);
$modal->content($html);

return $modal->response();
