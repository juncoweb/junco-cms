<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class SamplesModel extends Model
{
    // vars
    protected $samples;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->samples = new Samples;
    }

    /**
     * Get
     */
    public function getListData()
    {
        $data = $this->filter(POST, [
            'search' => 'text',
            'field' => 'id|max:2',
        ]);

        return $data + [
            'rows' => $this->samples->fetchAll($data['search'], $data['field'])
        ];
    }

    /**
     * Get
     */
    public function getShowData()
    {
        $data = $this->filter(GET, ['key' => 'required:abort']);

        //
        $file = $this->samples->getFileFromKey($data['key']);

        is_file($file)
            or alert('File not found: ' . $file);

        define('IS_TEST', true);
        return include $file;
    }

    /**
     * Get
     */
    public function getEditData()
    {
        $data = $this->filter(POST, ['id' => 'array:first']);

        return [
            'values' => $this->samples->fetch($data['id'])
        ];
    }

    /**
     * Update
     */
    public function update()
    {
        $data = $this->filter(POST, [
            'key'         => '',
            'title'       => 'text',
            'description' => 'multiline',
            'image'       => 'text',
        ]);

        $this->samples->save($data['key'], $data);
    }
}
