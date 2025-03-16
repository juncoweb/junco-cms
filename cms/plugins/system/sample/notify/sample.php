<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// vars
$rows = [
	[
		'code' => 'JsNotify({"message":"Hello world!","target":"#notify_1"})',
		'details' => '<div id="notify_1" role="alert" class="notify-box"></div>',
	],
	[
		'code' => 'JsNotify({"message":"Hello world!","target":"#notify_2"})',
		'details' => '<div id="notify_2" role="alert" class="notify-box"></div>',
	],
	[
		'code' => 'JsNotify.creator("#notify_3").notify("Hello world!")',
		'details' => '<div id="notify_3" role="alert" class="notify-box"></div>',
	],
	[
		'code' => 'JsNotify.creator("#notify_4").notify("Hello world!")',
		'details' => '<div id="notify_4" role="alert" class="notify-box"></div>',
	],
];

// loop
$table = snippet('sample_table', 'samples');
foreach ($rows as $i => $row) {
	$table->row($i + 1, $row['code'], $row['details']);
}

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'system.thirdbar']);
$tpl->title('Notify');
$tpl->content = $table->render();

return $tpl->response();
