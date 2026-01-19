<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setOptions('transport', [
        0       => '--- ' . _t('Select') . ' ---',
        'mail' => 'mail',
        'smtp' => 'smtp'
    ]);

    $loader->setOptions('charset', [
        'iso-8859-1' => 'iso-8859-1',
        'utf-8'      => 'utf-8'
    ]);

    $loader->setOptions('header_encoding', [
        'B' => 'Base64',
        'Q' => 'Quoted-printable'
    ]);

    $loader->setOptions('message_encoding', [
        'base64'           => 'Base64',
        'quoted-printable' => 'Quoted-printable'
    ]);

    $loader->setOptions('smtp_secure', [
        0     => '--- ' . _t('Select') . ' ---',
        'tls' => 'tls',
        'ssl' => 'ssl'
    ]);
};
