<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
    $rows['worker']['options'] = [
        ''            => '--- ' . _t('Select') . ' ---',
        'worker'    => 'Worker',
        'cron'        => 'Cron'
    ];
};
