<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\UserActivityToken;

class FrontUsysPasswordModel extends Model
{
    /**
     * Get
     */
    public function getResetData()
    {
        return [
            'options' => config('usys.options'),
        ];
    }

    /**
     * Get
     */
    public function getEditData()
    {
        return [
            'options' => config('usys.options'),
            'token' => UserActivityToken::get(GET, 'savepwd', true)
        ];
    }
}
