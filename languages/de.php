<?php

return [
	'admin:notifications' => 'Benachrichtigungen',

	'admin:notifications:methods' => 'Benachrichtigungsarten',
	'admin:notifications:methods:help' => '
		Mit dem Formular kannst Du die persönlichen und Abonnement-bezogenen Benachrichtigungseinstellungen für alle Benutzer ändern.
		Beachte bitte, dass dies die eventuell vorhandenen Einstellungen aller Benutzer überschreiben wird,
		und diese, falls gewünscht, die Benachrichtigungen erneut abschalten müssen.
	',

	'admin:notifications:methods:personal' => 'Sofortige (persönliche) Benachrichtigungen',
	'admin:notifications:methods:friends' => 'Benachrichtigungen von Freunden',
	'admin:notifications:methods:groups' => 'Gruppen-Benachrichtigungen',

	'admin:notifications:test_email' => 'Test E-Mail',
	'admin:notifications:test_email:recipient' => 'Empfänger',
	'admin:notifications:test_email:subject' => 'Betreff',
	'admin:notifications:test_email:body' => 'Inhalt',
	'admin:notifications:test_email:attachments' => 'Anhänge',
	'admin:notifications:test_email:success' => 'E-Mail wurde erfolgreich versendet',
	'admin:notifications:test_email:error' => 'E-Mail konnte nicht gesendet werden',

	'notification:method:site' => 'Site',

	'notifications' => 'Benachrichtigungen',
	'notification' => 'Benachrichtigung',
	'notifications:all' => 'Alle Benachrichtigungen',

	'notifications:settings:digest' => 'Benachrichtigungseinstellungen',
	'notifications:settings:digest:help' => 'Hier kannst Du einstellen, wie oft Du Benachrichtigungen über bestimmte Aktionen erhalten möchtest.',

	'notifications:frequency:never' => 'Nie',
	'notifications:frequency:instantly' => 'Sofort',
	'notifications:frequency:hourly' => 'Stündlich',
	'notifications:frequency:six_hours' => 'Alle sechs Stunden',
	'notifications:frequency:twelve_hours' => 'Alle 12 Stunden',
	'notifications:frequency:daily' => 'Täglich',

	'notifications:check_online_status' => 'Schicke keine Benachrichtigung, wenn ich online bin',

	'notification:subscriptions:publish:object:blog' => 'Neuer Blog-Post',
	'notification:subscriptions:create:object:bookmarks' => 'Neues Lesezeichen',
	'notification:subscriptions:create:object:discussion' => 'Neuer Forumsbeitrag',
	'notification:subscriptions:create:object:discussion_reply' => 'Neue Antwort auf einen Forumsbeitrag',
	'notification:subscriptions:create:object:file' => 'Neue Datei',
	'notification:subscriptions:create:object:page' => 'Neue Unterseite',
	'notification:subscriptions:create:object:page_top' => 'Neue Seite',
	'notification:subscriptions:create:object:thewire' => 'Neuer Kurzbeitrag wurde erzeugt',
	'notification:subscriptions:publish:object:thewire' => 'Neuer Kurzbeitrag wurde veröffentlicht',
	'notification:subscriptions:create:object:comment' => 'Neuer Kommentar',
	'notification:subscriptions:publish:object:news' => 'Neuigkeit hinzugefügt',
	'notification:subscriptions:publish:object:hjwall' => 'Neuer Pinnwand-Eintrag',
	'notification:subscriptions:create:object:videolist_item' => 'Neues Video',
	'notification:subscriptions:create:object:event_calendar' => 'Neuer Kalendereintrag',

	'notification:instant:add_friend:user:default' => 'Jemand hat dich als Freund hinzugefügt',
	'notification:instant:friend_request:user:default' => 'Jemand hat Dir eine Freundschaftsanfrage geschickt',
	'notification:instant:friend_request_decline:user:default' => 'Jemand hat Deine Freundschaftsanfrage abgelehnt',
	'notification:instant:create:annotation:likes' => 'Jemand mag Deinen Beitrag',
	'notification:instant:add_membership:group:default' => 'Dein Gruppenbeitritt wurde angenommen',
	'notification:instant:invite:group:default' => 'Jemand hat Dich zu einer Gruppe eingeladen',
	'notification:instant:create:object:comment' => 'Jemand hat Deinen Beitrag kommentiert',

	'notifications:subscriptions' => 'Aktivitäts-Benachrichtigungen',
	'notifications:subscriptions:title' => 'Benachrichtigungen über Aktivitäten bezüglich Deiner Beiträge',
	'notifications:instant' => 'Persönliche Benachrichtigungen',
	'notifications:instant:title' => 'Benachrichtigungen über Deine Freunde und Gruppen',

	'notifications:settings:digest:success' => 'Benachrichtigungseinstellungen wurden erfolgreich gespeichert',

	'notifications:digest:subject' => 'Benachrichtungszusammenfassung',
	'notifications:digest:body_intro' => 'Hier ist die Zusammenfassung Deiner E-Mailbenachrichtigungen von %s',

	'notifications:settings:enable_html_emails' => 'HTML-E-Mails aktivieren',

	'notifications:settings:transport_settings' => 'Transport-Einstellungen',
	'notifications:settings:from_email' => 'E-Mail Absender',
	'notifications:settings:from_email:help' => 'Eine E-Mailadresse, die für ausgehende E-Mailbenachrichtigungen benutzt wird (falls nicht gleich der Site-E-Mailadresse)',
	'notifications:settings:from_name' => 'E-Mail Name',
	'notifications:settings:from_name:help' => 'Name für ausgehende E-Mailbenachrichtigungen (falls nicht gleich des Site-E-Mailnamens)',
	'notifications:settings:transport' => 'E-Mail Transport',
	'notifications:settings:transport:sendmail' => 'Sendmail',
	'notifications:settings:transport:file' => 'File Transport',
	'notifications:settings:transport:smtp' => 'SMTP',
	'notifications:settings:transport:help' => 'Wähle aus, welcher E-Mailtransport benutzt werden soll, um ausgehende E-Mails auszuliefern. Benutze den File Transport, um ausgehende E-Mails zu deaktivieren und'
	. ' sie stattdessen in eine Datei zu schreiben. Sendmail ist der standardmäßige Transport. Konfiguriee den SMTP-Server, wenn Du SMTP auswählst',
	'notifications:settings:smtp_settings' => 'SMTP Einstellungen',
	'notifications:settings:smtp_host_name' => 'SMTP Server',
	'notifications:settings:smtp_host_name:help' => 'Name des SMTP-Servers (Standard: localhost)',
	'notifications:settings:smtp_host' => 'SMTP Server-Adresse',
	'notifications:settings:smtp_host:help' => 'Name oder IP-Adresse (Standard: 127.0.0.1)',
	'notifications:settings:smtp_port' => 'SMTP Port',
	'notifications:settings:smtp_port:help' => 'Port, auf dem der SMTP-Server horcht (Standard: 25)',
	'notifications:settings:smtp_ssl' => 'Sichere Verbindung',
	'notifications:settings:smtp_ssl:help' => 'Für Anmeldeverfaren außer SMTP musst Du vermutlich einen Benutzernamen und ein Passwort einstellen. Für sichere Verbindungen musst Du den Port auf 587 für TLS und 465 für SMTPS benutzen.',
	'notifications:settings:smtp_connection' => 'SMTP Anmeldeverfaren',
	'notifications:settings:smtp_connection:help' => 'Das zu benutzende Anmeldeverfaren',
	'notifications:settings:smtp_connection:smtp' => 'SMTP',
	'notifications:settings:smtp_connection:plain' => 'SMTP mit AUTH PLAIN',
	'notifications:settings:smtp_connection:login' => 'SMTP mit AUTH LOGIN',
	'notifications:settings:smtp_connection:crammd5' => 'SMTP mit AUTH CRAM-MD5',
	'notifications:settings:smtp_username' => 'SMTP Benutzername',
	'notifications:settings:smtp_password' => 'SMTP Passwort',

	'notifications:settings:mode' => 'Site-Modus',
	'notifications:settings:mode:help' => 'Im Staging-Modus wird die E-Mail/Domain Whitelist benutzt. Im Production-Modus, werden ganz normal E-Mails verschickt',
	'notifications:settings:mode:production' => 'Production-Modus',
	'notifications:settings:mode:staging' => 'Staging-Modus',

	'notifications:settings:staging_emails' => 'Zugelassene Adressen im Staging-Modus',
	'notifications:settings:staging_emails:help' => 'Trage eine E-Mailadresse pro Zeile ein. Nur diese Adressen (zusätzlich zu den Staging-Domains) werden E-Mailbenachrichtigungen erhalten',

	'notifications:settings:staging_domains' => 'Zugelassene Domains im Staging-Modus',
	'notifications:settings:staging_domains:help' => 'Trage eine Domain pro Zeile ein. Nur diese Domains (zusätzlich zu den Staging-E-Mailadressen) werden E-Mailbenachrichtigungen erhalten',

	'notifications:settings:staging_catch_all' => 'Catch-All-Adresse im Staging-Modus',
	'notifications:settings:staging_catch_all:help' => 'Falls angegeben, werden alle E-Mails und Benachrichtigung (an Adressen außerhalb der Whitelist) an diese Adresse zugestellt. Lasse sie leer, um E-Mails in eine Datei zu schreiben',

	'notifications:footer:link' => 'hier',
	'notifications:footer' => 'Diese E-Mail wurde verschickt von %s.<br />Du kannst Deine Benachrichtigungseinstellungen %s ändern.<br />Bite antworte nicht auf diese E-Mail.',

	'notifications:error:not_found' => 'Benachrichtigung nicht gefunden',
	'notifications:mark_all_read' => 'Alle als gelesen markieren',

	'notifications:no_results' => 'Du hast noch keine Benachrichtigungen',

	'admin:upgrades:notifications:notifier' => 'Notifier-Migration',
	'admin:upgrades:notifications:notifier:description' => 'Migriere Benachrichtigungsobjekte, die vom Notifier-Plugin erstellt worden sindin die neue Datenbanktabelle und lösche überflüssige Einträge',

	'notifications:settings:ticker' => 'Benachrichtigungszähler-Aktualisierung',
	'notifications:settings:ticker:help' => '
		Ein Intervall in Sekunden nachdem der Benachrichtigungszähler aktualisiert werden soll.
		Setze es auf 0, um automatische Updates auszuschalten.
		Es kann sein, dass Du Deinen Cache aktualisieren muss, damit dies funktioniert.
	',

];
