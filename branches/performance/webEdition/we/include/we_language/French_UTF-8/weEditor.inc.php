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
 * Language file: we_editor.inc.php
 * Provides language strings.
 * Language: English
 */
$l__tmp = array(
		'filename_empty' => "Vous n'avez pas encore saisi un nom pour le fichier!",
		'we_filename_notValid' => "Le nom saisi pour le fichier n'est pas valide!\\nSignes permis sont les lettres de a à z (majuscule- ou minuscule) , nombres, soulignage (_), signe moins (-), point (.)",
		'we_filename_notAllowed' => "Le nom du fichier n'est pas permis!",
		'response_save_noperms_to_create_folders' => "Le fichier n'a pas pu être enregistré, parce que vous n'avez pas les droits nécessaires, pour créer des nouveaux répertoire (%s)!",
);
$l_weEditor = array(
		'doubble_field_alert' => "The field '%s' already exists! A field name must be unique!", // TRANSLATE
		'variantNameInvalid' => "The name of an article variant can not be empty!", // TRANSLATE

		'folder_save_nok_parent_same' => "Le répertoire-parent est dans le répertoire actuel! S'il vous plaît choisissez un autre répertoire et essayez de nouveau!",
		'pfolder_notsave' => "Le répertoire ne peut pas être enregistré dans le répertoir choisi!",
		'required_field_alert' => "Le champ '%s' est obligatoire et doit être rempli!",
		'category' => array(
				'response_save_ok' => "La catégorie '%s' a été enregistré avec succès!",
				'response_save_notok' => "Erreur en enregistrant la catégorie '%s'!",
				'response_path_exists' => "La catégorie '%s' n'a pas pu être enregistré, parce qu'il existe déjà une catégorie à cet endroit!",
				'we_filename_notValid' => "Le nom saisi n'est pas valid!\\nPermit sont tous les signes sauf \\\", ' / < > et \\\\",
				'filename_empty' => "The file name cannot be empty.", // TRANSLATE
				'name_komma' => "Le nom saisi n'est pas valid!\\nDes virgule ne sont pas permit",
		),
		'text/webedition' => array_merge($l__tmp, array(
				'response_save_ok' => "Le site-webEdition '%s' a été enregistré avec succès!",
				'response_publish_ok' => "Le site-webEdition '%s' a été publié avec succès!",
				'response_publish_notok' => "Erreur en publiant le site-webEdition '%s'!",
				'response_unpublish_ok' => "Le site-webEdition '%s' a été depublié avec succès!",
				'response_unpublish_notok' => "Erreur en depubliant le site-webEdition '%s'!",
				'response_not_published' => "Le site-webEdition '%s' n'est pas publié!",
				'response_save_notok' => "Erreur en enregistrant le site-webEdition '%s'!",
				'response_path_exists' => "Le site-webEdition '%s' ne pouvait pas être enregistré, parce qu'il existe déjà un autre fichier ou un autre répertoire a cet endroit!",
				'autoschedule' => "Le site-webEdition sera publié automatiquement le %s!",
		)),
		'text/html' => array_merge($l__tmp, array(
				'response_save_ok' => "Le site-HTML '%s' a été enregistré avec succès!",
				'response_publish_ok' => "Le site-HTML '%s' a été publié avec succès!",
				'response_publish_notok' => "Erreur en publiant le site-HTML '%s'!",
				'response_unpublish_ok' => "Le site-HTML '%s' a été depublié avec succès!",
				'response_unpublish_notok' => "Erreur en depubliant le site-HTML '%s'!",
				'response_not_published' => "Le site-HTML '%s' n'est pas publié!",
				'response_save_notok' => "Erreur en enregistrant le site-HTML '%s'!",
				'response_path_exists' => "Le site-HTML '%s' n'a pas pu être enregistré, parce qu'il existe déjà un autre fichier ou un autre répertoire a cet endroit!",
				'autoschedule' => "The HTML page will be published automatically on %s.",
		)),
		'text/weTmpl' => array_merge($l__tmp, array(
				'response_save_ok' => "Le modèle '%s' a été enregistré avec succès!",
				'response_publish_ok' => "Le modèle '%s' a été publié avec succès!",
				'response_unpublish_ok' => "Le modèle '%s' a été depublié avec succès!",
				'response_save_notok' => "Erreur en enregistrant le modèle '%s'!",
				'response_path_exists' => "Le modèle '%s' n'a pas pu être enregistré, parce qu'il existe déjà un autre fichier ou un autre répertoire a cet endroit!",
				'no_template_save' => "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.",
		)),
		'text/css' => array_merge($l__tmp, array(
				'response_save_ok' => "Le feuille de style CSS '%s' a été enregistré avec succès!",
				'response_publish_ok' => "Le feuille de style CSS '%s' a été publié avec succès!",
				'response_unpublish_ok' => "Le feuille de style CSS '%s' a été depublié avec succès!",
				'response_save_notok' => "Erreur en enregistrant le feuille de style CSS '%s'!",
				'response_path_exists' => "Le feuille de style CSS '%s' ne pouvait pas être enregistré, parce qu'il existe déjà un autre fichier ou un autre répertoire a cet endroit!",
		)),
		'text/js' => array_merge($l__tmp, array(
				'response_save_ok' => "The JavaScript '%s' has been successfully saved!",
				'response_publish_ok' => "Le Javascript '%s' a été publié avec succès!",
				'response_unpublish_ok' => "Le Javascript '%s' a été depublié avec succès!",
				'response_save_notok' => "Erreur en enregistrant le Javascripts '%s'!",
				'response_path_exists' => "Le Javascript '%s' n'a pas pu être enregistré, parce qu'il existe déjà un autre fichier ou un autre répertoire a cet endroit!!",
		)),
		'text/plain' => array_merge($l__tmp, array(
				'response_save_ok' => "The text file '%s' has been successfully saved!",
				'response_publish_ok' => "Le fichier texte '%s' a été publié avec succès!",
				'response_unpublish_ok' => "Le fichier texte '%s' a été depublié avec succès!",
				'response_save_notok' => "Erreur en enregistrant le fichier texte '%s'!",
				'response_path_exists' => "Le fichier texte '%s' n'a pas pu être enregistré, parce qu'il existe déjà un autre fichier ou un autre répertoire a cet endroit!",
		)),
		'text/htaccess' => array_merge($l__tmp, array(
				'response_save_ok' => "The file '%s' has been successfully saved!", //TRANSLATE
				'response_publish_ok' => "The file '%s' has been successfully published!", //TRANSLATE
				'response_unpublish_ok' => "The file '%s' has been successfully unpublished!", //TRANSLATE
				'response_save_notok' => "Error while saving the file '%s'!", //TRANSLATE
				'response_path_exists' => "The file '%s' could not be saved because another document or directory is positioned at the same location!", //TRANSLATE
		)),
		'text/xml' => array_merge($l__tmp, array(
				'response_save_ok' => "The XML file '%s' has been successfully saved!",
				'response_publish_ok' => "The XML file '%s' has been successfully published!", // TRANSLATE
				'response_unpublish_ok' => "The XML file '%s' has been successfully unpublished!", // TRANSLATE
				'response_save_notok' => "Error while saving XML file '%s'!", // TRANSLATE
				'response_path_exists' => "The XML file '%s' could not be saved because another document or directory is positioned at the same location!", // TRANSLATE
		)),
		'folder' => array(
				'response_save_ok' => "The directory '%s' has been successfully saved!",
				'response_publish_ok' => "Le répertoire '%s' a été publié avec succès!",
				'response_unpublish_ok' => "Le répertoire '%s' a été depublié avec succès!",
				'response_save_notok' => "Erreur en enregistrant le répertoire '%s'!",
				'response_path_exists' => "Le répertoire '%s' n'a pas pu être enregistré, parce qu'il existe déjà un autre fichier ou un autre répertoire a cet endroit!",
				'filename_empty' => "Vous n'avez pas encore saisi un nom pour le répertoire!",
				'we_filename_notValid' => "Le nom saisi pour le répertoire n'est pas valide!\\nSignes permis sont les lettres de a à z (majuscule- ou minuscule) , nombres, soulignage (_), signe moins (-), point (.)",
				'we_filename_notAllowed' => "Le nom du répertoire n'est pas permis!",
				'response_save_noperms_to_create_folders' => "Le répertoire n'a pas pu être enregistré, parce que vous n'avez pas les droits nécessaires, pour créer des nouveaux répertoire (%s)!",
		),
		'image/*' => array_merge($l__tmp, array(
				'response_save_ok' => "La graphique '%s' a été enregistrée avec succès!",
				'response_publish_ok' => "La graphique '%s' a été publiée avec succès!",
				'response_unpublish_ok' => "La graphique '%s' a été depubliée avec succès!",
				'response_save_notok' => "Erreur en enregistrant la graphique '%s'!",
				'response_path_exists' => "La graphique '%s' n'a pas pu être enregistrée, parce qu'il existe déjà un autre fichier ou un autre répertoire a cet endroit!",
		)),
		'application/*' => array_merge($l__tmp, array(
				'response_save_ok' => "The document '%s' has been successfully saved!",
				'response_publish_ok' => "Le fichier '%s' a été publié avec succès!",
				'response_unpublish_ok' => "Le fichier '%s' a été depublié avec succès!",
				'response_save_notok' => "Erreur en enregistrant le fichier '%s'!",
				'response_path_exists' => "Le fichier '%s' n''a pas pu être enregistré, parce qu'il existe déjà un autre fichier ou un autre répertoire a cet endroit!",
				'we_description_missing' => "Please enter a desription in the 'Desription' field!",
				'response_save_wrongExtension' => "Erreur en enregistrant '%s' \\nL'extension de fichier '%s' n'est pas valide pour des fichiers divers!\\nPour cela créer s'il vous plaît un fichier html!",
		)),
		'application/x-shockwave-flash' => array_merge($l__tmp, array(
				'response_save_ok' => "Le vidéo-Flash '%s' a été enregistré avec succès!",
				'response_publish_ok' => "Le vidéo-Flash '%s' a été publié avec succès!",
				'response_unpublish_ok' => "Le vidéo-Flash '%s' a été depublié avec succès!",
				'response_save_notok' => "Erreur en enregistrant le vidéo-Flash '%s'!",
				'response_path_exists' => "Le vidéo-Flash '%s' n'a pas pu être enregistré, parce qu'il existe déjà un autre fichier ou un autre répertoire a cet endroit!",
		)),
		'video/quicktime' => array_merge($l__tmp, array(
				'response_save_ok' => "The Quicktime movie '%s' has been successfully saved!",
				'response_publish_ok' => "Le film-Quicktime '%s' a été publié avec succès!",
				'response_unpublish_ok' => "Le film-Quicktime '%s' a été depublié avec succès!",
				'response_save_notok' => "Erreur en enregistrant le film-Quicktime '%s'!",
				'response_path_exists' => "Le film-Quicktime '%s' n'a pas pu être enregistré, parce qu'il existe déjà un autre fichier ou un autre répertoire a cet endroit!",
		)),
);


/* * ***************************************************************************
 * PLEASE DON'T TOUCH THE NEXT LINES
 * UNLESS YOU KNOW EXACTLY WHAT YOU ARE DOING!
 * *************************************************************************** */

$_language_directory = dirname(__FILE__) . "/modules";
$_directory = dir($_language_directory);

while (false !== ($entry = $_directory->read())) {
	if (strstr($entry, '_we_editor')) {
		include($_language_directory . "/" . $entry);
	}
}
