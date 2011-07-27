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

		'file' => "Файл",
		'new' => "Новый",
		##################################
		###### SubMenu Datei/Neu #########
		##################################

		'webEdition_page' => "Страница webEdition",
		'empty_page' => "Новая страница",
		'image' => "Графика",
		'other' => "Другое",
		##################################
		### SubMenu Datei/Neu/Sonstige ###
		##################################

		'html_page' => "Страница HTML",
		'flash_movie' => "Flash Movie", // TRANSLATE
		'quicktime_movie' => "Quicktime Movie", // TRANSLATE
		'text_plain' => "Plain Text Document", // TRANSLATE
		'text_xml' => "XML Document", // TRANSLATE
		'javascript' => "Javascript", // TRANSLATE
		'css_stylesheet' => "CSS таблица стилей",
		'htaccess' => ".htaccess Document", //TRANSLATE
		'other_files' => "Другие файлы",
		#####################################################
		########## End SubMenu Datei/Neu/Sonstige ###########
		#####################################################

		'template' => "Шаблон",
		##################################
		## SubMenu Datei/Neu/Verzeichnis #
		##################################

		'document_directory' => "Директория документов",
		'template_directory' => "Директория шаблонов",
		#################################################
		######## End Submenu Datei/Neu/Verzeichnis  #####
		#################################################

		'directory' => "Директория",
		'wizards' => "Wizards", // TRANSLATE
		##################################
		## SubMenu Datei/Neu/Wizards #####
		##################################

		'first_steps_wizard' => "First Steps Wizard", // TRANSLATE
		############################################
		######## End Submenu Datei/Neu  ############
		############################################

		'open' => "Открыть",
		##################################
		###### SubMenu Datei/Open ########
		##################################
		'open_document' => "Документ",
		'open_template' => "Шаблон",
		##################################
		###### End SubMenu Datei/Open ####
		##################################
		// close
		'close_single_document' => "Close tab", // TRANSLATE
		'close_all_documents' => "Close all tabs", // TRANSLATE
		'close_all_but_active_document' => "Close inactive tabs", // TRANSLATE
		'delete_active_document' => "Delete active document/object", // TRANSLATE



		'save' => "Сохранить",
		'publish' => "Publish", // TRANSLATE
		'delete' => "Удалить",
		##################################
		##### SubMenu Datei/Löschen ######
		##################################

		'documents' => "Документы",
		'templates' => "Шаблоны",
		'cache' => "Cache", // TRANSLATE
		##################################
		######## End Submenu  ############
		##################################

		'move' => "Move", // TRANSLATE
		#########################################
		#####   	 Import/export		    #####
		#########################################

		'import_export' => "Импорт/экспорт",
		'import' => "Import", // TRANSLATE
		'export' => "Export", // TRANSLATE
		#########################################
		#####	    End Import/export	    #####
		#########################################

		'backup' => "Backup", // TRANSLATE
		##################################
		### SubMenu Datei/Backup ####
		##################################

		'make_backup' => "Создать Backup",
		'recover_backup' => "Восстановить Backup",
		'view_backuplog' => "View Backup-Log", // TRANSLATE
		##################################
		### SubMenu Datei/Backup ####
		##################################

		'rebuild' => "Перестроить",
		'clear_cache' => "Clear cache", // TRANSLATE

		'browse_server' => "Поиск по серверу",
		'quit' => "Завершить работу",
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

		'edit' => "Опции",
		'document_types' => "Типы документов",
		'categories' => "Категории",
		'thumbnails' => "Иконки",
		'metadata' => "Metadata fields", // TRANSLATE
		'navigation' => "Navigation", // TRANSLATE
		'change_username' => "Изменить имя пользователя",
		'change_password' => "Изменить пароль",
		'formmail_recipients' => "Получатели Formmail",
		'proxy_server' => "Proxy-сервер",
		'unpublished_pages' => "Неопубликованные страницы",
		'preferences' => "Настройки",
		'versioning' => "Version-Wizard", // TRANSLATE
		'versioning_log' => "Version-Log", // TRANSLATE
		'econda' => "Econda", // TRANSLATE
##################################
###### End Menu Bearbeiten #######
##################################
##############################
###### Menu Module ###########
##############################

		'modules' => "Модули",
		'module_installation' => "Инсталляция модуля",
//	The content is generated dynamically
		'extras' => "Extras", // TRANSLATE
		'inactive_extras' => "Inactive Extras", // TRANSLATE
#################################
###### End Menu Module ###########
##################################
##################################
######### Menu Hilfe #############
##################################

		'help' => "Помощь",
		'onlinehelp' => "Online help", // TRANSLATE
		'onlinehelp_documentation' => "онлайн документация",
		'onlinehelp_forum' => "webEdition forums", // TRANSLATE
		'onlinehelp_bugtracker' => "Bug tracker", // TRANSLATE
		'onlinehelp_tagreference' => "Список тэгов",
		'onlinehelp_demo' => "Демосайты",
		'onlinehelp_changelog' => "История версий",
		'webEdition_online' => "webEdition online", // TRANSLATE
		'sidebar' => "Sidebar", // TRANSLATE
		'update' => "Обновить",
		'upgrade' => "Update webEdition 5", // TRANSLATE
		'register' => "Зарегистрировать",
		'info' => "Справка",
########################################################
######### Navigation back - forward - home #############
########################################################

		'close' => "Close", // TRANSLATE
		'home' => "на главную",
		'back' => "назад",
		'next' => "вперед",
		'reload' => "перезапуск",
		'not_installed_modules' => "Неустановленные модули",
		'search' => "Search", // TRANSLATE

		'common' => "Common", // TRANSLATE
		'sysinfo' => "System information", // TRANSLATE
		'showerrorlog' => "Errorlog",// TRANSLATE
);