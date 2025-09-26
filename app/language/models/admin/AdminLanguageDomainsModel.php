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
        $data = $this->filter(POST, [
            'id' => 'array:first|required:abort',
            'search' => 'text'
        ]);

        return [
            'title' => $data['id'],
            'data' => $data + ['language' => $data['id']]
        ];
    }

    /**
     * Get
     */
    public function getListData()
    {
        $data = $this->filter(POST, [
            'language' => 'required:abort',
            'search' => 'text'
        ]);

        $dir  = (new LanguageHelper())->getLocale() . $data['language'] . '/LC_MESSAGES/';
        $filter = $data['search']
            ? '/' . preg_quote($data['search']) . '/i'
            : '';

        return $data + ['rows' => $this->getAll($dir, $filter)];
    }

    /**
     * Get
     */
    protected function getAll(string $dir, string $filter): array
    {
        $rows = [];
        $cdir = is_readable($dir)
            ? scandir($dir)
            : [];

        foreach ($cdir as $elem) {
            if (
                $elem != '.'
                && $elem != '..'
                //&& is_file($dir . $elem)
                && (pathinfo($dir . $elem, PATHINFO_EXTENSION) == 'po')
                && (!$filter || preg_match($filter, $elem))
            ) {
                $rows[$elem] = $elem;
            }
        }

        return $rows;
    }
}
