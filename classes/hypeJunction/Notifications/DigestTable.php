<?php

namespace hypeJunction\Notifications;

use stdClass;

/**
 * @access private
 */
class DigestTable {

	private $table;
	private $row_callback;

	/**
	 * Constructor
	 */
	public function __construct() {
		$dpbrefix = elgg_get_config('dbprefix');
		$this->table = "{$dpbrefix}digest";
		$this->row_callback = [$this, 'rowToNotification'];
	}

	/**
	 * Convert DB row to an instance of Notification
	 * 
	 * @param stdClass $row DB row
	 * @return Notification
	 */
	public function rowToNotification(stdClass $row) {
		return new DigestNotification($row);
	}

	/**
	 * Get notification by its ID
	 * 
	 * @param int $id ID
	 * @return Notification|false
	 */
	public function get($id) {
		$query = "
			SELECT * FROM {$this->table}
			WHERE id = :id
		";

		$params = [
			':id' => $id,
		];

		return get_data_row($query, $this->row_callback, $params) ? : false;
	}

	/**
	 * Get user notifications
	 *
	 * @param array $options Options
	 * @return Notification[]|false
	 */
	public function getAll(array $options = []) {

		$recipient_guid = elgg_extract('recipient_guid', $options);
		$time_scheduled = elgg_extract('time_scheduled', $options, time());
		
		$query = "
			SELECT * FROM {$this->table} nt
			WHERE nt.recipient_guid = :recipient_guid
			ORDER BY nt.time_created ASC
			LIMIT 0, 100
		";

		$params = [
			':recipient_guid' => (int) $recipient_guid,
			':time_scheduled' => (int) $time_scheduled,
		];

		return get_data($query, $this->row_callback, $params);
	}

	/**
	 * Get recipients who have pending digest notifications
	 *
	 * @param array $options Options
	 * @return array
	 */
	public function getRecipients(array $options = []) {

		$recipients = [];

		$time_scheduled = elgg_extract('time_scheduled', $options, time());
		$query = "
			SELECT recipient_guid FROM {$this->table}
			WHERE time_scheduled <= :time_scheduled
			GROUP BY recipient_guid
		";

		$params = [
			':time_scheduled' => (int) $time_scheduled,
		];

		$data = get_data($query, null, $params);
		foreach ($data as $row) {
			$recipients[] = $row->recipient_guid;
		}

		return $recipients;
	}

	/**
	 * Insert row
	 *
	 * @param DigestNotification $notification Notification
	 * @return int|false
	 */
	public function insert(DigestNotification $notification) {

		$query = "
			INSERT INTO {$this->table}
			SET recipient_guid = :recipient_guid,
				time_created = :time_created,
				time_scheduled = :time_scheduled,
				data = :data
		";

		$params = [
			':recipient_guid' => (int) $notification->recipient_guid,
			':time_created' => (int) $notification->time_created,
			':time_scheduled' => $notification->time_scheduled,
			':data' => serialize($notification->data),
		];

		return insert_data($query, $params);
	}

	/**
	 * Update database row
	 *
	 * @param DigestNotification $notification Notification
	 * @return bool
	 */
	public function update(DigestNotification $notification) {

		$query = "
			UPDATE {$this->table}
			SET time_scheduled = :time_scheduled,
			WHERE id = :id
		";

		$params = [
			':id' => (int) $notification->id,
			':time_scheduled' => $notification->time_scheduled,
		];

		return update_data($query, $params);
	}

	/**
	 * Delete row
	 *
	 * @param int $id ID
	 * @return bool
	 */
	public function delete($id) {

		$query = "
			DELETE FROM {$this->table}
			WHERE id = :id
		";

		$params = [
			':id' => (int) $id,
		];

		return delete_data($query, $params);
	}

}
