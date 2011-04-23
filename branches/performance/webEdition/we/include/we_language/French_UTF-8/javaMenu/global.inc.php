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

		'file' => "Fichier",
		'new' => "Nouveau",
		##################################
		###### SubMenu Datei/Neu #########
		##################################

		'webEdition_page' => "Site webEdition",
		'empty_page' => "Site vide",
		'image' => "Graphique",
		'other' => "Autres",
		##################################
		### SubMenu Datei/Neu/Sonstige ###
		##################################

		'html_page' => "Site HTML",
		'flash_movie' => "Vidéo Flash",
		'quicktime_movie' => "Film de Quicktime",
		'text_plain' => "Document de Texte",
		'text_xml' => "XML Document", // TRANSLATE
		'javascript' => "Javascript", // TRANSLATE
		'css_stylesheet' => "CSS Stylesheet", // TRANSLATE
		'htaccess' => ".htaccess Document", //TRANSLATE
		'other_files' => "Autres Fichiers",
		#####################################################
		########## End SubMenu Datei/Neu/Sonstige ###########
		#####################################################

		'template' => "Modèle",
		##################################
		## SubMenu Datei/Neu/Verzeichnis #
		##################################

		'document_directory' => "Répertoire de documents",
		'template_directory' => "Répertoire de modèles",
		#################################################
		######## End Submenu Datei/Neu/Verzeichnis  #####
		#################################################

		'directory' => "Répertoire",
		'wizards' => "Wizards", // TRANSLATE
		##################################
		## SubMenu Datei/Neu/Wizards #####
		##################################

		'first_steps_wizard' => "First Steps Wizard", // TRANSLATE
		############################################
		######## End Submenu Datei/Neu  ############
		############################################

		'open' => "Ouvrir",
		##################################
		###### SubMenu Datei/Open ########
		##################################
		'open_document' => "Document", // TRANSLATE
		'open_template' => "Modèle",
		##################################
		###### End SubMenu Datei/Open ####
		##################################
		// close
		'close_single_document' => "Close tab", // TRANSLATE
		'close_all_documents' => "Close all tabs", // TRANSLATE
		'close_all_but_active_document' => "Close inactive tabs", // TRANSLATE
		'delete_active_document' => "Delete active document/object", // TRANSLATE



		'save' => "Enregistrer",
		'publish' => "Publish", // TRANSLATE
		'delete' => "Supprimer",
		##################################
		##### SubMenu Datei/Löschen ######
		##################################

		'documents' => "Document",
		'templates' => "Modèle",
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

		'backup' => "Sauvegardes",
		##################################
		### SubMenu Datei/Backup ####
		##################################

		'make_backup' => "Créer une Sauvegarde",
		'recover_backup' => "Restaurer une sauvegarde",
		'view_backuplog' => "View Backup-Log", // TRANSLATE
		##################################
		### SubMenu Datei/Backup ####
		##################################

		'rebuild' => "Rebuild", // TRANSLATE
		'clear_cache' => "Clear cache", // TRANSLATE

		'browse_server' => "Fouiller le server",
		'quit' => "Quitter",
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

		'edit' => "Options", // TRANSLATE
		'document_types' => "Types de documents",
		'categories' => "Catégories",
		'thumbnails' => "Imagettes",
		'metadata' => "Metadata fields", // TRANSLATE
		'navigation' => "Navigation", // TRANSLATE
		'change_username' => "Changer l'identifiant ",
		'change_password' => "Changer le mot de passe",
		'formmail_recipients' => "Destinataire de Formmail",
		'proxy_server' => "Serveur proxy",
		'unpublished_pages' => "Sites non publiés",
		'preferences' => "Préférences",
		'versioning' => "Version-Wizard", // TRANSLATE
		'versioning_log' => "Version-Log", // TRANSLATE
		'econda' => "Econda", // TRANSLATE
##################################
###### End Menu Bearbeiten #######
##################################
##############################
###### Menu Module ###########
##############################

		'modules' => "Modules", // TRANSLATE
		'module_installation' => "Installation de module",
//	The content is generated dynamically
		'extras' => "Extras", // TRANSLATE
		'inactive_extras' => "Inactive Extras", // TRANSLATE
#################################
###### End Menu Module ###########
##################################
##################################
######### Menu Hilfe #############
##################################

		'help' => "Aide",
		'onlinehelp' => "Aide en ligne",
		'onlinehelp_documentation' => "Documentation en ligne",
		'onlinehelp_forum' => "webEdition forums", // TRANSLATE
		'onlinehelp_bugtracker' => "Bug tracker", // TRANSLATE
		'onlinehelp_tagreference' => "Reference d'étiquete",
		'onlinehelp_demo' => "Online demo", // TRANSLATE
		'onlinehelp_changelog' => "Version histoire",
		'webEdition_online' => "webEdition online", // TRANSLATE
		'sidebar' => "Sidebar", // TRANSLATE
		'update' => "Mise à jour",
		'upgrade' => "Update webEdition 5", // TRANSLATE
		'register' => "Enrégistrer",
		'info' => "À propos",
########################################################
######### Navigation back - forward - home #############
########################################################

		'close' => "Close", // TRANSLATE
		'home' => "Page d'accueil",
		'back' => "Reculer",
		'next' => "Avancer",
		'reload' => "Actualiser",
		'not_installed_modules' => "Modules non installés",
		'search' => "Search", // TRANSLATE

		'common' => "Common", // TRANSLATE
		'sysinfo' => "System information", // TRANSLATE
		'showerrorlog' => "Errorlog",// TRANSLATE
);