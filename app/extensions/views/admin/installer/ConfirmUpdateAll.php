<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */


if ($num_updates) {
	$html = sprintf(
		_nt('An update is available.', 'There is available %d updates.', $num_updates),
		$num_updates
	);
} else {
	$html = _t('No updates available.');
}

// modal
$modal = Modal::get();
if ($num_updates) {
	$modal->enter(_t('Update'));
}
$modal->close();
$modal->title(_t('Update all'));
$modal->content = $html;
$modal->form();

return $modal->response();
