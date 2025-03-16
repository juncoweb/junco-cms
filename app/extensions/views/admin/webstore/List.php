<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

if (!empty($error)) {
	$html = '<div class="dialog dialog-warning mt-4">' . $error . '</div>';
} else {
	// list
	$bls = Backlist::get();

	// filters
	$bft = $bls->getFilters();
	$bft->setValues($data);
	$bft->search();

	// table
	$bls->th(['width' => 100]);
	$bls->th();
	$bls->th(['width' => 80]);

	if ($rows) {
		$download_tag = '<button type="button" class="btn%s" control-list="confirm_download" data-name="extension_id" data-value="%d">' . _t('Download') . '</button>';
		$installed_tag = '<div class="color-light text-right text-nowrap">' . _t('It is installed.') . '</div>';
		$details_tag = '<a href="%s" class="ws-title">%s</a>'
			. '<div class="ws-details">'
			. _t('By %s')
			. '<div>' . snippet('rating', 'utils')->render('%d') . ' | <span>%d ' . _t('Visits') . '</span></div>'
			. '</div>';

		foreach ($rows as $row) {
			$bls->td('<div class="box-primary box-solid"><img src="' . $row['image'] . '" alt="' . $row['image'] . '" class="responsive ws-image" /></div>');
			$bls->td(sprintf(
				$details_tag,
				$row['details_url'],
				$row['name'],
				'<b>' . $row['developer'] . '</b>',
				$row['num_ratings'],
				$row['num_views']
			));
			if ($row['is_installed']) {
				$bls->td(sprintf($download_tag, '', $row['id']) . $installed_tag);
			} else {
				$bls->td(sprintf($download_tag, ' btn-primary btn-solid', $row['id']));
			}
		}
	}
	$html = $bls->render($pagi);
}

return $html;
