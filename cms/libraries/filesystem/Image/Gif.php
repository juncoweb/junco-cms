<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem\Image;

use Exception;
use GdImage;

class Gif implements ImageInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        if (!(imagetypes() & IMG_GIF)) {
            throw new Exception('The GD library does not support gif.');
        }
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
        return imagecreatefromgif($file);
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

        $transparent = imagecolortransparent($imgBase);

        if ($transparent >= 0) {
            $color            = imagecolorsforindex($imgBase, $transparent);
            $transparent    = imagecolorallocate($image, $color['red'], $color['green'], $color['blue']);
            imagefill($image, 0, 0, $transparent);
            imagecolortransparent($image, $transparent);
        }

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
        return imagegif($image, $file);
    }
}
