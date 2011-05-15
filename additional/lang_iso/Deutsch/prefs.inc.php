<?php

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
 * Language file: prefs.inc.php
 * Provides language strings.
 * Language: Deutsch
 */
/* * ***************************************************************************
 * PRELOAD
 * *************************************************************************** */
$l_prefs = array(
		'preload' => "Einstellungen werden geladen, einen Moment ...",
		'preload_wait' => "Lade Einstellungen",
		/*		 * ***************************************************************************
		 * SAVE
		 * *************************************************************************** */

		'save' => "Einstellungen werden gespeichert, einen Moment ...",
		'save_wait' => "Speichere Einstellungen",
		'saved' => "Die Einstellungen wurden erfolgreich gespeichert.",
		'saved_successfully' => "Einstellungen gespeichert",
		/*		 * ***************************************************************************
		 * TABS
		 * *************************************************************************** */

		'tab_ui' => "Oberfl�che",
		'tab_glossary' => "Glossar",
		'tab_extensions' => "Dateierweiterungen",
		'tab_editor' => 'Editor',
		'tab_formmail' => 'Formmail',
		'formmail_recipients' => 'Formmail Empf�nger',
		'tab_proxy' => 'Proxy Server',
		'tab_advanced' => 'Erweitert',
		'tab_system' => 'System',
		'tab_seolinks' => 'SEO-Links',
		'tab_error_handling' => 'Fehlerbehandlung',
		'tab_cockpit' => 'Cockpit',
		'tab_cache' => 'Cache',
		'tab_language' => 'Sprachen',
		'tab_countries' => 'L�nder',
		'tab_modules' => 'Module',
		'tab_versions' => 'Versionierung',
		/*		 * ***************************************************************************
		 * USER INTERFACE
		 * *************************************************************************** */
		/**
		 * Countries
		 */
		'countries_information' => "W�hlen Sie hier die L�nder aus, die in der Kundenverwaltung, im Shop usw. ausgew�hlt werden k�nnen.",
		'countries_headline' => "L�nderauswahl",
		'countries_country' => "Land",
		'countries_top' => "Top-Liste",
		'countries_show' => "anzeigen",
		'countries_noshow' => "keine Anzeige",
		/**
		 * LANGUAGE
		 */
		'choose_language' => "Sprache",
		'language_notice' => "Die Sprachumstellung wird erst nach einem Neustart von webEdition an allen Stellen durchgef�hrt.",
		/**
		 * CHARSET
		 */
		'default_charset' => "Standard-Charset",
		/**
		 * SEEM
		 */
		'seem' => "seeMode",
		'seem_deactivate' => "deaktivieren",
		'seem_startdocument' => "Startseite",
		'seem_start_type_document' => "Dokument",
		'seem_start_type_object' => "Objekt",
		'seem_start_type_cockpit' => "Cockpit",
		'question_change_to_seem_start' => "M�chten Sie zum ausgew�hlten Dokument wechseln?",
		/**
		 * Sidebar
		 */
		'sidebar' => "Sidebar",
		'sidebar_deactivate' => "deaktivieren",
		'sidebar_show_on_startup' => "beim Starten anzeigen",
		'sidebar_width' => "Breite in Pixel",
		'sidebar_document' => "Dokument",
		/**
		 * WINDOW DIMENSION
		 */
		'dimension' => "Fenstergr��e",
		'maximize' => "Maximieren",
		'specify' => "Spezifizieren",
		'width' => "Breite",
		'height' => "H�he",
		'predefined' => "Voreingestellte Gr��en",
		'show_predefined' => "Voreingestellte Gr��en anzeigen",
		'hide_predefined' => "Voreingestellte Gr��en ausblenden",
		/**
		 * TREE
		 */
		'tree_title' => "Baummen�",
		'all' => "Alle",
		/*		 * ***************************************************************************
		 * FILE EXTENSIONS
		 * *************************************************************************** */

		/**
		 * FILE EXTENSIONS
		 */
		'extensions_information' => "Hier werden die standardm��ig verwendeten Datei-Erweiterungen f�r statische und dynamische Seiten festgelegt.",
		'we_extensions' => "webEdition-Erweiterungen",
		'static' => "Statische Seiten",
		'dynamic' => "Dynamische Seiten",
		'html_extensions' => "HTML-Erweiterungen",
		'html' => "HTML-Dateien",
		/*		 * ***************************************************************************
		 * Glossary
		 * *************************************************************************** */

		'glossary_publishing' => "Pr�fen bei Ver�ffentlichung",
		'force_glossary_check' => "Glossarpr�fung erzwingen",
		'force_glossary_action' => "Aktion erzwingen",
		/*		 * ***************************************************************************
		 * COCKPIT
		 * *************************************************************************** */

		/**
		 * Cockpit
		 */
		'cockpit_amount_columns' => "Spalten im Cockpit",
		/*		 * ***************************************************************************
		 * CACHING
		 * *************************************************************************** */

		/**
		 * Cache Type
		 */
		'cache_information' => "Stellen Sie hier die Werte ein, mit welchen die Felder \"Art des Caches\" und \"Cache G�ltigkeit in Sekunden\" bei neuen Vorlagen belegt sein sollen.<br /><br />Beachten Sie bitte, dass diese Einstellung lediglich eine Vorbelegung der Felder ist.",
		'cache_navigation_information' => "Tragen Sie hier die Standardwerte f�r den Tag &lt;we:navigation&gt; ein. Dieser Wert kann durch das setzen des Attributes cachelifetime im Tag &lt;we:navigation&gt; �berschrieben werden.",
		'cache_presettings' => "Voreinstellung",
		'cache_type' => "Art des Caches",
		'cache_type_none' => "Caching deaktiviert",
		'cache_type_full' => "Full Cache",
		'cache_type_document' => "Dokument Cache",
		'cache_type_wetag' => "we:Tag Cache",
		'delete_cache_after' => 'Cache der Navigation l�schen',
		'delete_cache_add' => 'nach Anlegen eines neuen Eintrages',
		'delete_cache_edit' => 'nach �ndern eines Eintrages',
		'delete_cache_delete' => 'nach L�schen eines Eintrages',
		'cache_navigation' => 'Standardeinstellung',
		'default_cache_lifetime' => 'Standard Cache G�ltigkeit',
		/**
		 * Cache Life Time
		 */
		'cache_lifetime' => "Cache G�ltigkeit in Sekunden",
		'cache_lifetimes' => array(
				0 => "",
				60 => "1 Minute",
				300 => "5 Minuten",
				600 => "10 Minuten",
				1800 => "30 Minuten",
				3600 => "1 Stunde",
				21600 => "6 Stunden",
				43200 => "12 Stunden",
				86400 => "1 Tag",
		),
		/*		 * ***************************************************************************
		 * LOCALES // LANGUAGES
		 * *************************************************************************** */

		/**
		 * Languages
		 */
		'locale_information' => "F�gen Sie hier alle Sprachen (Locales) hinzu, f�r welche Sie eine Webseite mit webEdition erstellen m�chten.<br />Sie k�nnen dann das Locale den einzelnen Dokumenten/Objekten und Verzeichnissen zuweisen.<br />Diese Einstellung wird f�r das Glossar und die Rechtschreibpr�fung einzelner Dokumente verwendet, steht aber auch z.B. f�r listviews als Selektionskriterium zur Verf�gung",
		'locale_languages' => "Sprache",
		'locale_countries' => "Land",
		'locale_add' => "Sprache hinzuf�gen",
		'cannot_delete_default_language' => "Die Standardsprache kann nicht gel�scht werden.",
		'language_already_exists' => "Diese Sprache wurde bereits angelegt.",
		'language_country_missing' => "Bitte w�hlen Sie auch ein Land aus",
		'add_dictionary_question' => "M�chten Sie gleich das W�rterbuch f�r diese Sprache hinzuf�gen?",
		'langlink_headline' => "Unterst�tzung f�r die Verlinkung zwischen verschiedenen Sprachen",
		'langlink_information' => "Mit dieser Option k�nnen Sie im Backend die verschiedenen korrespondierenden Sprachversionen eines Dokumentes/Objektes verwalten und diese Dokumente zuweisen, aufrufen usw.<br/>Eine Ausgabe im Frontend erfolgt dann �ber eine listview type=languagelink.<br/><br/>F�r Verzeichnisse kann dann ein <b>Dokument</b> in der jeweiligen Sprache gew�hlt werden, auf das zur�ckgegriffen wird, wenn einzelnen Dokumenten im Verzeichnis selbst kein korrespondierendes Sprachdokument zugewiesen wurde.",
		'langlink_support' => "Aktiviert",
		'langlink_support_backlinks' => "Erzeuge automatisch die R�cklinks",
		'langlink_support_backlinks_information' => "R�cklinks k�nnen f�r Dokumente (nicht Verzeichnisse!) automatisch generiert werden. Dabei sollte das verlinkte Dokument nicht in einem Editor-Tab ge�ffnet sein!",
		'langlink_support_recursive' => "Erzeuge die Sprachenlinks rekursiv",
		'langlink_support_recursive_information' => "Recursives Setzen der Sprachlinks generiert f�r Dokumente (nicht Verzeichnisse!) alle verf�gbaren Links und versucht den Sprachenkreis schnellstm�glich zu schlie�en. Dabei sollten die verlinkten Dokument nicht in einem Editor-Tab ge�ffnet sein!",
		/*		 * ***************************************************************************
		 * EDITOR
		 * *************************************************************************** */

		/**
		 * EDITOR PLUGIN
		 */
		'editor_plugin' => 'Editor PlugIn',
		'use_it' => "Benutzen",
		'start_automatic' => "Automatisch starten",
		'ask_at_start' => 'Beim Starten nachfragen,<br>welcher Editor benutzt<br>werden soll',
		'must_register' => 'Muss registriert sein',
		'change_only_in_ie' => 'Da das Editor PlugIn nur unter Windows im Internet Explorer, Mozilla, Firebird sowie Firefox funktioniert, sind diese Einstellungen nicht ver�nderbar.',
		'install_plugin' => 'Um das Editor PlugIn in Ihrem Browser benutzen zu k�nnen, muss das Mozilla ActiveX PlugIn installiert werden.',
		'confirm_install_plugin' => 'Das Mozilla ActiveX PlugIn erm�glicht es, ActiveX Controls in Mozilla Browser zu integrieren. Nach der Installation muss der Browser neu gestartet werden.\\n\\nBeachten Sie: ActiveX kann ein Sicherheitsrisiko darstellen!\\n\\nMit der Installation fortfahren?',
		'install_editor_plugin' => 'Um das webEdition Editor PlugIn in Ihrem Browser benutzen zu k�nnen, muss es installiert werden.',
		'install_editor_plugin_text' => 'Das webEdition Editor PlugIn wird installiert...',
		/**
		 * TEMPLATE EDITOR
		 */
		'editor_information' => "Geben Sie hier Schriftart und Gr��e an, die f�r die Bearbeitung der Vorlagen, CSS- und JavaScript-Dateien innerhalb von webEdition verwendet werden soll.<br /><br />Diese Einstellungen werden f�r den Texteditor der obengenannten Dateitypen verwendet.",
		'editor_mode' => 'Editor',
		'editor_font' => 'Schrift im Editor',
		'editor_fontname' => 'Schriftart',
		'editor_fontsize' => 'Gr��e',
		/*		 * ***************************************************************************
		 * FORMMAIL
		 * *************************************************************************** */

		/**
		 * FORMMAIL RECIPIENTS
		 */
		'formmail_information' => "Tragen Sie hier alle E-Mail-Adressen ein, an welche Formulare mit der Formmail-Funktion (&lt;we:form type=\"formmail\" ..&gt;) geschickt werden d�rfen.<br><br>Ist hier keine E-Mail-Adresse eingetragen, kann man keine Formulare mit der Formmail-Funktion verschicken!",
		'formmail_log' => "Formmail-Logbuch",
		'log_is_empty' => "Das Logbuch ist leer!",
		'ip_address' => "IP Adresse",
		'blocked_until' => "Geblockt bis",
		'unblock' => "freigeben",
		'clear_log_question' => "M�chten Sie das Logbuch wirklich leeren?",
		'clear_block_entry_question' => "M�chten Sie die IP %s wirklich freigeben?",
		'forever' => "F�r immer",
		'yes' => "ja",
		'no' => "nein",
		'on' => "ein",
		'off' => "aus",
		'formmailConfirm' => "Formmail Best�tigungsfunktion",
		'logFormmailRequests' => "Formmail Anfragen protokollieren",
		'deleteEntriesOlder' => "Eintr�ge l�schen die �lter sind als",
		'formmailViaWeDoc' => "Formmail �ber webEdition-Dokument aufrufen",
		'blockFormmail' => "Formmail Anfragen begrenzen",
		'formmailSpan' => "Innerhalb der Zeitspanne",
		'formmailTrials' => "Erlaubte Anfragen",
		'blockFor' => "Blockieren f�r",
		'never' => "nie",
		'1_day' => "1 Tag",
		'more_days' => "%s Tage",
		'1_week' => "1 Woche",
		'more_weeks' => "%s Wochen",
		'1_year' => "1 Jahr",
		'more_years' => "%s Jahre",
		'1_minute' => "1 Minute",
		'more_minutes' => "%s Minuten",
		'1_hour' => "1 Stunde",
		'more_hours' => "%s Stunden",
		'ever' => "immer",
		/*		 * ***************************************************************************
		 * PROXY SERVER
		 * *************************************************************************** */

		/**
		 * PROXY SERVER
		 */
		'proxy_information' => "Hier nehmen Sie die Einstellungen f�r den Proxy Server vor, falls Ihr Server einen Proxy f�r die Verbindung mit dem Internet verwendet.",
		'useproxy' => "Proxy Server f�r Live-Update<br>verwenden",
		'proxyaddr' => "Adresse",
		'proxyport' => "Port",
		'proxyuser' => "Benutzername",
		'proxypass' => "Kennwort",
		/*		 * ***************************************************************************
		 * ADVANCED
		 * *************************************************************************** */

		/**
		 * ATTRIBS
		 */
		'default_php_setting' => "Standard Einstellung f�r<br><em>php</em>-Attribut in we:tags",
		/**
		 * INLINEEDIT
		 */
		'inlineedit_default' => "Standard Einstellung f�r<br><em>inlineedit</em>-Attribut in<br>&lt;we:textarea&gt;",
		'removefirstparagraph_default' => "Standard Einstellung f�r<br><em>removefirstparagraph</em>-Attribut in<br>&lt;we:textarea&gt;",
		'hidenameattribinweimg_default' => "Keine Ausgabe von name=xyz in we:img (HTML 5)",
		'hidenameattribinweform_default' => "Keine Ausgabe von name=xyz in we:form (XHTML strict)",
		/**
		 * SAFARI WYSIWYG
		 */
		'safari_wysiwyg' => "Safari Wysiwyg Editor<br>(Betaversion) benutzen",
		/**
		 * SHOWINPUTS
		 */
		'showinputs_default' => "Standard Einstellung f�r<br><em>showinputs</em>-Attribut in<br>&lt;we:img&gt;",
		/**
		 * NAVIGATION
		 */
		'navigation_entries_from_document' => "Erzeuge neue Navigations-Eintr�ge aus dem Dokument als",
		'navigation_entries_from_document_item' => "Eintrag",
		'navigation_entries_from_document_folder' => "Ordner",
		'navigation_rules_continue' => "Werte Navigationsregeln auch nach einem ersten Match aus",
		'general_directoryindex_hide' => "Verstecke DirectoryIndex-Dateinamen in der Ausgabe",
		'general_directoryindex_hide_description' => "F�r die Tags <we:link>, <we:linklist>, <we:listview> kann das Attribut 'hidedirindex' verwendet werden",
		'navigation_directoryindex_hide' => "der Navigation",
		'wysiwyglinks_directoryindex_hide' => "von Links aus dem WYSIWYG-Editor",
		'objectlinks_directoryindex_hide' => "von Links auf Objekte",
		'navigation_directoryindex_description' => "Nach einer �nderung muss ein Rebuild durchgef�hrt werden (z.B. Navigation Cache, Objekte usw.)",
		'navigation_directoryindex_names' => "DirectoryIndex-Dateinamen (Komma-separiert, einschl. Datei-Extensions, z.B. 'index.php,index.html')",
		'general_objectseourls' => "Erzeuge Objekt SEO-Urls ",
		'navigation_objectseourls' => "in der Navigation",
		'wysiwyglinks_objectseourls' => "bei Links aus dem WYSIWYG-Editor",
		'general_objectseourls_description' => "F�r die Tags <we:link>, <we:linklist>, <we:listview>, <we:object> kann das Attribut 'objectseourls' verwendet, oder die folgende Voreinstellung vorgenommen werden:",
		'taglinks_directoryindex_hide' => "Voreinstellung f�r Tags",
		'taglinks_objectseourls' => "Voreinstellung f�r Tags",
		'urlencode_objectseourls' => "URLencode die SEO-Links",
		'suppress404code' => "unterdr�cke 404 not found",
		'general_seoinside' => "Darstellung innerhalb von webEdition ",
		'general_seoinside_description' => "Werden DirectoryIndex-Dateinamen und Objekt SEO-Urls innerhalb von webEdition dargestellt, so kann webEdition diese nicht mehr als interne Links erkennen und ein Klick auf den Link �ffnet nicht mehr das Bearbeitungsfenster. Mit den folgenden Optionen k�nnen beide (bis auf die Navigation) im Editmode und der Vorschau daher unterdr�ckt werden.",
		'seoinside_hideinwebedition' => "In der Vorschau verstecken",
		'seoinside_hideineditmode' => "Im Editmode verstecken",
		'navigation' => "Navigation",
		/**
		 * DATABASE
		 */
		'db_connect' => "Art der Datenbank-<br>verbindungen",
		'db_set_charset' => "Verbindungszeichensatz",
		'db_set_charset_information' => "Der Verbindungszeichensatz wird f�r die Kommunikation zwischen webEdition und Datenbank genutzt.<br/>Ist kein Wert gesetzt, so wird der Standard-Verbindungszeichensatz von PHP verwendet.<br/>Im Ideal sollten webEdition Spache (z. B. Deutsch_UTF-8), Kollation der Datenbank (z. B. utf8_general_ci), Verbindungszeichensatz (z. B. utf8) und die Einstellung externer Tools wie phpMyAdmin (z. B. utf-8) �bereinstimmen, damit mit diesen externen Tools ein Editieren von Datenbankwerten m�glich ist.",
		'db_set_charset_warning' => "Der Verbindungszeichensatz sollte nur bei einer frischen Installation von webEdition (ohne Daten in der Datenbank) ein- bzw. umgestellt werden, da sonst alle nicht ASCII-Zeichen falsch interpretiert und gegebenenfalls zerst�rt werden.",
		/**
		 * HTTP AUTHENTICATION
		 */
		'auth' => "HTTP Authentifizierung",
		'useauth' => "Server verwendet HTTP<br>Authentifizierung im webEdition<br>Verzeichnis",
		'authuser' => "Benutzername",
		'authpass' => "Kennwort",
		/**
		 * THUMBNAIL DIR
		 */
		'thumbnail_dir' => "Verzeichnis f�r Miniaturansichten",
		/**
		 * PAGELOGGER DIR
		 */
		'pagelogger_dir' => "pageLogger-Verzeichnis",
		/**
		 * HOOKS
		 */
		'hooks' => "Hooks",
		'hooks_information' => "Die Verwendung von Hooks erm�glicht die Ausf�hrung von beliebigem PHP-Code w�hrend dem Speichern, Parken, Ver�ffentlichen und L�schen jeglicher Inhaltstypen in webEdition.<br/>
	N�here Infos finden Sie in der Online-Dokumentation.<br/><br/>M�chten Sie die Ausf�hrung von Hooks zulassen?",
		/**
		 * Backward compatibility
		 */
		'backwardcompatibility' => "Abw�rtskompatibilit�t",
		'backwardcompatibility_tagloading' => "Lade alle 'alten' we_tag Funktionen",
		'backwardcompatibility_tagloading_message' => "Wird nur ben�tigt, wenn in custom_tags oder Vorlagen we_tags in der Form we_tag_tagname() aufgerufen werden.<br/> Empfohlen wird ein Aufruf in der Form we_tag<br/>('tagname',&#36;attribs,&#36;content)",
		/*		 * ***************************************************************************
		 * ERROR HANDLING
		 * *************************************************************************** */

		'error_no_object_found' => 'Fehlerseite f�r nicht existierende Objekte',
		/**
		 * TEMPLATE TAG CHECK
		 */
		'templates' => "Vorlagen",
		'disable_template_tag_check' => "Pr�fung auf fehlende,<br />schlie�ende we:tags deaktivieren",
		/**
		 * ERROR HANDLER
		 */
		'error_use_handler' => "webEdition Fehler-<br>behandlung aktivieren",
		/**
		 * ERROR TYPES
		 */
		'error_types' => "Zu behandelnde Fehler",
		'error_notices' => "Hinweise",
		'error_deprecated' => "veraltet Hinweise",
		'error_warnings' => "Warnungen",
		'error_errors' => "Fehler",
		'error_notices_warning' => 'Option f�r Entwickler! Nicht auf Live-System aktivieren.',
		/**
		 * ERROR DISPLAY
		 */
		'error_displaying' => "Fehleranzeige",
		'error_display' => "Fehler anzeigen",
		'error_log' => "Fehler protokollieren",
		'error_mail' => "Fehler als Mail senden",
		'error_mail_address' => "Adresse",
		'error_mail_not_saved' => 'Fehler werden nicht an die von Ihnen eingegebene E-Mail-Adresse geschickt, da diese Adresse fehlerhaft eingegeben wurde!\n\nDie restlichen Einstellungen wurden erfolgreich gespeichert.',
		/**
		 * DEBUG FRAME
		 */
		'show_expert' => "Experteneinstellungen anzeigen",
		'hide_expert' => "Experteneinstellungen ausblenden",
		'show_debug_frame' => "Debug-Frame anzeigen",
		'debug_normal' => "Im normalen Modus",
		'debug_seem' => "Im SeeModus",
		'debug_restart' => "�nderungen erfordern einen Neustart",
		/*		 * ***************************************************************************
		 * MODULES
		 * *************************************************************************** */

		/**
		 * OBJECT MODULE
		 */
		'module_object' => "DB/Objekt Modul",
		'tree_count' => "Anzahl anzuzeigender Objekte",
		'tree_count_description' => "Dieser Wert gibt die maximale Anzahl anzuzeigender Eintr�ge in der linken Navigation an.",
		/*		 * ***************************************************************************
		 * BACKUP
		 * *************************************************************************** */
		'backup' => "Backup",
		'backup_slow' => "Langsam",
		'backup_fast' => "Schnell",
		'performance' => "Stellen Sie hier ein passendes Leistungslevel ein. Dieses richtet sich nach der Leistungsf�higkeit Ihres Servers. Wenn die Ressourcen Ihres Systemes eingeschr�nkt sind (Speicher, Timeout etc.) w�hlen Sie bitte eine niedrigere Einstellung.",
		'backup_auto' => "Auto",
		/*		 * ***************************************************************************
		 * Validation
		 * *************************************************************************** */
		'validation' => 'Validierung',
		'xhtml_default' => 'Standardeinstellung f�r das <em>xml</em>-Attribut in we:Tags',
		'xhtml_debug_explanation' => 'Das XHTML-Debugging unterst�tzt Sie bei der Erstellung valider Websites. Optional kann jede Ausgabe eines we:Tags auf G�ltigkeit kontrolliert werden und bei Bedarf fehlerhafte Attribute entfernt, bzw. angezeigt werden. Bitte beachten Sie, dass dieser Vorgang etwas Zeit erfordert und nur w�hrend der Erstellung einer neuen Website benutzt werden sollte.',
		'xhtml_debug_headline' => 'XHTML-Debugging',
		'xhtml_debug_html' => 'XHTML-Debugging aktivieren',
		'xhtml_remove_wrong' => 'Fehlerhafte Attribute entfernen',
		'xhtml_show_wrong_headline' => 'Benachrichtigung bei fehlerhaften Attributen',
		'xhtml_show_wrong_html' => 'Aktivieren',
		'xhtml_show_wrong_text_html' => 'Als Text',
		'xhtml_show_wrong_js_html' => 'Als JavaScript-Meldung',
		'xhtml_show_wrong_error_log_html' => 'Ins Error-Log (PHP)',
		/*		 * ***************************************************************************
		 * max upload size
		 * *************************************************************************** */
		'we_max_upload_size' => "Maximale Upload Gr��e<br>in Hinweistexten",
		'we_max_upload_size_hint' => "(in MByte, 0=automatisch)",
		/*		 * ***************************************************************************
		 * we_new_folder_mod
		 * *************************************************************************** */
		'we_new_folder_mod' => "Zugriffsrechte f�r<br>neue Verzeichnisse",
		'we_new_folder_mod_hint' => "(Standard ist 755)",
		/*		 * ***************************************************************************
		 * we_doctype_workspace_behavior
		 * *************************************************************************** */

		'we_doctype_workspace_behavior_hint0' => "Das Standardverzeichnis eines Dokument-Typs mu� sich innerhalb des Arbeitsbereich des Benutzers befinden, damit der Benutzer den Dokument-Typ ausw�hlen kann.",
		'we_doctype_workspace_behavior_hint1' => "Der Arbeitsbereich des Benutzers mu� sich innerhalb des im Dokument-Typ eingestellten Standardverzeichnis befinden, damit der Benutzer den Dokument-Typ ausw�hlen kann.",
		'we_doctype_workspace_behavior_1' => "Umgekehrt",
		'we_doctype_workspace_behavior_0' => "Standard",
		'we_doctype_workspace_behavior' => "Verhalten der Dokument-Typ Auswahl",
		/*		 * ***************************************************************************
		 * jupload
		 * *************************************************************************** */

		'use_jupload' => 'Java-Upload benutzen',
		/*		 * ***************************************************************************
		 * message_reporting
		 * *************************************************************************** */
		'message_reporting' => array(
				'information' => "�ber die jeweiligen, nachfolgenden Checkboxen k�nnen Sie entscheiden, ob Sie bei den webEdition Aktionen wie z. B. Speichern, Ver�ffentlichen, L�schen usw. einen Hinweis erhalten m�chten.",
				'headline' => "Benachrichtigungen",
				'show_notices' => "Hinweise anzeigen",
				'show_warnings' => "Warnungen anzeigen",
				'show_errors' => "Fehler anzeigen",
		),
		/*		 * ***************************************************************************
		 * Module Activation
		 * *************************************************************************** */
		'module_activation' => array(
				'information' => "Hier k�nnen Sie die Module aktivieren bzw. deaktivieren wenn Sie diese nicht ben�tigen.<br />Nicht aktivierte Module verbessern die Performance von webEdition.<br />Gegebenenfalls m�ssen Sie webEdition neu starten, um Module zu aktivieren.<br />Das Shop-Modul ben�tigt das Kundenverwaltungs-Modul, das Workflow-Modul ben�tigt das ToDo-Messaging-Modul.",
				'headline' => "Modulaktivierung",
		),
		/*		 * ***************************************************************************
		 * Email settings
		 * *************************************************************************** */

		'mailer_information' => "Hier k�nnen Sie einstellen, ob f�r die von webEdition versendeten E-Mails die in PHP integrierte mail()-Funktion oder ein SMTP-Server verwendet werden soll.<br /><br />Durch die Verwendung des \"richtigen\" Mailservers sinkt das Risiko, dass Mails beim Empf�nger als \"Spam\" eingestuft werden.",
		'mailer_type' => "Mailer-Typ",
		'mailer_php' => "Benutze php mail() Funktion",
		'mailer_smtp' => "Benutze SMTP-Server",
		'email' => "E-Mail",
		'tab_email' => "E-Mail",
		'smtp_auth' => "Authentifizierung",
		'smtp_server' => "SMTP-Server",
		'smtp_port' => "SMTP-Port",
		'smtp_username' => "Benutzername",
		'smtp_password' => "Kennwort",
		'smtp_halo' => "SMTP-Halo",
		'smtp_timeout' => "SMTP-Timeout",
		'smtp_encryption' => "Verschl�sselte �bertragung",
		'smtp_encryption_none' => "nein",
		'smtp_encryption_ssl' => "SSL",
		'smtp_encryption_tls' => "TLS",
		/*		 * ***************************************************************************
		 * Versions settings
		 * *************************************************************************** */

		'versioning' => "Versionierung",
		'version_all' => "alle",
		'versioning_activate_text' => "Aktivieren sie hier die Versionierung entweder f�r alle oder nur bestimmte Inhaltstypen.",
		'versioning_time_text' => "Bei Angabe eines Zeitraums werden nur Versionen gespeichert, deren Erstellungsdatum sich innerhalb des angegebenen Zeitraums bis heute befindet. �ltere Versionen werden gel�scht.",
		'versioning_time' => "Zeitraum",
		'versioning_anzahl_text' => "Geben Sie hier eine Anzahl von Versionen an, die f�r jedes Dokument bzw. Objekt gespeichert werden sollen. ",
		'versioning_anzahl' => "Anzahl",
		'versioning_wizard_text' => "�ffnen Sie den Versions-Wizard um Versionen zu l�schen oder �ltere Versionen wiederherzustellen.",
		'versioning_wizard' => "Versions-Wizard �ffnen",
		'ContentType' => "Inhaltstyp",
		'versioning_create_text' => "Legen Sie fest, bei welchen Aktionen Versionen erzeugt werden sollen. Entweder nur beim Ver�ffentlichen oder auch beim Speichern, Parken, L�schen oder Importieren.",
		'versioning_create' => "Version erstellen",
		'versions_create_publishing' => "nur beim Ver�ffentlichen",
		'versions_create_always' => "immer",
		'versioning_templates_text' => "Legen Sie hier spezielle Werte f�r die <b>Versionierung von Vorlagen</b> fest",
		'versions_create_tmpl_publishing' => "nur durch speziellen Button",
		'versions_create_tmpl_always' => "immer",
		'use_jeditor' => "Benutzen",
		'editor_font_colors' => 'Schriftfarben spezifizieren',
		'editor_normal_font_color' => 'Standard',
		'editor_we_tag_font_color' => 'webEdition-Tags',
		'editor_we_attribute_font_color' => 'webEdition-Attribute',
		'editor_html_tag_font_color' => 'HTML-Tags',
		'editor_html_attribute_font_color' => 'HTML-Attribute',
		'editor_pi_tag_font_color' => 'PHP Code',
		'editor_comment_font_color' => 'Kommentare',
		'editor_highlight_colors' => 'Highlighting-Farben',
		'editor_linenumbers' => 'Zeilennummern',
		'editor_completion' => 'Codevervollst�ndigung',
		'editor_tooltips' => 'Tooltips auf we:tags',
		'editor_docuclick' => 'Doku-Integration',
		'editor_enable' => 'Aktivieren',
		'editor_plaintext' => 'Unformatierte Textarea',
		'editor_java' => 'Java-Editor',
		'editor_javascript' => 'JavaScript-Editor (beta)',
		'editor_javascript_information' => 'Der JavaScript-Editor befindet sich derzeit im Beta-Stadium. Je nach aktivierten Funktionen kann es noch zu Fehlern kommen. Die Codevervollst�ndigung funktioniert derzeit nicht im Internet Explorer. F�r eine komplette Liste von bekannten Problemen schauen Sie bitte in den <a href="http://qa.webedition.org/tracker/search.php?project_id=107&sticky_issues=on&sortby=last_updated&dir=DESC&hide_status_id=90" target="_blank">webEdition Bugtracker</a>.',
		'juplod_not_installed' => 'JUpload ist nicht installiert!',
);
