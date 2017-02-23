<?php

use hypeJunction\Notifications\Notification;

$notification = elgg_extract('item', $vars);
$full = elgg_extract('full_view', $vars, false);

if (!$notification instanceof Notification) {
	return;
}

$actor = $notification->getActor();
$object = $notification->getObject();

$icon = elgg_view_entity_icon($actor, 'tiny', [
	'use_hover' => false,
	'use_link' => false,
	'href' => false,
		]);

if (!$notification->isSeen()) {
	$notification->markAsSeen();
}

$class = ['notification-item'];
if (!$notification->isRead()) {
	$class[] = 'notification-new';
}

if ($full) {
	$content = elgg_view('output/longtext', [
		'value' => $notification->getBody(),
	]);
	if (!$notification->isRead()) {
		$notification->markAsRead();
	}
	$class[] = 'notification-full-listing';
} else {
	$content = elgg_view('output/longtext', [
		'value' => $notification->getSummary(),
	]);
	$class[] = 'notification-summary-listing';
}

echo elgg_view('object/elements/summary', [
	'entity' => $actor,
	'title' => false,
	'tags' => false,
	'content' => $content,
	'metadata' => elgg_view_menu('notification', $vars),
	'icon' => $icon,
	'image_block_vars' => [
		'class' => $class,
		'data-id' => $notification->id,
	],
]);
