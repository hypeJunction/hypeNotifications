<?php

$entity = elgg_extract('entity', $vars);

$site = elgg_get_site_entity();

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('notifications:settings:ticker'),
	'#help' => elgg_echo('notifications:settings:ticker:help'),
	'name' => 'params[ticker]',
	'value' => isset($entity->ticker) ? $entity->ticker : 60,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('notifications:settings:mode'),
	'#help' => elgg_echo('notifications:settings:mode:help'),
	'name' => 'params[mode]',
	'value' => $entity->mode ? : 'production',
	'options_values' => [
		'production' => elgg_echo('notifications:settings:mode:production'),
		'staging' => elgg_echo('notifications:settings:mode:staging'),
	],
]);

echo elgg_view_field([
	'#type' => 'plaintext',
	'#label' => elgg_echo('notifications:settings:staging_emails'),
	'#help' => elgg_echo('notifications:settings:staging_emails:help'),
	'name' => 'params[staging_emails]',
	'value' => $entity->staging_emails,
]);

echo elgg_view_field([
	'#type' => 'plaintext',
	'#label' => elgg_echo('notifications:settings:staging_domains'),
	'#help' => elgg_echo('notifications:settings:staging_domains:help'),
	'name' => 'params[staging_domains]',
	'value' => $entity->staging_domains,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('notifications:settings:staging_catch_all'),
	'#help' => elgg_echo('notifications:settings:staging_catch_all:help'),
	'name' => 'params[staging_catch_all]',
	'value' => $entity->staging_catch_all,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('notifications:settings:enable_html_emails'),
	'name' => 'params[enable_html_emails]',
	'value' => $entity->enable_html_emails,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('notifications:settings:from_email'),
	'#help' => elgg_echo('notifications:settings:from_email:help'),
	'name' => 'params[from_email]',
	'value' => $entity->from_email,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('notifications:settings:from_name'),
	'#help' => elgg_echo('notifications:settings:from_name:help'),
	'name' => 'params[from_name]',
	'value' => $entity->from_name,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('notifications:settings:transport'),
	'#help' => elgg_echo('notifications:settings:transport:help'),
	'name' => 'params[transport]',
	'value' => $entity->transport,
	'class' => 'notifications-transport-selector',
	'options_values' => array_filter([
		'sendmail' => elgg_echo('notifications:settings:transport:sendmail'),
		'file' => elgg_echo('notifications:settings:transport:file'),
		'smtp' => elgg_echo('notifications:settings:transport:smtp'),
		'mailgun' => elgg_is_active_plugin('mailgun') ? 'Mailgun' : null,
	]),
]);


$smpt = '';

$smtp .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('notifications:settings:smtp_host'),
	'#help' => elgg_echo('notifications:settings:smtp_host:help'),
	'name' => 'params[smtp_host]',
	'value' => $entity->smtp_host,
]);

$smtp .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('notifications:settings:smtp_port'),
	'#help' => elgg_echo('notifications:settings:smtp_port:help'),
	'name' => 'params[smtp_port]',
	'value' => $entity->smtp_port,
]);

$smtp .= elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('notifications:settings:smtp_ssl'),
	'#help' => elgg_echo('notifications:settings:smtp_ssl:help'),
	'name' => 'params[smtp_ssl]',
	'value' => $entity->smtp_ssl,
	'options_values' => [
		'' => elgg_echo('option:no'),
		'ssl' => 'SSL',
		'tls' => 'TLS',
	],
]);

$smtp .= elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('notifications:settings:smtp_connection'),
	'#help' => elgg_echo('notifications:settings:smtp_connection:help'),
	'name' => 'params[smtp_connection]',
	'value' => $entity->smtp_connection,
	'options_values' => [
		'smtp' => elgg_echo('notifications:settings:smtp_connection:smtp'),
		'plain' => elgg_echo('notifications:settings:smtp_connection:plain'),
		'login' => elgg_echo('notifications:settings:smtp_connection:login'),
		'crammd5' => elgg_echo('notifications:settings:smtp_connection:crammd5'),
	],
]);

$smtp .= elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('notifications:settings:smtp_username'),
	'name' => 'params[smtp_username]',
	'value' => $entity->smtp_username,
	'autocomplete' => 'off',
]);

$smtp .= elgg_view_field([
	'#type' => 'password',
	'#label' => elgg_echo('notifications:settings:smtp_password'),
	'name' => 'params[smtp_password]',
	'value' => $entity->smtp_password,
	'autocomplete' => 'off',
]);

echo elgg_format_element('div', [
	'class' => [
		'notifications-smtp-settings',
		$entity->transport !== 'smtp' ? 'hidden' : '',
	],
], $smtp);

elgg_require_js('plugins/hypeNotifications/settings');