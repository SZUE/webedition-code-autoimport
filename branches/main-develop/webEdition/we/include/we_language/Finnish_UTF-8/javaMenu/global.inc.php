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

		'file' => "Tiedosto",
		'new' => "Uusi",
		##################################
		###### SubMenu Datei/Neu #########
		##################################

		'webEdition_page' => "webEdition sivu",
		'empty_page' => "Tyhjä sivu",
		'image' => "Kuva",
		'other' => "Muu tiedosto",
		##################################
		### SubMenu Datei/Neu/Sonstige ###
		##################################

		'html_page' => "HTML Sivu",
		'flash_movie' => "Flash Tiedosto",
		'quicktime_movie' => "Quicktime Tiedosto",
		'text_plain' => "Tekstitiedosto",
		'text_xml' => "XML Dokumentti",
		'javascript' => "Javascript -tiedosto",
		'css_stylesheet' => "CSS tyylitiedosto",
		'htaccess' => ".htaccess Document", //TRANSLATE
		'other_files' => "Muut tiedostot",
		#####################################################
		########## End SubMenu Datei/Neu/Sonstige ###########
		#####################################################

		'template' => "Sivupohja",
		##################################
		## SubMenu Datei/Neu/Verzeichnis #
		##################################

		'document_directory' => "Dokumenttihakemisto",
		'template_directory' => "Sivupohjahakemisto",
		#################################################
		######## End Submenu Datei/Neu/Verzeichnis  #####
		#################################################

		'directory' => "Hakemisto",
		'wizards' => "Velhot",
		##################################
		## SubMenu Datei/Neu/Wizards #####
		##################################

		'first_steps_wizard' => "Aloitusvelho",
		############################################
		######## End Submenu Datei/Neu  ############
		############################################

		'open' => "Avaa",
		##################################
		###### SubMenu Datei/Open ########
		##################################
		'open_document' => "Dokumentti",
		'open_template' => "Sivupohja",
		##################################
		###### End SubMenu Datei/Open ####
		##################################
		// close
		'close_single_document' => "Sulje dokumentti",
		'close_all_documents' => "Sulje kaikki dokumentit",
		'close_all_but_active_document' => "Sulje kaikki ei-aktiiviset dokumentit",
		'delete_active_document' => "Poista aktiivinen dokumentti",
		'save' => "Tallenna",
		'publish' => "Julkaise",
		'delete' => "Poista",
		##################################
		##### SubMenu Datei/Löschen ######
		##################################

		'documents' => "Dokumentteja",
		'templates' => "Sivupohjia",
		'cache' => "Välimuisti",
		##################################
		######## End Submenu  ############
		##################################

		'move' => "Siirrä",
		#########################################
		#####   	 Import/export		    #####
		#########################################

		'import_export' => "Tuo/Vie",
		'import' => "Tuo",
		'export' => "Vie",
		#########################################
		#####	    End Import/export	    #####
		#########################################

		'backup' => "Varmuuskopioi",
		##################################
		### SubMenu Datei/Backup ####
		##################################

		'make_backup' => "Luo varmuuskopio",
		'recover_backup' => "Palauta varmuuskopiosta",
		'view_backuplog' => "View Backup-Log", // TRANSLATE
		##################################
		### SubMenu Datei/Backup ####
		##################################

		'rebuild' => "Rakenna uudelleen",
		'clear_cache' => "Tyhjennä välimuisti",
		'browse_server' => "Selaa palvelinta",
		'quit' => "Poistu",
		##################################
		### SubMenu Cockpit           ####
		##################################

		'display' => "Näytä",
		'new_widget' => "Uusi vimpain",
		###############################################
		### SubMenu Cockpit/New Widget             ####
		###############################################

		'shortcuts' => "Oikopolut",
		'rss_reader' => "RSS lukija",
		'last_modified' => "viimeksi muokattu",
		'todo_messaging' => "Tehtävät/Viestintä",
		'users_online' => "Käyttäjiä järjestelmässä",
		'unpublished' => "julkaisematon",
		'my_documents' => "Omat dokumentit",
		'notepad' => "Muistio",
		'pagelogger' => "pageLogger",
		###############################################
		### SubMenu Cockpit/Standard Einstellungen ####
		###############################################

		'default_settings' => "Palauta vakioasetukset",
		##################################
		### End SubMenu Cockpit       ####
		##################################
########################################
######### End / Menu Datei #############
########################################
##################################
###### Menu Bearbeiten ###########
##################################

		'edit' => "Muokkaa",
		'document_types' => "Dokumenttityypit",
		'categories' => "Kategoriat",
		'thumbnails' => "Esikatselukuvat",
		'metadata' => "Metatietokentät",
		'navigation' => "Navigaatio",
		'change_username' => "Vaihda käyttäjänimeä",
		'change_password' => "Vaihda salasanaa",
		'formmail_recipients' => "Formmail vastaanottajat",
		'proxy_server' => "Proxy-palvelin",
		'unpublished_pages' => "Julkaisemattomat sivut",
		'preferences' => "Asetukset",
		'versioning' => "Versio-Velho",
		'versioning_log' => "Versiologi",
		'econda' => "Econda",
##################################
###### End Menu Bearbeiten #######
##################################
##############################
###### Menu Module ###########
##############################

		'modules' => "Moduulit",
		'module_installation' => "Moduulien asennus",
//	The content is generated dynamically
		'extras' => "Extrat",
		'inactive_extras' => "Toimettomat extrat",
#################################
###### End Menu Module ###########
##################################
##################################
######### Menu Hilfe #############
##################################

		'help' => "Ohje",
		'onlinehelp' => "Online ohje",
		'onlinehelp_documentation' => "Onlinedokumentaatio",
		'onlinehelp_forum' => "Keskustelufoorumi",
		'onlinehelp_bugtracker' => "Bug tracker",
		'onlinehelp_tagreference' => "Tagi hakemisto",
		'onlinehelp_demo' => "Demosivut",
		'onlinehelp_changelog' => "Versiohistoria",
		'webEdition_online' => "webEdition online",
		'sidebar' => "Sivupalkki",
		'update' => "Päivitä",
		'upgrade' => "Päivitä webEdition 5:n",
		'register' => "Rekisteröi",
		'info' => "Tietoja",
########################################################
######### Navigation back - forward - home #############
########################################################

		'close' => "Sulje",
		'home' => "Aloitus",
		'back' => "Takaisin",
		'next' => "Eteenpäin",
		'reload' => "Lataa uudelleen",
		'not_installed_modules' => "Asentamattomat moduulit",
		'search' => "Etsi",
		'common' => "Yleiset",
		'sysinfo' => "Järjestelmätiedot",
		'showerrorlog' => "Errorlog",// TRANSLATE
);