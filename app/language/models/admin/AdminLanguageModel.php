<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminLanguageModel extends Model
{
    /**
     * Get
     */
    public function getListData()
    {
        $data = $this->filter(POST, ['search' => 'text']);

        // vars
        $dir    = (new LanguageHelper())->getLocale();
        $cdir   = scandir($dir);
        $rows   = [];

        if ($cdir) {
            $filter = $data['search']
                ? '/' . preg_quote($data['search']) . '/i'
                : '';
            $availables = app('language')->getAvailables();
            $curLang    = app('language')->getCurrent();

            foreach ($cdir as $has) {
                if (
                    $has != '.'
                    && $has != '..'
                    && is_dir($dir . $has)
                    && (!$filter || preg_match($filter, $has))
                ) {
                    $file = $dir . $has . '/' . $has . '.json';
                    $json = is_file($file)
                        ? json_decode(file_get_contents($file), true)
                        : null;

                    $rows[] = [
                        'id'       => $has,
                        'name'     => $json['name'] ?? $has,
                        'selected' => ($has == $curLang ? 'yes' : 'no'),
                        'status'   => in_array($has, $availables) ? 'enabled' : 'disabled',
                    ];
                }
            }
        }

        foreach ($rows as $i => $row) {
            $rows[$i]['__labels'] = [];

            if ($row['status'] == 'enabled') {
                $rows[$i]['__labels'][] = 'enabled';
            }
        }


        return $data + ['rows' => $rows];
    }


    /**
     * Get
     */
    public function getEditData()
    {
        $data = $this->filter(POST, ['id' => 'array:first|required:abort']);

        // query
        $locale = (new LanguageHelper)->getLocale();
        $json   = $locale . $data['id'] . '/' . $data['id'] . '.json';
        $json   = is_file($json)
            ? json_decode(file_get_contents($json), true)
            : false;

        // security
        $json or abort();
        $json['language'] = $data['id'];

        return [
            'title' => _t('Edit'),
            'values' => $json,
        ];
    }

    /**
     * Get
     */
    public function getConfirmDuplicateData()
    {
        $data = $this->filter(POST, ['id' => 'array:first|required:abort']);

        // query
        is_dir((new LanguageHelper)->getLocale() . $data['id']) or abort();

        return ['language' => $data['id']];
    }

    /**
     * Get
     */
    public function getConfirmSelectData()
    {
        return $this->filter(POST, ['id' => 'array:first|required:abort']);
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        return $this->filter(POST, ['id' => 'array|required:abort']);
    }

    /**
     * Get
     */
    public function getConfirmDistributeData()
    {
        return $this->filter(POST, ['id' => 'array:first|required:abort']);
    }
}
