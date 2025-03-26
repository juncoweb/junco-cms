<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminLanguageDomainsModel extends Model
{
    /**
     * Get
     */
    public function getIndexData()
    {
        // data
        $this->filter(POST, [
            'id' => 'array:first|required:abort',
            'search' => 'text'
        ]);

        return [
            'title' => $this->data['id'],
            'data' => $this->data + ['language' => $this->data['id']]
        ];
    }

    /**
     * Get
     */
    public function getListData()
    {
        // data
        $this->filter(POST, [
            'language' => 'required:abort',
            'search' => 'text'
        ]);

        if ($this->data['search'] && preg_match('@[\w_]+@', preg_quote($this->data['search'], '@'))) {
            $filter = '@' . $this->data['search'] . '@i';
        } else {
            $filter = '';
        }

        $dir  = (new LanguageHelper())->getLocale() . $this->data['language'] . '/LC_MESSAGES/';
        $cdir = is_readable($dir) ? scandir($dir) : [];
        $rows = [];

        foreach ($cdir as $has) {
            if (
                $has != '.'
                && $has != '..'
                //&& is_file($dir . $has)
                && (pathinfo($dir . $has, PATHINFO_EXTENSION) == 'po')
                && (!$filter || preg_match($filter, $has))
            ) {
                $rows[$has] = $has;
            }
        }
        return $this->data + ['rows' => $rows];
    }
}
