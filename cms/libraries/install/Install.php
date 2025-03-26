<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * Install
 */
class Install
{
    /**
     * Database Can Connect
     *
     * @return bool
     */
    public static function dbCanconnect()
    {
        try {
            return db()->isConnected();
        } catch (Throwable $e) {
            return false;
        }
    }
}
