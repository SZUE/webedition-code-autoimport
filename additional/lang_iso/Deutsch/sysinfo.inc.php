<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");
/**
 * webEdition CMS
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

$_sysinfo['we_version'] = 'webEdition-Version';
$_sysinfo['server_name'] = 'Server-Name';
$_sysinfo['port'] ='Port' ;
$_sysinfo['protocol'] = 'Protokoll';
$_sysinfo['installation_folder'] = 'Installation Verzeichnis';
$_sysinfo['we_max_upload_size'] = 'Max. Dateigr��e f�r Uploads';
$_sysinfo['php_version'] = 'PHP-Version';
$_sysinfo['mysql_version'] = 'MySql-Version';
$_sysinfo['more_info'] = 'weitere Informationen anzeigen';
$_sysinfo['back'] = 'zur�ck';
$_sysinfo['sysinfo'] = 'Systeminformationen';
$_sysinfo['zendframework_version'] = 'Zend Framework Version';
$_sysinfo["register_globals warning"] = 'WARNUNG: register_globals kann die Sicherheit Ihres Systems erheblich beeintr�chtigen. Wir empfehlen daher, diese Funktion zu deaktivieren!';
$_sysinfo["short_open_tag warning"] = 'WARNUNG: short_open_tag kann zu erheblichen Problemen bei der Verarbeitung von xml-Dateien f�hren, z.B. f�r die Erzeugung von Backup-Files. Wir empfehlen daher, diese Funktion zu deaktivieren!';
$_sysinfo["safe_mode warning"] = 'Der PHP Safe Mode kann u.U. zu Probelem bei Installation und Aktualisierung von webEdition f�hren. Deaktivieren Sie in diesem Fall den Safe Mode.';
$_sysinfo["zend_framework warning"] = 'Sie verwenden derzeit eine andere Version des Zend Framework als die f�r webEdition urspr�nglich vorhergesehene Version '.WE_ZFVERSION.'.';
$_sysinfo["suhosin warning"] = 'Wegen der vielf�ltigen Konfigurationsm�glichkeiten kann bei Nutzung dieser PHP Erweiterung die volle Funktionsf�higkeit von webEdition leider nicht garantiert werden.';
$_sysinfo["dbversion warning"] = 'Der verwendete DB-Server meldet die Version %s, webEdition ben�tigt jedoch mindestens die MySQL-Server Version 5.0. webEdition mag mit der genutzten Version funktionieren, dies kann jedoch nicht f�r neue webEdition Versionen (z.B. nach Updates) garantiert werden.  Sp�testens ab webEdition Version 7 wird MySQL Version 5 ben�tigt. Au�erdem: die auf dem Server installierte MySQL Version ist veraltet. F�r diese Version gibt es keine Updates mehr, dies kann die Sicherheit des gesamten Systems beeintr�chtigen.';
$_sysinfo["pcre warning"] = 'Versionen vor 7.0 k�nnen zu ernsten Problemen f�hren';
$_sysinfo["pcre_unkown"] = 'Nicht detektierbar';
$_sysinfo["not_active"] = 'Nicht aktiv';
$_sysinfo["exif warning"] = 'EXIF-Metadaten f�r Bilder sind nicht verf�gbar';
$_sysinfo['sdk_db warning'] = 'SDK Operationen und WE-APPS mit Datenbanknutzung sind nicht verf�gbar (ben�tigt: PDO &amp; PDO_mysql)';
$_sysinfo['phpext warning'] = 'nicht verf�gbar: ';
$_sysinfo['phpext warning2'] = 'Die Funktion von webEdition kann nicht garantiert werden!';
$_sysinfo['detectable warning'] = 'Einige Softwarevoraussetzungen konnten nicht �berpr�ft werden (Suhosin?). Bitte pr�fen Sie die Systemvoraussetzungen unter http://www.webedition.org/de/webedition-cms/systemvoraussetzungen.php';

$_sysinfo['connection_types'] = 'Update-Verbindungstypen';
$_sysinfo['gdlib'] = 'GDlib Unterst�tzung';
$_sysinfo['mbstring'] = 'Multibyte String Funktionen';
$_sysinfo['version'] = 'Version';
$_sysinfo['available'] = 'verf�gbar';
$_sysinfo['exif'] = 'EXIF Unterst�tzung';
$_sysinfo['pcre'] = 'PCRE-Extension';
$_sysinfo['sdk_db'] = 'SDK/Apps DB Unterst�tzung';
$_sysinfo['phpext'] = 'Minimal notwendige PHP-Extensions';

?>