<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Users\Curuser;

class template_backend_default_snippet extends Template
{
    // vars
    protected Curuser $user;
    protected bool    $isMinimized;
    protected string  $themeColorKey;
    protected string  $themeColor;

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = config('backend');
        $options            = $config['backend.default_options'];
        $options['theme']   = $config['backend.theme'];
        $options['mainbar'] = $config['backend.mainbar'];
        $options['sidebar'] = $config['backend.sidebar'];

        parent::__construct();
        $this->assets->options($options);
        $this->view          = __DIR__ . '/view.html.php';
        $this->user          = curuser();
        $this->isMinimized   = (bool)cookie('BackendNavbar');
        $this->themeColorKey = 'ThemeColor' . $this->user->getId();
        $this->themeColor    = cookie($this->themeColorKey, $config['backend.header_color']);
    }

    /**
     * Get
     */
    protected function getCapital()
    {
        return implode(array_map(function ($part) {
            return $part ? $part[0] : '';
        }, explode(' ', $this->site->name)));
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
}
