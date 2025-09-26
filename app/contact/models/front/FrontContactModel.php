<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class FrontContactModel extends Model
{
    // vars
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Get
     */
    public function getIndexData()
    {
        return [
            'snippet' => config('contact.snippet'),
            'options' => config('contact.options')
        ];
    }

    /**
     * Get
     */
    public function getMessageData()
    {
        return [
            'options' => config('contact.options')
        ];
    }
}
