<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

class ImageSet
{
    /**
     * Constructor
     */
    public function __construct(protected array $urls, protected string $image = '') {}

    /**
     * Set
     * 
     * @param string $image
     * 
     * @return self
     */
    public function setImage(string $image = ''): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get
     * 
     * @param int $min_size
     * 
     * @return ?string
     */
    public function getImage(int $min_size): ?string
    {
        foreach ($this->urls as $size => $url) {
            if ($size >= $min_size) {
                return $url . $this->image;
            }
        }

        return $this->urls[array_key_last($this->urls)] . $this->image;
    }

    /**
     * Get
     * 
     * @param array $conditions
     * @param int   $default
     * 
     * @return ?string
     */
    public function getSrcSet(array $conditions, int $default): ?string
    {
        $srcset = [];
        $sizes  = [];

        foreach ($conditions as $min_size => $condition) {
            foreach ($this->urls as $size => $url) {
                if ($size >= $min_size) {
                    $url = $url . $this->image;

                    $srcset[] = "{$url} {$size}w";
                    $sizes[]  = "{$condition} {$size}px";
                    continue 2;
                }
            }

            return null;
        }

        foreach ($this->urls as $size => $url) {
            if ($size >= $default) {
                $url = $url . $this->image;

                $srcset[] = "{$url} {$size}w";
                $sizes[]  = "{$size}px";
                break;
            }
        }

        return 'srcset="' . implode(', ', $srcset) . '"'
            . ' sizes="' . implode(', ', $sizes) . '"';
    }
}
