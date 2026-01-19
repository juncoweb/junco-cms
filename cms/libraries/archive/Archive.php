<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Archive
{
    // vars
    protected $abspath = '';
    protected $adapter = [];

    /**
     * Constructor
     */
    function __construct(?string $abspath = null)
    {
        $this->abspath = $abspath === null ? SYSTEM_ABSPATH : $abspath;
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
    public function extract(string $file, string $dir = '', bool $delete = false): void
    {
        $file = $this->abspath . $file;
        $dir = $this->abspath . $dir;

        $this->getAdapter($file)->extract($file, $dir);

        $delete and unlink($file);
    }

    /**
     * Compress
     * 
     * @param string $file		The full path to the file to be created.
     * @param string $dir		The base directory.
     * @param array  $nodes		Select only some directories or files from the base directory.
     * 
     * @throws Exception
     */
    public function compress(string $file, string $dir = '', array $nodes = []): void
    {
        $file = $this->abspath . $file;
        $dir  = $this->abspath . $dir;

        is_dir($dst = dirname($file))
            or mkdir($dst, SYSTEM_MKDIR_MODE);

        $this->getAdapter($file)->compress($file, $dir, $nodes);
    }

    /**
     * Get
     * 
     * @param string $extension
     */
    protected function getAdapter(string $file): Junco\Archive\Adapter\AdapterInterface
    {
        switch (pathinfo($file, PATHINFO_EXTENSION)) {
            case 'rar':
                return $this->adapter['rar'] ??= new Junco\Archive\Adapter\RarAdapter();

            case 'zip':
                return $this->adapter['zip'] ??= new Junco\Archive\Adapter\ZipAdapter();

            default:
                return $this->adapter['phar'] ??= new Junco\Archive\Adapter\PharAdapter();
        }
    }

    /**
     * Accept
     */
    public function acceptsToExtract()
    {
        $accept = [];
        if (extension_loaded('zip')) {
            $accept[] = 'zip';
        }
        if (extension_loaded('rar')) {
            $accept[] = 'rar';
        }
        if (extension_loaded('phar')) {
            $accept[] = 'tar';
            if (extension_loaded('zlib')) {
                $accept = array_merge($accept, ['gz', 'gzip', 'tgz', 'tgzip']);
            }
            if (extension_loaded('bz2')) {
                $accept = array_merge($accept, [
                    'bz',
                    'bzip',
                    'bzip2',
                    'bz2',
                    'tbz',
                    'tbzip',
                    'tbz2',
                    'tbzip2'
                ]);
            }
        }

        return $accept;
    }

    /**
     * Accept
     */
    public function acceptsToCompress()
    {
        $accept = [];
        if (extension_loaded('zip')) {
            $accept[] = 'zip';
        }
        if (extension_loaded('phar')) {
            $accept[] = 'tar';
            //$accept[] = 'phar';

            if (extension_loaded('zlib')) {
                $accept[] = 'tar.gz';
                /* $accept = array_merge($accept, ['tar.gz', 'tar.gzip', 'tgz', 'tgzip']); */
            }
            if (extension_loaded('bz2')) {
                $accept[] = 'tar.bz2';
                /* $accept = array_merge($accept, [
					'tar.bz', 'tar.bzip', 'tar.bzip2', 'tar.bz2', 
					'tbz', 'tbzip', 'tbz2', 'tbzip2'
				]); */
            }
        }

        return $accept;
    }
}
