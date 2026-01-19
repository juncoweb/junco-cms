<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setOptions('btn_caption', [
        'responsive' => _t('Responsive'),
        'visible'    => _t('Visible'),
        'hidden'     => _t('Hidden'),
    ]);
};
