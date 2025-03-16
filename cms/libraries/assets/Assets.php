<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

class Assets
{
	// vars
	protected $options	= null;
	protected $meta		= [];
	protected $css		= [];
	protected $head_js	= [];
	protected $js		= [];
	protected $domready	= [];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->options = new stdClass();
	}

	/**
	 * Destruct
	 */
	public function __destruct()
	{
		$this->clear();
	}

	/**
	 * Clear
	 * 
	 * @return void
	 */
	public function clear(): void
	{
		$this->options	= null;
		$this->meta		= [];
		$this->css		= [];
		$this->head_js	= [];
		$this->js		= [];
		$this->domready	= [];
	}

	/**
	 * Create a meta tag
	 *
	 * @param array $attr An array with the attributes of the tag
	 * 
	 * @return void
	 */
	public function meta(array $attr): void
	{
		$this->meta[] = $attr;
	}

	/**
	 * Get meta tags
	 * 
	 * @return array
	 */
	public function getMeta(): array
	{
		return $this->meta;
	}

	/**
	 * Load style sheets
	 *
	 * @param string|array $css  A list of style sheets to load
	 * 
	 * @return void
	 */
	public function css(string|array $css = ''): void
	{
		if (!$css) {
			return;
		}
		if (!is_array($css)) {
			$css = $this->toArray($css);
		}

		foreach ($css as $value) {
			if (is_array($value)) {
				$value = array_merge(['rel' => 'stylesheet', 'type' => 'text/css'], $value);
			} else {
				$value = ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => $value];
			}

			// I avoid repeated loads
			$this->css[$value['href']] = $value;
		}
	}

	/**
	 * Print style sheets
	 * 
	 * @return array
	 */
	public function getCss(): array
	{
		return $this->css;
	}

	/**
	 * Load javascripts resources
	 *
	 * @param string|array $js         A list of scripts to load
	 * @param bool         $inHead
	 * 
	 * @return void
	 */
	public function js(string|array $js = '', bool $inHead = false): void
	{
		if (!$js) {
			return;
		}
		if (!is_array($js)) {
			$js = $this->toArray($js);
		}

		$prop = $inHead ? 'head_js' : 'js';

		foreach ($js as $value) {
			if (!$value) {
				continue;
			}
			if (!is_array($value)) {
				$value = ['src' => $value];
			}
			if (isset($value['src'])) {
				// I avoid repeated loads
				$this->$prop[$value['src']] = $value;
			} else {
				$this->$prop[] = $value;
			}
		}
	}

	/**
	 * Get Javascript
	 * 
	 * @param bool $inHead
	 * 
	 * @return array
	 */
	public function getJs(bool $inHead = false): array
	{
		if ($inHead) {
			return $this->head_js;
		}
		return $this->js;
	}

	/**
	 * Load functions that will be executed when loading the page
	 *
	 * @param string $script    A javascript function
	 * 
	 * @return void
	 */
	public function domready(string $script = ''): void
	{
		if ($script) {
			$this->domready[] = $script;
		}
	}

	/**
	 * Get javascript
	 * 
	 * @return array
	 */
	public function getDomready(): array
	{
		return $this->domready;
	}

	/**
	 * Load a set of values that will be passed to the template.
	 *
	 * @param array|null $options A list of keys / values.
	 * 
	 * @return void
	 */
	public function options(?array $options = null): void
	{
		if (!$options) {
			return;
		}

		foreach ($options as $key => $value) {
			switch ($key) {
				case 'css':
					if ($value) {
						$this->css($value);
					}
					break;

				case 'js':
					if ($value) {
						$this->js($value);
					}
					break;

				case 'head_js':
					if ($value) {
						$this->js($value, true);
					}
					break;

				case 'domready':
					if ($value) {
						$this->domready[] = $value;
					}
					break;

				default:
					if ($key) {
						$this->options->$key = $value;
					}
					break;
			}
		}
	}

	/**
	 * Set
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public function setOption(string $name, mixed $value): void
	{
		$this->options->$name = $value;
	}

	/**
	 * Get
	 *
	 * @param string $name
	 * 
	 * @return mixed
	 */
	public function getOption(string $name): mixed
	{
		return $this->options->$name ?? null;
	}

	/**
	 * Get
	 * 
	 * @return mixed
	 */
	public function getOptions(): mixed
	{
		return $this->options;
	}

	/**
	 * Transform input string to array.
	 * 
	 * @param string $input
	 * 
	 * @return array
	 */
	protected function toArray(string $input): array
	{
		return explode(',', str_replace([' ', "\t", "\n", "\r"], '', $input));
	}
}
