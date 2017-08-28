<?php

namespace hypeJunction\Notifications;

use ElggMenuItem;
use ElggUser;

/**
 * @access private
 */
class Menus {

	/**
	 * Setup topbar menu
	 *
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:topbar"
	 * @param ElggMenuItem[] $return  Menu
	 * @param array          $params  Hook params
	 * @return ElggMenuItem[]
	 */
	public static function setupTopbarMenu($hook, $type, $return, $params) {

		$user = elgg_get_logged_in_user_entity();
		if (!$user) {
			return;
		}

		$count = hypeapps_count_notifications([
			'status' => 'unseen',
			'recipient_guid' => $user->guid,
		]);

		if ($count > 99) {
			$count = '99+';
		}

		if (elgg_is_active_plugin('hypeUI')) {
			$return[] = ElggMenuItem::factory([
				'name' => 'notifications',
				'href' => 'notifications/all#notifications-popup',
				'text' => elgg_echo('notifications'),
				'badge' => $count ? : '',
				'priority' => 600,
				'tooltip' => elgg_echo('notifications:thread:unread', [$count]),
				'rel' => 'popup',
				'id' => 'notifications-popup-link',
				'link_class' => 'has-hidden-label',
				'icon' => 'bell',
				'data-position' => json_encode([
					'my' => 'center top',
					'of' => 'center bottom',
					'of' => '.elgg-menu-topbar > .elgg-menu-item-notifications',
					'collission' => 'fit fit',
				]),
			]);
		} else {

			if (elgg_is_active_plugin('menus_api')) {
				$text = elgg_echo('notifications');
			} else {
				$text = elgg_view_icon('bell');
			}

			$counter = elgg_format_element('span', [
				'id' => 'notifications-new',
				'class' => $count ? 'notifications-unread-count messages-new' : 'notifications-unread-count messages-new hidden',
			], $count);

			$return[] = ElggMenuItem::factory([
				'name' => 'notifications',
				'href' => 'notifications/all#notifications-popup',
				'text' => $text . $counter,
				'priority' => 600,
				'tooltip' => elgg_echo('notifications:thread:unread', [$count]),
				'rel' => 'popup',
				'id' => 'notifications-popup-link',
				'data' => [
					'icon' => 'bell',
				],
				'data-position' => json_encode([
					'my' => 'left top',
					'of' => 'left bottom',
					'of' => '#notifications-popup-link',
					'collission' => 'fit fit',
				]),
			]);
		}

		return $return;
	}

	/**
	 * Setup page menu
	 *
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:page"
	 * @param ElggMenuItem[] $return  Menu
	 * @param array          $params  Hook params
	 * @return ElggMenuItem[]
	 */
	public static function setupPageMenu($hook, $type, $return, $params) {

		if (elgg_in_context('settings')) {
			$page_owner = elgg_get_page_owner_entity();
			if ($page_owner instanceof ElggUser) {
				$return[] = ElggMenuItem::factory([
							'name' => 'notifications:digest',
							'text' => elgg_echo('notifications:settings:digest'),
							'href' => "notifications/settings/digest/$page_owner->username",
							'section' => 'notifications',
				]);
			}
		}

		$return[] = ElggMenuItem::factory([
					'name' => 'notifications',
					'text' => elgg_echo('admin:notifications'),
					'href' => '#',
					'section' => 'configure',
					'context' => ['admin'],
		]);

		$return[] = ElggMenuItem::factory([
			'name' => 'notifications:settings',
			'text' => elgg_echo('settings'),
			'href' => 'admin/plugin_settings/hypeNotifications',
			'section' => 'configure',
			'parent_name' => 'notifications',
			'context' => ['admin'],
		]);

		$return[] = ElggMenuItem::factory([
					'name' => 'notifications:methods',
					'text' => elgg_echo('admin:notifications:methods'),
					'href' => 'admin/notifications/methods',
					'section' => 'configure',
					'parent_name' => 'notifications',
					'context' => ['admin'],
		]);

		$return[] = ElggMenuItem::factory([
					'name' => 'notifications:test_email',
					'text' => elgg_echo('admin:notifications:test_email'),
					'href' => 'admin/notifications/test_email',
					'section' => 'configure',
					'parent_name' => 'notifications',
					'context' => ['admin'],
		]);

		return $return;
	}

}
