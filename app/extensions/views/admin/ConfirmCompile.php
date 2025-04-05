<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

$html = '';

// errors
if ($errors) {
    if (isset($errors['non-compiled'])) {
        $errors['non-compiled'] = sprintf(
            _t('The following extensions have uncompiled changes: %s.'),
            implode(', ', $errors['non-compiled'])
        );
    }
    foreach ($errors as $error) {
        $html .= '<div class="dialog dialog-danger">' . $error . '</div>';
    }
}

if ($status) {
    // form
    $form = Form::get();
    $form->setValues($values);
    $form->hidden('id');

    switch ($status) {
        case -1: // Question: update requires
            foreach ($changes as $extension => $version) {
                $changes[$extension] = sprintf(_t('«%s» to %s'), $extension, $version);
            }
            $form->radio('update_requires', ['no' => _t('No'), 'yes' => _t('Yes')])
                ->setLabel('<span class="text-nowrap">' . _t('Update') . '</span>')
                ->setHelp(sprintf(_t('The following requirements will be updated: %s'), implode(', ', $changes)));
            break;

        case -2: // Question: update versions
            $form->hidden('update_requires');
            $form->radio('update_versions', ['no' => _t('No'), 'yes' => _t('Yes')])
                ->setLabel('<span class="text-nowrap">' . _t('Update') . '</span>')
                ->setHelp(sprintf(_t('The versions of the following extensions will be updated: %s.'), implode(', ', $updates)));
            break;

        case -3:
            $form->radio('package_name_format', $name_formats)->setLabel(_t('Format'));
            $form->radio('output', $outputs)->setLabel(_t('Output'));
            if ($install_package) {
                $form->checkbox('get_install_package')->setLabel(_t('Get install package'));
            }
            $form->separate(['class' => 'mx-8 mb-4']);

            if ($repairs) {
                $form->header(_t('Some repairs have been made.'), false);

                $html_1 = '';
                foreach ($repairs as $row) {
                    $html_1 .= '<div><b>' . $row['name'] . '</b></div>';

                    foreach ($row['set'] as $key => $value) {
                        $html_1 .= '<div class="color-light"><b>' . $key . ':</b> ' . str_replace(',', ', ', $value) . '</div>';
                    }
                }

                $form->addRow(['content' => $html_1]);
            }

            if ($compiler_plugins) {
                $form->separate();

                foreach ($compiler_plugins as $plugin => $row) {
                    $form->setValues(['plugins[' . $plugin . ']' => $row['value']]);
                    $form->checkbox('plugins[' . $plugin . ']')->setLabel($row['caption']);
                }

                $form->separate(_t('Plugins'));
            }
            break;
    }
    $html .= $form->render();
}

// modal
$modal = Modal::get();
if ($enter) {
    $modal->enter();
}
$modal->close();
$modal->title([_t('Extensions'), _t('Compile')]);
$modal->content = $html;

return $modal->response();
