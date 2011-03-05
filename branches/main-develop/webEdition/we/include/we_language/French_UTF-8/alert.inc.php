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
 * Language file: alert.inc.php
 * Provides language strings.
 * Language: English
 */
$l_alert = array(
		'notice' => "Notice",
		'warning' => "Warning", // TRANSLATE
		'error' => "Error", // TRANSLATE

		'noRightsToDelete' => "\\'%s\\' cannot be deleted! You do not have permission to perform this action!", // TRANSLATE
		'noRightsToMove' => "\\'%s\\' cannot be moved! You do not have permission to perform this action!", // TRANSLATE
		'in_wf_warning' => "Avant que le document puisse être passé dans le Gestion de Flux, il doit être enregistré!\\nEnregistrer le document maintenant?",
		'in_wf_warning' => "Avant que le modèle puisse être passé dans le Gestion de Flux, il doit être enregistré!\\nEnregistrer le modèle maintenant?",
		'not_im_ws' => "Le fichier n'est pas dans votre éspace de travail!",
		'not_im_ws' => "Le répertoire n'est pas dans votre éspace de travail!",
		'not_im_ws' => "Le modèle n'est pas dans votre éspace de travail!",
		'delete_recipient' => "Voulez-vous vraiment supprimer l'adresse E-Mail sélectionné?",
		'recipient_exists' => "Cette adresse E-Mail existe déjà!",
		'input_name' => "S'il vous plaît saisissez une nouvelle adresse E-Mail!",
		'input_file_name' => "Enter a filename.", // TRANSLATE
		'max_name_recipient' => "L'adresse E-Mail doit avoir une longeur maximale de 32 signe!!",
		'not_entered_recipient' => "Vous n'avez pas encore saisi une adresse E-Mail!",
		'recipient_new_name' => "Changer l'adresse E-Mail!",
		'objectFile' => "Vous ne pouvez pas créer des nouveaux objects, parce que ou vous n'avez pas les droits nécessaires<br>ou parce qu'il n'y a aucune classe qui est valide dans un de votre éspaces de travail!",
		'required_field_alert' => "Le champ '%s' est obligatoire et doit être rempli!",
		'phpError' => "webEdition ne peut pas être démarré",
		'3timesLoginError' => "L'authentification a échouée %sx ! S'il vous plaît attendez %s minutes et l'essayez de nouveau!",
		'popupLoginError' => "La fenêtre de webEdition n\'a pas pu etre ouvert!\\n\\nwebEdition ne peut être démarer que si votre navigateur ne supprime pas le Pop-Up.",
		'publish_when_not_saved_message' => "Le document n'est pas encore enregistré!Voulez-vous le publier quand même?",
		'template_in_use' => "Le modèle est utilisé en ce moment et ne peut pas être enlever alors!",
		'no_cookies' => "Vous n\'avez pas activé les cookies. S'il vous plaît activez les cookies dans votre navigateur, pour que webEdition puisse fonctionner!",
		'doctype_hochkomma' => "Le nom du type-de-document ne doit pas contenir de ' (apostroph) et pas de , (virgule)!",
		'thumbnail_hochkomma' => "Le nom d'une imagettes ne doit pas contenir de  ' (apostroph) et pas de , (virgule)!",
		'can_not_open_file' => "Le fichier %s ne pouvait pas être ouvert!",
		'no_perms_title' => "Non authorisé",
		'no_perms_action' => "You don't have the permission to perform this action.", // TRANSLATE
		'access_denied' => "Accès réfusé!",
		'no_perms' => "S'il vous plaît contacter le propriétaire (%s)<br>ou l'administrateur, si vous nécessiter accès!",
		'temporaere_no_access' => "Accès non possible pour l'instant",
		'temporaere_no_access_text' => "Le fichier (%s) est éditer par l'utilisateur '%s' en ce moment.",
		'file_locked_footer' => "This document is edited by \"%s\" at the moment.", // TRANSLATE
		'file_no_save_footer' => "Vous n'avez pas le droits nécessaires, pour enregistrer le document.",
		'login_failed' => "Votre nom d'utilisateur ou votre mot de passe n'est pas reconnu!",
		'login_failed_security' => "webEdition ne pouvait pas être démarré!\\n\\nÀ cause des raisons de sécurité l'authentification a été arrêté, parc que le temps maximal pour l'authentification a été dépassé!\\n\\nS'il vous plaît reauthentifiez vous.",
		'perms_no_permissions' => "Vous n'êtes pas authorisé pour cette action!\\nS'il vous plaît reauthentifiez vous!",
		'no_image' => "Le fichier choisi n'est pas une graphique!",
		'delete_ok' => "Fichiers ou bien répertoires supprimé avec succès!",
		'delete_cache_ok' => "Cache successfully deleted!", // TRANSLATE
		'nothing_to_delete' => "Rien choisi à supprimer!",
		'delete' => "Supprimer les entrées sélectionné?\\nÊtes-vous sûr?",
		'delete_cache' => "Delete cache for the selected entries?\\nDo you want to continue?", // TRANSLATE
		'delete_folder' => "Supprimer le répertoire sélectionné?\\nConsiderez que tous les documents et répertoires qui se trouve dans le répertoire seront supprimé également!\\nÊtes-vous sûr?",
		'delete_nok_error' => "Le fichier '%s' ne peut pas être supprimé.",
		'delete_nok_file' => "Le fichier '%s' ne peut pas être supprimé.\\nIl est possible qu'il soit protécté.",
		'delete_nok_folder' => "Le répertoire '%s' ne peut pas être supprimé.\\nIl est possible qu'il soit protécté.",
		'delete_nok_noexist' => "Le fichier '%s' n'existe pas!",
		'noResourceTitle' => "No Item!", // TRANSLATE
		'noResource' => "The document or directory does not exist!", // TRANSLATE
		'move_exit_open_docs_question' => "Before moving all %s must be closed.\\nIf you continue, the following %s will be closed, unsaved changes will not be saved.\\n\\n", // TRANSLATE
		'move_exit_open_docs_continue' => 'Continue?', // TRANSLATE
		'move' => "Move selected entries?\\nDo you want to continue?", // TRANSLATE
		'move_ok' => "Files successfully moved!", // TRANSLATE
		'move_duplicate' => "There are files with the same name in the target directory!\\nThe files cannot be moved.", // TRANSLATE
		'move_nofolder' => "The selected files cannot be moved.\\nIt isn't possible to move directories.", // TRANSLATE
		'move_onlysametype' => "The selected objects cannnot be moved.\\nObjects can only be moved in there own classdirectory.", // TRANSLATE
		'move_no_dir' => "Please choose a target directory!", // TRANSLATE
		'document_move_warning' => "After moving documents it is  necessary to do a rebuild.<br />Would you like to do this now?", // TRANSLATE
		'nothing_to_move' => "There is nothing marked to move!", // TRANSLATE
		'move_of_files_failed' => "One or more files couldn't moved! Please move these files manually.\\nThe following files are affected:\\n%s", // TRANSLATE
		'template_save_warning' => "This template is used by %s published documents. Should they be resaved? Attention: This procedure may take some time if you have many documents!", // TRANSLATE
		'template_save_warning1' => "This template is used by one published document. Should it be resaved?", // TRANSLATE
		'template_save_warning2' => "This template is used by other templates and documents, should they be resaved?", // TRANSLATE
		'thumbnail_exists' => "Cette imagette existe déjà!",
		'thumbnail_not_exists' => "Cette imagette n'existe pas!",
		'thumbnail_empty' => "You must enter a name for the new thumbnail!", // TRANSLATE
		'doctype_exists' => "Ce type-de-document existe déjà!",
		'doctype_empty' => "Vous n'avez pas encore saisi un nom!",
		'delete_cat' => "Voulez-vous vraiment supprimez la catégories séléctionée?",
		'delete_cat_used' => "Cette catégorie est déjà utilisé et ne peut pas être supprimer alors!",
		'cat_exists' => "Cette catégorie existe déjà!",
		'cat_changed' => "Cette catégorie est déja utilisé! Si la catégorie est affichée dans des document, vous devez reenregistrer ce document!\\nChanger la catégories quand même?",
		'max_name_cat' => "Le nom de la catégorie doit avoir une longeur maximale de 32 signe!",
		'not_entered_cat' => "Vous n'avez pas encore saisi un nom de catégorie!",
		'cat_new_name' => "S'il vous plaît saisissez le nouveau nom de la catégorie!",
		'we_backup_import_upload_err' => "Erreur en téléchargant le fichier de sauvegarde! La taille de fichier maximale pour le téléchargement est %s. Si votre fichier de sauvegardes est plus grand, copier-le par FTP dans le répértoire webEdition/we_backup/ et choisissez '" . g_l('backup', "[import_from_server]") . "'!",
		'rebuild_nodocs' => "Il n'y aucun document, qui correspond aux critère choisis!",
		'we_name_not_allowed' => "Les noms 'we' et 'webEdition' sont utilisés par webEdition même et ne doivent pas être utilisés alors!",
		'we_filename_empty' => "Vous n'avez pas encore saisi un nom pour ce fichier ou bien répertoire!",
		'exit_multi_doc_question' => "Several open documents contain unsaved changes. If you continue all unsaved changes are discarded. Do you want to continue and discard all modifications?", // TRANSLATE
		'exit_doc_question_' . FILE_TABLE => "Le document a été modifié.<br>Souhaitez-vous enregistrer les modifications apportées?",
		'exit_doc_question_' . TEMPLATES_TABLE => "Le modèle a été modifié.<br>Souhaitez-vous enregistrer les modifications apportées?",
		'deleteTempl_notok_used' => "L'action ne pouvat pas être éfféctuée, comme un ou plusieur modèle à supprimer sont déjà en utilisation!",
		'deleteClass_notok_used' => "One or more of the classes are in use and could not be deleted!", // TRANSLATE
		'delete_notok' => "Erreur en supprimant!",
		'nothing_to_save' => "La fonction enregistrer ne peut pas être éfféctué en ce moment !",
		'nothing_to_publish' => "The publish function is disabled at the moment!", // TRANSLATE
		'we_filename_notValid' => "Invalid filename\\nValid characters are alpha-numeric, upper and lower case, as well as underscore, hyphen and dot (a-z, A-Z, 0-9, _, -, .)",
		'empty_image_to_save' => "La graphique choisi est vide.\\nPoursuivre",
		'path_exists' => "Le document ou bien le répertoire %s ne pouvait pas être enregistré, parce qu'il y déjà un autre document au même endroit!",
		'folder_not_empty' => "Comme un ou plusieurs répertoire n'ont pas éte complètement vide, il n'était pas possible de les supprimer complètement du serveur! Supprimer ces fichiers à main.\\n Les répertoires suivant sont concerné:\\n%s",
		'name_nok' => "Des nome ne doivent pas contenir les signe '<' et '>'!",
		'found_in_workflow' => "Une ou plusieurs  entrées à supprimer se trouve dans le Gestion de Flux en ce moment! Voulez-vous enlever ces entrées du Gestion de Flux?",
		'import_we_dirs' => "Vous essayez d'importer d'un répertoire de webEdition!\\nCes répertoires sont protectés et c'est pourquoi un import n'est pas possible!",
		'image/*' => "Le fichier ne pouvait pas être créé. Ou ce n\\'est pas une graphique ou votre espace web est plein !",
		'application/x-shockwave-flash' => "Le fichier ne pouvait pas être créé. Ou ce n\\'est pas un vidéo flash ou votre espace web est plein!",
		'video/quicktime' => "Le fichier ne pouvait pas être créé. Ou ce n\\'est pas un film quicktime ou votre espace web est plein",
		'text/css' => "The file could not be stored. Either it is not a CSS file or your disk space is exhausted!", // TRANSLATE
		'no_file_selected' => "Vous n\\'avez choisi aucun fichier à télécharger!",
		'browser_crashed' => "La fenêtre ne pouvait pas être ouvert, pace que votre navigateur a causé une erreur! S'il vous plaît enregistrez votre travail et redémarrez le navigateur.",
		'copy_folders_no_id' => "La fenêtre actuelle doit être enregistré d'abord!",
		'copy_folder_not_valid' => "Le répertoire même ou un des répertoire parental ne peut pas être copier!",
		'headline' => 'Attention', // TRANSLATE
		'description' => 'Ce document n\'a pas de vue.',
		'last_document' => 'You edit the last document.', // TRANSLATE
		'first_document' => 'Vous êtes sur le premier document.',
		'doc_not_found' => 'Could not find matching document.', // TRANSLATE
		'no_entry' => 'No entry found in history.', // TRANSLATE
		'no_open_document' => 'There is no open document.', // TRANSLATE
		'confirm_delete' => 'Delete this document?', // TRANSLATE
		'no_delete' => 'This document could not be deleted.', // TRANSLATE
		'return_to_start' => 'Le fichier a été supprimé avec succès.\\n De retour à la page d\'accueil du seeMode.',
		'return_to_start' => 'The document was moved. \\nBack to seeMode startdocument.', // TRANSLATE
		'no_delete' => 'This document could not be moved', // TRANSLATE
		'cockpit_not_activated' => 'The action could not be performed because the cockpit is not activated.', // TRANSLATE
		'cockpit_reset_settings' => 'Are you sure to delete the current Cockpit settings and reset the default settings?', // TRANSLATE
		'save_error_fields_value_not_valid' => 'The highlighted fields contain invalid data.\\nPlease enter valid data.', // TRANSLATE

		'eplugin_exit_doc' => "The document has been edited with extern editor. The connection between webEdition and extern editor will be closed and the content will not be synchronized anymore.\\nDo you want to close the document?", // TRANSLATE

		'delete_workspace_user' => "The directory %s could not be deleted! It is defined as workspace for the following users or groups:\\n%s", // TRANSLATE
		'delete_workspace_user_r' => "The directory %s could not be deleted! Within the directory there are other directories which are defined as workspace for the following users or groups:\\n%s", // TRANSLATE
		'delete_workspace_object' => "The directory %s could not be deleted! It is defined as workspace for the following objects:\\n%s", // TRANSLATE
		'delete_workspace_object_r' => "The directory %s could not be deleted! Within the directory there are other directories which are defined as workspace in the following objects:\\n%s", // TRANSLATE


		'field_contains_incorrect_chars' => "A field (of the type %s) contains invalid characters.", // TRANSLATE
		'field_input_contains_incorrect_length' => "The maximum length of a field of the type \'Text input\' is 255 characters. If you need more characters, use a field of the type \'Textarea\'.", // TRANSLATE
		'field_int_contains_incorrect_length' => "The maximum length of a field of the type \'Integer\' is 10 characters.", // TRANSLATE
		'field_int_value_to_height' => "The maximum value of a field of the type \'Integer\' is 2147483647.", // TRANSLATE


		'we_filename_notValid' => "Le nom de fichier saisi n'est pas valide!\\nnSignes permis sont les lettres de a à z (majuscule- ou minuscule) , nombres, soulignage (_), signe moins (-), point (.)",
		'login_denied_for_user' => "The user cannot login. The user access is disabled.", // TRANSLATE
		'no_perm_to_delete_single_document' => "You have not the needed permissions to delete the active document.", // TRANSLATE

		'applyWeDocumentCustomerFiltersDocument' => "The document has been moved to a folder with divergent customer account policies. Should the settings of the folder be transmitted to this document?", // TRANSLATE
		'applyWeDocumentCustomerFiltersFolder' => "The directory has been moved to a folder with divergent customers account policies. Should the settings be adopted for this directory and all subelements? ", // TRANSLATE

		'field_in_tab_notvalid_pre' => "The settings could not be saved, because the following fields contain invalid values:", // TRANSLATE
		'field_in_tab_notvalid' => ' - field %s on tab %s', // TRANSLATE
		'field_in_tab_notvalid_post' => 'Correct the fields before saving the settings.', // TRANSLATE
		'discard_changed_data' => 'There are unsaved changes that will be discarded. Are you sure?', // TRANSLATE
);


if (defined("OBJECT_FILES_TABLE")) {
	$l_alert = array_merge($l_alert, array(
			'in_wf_warning' => "Avant que l\\'object puisse être passé dans le Gestion de Flux, il doit être enregistré!\\nEnregistrer l\\'object maintenant?",
			'in_wf_warning' => "Avant que la classe puisse être passée dans le Gestion de Flux, elle doit être enregistrée!\\nEnregistrer la classe maintenant",
			'exit_doc_question_' . OBJECT_TABLE => "La classe a été modifiée.<br>Souhaitez-vous enregistrer les modifications apportées?",
			'exit_doc_question_' . OBJECT_FILES_TABLE => "L'object a été modifié.<br>Souhaitez-vous enregistrer les modifications apportées?",
					));
}
