<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem\Image;

use Exception;
use GdImage;

class Png implements ImageInterface
{
    // vars
    protected int $quality;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!(imagetypes() & IMG_PNG)) {
            throw new Exception('The GD library does not support png.');
        }

        $this->quality = config('filesystem.png_quality') ?? -1;
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
        return imagecreatefrompng($file);
    }

    /**
     * Create
     * 
     * @param int      $width
     * @param int      $height
     * @param ?GdImage $imageBase
     * 
     * @return GdImage|false
     */
    public function createImage(int $width, int $height, ?GdImage $imageBase = null): GdImage|false
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
        return imagepng($image, $file, $this->quality);
    }
}
