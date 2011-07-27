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

		'file' => "Archivo",
		'new' => "Nuevo",
		##################################
		###### SubMenu Datei/Neu #########
		##################################

		'webEdition_page' => "Página webEdition",
		'empty_page' => "Página vacía",
		'image' => "Imagen",
		'other' => "Otra",
		##################################
		### SubMenu Datei/Neu/Sonstige ###
		##################################

		'html_page' => "Página HTML",
		'flash_movie' => "Película Flash",
		'quicktime_movie' => "Película Quicktime",
		'text_plain' => "Documento de texto simple",
		'text_xml' => "XML Document", // TRANSLATE
		'javascript' => "Javascript", // TRANSLATE
		'css_stylesheet' => "Hoja de estilo en cascada CSS",
		'htaccess' => ".htaccess Document", //TRANSLATE
		'other_files' => "Otros archivos",
		#####################################################
		########## End SubMenu Datei/Neu/Sonstige ###########
		#####################################################

		'template' => "Plantilla",
		##################################
		## SubMenu Datei/Neu/Verzeichnis #
		##################################

		'document_directory' => "Directorio de documentos",
		'template_directory' => "Directorio de plantillas",
		#################################################
		######## End Submenu Datei/Neu/Verzeichnis  #####
		#################################################

		'directory' => "Directorio",
		'wizards' => "Wizards", // TRANSLATE
		##################################
		## SubMenu Datei/Neu/Wizards #####
		##################################

		'first_steps_wizard' => "First Steps Wizard", // TRANSLATE
		############################################
		######## End Submenu Datei/Neu  ############
		############################################

		'open' => "Abrir",
		##################################
		###### SubMenu Datei/Open ########
		##################################
		'open_document' => "Documento",
		'open_template' => "Plantilla",
		##################################
		###### End SubMenu Datei/Open ####
		##################################
		// close
		'close_single_document' => "Close tab", // TRANSLATE
		'close_all_documents' => "Close all tabs", // TRANSLATE
		'close_all_but_active_document' => "Close inactive tabs", // TRANSLATE
		'delete_active_document' => "Delete active document/object", // TRANSLATE



		'save' => "Salvar",
		'publish' => "Publish", // TRANSLATE
		'delete' => "Borrar",
		##################################
		##### SubMenu Datei/Löschen ######
		##################################

		'documents' => "Documentos",
		'templates' => "Plantillas",
		'cache' => "Cache", // TRANSLATE
		##################################
		######## End Submenu  ############
		##################################

		'move' => "Move", // TRANSLATE
		#########################################
		#####   	 Import/export		    #####
		#########################################

		'import_export' => "Importar/Exportar",
		'import' => "Importar",
		'export' => "Exportar",
		#########################################
		#####	    End Import/export	    #####
		#########################################

		'backup' => "Reserva",
		##################################
		### SubMenu Datei/Backup ####
		##################################

		'make_backup' => "Crear Reserva",
		'recover_backup' => "Restaurar Reserva",
		'view_backuplog' => "View Backup-Log", // TRANSLATE
		##################################
		### SubMenu Datei/Backup ####
		##################################

		'rebuild' => "Reconstruir",
		'clear_cache' => "Clear cache", // TRANSLATE

		'browse_server' => "Navegar por el Servidor",
		'quit' => "Finalizar",
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

		'edit' => "Opciones",
		'document_types' => "Tipos de documentos",
		'categories' => "Categorías",
		'thumbnails' => "Imágenes en miniatura",
		'metadata' => "Metadata fields", // TRANSLATE
		'navigation' => "Navigation", // TRANSLATE
		'change_username' => "Cambiar nombre de usuario",
		'change_password' => "Cambiar contraseña",
		'formmail_recipients' => "Destinatarios de formas de correos",
		'proxy_server' => "Servidor Proxy",
		'unpublished_pages' => "Páginas inéditas",
		'preferences' => "Preferencias",
		'versioning' => "Version-Wizard", // TRANSLATE
		'versioning_log' => "Version-Log", // TRANSLATE
		'econda' => "Econda", // TRANSLATE
##################################
###### End Menu Bearbeiten #######
##################################
##############################
###### Menu Module ###########
##############################

		'modules' => "Módulos",
		'module_installation' => "Instalación de Módulos",
//	The content is generated dynamically
		'extras' => "Extras", // TRANSLATE
		'inactive_extras' => "Inactive Extras", // TRANSLATE
#################################
###### End Menu Module ###########
##################################
##################################
######### Menu Hilfe #############
##################################

		'help' => "Ayuda",
		'onlinehelp' => "Ayuda en línea",
		'onlinehelp_documentation' => "Documentación en línea",
		'onlinehelp_forum' => "webEdition Forum",
		'onlinehelp_bugtracker' => "Bug tracker", // TRANSLATE
		'onlinehelp_tagreference' => "Referencias de rótulos",
		'onlinehelp_demo' => "Páginas Demo",
		'onlinehelp_changelog' => "Versión historia",
		'webEdition_online' => "webEdition online", // TRANSLATE
		'sidebar' => "Sidebar", // TRANSLATE
		'update' => "Actualizar",
		'upgrade' => "Update webEdition 5", // TRANSLATE
		'register' => "Registrar",
		'info' => "Info", // TRANSLATE
########################################################
######### Navigation back - forward - home #############
########################################################

		'close' => "Close", // TRANSLATE
		'home' => "Home", // TRANSLATE
		'back' => "Atras",
		'next' => "Reenviar",
		'reload' => "Recargar",
		'not_installed_modules' => "Módulos no instalados",
		'search' => "Search", // TRANSLATE

		'common' => "Common", // TRANSLATE
		'sysinfo' => "System information", // TRANSLATE
		'showerrorlog' => "Errorlog",// TRANSLATE
);