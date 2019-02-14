<?php

namespace hypeJunction\Notifications;

class Router {

	public static function routeNotifications($hook, $type, $return, $params) {

		$segments = (array) elgg_extract('segments', $return);

		$page = array_shift($segments);

		switch ($page) {

			case 'all' :
				echo elgg_view_resource('notifications/all', [
					'username' => array_shift($segments),
				]);
				return false;

			case 'settings' :
				$setting = array_shift($segments);
				if (elgg_view_exists("resources/notifications/settings/$setting")) {
				echo elgg_view_resource("notifications/settings/$setting", [
					'username' => array_shift($segments),
				]);
				}
				return false;

			case 'view' :
				echo elgg_view_resource('notifications/view', [
					'id' => array_shift($segments),
				]);
				return false;

			case 'ticker' :
				echo elgg_view_resource('notifications/ticker');
				return false;
		}

	}
}
