<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Settings;


class SettingType
{
    /**
     * Get
     * 
     * @return bool
     */
    public function isRestorable(string $type, ?array $options): bool
    {
        return in_array($type, [
            'input-integer',
            'input-range',
            'input-text',
            'input-email',
            'input-password',
            'input-phone',
            'input-url',
            'input-color',
            'textarea',
            'boolean',
            'list',
            'json',
            'image-resizes'
        ])
            || (in_array($type, [
                'select-integer',
                'select-text',
                'snippet',
                'plugin',
                'select-multiple-integer',
                'select-multiple-text',
                'plugins'
            ]) && $options !== null);
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getList(): array
    {
        return [
            _t('Integer') => [
                'input-integer'           => _t('Input (Integer)'),
                'input-range'             => _t('Range'),
                'select-integer'          => _t('Selector (Integer)'),
                'select-multiple-integer' => _t('Selector multiple (Integer)'),
            ],
            _t('Text') => [
                'input-text'              => _t('Input (Text)'),
                'input-email'             => _t('Email'),
                'input-password'          => _t('Password'),
                'input-phone'             => _t('Phone'),
                'input-url'               => _t('Url'),
                'input-color'             => _t('Color'),
                'select-text'             => _t('Selector (Text)'),
                'select-multiple-text'    => _t('Multiple selector (Text)'),
                'textarea'                => _t('Textarea'),
            ],
            _t('Others') => [
                'boolean'                 => _t('Boolean'),
                'list'                    => _t('List'),
                'json'                    => _t('JSON'),
            ],
            _t('Framework') => [
                'plugin'                  => _t('Plugin'),
                'plugins'                 => _t('Plugins'),
                'snippet'                 => _t('Snippet'),
                'image-resizes'           => _t('Image resizes'),
            ],
        ];
    }
}
