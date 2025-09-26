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
        $data = $this->filter(GET, ['key' => 'text|required:abort']);

        // security
        (new AssetsVariables)->isTheme($data['key']) or redirect(['admin/assets.themes']);

        return $data + [
            'title' => $data['key']
        ];
    }

    /**
     * Get
     */
    public function getListData()
    {
        $data = $this->filter(POST, [
            'key' => 'required:abort',
            'search' => 'text',
        ]);

        return $data + [
            'rows' => (new AssetsVariables)->getSources($data['search'])
        ];
    }

    /**
     * Get
     */
    public function getEditData()
    {
        $data = $this->filter(POST, [
            'key' => 'required:abort',
            'id' => 'array:first',
        ]);

        // vars
        $variables = (new AssetsVariables)->getData(
            $data['id'],
            $data['key']
        );
        $values = [
            'key' => $data['key'],
            'file' => $data['id']
        ];

        //
        $i = 0;
        foreach ($variables as $k => $row) {
            $variables[$k]['type'] = $this->getElementType($row['default']);
            $values += [
                'variables[' . $i . '][value]'   => $row['value'],
                'variables[' . $i . '][default]' => $row['default'],
                'variables[' . $i . '][name]'    => $row['name']
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
        $data = $this->filter(POST, [
            'key'       => 'text|required:abort',
            'file'      => 'text|required:abort',
            'variables' => 'array',
        ]);

        (new AssetsVariables)->update(
            $data['key'],
            $data['file'],
            $data['variables']
        ) or abort();
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
