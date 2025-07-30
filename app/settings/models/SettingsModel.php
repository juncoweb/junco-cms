<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class SettingsModel extends Model
{
    // vars
    protected $db;
    protected string $key = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Set
     */
    public function set()
    {
        // data
        $this->filter(POST, [
            '__key'  => '',
            'unlock' => 'array',
        ]);

        // vars
        $settings = new Settings($this->data['__key']);

        // security
        $settings->security() or abort();

        // vars
        $data             = $settings->getData();
        $__data           = request()->getParsedBody(); // It's a horror!
        $rows             = [];
        $set_autoload     = false;
        $save_data        = false;
        $reload_on_change = false;
        $translate        = [];
        $developer_mode   = SYSTEM_DEVELOPER_MODE;

        //
        foreach (array_keys($data['rows']) as $k) {
            $rows[$k] = $__data[$k] ?? null;
        }

        # plugins
        Plugins::get('settings', 'update', str_replace('-', '.', $this->data['__key']))?->run($rows);

        foreach ($rows as $k => $value) {
            // security
            if (
                !isset($data['rows'][$k])
                || (!$developer_mode && !$data['rows'][$k]['status']) // If you are not in developer mode, reject settings that are not part of developer mode.
            ) {
                unset($rows[$k]);
                continue;
            }

            // type
            switch ($data['rows'][$k]['type']) {
                // integer
                case 'input-integer':
                case 'select-integer':
                    $value = (int)$value;
                    break;

                case 'select-multiple-integer':
                    $value = array_map(function ($value) {
                        return (int)$value;
                    }, $this->parseArray($value));
                    break;

                // text
                case 'input-text':
                    if ($data['rows'][$k]['translate'] && $value) {
                        $translate[] = $value;
                    }
                    break;

                // boolean
                case 'boolean':
                    $value = (bool)$value;
                    break;

                // boolean
                case 'list':
                    $value = $this->parseArray($value);
                    break;

                // json
                case 'json':
                    if ($value && is_string($value)) {
                        $value = json_decode($value, true);

                        if (null === $value) {
                            return $this->unprocessable(sprintf('The value «%s» is not a valid Json.', $k));
                        }
                    }
                    break;

                case 'plugins':
                case 'select-multiple-text':
                    $value = $this->parseArray($value);
                    break;

                case 'snippet':
                    if (!$value) {
                        $value = 'default';
                    }
                    break;
            }

            // reload_on_change
            if (
                $data['rows'][$k]['reload_on_change']
                && $data['rows'][$k]['value'] != $value
            ) {
                $reload_on_change = true;
            }

            $data['rows'][$k]['value'] = $value;

            // unlock
            if (
                $developer_mode
                && $this->data['unlock']
                && $data['rows'][$k]['default_value'] != $value
                && in_array($k, $this->data['unlock'])
            ) {
                $data['rows'][$k]['default_value'] = $value;
                $save_data = true;
            }

            if ($data['rows'][$k]['autoload']) {
                $set_autoload = true;
            } else {
                $save_data = true;
            }
        }

        //  write
        if ($save_data) {
            $settings->save($data);
        }

        if ($set_autoload) {
            $settings->set($data);
        }

        if ($translate) {
            $settings->translate($translate, true);
        }

        return $this->result()->setCode($reload_on_change ? 2 : 1);
    }

    /**
     * Save
     */
    public function save()
    {
        // data
        $this->filterArray(POST, [
            'id'               => '',
            'name'             => '',
            'label'            => 'text|required',
            'type'             => '',
            'group'            => 'int',
            'ordering'         => 'int',
            'help'             => 'text',
            'history'          => 'text',
            'autoload'         => 'int',
            'translate'        => 'int',
            'reload_on_change' => 'int',
            'status'           => 'int',
            'delete'           => 'int',
        ]);

        $this->filter(POST, [
            'key'         => 'text',
            'title'       => 'text',
            'description' => 'text',
            'groups'      => 'text',
        ]);

        // validate
        if (!$this->data_array) {
            return $this->unprocessable(_t('Please, add a row before proceeding.'));
        }

        // extract
        $this->extract('key');

        $settings = new Settings($this->key);
        $settings->security() or abort();

        //
        $data = $settings->getData();
        $this->data['groups']        = $this->parseArray($this->data['groups'], '|');
        $this->data['descriptions'] = $this->getDescriptions($this->data['description']);
        $this->data['rows']            = [];

        // translate
        $translate = $this->data['groups'] ?: [];
        foreach (['title', 'description'] as $k) {
            if ($this->data[$k]) {
                $translate[] = $this->data[$k];
            }
        }
        foreach ($this->data['descriptions'] as $value) {
            if ($value) {
                $translate[] = $value;
            }
        }

        //
        foreach ($this->data_array as $row) {
            $row['id'] or abort();

            if ($row['delete']) {
                continue;
            }

            $row['name']    = $this->sanitizeName($row['name'] ?: $row['label']);
            $row['history'] = $this->getHistory($row);

            $old_row = $data['rows'][$row['id']] ?? false;
            $new_row = [
                'label'            => $row['label'],
                'type'             => $row['type'],
                'group'            => $row['group'],
                'ordering'         => $row['ordering'],
                'value'            => $old_row['value'] ?? '',
                'default_value'    => $old_row['default_value'] ?? '',
                'help'             => $row['help'],
                'history'          => $row['history'],
                'autoload'         => $row['autoload'],
                'translate'        => $row['translate'],
                'reload_on_change' => $row['reload_on_change'],
                'status'           => $row['status'],
            ];

            // translate
            foreach (['label', 'help'] as $k) {
                if ($new_row[$k]) {
                    $translate[] = $new_row[$k];
                }
            }

            $this->data['rows'][$row['name']] = $new_row;
        }

        $settings->save($this->data);
        $settings->set($this->data);
        $settings->translate($translate);
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['key' => 'text|required:abort']);

        (new Settings($this->data['key']))->delete();
    }

    /**
     * Parse
     * 
     * @param string|array	$value
     * @param string		$separator
     * 
     * @return array
     */
    protected function parseArray(string|array|null $value = null, string $separator = ','): array
    {
        if (!$value) {
            return [];
        }
        if (is_array($value)) {
            return $value;
        }
        return array_map('trim', explode($separator, $value));
    }

    /**
     * Extracts from the main description, the group descriptions
     * 
     * @param string &$description
     * 
     * @return array $descriptions
     */
    protected function getDescriptions(string &$description): array
    {
        if ($description) {
            $descriptions = explode('|', $description);
            $description = array_shift($descriptions);
            array_map('trim', $descriptions);
        } else {
            $descriptions = [];
        }

        return $descriptions;
    }

    /**
     * Sanitize
     */
    protected function sanitizeName(string $name): string
    {
        return str_replace(' ', '_', strtolower($name));
    }

    /**
     * History
     */
    protected function getHistory(array $row): array
    {
        $history = $this->parseArray($row['history']);
        if (
            $row['id'] != $row['name']
            && !preg_match('/^new_\d+$/', $row['id'])
            && !in_array($row['id'], $history)
        ) {
            $history[] = $row['id'];
        }
        if (in_array($row['name'], $history)) {
            unset($history[array_search($row['name'], $history)]);
        }

        return $history;
    }
}
