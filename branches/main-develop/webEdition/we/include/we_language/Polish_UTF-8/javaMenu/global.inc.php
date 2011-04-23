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
/**
 * Language file: javaMenu.inc.php
 * Provides language strings.
 * Language: English
 */
/**
 * 	This file contains the text-entries for the java-menu
 * 	Only module names come from file: we_defines.inc.php
 * 	And the doctypes are always the same, come from table
 */
$l_javaMenu_global = array(
##################################
######### Menu Datei #############
##################################

		'file' => "Plik",
		'new' => "Nowy",
		##################################
		###### SubMenu Datei/Neu #########
		##################################

		'webEdition_page' => "Strona webEdition",
		'empty_page' => "Pusta strona",
		'image' => "Grafika",
		'other' => "Pozostałe",
		##################################
		### SubMenu Datei/Neu/Sonstige ###
		##################################

		'html_page' => "Strona HTML",
		'flash_movie' => "Animacja Flash",
		'quicktime_movie' => " Film Quicktime",
		'text_plain' => "Dokument Plain-Tekst",
		'text_xml' => "XML Document", // TRANSLATE
		'javascript' => "Javascript", // TRANSLATE
		'css_stylesheet' => "CSS Stylesheet", // TRANSLATE
		'htaccess' => ".htaccess Document", //TRANSLATE
		'other_files' => "Pozostałe pliki",
		#####################################################
		########## End SubMenu Datei/Neu/Sonstige ###########
		#####################################################

		'template' => "Szablon",
		##################################
		## SubMenu Datei/Neu/Verzeichnis #
		##################################

		'document_directory' => "Katalog dokumentów",
		'template_directory' => "Katalog szablonów",
		#################################################
		######## End Submenu Datei/Neu/Verzeichnis  #####
		#################################################

		'directory' => "Katalog",
		'wizards' => "Wizards", // TRANSLATE
		##################################
		## SubMenu Datei/Neu/Wizards #####
		##################################

		'first_steps_wizard' => "First Steps Wizard", // TRANSLATE
		############################################
		######## End Submenu Datei/Neu  ############
		############################################

		'open' => "Otwórz",
		##################################
		###### SubMenu Datei/Open ########
		##################################
		'open_document' => "Dokument",
		'open_template' => "Szablon",
		##################################
		###### End SubMenu Datei/Open ####
		##################################
		// close
		'close_single_document' => "Close tab", // TRANSLATE
		'close_all_documents' => "Close all tabs", // TRANSLATE
		'close_all_but_active_document' => "Close inactive tabs", // TRANSLATE
		'delete_active_document' => "Delete active document/object", // TRANSLATE



		'save' => "Zapisz",
		'publish' => "Publish", // TRANSLATE
		'delete' => "Kasuj",
		##################################
		##### SubMenu Datei/Löschen ######
		##################################

		'documents' => "Dokumenty",
		'templates' => "Szablony",
		'cache' => "Cache", // TRANSLATE
		##################################
		######## End Submenu  ############
		##################################

		'move' => "Move", // TRANSLATE
		#########################################
		#####   	 Import/export		    #####
		#########################################

		'import_export' => "Import/Export", // TRANSLATE

		'import' => "Import", // TRANSLATE
		'export' => "Export", // TRANSLATE
		#########################################
		#####	    End Import/export	    #####
		#########################################

		'backup' => "Backup", // TRANSLATE
		##################################
		### SubMenu Datei/Backup ####
		##################################

		'make_backup' => "Utwórz backup",
		'recover_backup' => "Przywróć backup",
		'view_backuplog' => "View Backup-Log", // TRANSLATE
		##################################
		### SubMenu Datei/Backup ####
		##################################

		'rebuild' => "Rebuild", // TRANSLATE
		'clear_cache' => "Clear cache", // TRANSLATE

		'browse_server' => "Przeszukaj serwer",
		'quit' => "Zakończ",
		##################################
		### SubMenu Cockpit           ####
		##################################

		'display' => "Display", // TRANSLATE
		'new_widget' => "Add Widget", // TRANSLATE
		###############################################
		### SubMenu Cockpit/New Widget             ####
		###############################################

		'shortcuts' => "Shortcuts", // TRANSLATE
		'rss_reader' => "RSS Reader", // TRANSLATE
		'last_modified' => "last modified",
		'todo_messaging' => "ToDo/Messaging", // TRANSLATE
		'users_online' => "Users Online", // TRANSLATE
		'unpublished' => "unpublished",
		'my_documents' => "My documents", // TRANSLATE
		'notepad' => "Notepad", // TRANSLATE
		'pagelogger' => "pageLogger", // TRANSLATE
		###############################################
		### SubMenu Cockpit/Standard Einstellungen ####
		###############################################

		'default_settings' => "Reset default settings", // TRANSLATE
		##################################
		### End SubMenu Cockpit       ####
		##################################
########################################
######### End / Menu Datei #############
########################################
##################################
###### Menu Bearbeiten ###########
##################################

		'edit' => "Opcje",
		'document_types' => "Typy dokumentów",
		'categories' => "Kategorie",
		'thumbnails' => "Podgląd miniatury",
		'metadata' => "Metadata fields", // TRANSLATE
		'navigation' => "Navigation", // TRANSLATE
		'change_username' => "Zmień nazwę użytkownika",
		'change_password' => "Zmień hasło",
		'formmail_recipients' => "Formmail-Odbiorca",
		'proxy_server' => "Serwer Proxy",
		'unpublished_pages' => "Nieopublikowane strony",
		'preferences' => "Ustawienia",
		'versioning' => "Version-Wizard", // TRANSLATE
		'versioning_log' => "Version-Log", // TRANSLATE
		'econda' => "Econda", // TRANSLATE
##################################
###### End Menu Bearbeiten #######
##################################
##############################
###### Menu Module ###########
##############################

		'modules' => "Moduły",
		'module_installation' => "Instalacja modułów",
//	The content is generated dynamically
		'extras' => "Extras", // TRANSLATE
		'inactive_extras' => "Inactive Extras", // TRANSLATE
#################################
###### End Menu Module ###########
##################################
##################################
######### Menu Hilfe #############
##################################

		'help' => "Pomoc",
		'onlinehelp' => "Pomoc Online",
		'onlinehelp_documentation' => "Online documentation", // TRANSLATE
		'onlinehelp_forum' => "webEdition forums", // TRANSLATE
		'onlinehelp_bugtracker' => "Bug tracker", // TRANSLATE
		'onlinehelp_tagreference' => "Tag reference", // TRANSLATE
		'onlinehelp_demo' => "Online demo", // TRANSLATE
		'onlinehelp_changelog' => "Version history", // TRANSLATE
		'webEdition_online' => "webEdition online", // TRANSLATE
		'sidebar' => "Sidebar", // TRANSLATE
		'update' => "Aktualizacja",
		'upgrade' => "Update webEdition 5", // TRANSLATE
		'register' => "Rejestracja",
		'info' => "Info", // TRANSLATE
########################################################
######### Navigation back - forward - home #############
########################################################

		'close' => "Close", // TRANSLATE
		'home' => "Strona startowa",
		'back' => "Wstecz",
		'next' => "Dalej",
		'reload' => "Odśwież",
		'not_installed_modules' => "Nie zainstalowane moduły",
		'search' => "Search", // TRANSLATE

		'common' => "Common", // TRANSLATE
		'sysinfo' => "System information", // TRANSLATE
		'showerrorlog' => "Errorlog",// TRANSLATE
);