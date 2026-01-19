<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setOptions('worker', [
        ''       => '--- ' . _t('Select') . ' ---',
        'worker' => 'Worker',
        'cron'   => 'Cron'
    ]);
};
