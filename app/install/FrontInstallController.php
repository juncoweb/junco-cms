<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class FrontInstallController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        return $this->view('Language', [
            'availables' => (new LanguageHelper)->getAvailables()
        ]);
    }

    /**
     * Language
     */
    public function takeLanguage()
    {
        return $this->wrapper(fn() => (new InstallLanguageModel)->change());
    }

    /**
     * License
     */
    public function license()
    {
        return $this->view(null, (new InstallLicenseModel)->getData());
    }

    /**
     * Requirements
     */
    public function requirements()
    {
        return $this->view(null, (new InstallRequirementsModel)->getData());
    }

    /**
     * Database
     */
    public function database()
    {
        return $this->view(null, (new InstallDatabaseModel)->getData());
    }

    /**
     * Take Database
     */
    public function takeDatabase()
    {
        return $this->wrapper(fn() => (new InstallDatabaseModel)->save());
    }

    /**
     * Extensions
     */
    public function extensions()
    {
        return $this->middleware('install.database.exists')
            ?: $this->view();
    }

    /**
     * Extensions
     */
    public function takeExtensions()
    {
        return $this->middleware('install.database.exists')
            ?: $this->wrapper(fn() => (new InstallExtensionsModel)->install());
    }

    /**
     * Settings
     */
    public function settings()
    {
        return $this->middleware('install.database.exists', 'install.user.table.exists')
            ?: $this->view(null, (new InstallSettingsModel)->getData());
    }

    /**
     * Take Settings
     */
    public function takeSettings()
    {
        return $this->middleware('install.database.exists', 'install.user.table.exists')
            ?: $this->wrapper(fn() => (new InstallSettingsModel)->save());
    }

    /**
     * Finish
     */
    public function finish()
    {
        return $this->middleware('install.database.exists', 'install.user.table.exists')
            ?: $this->view(null, (new InstallFinishModel)->getData());
    }

    /**
     * Take Finish
     */
    public function takeFinish()
    {
        $this->middleware('install.database.exists', 'install.user.table.exists')
            and (new InstallFinishModel)->take();
        //return $this->view();
    }
}
