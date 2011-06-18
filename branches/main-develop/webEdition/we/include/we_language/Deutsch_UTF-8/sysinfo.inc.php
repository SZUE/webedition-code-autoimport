<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we.inc.php");
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
/**
 * Language file: sysinfo.inc.php
 * Provides language strings for system info.
 * Language: Deutsch
 */
$l_sysinfo = array(
		'we_version' => 'webEdition-Version',
		'server_name' => 'Server-Name',
		'port' => 'Port',
		'protocol' => 'Protokoll',
		'installation_folder' => 'Installation Verzeichnis',
		'we_max_upload_size' => 'Max. Dateigröße für Uploads',
		'php_version' => 'PHP-Version',
		'mysql_version' => 'MySql-Version',
		'more_info' => 'weitere Informationen anzeigen',
		'back' => 'zurück',
		'sysinfo' => 'Systeminformationen',
		'zendframework_version' => 'Zend Framework Version',
		'register_globals warning' => 'WARNUNG: register_globals kann die Sicherheit Ihres Systems erheblich beeinträchtigen. Wir empfehlen daher, diese Funktion zu deaktivieren!',
		'short_open_tag warning' => 'WARNUNG: short_open_tag kann zu erheblichen Problemen bei der Verarbeitung von xml-Dateien führen, z.B. für die Erzeugung von Backup-Files. Wir empfehlen daher, diese Funktion zu deaktivieren!',
		'safe_mode warning' => 'Der PHP Safe Mode kann u.U. zu Probelem bei Installation und Aktualisierung von webEdition führen. Deaktivieren Sie in diesem Fall den Safe Mode.',
		'zend_framework warning' => 'Sie verwenden derzeit eine andere Version des Zend Framework als die für webEdition ursprünglich vorhergesehene Version ' . WE_ZFVERSION . '.',
		'suhosin warning' => 'Wegen der vielfältigen Konfigurationsmöglichkeiten kann bei Nutzung dieser PHP Erweiterung die volle Funktionsfähigkeit von webEdition leider nicht garantiert werden.',
		'dbversion warning' => 'Der verwendete DB-Server meldet die Version %s, webEdition benötigt jedoch mindestens die MySQL-Server Version 5.0. webEdition mag mit der genutzten Version funktionieren, dies kann jedoch nicht für neue webEdition Versionen (z.B. nach Updates) garantiert werden.  Spätestens ab webEdition Version 7 wird MySQL Version 5 benötigt. Außerdem: die auf dem Server installierte MySQL Version ist veraltet. Für diese Version gibt es keine Updates mehr, dies kann die Sicherheit des gesamten Systems beeinträchtigen.',
		'pcre warning' => 'Versionen vor 7.0 können zu ernsten Problemen führen',
		'pcre_unkown' => 'Nicht detektierbar',
		'exif warning' => 'EXIF-Metadaten für Bilder sind nicht verfügbar',
		'sdk_db warning' => 'SDK Operationen und WE-APPS mit Datenbanknutzung sind nicht verfügbar (benötigt: PDO &amp; PDO_mysql)',
		'phpext warning' => 'nicht verfügbar: ',
		'phpext warning2' => 'Die Funktion von webEdition kann nicht garantiert werden!',
		'detectable warning' => 'Einige Softwarevoraussetzungen konnten nicht überprüft werden (Suhosin?). Bitte prüfen Sie die Systemvoraussetzungen unter http://www.webedition.org/de/webedition-cms/systemvoraussetzungen.php',
		'connection_types' => 'Update-Verbindungstypen',
		'gdlib' => 'GDlib Unterstützung',
		'mbstring' => 'Multibyte String Funktionen',
		'version' => 'Version',
		'available' => 'verfügbar',
		'exif' => 'EXIF Unterstützung',
		'pcre' => 'PCRE-Extension',
		'sdk_db' => 'SDK/Apps DB Unterstützung',
		'phpext' => 'Minimal notwendige PHP-Extensions',
		'not_set' => 'nicht gesetzt (off)',
		'suhosin simulation'=>'Simulations Modus',

	);
