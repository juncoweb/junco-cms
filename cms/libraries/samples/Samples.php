<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Samples
{
    /**
     * Get
     */
    public function fetchAll(string $search = '', int $field = 0): array
    {
        $pattern = $this->keyInfo('*.*')['file'];
        $files = glob($pattern);

        if (!$files) {
            return [];
        }

        $rows        = [];
        $regex_1    = '';
        $regex_2    = '';
        $url        = url('admin/samples/show', ['key' => '%s']);

        if ($search) {
            switch ($field) {
                case 1:
                    $regex_1 = '@' . preg_quote($search, '@') . '@i';
                    break;
                case 2:
                    if (preg_match('@^[\w]+$@', $search)) {
                        $regex_2 = '@' . $search . '@i';
                    }
                    break;
            }
        }

        foreach ($files as $file) {
            $info        = pathinfo($file);
            $extension    = basename(dirname($file, 3));

            if ($regex_2 && !preg_match($regex_2, $extension)) {
                continue;
            }

            $row = $this->read($extension . '.' . basename($info['dirname']));

            if ($regex_1 && !preg_match($regex_1, $row['title'])) {
                continue;
            }

            $row['url'] = sprintf($url, $row['key']);
            $rows[] = $row;
        }


        return $rows;
    }

    /**
     * Fetch
     * 
     * @param string $key
     */
    public function fetch(string $key)
    {
        return $this->read($key);
    }

    /**
     * Get file
     * 
     * @param string $key
     */
    public function getFileFromKey(string $key): string
    {
        return $this->keyInfo($key)['file'];
    }
    /**
     * Save
     * 
     * @param string $key
     */
    public function save(string $key, array $data)
    {
        // security
        if (isset($data['key'])) {
            unset($data['key']);
        }

        $file   = $this->keyInfo($key)['json'];
        $buffer    = json_encode($data, JSON_PRETTY_PRINT);

        //
        if (false === file_put_contents($file, $buffer)) {
            throw new Exception(_t('Error! the task has not been realized.'));
        }
    }

    /**
     * Get
     * 
     * @param string $key
     */
    protected function keyInfo(string $key): array
    {
        $part = explode('.', $key, 3);
        $part[1] ??= 'default';
        $part[2] ??= 'sample';

        return [
            'extension' => $part[0],
            'name' => $part[1],
            'point' => $part[2],
            'file' => SYSTEM_ABSPATH . sprintf('cms/plugins/%s/sample/%s/%s.php', $part[0], $part[1], $part[2]),
            'json'    => SYSTEM_ABSPATH . sprintf('cms/plugins/%s/sample/%s/%s.json', $part[0], $part[1], 'sample'),
        ];
    }

    /**
     * Get
     * 
     * @param string $key
     */
    protected function read(string $key)
    {
        $info = $this->keyInfo($key);
        $data = [
            'key'            => $key,
            'extension'        => $info['extension'],
            'title'            => $info['name'],
            'description'    => '',
            'image'            => 'fa-solid fa-gear',
        ];

        if (is_file($info['json'])) {
            $json = file_get_contents($info['json']);

            if ($json) {
                $json = json_decode($json, true);

                if ($json) {
                    if (!empty($json['title'])) {
                        $data['title'] = $json['title'];
                    }
                    if (!empty($json['description'])) {
                        $data['description'] = $json['description'];
                    }
                    if (!empty($json['image'])) {
                        $data['image'] = $json['image'];
                    }
                }
            }
        }

        return $data;
    }


    /**
     * Menu
     * 
     * @param string $extension
     * @param array  $rows
     */
    public function menu(string $extension, array $rows = [])
    {
        // vars
        $groups = [];

        foreach ($rows as $i => $row) {
            if (isset($row['edge'])) {
                foreach ($row['edge'] as $key) {
                    $groups[$key] = $i;
                }
                $rows[$i]['edge'] = [];
            }
        }

        $_rows = $this->fetchAll($extension, 2);

        foreach ($_rows as $row) {
            if (isset($groups[$row['key']])) {
                $rows[$groups[$row['key']]]['edge'][] = $row;
            } else {
                $rows[] = $row;
            }
        }

        array_unshift($rows, [
            'title' => _t('Back'),
            'url' => url('admin/samples'),
            'image' => 'fa-solid fa-arrow-left',
        ]);

        return '<div class="widget-thirdbar" control-tpl="thirdbar">'
            . $this->_menu($rows)
            . '</div>';
    }

    /**
     * 
     */
    protected function _menu($rows)
    {
        $html = '';
        foreach ($rows as $row) {
            $edge = '';
            if (isset($row['edge'])) {
                $edge = $this->_menu($row['edge']);
            }

            $html .= '<li>'
                . '<a href="' . ($row['url'] ?? 'javascript:void(0)') . '">'
                . '<i class="' . $row['image'] . '"></i> '
                . '<span>' . $row['title'] . '</span>'
                . '</a>' . $edge . '</li>';
        }

        return '<ul>' . $html . '</ul>';
    }
}
