<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

use Junco\Filesystem\ImageResizer;

class UploadedImageManager extends UploadedFileManager
{
    // vars
    protected $resizes_path = '';
    protected $resizes      = [];
    protected $save_orig    = true;

    // see later
    protected $orderOfImages = [];

    /**
     * Validate
     * 
     * @param ?array $rules
     * 
     */
    public function validate(?array $rules = null): self
    {
        return parent::validate(
            array_merge(['allow_extensions' => config('filesystem.accept_images')], $rules ?? [])
        );

        return $this;
    }

    /**
     * Resize
     * 
     * @param string $resizes_path
     * @param mixed  $resizes			The new size value.
     * @param int    $resize_mode
     * @param bool   $save_orig
     * 
     * @return self
     */
    public function resize(
        string $resizes_path,
        mixed  $resizes,
        int    $resize_mode = 0,
        bool   $save_orig = true
    ): self {
        $this->setResize($resizes_path, $resizes, $resize_mode, $save_orig);

        if ($this->resizes) {
            $resizer = new ImageResizer();

            foreach ($this->files as $info) {
                foreach ($this->resizes as $sizename => $data) {
                    $src_file = $this->dirpath . $info['filename'];
                    $rsz_file = $this->basedir . strtr($this->resizes_path, [
                        '{sizename}' => $sizename,
                        '{filename}' => $info['filename']
                    ]);

                    $resizer->resize($src_file, $rsz_file, $data['size'], $data['mode']);
                }
                $this->save_orig or unlink($src_file);
            }
        }

        return $this;
    }

    /**
     * Set resize
     *
     * @param string $resizes_path
     * @param mixed  $resizes			The new size value.
     * @param int    $resize_mode
     * @param bool   $save_orig
     *
     * @return self
     */
    public function setResize(string $resizes_path, mixed $resizes, int $resize_mode = 0, bool $save_orig = true): self
    {
        // prepare resizes
        if (is_array($resizes)) {
            foreach ($resizes as $i => $data) {
                if (is_array($data)) {
                    $data['size'] ??= 0;
                    $data['mode'] ??= $resize_mode;
                } else {
                    $resizes[$i] = [
                        'size' => $data,
                        'mode' => $resize_mode
                    ];
                }
            }
        } else {
            $resizes = [[
                'size' => $resizes,
                'mode' => $resize_mode
            ]];
        }

        $this->resizes_path    = $resizes_path;
        $this->resizes        = $resizes;
        $this->save_orig    = $save_orig;

        return $this;
    }

    /**
     * Set order
     * 
     * @param string $order
     * 
     * @return self
     */
    public function setOrder(string $order): self
    {
        $this->orderOfImages = explode('|', $order);

        return $this;
    }

    /**
     * Current image
     * 
     * @param string $filename
     * 
     * @return self
     */
    public function setCurrentImage(?string $filename = null): self
    {
        return $this->setCurrentFile($filename);
    }

    /**
     * Current Images
     * 
     * @param array $filenames
     * 
     * @return self
     */
    public function setCurrentImages(array|string $filenames, string $separator = ','): self
    {
        if ($filenames) {
            $files = [];
            $names = array_column($this->files, 'clientFilename');

            // replace names
            foreach ($this->orderOfImages as $name) {
                if (in_array($name, $names)) {
                    $files[] = $this->files[array_search($name, $names)];
                } else {
                    $files[] = $this->builtFileData($name);
                }
            }

            $this->num_files = count($files);
            $this->files     = $files;

            if (!is_array($filenames)) {
                $filenames = explode($separator, $filenames);
            }

            // delete files
            $this->delete(array_diff($filenames, array_column($files, 'filename')));
        }

        return $this;
    }

    /**
     * Delete
     */
    public function delete($files): void
    {
        if ($files === true) {
            $files = $this->files;
        } elseif (!is_array($files)) {
            $files = [$files];
        }

        $sizenames = array_keys($this->resizes);
        foreach ($files as $filename) {
            foreach ($sizenames as $sizename) {
                $f = $this->basepath . strtr($this->resizes_path, [
                    '{sizename}'    => $sizename,
                    '{filename}'    => $filename
                ]);

                is_file($f) and unlink($f);
            }

            if ($this->save_orig) {
                $f = $this->dirpath . $filename;

                is_file($f) and unlink($f);
            }
        }
    }

    /**
     * Validate
     *
     * @param mixed  $resizes
     * @param int    $resize_mode
     *
     * @return void
     */
    public static function validateResize(mixed &$resizes, int $resize_mode = 0): void
    {
        // prepare resizes
        if (is_array($resizes)) {
            foreach ($resizes as $i => $data) {
                if (is_array($data)) {
                    $data['size'] ??= 0;
                    $data['mode'] ??= $resize_mode;
                } else {
                    $resizes[$i] = [
                        'size' => $data,
                        'mode' => $resize_mode
                    ];
                }
            }
        } else {
            $resizes = [[
                'size' => $resizes,
                'mode' => $resize_mode
            ]];
        }
    }
}
