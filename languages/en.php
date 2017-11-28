<?php

return [
	'admin:notifications' => 'Notifications',
	
	'admin:notifications:methods' => 'Notification Methods',
	'admin:notifications:methods:help' => '
		Using the form below, you can update personal and subscription notification preferences for all site users.
		Note that this will override any preferences users may have selected previously, 
		and they will have to opt out of notifications again.
	',
	
	'admin:notifications:methods:personal' => 'Instant (personal) notifications',
	'admin:notifications:methods:friends' => 'Friend subscriptions',
	'admin:notifications:methods:groups' => 'Group subscriptions',

	'admin:notifications:test_email' => 'Test Email',
	'admin:notifications:test_email:recipient' => 'Recipient',
	'admin:notifications:test_email:subject' => 'Subject',
	'admin:notifications:test_email:body' => 'Body',
	'admin:notifications:test_email:attachments' => 'Attachment',
	'admin:notifications:test_email:success' => 'Email was sent successfully',
	'admin:notifications:test_email:error' => 'Email could not be sent',

	'notification:method:site' => 'Site',

	'notifications' => 'Notifications',
	'notification' => 'Notification',
	'notifications:all' => 'All notifications',
	
	'notifications:settings:digest' => 'Notifications digest',
	'notifications:settings:digest:help' => 'Here you can configure how often you receive notifications about certain events.',
	
	'notifications:frequency:never' => 'Never',
	'notifications:frequency:instantly' => 'Instantly',
	'notifications:frequency:hourly' => 'Hourly',
	'notifications:frequency:six_hours' => 'Every six hours',
	'notifications:frequency:twelve_hours' => 'Every twelve hours',
	'notifications:frequency:daily' => 'Daily',

	'notifications:check_online_status' => 'Do not send notifications when I am online',

	'notification:subscriptions:publish:object:blog' => 'New blog is published',
	'notification:subscriptions:create:object:bookmarks' => 'New bookmark is added',
	'notification:subscriptions:create:object:discussion' => 'New discussion topic is posted',
	'notification:subscriptions:create:object:discussion_reply' => 'New reply to a discussion topic is posted',
	'notification:subscriptions:create:object:file' => 'New file is uploaded',
	'notification:subscriptions:create:object:page' => 'New subpage is created',
	'notification:subscriptions:create:object:page_top' => 'New top-level page is created',
	'notification:subscriptions:create:object:thewire' => 'New wire post is made',
	'notification:subscriptions:publish:object:thewire' => 'New wire post is published',
	'notification:subscriptions:create:object:comment' => 'New comment is posted',
	'notification:subscriptions:publish:object:news' => 'News item is published',
	'notification:subscriptions:publish:object:hjwall' => 'New wall post is made',
	'notification:subscriptions:create:object:videolist_item' => 'New video is added',

	'notification:instant:add_friend:user:default' => 'Someone adds you as a friend',
	'notification:instant:friend_request:user:default' => 'Someone requests to add you as a friend',
	'notification:instant:friend_request_decline:user:default' => 'Someone declines your friend request',
	'notification:instant:create:annotation:likes' => 'Someone likes your content',
	'notification:instant:add_membership:group:default' => 'Your group membership request is approved',
	'notification:instant:invite:group:default' => 'Someone invites you to join a group',
	'notification:instant:create:object:comment' => 'Someone comments on your content',

	'notifications:subscriptions' => 'Activity notifications',
	'notifications:subscriptions:title' => 'Notifications about activity related to you and your content',
	'notifications:instant' => 'Personal notifications',
	'notifications:instant:title' => 'Notifications about your friends and groups',

	'notifications:settings:digest:success' => 'Notification settings have been saved successfully',

	'notifications:digest:subject' => 'Notifications digest',
	'notifications:digest:body_intro' => 'Here is the digest of your email notifications from %s',

	'notifications:settings:enable_html_emails' => 'Enable HTML emails',
	
	'notifications:settings:transport_settings' => 'Transport Settings',
	'notifications:settings:from_email' => 'From Email',
	'notifications:settings:from_email:help' => 'An email address to use for outgoing email notifications, if different from site email',
	'notifications:settings:from_name' => 'From Name',
	'notifications:settings:from_name:help' => 'Name to use for outgoing email notifications, if different from site name',
	'notifications:settings:transport' => 'Email Transport',
	'notifications:settings:transport:sendmail' => 'Sendmail',
	'notifications:settings:transport:file' => 'File Transport',
	'notifications:settings:transport:smtp' => 'SMTP',
	'notifications:settings:transport:help' => 'Select, which transport should be used to deliver outgoing emails. Use File Transport to disable outgoing emails and'
	. ' write them to filestore instead. Sendmail is the default transport. Configure your SMTP server details below, if choosing SMTP option',
	'notifications:settings:smtp_settings' => 'SMTP Settings',
	'notifications:settings:smtp_host_name' => 'SMTP Host Name',
	'notifications:settings:smtp_host_name:help' => 'Name of the SMTP host; defaults to "localhost".',
	'notifications:settings:smtp_host' => 'SMTP Host Address',
	'notifications:settings:smtp_host:help' => 'Remote hostname or IP address; defaults to "127.0.0.1".',
	'notifications:settings:smtp_port' => 'SMTP Port',
	'notifications:settings:smtp_port:help' => 'Port on which the remote host is listening; defaults to "25".',
	'notifications:settings:smtp_ssl' => 'SMTP Secure Connection',
	'notifications:settings:smtp_ssl:help' => 'For authentication types other than SMTP, you will typically need to define the "username" and "password" options. For secure connections you will use port 587 for TLS or port 465 for SSL.',
	'notifications:settings:smtp_connection' => 'SMTP Authentication Type',
	'notifications:settings:smtp_connection:help' => 'Authentication protocol to use',
	'notifications:settings:smtp_connection:smtp' => 'SMTP',
	'notifications:settings:smtp_connection:plain' => 'SMTP with AUTH PLAIN',
	'notifications:settings:smtp_connection:login' => 'SMTP with AUTH LOGIN',
	'notifications:settings:smtp_connection:crammd5' => 'SMTP with AUTH CRAM-MD5',
	'notifications:settings:smtp_username' => 'SMTP Username',
	'notifications:settings:smtp_password' => 'SMTP Password',
	
	'notifications:settings:mode' => 'Site mode',
	'notifications:settings:mode:help' => 'In staging mode, email/domain whitelist settings will apply. In production mode, emails will be sent as usual',
	'notifications:settings:mode:production' => 'Production mode',
	'notifications:settings:mode:staging' => 'Staging mode',

	'notifications:settings:staging_emails' => 'Allowed staging emails',
	'notifications:settings:staging_emails:help' => 'Enter one email per line. Only these emails (in addition to staging domains) will be receiving emails when the site is in the staging mode.',

	'notifications:settings:staging_domains' => 'Allowed staging domains',
	'notifications:settings:staging_domains:help' => 'Enter one domain per line. Only these domains (in addition to staging emails) will be receiving emails when the site is in the staging mode.',

	'notifications:settings:staging_catch_all' => 'Staging catch-all email address',
	'notifications:settings:staging_catch_all:help' => 'If provided, all system emails and notifications (to non-whitelisted addresses) will be forwarded to this email address. Leave empty to write emails to disk with the file transport.',

	'notifications:footer:link' => 'here',
	'notifications:footer' => 'This email has been sent by %s.<br />You can modify your notification preferences %s.<br />Please do not reply to this email.',

	'notifications:error:not_found' => 'Notification not found',
	'notifications:mark_all_read' => 'Mark all read',

	'notifications:no_results' => 'You do not have any notifications yet',

	'admin:upgrades:notifications:notifier' => 'Migrate notifier notifications',
	'admin:upgrades:notifications:notifier:description' => 'Migrate notification entities created by the notifier plugin to the new database table and clean up redundnant entities upon completion',

	'notifications:settings:ticker' => 'Ticker timeout',
	'notifications:settings:ticker:help' => '
		An interval in seconds at which the notifications counter should be updated.
		Set to 0 to disable automatic updates.
		You may need to flush your caches for the changes to take effect.
	',

];