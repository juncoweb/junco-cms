<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

if (!empty($error)) {
    $html = '<div class="dialog dialog-warning mt-4">' . $error . '</div>';
} else {
    // list
    $bls = Backlist::get();

    // filters
    $filters = $bls->getFilters();
    $filters->setValues($data);
    $filters->search();

    // table
    if ($rows) {
        $bls->setRows($rows);
        $bls->fixEnum('is_installed', [
            ['class' => ' btn-primary btn-solid', 'title' => _t('Install')],
            ['class' => '', 'title' => _t('Update')],
        ]);
    }
    //
    $bls->column('<div class="box-primary box-solid"><img src="{{ image }}" alt="{{ image }}" class="responsive ws-image" /></div>')
        ->setWidth(100);

    $bls->column('<a href="{{ details_url }}" class="ws-title">{{ name }}</a>'
        . '<div class="ws-details">'
        . sprintf(_t('By %s'), '<b>{{ developer }}</b>')
        . '<div>' . snippet('rating', 'utils')->render('{{ num_ratings }}') . ' | <span>{{ num_views }} ' . _t('Visits') . '</span></div>'
        . '</div>');

    $bls->column('<button'
        .  ' type="button"'
        .  ' class="btn{{ is_installed.class }}"'
        .  ' control-list="confirm_download"'
        .  ' data-name="extension_id"'
        .  ' data-value="{{ id }}"'
        . '>{{ is_installed.title }}</button>')
        ->setWidth(80);

    $html = $bls->render($pagi);
}

return $html;
