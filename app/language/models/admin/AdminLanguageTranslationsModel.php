<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Http\Client;

class AdminLanguageTranslationsModel extends Model
{
    /**
     * Get
     */
    public function getIndexData()
    {
        $data = $this->filter(POST, ['id' => 'array:first']);

        return [
            'data' => ['search' => $data['id']]
        ];
    }

    /**
     * Get
     */
    public function getListData()
    {
        $data = $this->filter(POST, [
            'search' => 'text',
            'page'   => 'id',
        ]);

        try {
            $json = $this->query($data);
        } catch (Exception $e) {
            return ['error' => $e->getMessage() ?: 'Error!'];
        }

        // query
        $pagi = new Pagination();
        $pagi->num_rows = $json['num_rows'];
        $pagi->rows_per_page = $json['rows_per_page'];
        $pagi->calculate();

        return $data + [
            'pagi' => $pagi,
            'rows' => $json['rows']
        ];
    }

    /**
     * Get
     */
    public function getDownloadData()
    {
        return $this->filter(POST, ['id' => 'id|array:first|required:abort']);
    }

    /**
     * download
     */
    public function download()
    {
        $data = $this->filter(POST, ['id' => 'id|required:abort']);

        // vars
        $locale = (new LanguageHelper)->getLocale();
        $url    = sprintf('https://www.juncoweb.com/translations/download?id=%d&format=blank', $data['id']);

        (new Client)
            ->get($url)
            ->moveTo($locale, $filename);

        (new Archive($locale))->extract($filename, '', true);
        (new LanguageHelper)->refresh();
    }

    /**
     * Get
     */
    protected function query(array $data): array
    {
        $url  = 'https://www.juncoweb.com/index.php';
        $args = [
            'goto' => 'translations/json',
            'format' => 'json'
        ];

        if ($data['search']) {
            $args['search'] = $data['search'];
        }

        if ($data['page']) {
            $args['page'] = $data['page'];
        }

        $content = (new Client)
            ->get($url, ['data' => $args])
            ->getBody();

        $json = json_decode($content, true);

        if (!empty($json['__alert'])) {
            throw new Exception($json['__alert']);
        }

        return $json;
    }
}
