<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class InstallFinishModel extends Model
{
    // vars
    protected $db;
    protected string $bootstrap_file;
    protected string $install_file;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db             = db();
        $this->bootstrap_file = SYSTEM_ABSPATH . 'bootstrap.php';
        $this->install_file   = SYSTEM_ABSPATH . 'app/install';
    }

    /**
     * Get Data
     */
    public function getData()
    {
        // data
        $this->filter(GET, [
            'take'     => '',
            'remove_r' => '',
            'remove_e' => '',
            'redirect' => '',
            'goto'     => '',
        ]);

        if ($this->data['take']) {
            return $this->take();
        }

        $data = $this->getUserData(config('install.admininstrator_user_id')) or redirect();

        return [
            'fullname' => $data['fullname'],
            'site_name' => config('site.name'),
            'values' => [
                'remove_r' => true,
                'remove_e' => true,
                'redirect' => 1,
                'take'     => 1,
                'goto'     => $this->data['goto'],
            ],
            'bootstrap_is_writable' => is_writable($this->bootstrap_file),
            'install_is_writable' => is_writable($this->install_file),
        ];
    }

    /**
     * Take
     */
    public function take()
    {
        try {
            if ($this->data['remove_r']) {
                $this->removeRedirection();
            }

            if ($this->data['remove_e']) {
                $this->removeInstallExtension();
            }

            if ($this->data['redirect'] == 1) {
                redirect();
            }

            if ($this->data['redirect'] == 2) {
                redirect(['admin/']);
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Get
     */
    protected function getUserData(int $user_id): array|false
    {
        return $this->db->query("
        SELECT
         id ,
         fullname
        FROM `#__users`
        WHERE id = ?", $user_id)->fetch();
    }

    /**
     * Remove
     */
    protected function removeRedirection(): void
    {
        $contents = file_get_contents($this->bootstrap_file);
        $contents = preg_replace('/\# Redirect to install(.*)/s', '', $contents);

        @file_put_contents($this->bootstrap_file, $contents);
    }

    /**
     * Remove
     */
    protected function removeInstallExtension(): void
    {
        $extension_id = $this->db->query("
        SELECT
         id 
        FROM `#__extensions`
        WHERE extension_alias = 'install'")->fetchColumn();

        if (!$extension_id) {
            return;
        }

        (new ExtensionsModel)->setData([
            'id' => [$extension_id],
            'option' => ['files' => true, 'data' => true]
        ])->delete();
    }
}
