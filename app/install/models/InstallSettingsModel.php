<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
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
        $values = $this->db->query("
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
            $values['site_url'] = $this->calculeUrl();
        }
        if (!$values['site_baseurl']) {
            $values['site_baseurl'] = $this->calculeBaseUrl();
        }

        return ['values' => $values];
    }

    /**
     * Save
     */
    public function save()
    {
        $data = $this->filter(POST, [
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

        // slice
        $site_name    = $this->slice($data, 'site_name');
        $site_url     = $this->slice($data, 'site_url');
        $site_baseurl = $this->slice($data, 'site_baseurl');
        $site_email   = $this->slice($data, 'site_email');

        // validate
        if (!$site_name) {
            return $this->unprocessable(_t('Please, fill in the name.'));
        }
        if (!$site_url) {
            return $this->unprocessable(_t('Please, verify the site url.'));
        }
        if (!$site_email) {
            return $this->unprocessable(_t('Please, fill in with a valid email.'));
        }

        //
        if (!$data['fullname']) {
            return $this->unprocessable(_t('Please, fill in the name.'));
        }

        UserHelper::validateUsername($data['username']);
        UserHelper::validatePassword($data['password']);

        if (!$data['email']) {
            return $this->unprocessable(_t('Please, fill in with a valid email.'));
        }

        // query: settings
        (new Settings('site'))->update([
            'name'    => $site_name,
            'url'     => $this->sanitizeUrl($site_url),
            'baseurl' => $this->sanitizeBaseUrl($site_baseurl),
            'email'   => $site_email
        ]);

        // query: admin
        $data['password'] = UserHelper::hash($data['password']);

        $role_id = config('install.admininstrator_role_id') ?: 1;
        $label_id = L_SYSTEM_ADMIN;

        // query
        $this->db->exec("INSERT INTO `#__users` (??) VALUES (??) ON DUPLICATE KEY UPDATE ??", $data + [
            'id'     => $this->user_id,
            'status' => UserStatus::active
        ]);

        /**
         * Assig user role
         */
        $this->db->exec("INSERT IGNORE INTO `#__users_roles_map` (??) VALUES (??)", [
            'user_id' => $this->user_id,
            'role_id' => $role_id,
        ]);

        /**
         * Set admin permission
         * This was pending of "extensions", because it needs the constants.
         */
        $this->db->exec("INSERT INTO `#__users_roles_labels_map` (??) VALUES (??) ON DUPLICATE KEY UPDATE ??", [
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
        $uri = request()->getUri();

        return dirname($uri->getScheme() . '://' . $uri->getHost() . $uri->getPath());
    }

    /**
     * Calcule
     */
    protected function calculeBaseUrl()
    {
        $uri = request()->getUri();

        return $this->sanitizeBaseUrl(dirname($uri->getPath()));
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
