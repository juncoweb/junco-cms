<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class AdminAssetsVariablesModel extends Model
{
    /**
     * Get
     */
    public function getIndexData()
    {
        // data
        $this->filter(GET, ['key' => 'text|required:abort']);

        // security
        $this->getVarFile($this->data['key']) or redirect(['admin/assets.themes']);

        return $this->data + [
            'title' => $this->data['key']
        ];
    }

    /**
     * Get
     */
    public function getListData()
    {
        // data
        $this->filter(POST, [
            'key' => 'required:abort',
            'search' => 'text',
        ]);

        // vars
        $quests = [
            ['pattern' => 'app/*/sass/partials/_variables.scss', 'scope' => 'App'],
            ['pattern' => 'cms/scripts/*/sass/partials/_variables.scss', 'scope' => 'Script'],
            ['pattern' => 'cms/plugins/*/*/*/sass/partials/_variables.scss', 'scope' => 'Plugin'],
        ];
        $rows = [];

        foreach ($quests as $quest) {
            $files = glob(SYSTEM_ABSPATH . $quest['pattern']) ?: [];

            foreach ($files as $file) {
                $file = str_replace(SYSTEM_ABSPATH, '', $file);
                $name = $this->getNameOfFile($file, $quest['scope']);
                $rows[] = [
                    'file' => $file,
                    'name' => $name,
                    'scope' => $quest['scope']
                ];
            }
        }

        if ($this->data['search']) {
            $search = '/' . preg_quote($this->data['search'], '/') . '/i';
            $rows = array_filter($rows, function ($row) use ($search) {
                return preg_match($search, $row['name']);
            });
        }

        return $this->data + ['rows' => $rows];
    }

    /**
     * Get
     */
    public function getEditData()
    {
        // data
        $this->filter(POST, [
            'key' => 'required:abort',
            'id' => 'array:first',
        ]);

        // vars
        $varFile    = $this->getVarFile($this->data['key']) or abort();
        $variables    = $this->read($this->data['id'], $varFile);
        $values = [
            'key' => $this->data['key'],
            'file' => $this->data['id']
        ];

        //
        $i = 0;
        foreach ($variables as $k => $row) {
            $variables[$k]['type'] = $this->getElementType($row['default']);
            $values += [
                'variables[' . $i . '][value]' => $row['value'],
                'variables[' . $i . '][default]' => $row['default'],
                'variables[' . $i . '][name]' => $row['name']
            ];
            $i++;
        }

        return [
            'variables' => $variables,
            'values' => $values
        ];
    }

    /**
     * Update
     */
    public function update()
    {
        // data
        $this->filter(POST, [
            'key'        => 'text|required:abort',
            'file'        => 'text|required:abort',
            'variables' => 'array',
        ]);

        // vars
        $varFile    = $this->getVarFile($this->data['key']) or abort();
        $file        = $this->data['file'];
        $blocks        = explode('// ', file_get_contents($varFile));
        $partials    = [];
        $variables    = [];

        foreach ($blocks as $partial) {
            $partial  = trim($partial);
            $index    = preg_split('#(\r|\n)#', $partial, 2);
            $index    = trim($index[0]);

            if ($index) {
                $partials[$index] = $partial . PHP_EOL;
            }
        }

        if (
            isset($partials[$file])
            && preg_match_all('#\$([\w-]+)\s*:\s*([^;]*?)(?:\!default)?;#', $partials[$file], $matches, PREG_SET_ORDER)
        ) {
            foreach ($matches as $match) {
                $variables[$match[1]] = htmlentities(trim($match[2]));
            }
        }

        // read
        foreach ($this->data['variables'] as $row) {
            if ($row['value'] && $row['value'] != $row['default']) {
                $variables[$row['name']] = $row['value'];
            } elseif (isset($variables[$row['name']])) {
                unset($variables[$row['name']]);
            }
        }

        // generate and save the new partial
        if ($variables) {
            $buffer = '';
            foreach ($variables as $name => $value) {
                $buffer .= '$' . $name . ': ' . $value . ' !default;' . PHP_EOL;
            }

            $partials[$file] = $file . PHP_EOL . $buffer;
        } elseif (isset($partials[$file])) {
            unset($partials[$file]);
        }

        ksort($partials);
        $buffer = $partials ? '// ' . implode('// ', $partials) : '';

        // save
        file_put_contents($varFile, $buffer);
    }

    /**
     * Get
     * 
     * @param string $file
     * 
     * @return ?string
     */
    protected function getNameOfFile(string $file, string $scope): ?string
    {
        switch ($scope) {
            case 'App':
                preg_match('#app\/(.*)?\/sass\/partials\/_variables\.scss#', $file, $match);
                return $match[1];

            case 'Script':
                preg_match('#cms\/scripts\/(.*)?\/sass\/partials\/_variables\.scss#', $file, $match);
                return $match[1];

            case 'Plugin':
                preg_match('#cms\/plugins\/(.*)?\/(.*)?\/(.*)?\/sass\/partials\/_variables\.scss#', $file, $match);
                return $match[1] . '.' . $match[2] . '.' . $match[3];
        }

        return null;
    }

    /**
     * Get
     * 
     * @param string $key
     * 
     * @return ?string
     */
    protected function getVarFile(string $key): ?string
    {
        $file = (new AssetsThemes)->getScssVarFile($key);

        return is_readable($file) ? $file : null;
    }

    /**
     * Read
     * 
     * @param string $file
     */
    protected function read(string $file, string $varFile)
    {
        // vars
        $file        = SYSTEM_ABSPATH . $file;
        $info        = pathinfo($file);
        $variables    = [];

        // default
        if (
            is_file($file)
            && isset($info['extension'])
            && $info['extension'] == 'scss'
            && substr($info['basename'], 0, 10) == '_variables'
        ) {
            $contents = file_get_contents($file);

            if (preg_match_all('#\$([\w-]+)\s*:\s*([^;]*?)(?:\!default)?;#', $contents, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $variables[$match[1]] = [
                        'name'        => $match[1],
                        'value'        => false,
                        'default'    => htmlentities(trim($match[2])),
                    ];
                }
            }
        } else {
            abort();
        }

        // custom
        $contents = file_get_contents($varFile);

        if (preg_match_all('#\$([\w-]+)\s*:\s*(.*?)(?:\!default)?;#m', $contents, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if (isset($variables[$match[1]])) {
                    $variables[$match[1]]['value'] = htmlentities(trim($match[2]));
                }
            }
        }

        return $variables;
    }

    /**
     * Get
     * 
     * @param string $value
     * 
     * @return string
     */
    protected function getElementType(string $value): string
    {
        return (false !== strpos($value, "\r") || false !== strpos($value, "\n"))
            ? 'textarea'
            : 'input';
    }
}
