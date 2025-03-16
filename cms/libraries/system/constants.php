<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * System
 */
$config = config('system');
define('SYSTEM_DEVELOPER_MODE', $config['system.developer_mode']);
define('SYSTEM_HANDLE_ERRORS', $config['system.handle_errors']);
// Cache
define('SYSTEM_ALLOW_CACHE', $config['system.allow_cache']);
define('SYSTEM_CACHE_SHORT_TTL', $config['system.cache_short_ttl']);
define('SYSTEM_CACHE_LONG_TTL', $config['system.cache_long_ttl']);
//
define('SYSTEM_MKDIR_MODE', $config['system.mkdir_mode']);
