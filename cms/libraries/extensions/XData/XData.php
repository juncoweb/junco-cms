<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\XData;

use Database;
use Exception;

class XData
{
    // vars
    protected string $basepath     = '';
    protected string $store_path   = '';
    protected bool   $is_installer = false;
    protected ?array $extensions   = null;

    //
    protected string $extension_alias_client   = '';
    protected string $extension_alias_host     = '';
    protected int    $extension_id_client      = 0;
    protected mixed  $output_type              = null;
    protected array  $extension_aliases_client = [];

    /**
     * Constructor
     * 
     * @param string $basepath
     * @param bool   $is_installer
     */
    public function __construct(string $basepath = '', bool $is_installer = false)
    {
        $this->basepath     = $basepath;
        $this->store_path   = $basepath . ($is_installer ? 'app/install/' : '') . config('extensions.xdata_path');
        $this->is_installer = $is_installer;
    }

    /**
     * Set
     * 
     * @param string $extension_alias_host
     * @param string $extension_alias_client
     * @param int    $extension_id_client
     * @param string $output_type
     */
    public function setClient(
        string $extension_alias_host,
        string $extension_alias_client,
        int    $extension_id_client,
        mixed  $output_type,
    ): void {
        $this->extension_alias_host       = $extension_alias_host;
        $this->extension_alias_client     = $extension_alias_client;
        $this->extension_id_client        = $extension_id_client;
        $this->output_type                = $output_type;
        $this->extension_aliases_client[] = $extension_alias_client;
    }

    /**
     * Reset
     */
    public function resetClient(): void
    {
        $this->extension_alias_host     = '';
        $this->extension_alias_client   = '';
        $this->extension_id_client      = 0;
        $this->output_type              = null;
        $this->extension_aliases_client = array_unique($this->extension_aliases_client);
    }

    /**
     * Get data
     * 
     * @param string|array|null $file
     */
    public function getData($file = null): array
    {
        $file ??= $this->output_type;

        if (is_array($file)) {
            $file = $this->getFileFromRequet($file);
        } elseif ($this->extension_alias_host && $this->extension_alias_client) {
            $file = $this->getFile();
        } else {
            throw new Exception(_t('Please select a file from your computer.'));
        }

        $data = $this->getJsonData($file);
        $extension_alias_client = array_key_first($data);

        if (!$this->extension_alias_client) {
            $this->extension_alias_client = $extension_alias_client;
        } elseif ($this->extension_alias_client != $extension_alias_client) {
            throw new MalformedDataException('XData file is invalid');
        }

        $this->extensions        ??= $this->getExtensions();
        $this->extension_id_client = $this->extensions[$extension_alias_client] ?? 0;

        if (!$this->extension_id_client) {
            throw new MalformedDataException('XData client_id not found');
        }

        return $data[$extension_alias_client];
    }

    /**
     * Put data
     * 
     * @param array $data
     * @param ?bool $is_resource
     */
    public function putData(array $data, ?bool $is_resource = null)
    {
        if (!$this->extension_alias_client) {
            throw new MalformedDataException('XData client alias not found');
        }

        $file   = $this->getFile();
        $buffer = json_encode([$this->extension_alias_client => $data], JSON_PRETTY_PRINT);

        if ($is_resource ??= $this->output_type) {
            return $this->getFileResponse($file, $buffer);
        }

        is_dir($this->store_path)
            or mkdir($this->store_path, SYSTEM_MKDIR_MODE, true);

        if (false === file_put_contents($file, $buffer)) {
            throw new MalformedDataException('XData error writing export file');
        }
    }

    /**
     * Get
     */
    public function __get(string $name) // @deprecated in v16
    {
        switch ($name) {
            case 'basepath':
                return $this->basepath;

            case 'is_installer':
                return $this->is_installer;

            case 'extension_id':
                if (!$this->extension_id_client) {
                    trigger_error('XData::extension_id = 0. This should not happen. To correct it, first call the XData::getData() method.', E_USER_NOTICE);
                }

                return $this->extension_id_client;

            case 'extension_alias':
                return $this->extension_alias_client;

            case 'extension_aliases':
                return $this->extension_aliases_client;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
            E_USER_NOTICE
        );
        return null;
    }

    /**
     * Get
     */
    public function getPath(): string
    {
        return $this->basepath;
    }

    /**
     * Is
     */
    public function isInstaller(): bool
    {
        return $this->is_installer;
    }

    /**
     * Get
     */
    public function getExtensionId(): int
    {
        if (!$this->extension_id_client) {
            trigger_error('XData::extension_id = 0. This should not happen. To correct it, first call the XData::getData() method.', E_USER_NOTICE);
        }

        return $this->extension_id_client;
    }

    /**
     * Get
     */
    public function getExtensionAlias(): string
    {
        return $this->extension_alias_client;
    }

    /**
     * Get
     */
    public function getExtensionAliases(): array
    {
        return $this->extension_aliases_client;
    }

    /**
     * Get
     * 
     * @param array $file
     * 
     * @return string
     */
    protected function getFileFromRequet(array $file): string
    {
        $extension = pathinfo($file['name'] ?? '', PATHINFO_EXTENSION);

        if ($extension != 'json') {
            throw new Exception(_t('The file type is invalid.'));
        }

        return $file['tmp_name'];
    }

    /**
     * Get
     * 
     * @return string
     */
    protected function getFile(): string
    {
        return $this->store_path . sprintf('%s.%s.json', $this->extension_alias_client, $this->extension_alias_host);
    }

    /**
     * Get data
     * 
     * @param string  $file
     */
    protected function getJsonData($file = null): array
    {
        if (!is_file($file)) {
            throw new MalformedDataException('XData file not found');
        }

        $data = file_get_contents($file);

        if (false === $data) {
            throw new MalformedDataException('XData file get contents error');
        }

        $data = json_decode($data, true);

        if (!$data || !is_array($data) || count($data) != 1) {
            throw new MalformedDataException('XData file decode error');
        }

        return $data;
    }

    /**
     * Get
     */
    protected function getExtensions(): array
    {
        return db()
            ->query("SELECT extension_alias, id FROM `#__extensions`")
            ->fetchAll(Database::FETCH_COLUMN, [0 => 1]);
    }

    /**
     * Get
     * 
     * @param string $file, 
     * @param string $buffer
     * 
     * @return void
     */
    public function getFileResponse(string $file, string $buffer): void
    {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: protected', false); // required for certain browsers
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . pathinfo($file, PATHINFO_BASENAME) . '";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($buffer));

        echo $buffer;
        die;
    }
}
