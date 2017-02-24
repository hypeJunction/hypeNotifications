<?php

if (get_input('upgrade_completed')) {
	$factory = new ElggUpgrade();
	$upgrade = $factory->getUpgradeFromPath('admin/upgrades/notifications/notifier');
	if ($upgrade instanceof ElggUpgrade) {
		$upgrade->setCompleted();
	}
	return true;
}

$original_time = microtime(true);
$time_limit = 4;

$success_count = 0;
$error_count = 0;

$response = [];

while (microtime(true) - $original_time < $time_limit) {

	$entities = elgg_get_entities([
		'types' => 'object',
		'subtypes' => 'notification',
		'batch' => true,
	]);

	$entities->setIncrementOffset(false);

	foreach ($entities as $entity) {
		$objects = $entity->getEntitiesFromRelationship(['relationship' => 'hasObject']);
		if ($objects) {
			$object = array_shift($objects);
		}

		$actors = $entity->getEntitiesFromRelationship(['relationship' => 'hasActor']);
		if (!$actors) {
			$entity->delete();
			$success_count++;
			continue;
		}

		$recipient = $entity->getOwnerEntity();
		if (!$recipient) {
			$entity->delete();
			$success_count++;
			continue;
		}

		foreach ($actors as $actor) {
			$notification = new hypeJunction\Notifications\Notification();
			$notification->setActor($actor);
			$notification->setRecipient($recipient);
			if ($object) {
				$notification->setObject($object);
			} else {
				list($action, $object_type, $object_subtype) = explode(':', $entity->event);
				$notification->action = $action;
				$notification->object_type = $object_type;
				$notification->object_subtype = $object_subtype;
			}

			$notification->time_created = $entity->time_created;
			
			if ($entity->status == 'read') {
				$notification->markAsSeen();
				$notification->markAsRead();
			}

			$notification->setData([
				'summary' => $entity->title,
			]);

			if ($notification->save()) {
				$entity->delete();
				$success_count++;
			} else {
				$error_count++;
			}
		}
	}
}

if (elgg_is_xhr()) {
	$response['numSuccess'] = $success_count;
	$response['numErrors'] = $error_count;
	echo json_encode($response);
}
