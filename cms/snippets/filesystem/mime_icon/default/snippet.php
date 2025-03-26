<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Filesystem\MimeHelper;

class mime_icon_filesystem_default_snippet
{
    /**
     * Load resources
     */
    public function loadAssets()
    {
        app('assets')->css('assets/filesystem-icons.min.css');
    }

    /**
     * 
     */
    public function render($extension)
    {
        return '<i class="mime-icon ' . $this->getStyleClass($extension) . '" aria-hidden="true"></i>';
    }

    /**
     * Get
     */
    protected function getStyleClass($extension)
    {
        switch ($extension) {
            case 'dir':
            case 'folder':
                return 'fa-solid fa-folder';
            case 'bat':
            case 'com':
            case 'exe':
                return 'fa-regular fa-file-code';
            case 'afp':
            case 'afpa':
            case 'asp':
            case 'aspx':
            case 'c':
            case 'cfm':
            case 'cgi':
            case 'cpp':
            case 'h':
            case 'lasso':
            case 'php':
            case 'phtml':
            case 'vb':
            case 'xml':
                return 'fa-solid fa-file-code';
                /* case 'php':
			case 'phtml':
				return 'fa-brands fa-php'; */
            case 'doc':
            case 'docx':
                return 'fa-solid fa-file-word';
            case '3gp':
            case 'avi':
            case 'mov':
            case 'mp4':
            case 'mpg':
            case 'mpeg':
            case 'wmv':
                return 'fa-solid fa-file-video';
            case 'htm':
            case 'html':
                return 'fa-solid fa-globe';
            case 'm4p':
            case 'mp3':
            case 'ogg':
            case 'wav':
                return 'fa-solid fa-file-audio';
            case 'pdf':
                return 'fa-solid fa-file-pdf';
            case 'bmp':
            case 'gif':
            case 'ico':
            case 'jpg':
            case 'jpeg':
            case 'pcx':
            case 'png':
            case 'svg':
            case 'tif':
            case 'tiff':
                return 'fa-solid fa-file-image';
            case 'ppt':
            case 'pptx':
            case 'pps':
            case 'ppsx':
                return 'fa-solid fa-file-powerpoint';
            case 'log':
            case 'txt':
                return 'fa-solid fa-file-lines';
            case 'xls':
            case 'xlsx':
                return 'fa-solid fa-file-excel';
            case 'rar':
            case 'tgz':
            case 'zip':
                return 'fa-solid fa-file-zipper';
            case 'csv':
                return 'fa-solid fa-file-csv';
        }

        $type = (new MimeHelper)->getType($extension);
        if ($type) {
            $type = explode('/', $type)[0];

            switch ($type) {
                case 'application':
                    return 'fa-regular fa-file-code';
                case 'audio':
                    return 'fa-solid fa-file-audio';
                case 'video':
                    return 'fa-solid fa-file-video';
                case 'image':
                    return 'fa-solid fa-file-image';
            }
        }

        return 'fa-solid fa-file';
    }
}
