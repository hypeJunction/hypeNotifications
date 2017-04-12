<?php

namespace hypeJunction\Notifications;

use ElggData;
use ElggEntity;
use ElggExtender;
use stdClass;

/**
 * @access private
 */
class SiteNotificationsTable {

	private $table;
	private $row_callback;

	/**
	 * Constructor
	 */
	public function __construct() {
		$dpbrefix = elgg_get_config('dbprefix');
		$this->table = "{$dpbrefix}site_notifications";
		$this->row_callback = [$this, 'rowToNotification'];
	}

	/**
	 * Convert DB row to an instance of Notification
	 * 
	 * @param stdClass $row DB row
	 * @return Notification
	 */
	public function rowToNotification(stdClass $row) {
		return new Notification($row);
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

		return get_data_row($query, $this->row_callback, $params) ?: false;
	}

	/**
	 * Get user notifications
	 *
	 * @param array $options Options
	 * @return Notification[]|false
	 */
	public function getAll(array $options = []) {

		$recipient_guid = elgg_extract('recipient_guid', $options);
		$limit = (int) elgg_extract('limit', $options, 25);
		$offset = (int) elgg_extract('offset', $options, 0);
		$status = elgg_extract('status', $options);

		$access_sql = _elgg_get_access_where_sql([
			'table_alias' => 'nt',
			'owner_guid_column' => 'access_owner_guid',
			'guid_column' => 'access_guid',
			'use_enabled_clause' => false,
		]);

		switch ($status) {
			default :
				$status_sql = '1=1';
				break;
			case 'read' :
				$status_sql = 'nt.time_read IS NOT NULL';
				break;
			case 'unread' :
				$status_sql = 'nt.time_read IS NULL or nt.time_read = 0';
				break;
			case 'seen' :
				$status_sql = 'nt.time_seen IS NOT NULL';
				break;
			case 'unseen' :
				$status_sql = 'nt.time_seen IS NULL or nt.time_seen = 0';
				break;
		}

		$query = "
			SELECT * FROM {$this->table} nt
			WHERE nt.recipient_guid = :recipient_guid
			AND $access_sql
			AND ($status_sql)
			ORDER BY nt.time_created DESC
			LIMIT $offset, $limit
		";

		$params = [':recipient_guid' => (int) $recipient_guid];

		return get_data($query, $this->row_callback, $params);
	}

	/**
	 * Count user notifications
	 *
	 * @param int    $recipient_guid GUID of the recipient
	 * @param string $status         read|seen|unread|unseen|all
	 * @return int
	 */
	public function count(array $options = []) {

		$recipient_guid = elgg_extract('recipient_guid', $options);
		$status = elgg_extract('status', $options);

		$access_sql = _elgg_get_access_where_sql([
			'table_alias' => 'nt',
			'owner_guid_column' => 'access_owner_guid',
			'guid_column' => 'access_guid',
			'use_enabled_clause' => false,
		]);

		switch ($status) {
			default :
				$status_sql = '1=1';
				break;
			case 'read' :
				$status_sql = 'nt.time_read IS NOT NULL';
				break;
			case 'unread' :
				$status_sql = 'nt.time_read IS NULL or nt.time_read = 0';
				break;
			case 'seen' :
				$status_sql = 'nt.time_seen IS NOT NULL';
				break;
			case 'unseen' :
				$status_sql = 'nt.time_seen IS NULL or nt.time_seen = 0';
				break;
		}

		$query = "
			SELECT COUNT(DISTINCT nt.id) as total
			FROM {$this->table} nt
			WHERE nt.recipient_guid = :recipient_guid
			AND ($status_sql)
			AND $access_sql
		";

		$params = [':recipient_guid' => (int) $recipient_guid];

		$row = get_data_row($query, null, $params);
		if ($row) {
			return (int) $row->total;
		}
		return 0;
	}

	/**
	 * Insert row
	 *
	 * @param Notification $notification Notification
	 * @return int|false
	 */
	public function insert(Notification $notification) {

		$query = "
			INSERT INTO {$this->table}
			SET recipient_guid = :recipient_guid,
				actor_guid = :actor_guid,
				object_id = :object_id,
				object_type = :object_type,
				object_subtype = :object_subtype,
				action = :action,
				time_created = :time_created,
				time_seen = :time_seen,
				time_read = :time_read,
				access_guid = :access_guid,
				access_owner_guid = :access_owner_guid,
				access_id = :access_id,
				data = :data
		";

		$params = [
			':recipient_guid' => (int) $notification->recipient_guid,
			':actor_guid' => (int) $notification->actor_guid,
			':object_id' => (int) $notification->object_id,
			':action' => (string) $notification->action,
			':object_type' => (string) $notification->object_type,
			':object_subtype' => (string) $notification->object_subtype,
			':time_created' => (int) $notification->time_created,
			':time_seen' => $notification->time_seen,
			':time_read' => $notification->time_read,
			':access_guid' => (int) $notification->access_guid,
			':access_owner_guid' => (int) $notification->access_owner_guid,
			':access_id' => (int) $notification->access_id,
			':data' => serialize($notification->data),
		];

		return insert_data($query, $params);
	}

	/**
	 * Update database row
	 *
	 * @param Notification $notification Notification
	 * @return bool
	 */
	public function update(Notification $notification) {

		$query = "
			UPDATE {$this->table}
			SET access_guid = :access_guid,
				access_owner_guid = :access_owner_guid,
				access_id = :access_id,
				time_seen = :time_seen,
				time_read = :time_read
			WHERE id = :id
		";

		$params = [
			':id' => (int) $notification->id,
			':time_seen' => $notification->time_seen,
			':time_read' => $notification->time_read,
			':access_guid' => (int) $notification->access_guid,
			':access_owner_guid' => (int) $notification->access_owner_guid,
			':access_id' => (int) $notification->access_id,
		];

		return update_data($query, $params);
	}

	/**
	 * Update database row
	 *
	 * @param ElggData $object Object
	 * @return bool
	 */
	public function updateAccess(ElggData $object) {

		$query = "
			UPDATE {$this->table}
			SET access_guid = :access_guid,
				access_owner_guid = :access_owner_guid,
				access_id = :access_id
			WHERE object_id = :object_id AND object_type = :type
		";

		if ($object instanceof ElggEntity) {
			$params = [
				':object_id' => (int) $object->guid,
				':type' => (string) $object->getType(),
				':access_guid' => (int) $object->guid,
				':access_owner_guid' => (int) $object->owner_guid,
				':access_id' => (int) $object->access_id,
			];
			return update_data($query, $params);
		} else if ($object instanceof ElggExtender) {
			$params = [
				':object_id' => (int) $object->id,
				':type' => (string) $object->getType(),
				':access_guid' => (int) $object->entity_guid,
				':access_owner_guid' => (int) $object->owner_guid,
				':access_id' => (int) $object->access_id,
			];
			return update_data($query, $params);
		} else {
			return true;
		}
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

	/**
	 * Delete rows by recipient, actor, object guids
	 *
	 * @param int $guid GUID
	 * @return bool
	 */
	public function deleteByEntityGUID($guid) {

		$query = "
			DELETE FROM {$this->table}
			WHERE (recipient_guid = :guid)
			OR (actor_guid = :guid)
			OR (object_id = :guid AND object_type IN ('object', 'user', 'site', 'group'))
		";

		$params = [
			':guid' => (int) $guid,
		];

		return delete_data($query, $params);
	}

	/**
	 * Delete rows by object id
	 *
	 * @param int    $id   Extender or relationship ID
	 * @param string $type Object type
	 * @return bool
	 */
	public function deleteByExtenderID($id, $type) {

		$query = "
			DELETE FROM {$this->table}
			WHERE object_id = :id
			AND object_type = :type
		";

		$params = [
			':id' => (int) $id,
			':type' => (string) $type,
		];

		return delete_data($query, $params);
	}

	/**
	 * Mark all notifications read
	 *
	 * @param int $recipient_guid Recipient GUID
	 * @return bool
	 */
	public function markAllRead($recipient_guid) {

		$query = "
			UPDATE {$this->table}
			SET time_seen = :time,
				time_read = :time
			WHERE recipient_guid = :recipient_guid
		";

		$params = [
			':time' => time(),
			':recipient_guid' => (int) $recipient_guid,
		];

		return update_data($query, $params);
	}

	/**
	 * Mark notifications about an entity as read
	 *
	 * @param int $guid           GUID
	 * @param int $recipient_guid Recipient GUID (defaults to logged in user)
	 * @return bool
	 */
	public function markReadByEntityGUID($guid, $recipient_guid = null) {

		$query = "
			UPDATE {$this->table}
			SET time_seen = :time,
				time_read = :time
			WHERE object_id = :guid
			AND object_type IN ('object', 'user', 'site', 'group')
			AND recipient_guid = :recipient_guid
		";

		if (!isset($recipient_guid)) {
			$recipient_guid = elgg_get_logged_in_user_guid();
		}
		
		$params = [
			':time' => time(),
			':guid' => (int) $guid,
			':recipient_guid' => (int) $recipient_guid,
		];

		return update_data($query, $params);
	}

	/**
	 * Mark notifications about an entity as read
	 *
	 * @param int    $id   Object id
	 * @param string $type Object type
	 * @param int $recipient_guid Recipient GUID (defaults to logged in user)
	 * @return bool
	 */
	public function markReadByExtenderID($id, $type, $recipient_guid = null) {

		$query = "
			UPDATE {$this->table}
			SET time_seen = :time,
				time_read = :time
			WHERE object_id = :guid
			AND object_type = :type
			AND recipient_guid = :recipient_guid
		";

		if (!isset($recipient_guid)) {
			$recipient_guid = elgg_get_logged_in_user_guid();
		}

		$params = [
			':time' => time(),
			':guid' => (int) $type,
			':recipient_guid' => (int) $recipient_guid,
		];

		return update_data($query, $params);
	}

}
