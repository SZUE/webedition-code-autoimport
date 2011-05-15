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

		'tab_ui' => "Oberfläche",
		'tab_glossary' => "Glossar",
		'tab_extensions' => "Dateierweiterungen",
		'tab_editor' => 'Editor',
		'tab_formmail' => 'Formmail',
		'formmail_recipients' => 'Formmail Empfänger',
		'tab_proxy' => 'Proxy Server',
		'tab_advanced' => 'Erweitert',
		'tab_system' => 'System',
		'tab_seolinks' => 'SEO-Links',
		'tab_error_handling' => 'Fehlerbehandlung',
		'tab_cockpit' => 'Cockpit',
		'tab_cache' => 'Cache',
		'tab_language' => 'Sprachen',
		'tab_countries' => 'Länder',
		'tab_modules' => 'Module',
		'tab_versions' => 'Versionierung',
		/*		 * ***************************************************************************
		 * USER INTERFACE
		 * *************************************************************************** */
		/**
		 * Countries
		 */
		'countries_information' => "Wählen Sie hier die Länder aus, die in der Kundenverwaltung, im Shop usw. ausgewählt werden können.",
		'countries_headline' => "Länderauswahl",
		'countries_country' => "Land",
		'countries_top' => "Top-Liste",
		'countries_show' => "anzeigen",
		'countries_noshow' => "keine Anzeige",
		/**
		 * LANGUAGE
		 */
		'choose_language' => "Sprache",
		'language_notice' => "Die Sprachumstellung wird erst nach einem Neustart von webEdition an allen Stellen durchgeführt.",
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
		'question_change_to_seem_start' => "Möchten Sie zum ausgewählten Dokument wechseln?",
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
		'dimension' => "Fenstergröße",
		'maximize' => "Maximieren",
		'specify' => "Spezifizieren",
		'width' => "Breite",
		'height' => "Höhe",
		'predefined' => "Voreingestellte Größen",
		'show_predefined' => "Voreingestellte Größen anzeigen",
		'hide_predefined' => "Voreingestellte Größen ausblenden",
		/**
		 * TREE
		 */
		'tree_title' => "Baummenü",
		'all' => "Alle",
		/*		 * ***************************************************************************
		 * FILE EXTENSIONS
		 * *************************************************************************** */

		/**
		 * FILE EXTENSIONS
		 */
		'extensions_information' => "Hier werden die standardmäßig verwendeten Datei-Erweiterungen für statische und dynamische Seiten festgelegt.",
		'we_extensions' => "webEdition-Erweiterungen",
		'static' => "Statische Seiten",
		'dynamic' => "Dynamische Seiten",
		'html_extensions' => "HTML-Erweiterungen",
		'html' => "HTML-Dateien",
		/*		 * ***************************************************************************
		 * Glossary
		 * *************************************************************************** */

		'glossary_publishing' => "Prüfen bei Veröffentlichung",
		'force_glossary_check' => "Glossarprüfung erzwingen",
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
		'cache_information' => "Stellen Sie hier die Werte ein, mit welchen die Felder \"Art des Caches\" und \"Cache Gültigkeit in Sekunden\" bei neuen Vorlagen belegt sein sollen.<br /><br />Beachten Sie bitte, dass diese Einstellung lediglich eine Vorbelegung der Felder ist.",
		'cache_navigation_information' => "Tragen Sie hier die Standardwerte für den Tag &lt;we:navigation&gt; ein. Dieser Wert kann durch das setzen des Attributes cachelifetime im Tag &lt;we:navigation&gt; überschrieben werden.",
		'cache_presettings' => "Voreinstellung",
		'cache_type' => "Art des Caches",
		'cache_type_none' => "Caching deaktiviert",
		'cache_type_full' => "Full Cache",
		'cache_type_document' => "Dokument Cache",
		'cache_type_wetag' => "we:Tag Cache",
		'delete_cache_after' => 'Cache der Navigation löschen',
		'delete_cache_add' => 'nach Anlegen eines neuen Eintrages',
		'delete_cache_edit' => 'nach Ändern eines Eintrages',
		'delete_cache_delete' => 'nach Löschen eines Eintrages',
		'cache_navigation' => 'Standardeinstellung',
		'default_cache_lifetime' => 'Standard Cache Gültigkeit',
		/**
		 * Cache Life Time
		 */
		'cache_lifetime' => "Cache Gültigkeit in Sekunden",
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
		'locale_information' => "Fügen Sie hier alle Sprachen (Locales) hinzu, für welche Sie eine Webseite mit webEdition erstellen möchten.<br />Sie können dann das Locale den einzelnen Dokumenten/Objekten und Verzeichnissen zuweisen.<br />Diese Einstellung wird für das Glossar und die Rechtschreibprüfung einzelner Dokumente verwendet, steht aber auch z.B. für listviews als Selektionskriterium zur Verfügung",
		'locale_languages' => "Sprache",
		'locale_countries' => "Land",
		'locale_add' => "Sprache hinzufügen",
		'cannot_delete_default_language' => "Die Standardsprache kann nicht gelöscht werden.",
		'language_already_exists' => "Diese Sprache wurde bereits angelegt.",
		'language_country_missing' => "Bitte wählen Sie auch ein Land aus",
		'add_dictionary_question' => "Möchten Sie gleich das Wörterbuch für diese Sprache hinzufügen?",
		'langlink_headline' => "Unterstützung für die Verlinkung zwischen verschiedenen Sprachen",
		'langlink_information' => "Mit dieser Option können Sie im Backend die verschiedenen korrespondierenden Sprachversionen eines Dokumentes/Objektes verwalten und diese Dokumente zuweisen, aufrufen usw.<br/>Eine Ausgabe im Frontend erfolgt dann über eine listview type=languagelink.<br/><br/>Für Verzeichnisse kann dann ein <b>Dokument</b> in der jeweiligen Sprache gewählt werden, auf das zurückgegriffen wird, wenn einzelnen Dokumenten im Verzeichnis selbst kein korrespondierendes Sprachdokument zugewiesen wurde.",
		'langlink_support' => "Aktiviert",
		'langlink_support_backlinks' => "Erzeuge automatisch die Rücklinks",
		'langlink_support_backlinks_information' => "Rücklinks können für Dokumente (nicht Verzeichnisse!) automatisch generiert werden. Dabei sollte das verlinkte Dokument nicht in einem Editor-Tab geöffnet sein!",
		'langlink_support_recursive' => "Erzeuge die Sprachenlinks rekursiv",
		'langlink_support_recursive_information' => "Recursives Setzen der Sprachlinks generiert für Dokumente (nicht Verzeichnisse!) alle verfügbaren Links und versucht den Sprachenkreis schnellstmöglich zu schließen. Dabei sollten die verlinkten Dokument nicht in einem Editor-Tab geöffnet sein!",
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
		'change_only_in_ie' => 'Da das Editor PlugIn nur unter Windows im Internet Explorer, Mozilla, Firebird sowie Firefox funktioniert, sind diese Einstellungen nicht veränderbar.',
		'install_plugin' => 'Um das Editor PlugIn in Ihrem Browser benutzen zu können, muss das Mozilla ActiveX PlugIn installiert werden.',
		'confirm_install_plugin' => 'Das Mozilla ActiveX PlugIn ermöglicht es, ActiveX Controls in Mozilla Browser zu integrieren. Nach der Installation muss der Browser neu gestartet werden.\\n\\nBeachten Sie: ActiveX kann ein Sicherheitsrisiko darstellen!\\n\\nMit der Installation fortfahren?',
		'install_editor_plugin' => 'Um das webEdition Editor PlugIn in Ihrem Browser benutzen zu können, muss es installiert werden.',
		'install_editor_plugin_text' => 'Das webEdition Editor PlugIn wird installiert...',
		/**
		 * TEMPLATE EDITOR
		 */
		'editor_information' => "Geben Sie hier Schriftart und Größe an, die für die Bearbeitung der Vorlagen, CSS- und JavaScript-Dateien innerhalb von webEdition verwendet werden soll.<br /><br />Diese Einstellungen werden für den Texteditor der obengenannten Dateitypen verwendet.",
		'editor_mode' => 'Editor',
		'editor_font' => 'Schrift im Editor',
		'editor_fontname' => 'Schriftart',
		'editor_fontsize' => 'Größe',
		/*		 * ***************************************************************************
		 * FORMMAIL
		 * *************************************************************************** */

		/**
		 * FORMMAIL RECIPIENTS
		 */
		'formmail_information' => "Tragen Sie hier alle E-Mail-Adressen ein, an welche Formulare mit der Formmail-Funktion (&lt;we:form type=\"formmail\" ..&gt;) geschickt werden dürfen.<br><br>Ist hier keine E-Mail-Adresse eingetragen, kann man keine Formulare mit der Formmail-Funktion verschicken!",
		'formmail_log' => "Formmail-Logbuch",
		'log_is_empty' => "Das Logbuch ist leer!",
		'ip_address' => "IP Adresse",
		'blocked_until' => "Geblockt bis",
		'unblock' => "freigeben",
		'clear_log_question' => "Möchten Sie das Logbuch wirklich leeren?",
		'clear_block_entry_question' => "Möchten Sie die IP %s wirklich freigeben?",
		'forever' => "Für immer",
		'yes' => "ja",
		'no' => "nein",
		'on' => "ein",
		'off' => "aus",
		'formmailConfirm' => "Formmail Bestätigungsfunktion",
		'logFormmailRequests' => "Formmail Anfragen protokollieren",
		'deleteEntriesOlder' => "Einträge löschen die älter sind als",
		'formmailViaWeDoc' => "Formmail über webEdition-Dokument aufrufen",
		'blockFormmail' => "Formmail Anfragen begrenzen",
		'formmailSpan' => "Innerhalb der Zeitspanne",
		'formmailTrials' => "Erlaubte Anfragen",
		'blockFor' => "Blockieren für",
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
		'proxy_information' => "Hier nehmen Sie die Einstellungen für den Proxy Server vor, falls Ihr Server einen Proxy für die Verbindung mit dem Internet verwendet.",
		'useproxy' => "Proxy Server für Live-Update<br>verwenden",
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
		'default_php_setting' => "Standard Einstellung für<br><em>php</em>-Attribut in we:tags",
		/**
		 * INLINEEDIT
		 */
		'inlineedit_default' => "Standard Einstellung für<br><em>inlineedit</em>-Attribut in<br>&lt;we:textarea&gt;",
		'removefirstparagraph_default' => "Standard Einstellung für<br><em>removefirstparagraph</em>-Attribut in<br>&lt;we:textarea&gt;",
		'hidenameattribinweimg_default' => "Keine Ausgabe von name=xyz in we:img (HTML 5)",
		'hidenameattribinweform_default' => "Keine Ausgabe von name=xyz in we:form (XHTML strict)",
		/**
		 * SAFARI WYSIWYG
		 */
		'safari_wysiwyg' => "Safari Wysiwyg Editor<br>(Betaversion) benutzen",
		/**
		 * SHOWINPUTS
		 */
		'showinputs_default' => "Standard Einstellung für<br><em>showinputs</em>-Attribut in<br>&lt;we:img&gt;",
		/**
		 * NAVIGATION
		 */
		'navigation_entries_from_document' => "Erzeuge neue Navigations-Einträge aus dem Dokument als",
		'navigation_entries_from_document_item' => "Eintrag",
		'navigation_entries_from_document_folder' => "Ordner",
		'navigation_rules_continue' => "Werte Navigationsregeln auch nach einem ersten Match aus",
		'general_directoryindex_hide' => "Verstecke DirectoryIndex-Dateinamen in der Ausgabe",
		'general_directoryindex_hide_description' => "Für die Tags <we:link>, <we:linklist>, <we:listview> kann das Attribut 'hidedirindex' verwendet werden",
		'navigation_directoryindex_hide' => "der Navigation",
		'wysiwyglinks_directoryindex_hide' => "von Links aus dem WYSIWYG-Editor",
		'objectlinks_directoryindex_hide' => "von Links auf Objekte",
		'navigation_directoryindex_description' => "Nach einer Änderung muss ein Rebuild durchgeführt werden (z.B. Navigation Cache, Objekte usw.)",
		'navigation_directoryindex_names' => "DirectoryIndex-Dateinamen (Komma-separiert, einschl. Datei-Extensions, z.B. 'index.php,index.html')",
		'general_objectseourls' => "Erzeuge Objekt SEO-Urls ",
		'navigation_objectseourls' => "in der Navigation",
		'wysiwyglinks_objectseourls' => "bei Links aus dem WYSIWYG-Editor",
		'general_objectseourls_description' => "Für die Tags <we:link>, <we:linklist>, <we:listview>, <we:object> kann das Attribut 'objectseourls' verwendet, oder die folgende Voreinstellung vorgenommen werden:",
		'taglinks_directoryindex_hide' => "Voreinstellung für Tags",
		'taglinks_objectseourls' => "Voreinstellung für Tags",
		'urlencode_objectseourls' => "URLencode die SEO-Links",
		'suppress404code' => "unterdrücke 404 not found",
		'general_seoinside' => "Darstellung innerhalb von webEdition ",
		'general_seoinside_description' => "Werden DirectoryIndex-Dateinamen und Objekt SEO-Urls innerhalb von webEdition dargestellt, so kann webEdition diese nicht mehr als interne Links erkennen und ein Klick auf den Link öffnet nicht mehr das Bearbeitungsfenster. Mit den folgenden Optionen können beide (bis auf die Navigation) im Editmode und der Vorschau daher unterdrückt werden.",
		'seoinside_hideinwebedition' => "In der Vorschau verstecken",
		'seoinside_hideineditmode' => "Im Editmode verstecken",
		'navigation' => "Navigation",
		/**
		 * DATABASE
		 */
		'db_connect' => "Art der Datenbank-<br>verbindungen",
		'db_set_charset' => "Verbindungszeichensatz",
		'db_set_charset_information' => "Der Verbindungszeichensatz wird für die Kommunikation zwischen webEdition und Datenbank genutzt.<br/>Ist kein Wert gesetzt, so wird der Standard-Verbindungszeichensatz von PHP verwendet.<br/>Im Ideal sollten webEdition Spache (z. B. Deutsch_UTF-8), Kollation der Datenbank (z. B. utf8_general_ci), Verbindungszeichensatz (z. B. utf8) und die Einstellung externer Tools wie phpMyAdmin (z. B. utf-8) übereinstimmen, damit mit diesen externen Tools ein Editieren von Datenbankwerten möglich ist.",
		'db_set_charset_warning' => "Der Verbindungszeichensatz sollte nur bei einer frischen Installation von webEdition (ohne Daten in der Datenbank) ein- bzw. umgestellt werden, da sonst alle nicht ASCII-Zeichen falsch interpretiert und gegebenenfalls zerstört werden.",
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
		'thumbnail_dir' => "Verzeichnis für Miniaturansichten",
		/**
		 * PAGELOGGER DIR
		 */
		'pagelogger_dir' => "pageLogger-Verzeichnis",
		/**
		 * HOOKS
		 */
		'hooks' => "Hooks",
		'hooks_information' => "Die Verwendung von Hooks ermöglicht die Ausführung von beliebigem PHP-Code während dem Speichern, Parken, Veröffentlichen und Löschen jeglicher Inhaltstypen in webEdition.<br/>
	Nähere Infos finden Sie in der Online-Dokumentation.<br/><br/>Möchten Sie die Ausführung von Hooks zulassen?",
		/**
		 * Backward compatibility
		 */
		'backwardcompatibility' => "Abwärtskompatibilität",
		'backwardcompatibility_tagloading' => "Lade alle 'alten' we_tag Funktionen",
		'backwardcompatibility_tagloading_message' => "Wird nur benötigt, wenn in custom_tags oder Vorlagen we_tags in der Form we_tag_tagname() aufgerufen werden.<br/> Empfohlen wird ein Aufruf in der Form we_tag<br/>('tagname',&#36;attribs,&#36;content)",
		/*		 * ***************************************************************************
		 * ERROR HANDLING
		 * *************************************************************************** */

		'error_no_object_found' => 'Fehlerseite für nicht existierende Objekte',
		/**
		 * TEMPLATE TAG CHECK
		 */
		'templates' => "Vorlagen",
		'disable_template_tag_check' => "Prüfung auf fehlende,<br />schließende we:tags deaktivieren",
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
		'error_notices_warning' => 'Option für Entwickler! Nicht auf Live-System aktivieren.',
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
		'debug_restart' => "Änderungen erfordern einen Neustart",
		/*		 * ***************************************************************************
		 * MODULES
		 * *************************************************************************** */

		/**
		 * OBJECT MODULE
		 */
		'module_object' => "DB/Objekt Modul",
		'tree_count' => "Anzahl anzuzeigender Objekte",
		'tree_count_description' => "Dieser Wert gibt die maximale Anzahl anzuzeigender Einträge in der linken Navigation an.",
		/*		 * ***************************************************************************
		 * BACKUP
		 * *************************************************************************** */
		'backup' => "Backup",
		'backup_slow' => "Langsam",
		'backup_fast' => "Schnell",
		'performance' => "Stellen Sie hier ein passendes Leistungslevel ein. Dieses richtet sich nach der Leistungsfähigkeit Ihres Servers. Wenn die Ressourcen Ihres Systemes eingeschränkt sind (Speicher, Timeout etc.) wählen Sie bitte eine niedrigere Einstellung.",
		'backup_auto' => "Auto",
		/*		 * ***************************************************************************
		 * Validation
		 * *************************************************************************** */
		'validation' => 'Validierung',
		'xhtml_default' => 'Standardeinstellung für das <em>xml</em>-Attribut in we:Tags',
		'xhtml_debug_explanation' => 'Das XHTML-Debugging unterstützt Sie bei der Erstellung valider Websites. Optional kann jede Ausgabe eines we:Tags auf Gültigkeit kontrolliert werden und bei Bedarf fehlerhafte Attribute entfernt, bzw. angezeigt werden. Bitte beachten Sie, dass dieser Vorgang etwas Zeit erfordert und nur während der Erstellung einer neuen Website benutzt werden sollte.',
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
		'we_max_upload_size' => "Maximale Upload Größe<br>in Hinweistexten",
		'we_max_upload_size_hint' => "(in MByte, 0=automatisch)",
		/*		 * ***************************************************************************
		 * we_new_folder_mod
		 * *************************************************************************** */
		'we_new_folder_mod' => "Zugriffsrechte für<br>neue Verzeichnisse",
		'we_new_folder_mod_hint' => "(Standard ist 755)",
		/*		 * ***************************************************************************
		 * we_doctype_workspace_behavior
		 * *************************************************************************** */

		'we_doctype_workspace_behavior_hint0' => "Das Standardverzeichnis eines Dokument-Typs muß sich innerhalb des Arbeitsbereich des Benutzers befinden, damit der Benutzer den Dokument-Typ auswählen kann.",
		'we_doctype_workspace_behavior_hint1' => "Der Arbeitsbereich des Benutzers muß sich innerhalb des im Dokument-Typ eingestellten Standardverzeichnis befinden, damit der Benutzer den Dokument-Typ auswählen kann.",
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
				'information' => "Über die jeweiligen, nachfolgenden Checkboxen können Sie entscheiden, ob Sie bei den webEdition Aktionen wie z. B. Speichern, Veröffentlichen, Löschen usw. einen Hinweis erhalten möchten.",
				'headline' => "Benachrichtigungen",
				'show_notices' => "Hinweise anzeigen",
				'show_warnings' => "Warnungen anzeigen",
				'show_errors' => "Fehler anzeigen",
		),
		/*		 * ***************************************************************************
		 * Module Activation
		 * *************************************************************************** */
		'module_activation' => array(
				'information' => "Hier können Sie die Module aktivieren bzw. deaktivieren wenn Sie diese nicht benötigen.<br />Nicht aktivierte Module verbessern die Performance von webEdition.<br />Gegebenenfalls müssen Sie webEdition neu starten, um Module zu aktivieren.<br />Das Shop-Modul benötigt das Kundenverwaltungs-Modul, das Workflow-Modul benötigt das ToDo-Messaging-Modul.",
				'headline' => "Modulaktivierung",
		),
		/*		 * ***************************************************************************
		 * Email settings
		 * *************************************************************************** */

		'mailer_information' => "Hier können Sie einstellen, ob für die von webEdition versendeten E-Mails die in PHP integrierte mail()-Funktion oder ein SMTP-Server verwendet werden soll.<br /><br />Durch die Verwendung des \"richtigen\" Mailservers sinkt das Risiko, dass Mails beim Empfänger als \"Spam\" eingestuft werden.",
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
		'smtp_encryption' => "Verschlüsselte Übertragung",
		'smtp_encryption_none' => "nein",
		'smtp_encryption_ssl' => "SSL",
		'smtp_encryption_tls' => "TLS",
		/*		 * ***************************************************************************
		 * Versions settings
		 * *************************************************************************** */

		'versioning' => "Versionierung",
		'version_all' => "alle",
		'versioning_activate_text' => "Aktivieren sie hier die Versionierung entweder für alle oder nur bestimmte Inhaltstypen.",
		'versioning_time_text' => "Bei Angabe eines Zeitraums werden nur Versionen gespeichert, deren Erstellungsdatum sich innerhalb des angegebenen Zeitraums bis heute befindet. Ältere Versionen werden gelöscht.",
		'versioning_time' => "Zeitraum",
		'versioning_anzahl_text' => "Geben Sie hier eine Anzahl von Versionen an, die für jedes Dokument bzw. Objekt gespeichert werden sollen. ",
		'versioning_anzahl' => "Anzahl",
		'versioning_wizard_text' => "Öffnen Sie den Versions-Wizard um Versionen zu löschen oder ältere Versionen wiederherzustellen.",
		'versioning_wizard' => "Versions-Wizard öffnen",
		'ContentType' => "Inhaltstyp",
		'versioning_create_text' => "Legen Sie fest, bei welchen Aktionen Versionen erzeugt werden sollen. Entweder nur beim Veröffentlichen oder auch beim Speichern, Parken, Löschen oder Importieren.",
		'versioning_create' => "Version erstellen",
		'versions_create_publishing' => "nur beim Veröffentlichen",
		'versions_create_always' => "immer",
		'versioning_templates_text' => "Legen Sie hier spezielle Werte für die <b>Versionierung von Vorlagen</b> fest",
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
		'editor_completion' => 'Codevervollständigung',
		'editor_tooltips' => 'Tooltips auf we:tags',
		'editor_docuclick' => 'Doku-Integration',
		'editor_enable' => 'Aktivieren',
		'editor_plaintext' => 'Unformatierte Textarea',
		'editor_java' => 'Java-Editor',
		'editor_javascript' => 'JavaScript-Editor (beta)',
		'editor_javascript_information' => 'Der JavaScript-Editor befindet sich derzeit im Beta-Stadium. Je nach aktivierten Funktionen kann es noch zu Fehlern kommen. Die Codevervollständigung funktioniert derzeit nicht im Internet Explorer. Für eine komplette Liste von bekannten Problemen schauen Sie bitte in den <a href="http://qa.webedition.org/tracker/search.php?project_id=107&sticky_issues=on&sortby=last_updated&dir=DESC&hide_status_id=90" target="_blank">webEdition Bugtracker</a>.',
		'juplod_not_installed' => 'JUpload ist nicht installiert!',
);
