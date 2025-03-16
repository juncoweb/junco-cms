<?php

/**
 * Email
 */

return [
	/**
	 * General
	 */
	'transport' => 'mail',
	'no_reply' => 'no-reply@example.com',
	'x_mailer' => 'JuncoCMS',
	'snippet_color' => '#009eff',

	/**
	 * Encoding
	 */
	'charset' => 'utf-8',
	'header_encoding' => 'Q',
	'message_encoding' => 'quoted-printable',

	/**
	 * Smtp
	 */
	'smtp_secure' => '0',
	'smtp_host' => '',
	'smtp_port' => 0,
	'smtp_timeout' => 0,
	'smtp_auth' => '',
	'smtp_user' => '',
	'smtp_pwd' => '',
];
