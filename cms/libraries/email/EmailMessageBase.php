<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Email;

abstract class EmailMessageBase implements EmailMessageInterface
{
	// vars
	protected string $site_name		= '';
	protected string $site_url		= '';
	protected string $site_email	= '';
	//
	protected string $CRLF			= "\r\n";
	protected string $color			= '';
	protected string $title			= '';
	protected string $legend		= '';
	protected string $legal			= '';
	protected array  $lines			= [];
	protected array  $templates		= [];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->color      = config('email.snippet_color');
		$this->site_name  = config('site.name');
		$this->site_email = config('site.email');
		$this->site_url   = config('site.url');
		$this->templates = [
			// plain
			'line_plain' => '{{ line }}' . $this->CRLF . $this->CRLF,
			'codelink_plain' => '-------------------------------------------' . $this->CRLF
				. '{{ url }}' . $this->CRLF
				. '-------------------------------------------' . $this->CRLF . $this->CRLF,
			'legend_plain' => '{{ legend }}' . $this->CRLF . '-------' . $this->CRLF,
			'legal_plain' => $this->CRLF . $this->CRLF . '{{ legal }}',
			// html
			'header_html' => '<tr><td bgcolor="{{ color }}" style="color: #ffffff; font-family: Arial, sans-serif; padding: 0px 15px 0px 15px;"><h1 style="font-weight: normal;">{{ header }}</h1></td></tr>',
			'line_html' => '<tr><td style="padding: 15px 15px 15px 15px;">{{ line }}</td></tr>',
			'codelink_html' => '<tr><td style="padding: 15px 15px 15px 15px;">'
				.    '<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">'
				.      '<tr><td bgcolor="#f0f0f0" style="padding: 15px 15px 0px 15px;"><a href="{{ url }}">{{ url }}</a></td></tr>'
				.      '<tr><td bgcolor="#f0f0f0" align="right" style="color: {{ color }}; padding: 5px 15px 5px 15px;"><small>{{ help }}</small></td></tr>'
				.    '</table>'
				.  '</td></tr>',
			'legend_html' => '<tr><td style="padding: 15px 0px 15px 0px;">{{ legend }}</td></tr>',
			'legal_html' => '<tr><td align="center" style="color: #666666; padding: 30px 15px 30px 15px;"><small>{{ legal }}</small></td></tr>',
			'document_html' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
				. '<html xmlns="http://www.w3.org/1999/xhtml">'
				. '<head>'
				.    '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>'
				.    '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>'
				.    '<title>{{ title }}</title>'
				. '</head>'
				. '<body style="color: #333333; font-family: Arial, sans-serif; font-size: 14px; margin: 0; padding: 0;">'
				.   '<table border="0" cellpadding="0" cellspacing="0" width="100%">'
				.   '<tr>'
				.     '<td>'
				.        '<table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse;">{{ body }}</table>'
				.       '</td>'
				.     '</tr>'
				.   '</table>'
				. '</body>'
				. '</html>'
		];
	}

	/**
	 * Title
	 * 
	 * @param string $title
	 */
	public function title(string $title): void
	{
		$this->title = $title;
	}

	/**
	 * Legend
	 * 
	 * @param string $legend
	 */
	public function legend(string $legend): void
	{
		$this->legend = $legend;
	}

	/**
	 * Line
	 * 
	 * @param string $text
	 */
	public function line(string $text, ...$args): void
	{
		if (!$text) {
			return;
		}
		if ($args) {
			$text = vsprintf($text, $args);
		}

		$this->lines[] = ['type' => 'line', 'text' => $text];
	}

	/**
	 * Codelink
	 * 
	 * @param string $url
	 */
	public function codelink(string $url): void
	{
		$this->lines[] = [
			'type' => 'codelink',
			'url' => $url,
			'help' => _t('If the link does not work, copy and paste the link into your browser'),
		];
	}

	/**
	 * Body
	 * 
	 * @param string $html
	 * @param string $plain
	 */
	public function body(string $html, string $plain = ''): void
	{
		$this->lines = [['type' => 'body', 'html' => $html, 'plain' => $plain]];
	}

	/**
	 * Legal
	 * 
	 * @param string $text
	 */
	public function legal(string $text = ''): void
	{
		$this->legal = $text ?: sprintf(
			_t('This message was generated automatically by the site %s. If you have not made any request on this site, please ignore it. Contact: %s'),
			'<a href="' . $this->site_url . '">' . $this->site_name . '</a>',
			'<a href="mailto:' . $this->site_email . '">' . $this->site_email . '</a>'
		);
	}

	/**
	 * Get plain message
	 */
	public function getPlain(): string
	{
		$plain = '';

		if ($this->legend) {
			$plain .= $this->render('legend_plain', [
				'{{ legend }}' => $this->stripTags($this->legend)
			]);
		}

		foreach ($this->lines as $line) {
			switch ($line['type']) {
				case 'body':
					$line['text'] = ($line['plain'] ?: $line['html']);
					//break;

				case 'line':
					$plain .= $this->render('line_plain', [
						'{{ line }}' => $this->stripTags($line['text'])
					]);
					break;

				case 'codelink':
					$plain .= $this->render('codelink_plain', [
						'{{ url }}' => $line['url']
					]);
					break;
			}
		}

		$plain = trim($plain);

		if ($this->legal) {
			$plain .= $this->render('legal_plain', ['{{ legal }}' => $this->stripTags($this->legal)]);
		}

		return  $plain;
	}

	/**
	 * Get html message
	 */
	public function getHtml(): string
	{
		$html = '';

		if ($this->legend) {
			$html .= $this->render('legend_html', ['{{ legend }}' => $this->legend]);
		}

		$html .= $this->render('header_html', ['{{ header }}' => $this->site_name]);

		foreach ($this->lines as $line) {
			switch ($line['type']) {
				case 'body':
					$line['text'] = $line['html'];
					//break;

				case 'line':
					$html .= $this->render('line_html', [
						'{{ line }}' => $line['text']
					]);
					break;

				case 'codelink':
					$html .= $this->render('codelink_html', [
						'{{ url }}' => $line['url'],
						'{{ help }}' => _t('If the link does not work, copy and paste the link into your browser')
					]);
					break;
			}
		}

		if ($this->legal) {
			$html .= $this->render('legal_html', ['{{ legal }}' => $this->legal]);
		}

		$html = $this->render('document_html', [
			'{{ title }}' => ($this->title ?: $this->site_name),
			'{{ body }}' => $html
		]);

		return strtr($html, [
			'{{ color }}'		=> $this->color,
			'{{ site_name }}'	=> $this->site_name,
			'{{ site_email }}'	=> $this->site_email,
			'{{ site_url }}'	=> $this->site_url
		]);
	}

	/**
	 * Get html message
	 */
	protected function stripTags(string $text): string
	{
		if (preg_match_all('/<a (?:.*?)href="(.*?)"(?:.*?)>(.*?)<\/a>/', $text, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$text = str_replace($match[0], "$match[2] ($match[1])", $text);
			}
		}

		if (preg_match_all('/<img (?:.*?)src="(.*?)"(?:.*?)>/', $text, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$text = str_replace($match[0], "img[$match[1]]", $text);
			}
		}

		return trim(strip_tags($text));
	}

	/**
	 * Render
	 */
	protected function render(string $key, array $replaces): string
	{
		return strtr($this->templates[$key], $replaces);
	}
}
