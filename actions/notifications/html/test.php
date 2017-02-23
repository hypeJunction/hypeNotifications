<?php

$user = elgg_get_logged_in_user_entity();
$site = elgg_get_site_entity();

$subject = "Test email from $site->name";
$body = elgg_view('plugins/hypeNotifications/kitchen-sink.html');

$file = new ElggFile();
$file->owner_guid = $user->guid;
$file->setFilename('tmp/notify.txt');
$file->open('write');
$file->write('Hello world!');
$file->close();

$result = notify_user($user->guid, $site->guid, $subject, $body, array(
	'attachments' => array($file)
), 'email');

$file->delete();

if ($result[$user->guid]['email']) {
	return elgg_ok_response('', elgg_echo('notifications:settings:send:success'));
} else {
	return elgg_error_response(elgg_echo('notifications:settings:send:error'));
}