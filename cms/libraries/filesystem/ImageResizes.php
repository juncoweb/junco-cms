<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

class ImageResizes
{
    protected string  $key     = '';
    protected string  $path    = '';
    protected int     $mode    = 0;
    protected array   $sizes   = [];
    // tools
    protected array   $files   = [];
    protected string  $default = '';
    // internal
    protected ?array  $sorted    = null;
    protected ?string $basepath  = null;
    protected ?string $baseurl   = null;
    protected ?string $origpath  = null;
    protected bool    $save_orig = true;

    /**
     * From
     * 
     * @param ?string $resizes
     * @param string  $key
     * @param string  $path_key
     * 
     * @return self
     */
    public static function fromString(?string $resizes, string $key, string $path_key = ''): self
    {
        if ($resizes) {
            $resizes = json_decode($resizes, true);
        }
        $path = $path_key
            ? config($path_key)
            : '';

        return new self($resizes ?: config($key), $path);
    }

    /**
     * From
     * 
     * @param string $key
     * @param string $path_key
     * 
     * @return self
     */
    public static function from(string $key, string $path_key): self
    {
        return new self(config($key), config($path_key));
    }

    /**
     * Constructor
     */
    public function __construct(array $resizes, string $path = '')
    {
        if (!$resizes) {
            return;
        }

        $ImageResizes = config('filesystem.image_resizes') ?: [];

        if (empty($ImageResizes[$resizes['key']])) {
            return;
        }

        $this->key  = $resizes['key'];
        $this->mode = $ImageResizes[$resizes['key']]['mode'];
        $this->path = $path;

        foreach ($resizes['sizes'] as $size) {
            $value = $ImageResizes[$resizes['key']]['sizes'][$size] ?? null;

            if ($value) {
                $this->sizes[$size] = $value;
            }
        }
    }

    /**
     * Set
     * 
     * @param string $basepath
     * 
     * @return self
     */
    public function setBasePath(string $basepath = ''): self
    {
        $this->basepath = $basepath;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basepath ??= media_path();
    }

    /**
     * Set
     * 
     * @param string $baseurl
     * 
     * @return self
     */
    public function setBaseUrl(string $baseurl = ''): self
    {
        $this->baseurl = $baseurl;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseurl ??= media_url();
    }

    /**
     * Set
     * 
     * @param string $path
     * 
     * @return self
     */
    public function setPath(string $path = ''): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get
     * 
     * @return int
     */
    public function getMode(): int
    {
        return $this->mode;
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getSizes(): array
    {
        return $this->sizes;
    }

    /**
     * Get
     * 
     * @param int $min_size
     * 
     * @return string
     */
    public function getSize(int $min_size = 0): string
    {
        return $this->sizes[$this->getSizeName($min_size)];
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getSizeName(int $min_size = 0): string
    {
        if (!$this->sizes) {
            return '';
        }

        $this->sorted ??= $this->getSortedSizes();

        if ($min_size < 0) {
            return array_key_last($this->sorted);
        }

        foreach ($this->sorted as $sizename => $size) {
            if ($size >= $min_size) {
                return $sizename;
            }
        }

        return array_key_last($this->sorted);
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getUrl(string $file = '', int $min_size = 0): string
    {
        return $this->getBaseUrl() . $this->path . $this->getSizeName($min_size) . '/' . $file;
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getUrls(string $file = ''): array
    {
        $this->baseurl ??= $this->getBaseUrl();
        $this->sorted ??= $this->getSortedSizes();

        $url = [];
        foreach ($this->sorted as $sizename => $size) {
            $url[$size] = $this->baseurl . $this->path . $sizename . '/' . $file;
        }

        return $url;
    }

    /**
     * Get
     * 
     * @return ImageSet
     */
    public function getImageSet(string $image = ''): ImageSet
    {
        return new ImageSet($this->getUrls(), $image);
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getOptions(string $image): array
    {
        return [
            'accept' => 'image',
            'images' => [$this->getUrl('', 80), $image],
            'max_wh' => $this->getSize(-1)
        ];
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getMultOptions(array $images, bool $reorder = false): array
    {
        return [
            'multiple' => true,
            'accept'   => 'image',
            'images'   => [$this->getUrl('', 80), $images],
            'max_wh'   => $this->getSize(-1),
            'reorder'  => $reorder
        ];
    }

    /**
     * Set
     * 
     * @param string $path
     * 
     * @return self
     */
    public function setOrigPath(string $path = ''): self
    {
        $this->origpath = $path;
        return $this;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getOrigPath(): string
    {
        return $this->origpath ??= $this->path . 'original/';
    }

    /**
     * Set
     * 
     * @param array $files
     * 
     * @return self
     */
    public function setFiles(array $files, string $default = ''): self
    {
        if ($default) {
            $files[] = $default;
        }

        $this->files = $files;
        return $this;
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * Resize
     * 
     * @param array $files
     * 
     * @return void
     */
    public function resize(array $files): void
    {
        $resizer  = new ImageResizer();
        $rszpath  = $this->getFullRszPath();
        $origpath = $this->getFullOrigPath();

        foreach ($files as $file) {
            $src_file = $origpath . $file;

            foreach ($this->sizes as $sizename => $size) {
                $rsz_file = strtr($rszpath, [
                    '{sizename}' => $sizename,
                    '{filename}' => $file
                ]);

                $resizer->resize($src_file, $rsz_file, $size, $this->mode);
            }

            $this->save_orig or unlink($src_file);
        }
    }

    /**
     * Remove
     * 
     * @param array $files
     * 
     * @return void
     */
    public function remove(array $files): void
    {
        $sizenames = array_keys($this->sizes);
        $rszpath   = $this->getFullRszPath();

        foreach ($files as $filename) {
            foreach ($sizenames as $sizename) {
                $f = strtr($rszpath, [
                    '{sizename}' => $sizename,
                    '{filename}' => $filename
                ]);

                is_file($f) and unlink($f);
            }
        }

        if ($this->save_orig) {
            $origpath = $this->getFullOrigPath();

            foreach ($files as $filename) {
                $f = $origpath . $filename;

                is_file($f) and unlink($f);
            }
        }
    }

    /**
     * To Array
     * 
     * @return ?array
     */
    public function toArray(): ?array
    {
        if (!$this->key) {
            return null;
        }

        return [
            'key'   => $this->key,
            'mode'  => $this->mode,
            'sizes' => array_keys($this->sizes)
        ];
    }

    /**
     * To String
     * 
     * @return ?string
     */
    public function toString(): ?string
    {
        return $this->key
            ? json_encode($this->toArray())
            : null;
    }

    /**
     * Get
     */
    protected function getSortedSizes(): array
    {
        $sizes = [];
        foreach ($this->sizes as $sizename => $size) {
            $sizes[$sizename] = (false === strpos($size, 'x'))
                ? (int)$size
                : max(...array_map('intval', explode('x', $size)));
        }
        asort($sizes);

        return $sizes;
    }

    /**
     * Get
     */
    protected function getFullRszPath(): string
    {
        $rszpath = $this->getBasePath() . $this->path;

        if (false === strpos($rszpath, '{')) {
            $rszpath .= '{sizename}/{filename}';
        }

        return $rszpath;
    }

    /**
     * Get
     */
    protected function getFullOrigPath(): string
    {
        return $this->getBasePath() . $this->getOrigPath();
    }
}
