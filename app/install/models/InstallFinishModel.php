<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class InstallFinishModel extends Model
{
    // vars
    protected $db                = null;
    protected $bootstrap_file    = null;
    protected $install_file        = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db                = db();
        $this->bootstrap_file    = SYSTEM_ABSPATH . 'bootstrap.php';
        $this->install_file        = SYSTEM_ABSPATH . 'app/install';
    }

    /**
     * Get Data
     */
    public function getData()
    {
        // data
        $this->filter(GET, [
            'take'        => '',
            'remove_r'    => '',
            'remove_e'    => '',
            'redirect'    => '',
            'goto'        => '',
        ]);

        if ($this->data['take']) {
            $this->take();
        }

        // vars
        $admininstrator_user_id = config('install.admininstrator_user_id');
        $fullname = $this->db->safeFind("SELECT fullname FROM `#__users` WHERE id = ?", $admininstrator_user_id)->fetchColumn();

        // security
        $fullname or redirect();

        return [
            'fullname' => $fullname,
            'site_name' => config('site.name'),
            'values' => [
                'remove_r'    => true,
                'remove_e'    => true,
                'redirect'    => 1,
                'take'        => 1,
                'goto'        => $this->data['goto'],
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
                $contents = file_get_contents($this->bootstrap_file);
                $contents = preg_replace('/\# Redirect to install(.*)/s', '', $contents);

                @file_put_contents($this->bootstrap_file, $contents);
            }

            if ($this->data['remove_e']) {
                // query
                $extension_id = $this->db->safeFind("SELECT id FROM `#__extensions` WHERE extension_alias = 'install'")->fetchColumn();
                if ($extension_id) {
                    (new ExtensionsModel)->setData([
                        'id' => [$extension_id],
                        'option' => ['files' => true, 'data' => true]
                    ])->delete();
                }
            }

            if ($this->data['redirect'] == 1) {
                redirect();
            } elseif ($this->data['redirect'] == 2) {
                redirect(['admin/']);
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
