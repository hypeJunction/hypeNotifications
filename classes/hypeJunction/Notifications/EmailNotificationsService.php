<?php

namespace hypeJunction\Notifications;

use Elgg\Mail\Address as ElggEmail;
use Elgg\Notifications\Notification as ElggNotification;
use Elgg\Notifications\NotificationEvent;
use ElggEntity;
use ElggFile;
use ElggUser;
use Exception;
use Mailgun\Api\Message;
use NotificationException;
use Zend\Mail\Address;
use Zend\Mail\Message as EmailMessage;
use Zend\Mail\Transport\File;
use Zend\Mail\Transport\FileOptions;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;

class EmailNotificationsService {

	/**
	 * Send an email notification
	 *
	 * @param string $hook   "send"
	 * @param string $type   "notification:email"
	 * @param bool   $result Was the email already sent
	 * @param array  $params Hook parameters
	 *
	 * @return bool
	 */
	public static function sendNotification($hook, $type, $result, $params) {

		if ($result === true) {
			// email was already sent by some other handler
			return;
		}

		$notification = elgg_extract('notification', $params);
		if (!$notification instanceof ElggNotification) {
			return false;
		}

		$sender = $notification->getSender();
		$recipient = $notification->getRecipient();

		if (!$sender instanceof ElggEntity) {
			return false;
		}

		if (!$recipient instanceof ElggEntity || !$recipient->email) {
			return false;
		}

		$to = new Address($recipient->email, $recipient->getDisplayName());

		$site = elgg_get_site_entity();

		if (elgg_is_active_plugin('mailgun')) {

			$domain = elgg_get_plugin_setting('domain', 'mailgun');
			if (!$domain) {
				$domain = $site->getDomain();
			}

			$project = elgg_get_plugin_setting('project', 'mailgun');
			if ($project) {
				$from_email = "$project@$domain";
			} else {
				$from_email = "noreply@$domain";
			}

			$from_name = $sender->getDisplayName(); // But set the name to that of the sender

			$from = new Address($from_email, $from_name);

			// was the token provided with $params through notify_user()
			$token = elgg_extract('token', $params);

			if (!$token) {
				// check if token has been added to notification by "prepare", "notification:*" hook
				$token = $notification->token;
			}

			if (!$token) {
				$notification_params = $notification->params;
				$token = elgg_extract('token', $notification->params);
			}

			if (!$token) {
				// check if we have a notification event with an object
				$event = elgg_extract('event', $params);
				if ($event instanceof NotificationEvent) {
					$object = $event->getObject();
					if ($object instanceof ElggEntity) {
						$token = mailgun_get_entity_notification_token($object, $event->getDescription());
					}
				}
			}

			if (!$token && $sender instanceof ElggUser) {
				$options['token'] = mailgun_get_entity_notification_token($sender);
				$token = \ArckInteractive\Mailgun\Message::addToken($from->getEmail(), $options['token']);
				$from_email = mailgun_build_rfc822_address($token['email'], $sender->getDisplayName());
				$from = ElggEmail::fromString($from_email);
			}

			$email_params['token'] = $token;
		} else {
			$from_email = elgg_get_plugin_setting('from_email', 'hypeNotifications', $site->email);
			if (!$from_email) {
				$from_email = "noreply@{$site->getDomain()}";
			}
			$from_name = elgg_get_plugin_setting('from_name', 'hypeNotifications', $site->name);
			$from = new Address($from_email, $from_name);
		}

		$email_params = array_merge((array) $notification->params, $params);
		$email_params['notification'] = $notification;

		return self::deliverEmail($from->toString(), $to->toString(), $notification->subject, $notification->body, $email_params);
	}

	/**
	 * Format email notification
	 *
	 * @param string           $hook         "format"
	 * @param string           $type         "notification"
	 * @param ElggNotification $notification Notification
	 * @param array            $params       Hook parameters
	 *
	 * @return ElggNotification
	 */
	public static function formatNotification($hook, $type, $notification, $params) {

		if (!$notification instanceof ElggNotification) {
			return;
		}

		if (!elgg_get_plugin_setting('enable_html_emails', 'hypeNotifications')) {
			return;
		}

		$body = elgg_view('notifications/wrapper/html/post', [
			'notification' => $notification,
		]);

		if ($body) {
			$notification->body = $body;
		}

		return $notification;
	}

	/**
	 * Send emails initiated by elgg_send_email() wrapped as HTML
	 *
	 * @param string $hook   "email"
	 * @param string $type   "system"
	 * @param mixed  $return Email params or bool
	 * @param array  $params Hook params
	 *
	 * @return mixed
	 */
	public static function sendSystemEmail($hook, $type, $return, $params) {

		if (!is_array($return)) {
			// another hook has already sent the email
			return;
		}

		$email_params = elgg_extract('params', $return);
		$notification = elgg_extract('notification', $email_params);

		if ($notification instanceof ElggNotification) {
			return;
		}

		$to = ElggEmail::fromString($return['to']);
		$from = ElggEmail::fromString($return['from']);

		$recipients = get_user_by_email($to->getEmail());
		$senders = get_user_by_email($from->getEmail());

		if ($recipients) {
			$recipient = $recipients[0];
		} else {
			$recipient = new ElggUser();
			$recipient->email = $to->getEmail();
			$recipient->name = $to->getName();
		}

		$site = elgg_get_site_entity();
		if ($senders) {
			$sender = $senders[0];
		} else if ($from->getEmail() == $site->email) {
			$sender = $site;
		} else {
			$sender = new ElggUser();
			$sender->email = $from->getEmail();
			$sender->name = $from->getName();
		}

		$event = null;
		if (isset($email_params['object']) && isset($email_params['action'])) {
			$event = new SystemEmailEvent($email_params['object'], $email_params['action'], $sender);
		}
		$email_params['event'] = $event;

		$language = $recipient->language ? : 'en';
		$summary = $email_params['summary'] ? : '';

		$notification = new ElggNotification($sender, $recipient, $language, $return['subject'], $return['body'], $summary, $email_params);

		$notification = elgg_trigger_plugin_hook('format', "notification:email", [], $notification);
		$email_params['notification'] = $notification;

		return elgg_trigger_plugin_hook('send', "notification:email", $email_params, false);
	}

	/**
	 * Send an email to any email address
	 *
	 * @param mixed  $from    Email address or string: "name <email>"
	 * @param mixed  $to      Email address or string: "name <email>"
	 * @param string $subject The subject of the message
	 * @param string $body    The message body
	 * @param array  $params  Optional parameters
	 *
	 * @return bool
	 * @throws NotificationException
	 */
	public static function deliverEmail($from, $to, $subject, $body, array $params = null) {

		$options = [
			'to' => $to,
			'from' => $from,
			'subject' => $subject,
			'body' => $body,
			'params' => $params,
			'headers' => [],
		];

		$transport_name = elgg_get_plugin_setting('transport', 'hypeNotifications', 'sendmail');

		if (elgg_get_plugin_setting('mode', 'hypeNotifications') == 'staging') {
			$to_address = ElggEmail::fromString($options['to']);
			if (!EmailWhitelist::isWhitelisted($to_address->getEmail())) {
				$catch_all = elgg_get_plugin_setting('staging_catch_all', 'hypeNotifications');
				if ($catch_all) {
					$options['to'] = $catch_all;
				} else {
					$transport_name = 'file';
				}
			}
		}

		$options = elgg_trigger_plugin_hook('email', 'system', $options, $options);
		if (!is_array($options)) {
			return (bool) $options;
		}

		if ($transport_name == 'mailgun') {
			if (elgg_is_active_plugin('mailgun')) {

				try {
					if (!empty($options['token']) && !preg_match('/\+(\S+)@.*/', $options['from'])) {
						// Add a token to the email
						$from_address = ElggEmail::fromString($options['from']);
						$token = \ArckInteractive\Mailgun\Message::addToken($from_address->getEmail(), $options['token']);
						$options['from'] = mailgun_build_rfc822_address($token['email'], $from_address->getName());
					} else if (empty($options['token'])) {
						$from_address = ElggEmail::fromString($options['from']);
						$users = get_user_by_email($from_address->getEmail());
						if ($users) {
							$user = array_shift($users);
							$options['token'] = mailgun_get_entity_notification_token($user);
							$token = \ArckInteractive\Mailgun\Message::addToken($from_address->getEmail(), $options['token']);
							$options['from'] = mailgun_build_rfc822_address($token['email'], $from_address->getName());
							$options['token'] = $token;
						}
					}
				} catch (\Exception $ex) {

				}

				$options['template'] = false;
				if (elgg_get_plugin_setting('enable_html_emails', 'hypeNotifications')) {
					$options['html'] = elgg_view('notifications/wrapper/html', $options);
				} else {
					$options['text'] = elgg_view('notifications/wrapper/plaintext', $options);
				}

				$files = elgg_extract('attachments', $options['params']);
				unset($options['params']['attachments']);

				if (!empty($files) && is_array($files)) {
					foreach ($files as $file) {
						if ($file instanceof ElggFile) {
							$options['attachments'][] = [
								'filename' => $file->originalfilename ? : basename($file->getFilename()),
								'filePath' => $file->getFilenameOnFilestore(),
							];
						}
					}
				}

				return mailgun_send_email($options);
			} else {
				$transport_name = 'sendmail';
			}
		}

		try {
			if (empty($options['from'])) {
				$msg = "Missing a required parameter, '" . 'from' . "'";
				throw new NotificationException($msg);
			}

			if (empty($options['to'])) {
				$msg = "Missing a required parameter, '" . 'to' . "'";
				throw new NotificationException($msg);
			}

			$options['to'] = ElggEmail::fromString($options['to']);
			$options['from'] = ElggEmail::fromString($options['from']);

			$options['subject'] = elgg_strip_tags($options['subject']);
			$options['subject'] = html_entity_decode($options['subject'], ENT_QUOTES, 'UTF-8');
			// Sanitise subject by stripping line endings
			$options['subject'] = preg_replace("/(\r\n|\r|\n)/", " ", $options['subject']);
			$options['subject'] = elgg_get_excerpt(trim($options['subject'], 80));

			$message = new EmailMessage();

			$message->setEncoding('UTF-8');
			$message->addFrom($options['from']);
			$message->addTo($options['to']);
			$message->setSubject($options['subject']);

			// make the email body
			$mime_body = new MimeMessage();

			if (elgg_get_plugin_setting('enable_html_emails', 'hypeNotifications')) {
				$html_body = elgg_view('notifications/wrapper/html', $options);
				$html_part = new MimePart($html_body);
				$html_part->setCharset('UTF-8');
				$html_part->setType(Mime::TYPE_HTML);
				$mime_body->addPart($html_part);
			} else {
				$plaintext_body = elgg_view('notifications/wrapper/plaintext', $options);
				$plaintext_part = new MimePart($plaintext_body);
				$plaintext_part->setCharset('UTF-8');
				$plaintext_part->setType(Mime::TYPE_TEXT);
				$mime_body->addPart($plaintext_part);
			}

			$files = elgg_extract('attachments', $options['params']);
			if (!empty($files) && is_array($files)) {
				foreach ($files as $file) {
					if ($file instanceof ElggFile) {
						$attachment = new MimePart(fopen($file->getFilenameOnFilestore(), 'r'));
						$attachment->type = $file->getMimeType() ? : $file->detectMimeType();
						$attachment->filename = $file->originalfilename ? : basename($file->getFilename());
						$attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
						$attachment->encoding = Mime::ENCODING_BASE64;
						$mime_body->addPart($attachment);
					}
				}
			}

			$message->setBody($mime_body);

			foreach ($options['headers'] as $headerName => $headerValue) {
				$message->getHeaders()->addHeaderLine($headerName, $headerValue);
			}

			$transport = self::getTransport($transport_name);
			if (!$transport instanceof TransportInterface) {
				throw new NotificationException("Invalid Email transport");
			}

			$transport->send($message);
		} catch (Exception $e) {
			elgg_log($e->getMessage(), 'ERROR');

			return false;
		}

		return true;
	}

	/**
	 * Returns email transport
	 *
	 * @param string $name Transport type
	 *
	 * @return TransportInterface
	 */
	public static function getTransport($name = null) {
		switch ($name) {
			default :
				$transport = new Sendmail();
				break;

			case 'file' :
				$dirname = elgg_get_config('dataroot') . 'notifications_log/zend/';
				if (!is_dir($dirname)) {
					mkdir($dirname, 0700, true);
				}
				$options = [
					'path' => $dirname,
					'callback' => function () {
						return 'Message_' . microtime(true) . '_' . mt_rand() . '.txt';
					},
				];
				$transport = new File(new FileOptions($options));
				break;

			case 'smtp' :
				$options = array_filter([
					'name' => elgg_get_plugin_setting('smtp_host_name', 'hypeNotifications'),
					'host' => elgg_get_plugin_setting('smtp_host', 'hypeNotifications'),
					'port' => elgg_get_plugin_setting('smtp_port', 'hypeNotifications'),
					'connection_class' => elgg_get_plugin_setting('smtp_connection', 'hypeNotifications'),
					'connection_config' => array_filter([
						'username' => elgg_get_plugin_setting('smtp_username', 'hypeNotifications'),
						'password' => elgg_get_plugin_setting('smtp_password', 'hypeNotifications'),
						'ssl' => elgg_get_plugin_setting('smtp_ssl', 'hypeNotifications'),
					]),
				]);
				$transport = new Smtp(new SmtpOptions($options));
				break;
		}

		return elgg_trigger_plugin_hook('email:transport', 'system', null, $transport);
	}

}
