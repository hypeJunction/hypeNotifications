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

elgg_set_context('settings');

$title = elgg_echo('notifications:settings:digest');

elgg_push_breadcrumb(elgg_echo('settings'), "settings/user/$user->username");
elgg_push_breadcrumb($title);

elgg_set_page_owner_guid($user->guid);

$content = elgg_view_form('notifications/settings/digest', [], [
	'entity' => $user,
]);

$layout = elgg_view_layout('content', [
	'title' => $title,
	'content' => $content,
	'filter' => '',
		]);

echo elgg_view_page($title, $layout);
