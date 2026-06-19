<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;
use Junco\Users\Repository\Setter;

return function (PluginLoader $loader) {
    $roles = (new Setter)->getRoles(['--- ' . _t('Select') . ' ---']);

    $loader->setOptions('default_ucid', $roles);
    $loader->setOptions('password_level', [
        '0 - ' . _t('No requirement'),
        '1 - ' . _t('At least one number'),
        '2 - ' . _t('At least one number and one uppercase'),
        '3 - ' . _t('At least one number, one uppercase and one symbol'),
    ]);
    $loader->setOptions('locks_level', [
        '0 - ' . _t('Do not lock'),
        '1 - ' . _t('Low'),
        '2 - ' . _t('Medium'),
        '3 - ' . _t('High'),
    ]);
};
