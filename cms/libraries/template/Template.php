<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Responder\ResponderBase;
use Psr\Http\Message\ResponseInterface;

class Template extends ResponderBase implements TemplateInterface
{
	// vars
	protected array  $config;
	protected Assets $assets;
	protected object $options;
	protected ?array $alter_options	= null;
	protected object $site;
	//
	protected $pathway				= null;
	protected $title				= null;
	protected array $title_options	= [];
	protected $help_url				= null;
	public    $content				= null;
	protected $view					= null;

	/**
	 * Get Snippet
	 * 
	 * @param string $snippet
	 */
	public static function get(string $snippet = ''): TemplateInterface
	{
		if (!$snippet) {
			if (router()->getAccessPoint() == 'admin') {
				$snippet = config('template.backend_default_snippet');
			} else {
				$snippet = config('template.frontend_default_snippet');
			}
		}
		return snippet('template', $snippet);
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->config = config('template');
		$this->assets = app('assets');
		//
		$site = config('site');
		$this->site = (object)[
			'name'			=> $site['site.name'],
			'description'	=> $site['site.description'],
			'author'		=> $site['site.author'],
			'email'			=> $site['site.email'],
			'url'			=> $site['site.url'],
			'baseurl'		=> $site['site.baseurl'],
		];
	}

	/**
	 * Seo
	 * 
	 * @param string $description
	 * @param string $keywords
	 */
	public function seo(string $description = '', string $keywords = '')
	{
		if ($description) {
			$this->assets->meta(['name' => 'description', 'content' => $description]);
		}
		if ($keywords) {
			$this->assets->meta(['name' => 'keywords', 'content' => $keywords]);
		}
	}

	/**
	 * Create a meta tag
	 *
	 * @param array $attr An array with the attributes of the tag
	 */
	public function meta(array $attr)
	{
		$this->assets->meta($attr);
	}

	/**
	 * Render meta tags
	 * 
	 * @return string
	 */
	protected function renderMeta(): string
	{
		$meta = $this->assets->getMeta();
		$html = '';

		foreach ($meta as $attr) {
			$html .= "\t" . '<meta' . $this->merge_attributes($attr) . ' />' . "\n";
		}

		return $html . "\n";
	}

	/**
	 * Load a text editor
	 *
	 * @param string $plugin The plugin with a particular editor
	 */
	public function editor(string $plugin = ''): void
	{
		Plugins::get('editor', 'load', $plugin ?: config('system.default_editor'))?->run($this);
	}

	/**
	 * Load style sheets
	 *
	 * @param string|array $css  A list of style sheets to load
	 */
	public function css(string|array $css = ''): void
	{
		$this->assets->css($css);
	}

	/**
	 * Render style sheets
	 * 
	 * @return string
	 */
	protected function renderCss(): string
	{
		$css = $this->assets->getCss();
		if ($this->config['template.explain_assets']) {
			$css = (new AssetsExplainer)->explain($css);
		} else {
			if ($this->config['template.version_control']) {
				foreach ($css as $i => $attr) {
					if (isset($this->config['template.version_control'][$attr['href']])) {
						$css[$i]['href'] .= '?v=' . $this->config['template.version_control'][$attr['href']];
					}
				}
			}

			$theme = $this->assets->getOption('theme');
			if ($theme) {
				foreach ($css as $i => $attr) {
					if (preg_match('@^assets/(.*?)$@', $attr['href'], $match)) {
						$css[$i]['href'] = 'assets/' . $theme . '/' . $match[1];
					}
				}
			}
		}

		$html = '';
		if ($css) {
			foreach ($css as $attr) {
				if (substr($attr['href'], 0, 4) != 'http') {
					$attr['href'] = $this->site->baseurl . $attr['href'];
				}
				$html .= "\t" . '<link' . $this->merge_attributes($attr) . '/>' . "\n";
			}
			$css = [];
		}

		return $html;
	}

	/**
	 * Load javascripts resources
	 *
	 * @param string|array $js         A list of scripts to load
	 * @param bool         $in_head
	 */
	public function js(string|array $js = '', bool $in_head = false): void
	{
		$this->assets->js($js, $in_head);
	}

	/**
	 * Load functions that will be executed when loading the page
	 *
	 * @param string $script    A javascript function
	 */
	public function domready(string $script = ''): void
	{
		$this->assets->domready($script);
	}

	/**
	 * Render Javascript
	 * 
	 * @param array $js
	 * 
	 * @return string
	 */
	private function realRenderJs(array $js): string
	{
		if ($this->config['template.explain_assets']) {
			$js = (new AssetsExplainer)->explain($js);
		} elseif ($this->config['template.version_control']) {
			foreach ($js as $i => $attr) {
				if (isset($attr['src']) && isset($this->config['template.version_control'][$attr['src']])) {
					$js[$i]['src'] .= '?v=' . $this->config['template.version_control'][$attr['src']];
				}
			}
		}

		$html = '';
		foreach ($js as $attr) {
			if (isset($attr['content'])) {
				$content = $attr['content'];
				unset($attr['content']);
			} else {
				$content = '';

				if (
					isset($attr['src'])
					&& substr($attr['src'], 0, 4) != 'http'
				) {
					$attr['src'] = $this->site->baseurl . $attr['src'];
				}
			}

			$html .= "\t" . '<script' . $this->merge_attributes($attr) . '>' . $content . '</script>' . "\n";
		}

		return $html;
	}

	/**
	 * Render javascript of head
	 */
	protected function renderHeadJs()
	{
		$js = $this->assets->getJs(true);
		if ($js) {
			return $this->realRenderJs($js);
		}

		return '';
	}

	/**
	 * Render javascript
	 */
	protected function renderJs()
	{
		$js			= $this->assets->getJs();
		$domready	= $this->assets->getDomready();
		$html		= '';

		if ($js) {
			$html .= $this->realRenderJs($js);
		}
		if ($domready) {
			$html .= "\t" . '<script>window.addEventListener("load", function(){ ' . implode(';', $domready) . ' }, false);</script>' . "\n";
		}

		return $html;
	}

	/**
	 * Load a set of values that will be passed to the template.
	 *
	 * @param array|string|null $options A list of keys / values.
	 */
	public function options(array|string|null $options = null): void
	{
		$this->assets->options($options);
	}

	/**
	 * Returns the value of a variable.
	 *
	 * @param string $name
	 */
	public function getOption(string $name): mixed
	{
		return $this->assets->getOption($name);
	}

	/**
	 * Get
	 */
	protected function getLang()
	{
		return explode('_', app('language')->getCurrent())[0];
	}

	/**
	 * Set the pathway of the page
	 *
	 * @param string|array $value
	 */
	public function pathway(string|array $value): void
	{
		$this->pathway = $value;
	}

	/**
	 * Get the pathway of the page
	 *
	 * @param string $separator
	 */
	protected function getPathway(string $separator = ' / ')
	{
		return is_array($this->pathway) ? implode($separator, $this->pathway) : $this->pathway;
	}

	/**
	 * Set the title of the page
	 *
	 * @param array|string $title
	 * @param array|string $options
	 */
	public function title(array|string $title, array|string $options = []): void
	{
		$this->title = $title;
		$this->title_options = is_array($options)
			? $options
			: ['icon' => $options];
	}

	/**
	 * Get the title of the page
	 *
	 * @param string $separator
	 */
	protected function getTitle(string $separator = ' &gt; ')
	{
		return is_array($this->title)
			? implode($separator, $this->title)
			: $this->title;
	}

	/**
	 * Get
	 * 
	 * @return 
	 */
	protected function getDocumentTitle()
	{
		$title = $this->title_options['document_title'] ?? '';

		if ($title) {
			$title .= ' - ';
		} elseif ($this->title) {
			$title = $this->getTitle(' · ') . ' - ';
		}

		return $title .= $this->site->name;
	}

	/**
	 * Hash
	 *
	 * @param string $value
	 */
	public function hash(string $value): void
	{
		$this->assets->setOption('hash', $value);
	}

	/**
	 * Help link
	 *
	 * @param string $url
	 */
	public function helpLink(string $url): void
	{
		$this->help_url	= $url;
	}

	/**
	 * Merge Attributes
	 * 
	 * @param array $attr
	 * 
	 * @return string
	 */
	protected function merge_attributes(array $attr): string
	{
		$html = '';
		foreach ($attr as $k => $v) {
			$html .=  ' ' . $k . '="' . $v . '"';
		}

		return $html;
	}

	/**
	 * Creates a simplified response with a message.
	 * 
	 * @param string $message
	 * @param int    $code
	 * 
	 * @return ResponseInterface
	 */
	public function message(string $message = '', int $code = 0): ResponseInterface
	{
		// 401 Unauthorized - The user must login. I display the login page in the current template.
		// 403 Forbidden - The user has insufficient permissions.
		// 404 Not Found - I display the message in the current template.
		// 500 Internal Server Error.
		if ($code == 401) {
			redirect(401); // login
		}

		if ($code == 403) {
			return (new Debugger)->alert($message, $code);
		}

		$this->title($code ? sprintf(_t('%d. That’s an error.'), $code) : _t('Error'));
		$this->content	= '<div class="mt-8 mb-8 italic"><p>' . $message . '</p>';

		$response = $this->response();

		if ($code >= 100 && $code <= 599) {
			$response = $response->withStatus($code);
		}

		return $response;
	}

	/**
	 * Creates a simplified response with an alert message.
	 * 
	 * @param string $message
	 * @param int    $code
	 * 
	 * return ResponseInterface
	 */
	public function alert(string $message = '', int $code = 0): ResponseInterface
	{
		return $this->message($message, $code);
	}

	/**
	 * Create a response.
	 * 
	 * @return ResponseInterface
	 */
	public function response(): ResponseInterface
	{
		# plugins
		$on_display = $this->assets->getOption('on_display');
		if ($on_display) {
			Plugins::get('on_display', 'load', $on_display)?->run($this);
		}

		$load = $this->assets->getOption('load');
		if ($load) {
			if (!is_array($load)) {
				$load = [$load];
			}

			foreach ($load as $item) {
				if ($item === 'default') {
					$config = config('frontend');

					$this->options($config['frontend.default_options'] + [
						'theme' => $config['frontend.theme']
					]);
					$this->alter_options = $config['frontend.alter_options'];
				} elseif (isset($this->alter_options[$item])) {
					$this->assets->options($this->alter_options[$item]);
				}
			}
		}

		if ($this->view) {
			$this->options = $this->assets->getOptions();

			ob_start();
			$content = include $this->view;
			if ($content === 1) {
				$content = '';
			}

			$this->content = $this->getOutputBuffer() . $content;
		}

		return $this->createTextResponse($this->content);
	}
}
