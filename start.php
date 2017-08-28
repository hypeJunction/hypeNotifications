<?php

/**
 * hypeNotifications
 *
 * Enhanced on-site and off-site notifications
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2017, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

use hypeJunction\Notifications\DigestService;
use hypeJunction\Notifications\EmailNotificationsService;
use hypeJunction\Notifications\Menus;
use hypeJunction\Notifications\Router;
use hypeJunction\Notifications\SiteNotificationsService;

elgg_register_event_handler('init', 'system', function() {

	// Router
	elgg_register_plugin_hook_handler('route', 'notifications', [Router::class, 'routeNotifications']);

	// Digests
	elgg_register_plugin_hook_handler('send', 'all', [DigestService::class, 'scheduleDigest'], 100);
	elgg_register_plugin_hook_handler('cron', 'hourly', [DigestService::class, 'sendDigest']);

	// Site notifications
	elgg_register_notification_method('site');
	elgg_register_plugin_hook_handler('send', 'notification:site', [SiteNotificationsService::class, 'sendNotification'], 400);
	elgg_register_event_handler('update', 'all', [SiteNotificationsService::class, 'entityUpdateHandler'], 999);
	elgg_register_event_handler('delete', 'all', [SiteNotificationsService::class, 'entityDeleteHandler'], 999);
	elgg_register_event_handler('create', 'user', [SiteNotificationsService::class, 'enableSiteNotificationsForNewUser']);
	elgg_register_event_handler('create', 'relationship', [SiteNotificationsService::class, 'enableSiteNotificationsForNewMembers']);

	elgg_register_plugin_hook_handler('view', 'profile/details', [SiteNotificationsService::class, 'dismissProfileNotifications']);
	elgg_register_plugin_hook_handler('view', 'groups/profile/layout', [SiteNotificationsService::class, 'dismissProfileNotifications']);
	elgg_register_plugin_hook_handler('view', 'object/default', [SiteNotificationsService::class, 'dismissObjectNotifications']);
	$subtypes = (array) get_registered_entity_types('object');
	foreach ($subtypes as $subtype) {
		elgg_register_plugin_hook_handler('view', "object/$subtype", [SiteNotificationsService::class, 'dismissObjectNotifications']);
	}
	elgg_register_plugin_hook_handler('elgg.data', 'site', [SiteNotificationsService::class, 'setClientSiteConfig']);

	// Email notifications and transport
	elgg_register_plugin_hook_handler('send', 'notification:email', [EmailNotificationsService::class, 'sendNotification'], 400);
	elgg_register_plugin_hook_handler('email', 'system', [EmailNotificationsService::class, 'sendSystemEmail'], 100);
	elgg_register_plugin_hook_handler('format', 'notification:email', [EmailNotificationsService::class, 'formatNotification'], 999);

	// Mailgun overrides
	if (elgg_is_active_plugin('mailgun')) {
		elgg_unregister_plugin_hook_handler('email', 'system', 'mailgun_email_handler');
		elgg_unregister_plugin_hook_handler('send', 'notification:email', 'mailgun_send_email_notification');
	}

	// Actions
	elgg_register_action('admin/notifications/methods', __DIR__ . '/actions/admin/notifications/methods.php', 'admin');
	elgg_register_action('admin/notifications/test_email', __DIR__ . '/actions/admin/notifications/test_email.php', 'admin');
	elgg_register_action('notifications/mark_all_read', __DIR__ . '/actions/notifications/mark_all_read.php');
	elgg_register_action('notifications/mark_read', __DIR__ . '/actions/notifications/mark_read.php');
	elgg_register_action('notifications/settings/digest', __DIR__ . '/actions/notifications/settings/digest.php');
	elgg_register_action('upgrade/notifications/notifier', __DIR__ . '/actions/upgrade/notifications/notifier.php', 'admin');

	// Menus
	elgg_register_plugin_hook_handler('register', 'menu:topbar', [Menus::class, 'setupTopbarMenu']);
	elgg_register_plugin_hook_handler('register', 'menu:page', [Menus::class, 'setupPageMenu']);

	// Views
	elgg_extend_view('page/elements/topbar', 'notifications/popup');

	elgg_extend_view('elgg.css', 'notifications.css'); // core notifications
	elgg_extend_view('elgg.css', 'notifications/notifications.css');
	elgg_extend_view('admin.css', 'notifications/notifications.css');

});

elgg_register_event_handler('upgrade', 'system', function() {
	if (!elgg_is_admin_logged_in()) {
		return;
	}
	require __DIR__ . '/lib/upgrades.php';
});