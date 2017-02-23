<?php

echo elgg_format_element('p', [
	'class' => 'elgg-text-help',
], elgg_echo('admin:notifications:notifiation_methods:help'));

$methods = elgg_get_notification_methods();
$options = [];
foreach ($methods as $method) {
	$label = elgg_echo("notification:method:$method");
	$options[$label] = $method;
}

echo elgg_view_field([
	'#type' => 'checkboxes',
	'#label' => elgg_echo('admin:notifiations:notifications_methods:personal'),
	'name' => 'personal',
	'default' => false,
	'options' => $options,
]);

echo elgg_view_field([
	'#type' => 'checkboxes',
	'#label' => elgg_echo('admin:notifiations:notifications_methods:friends'),
	'name' => 'friends',
	'default' => false,
	'options' => $options,
]);

echo elgg_view_field([
	'#type' => 'checkboxes',
	'#label' => elgg_echo('admin:notifiations:notifications_methods:groups'),
	'name' => 'groups',
	'default' => false,
	'options' => $options,
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);

elgg_set_form_footer($footer);