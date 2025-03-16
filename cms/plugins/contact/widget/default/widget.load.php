<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$widget) {
	$config = config('contact-widget');
	$config['contact-widget.load_resources'] and app('assets')->css(['assets/contact-widget.min.css']);

	foreach ($config['contact-widget.delivery'] as $partial) {
		if (!$partial) {
			$widget->section(['css' => 'widget-space']);
		} else {
			$html = '';
			$title = null;
			foreach ($partial as $item) {
				switch ($item) {
					case 'basic':
						$title = ($config['contact-widget.show_title'] ? _t('Contact') : '');
						$html .= '<div class="ci-basic">';
						if ($config['contact-widget.address']) {
							$html .= '<p><i class="fa-solid fa-location-pin ci-icon" title="' . ($t = _t('Address')) . '"><span class="visually-hidden">' . $t . '</span></i> ' . $config['contact-widget.address'] . '</p>';
						}
						if ($config['contact-widget.phone']) {
							$html .= '<p><i class="fa-solid fa-phone ci-icon" title="' . ($t = _t('Phone')) . '"><span class="visually-hidden">' . $t . '</span></i> ' . $config['contact-widget.phone'] . '</p>';
						}
						if ($config['contact-widget.email']) {
							$html .= '<p><i class="fa-solid fa-envelope ci-icon" title="' . ($t = _t('Email')) . '"><span class="visually-hidden">' . $t . '</span></i> <a href="' . url('/contact') . '">' . $config['contact-widget.email'] . '</a></p>';
						}
						$html .= '</div>';
						break;

					case 'links':
						if ($config['contact-widget.links']) {
							$html_1 = '';

							foreach ($config['contact-widget.links'] as $i => $row) {
								if (!isset($row['target'])) {
									$row['target'] = '';
								} elseif ($row['target']) {
									$row['target'] = ' target="' . $row['target'] . '"';
								}
								if (!isset($row['color'])) {
									$row['color'] = '';
								} elseif ($row['color']) {
									$row['color'] = ' style="color: ' . $row['color'] . ';"';
								}

								$html_1 .= '<a href="' . $row['url'] . '"' . $row['target']  . ' title="' . $row['title'] . '">'
									. '<i aria-label="' . $row['title'] . '" class="' . $row['icon'] . '"' . $row['color'] . '></i>'
									. '</a>';
							}
							$html .= '<div class="ci-links">' . $html_1 . '</div>';
						}
						break;

					case 'map':
						if ($config['contact-widget.map_code']) {
							if ($title === null) {
								$title = $config['contact-widget.map_show_title'] ? _t('How to get there') : '';
							}

							$html .= '<div class="ci-map">' . $config['contact-widget.map_code'] . '</div>';
						}
						break;
				}
			}

			$widget->section([
				'title' => $title,
				'content' => $html,
				'css' => 'widget-contact'
			]);
		}
	}
};
