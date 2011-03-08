<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$l_validation = array(
		'headline' => 'Online-Validierung dieses Dokuments.',
//  variables for checking html files.
		'description' => 'Sie können hier einige Dienste des Webs nutzen, um Ihre Seiten nach Validität, bzw. Zugänglichkeit zu testen.',
		'available_services' => 'Eingetragene Dienste',
		'category' => 'Kategorie',
		'service_name' => 'Name des Diensts',
		'service' => 'Dienst',
		'host' => 'Host',
		'path' => 'Pfad',
		'ctype' => 'Datei-Typ',
		'ctype' => 'Erkennungsmerkmal für den Zielserver, um was für eine Datei es sich handelt. (text/html oder text/css)',
		'fileEndings' => 'Datei-Endungen',
		'fileEndings' => 'Dateiendungen für den dieser Service benutzt werden soll, können hier eingetragen werden. (.html,.css)',
		'method' => 'Methode',
		'checkvia' => 'Verschicken per',
		'checkvia_upload' => 'Datei-Upload',
		'checkvia_url' => 'URL-Übergabe',
		'varname' => 'Variablenname',
		'varname' => '(Name des HTML-Eingabefelds der Datei/ URL eintragen)',
		'additionalVars' => 'Zusatz-Parameter',
		'additionalVars' => 'optional: var1=wert1&var2=wert2&...',
		'active' => 'Aktiv',
		'active' => 'Sie können Dienste zeitweise ausblenden.',
		'result' => 'Ergebnis',
		'no_services_available' => 'Für diesen Dateityp sind noch keine Dienste eingetragen.',
//  the different predefined services
		'adjust_service' => 'Validierungsdienste bearbeiten',
		'art_custom' => 'Benutzerdefinierte Dienste',
		'art_default' => 'Voreingestellte Dienste',
		'category_xhtml' => '(X)HTML',
		'category_links' => 'Links',
		'category_css' => 'Cascading Style Sheets',
		'category_accessibility' => 'Zugänglichkeit',
		'edit_service' => array(
				'new' => 'Neuer Dienst',
				'saved_success' => 'Der Dienst wurde gespeichert.',
				'saved_failure' => 'Der Dienst konnte nicht gespeichert werden.',
				'servicename_already_exists' => 'Ein Dienst mit diesem Namen existiert bereits.',
				'delete_success' => 'Der Dienst wurde erfolgreich gelöscht.',
				'delete_failure' => 'Der Dienst konnte nicht gelöscht werden.',
		),
//  services for html
		'service_xhtml_upload' => '(X)HTML Validierung des W3C per Datei-Upload',
		'service_xhtml_url' => '(X)HTML Validierung des W3C per URL-Übergabe',
//  services for css
		'service_css_upload' => 'CSS Validierung per Datei-Upload',
		'service_css_url' => 'CSS Validierung per URL-Übergabe',
		'connection_problems' => '<strong>Bei der Verbindung zu dem gewählten Dienst ist ein Fehler aufgetreten.</strong><br /><br />Bitte beachten Sie: Die Option "URL-Übergabe" kann nur verwendet werden, wenn Ihre webEdition-Installation vom Internet (also auch ausserhalb ihres lokalen Netzwerks) aus zu erreichen ist. Dies ist nicht der Fall bei einer lokalen Installation (Localhost).<br /><br />Ebenso können Probleme mit Firewalls und Proxy-Servern auftreten. Überprüfen Sie in diesen Fällen bitte Ihre Konfiguration.<br /><br />HTTP-Antwort: %s',
);