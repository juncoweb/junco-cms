<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem\Image;

use GdImage;

interface ImageInterface
{
    /**
     * Create
     * 
     * @param string $file
     * 
     * @return GdImage|false
     */
    public function createImageFromFile(string $file): GdImage|false;

    /**
     * Create
     * 
     * @param int      $width
     * @param int      $height
     * @param ?GdImage $imgBase
     * 
     * @return GdImage|false
     */
    public function createImage(int $width, int $height, ?GdImage $imgBase = null): GdImage|false;

    /**
     * Write
     * 
     * @param string  $file
     * @param GdImage $image
     * 
     * @return bool
     */
    public function write(string $file, GdImage $image): bool;
}
