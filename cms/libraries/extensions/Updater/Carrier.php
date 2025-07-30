<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Extensions\Updater;

use Junco\Http\Client;
use Archive;
use Exception;

class Carrier
{
    // vars
    protected string $target;
    protected ?Archive $archive = null;

    /**
     * Constructor
     */
    public function __construct(string $target = '')
    {
        $this->target = $target ?: SYSTEM_STORAGE . config('extensions.installer_path');
    }

    /**
     * Get
     */
    public function getTargetPath(): string
    {
        return $this->target;
    }

    /**
     * Get
     * 
     * @param string $search
     * @param int    $page
     * 
     * @return iterable
     */
    public function getListData(string $search, int $page): iterable
    {
        $data = $this->getDefaultWebstoreUrlData();
        $url  = $this->getUrl($data['webstore_url'], 'json');
        $args = ['format' => 'json'];

        if ($search) {
            $args['search'] = $search;
        }
        if ($page) {
            $args['page'] = $page;
        }

        return (new Client)
            ->get($url, ['data' => $args])
            ->getJson(true);
    }

    /**
     * Get
     * 
     * @param string $webstore_url
     * @param array  $developers
     * 
     * @return iterable
     */
    public function getWebstoreData(string $webstore_url, array $developers): iterable
    {
        $webstore_url = $this->getUrl($webstore_url, 'find', ['format' => 'json']);

        return (new Client)
            ->post($webstore_url, ['data' => [
                'php_version' => PHP_VERSION,
                'developers' => $developers
            ]])
            ->getJson(true);
    }

    /**
     * Get
     * 
     * @param array|int $data
     * @param int       $extension_id
     * 
     * @return array
     */
    public function getServerData(array|int $data = 0): array
    {
        $extension_id = 0;

        if (is_int($data)) {
            $extension_id    = $data;
            $data            = $this->getDefaultWebstoreUrlData();
        }

        //
        $webstore_url = $this->getUrl($data['webstore_url'], 'data', [
            'alias' => $data['extension_alias'] ?? $extension_id,
            'format' => 'json'
        ]);

        $json = (new Client())
            ->get($webstore_url)
            ->getJson(true);

        $data = array_merge([
            'extension_alias'    => '',
            'extension_name'    => '',
            'is_close'            => false,
            'extension_key'        => $data['extension_key'] ?? '',
            'download_url'        => '',
            'status'            => ''
        ], $json);

        if (!$data['status']) {
            throw new Exception(sprintf(_t('The extension «%s» is no longer available.'), $data['extension_name']));
        }

        // security
        if (app('system')->isDemo()) {
            $data['extension_key'] = '';
        } elseif ($extension_id) {
            $data['extension_key'] = $this->getExtensionKey($data['extension_alias']);
        }

        return $data;
    }

    /**
     * Get
     * 
     * @param array  $data
     * 
     * @return string
     */
    public function download(array $data): string
    {
        if ($data['is_close']) {
            if (empty($data['extension_key'])) {
                throw new Exception(_t('The key is required.'));
            }

            if (!$this->isValidKey($data['extension_key'])) {
                throw new Exception(_t('The key is invalid.'));
            }

            $data['download_url'] = sprintf($data['download_url'], $data['extension_key']);
        }

        (new Client)->get($data['download_url'], [
            'headers' => [
                'Referer' => (config('site.url') ?: $_SERVER["HTTP_HOST"])
            ]
        ])->moveTo($this->target, $filename);

        return $filename;
    }

    /**
     * Extract
     * 
     * @param string $package
     * @param bool   $delete
     * 
     * @return void
     */
    public function extract(string $package, bool $delete = true): void
    {
        $this->archive ??= new Archive($this->target);
        $this->archive->extract($package, '', $delete);
    }

    /**
     * Change status
     * 
     * @param string $webstore_url
     * @param string $token
     * @param array  $extensions
     * @param string $status
     * 
     * @return string
     */
    public function changeStatus(string $webstore_url, string $token, array $extensions, string $status): string
    {
        $webstore_url = $this->getUrl($webstore_url, 'status', ['format' => 'blank']);
        $data = [
            'token'            => $token,
            'extensions'    => implode(',', $extensions),
            'status'        => $status,
        ];

        return (string)(new Client)
            ->post($webstore_url, ['data' => $data])
            ->getBody();
    }

    /**
     * Distribute
     */
    public function distribute(string $url, string $token, string $file): string
    {
        // vars
        $url = $this->getUrl($url, 'distribute', ['format' => 'blank']);
        $response = (new Client)
            ->post($url, [
                'multipart'  => [
                    ['name' => 'token', 'contents' => $token],
                    ['name' => 'file', 'file' => $this->target . $file],
                ]
            ]);

        return (string)$response->getBody();
    }

    /**
     * Get
     */
    protected function getDefaultWebstoreUrlData(): array
    {
        $data = db()->query("
		SELECT
		 d.webstore_url
		FROM `#__extensions` e
		LEFT JOIN `#__extensions_developers` d ON ( e.developer_id = d.id )
		WHERE extension_alias = 'system'")->fetch() or abort();

        return $data;
    }

    /**
     * Get
     */
    protected function getExtensionKey(string $extension_alias): string
    {
        return db()->query("SELECT extension_key FROM `#__extensions` e WHERE extension_alias = ?", $extension_alias)->fetchColumn();
    }

    /**
     * Get
     * 
     * @param string $url
     * @param string $task
     * @param array  $args
     * 
     * @return string
     */
    protected function getUrl(string $url, string $task, array $args = []): string
    {
        $url = sprintf($url, $task);
        if ($args) {
            $url .= (false === strpos($url, '?') ? '?' : '&') . http_build_query($args);
        }

        return $url;
    }

    /**
     * Validate
     * 
     * @return bool
     */
    protected function isValidKey(string $key): bool
    {
        return preg_match('@^[\w-]{32}$@i', $key);
    }
}
