<?php

$lang['email_must_be_array'] = "Die E-Mail-Validierungsmethode muss als Array übermittelt werden.";
$lang['email_invalid_address'] = "Ungültige E-Mail-Adresse: %s";
$lang['email_attachment_missing'] = "Der Dateianhang %s wurde nicht gefunden.";
$lang['email_attachment_unredable'] = "Der Dateianhang %s konnte nicht geöffnet werden.";
$lang['email_no_recipients'] = "Die Empfänger To, Cc oder Bcc müssen angegeben werden.";
$lang['email_send_failure_phpmail'] = "Die Nachricht konnte nicht über die PHP-Funktion mail() versandt werden. Der Server muss möglicherweise so konfiguriert werden, dass er Mails mit dieser Funktion versenden kann.";
$lang['email_send_failure_sendmail'] = "Die Nachricht konnte nicht über sendmail versandt werden. Der Server muss möglicherweise so konfiguriert werden, dass er Mails mit sendmail versenden kann.";
$lang['email_send_failure_smtp'] = "Die Nachricht konnte nicht über die PHP SMTP versandt werden. Der Server muss möglicherweise so konfiguriert werden, dass er Mails mit dieser Funktion versenden kann.";
$lang['email_sent'] = "Die Nachricht wurde erfolgreich mittels %s versandt.";
$lang['email_no_socket'] = "Es konnte kein Socket für Sendmail geöffnet werden. Bitte prüfen Sie die Einstellungen.";
$lang['email_no_hostname'] = "Es wurde kein SMTP-Hostname angegeben.";
$lang['email_smtp_error'] = "Der SMTP-Fehler %s ist aufgetreten";
$lang['email_no_smtp_unpw'] = "SMTP-Benutzername und -Passwort müssen angegeben werden.";
$lang['email_filed_smtp_login'] = "Der AUTH LOGIN konnte nicht gesendet werden. Fehler: %s";
$lang['email_smtp_auth_un'] = "Der Benutzername konnte nicht bestätigt werden. Fehler: %s";
$lang['email_smtp_auth_pw'] = "Das Passwort konnte nicht bestätigt werden. Fehler: %s";
$lang['email_smtp_data_failure'] = "Die Daten konnten nicht versandt werden: %s";

?>