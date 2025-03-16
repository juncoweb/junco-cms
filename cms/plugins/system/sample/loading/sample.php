<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// vars
$rows = [
	[
		'code' => 'JsLoading(true);',
		'details' => '',
	],
	[
		'code' => 'JsLoading(false);',
		'details' => '',
	],
];

$table = snippet('sample_table', 'samples');
foreach ($rows as $i => $row) {
	$table->row($i + 1, $row['code'], $row['details']);
}


// template
$tpl = Template::get();
$tpl->options([
	'thirdbar' => 'system.thirdbar'
]);
$tpl->title('Spinner');
$tpl->content = $table->render();

return $tpl->response();
