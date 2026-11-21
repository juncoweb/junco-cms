<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

class UploadedImageManager extends UploadedFileManager
{
    protected ?ImageResizes $resizes = null;
    protected array $orderOfImages   = []; // see later

    /**
     * Validate
     * 
     * @param ?array $rules
     * 
     * @return static
     */
    public function validate(?array $rules = null): static
    {
        return parent::validate(
            array_merge(['allow_extensions' => config('filesystem.accept_images')], $rules ?? [])
        );
    }

    /**
     * Set resizes
     *
     * @param ImageResizes $resizes
     *
     * @return self
     */
    public function setResizes(ImageResizes $resizes): self
    {
        $this->resizes = $resizes->setBasePath($this->basepath);
        return $this;
    }

    /**
     * Move
     */
    public function moveTo(string $target = '', int $rename = 0, bool $rewrite = false): static
    {
        if ($target) {
            $this->resizes?->setOrigPath($target);
        } else {
            $target = $this->resizes?->getOrigPath();
        }

        parent::moveTo($target, $rename, $rewrite);

        if ($this->resizes && $this->files) {
            $this->resizes->resize(array_column($this->files, 'filename'));
        }

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

            // delete
            $this->deleteMultiple(
                array_diff($filenames, array_column($this->files, 'filename'))
            );
        }

        return $this;
    }

    /**
     * Delete
     */
    protected function delete(string $filename): void
    {
        if ($this->resizes) {
            $this->resizes->remove([$filename]);
        } else {
            parent::delete($filename);
        }
    }

    /**
     * Delete
     */
    protected function deleteMultiple(array $filenames): void
    {
        if ($this->resizes) {
            $this->resizes->remove($filenames);
        } else {
            foreach ($this->files as $file) {
                parent::delete($file['filename']);
            }
        }
    }
}
