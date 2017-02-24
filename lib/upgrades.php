<?php

// Register upgrade scripts
$path = 'admin/upgrades/notifications/notifier';
$upgrade = new ElggUpgrade();
$upgrade = $upgrade->getUpgradeFromPath($path);

if ($upgrade instanceof ElggUpgrade) {
	// Upgrade already exists
	return;
}

$count = elgg_get_entities([
	'types' => 'object',
	'subtypes' => 'notification',
	'count' => true,
]);

if ($count) {
	$upgrade = new ElggUpgrade();
	$upgrade->setPath($path);
	$upgrade->title = elgg_echo('admin:upgrades:notifications:notifier');
	$upgrade->description = elgg_echo('admin:upgrades:notifications:notifier:description');
	$upgrade->is_completed = 0;
	$upgrade->save();
}
