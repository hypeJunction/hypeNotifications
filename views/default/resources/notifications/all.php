<?php

elgg_gatekeeper();

$username = elgg_extract('username', $vars);

if ($username) {
	$user = get_user_by_username($username);
} else {
	$user = elgg_get_logged_in_user_entity();
}

if (!$user || !$user->canEdit()) {
	forward('', '404');
}

elgg_register_menu_item('title', [
	'name' => 'mark_all_read',
	'text' => elgg_echo('notifications:mark_all_read'),
	'href' => 'action/notifications/mark_all_read?guid=' . $user->guid,
	'is_action' => true,
	'class' => 'elgg-button elgg-button-action',
]);

elgg_push_breadcrumb(elgg_echo('notifications'));

elgg_set_page_owner_guid($user->guid);

elgg_set_context('settings');

$title = elgg_echo('notifications');

$content = elgg_view('notifications/listing', [
	'entity' => $user,
		]);

$layout = elgg_view_layout('content', [
	'title' => elgg_echo('notifications'),
	'content' => $content,
	'filter' => '',
		]);

echo elgg_view_page($title, $layout);
