<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setOptions('handler', [
        ''     => _t('Default'),
        'file' => _t('File'),
        'db'   => _t('Database'),
    ]);
};
