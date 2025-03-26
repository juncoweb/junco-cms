<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class DatabaseImporter
{
    // vars
    protected  $adapter    = [];

    // settings
    public     $abspath                        = '';
    public     $drop_nonexistent_columns    = false; // only jschema

    /**
     * Import from directory
     *
     * @param string $dir  The directory path
     */
    public function fromDir($dir)
    {
        foreach ($this->scandir($this->abspath . $dir) as $file) {
            $this->fromFile($file);
        }
    }

    /**
     * Import from file
     *
     * @param string $file the file path
     */
    public function fromFile($file)
    {
        if (!is_file($this->abspath . $file)) {
            return;
        }

        $content = file_get_contents($this->abspath . $file);

        if (!$content) {
            throw new Exception(_t('The file is empty or could not be read.'));
        }

        $this->fromContent($content, pathinfo($file, PATHINFO_EXTENSION));
    }

    /**
     * Import from file
     *
     * @param string $content
     * @param string $type
     */
    public function fromContent(string $content, string $type = '')
    {
        if ($type != 'json') {
            $type = 'sql';
        }

        $this->adapter[$type] ??= $this->getAdapter($type);
        $this->adapter[$type]->import($content);
    }

    /**
     * Scandir
     * 
     * @param string $dir
     * 
     * @return array
     */
    protected function scandir(string $dir): array
    {
        $rows = [];
        $cdir = is_readable($dir) ? scandir($dir) : false;

        if ($cdir) {
            $dir = rtrim($dir, '\\/') . '/';
            foreach ($cdir as $elem) {
                if (
                    $elem != '.'
                    && $elem != '..'
                    && is_file($dir . $elem)
                ) {
                    $rows[] = $elem;
                }
            }
        }

        return $rows;
    }

    /**
     * Get
     * 
     * @param string $type
     */
    protected function getAdapter(string $type)
    {
        switch ($type) {
            case 'json':
                $adapter = new Junco\Database\Importer\JSDataImporter();
                $adapter->drop_nonexistent_columns = $this->drop_nonexistent_columns;
                return $adapter;

            case 'sql':
                return new Junco\Database\Importer\MysqlImporter();
        }
    }
}
