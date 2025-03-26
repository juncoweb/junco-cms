<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
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
        $options['logo_text']        = $config['frontend.logo_text'];
        $options['header_fixed']    = $config['frontend.header_fixed'];
        $options['header_css']        = $config['frontend.header_css'];
        $options['theme']            = $config['frontend.theme'];
        $options['on_display']        = $config['frontend.on_display'];
        $options['topbar']            = $config['frontend.topbar'] ?: [];
        $options['navbar']            = $config['frontend.navbar'];
        $options['sidebar']            = $config['frontend.sidebar'];
        $options['footer']            = $config['frontend.footer'];
        $options['hash']            = router()->getHash();

        $this->assets->options($options);
        $this->alter_options        = $config['frontend.alter_options'];
        $this->view                    = __DIR__ . '/view.html.php';
        //
        $this->terms_url            = $config['frontend.terms_url'];
        $this->privacy_url            = $config['frontend.privacy_url'];
        $this->cookie_consent        = $config['frontend.cookie_consent'] && empty($_COOKIE['cookieConsent']);
        $this->user                    = curuser();
    }

    /**
     * Render
     */
    protected function renderLink(): string
    {
        $html = '<link rel="shortcut icon" type="image/x-icon" href="' . $this->site->baseurl . ($this->options->favicon ?? 'favicon.ico') . '" />' . "\n";

        if (!empty($this->options->rss)) {
            $html .= "\t" . '<link rel="alternate" type="application/rss+xml" href="' . $this->site->baseurl . $this->options->rss . '" title="' . $this->site->name . ' RSS" />' . "\n";
        }

        return $html;
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
                    if ($this->user->id) {
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
                        $html .= '<div class="btn-group">'
                            . '<button type="button" control-felem="dropdown" role="caret" class="th-btn"><span data-select-label><i class="capital" aria-hidden="true">' . $this->user->fullname[0] . '</i>' . $this->user->fullname . '</span></button>'
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
                        .   '<span data-select-label><i class="fa-solid fa-sun" aria-hidden="true"></i><i class="fa-solid fa-caret-down ml-2"></i></span>'
                        . '</button>'
                        . '<div role="drop-menu" class="dropdown-menu" style="display: none;">'
                        .  '<ul>'
                        .   '<li><a href="javascript:void(0)" data-value="light"><i class="fa-solid fa-sun"></i> <span>' . _t('Light') . '</span></a></li>'
                        .   '<li><a href="javascript:void(0)" data-value="dark"><i class="fa-solid fa-moon"></i> <span>' . _t('Dark') . '</span></a></li>'
                        .   '<li><a href="javascript:void(0)" data-value="auto"><i class="fa-solid fa-circle-half-stroke"></i> <span>' . _t('Auto') . '</span></a></li>'
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
                    $total = $this->getNotifications();
                    if ($total) {
                        $html .= '<a href="javascript:void(0)" control-tpl="notifications" title="' . ($t = _t('Notifications')) . '" aria-label="' . $t . '" class="th-btn">'
                            . '<i class="fa-solid fa-bell" aria-hidden="true"></i>'
                            . ($total ? '<span class="badge badge-danger badge-small">' . $total  . '</span>' : '')
                            . '</a>';
                    }
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
     * Get
     */
    protected function getNotifications(): int
    {
        return db()->safeFind("
		SELECT COUNT(*)
		FROM `#__notifications`
		WHERE user_id = ?
		AND read_at IS NULL", $this->user->id)->fetchColumn();
    }

    /**
     * Render
     */
    protected function renderCopyright()
    {
        $copyright    = sprintf(_t('© %d by %s - All rights reserved'), date('Y'), '<a href="' . $this->site->url . '">' . $this->site->name . '</a>');
        $legal        = [];

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
    protected function renderCookieConsent()
    {
        if ($this->cookie_consent) {
            $legend = sprintf(
                _t('We use our own and third party cookies to improve the user experience through their navigation. If you continue to browse you accept their use. %sTerms and conditions of use%s'),
                '<a href="' . $this->terms_url . '" target="_blank">',
                '</a>'
            );

            return '<section id="cookieconsent" class="container tpl-legal visible" role="dialog" aria-live="polite" aria-describedby="cc-text cc-btn"><div>'
                .  '<p id="cc-text">' . $legend . '</p>'
                .  '<button id="cc-btn" class="btn btn-small btn-primary btn-solid">' . _t('Understood') . '</button>'
                . '</div></section>' . "\n";
        }
    }
}
