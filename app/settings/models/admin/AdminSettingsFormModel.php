<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Settings\SettingType;

class AdminSettingsFormModel extends Model
{
    protected array $snippets = [];
    protected array $plugins  = [];

    /**
     * Get
     */
    public function getFormData()
    {
        $input = $this->filter(GET, ['key' => 'text']);

        //
        $settings = new Settings($input['key']);

        if (!$settings->security()) {
            if ($input['key']) {
                return ['error' => true];
            }

            return ['home' => true];
        }

        //
        $developer_mode = SYSTEM_DEVELOPER_MODE;
        $stm = new SettingType;
        $data = $settings->getData(true);
        $output = [
            'developer_mode' => $developer_mode,
            'title'          => $data['title'] ? _t($data['title']) : ucfirst($input['key']),
            'description'    => $data['description'],
            'warning'        => $data['warning'],
            'keys'           => $this->getKeys($settings, $input['key']),
            'values'         => ['__key' => $input['key']],
            'groups'         => [],
            'restore'        => '',
        ];
        $restore = [];

        // prepare groups
        foreach ($data['rows'] as $name => $row) {
            if (!$row['status'] && !$developer_mode) {
                continue;
            }

            $row['name'] = $name;

            // help
            if (!empty($row['alter_help'])) {
                $row['help'] = $row['alter_help'];
            } elseif ($row['help']) {
                $row['help'] = _t($row['help']);
            }

            switch ($row['type']) {
                case 'snippet';
                    $row['options'] = $this->getSnippets(
                        $this->resolveName($row['snippet'] ?? null, $row['name'], $input['key'])
                    );

                    if (!$row['help']) {
                        $row['help'] = _t('Select a snippet to display.');
                    }
                    break;

                case 'plugin':
                    $row['options'] = $this->getPlugin(
                        $this->resolveName($row['plugin'] ?? null, $row['name'], $input['key'])
                    );

                    if (!$row['help']) {
                        $row['help'] = _t('The plugins allow additional functions.');
                    }
                    break;

                case 'plugins':
                    $row['options'] = $this->getPlugins(
                        $this->resolveName($row['plugins'] ?? null, $row['name'], $input['key'])
                    );

                    if (!$row['help']) {
                        $row['help'] = _t('The plugins allow additional functions.');
                    }
                    break;

                case 'select-multiple-integer':
                case 'select-multiple-text':
                case 'select-integer':
                case 'select-text':
                    $this->sanitizeArray($row['options']);

                    if (!$row['help']) {
                        $row['help'] = _t('Select a list item.');
                    }
                    break;

                case 'list':
                    $row['value']         = $this->getArrayValue($row['value']);
                    $row['default_value'] = $this->getArrayValue($row['default_value']);
                    break;

                case 'json':
                    $this->sanitizeArray($row['options']);

                    if ($row['options'] !== null) {
                        $row['options'] = $this->getArrayValue($row['options']);
                    }

                    $row['value']         = $this->getJsonValue($row['value']);
                    $row['default_value'] = $this->getJsonValue($row['default_value']);
                    break;

                case 'input-range':
                    $row['min'] ??= 0;
                    $row['max'] ??= 100;
                    break;

                case 'image-resizes':
                    if (!$row['help']) {
                        $row['help'] = _t('An object that stores the data of the resizes.');
                    }
                    break;
            }

            // restore
            if (($row['value'] != $row['default_value']) && $stm->isRestorable($row['type'], $row['options'] ?? null)) {
                $restore[$row['name']] = $row['default_value'];
                $row['restore'] = true;
            } else {
                $row['restore'] = false;
            }

            $output['groups'][$row['group']] ??= [
                'legend'      => !empty($data['groups'][$row['group']]) ? _t($data['groups'][$row['group']]) : '',
                'description' => !empty($data['descriptions'][$row['group']]) ? _t($data['descriptions'][$row['group']]) : '',
                'rows'        => []
            ];
            $output['groups'][$row['group']]['rows'][] = $row;
            $output['values'][$row['name']] = $row['value'];
        }

        if ($restore) {
            $output['restore'] = json_encode($restore);
        }

        return $output;
    }

    /**
     * Get
     */
    protected function getKeys(Settings $settings, string $curKey): array
    {
        $rows = $settings->getAllData();

        if (count($rows) < 2) {
            return [];
        }

        $url = url('admin/settings/', ['key' => '%s']);

        foreach ($rows as $key => $data) {
            $rows[$key] = [
                'label' => $data['title'] ?? $key,
                'url' => sprintf($url, $key),
                'selected' => ($key === $curKey)
            ];
        }

        return $rows;
    }

    /**
     * Get
     */
    protected function getSnippets(string $name): array
    {
        return $this->snippets[$name] ??= SystemHelper::scanSnippets($name);
    }

    /**
     * Get
     */
    protected function getPlugin(string $name): array
    {
        $this->plugins[$name] ??= SystemHelper::scanPlugins($name);

        return array_merge(['' => _t('None')], $this->plugins[$name]);
    }

    /**
     * Get
     */
    protected function getPlugins(string $name): array
    {
        return $this->plugins[$name] ??= SystemHelper::scanPlugins($name);
    }

    /**
     * Get
     */
    protected function resolveName(?string $customName, string $name, string $key): string
    {
        if ($customName) {
            return $customName;
        }

        if ($name == $customName) {
            return $key;
        }

        $pos = strpos($name, '_');

        return $pos
            ? substr($name, 0, $pos)
            : $name;
    }

    /**
     * 
     */
    protected function sanitizeArray(mixed &$value = null): void
    {
        if (!is_array($value)) {
            $value = null;
        }
    }

    /**
     * Get
     */
    protected function getArrayValue(mixed $value): string
    {
        return $value && is_array($value) ? implode(',', $value) : '';
    }

    /**
     * Get
     */
    protected function getJsonValue(mixed $value): string
    {
        return $value && is_array($value)
            ? json_encode($value, JSON_UNESCAPED_SLASHES)
            : '';
    }
}
