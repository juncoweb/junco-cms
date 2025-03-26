<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Usys\MFACollector;

return function (&$rows) {
    $rows['mfa_url']['options'] = MFACollector::getAll();
};
