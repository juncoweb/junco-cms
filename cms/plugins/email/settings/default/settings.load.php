<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
	$rows['transport']['options'] = [
		0		=> '--- ' . _t('Select') . ' ---',
		'mail'	=> 'mail',
		'smtp'	=> 'smtp'
	];

	$rows['charset']['options'] = [
		'iso-8859-1'	=> 'iso-8859-1',
		'utf-8'			=> 'utf-8'
	];

	$rows['header_encoding']['options'] = [
		'B'	=> 'Base64',
		'Q'	=> 'Quoted-printable'
	];

	$rows['message_encoding']['options'] = [
		'base64'			=> 'Base64',
		'quoted-printable'	=> 'Quoted-printable'
	];

	$rows['smtp_secure']['options'] = [
		0		=> '--- ' . _t('Select') . ' ---',
		'tls'	=> 'tls',
		'ssl'	=> 'ssl'
	];
};
