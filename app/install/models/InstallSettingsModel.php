<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\UserStatus;
use Junco\Users\UserHelper;

class InstallSettingsModel extends Model
{
    // vars
    protected $db;
    protected int $user_id;
    //
    protected $site_name    = null;
    protected $site_url     = null;
    protected $site_baseurl = null;
    protected $site_email   = null;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
        $this->user_id = config('install.admininstrator_user_id');
    }

    /**
     * Data
     */
    public function getData()
    {
        // query
        $values = $this->db->safeFind("
		SELECT
		 fullname ,
		 username ,
		 email
		FROM `#__users`
		WHERE id = ?", $this->user_id)->fetch() ?: [];

        $values['site_name']    = config('site.name');
        $values['site_url']     = config('site.url');
        $values['site_baseurl'] = config('site.baseurl');
        $values['site_email']   = config('site.email');

        if (!$values['site_url']) {
            $values['site_url']    = $this->calculeUrl();
        }
        if (!$values['site_baseurl']) {
            $values['site_baseurl']    = $this->calculeBaseUrl();
        }

        return ['values' => $values];
    }

    /**
     * Save
     */
    public function save()
    {
        // data
        $this->filter(POST, [
            'site_name'    => '',
            'site_url'     => '',
            'site_baseurl' => 'text',
            'site_email'   => 'email',
            //
            'fullname'     => 'text',
            'username'     => 'text',
            'password'     => '',
            'email'        => 'email',
        ]);

        $this->extract('site_name', 'site_url', 'site_baseurl', 'site_email');

        // validate
        if (!$this->site_name) {
            throw new Exception(_t('Please, fill in the name.'));
        }
        if (!$this->site_url) {
            throw new Exception(_t('Please, verify the site url.'));
        }
        if (!$this->site_email) {
            throw new Exception(_t('Please, fill in with a valid email.'));
        }

        //
        if (!$this->data['fullname']) {
            throw new Exception(_t('Please, fill in the name.'));
        }

        UserHelper::validateUsername($this->data['username']);
        UserHelper::validatePassword($this->data['password']);

        if (!$this->data['email']) {
            throw new Exception(_t('Please, fill in with a valid email.'));
        }

        // query: settings
        (new Settings('site'))->update([
            'name'    => $this->site_name,
            'url'     => $this->sanitizeUrl($this->site_url),
            'baseurl' => $this->sanitizeBaseUrl($this->site_baseurl),
            'email'   => $this->site_email
        ]);

        // query: admin
        $this->data['password'] = UserHelper::hash($this->data['password']);

        $role_id = config('install.admininstrator_role_id') ?: 1;
        $label_id = L_SYSTEM_ADMIN;

        // query
        $this->db->safeExec("INSERT INTO `#__users` (??) VALUES (??) ON DUPLICATE KEY UPDATE ??", $this->data + [
            'id'     => $this->user_id,
            'status' => UserStatus::active
        ]);

        /**
         * Assig user role
         */
        $this->db->safeExec("INSERT IGNORE INTO `#__users_roles_map` (??) VALUES (??)", [
            'user_id' => $this->user_id,
            'role_id' => $role_id,
        ]);

        /**
         * Set admin permission
         * This was pending of "extensions", because it needs the constants.
         */
        $this->db->safeExec("INSERT INTO `#__users_roles_labels_map` (??) VALUES (??) ON DUPLICATE KEY UPDATE ??", [
            'id'       => 1,
            'role_id'  => $role_id,
            'label_id' => $label_id,
            'status'   => 1
        ]);
    }

    /**
     * Calcule
     */
    protected function calculeUrl()
    {
        $protocol = ($_SERVER['HTTPS'] ?? false) ? 'https' : 'http';

        return dirname($protocol . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']);
    }

    /**
     * Calcule
     */
    protected function calculeBaseUrl()
    {
        return $this->sanitizeBaseUrl(dirname($_SERVER['SCRIPT_NAME']));
    }

    /**
     * Sanitize
     */
    protected function sanitizeUrl(string $url)
    {
        return trim($url, '\\/') . '/';
    }

    /**
     * Sanitize
     */
    protected function sanitizeBaseUrl(string $baseurl)
    {
        if ($baseurl && substr($baseurl, -1) != '/') {
            $baseurl .= '/';
        }
        return $baseurl;
    }
}
