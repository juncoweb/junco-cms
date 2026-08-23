<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Filesystem;

use Psr\Http\Message\UploadedFileInterface;
use Exception;

class UploadedFileManager
{
    // vars
    protected string  $basedir       = '';
    protected string  $dirpath       = '';
    protected bool    $mkdir         = true;
    protected array   $uploadedFiles = [];
    protected array   $files         = [];
    protected int     $num_files     = 0;
    protected bool    $isMultiple    = false;
    protected bool    $keepCurrent   = false;
    protected int     $pointer       = -1;
    protected ?string $userToken     = null;

    // const
    const DEFAULT_RENAME = 0;
    const DEFAULT_NAME   = 1;
    const CURUSER_NAME   = 2;
    const CLEAN_NAME     = 3;
    const UNIQUE_ID      = 4;

    /**
     * Constructor
     *
     * @param UploadedFileInterface|array|null $UploadedFile
     * @param bool $isMultiple
     */
    public function __construct(UploadedFileInterface|array|null $UploadedFile = null, bool $isMultiple = false)
    {
        $this->isMultiple = $isMultiple;
        $this->basedir    = SYSTEM_ABSPATH . SYSTEM_MEDIA_PATH;

        if ($UploadedFile !== null) {
            $isArray = is_array($UploadedFile);

            if ($this->isMultiple) {
                $isArray or abort();

                foreach ($UploadedFile as $file) {
                    $this->addUploadedFile($file);
                }
            } else {
                $isArray and abort();
                $this->addUploadedFile($UploadedFile);
            }
        }
    }

    /**
     * Getter
     * 
     * @deprecated in v16
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'num_files':
                return $this->num_files;
        }

        $trace = debug_backtrace();
        app('logger')->notice('Undefined property via __set(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line']);
    }

    /**
     * Get
     */
    public function getNumFiles(): int
    {
        return $this->num_files;
    }

    /**
     * Verify
     * 
     * @return static
     * 
     * @throws Exception
     */
    public function verifyIsEmpty(): static
    {
        if (!$this->num_files) {
            throw new Exception(_t('Please select a file from your computer.'));
        }

        return $this;
    }

    /**
     * Keep
     * 
     * @param bool $keepCurrent
     * 
     * @throws HttpError
     * 
     * @return void
     */
    public function keepCurrent(bool $keepCurrent = true): void
    {
        // security
        ($keepCurrent && $this->num_files) and abort();

        $this->keepCurrent = $keepCurrent;
    }

    /**
     * Set
     * 
     * @param string $basedir
     * 
     * @return static
     */
    public function setBasedir(string $basedir = ''): static
    {
        $this->basedir = $basedir;
        return $this;
    }

    /**
     * This performs a validation of the files
     *
     * @param array $rules [
     *    allow_extensions =>  array|string,    An array with the allowed extensions.
     *    min_size         =>  int,             Minimun size measured in bytes.
     *    max_size         =>  int,             Maximum size measured in bytes.
     *    max_chars        =>  int,             Maximum length of the file name.
     *    max_files        =>  int,             The maximum number of files.
     * ]
     * 
     * @return static
     * 
     * @throws Exception
     * 
     * @return static
     */
    public function validate(?array $rules = null): static
    {
        if ($rules === null) {
            return $this;
        }

        foreach ($rules as $type => $value) {
            if (!$value) {
                continue;
            }
            switch ($type) {
                case 'allow_extensions':
                    $this->validateExtension($value);
                    break;

                case 'min_size':
                    $this->validateMinSize($value);
                    break;

                case 'max_size':
                    $this->validateMaxSize($value);
                    break;

                case 'max_chars':
                    $this->validateMaxChars($value);
                    break;

                case 'max_files':
                    $this->validateMaxFiles($value);
                    break;
            }
        }

        return $this;
    }

    /**
     * Move the files
     * 
     * @param string $targetPath
     * @param int    $rename      Option with which the file is named.
     * @param bool   $rewrite     Rewrite the file if it already exists.
     *
     * @throws Exception
     * 
     * @return static
     */
    public function moveTo(string $targetPath, int $rename = 0, bool $rewrite = false): static
    {
        $this->dirpath = $this->basedir . $targetPath;

        is_dir($this->dirpath)
            or $this->makeDir($this->dirpath);

        if ($rename == self::DEFAULT_RENAME || !in_array($rename, [
            self::DEFAULT_NAME,
            self::CURUSER_NAME,
            self::CLEAN_NAME,
            self::UNIQUE_ID,
        ])) {
            $rename = config('filesystem.default_rename');
        }

        for ($i = 0; $i < $this->num_files; $i++) {
            $basename  = $this->files[$i]['basename'];
            $extension = $this->files[$i]['extension'];

            switch ($rename) {
                case self::CURUSER_NAME:
                    $this->userToken ??= \curuser()->getId() . '-' . date('YmdHis');
                    $basename = $this->userToken . ($i ? '-' . $i : '');
                    break;

                case self::CLEAN_NAME:
                    $basename = $this->cleanName($basename);
                    break;

                case self::UNIQUE_ID:
                    $basename = uniqid();
                    break;
            }

            if (!$rewrite) {
                $basename = $this->renameIfExists($basename, $extension);
            }

            if ($basename !== $this->files[$i]['basename']) {
                $this->files[$i]['basename'] = $basename;
                $this->files[$i]['filename'] = $basename . '.' . $extension;
            }

            $this->uploadedFiles[$i]->moveTo($this->dirpath . $this->files[$i]['filename']);
        }

        return $this;
    }

    /**
     * Set current file
     * 
     * @param string $filename
     * 
     * @return static
     */
    public function setCurrentFile(?string $filename = null): static
    {
        if ($this->keepCurrent) {
            $this->num_files and abort();

            if ($filename) {
                $this->files[] = $this->builtFileData($filename);
                $this->num_files++;
            }
        } elseif ($filename) {
            $this->delete($filename);
        }

        return $this;
    }

    /**
     * Returns an UploadedFile object or an array of them
     *
     * @return UploadedFileInterface|UploadedFileInterface[]
     */
    public function getUploadedFile(): UploadedFileInterface|array
    {
        return $this->isMultiple
            ? $this->uploadedFiles
            : $this->uploadedFiles[0];
    }

    /**
     * Fetch all files
     *
     * @return array
     */
    public function getUploadedFileData(): array
    {
        return $this->isMultiple
            ? $this->files
            : $this->files[0];
    }

    /**
     * Returns the client filename
     * 
     * if multiple, returns an array, otherwise a string.
     *
     * @return string|array
     */
    public function getClientFilename(): string|array
    {
        if ($this->isMultiple) {
            return array_column($this->files, 'clientFilename');
        }

        return $this->files[0]['clientFilename'] ?? '';
    }

    /**
     * Returns the filename
     * 
     * if multiple, returns an array or a string separated 
     * by the value passed as argument.
     * 
     * @param ?string $separator
     *
     * @return string|array
     */
    public function getFilename(?string $separator = null): string|array
    {
        if ($this->isMultiple) {
            if ($separator === null) {
                return $this->files;
            }

            return implode($separator, array_column($this->files, 'filename'));
        }

        return $this->files[0]['filename'] ?? '';
    }

    /**
     * Get parsed contents
     * 
     * @param bool|string $parse
     *
     * @return mixed
     */
    public function getContents(bool|string $parse = true): mixed
    {
        if (!$this->num_files) {
            return null;
        }

        $data = [];
        for ($i = 0; $i < $this->num_files; $i++) {
            $contents = $this->uploadedFiles[$i]->getStream();

            if ($parse === true) {
                $parse = $this->files[$i]['extension'];
            }
            switch ($parse) {
                case 'csv':
                    $data[] = str_getcsv($contents);
                    break;

                case 'json':
                    $data[] = json_decode($contents, $option ?? true);
                    break;

                case 'xml':
                    $data[] = simplexml_load_string($contents);
                    break;

                default:
                    $data[] = $contents;
            }
        }

        return $this->isMultiple
            ? $data
            : $data[0];
    }

    /**
     * Add an Uploaded File
     */
    protected function addUploadedFile(UploadedFileInterface $UploadedFile): void
    {
        switch ($UploadedFile->getError()) {
            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception(_t('The uploaded file is too big.'));

            case UPLOAD_ERR_NO_FILE:
                throw new Exception(_t('Please select a file from your computer.'));

            default:
            case UPLOAD_ERR_PARTIAL:
            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
            case UPLOAD_ERR_EXTENSION:
                if (SYSTEM_HANDLE_ERRORS) {
                    app('logger')->error('File upload failed.');
                }
                throw new Exception(_t('File upload failed.'));
        }

        $this->files[] = $this->builtFileData($UploadedFile->getClientFilename());
        $this->uploadedFiles[] = $UploadedFile;
        $this->num_files++;
    }

    /**
     * Returns a standard row of data
     */
    protected function builtFileData(string $filename): array
    {
        $info = pathinfo($filename);
        return [
            'clientFilename' => $filename,
            'filename'       => $filename,
            'basename'       => $info['filename'],
            'extension'      => strtolower($info['extension'] ?? '')
        ];
    }

    /**
     * Validate extension
     */
    protected function validateExtension(array|string $extensions): void
    {
        if ($extensions == '*') {
            return;
        }
        if (!is_array($extensions)) {
            $extensions = array_map('trim', explode(',', $extensions));
        }

        foreach ($this->files as $info) {
            if (!in_array($info['extension'], $extensions)) {
                throw new Exception(_t('The file type is invalid.'));
            }
        }
    }

    /**
     * Validate minimum size
     */
    protected function validateMinSize(int $min_size): void
    {
        foreach ($this->uploadedFiles as $file) {
            if ($file->getSize() < $min_size) {
                throw new Exception(
                    sprintf(
                        _t('The file is very small. Minimum allowed %s'),
                        FileHelper::toTextSize($min_size)
                    )
                );
            }
        }
    }

    /**
     * Validate maximum size
     */
    protected function validateMaxSize(int $max_size): void
    {
        foreach ($this->uploadedFiles as $file) {
            if ($file->getSize() > $max_size) {
                throw new Exception(
                    sprintf(
                        _t('The file is too big. Maximum allowed %s'),
                        FileHelper::toTextSize($max_size)
                    )
                );
            }
        }
    }

    /**
     * Validate maximum characters
     */
    protected function validateMaxChars(int $max_chars): void
    {
        foreach ($this->files as $info) {
            $length = strlen($info['clientFilename']);
            if ($length > $max_chars) {
                throw new Exception(sprintf(_t('The filename is too long. Maximum %s characters'), $max_chars));
            }
        }
    }

    /**
     * Validate maximum files
     */
    protected function validateMaxFiles(int $max_files): void
    {
        if ($max_files < 0) {
            throw new Exception(_t('At the moment it is not allowed to upload files.'));
        } elseif ($this->num_files > $max_files) {
            throw new Exception(sprintf(_t('You have exceeded the maximum %d files allowed.'), $max_files));
        }
    }

    /**
     * Make directory
     */
    protected function makeDir(string $dir): void
    {
        if ($this->mkdir) {
            mkdir($dir, SYSTEM_MKDIR_MODE, true);
        } else {
            throw new Exception(_t('The destination directory does not exist.'));
        }
    }

    /**
     * Rename if the file exists
     */
    protected function renameIfExists(string $basename, string $extension): string
    {
        for (
            $count = 1;
            is_file($this->dirpath . $basename . '.' . $extension);
            $count++
        ) {
            $partial = substr($basename, 0, -strlen($count));
            $basename = $partial . $count;
        }

        return $basename;
    }

    /**
     * Delete
     */
    protected function delete(string $filename): void
    {
        $file = $this->dirpath . $filename;

        is_file($file) and unlink($file);
    }

    /**
     * Transform a name to ASCII characters
     */
    public function cleanName(string $name): string
    {
        $name = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'â', 'ê', 'î', 'ô', 'û', 'ä', 'ë', 'ï', 'ö', 'ü', 'Á', 'É', 'Í', 'Ó', 'Ú', 'À', 'È', 'Ì', 'Ò', 'Ù', 'Â', 'Ê', 'Î', 'Ô', 'Û', 'Ä', 'Ë', 'Ï', 'Ö', 'Ü', 'ñ', 'Ñ', 'ç', 'Ç', 'º', 'ª'],
            ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'n', 'n', 'c', 'C', 'o', 'a'],
            trim($name)
        );

        return strtolower(preg_replace('/[\W]/', '_', $name));
    }

    /**
     * Get renamings
     */
    public static function getRenames(): array
    {
        return [
            self::DEFAULT_NAME => 'Default name',
            self::CURUSER_NAME => 'User based',
            self::CLEAN_NAME   => 'Clean name',
            self::UNIQUE_ID    => 'Unique ID',
        ];
    }
}
