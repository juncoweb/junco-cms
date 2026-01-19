<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Archive\Adapter;

use \ZipArchive;
use \Exception;

class ZipAdapter implements AdapterInterface
{
    // vars
    protected $zip = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!extension_loaded('zip')) {
            throw new Exception(_t('The Archive class has no support to execute the task.'));
        }

        $this->zip = new ZipArchive();
    }

    /**
     * Extract
     *
     * @param string $file		The full path to the file.
     * @param string $dir		The directory where the package will be extracted.
     * @param bool   $delete	Option to delete the compressed file.
     *
     * @throws Exception
     */
    public function extract(string $file, string $dir): void
    {
        if ($this->zip->open($file) !== true) {
            throw new Exception(_t('The package could not be opened.'));
        }
        if (!$this->zip->extractTo($dir)) {
            throw new Exception(_t('The archive could not be extracted.'));
        }
        if (!$this->zip->close()) {
            throw new Exception(_t('The archive could not be closed.'));
        }
    }

    /**
     * Compress
     * 
     * @param string $file		The full path to the file to be created.
     * @param string $dir		The base directory.
     * @param array  $nodes		Select only some directories or files from the base directory. 
     */
    public function compress(string $file, string $dir, array $nodes): void
    {
        if ($this->zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception(_t('The package could not be opened.'));
        }

        $this->addNodes($dir, $nodes);

        if (!$this->zip->close()) {
            throw new Exception(_t('The archive could not be closed.'));
        }
    }

    /**
     * Add Nodes
     * 
     * @param string $dir
     * @param array  $nodes
     */
    protected function addNodes(string $dir, array $nodes): void
    {
        if ($nodes) {
            foreach ($nodes as $node) {
                //$node  = trim($node, '\\/');
                $cnode = $dir . $node;

                if (is_dir($cnode)) {
                    $this->addDir($cnode . '/', $node . '/');
                } elseif (is_file($cnode)) {
                    $this->zip->addFile($cnode, $node);
                }
            }
        } elseif (is_dir($dir)) {
            $this->addDir($dir . '/');
        }
    }

    /**
     * Add directory
     * 
     * @param string $dir
     * @param string $localname
     */
    protected function addDir(string $dir, string $localname = ''): void
    {
        if ($localname) {
            $this->zip->addEmptyDir($localname);
        }

        foreach ($this->scandir($dir) as $node) {
            $cnode = $dir . $node;

            if (is_dir($cnode)) {
                $this->addDir($cnode . '/', $localname . $node . '/');
            } else if (is_file($cnode)) {
                $this->zip->addFile($cnode, $localname . $node);
            }
        }
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
        $cdir = is_readable($dir) ? scandir($dir) : false;
        if ($cdir) {
            return array_diff($cdir, ['.', '..']);
        }
        return [];
    }
}
