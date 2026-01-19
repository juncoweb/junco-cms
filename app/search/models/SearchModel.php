<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class SearchModel extends Model
{
    /**
     * Save
     */
    public function getIndexData()
    {
        $data = $this->filter(GET, [
            'q'      => 'text',
            'engine' => ''
        ]);

        return [
            'engines' => new SearchEngines($data['engine']),
            'search' => $data['q'],
            'options' => config('search.options')
        ];
    }
}
