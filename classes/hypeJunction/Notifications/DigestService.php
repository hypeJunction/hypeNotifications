<?php

namespace hypeJunction\Notifications;

use DateTime;
use Elgg\Notifications\InstantNotificationEvent;
use Elgg\Notifications\Notification;
use Elgg\Notifications\NotificationEvent;
use ElggData;
use ElggEntity;
use ElggObject;

/**
 * @access private
 */
class DigestService {

	const NEVER = 'never';
	const INSTANT = 'instant';
	const HOUR = 'hour';
	const SIX_HOURS = 'six_hour';
	const TWELVE_HOURS = 'twelve_hour';
	const DAY = 'day';

	/**
	 * @var self
	 */
	static $_instance;

	/**
	 * @var DigestTable
	 */
	private $table;

	/**
	 * Constructor 
	 * @param DigestTable $table DB table
	 */
	public function __construct(DigestTable $table) {
		$this->table = $table;
	}

	/**
	 * Returns a singleton
	 * @return self
	 */
	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self(new DigestTable());
		}
		return self::$_instance;
	}

	/**
	 * Returns DB table
	 * @return DigestTable
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * Get registered notification events
	 * @return array
	 */
	public static function getNotificationEvents() {
		$ignored_events = ['send', 'enqueue', 'admin_approval'];

		$subscriptions = _elgg_services()->notifications->getEvents();

		foreach ($subscriptions as $object_type => $object_subtypes) {
			foreach ($object_subtypes as $object_subtype => $events) {
				foreach ($events as $key => $event) {
					if (in_array($event, $ignored_events)) {
						unset($subscriptions[$object_type][$object_subtype][$key]);
					}
				}
			}
		}

		$notification_events['subscriptions'] = $subscriptions;

		// Add instant notifications that can be batched
		$notification_events['instant']['user']['default'][] = 'add_friend';
		if (elgg_is_active_plugin('friend_request')) {
			$notification_events['instant']['user']['default'][] = 'friend_request';
			$notification_events['instant']['user']['default'][] = 'friend_request_decline';
		}

		$notification_events['instant']['object']['comment'][] = 'create';

		if (elgg_is_active_plugin('likes')) {
			$notification_events['instant']['annotation']['likes'][] = 'create';
		}

		if (elgg_is_active_plugin('groups')) {
			$notification_events['instant']['group']['default'][] = 'add_membership';
			$notification_events['instant']['group']['default'][] = 'invite';
		}

		return elgg_trigger_plugin_hook('notification_events', 'notifications', null, $notification_events);
	}

	/**
	 * Respect user notification event preferences
	 *
	 * @param string $hook   "send"
	 * @param string $type   "all"
	 * @param array  $return Subscriptions
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function scheduleDigest($hook, $type, $return, $params) {

		list($prefix, $method) = explode(':', $type);

		if ($prefix !== 'notification') {
			return;
		}

		$event = elgg_extract('event', $params);
		if (!$event instanceof NotificationEvent) {
			return;
		}

		$notification = elgg_extract('notification', $params);
		if (!$notification instanceof Notification) {
			return;
		}

		$action = $event->getAction();
		$object = $event->getObject();
		$recipient_guid = $notification->getRecipientGUID();

		if ($object instanceof ElggEntity && !$object instanceof ElggObject) {
			$entity_type = $object->getType();
			$entity_subtype = 'default';
		} else if ($object instanceof ElggData) {
			$entity_type = $object->getType();
			$entity_subtype = $object->getSubtype();
		}

		$event_type = $event instanceof InstantNotificationEvent ? 'instant' : 'subscriptions';
		$setting_name = "$event_type:$action:$entity_type:$entity_subtype";
		$setting_value = elgg_get_plugin_user_setting($setting_name, $recipient_guid, 'hypeNotifications', self::INSTANT);
		
		if ($setting_value == self::NEVER) {
			// set notification as sent
			return true;
		}

		if ($method != 'email' || $setting_value == self::INSTANT || !$setting_value) {
			// let the handler deliver it
			return;
		}

		// Store in the database
		$time_schedule = self::getNextDeliveryTime($setting_value);

		$digest_notification = new DigestNotification();
		$digest_notification->setRecipient($notification->getRecipient());
		$digest_notification->setData((array) $notification->toObject());
		$digest_notification->setTimeScheduled($time_schedule);
		
		if ($digest_notification->save()) {
			return true;
		}
	}

	/**
	 * Send digests when cron runs
	 *
	 * @return void
	 */
	public static function sendDigest() {

		$dt = new DateTime();
		$time = $dt->getTimestamp();

		$svc = self::getInstance();
		$recipients = $svc->getTable()->getRecipients([
			'time_scheduled' => $time,
		]);
		
		if (empty($recipients)) {
			return;
		}
		
		foreach ($recipients as $recipient) {
			$notifications = $svc->getTable()->getAll([
				'recipient_guid' => $recipient,
				'time_scheduled' => $time,
			]);

			if (empty($notifications)) {
				return;
			}
			
			$subject = elgg_echo('notifications:digest:subject');
			$message = elgg_view('notifications/digest', [
				'notifications' => $notifications,
			]);

			$sent = notify_user($recipient, 0, $subject, $message, [], 'email');
			if ($sent) {
				foreach ($notifications as $notification) {
					$notification->delete();
				}
			}
		}
	}

	/**
	 * Get the time of the next digest delivery
	 * 
	 * @param string $interval Interval
	 * @return int
	 */
	public function getNextDeliveryTime($interval = null) {

		$now = new DateTime();
		$dt = new DateTime();

		switch ($interval) {
			case self::HOUR :
				$dt->modify('+1 hour');
				$h = $dt->format('H');
				$dt->setTime($h, 0, 0);
				break;

			case self::SIX_HOURS :
				foreach ([0, 6, 12, 18, 24] as $h) {
					$dt->setTime($h, 0, 0);
					if ($dt->getTimestamp() > $now->getTimestamp()) {
						break;
					}
				}
				break;

			case self::TWELVE_HOURS :
				foreach ([0, 12, 24] as $h) {
					$dt->setTime($h, 0, 0);
					if ($dt->getTimestamp() > $now->getTimestamp()) {
						break;
					}
				}
				break;

			case self::DAY :
				$dt->modify('+1 day');
				$dt->setTime(0, 0, 0);
				break;
		}

		return $dt->getTimestamp();
	}
}
