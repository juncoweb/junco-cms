<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$widget) {
    $html = (new Samples)->menu('form', [
        [
            'title' => _t('Buttons'),
            'image' => 'fa-regular fa-paper-plane',
            'edge' => ['form.button', 'form.button-group']
        ],
        [
            'title' => _t('Form'),
            'image' => 'fa-solid fa-pencil',
            'edge' => ['form.input', 'form.input-group', 'form.checks', 'form.date', 'form.editor', 'form.UploadHandle', 'form.collection', 'form.suite']
        ],
    ]);

    $widget->section([
        'content' => $html
    ]);
};
