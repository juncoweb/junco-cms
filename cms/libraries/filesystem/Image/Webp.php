<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem\Image;

use Exception;
use GdImage;

class Webp implements ImageInterface
{
    // vars
    protected int $quality;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!(imagetypes() & IMG_WEBP)) {
            throw new Exception('The GD library does not support webp.');
        }

        $this->quality = config('filesystem.webp_quality') ?? -1;
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
        return imagecreatefromwebp($file);
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
        $image = imagecreatetruecolor($width, $height);

        if (!$image) {
            return false;
        }

        // transparent
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefilledrectangle($image, 0, 0, $width, $height, $transparent);

        return $image;
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
        return imagewebp($image, $file, $this->quality);
    }
}
