<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
    $rows['charset'] = substr($rows['collation'], 0, strpos($rows['collation'], '_'));
};
