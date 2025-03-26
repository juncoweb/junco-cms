<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class ProfilerModel extends Model
{
    /**
     * Get
     */
    public function getIndexData()
    {
        // data
        $this->filter(GET, ['frame' => '']);

        return $this->data + [
            'title'        => _t('Console'),
            'base_url'    => config('site.baseurl')
        ];
    }
}
