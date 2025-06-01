<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminSettingsModel extends Model
{
    // vars
    protected $db = null;


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
        // data
        $this->filter(GET, ['key' => '']);

        return $this->data;
    }

    /**
     * Get
     */
    public function getPrepareData()
    {
        // data
        $this->filter(POST, ['key' => '']);

        $extension = explode('-', $this->data['key'], 2);

        return [
            'extensions' => $this->getExtensions(),
            'values' => [
                'add_rows' => 1,
                'extension' => $extension[0],
                'sub_extension' => $extension[1] ?? '',
            ],
        ];
    }

    /**
     * Get
     */
    public function getEditData()
    {
        // data
        $this->filter(POST, [
            'key'            => '',
            'extension'        => '',
            'sub_extension' => '',
            'add_rows'        => 'int',
        ]);

        if (!$this->data['key']) {
            $this->data['key'] = $this->data['extension'];
            if ($this->data['sub_extension']) {
                $this->data['key'] .= '-' . $this->data['sub_extension'];
            }
        }

        // vars
        $settings = new Settings($this->data['key']);

        // security
        $settings->security() or abort();

        //
        $data = $settings->getData();
        if ($this->data['add_rows']) {
            $this->append($data, $this->data['add_rows']);
        }

        $data['key']    = $this->data['key'];
        $data['groups'] = implode('|', $data['groups']);
        array_unshift($data['descriptions'], $data['description']);
        $data['description'] = implode("\n|", $data['descriptions']);

        foreach (array_keys($data['rows']) as $name) {
            $data['rows'][$name]['id'] = $name;
            $data['rows'][$name]['name'] = $name;
            $data['rows'][$name]['history'] = implode(',', $data['rows'][$name]['history']);
        }

        return ['data' => $data];
    }

    /**
     * Get
     */
    public function getJsonData()
    {
        // data
        $this->filter(POST, [
            'options' => '',
            'json' => 'json:decode_a',
        ]);

        // vars
        $is_edit = !empty($json);

        if ($this->data['options']) {
            if (is_string($this->data['options'])) {
                $this->data['options'] = explode(',', $this->data['options']);
            }
            $count = 0;

            if ($is_edit) {
                foreach ($json as $index => $row) {
                    $json[$index]['deep']   = "[$count]";
                    $json[$index]['values'] = ['__id' => $index];
                    //$json[$index]['name'] = is_numeric($index) ? "[$index]" : $index;

                    foreach ($this->data['options'] as $option) {
                        if (isset($row[$option])) {
                            $json[$index]['values'][$option] = $row[$option];
                        }
                    }
                    $count++;
                }
            } else {
                $json[0]['deep']    = "[0]";
                $json[0]['values']    = ['__id' => null];
            }
        } else {
            if ($is_edit) {
                foreach ($json as $index => $value) {
                    $json[$index] = [
                        'name' => is_numeric($index) ? "[$index]" : $index,
                        'values' => [$index => (is_array($value) ? htmlentities(stripcslashes(json_encode($value))) : $value)]
                    ];
                }
            }
        }

        return [
            'title' => $is_edit ? _t('Edit') : _t('Create'),
            'is_edit' => $is_edit,
            'json' => $json,
            'options' => $this->data['options'],
        ];
    }

    /**
     * Get
     */
    public function getConfirmDeleteData()
    {
        // data
        $this->filter(POST, ['key' => '']);

        return ['key' => $this->data['key']];
    }

    /**
     * Get
     */
    protected function getExtensions()
    {
        return $this->db->safeFind("
		SELECT
		 extension_alias
		FROM `#__extensions`")->fetchAll(Database::FETCH_COLUMN, [0 => 0]);
    }

    /**
     * Append
     */
    protected function append(array &$data, int $total)
    {
        $i = 0;
        while ($total) {
            $name = 'new_' . (++$i);
            if (!isset($data['rows'][$name])) {
                $data['rows'][$name] = [
                    'label'                => $name,
                    'group'                => 99,
                    'ordering'            => 99,
                    'type'                => 'text',
                    'help'                => '',
                    'history'            => [],
                    'autoload'            => 1,
                    'translate'            => 0,
                    'reload_on_change'    => 0,
                    'status'            => 1,
                ];
                --$total;
            }
        }
    }
}
