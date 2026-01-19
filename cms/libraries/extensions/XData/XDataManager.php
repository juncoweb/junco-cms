<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\XData;

use Junco\Extensions\XData\XData;
use SystemHelper;
use Plugins;
use Plugin;

class XDataManager
{
    // vars
    protected XData  $xdata;
    protected array  $services = [];
    protected ?array $finder   = null;

    /**
     * Constructor
     * 
     * @param string $basepath
     * @param bool   $is_installer
     */
    public function __construct(string $basepath = '', bool $is_installer = false)
    {
        $this->xdata = new XData($basepath, $is_installer);
    }

    /**
     * Find the data for an extension.
     *
     * @param int    $extension_id_client
     * @param string $extension_alias_client
     * 
     * @return array Array with the keys of the existing data.
     */
    public function find($extension_id_client, $extension_alias_client): array
    {
        $this->finder ??= $this->getPlugins('has', SystemHelper::scanPlugins('xdata'));

        $has = [];
        foreach ($this->finder as $extension_alias_host => $plugin) {
            if ($plugin->run($extension_id_client, $extension_alias_client)) {
                $has[] = $extension_alias_host;
            }
        }

        return $has;
    }

    /**
     * Collects data, then they will be processed all together.
     *
     * @param string|array $hosts                  Array with the host alias of the existing data.
     * @param string       $extension_alias_client Alias of the client extension 
     * @param int          $extension_id_client    ID of the client extension (only to export and delete).
     * @param array        $output_type
     * 
     * @return void
     */
    public function add(
        string|array $hosts,
        string       $extension_alias_client,
        int          $extension_id_client = 0,
        mixed        $output_type = null
    ): void {
        if (!is_array($hosts)) {
            $hosts = explode(',', $hosts);
        }

        // security
        ($output_type && count($hosts) !== 1) and abort();

        foreach ($hosts as $host) {
            $this->services[] = [
                'extension_alias_host'   => $host,
                'extension_alias_client' => $extension_alias_client,
                'extension_id_client'    => $extension_id_client,
                'file'                   => $output_type,
            ];
        }
    }

    /**
     * Import - This does the same as add / exec but for a single operation.
     *
     * @param string $extension_alias_host   It refers to the place where the data will be stored.
     * @param string $extension_alias_client
     * @param string $extension_id_client
     * @param mixed  $output_type                   File from which the data to be stored will be read.
     */
    public function import(
        string $extension_alias_host,
        string $extension_alias_client,
        int    $extension_id_client,
        mixed  $output_type
    ): void {
        // vars
        $extension_alias_host or abort();

        $this->add($extension_alias_host, $extension_alias_client, 0, $output_type);
        $this->exec('import');
    }

    /**
     * Import
     */
    public function importAll()
    {
        return $this->exec('import');
    }

    /**
     * Export - This does the same as add / exec but for a single operation.
     *
     * @param string $extension_alias_host   It refers to the place where the data will be stored.
     * @param string $extension_alias_client
     * @param string $extension_id_client
     * @param mixed  $output_type                   File from which the data to be stored will be read.
     */
    public function export(
        string $extension_alias_host,
        string $extension_alias_client,
        int    $extension_id_client,
        mixed  $output_type = null
    ): void {
        if (!$extension_alias_client) {
            if ($extension_id_client > 0) {
                $extension_alias_client = $this->getExtensionAlias($extension_id_client);

                if (!$extension_alias_client) {
                    throw new MalformedDataException('XData client alias not found');
                }
            } else {
                $extension_alias_client = '__';
            }
        }

        $this->add($extension_alias_host, $extension_alias_client, $extension_id_client, $output_type);
        $this->exec('export');
    }

    /**
     * Export
     */
    public function exportAll()
    {
        return $this->exec('export');
    }

    /**
     * Delete
     */
    public function deleteAll()
    {
        return $this->exec('delete');
    }

    /**
     * Process the collected data.
     *
     * @param string $option  One of the options (import, export, delete)
     */
    protected function exec($option)
    {
        if (!$this->services) {
            return true;
        }

        $hosts   = array_unique(array_column($this->services, 'extension_alias_host'));
        $plugins = $this->getPlugins($option, $hosts);

        foreach ($this->services as $row) {
            $this->xdata->setClient(
                $row['extension_alias_host'],
                $row['extension_alias_client'],
                $row['extension_id_client'],
                $row['file']
            );

            $plugins[$row['extension_alias_host']]?->safeRun($this->xdata);
        }

        $this->xdata->resetClient();

        if ($option == 'export') {
            Plugins::get('xdata', 'on_export', $hosts)?->safeRun($this->xdata);
        } elseif ($option == 'import') {
            Plugins::get('xdata', 'on_import', $hosts)?->safeRun($this->xdata);
            Plugins::get('xdata', 'on_update', $hosts)?->safeRun($this->xdata);
        } elseif ($option == 'delete') {
            Plugins::get('xdata', 'on_delete', $hosts)?->safeRun($this->xdata);
            Plugins::get('xdata', 'on_update', $hosts)?->safeRun($this->xdata);
        }

        $this->services = [];
    }

    /**
     * Get
     */
    protected function getExtensionAlias(int $extension_id): string
    {
        return db()->query("
        SELECT extension_alias
        FROM `#__extensions`
        WHERE id = ?", $extension_id)->fetchColumn() ?: '';
    }

    /**
     * Get
     * 
     * @return array
     */
    protected function getHosts(): array
    {
        return array_unique(array_column($this->services, 'extension_alias_host'));
    }

    /**
     * Scan XData plugins.
     */
    protected function getPlugins(string $option, array $hosts): array
    {
        $plugins = [];

        if ($option == 'has') {
            foreach ($hosts as $extension_alias_host) {
                $plugins[$extension_alias_host] = Plugin::get('xdata', $option, $extension_alias_host);
            }
        } else {
            foreach ($hosts as $extension_alias_host) {
                $plugins[$extension_alias_host] = Plugins::get('xdata', $option, $extension_alias_host);
            }
        }

        return $plugins;
    }
}
