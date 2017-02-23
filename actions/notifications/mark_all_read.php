<?php

$guid = get_input('guid');
$user = get_entity($guid);

if (!$user || !$user->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

hypeapps_mark_all_notifications_read($user->guid);
