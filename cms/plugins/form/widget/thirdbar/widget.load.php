<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetInterface;

return function (WidgetInterface $widget) {
    $html = (new Samples)->menu('form', [
        [
            'title' => _t('Buttons'),
            'image' => 'fa-regular fa-paper-plane',
            'edge' => [
                'form.button',
                'form.button-group',
                'form.press-btn',
                'form.button-inline'
            ]
        ],
        [
            'title' => _t('Form'),
            'image' => 'fa-solid fa-pencil',
            'edge' => [
                'form.input',
                'form.input-group',
                'form.icon-group',
                'form.checks',
                'form.date',
                'form.editor',
                'form.UploadHandler',
                'form.collection',
                'form.suite',
            ]
        ],
        [
            'title' => _t('Plugin'),
            'image' => 'fa-solid fa-puzzle-piece',
            'edge' => [
                'form.select-color',
                'form.select-weekday',
                'form.rating',
            ]
        ],
    ]);

    $widget->section([
        'content' => $html
    ]);
};
