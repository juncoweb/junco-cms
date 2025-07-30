<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Http\Client;
use Junco\Filesystem\UploadedFileManager;

class LanguageModel extends Model
{
    // vars
    protected $db;
    protected string $language = '';


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Duplicate
     */
    public function duplicate()
    {
        // data
        $this->filter(POST, [
            'language'    => 'text|required:abort',
            'language_to' => 'text',
        ]);

        // validate
        if (!$this->data['language_to']) {
            return $this->unprocessable(_t('Please, fill in the key.'));
        }

        // vars
        $locale = (new LanguageHelper)->getLocale();
        $fs = new Filesystem($locale);

        if (!$fs->copy($this->data['language'], $this->data['language_to'])) {
            return $this->unprocessable(_t('Error! the task has not been realized.'));
        }

        $fs->rename(
            $this->data['language_to'] . '/' . $this->data['language'] . '.json',
            $this->data['language_to'] . '/' . $this->data['language_to'] . '.json'
        );
    }

    /**
     * export
     */
    public function export()
    {
        // data
        $this->filter(GET, ['id' => 'array:first|required:abort']);

        return $this->getArchiveFile($this->data['id']);
    }

    /**
     * import
     */
    public function import()
    {
        // data
        $this->filter(POST, ['file' => 'archive|required']);

        $locale = (new LanguageHelper)->getLocale();
        $this->data['file']
            ->setBasedir()
            ->moveTo($locale, UploadedFileManager::DEFAULT_NAME, true)
            ->extract(true);

        (new LanguageHelper)->refresh();
    }

    /**
     * Save
     */
    public function save()
    {
        // data
        $this->filter(POST, [
            'language' => 'text|required:abort',
            'name'     => 'text|required',
        ]);

        // extract
        $this->extract('language');

        $locale = (new LanguageHelper)->getLocale();
        $file   = $locale . $this->language . '/' . $this->language . '.json';
        $buffer = json_encode($this->data, JSON_PRETTY_PRINT);

        if (false === file_put_contents($file, $buffer)) {
            return $this->unprocessable(_t('Error! the task has not been realized.'));
        }
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['language' => 'array|required:abort']);

        // vars
        $locale = (new LanguageHelper)->getLocale();
        $fs     = new Filesystem($locale);

        foreach ($this->data['language'] as $language) {
            $fs->remove($language);
        }
    }

    /**
     * Select
     */
    public function select()
    {
        // data
        $this->filter(POST, ['lang' => 'text']);

        (new LanguageHelper)->change($this->data['lang']);
    }

    /**
     * status
     */
    public function status()
    {
        // data
        $this->filter(POST, ['id' => 'array:first|required:abort']);

        // security
        if ($this->data['id'] == app('language')->getCurrent()) {
            return $this->unprocessable(_t('The key is being used.'));
        }

        $availables = config('language.availables') ?: [];
        if (in_array($this->data['id'], $availables)) {
            $availables = array_diff($availables, [$this->data['id']]);
        } else {
            $availables[] = $this->data['id'];
        }

        (new Settings('language'))->update(['availables' => array_values($availables)]);
    }

    /**
     * Distribute
     */
    public function distribute()
    {
        // data
        $this->filter(POST, ['language' => 'text|required:abort']);

        $config = config('language-distribute');
        if (!$config['language-distribute.token']) {
            return $this->unprocessable(_t('The distribution system requires a token.'));
        }
        if (!$config['language-distribute.url']) {
            return $this->unprocessable(_t('The distribution system requires a url.'));
        }
        if (!set_time_limit(0)) { // set time limit
            return $this->unprocessable('Error (time_limit)');
        }

        // vars
        $archive  = $this->getArchiveFile($this->data['language']);
        $url      = $config['language-distribute.url'];
        $file     = $archive['file'];

        $response = (new Client)
            ->post($url, [
                'multipart'  => [
                    ['name' => 'token', 'contents' => $config['language-distribute.token']],
                    ['name' => 'file', 'file' => $file],
                ]
            ]);

        $code = (string)$response->getBody();

        // remove
        unlink($file);

        if (!$code) {
            return $this->unprocessable(_t('Error! the task has not been realized.'));
        }
    }

    /**
     * Get
     */
    protected function getArchiveFile(string $language): array
    {
        // vars
        $locale   = (new LanguageHelper)->getLocale();
        $filename = $language . '.zip';
        $file     = app('system')->getTmpPath() . $filename;

        // compress
        (new Archive(''))->compress($file, $locale, [$language]);

        // return
        return ['file' => $file, 'filename' => $filename];
    }
}
