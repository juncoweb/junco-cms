<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$row) {
    $delivery = [];
    foreach (explode('|', $row['delivery']) as $partial) {
        $delivery[] = $partial ? explode('+', $partial) : [];
    }
    $row['delivery'] = $delivery;
};
