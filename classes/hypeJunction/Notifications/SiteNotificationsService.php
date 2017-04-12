<?php

namespace hypeJunction\Notifications;

use Elgg\Notifications\Notification as NotificationInstance;
use Elgg\Notifications\NotificationEvent;
use ElggData;
use ElggEntity;
use ElggExtender;
use ElggRelationship;

/**
 * @access private
 */
class SiteNotificationsService {

	/**
	 * @var self
	 */
	static $_instance;

	/**
	 * @var SiteNotificationsTable
	 */
	private $table;

	/**
	 * Constructor 
	 * @param SiteNotificationsTable $table DB table
	 */
	public function __construct(SiteNotificationsTable $table) {
		$this->table = $table;
	}

	/**
	 * Returns a singleton
	 * @return self
	 */
	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self(new SiteNotificationsTable());
		}
		return self::$_instance;
	}

	/**
	 * Returns DB table
	 * @return SiteNotificationsTable
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * Deliver site notification
	 *
	 * @param string $hook   "send"
	 * @param string $type   "notification:site"
	 * @param bool   $return Delivery status
	 * @param array  $params Hook params
	 * @return bool
	 */
	public static function sendNotification($hook, $type, $return, $params) {

		if ($return === true) {
			return;
		}

		$notification = elgg_extract('notification', $params);
		/* @var $notification NotificationInstance */

		$event = elgg_extract('event', $params);
		/* @var $event NotificationEvent */

		$site_notification = new Notification();
		$site_notification->setRecipient($notification->getRecipient());
		if ($event instanceof NotificationEvent) {
			$site_notification->setAction($event->getAction());
			$site_notification->setActor($event->getActor() ?: $notification->getSender());
			$site_notification->setObject($event->getObject() ?: null);
		} else {
			$site_notification->setActor($notification->getSender());
		}
		$site_notification->setData((array) $notification->toObject());
		if ($site_notification->save()) {
			return true;
		}
	}

	/**
	 * Remove rows from notification table when actor, recipient or object is deleted
	 * 
	 * @param string $event  "delete"
	 * @param string $type   "all"
	 * @param mixed  $object Deleted object
	 * @return void
	 */
	public static function entityDeleteHandler($event, $type, $object) {

		$svc = self::getInstance();
		if ($object instanceof ElggEntity) {
			$svc->getTable()->deleteByEntityGUID($object->guid);
		} else if ($object instanceof ElggExtender || $object instanceof ElggRelationship) {
			$svc->getTable()->deleteByExtenderID($object->id, $object->getType());
		}
	}

	/**
	 * Update access levels
	 *
	 * @param string $event  "update"
	 * @param string $type   "all"
	 * @param mixed  $object Updated object
	 * @return void
	 */
	public static function entityUpdateHandler($event, $type, $object) {
		$svc = self::getInstance();
		if ($object instanceof ElggEntity) {
			$attributes = $object->getOriginalAttributes();
			if (array_key_exists('access_id', $attributes)) {
				$svc->getTable()->updateAccess($object);
			}
		} else if ($object instanceof ElggData) {
			$svc->getTable()->updateAccess($object);
		}
	}

	/**
	 * Dismiss user/group notifications when their profile is viewed
	 *
	 * @param string $hook      "view"
	 * @param string $view_name View name
	 * @param array  $return    Content
	 * @param array  $params    Hook params
	 * @return void
	 */
	public static function dismissProfileNotifications($hook, $view_name, $return, $params) {

		if (empty($return)) {
			return;
		}

		if (elgg_in_context('action')) {
			return;
		}

		$vars = elgg_extract('vars', $params);

		$entity = elgg_extract('entity', $vars);
		if (!$entity) {
			$entity = elgg_get_page_owner_entity();
		}

		if (!$entity instanceof ElggEntity) {
			return;
		}

		$svc = self::getInstance();
		$svc->getTable()->markReadByEntityGUID($entity->guid);
	}

	/**
	 * Log an object listing view
	 *
	 * @param string $hook      "view"
	 * @param string $view_name View name
	 * @param array  $return    Content
	 * @param array  $params    Hook params
	 * @return void
	 */
	public static function dismissObjectNotifications($hook, $view_name, $return, $params) {

		if (empty($return)) {
			return;
		}

		$vars = elgg_extract('vars', $params);

		$entity = elgg_extract('entity', $vars);
		if (!$entity instanceof ElggEntity) {
			return;
		}

		$full_view = elgg_extract('full_view', $vars, false);
		if (!$full_view) {
			return;
		}

		$svc = self::getInstance();
		$svc->getTable()->markReadByEntityGUID($entity->guid);
	}

	/**
	 * Enable site notifications for new users
	 *
	 * @param string   $event "create"
	 * @param string   $type  "user"
	 * @param ElggUser $user  User
	 * @return void
	 */
	public static function enableSiteNotificationsForNewUser($event, $type, $user) {

		$user->setNotificationSetting('site', true);

		$metaname = 'collections_notifications_preferences_site';
		$user->$metaname = -1; // enable for new friends
	}

	/**
	 * Enable site notifications for new group members
	 *
	 * @param string           $event        "create"
	 * @param string           $type         "relationship"
	 * @param ElggRelationship $relationship Relationship
	 * @return void
	 */
	public static function enableSiteNotificationsForNewMembers($event, $type, $relationship) {

		if ($relationship->relationship != 'member') {
			return;
		}

		elgg_add_subscription($relationship->guid_one, 'notifysite', $relationship->guid_two);
	}

	/**
	 * Set client-side data
	 * 
	 * @param string $hook   "elgg.data"
	 * @param string $type   "site"
	 * @param array  $return Data
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function setClientSiteConfig($hook, $type, $return, $params) {
		$return['notifications']['ticker'] = (int) elgg_get_plugin_setting('ticker', 'hypeNotifications', 60);
		return $return;
	}
}
