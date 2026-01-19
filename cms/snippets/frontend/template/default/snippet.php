<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class template_frontend_default_snippet extends Template
{
    // vars
    protected $terms_url;
    protected $privacy_url;
    protected $cookie_consent;
    protected $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $config = config('frontend');
        $options = $config['frontend.default_options'];
        $options['logo_img']        = $config['frontend.logo_img'];
        $options['logo_text']       = $config['frontend.logo_text'];
        $options['header_fixed']    = $config['frontend.header_fixed'];
        $options['header_style']    = $config['frontend.header_style'];
        $options['footer_style']    = $config['frontend.footer_style'];
        $options['copyright_style'] = $config['frontend.copyright_style'];
        $options['theme']           = $config['frontend.theme'];
        $options['on_display']      = $config['frontend.on_display'];
        $options['topbar']          = $config['frontend.topbar'] ?: [];
        $options['navbar']          = $config['frontend.navbar'];
        $options['sidebar']         = $config['frontend.sidebar'];
        $options['footer']          = $config['frontend.footer'];
        $options['hash']            = router()->getHash();

        $this->assets->options($options);
        $this->alter_options     = $config['frontend.alter_options'];
        $this->view              = __DIR__ . '/view.html.php';
        //
        $this->terms_url         = $config['frontend.terms_url'];
        $this->privacy_url       = $config['frontend.privacy_url'];
        $this->cookie_consent    = $config['frontend.cookie_consent'];
        $this->user              = curuser();
    }

    /**
     * Get
     */
    protected function getBodyClass(): string
    {
        $css = [];
        if (!empty($this->options->full_body)) {
            $css[] = 'full-body';
        }
        if ($this->options->header_fixed) {
            $css[] = 'fixed-header';
        }
        if ($css) {
            return ' class="' . implode(' ', $css) . '"';
        }

        return '';
    }

    /**
     * Get widget
     * 
     * @param string|array $plugins
     * @param string       $widget
     * 
     * @return string
     */
    protected function getWidget(string|array $plugins, string $widget): string
    {
        $plugins = Plugins::get('widget', 'load', $plugins);

        if ($plugins) {
            $widget = snippet('widget', $widget);
            $plugins->run($widget);

            return $widget->render();
        }

        return '';
    }

    /**
     * Get
     */
    protected function renderLogo()
    {
        // logo
        $html = '';
        if (!empty($this->options->logo_img)) {
            $html = '<img src="' . $this->options->logo_img . '" alt="' . $this->site->name . '"/>';
        }
        if (!empty($this->options->logo_text)) {
            if ($html) {
                $html = '<div><div>' . $html . '</div><div>' . $this->options->logo_text . '</div></div>';
            } else {
                $html .= $this->options->logo_text;
            }
        }
        if (!$html) {
            $html = $this->site->name;
        }

        return $html;
    }

    /**
     * Render
     */
    protected function renderTopHeader()
    {
        $html = '';
        foreach ($this->options->topbar as $option) {
            switch ($option) {
                case 'login':
                    if ($this->user->getId()) {
                        $li = '';

                        if (config('system.statement') == 21) {
                            $li .= '<li><a href="' . url('/profile') . '">' . _t('Profile') . '</a></li>';
                        } else {
                            $li .= '<li><a href="' . url('/usys.account') . '">' . _t('Account') . '</a></li>';
                        }

                        $li .= '<li><a href="' . url('my') . '">' . _t('My space') . '</a></li>';

                        if ($this->user->isAdmin()) {
                            $li .= '<li><a href="' . url('admin') . '">' . _t('Administration') . '</a></li>';
                        }

                        $li .= '<li><a href="javascript:void(0)" control-tpl="logout">' . _t('Log out') . '</a></li>';
                        $name = $this->user->getName();
                        $html .= '<div class="btn-group">'
                            . '<button type="button" control-felem="dropdown" role="caret" class="th-btn"><span data-select-label><i class="capital" aria-hidden="true">' . $name[0] . '</i>' . $name . '</span></button>'
                            . '<div role="drop-menu" class="dropdown-menu" style="display: none;">'
                            .   '<ul>' . $li . '</ul>'
                            . '</div>'
                            . '</div>';
                    } else {
                        $html .= '<a href="' . url('/usys/login') . '" class="th-btn">' . _t('Log in') . '</a>';
                    }
                    break;

                case 'theme':
                    $html .= '<div class="btn-group">'
                        . '<button type="button" control-felem="dropdown" control-tpl="theme" role="caret" class="th-btn">'
                        .   '<span class="only-on-light"><i class="fa-solid fa-sun" aria-hidden="true"></i><div class="visually-hidden">' . ($t1 = _t('Light')) . '</div></span>'
                        .   '<span class="only-on-dark"><i class="fa-solid fa-moon" aria-hidden="true"></i><div class="visually-hidden">' . ($t2 = _t('Dark')) . '</div></span>'
                        .   '<span class="only-on-auto"><i class="fa-solid fa-circle-half-stroke" aria-hidden="true"></i><div class="visually-hidden">' . ($t3 = _t('Auto')) . '</div></span>'
                        .   '<i class="fa-solid fa-caret-down ml-2"></i>'
                        . '</button>'
                        . '<div role="drop-menu" class="dropdown-menu" style="display: none;">'
                        .  '<ul>'
                        .   '<li><a href="javascript:void(0)" data-value="light"><i class="fa-solid fa-sun"></i> <span>' . $t1 . '</span></a></li>'
                        .   '<li><a href="javascript:void(0)" data-value="dark"><i class="fa-solid fa-moon"></i> <span>' . $t2 . '</span></a></li>'
                        .   '<li><a href="javascript:void(0)" data-value="auto"><i class="fa-solid fa-circle-half-stroke"></i> <span>' . $t3  . '</span></a></li>'
                        .  '</ul>'
                        . '</div>'
                        . '</div>';
                    break;

                case 'language':
                    $languages = (new LanguageHelper)->getAvailables();
                    if (count($languages) > 1) {
                        $li = '';
                        foreach ($languages as $value => $name) {
                            $li .= '<li><a href="javascript:void(0)" data-value="' . $value . '">' . $name . '</a></li>';
                        }

                        $html .= '<div class="btn-group">'
                            . '<button type="button" control-felem="dropdown" control-tpl="language" role="caret" class="th-btn">'
                            .   '<span data-select-label>' . $this->getLang() . '<i class="fa-solid fa-caret-down ml-2"></i></span>'
                            . '</button>'
                            . '<div role="drop-menu" class="dropdown-menu" style="display: none;">'
                            .  '<ul>' . $li . '</ul>'
                            . '</div>'
                            . '</div>';
                    }

                    break;

                case 'notifications':
                    $html .= '<a href="javascript:void(0)" control-tpl="notifications" title="' . ($t = _t('Notifications')) . '" aria-label="' . $t . '" class="th-btn">'
                        . '<i class="fa-solid fa-bell" aria-hidden="true"></i>'
                        . '<span class="badge badge-danger badge-small rounded-full" style="display: none;"></span>'
                        . '</a>';
                    break;

                case 'search':
                    $html .= '<a href="' . url('/search') . '" control-tpl="search" title="' . ($t = _t('Search')) . '" class="th-btn"><i aria-label="' . $t . '" class="fa-solid fa-magnifying-glass"></i></a>';
                    break;

                case 'contact':
                    $html .= '<a href="' . url('/contact') . '" title="' . ($t = _t('Contact')) . '" class="th-btn"><i aria-label="' . $t . '" class="fa-solid fa-envelope"></i></a>';
                    break;
            }
        }

        return $html;
    }

    /**
     * Render
     */
    protected function renderCopyright()
    {
        $copyright = sprintf(_t('© %d by %s - All rights reserved'), date('Y'), '<a href="' . $this->site->url . '">' . $this->site->name . '</a>');
        $legal = [];

        if ($this->terms_url) {
            $legal[] = '<a href="' . $this->terms_url . '">' . _t('Terms & Conditions') . '</a>';
        }
        if ($this->privacy_url) {
            $legal[] = '<a href="' . $this->privacy_url . '">' . _t('Privacy') . '</a>';
        }

        if ($legal) {
            return '<div class="box"><div>' . $copyright . '</div><div>' . implode(' | ', $legal) . '</div></div>' . "\n";
        }

        return '<a href="' . $this->site->url . '">' . $this->site->name . '</a> - ' . $this->site->description . '<br />' . $copyright . "\n";
    }

    /**
     * Render
     */
    protected function renderCookieConsent(): string
    {
        return $this->cookie_consent
            ? (Plugin::get('cookie-consent', 'load', $this->cookie_consent)?->run() ?? '')
            : '';
    }
}
