<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

use Imagick;
use ImagickPixel;

class SvgPrinter
{
    /**
     * From
     * 
     * @param ImageResizes[] $list
     * @param string         $svg
     * @param string         $bgColor
     * @param string         $filename
     * 
     * @return string
     */
    public function write(
        array  $list,
        string $svg,
        string $bgColor = '',
        string $filename = ''
    ): string {
        $svgImg = new Imagick();
        $svgImg->setBackgroundColor(new ImagickPixel('transparent'));
        $svgImg->readImageBlob($this->setSvgColor($svg));
        //
        $svgSize = $svgImg->getImageGeometry();
        $ratio   = $svgSize['width'] / $svgSize['height'];

        if (!$filename) {
            $filename = uniqid() . '.png';
        }

        foreach ($list as $resizes) {
            $basepath = $resizes->getBasePath() . $resizes->getPath();

            foreach ($resizes->getSizes() as $sizename => $size) {
                $size = $this->parseSize($size);
                $svgSize = $this->calcSvgSize($size, $ratio);

                $svgImg->resizeImage($svgSize[0], $svgSize[1], Imagick::FILTER_LANCZOS, 1);
                //
                $pngImg = new Imagick();
                $pngImg->newImage($size[0], $size[1], new ImagickPixel($bgColor ?: 'transparent'));
                $pngImg->compositeImage($svgImg, Imagick::COMPOSITE_OVER, $svgSize['x'], $svgSize['y']);
                $pngImg->setImageFormat('png');
                $pngImg->writeImage($basepath . $sizename . DIRECTORY_SEPARATOR . $filename);
                $pngImg->clear();
                $pngImg->destroy();
            }
        }

        $svgImg->clear();
        $svgImg->destroy();

        return $filename;
    }

    /**
     * Set
     */
    protected function setSvgColor(string $svg): string
    {
        if (!preg_match('/style="(.*?)"/i', $svg)) {
            $svg = preg_replace('/\<path/i', '<path style="fill:white"', $svg);
        }

        return $svg;
    }

    /**
     * Get
     * 
     * @return array
     */
    public function parseSize(string $size): array
    {
        $size = explode('x', $size);
        return [
            (int)$size[0],
            (int)($size[1] ?? $size[0])
        ];
    }

    /**
     * Calc
     */
    protected function calcSvgSize(array $size, float $ratio): array
    {
        $rel = $this->getSvgRelation($size);
        $min = min(...$size);

        if ($ratio > 1) {
            $width  = $rel * $min;
            $height = $width / $ratio;
        } else {
            $height = $rel * $min;
            $width  = $height * $ratio;
        }

        $width = (int)$width;
        $height = (int)$height;
        $x = ($size[0] - $width) / 2;
        $y = ($size[1] - $height) / 2;


        return [(int)$width, (int)$height, 'x' => (int) $x, 'y' => (int)$y];
    }

    /**
     * Calc
     */
    protected function getSvgRelation(array $size): float
    {
        if ($size[0] >= 1200) {
            return 0.5;
        }

        if ($size[0] >= 600) {
            return 0.65;
        }

        return 0.75;
    }
}
