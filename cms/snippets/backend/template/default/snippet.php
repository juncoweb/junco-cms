<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class template_backend_default_snippet extends Template
{
	// vars
	protected $user = null;
	protected $isMinimized = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$config = config('backend');
		$options = $config['backend.default_options'];
		$options['theme'] = $config['backend.theme'];
		$options['mainbar'] = $config['backend.mainbar'];
		$options['sidebar'] = $config['backend.sidebar'];
		$options['header_color'] = $config['backend.header_color'];

		parent::__construct();
		$this->assets->options($options);
		$this->view	= __DIR__ . '/view.html.php';
		$this->user = curuser();
		$this->isMinimized = $_COOKIE['BackendNavbar'] ?? false;
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
