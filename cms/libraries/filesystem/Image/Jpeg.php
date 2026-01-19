<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem\Image;

use Exception;
use GdImage;

class Jpeg implements ImageInterface
{
    // vars
    protected int $quality;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!(imagetypes() & IMG_JPG)) {
            throw new Exception('The GD library does not support jpeg.');
        }

        $this->quality = config('filesystem.jpeg_quality') ?? -1;
    }

    /**
     * Create
     * 
     * @param string $file
     * 
     * @return GdImage|false
     */
    public function createImageFromFile(string $file): GdImage|false
    {
        return imagecreatefromjpeg($file);
    }

    /**
     * Create
     * 
     * @param int      $width
     * @param int      $height
     * @param ?GdImage $imgBase
     * 
     * @return GdImage|false
     */
    public function createImage(int $width, int $height, ?GdImage $imgBase = null): GdImage|false
    {
        return imagecreatetruecolor($width, $height);
    }

    /**
     * Write
     * 
     * @param string  $file
     * @param GdImage $image
     * 
     * @return bool
     */
    public function write(string $file, GdImage $image): bool
    {
        return imagejpeg($image, $file, $this->quality);
    }
}
