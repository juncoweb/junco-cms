<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

use GdImage;
use Junco\Filesystem\Image\ImageInterface;
use Junco\Filesystem\Image\Gif;
use Junco\Filesystem\Image\Jpeg;
use Junco\Filesystem\Image\Png;
use Junco\Filesystem\Image\Webp;

class Image
{
    protected ?string         $file = null;
    protected array           $info;
    protected ?ImageInterface $adapter = null;
    protected ?GdImage        $image   = null;

    /**
     * Constructor
     * 
     * @param string $file
     */
    public function __construct(string $file)
    {
        if (is_file($file)) {
            $info = getimagesize($file);

            if ($info) {
                $this->file = $file;
                $this->info = $info;
            }
        }
    }

    /**
     * Ok
     * 
     * @return bool
     */
    public function isOk(): bool
    {
        return (bool)$this->file;
    }

    /**
     * Widht
     * 
     * @return int
     */
    public function getWidth(): int
    {
        return $this->info[0];
    }

    /**
     * Height
     * 
     * @return int
     */
    public function getHeight(): int
    {
        return $this->info[1];
    }

    /**
     * Type
     * 
     * @return int
     */
    public function getType(): int
    {
        return $this->info[2];
    }

    /**
     * Mime
     * 
     * @return ?string
     */
    public function getMime(): ?string
    {
        return $this->info['mime'] ?? null;
    }

    /**
     * Bits
     * 
     * @return ?string
     */
    public function getBits(): ?string
    {
        return $this->info['bits'] ?? null;
    }

    /**
     * Channels
     * 
     * @return ?int
     */
    public function getChannels(): ?int
    {
        return $this->info['channels'] ?? null;
    }

    /**
     * Resolution
     * 
     * @return array|string|null
     */
    public function getResolution(bool $asString = false): array|string|null
    {
        $this->image ??= $this->getImage();

        if (!$this->image) {
            return null;
        }

        $resolution = imageresolution($this->image);

        return $asString
            ? implode('x', $resolution) . ' (dpi)'
            : $resolution;
    }

    /**
     * Resolution
     * 
     * @return ?GdImage
     */
    public function into(string $file): ?GdImage
    {
        if (!$this->getImage()) {
            return null;
        }

        $extension  = $this->getExtension($file);
        $newAdapter = $this->getAdapter($extension);
        $newImage   = $newAdapter?->createImage($this->info[0], $this->info[1], $this->image);

        if (!$newImage) {
            return null;
        }

        $result = imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $this->info[0], $this->info[1], $this->info[0], $this->info[1]);

        if (!$result) {
            return null;
        }

        $result = $newAdapter->write($file, $newImage);

        if (!$result) {
            return null;
        }

        return $newImage;
    }

    /**
     * Get
     */
    protected function getImage(): ?GdImage
    {
        if ($this->image === null) {
            $this->adapter = $this->getAdapter($this->info[2]);
            $this->image   = $this->adapter?->createImageFromFile($this->file) ?? null;
        }

        return $this->image;
    }

    /**
     * Get
     * 
     * @param int|string $type
     * 
     * @return ?ImageInterface
     */
    protected function getAdapter(int|string $type): ?ImageInterface
    {
        switch ($type) {
            case 'gif':
            case IMAGETYPE_GIF:
                return new Gif;

            case 'jpg':
            case 'jpeg':
            case IMAGETYPE_JPEG:
                return new Jpeg;

            case 'png':
            case IMAGETYPE_PNG:
                return new Png;

            case 'webp':
            case IMAGETYPE_WEBP:
                return new Webp;
        }

        return null;
    }

    /**
     * Get
     */
    protected function getExtension(string $file): string
    {
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }
}
