<?php

$count = $count = elgg_get_entities([
	'types' => 'object',
	'subtypes' => 'notification',
	'count' => true,
]);

echo elgg_view('output/longtext', [
	'value' => elgg_echo('admin:upgrades:notifications:notifier:description'),
]);

echo elgg_view('admin/upgrades/view', [
	'count' => $count,
	'action' => 'action/upgrade/notifications/notifier',
]);
