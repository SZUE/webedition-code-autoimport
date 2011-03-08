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
$l_backup = array(
		'save_not_checked' => "Vous n\\'avez pas encore choisi d'où la sauvegarde doit être enregistré!",
		'wizard_title' => "Assistant pour restaurer une sauvegarde",
		'wizard_title_export' => "Assistant de sauvegarde",
		'save_before' => "En restaurant le fichier de sauvegarde, tous les fichiers existants seront supprimer! Il est conseillé, de sauvegarder les fichier auparavant.",
		'save_question' => "Voulez-vous poursuivre?",
		'step1' => "Étape 1/4 - Enregistrer des fichiers existants",
		'step2' => "Étape 2/4 - Séléction de la source des données",
		'step3' => "Étape 3/4 - Restauerer les données enregistrées",
		'step4' => "Étape 4/4 - Restauration terminée",
		'extern' => "Restaurer les fichiers/répertoires externes de webEdition",
		'settings' => "Restaurer les préférences",
		'rebuild' => "Rebuild automatique",
		'select_upload_file' => "Restauration d'un fichier local",
		'select_server_file' => "Choisissez le fichier de sauvegarde que vous voulez restaurer.",
		'charset_warning' => "If you encounter problems when restoring a backup, please ensure that the <strong>target system uses the same character set as the source system</strong>. This applies both to the character set of the database (collation) as well as for the character set of the user interface language!", // TRANSLATE
		'defaultcharset_warning' => '<span style="color:ff0000">Attention! The standard charset is not defined.</span> For some server configurations, this can lead to problems while importing backups.!', // TRANSLATE
		'finished_success' => "La restauration de la sauvegarde a été terminé avec succès.",
		'finished_fail' => "La restauration de la sauvegarde n'a pas été terminé avec succès.",
		'question_taketime' => "L\\'export demande quelques temps.",
		'question_wait' => "Nous vous prions de patienter un peu!",
		'export_title' => "Créer une sauvegarde",
		'finished' => "Terminé",
		'extern_files_size' => "Comme la taille maximale de la base de donne est limité à %.1f MO (%s Octet), il est possible que plusiers fichiers soient créés",
		'extern_files_question' => "Sauvegarder les fichiers/répertoire externe de webEdition",
		'export_location' => "S'il vous plaît choisissez, d'où le fichier de sauvegarde doit être enregistré. Si le fichier est enregistré au serveur, vous le trouvez dans le répertoire '/webEdition/we_backup/data/'.",
		'export_location_server' => "Au Serveur",
		'export_location_send' => "Au disc dur local",
		'can_not_open_file' => "Le fichier '%s' ne peut pas être overt.",
		'too_big_file' => "Le fichier '%s' ne peut pas être enregistrer, parce que la taille de fichier maximale a été dépassée.",
		'cannot_save_tmpfile' => "Le fichier temporaire ne peut pas être créé! Vérifié s'il vous plaît, si vous avez les droits d'écrire dans %s.",
		'cannot_save_backup' => "Le fichier-sauvegarde ne peut pas être enregistré.",
		'cannot_send_backup' => " la Sauvegarde ne peut pas être éfféctuée ",
		'finish' => "The backup was successfully created.", // TRANSLATE
		'finish_error' => " Erreur: La Sauvegarde n'a pas été terminé avec succès",
		'finish_warning' => "Avertissement: La Sauvegarde a été terminé, mais il est possible que ne pas tous les fichiers ont été créé",
		'export_step1' => "Étape 1/2 - Paramètre de Sauvegarde",
		'export_step2' => "Étape 2/2 - Sauvegarde terminée",
		'unspecified_error' => "Une erreur inconnu s\\'est produit",
		'export_users_data' => "Sauvegarder les données d'utilisateur",
		'import_users_data' => "Restaurer les données d'utilisateur",
		'import_from_server' => "Charger les données du serveur",
		'import_from_local' => "Charger les données d'un fichier local",
		'backup_form' => "Sauvegarde du ",
		'nothing_selected' => "Rien a été choisi!",
		'query_is_too_big' => "Le fichier de sauvegarde contient un fichier qui ne pouvat pas être restauré, comme il est plus grand que %s octet!",
		'show_all' => "Afficher tous les fichiers",
		'import_customer_data' => "Restaurer les données de client",
		'import_shop_data' => "Restaurer les données de boutique",
		'export_customer_data' => "Sauvegarder les données de client",
		'export_shop_data' => "Sauvegarder les données de boutique",
		'working' => "En train...",
		'preparing_file' => "Préperation des donnés pour la restauration...",
		'external_backup' => "Sauvegarder les fichiers externes...",
		'import_content' => "Restaurer le contenu",
		'import_files' => "Restaurer les fichiers",
		'import_doctypes' => "Restaurer les types-de-documents",
		'import_user_data' => "Restaurer les données d'utilisateur",
		'import_templates' => "Restaurer les modèles",
		'export_content' => "Sauvegarder le contenu",
		'export_files' => "Sauvegarder les fichiers",
		'export_doctypes' => "Sauvegarder les type-de-documents",
		'export_user_data' => "Sauvegarder les données d'utilisateur",
		'export_templates' => "Sauvegarder les modèles",
		'download_starting' => "Le Téléchargement du fichier de sauvegarde est en cours.<br><br>S'il ne démarre pas dans 10 secondes,<br>",
		'download' => "cliquez ici s'il vous plaît.",
		'download_failed' => "Le fichier demandé n'existe pas ou vous n'avez pas l'authorisation de la télécharger.",
		'extern_backup_question_exp' => "Vous avez choisi 'Sauvegarder les fichiers/répertoires externes de webedition'. Cette option demande beaucoup de temps et peut produire des erreur de system. Poursuivre quand même?",
		'extern_backup_question_exp_all' => "Vous avez choisi 'Choisir tous'. 'Sauvegarder les fichiers/répertoires externes de webedition' sera marqué automatiquement avec. Cette option demande beaucoup de temps et peut produire des erreur de system.\\nLaisser marqué 'Sauvegarder les fichiers/répertoires externes de webedition' quand même?",
		'extern_backup_question_imp' => "Vous avez choisi 'Restaurer les fichiers/répertoires externes de webedition'. Cette option demande beaucoup de temps et peut produire des erreur de system. Poursuivre quand même?",
		'extern_backup_question_imp_all' => "Vous avez choisi 'Choisir tous'. 'Restaurer les fichiers/répertoires externes de webedition' sera marqué automatiquement avec. Cette option demande beaucoup de temps et peut produire des erreur de system.\\nLaisser marqué 'Sauvegarder les fichiers/répertoires externes de webedition' quand même?",
		'nothing_selected_fromlist' => "S'il vous plaît choisissez un ficher de sauvegarde de la liste!",
		'export_workflow_data' => "Sauvegarder les données de Gestion de Flux",
		'export_todo_data' => "Sauvegarder les données de Tâche/Messages",
		'import_workflow_data' => "Restaurer les données de Gestion Flux",
		'import_todo_data' => "Restaurer les données de Tâches/Messagerie",
		'import_check_all' => "Choisir tous",
		'export_check_all' => "Choisir tous",
		'import_shop_dep' => "Vous avez choisi 'Restaurer les données de boutique'. La Boutique nécessite les données de clients pour fonctionner correctement, c'est pourquoi 'Restaurer les données de clients' a été marqué automatiquement.",
		'export_shop_dep' => "Vous avez choisi 'Sauvegarder les données de boutique'. La Boutique nécessite les données de clients pour fonctionner correctement, c'est pourquoi 'Sauvegarder les données de clients' a été marqué automatiquement.",
		'import_workflow_dep' => "Vous avez chois 'Restaurer les données de Gestion Flux' . Le Gestion de Flux nécessite les documents et les données d'utilisateur pour fonctionner correctement, c'est pourqoi 'Restaurer les documents et modèles' et 'Restaurer les données d'utilisateur' ont  été marqués automatiquement.",
		'export_workflow_dep' => "Vous avez chois 'Sauvegarder les données de Gestion de Flux' . Le Gestion de Flux nécessite les documents et les données d'utilisateur pour fonctionner correctement, c'est pourqoi 'Sauvegarder les documents et modèles' et 'Sauvegarder les données d'utilisateur' ont été marqués automatiquement.",
		'import_todo_dep' => "Vous avez chois 'Restaurer les données de Tâches/Messagerie'. Le module Tâche/Messagerie nécessite les données d'utilisateur pour fonctionner correctement, c'est pourqoi wird 'Restaurer les données d'utilisateur' a été marqués automatiquement.",
		'export_todo_dep' => "Vous avez chois 'Sauvegarder les données de Tâches/Messagerie'. Le module Tâche/Messagerie nécessite les données d'utilisateur pour fonctionner correctement, c'est pourqoi wird 'Sauvegarder les données d'utilisateur' a été marqués automatiquement.",
		'export_newsletter_data' => "Sauvegarder les données de la lettre d'information",
		'import_newsletter_data' => "Restaurer les données de la lettre d'information",
		'export_newsletter_dep' => "Vous avez chois 'Sauvegarder les données de lettre d'inforamtion'. La Lettre d'info. nécessite les document,s les données de clients et les objects pour fonctionner correctement, c'est pourquoi 'Sauvegarder les documents et modèles', 'Sauvegarder les objects et les classes' et 'Sauvegarder les données de clients' ont été marqués automatiquement.",
		'import_newsletter_dep' => "Vous avez chois 'Restaurer les données de lettre d'inforamtion'. La Lettre d'info. nécessite les document,s les données de clients et les objects pour fonctionner correctement, c'est pourquoi 'Restaurer les documents et modèles', 'Restaurer les objects et les classes' et 'Restaurer les données de clients' ont été marqués automatiquement.",
		'warning' => "Avertissement",
		'error' => "Erreur",
		'export_temporary_data' => "Sauvegarder les fichiers temporaires",
		'import_temporary_data' => "Restaurer les fichiers temporaires",
		'export_banner_data' => "Sauvegarder les données de bannière",
		'import_banner_data' => "Restauer les données de bannière",
		'export_prefs' => "Sauvegarder les préférences",
		'import_prefs' => "Restaurer les préférences",
		'export_links' => "Sauvegarder les liens",
		'import_links' => "Restaurer les liens",
		'export_indexes' => "Sauvegarder les indices",
		'import_indexes' => "Restaurer les indices",
		'filename' => "Nom du fichier",
		'compress' => "Comprimer",
		'decompress' => "Decomprimer",
		'option' => "Options de la Sauvegarde",
		'filename_compression' => "Saisissez ici le nom du fichier de sauvegarde. Vous pouvez aussi activer la compression. Le fichier de sauvegarde est alors comprimer avec gzip et obtiendra l'extension .gz. Ce processus peut durer quelque minutes!<br>Si la sauvegarde ne réuissit pas, essayer de changer les préférences s'il vous plaît.",
		'export_core_data' => "Sauvegarder les documents et les modèles",
		'import_core_data' => "Restaurer les documents et les modèles",
		'export_object_data' => "Sauvegarder les objects et les classes",
		'import_object_data' => "Restaurer les objects et les classes",
		'export_binary_data' => "Sauvegarder les données binaires (Images, PDFs, ...)",
		'import_binary_data' => "Restaurer les données binaires (Images, PDFs, ...)",
		'export_schedule_data' => "Sauvergarder les données de l'ordonnanceur",
		'import_schedule_data' => "Restaurer les données de l'ordonnanceur",
		'export_settings_data' => "Sauvegarder les préférences",
		'import_settings_data' => "Restaurer les préférences",
		'export_extern_data' => "Sauvegarder les fichiers/répertoires externe de webEdition",
		'import_extern_data' => "Restaurer les fichiers/répertoires webEdition-externe",
		'export_binary_dep' => "Vous avez choisi 'Sauvegarder les données binaires'. Pour fonctionner correctement, les données binaires nécessitent aussi les documents. Ce pourqoi 'Sauvegarder les documents et modèles' a été marqué automatiquement.",
		'import_binary_dep' => "Vous avez choisi 'Restaurer les données binaires'. Pour fonctionner correctement, les données binaires nécessitent aussi les documents. Ce pourqoi 'Restaurer les documents et modèles' a été marqué automatiquement.",
		'export_schedule_dep' => "You have selected the option 'Save schedule data'. The Schedule Module needs the documents and objects and because of that, 'Save documents and templates' and 'Save objects and classes' has been automatically selected.", // TRANSLATE
		'import_schedule_dep' => "You have selected the option 'Restore schedule data'. The Schedule Module needs the documents data and objects and because of that, 'Restore documents and templates' and 'Restore objects and classes' has been automatically selected.", // TRANSLATE
		'export_temporary_dep' => "Vous avez choisi 'Sauvegarder les fichiers temporaires'. Pour fonctionner correctement, les fichiers temporaires nécessitent aussi les documents. Ce pourqoi 'Sauvegarder les documents et modèles' a été marqué automatiquement.",
		'import_temporary_dep' => "Vous avez choisi 'Restaurer les fichiers temporaires' ausgewählt. Pour fonctionner correctement, les fichiers temporaires nécessitent aussi les documents. Ce pourqoi 'Restaurer les documents et modèles' a été marqué automatiquement.",
		'compress_file' => "Comprimer les données",
		'export_options' => "Choisissez les données à sauvegarder.",
		'import_options' => "Choisissez les données à restaurer.",
		'extern_exp' => "Attention! Cette option demande beaucoup de temps et peut produire des erreur de system",
		'unselect_dep2' => "Vous avez demarquer '%s'. Les options suivant vont être demarqués automatiquement:",
		'unselect_dep3' => "Néanmois vous pouvez choisir les options non marquées.",
		'gzip' => "gzip", // TRANSLATE
		'zip' => "zip", // TRANSLATE
		'bzip' => "bzip", // TRANSLATE
		'none' => "aucune",
		'cannot_split_file' => "Il n'est pas possible de préparer le fichier '%s' pour la préparation!",
		'cannot_split_file_ziped' => "Le fichier a été comprimé avec une méthode de compression non supporté.",
		'export_banner_dep' => "You have selected the option 'Save banner data'. The banner data need the documents and because of that, 'Save documents and templates' has been automatically selected.", // TRANSLATE
		'import_banner_dep' => "You have selected the option 'Restore banner data'. The banner data need the documents data and because of that, 'Restore documents and templates' has been automatically selected.", // TRANSLATE

		'delold_notice' => "Il est conseillé de supprimer les fichiers exitants auparavant.<br>Voulez-vous le faire?",
		'delold_confirm' => "Êtes vous sûr, que vous voulez supprimer tous les fichiers du serveur?",
		'delete_entry' => "Supprimer %s",
		'delete_nok' => "Les fichiers ne peuvent pas être supprimé!",
		'nothing_to_delete' => "Rien à supprimer!",
		'files_not_deleted' => "Un ou plusiers fichiers n'ont pas pu être supprimé du serveur! Il est possible qu'ils soient . Supprimer manuellement les fichiers du serveur. Les fichier suivant sont concérnés:",
		'delete_old_files' => "En cours de supprimer les anciens fichiers...",
		'export_configuration_data' => "Sauvegarder la configuration",
		'import_configuration_data' => "Restaurer la configuration",
		'import_export_data' => "Restaurer les données d'Export",
		'export_export_data' => "Sauvegarder les données d'Export",
		'export_versions_data' => "Save version data", // TRANSLATE
		'export_versions_binarys_data' => "Save Version-Binary-Files", // TRANSLATE
		'import_versions_data' => "Restore version data", // TRANSLATE
		'import_versions_binarys_data' => "Restore Version-Binary-Files", // TRANSLATE

		'export_versions_dep' => "You have selected the option 'Save version data'. The version data need the documents, objects and version-binary-files and because of that, 'Save documents and templates', 'Save object and classes' and 'Save Version-Binary-Files' has been automatically selected.", // TRANSLATE
		'import_versions_dep' => "You have selected the option 'Restore version data'. The version data need the documents data, object data an version-binary-files and because of that, 'Restore documents and templates', 'Restore objects and classes and 'Restore Version-Binary-Files' has been automatically selected.", // TRANSLATE

		'export_versions_binarys_dep' => "You have selected the option 'Save Version-Binary-Files'. The Version-Binary-Files need the documents, objects and version data and because of that, 'Save documents and templates', 'Save object and classes' and 'Save version data' has been automatically selected.", // TRANSLATE
		'import_versions_binarys_dep' => "You have selected the option 'Restore Version-Binary-Files'. The Version-Binary-Files need the documents data, object data an version data and because of that, 'Restore documents and templates', 'Restore objects and classes and 'Restore version data' has been automatically selected.", // TRANSLATE

		'del_backup_confirm' => "Vous voulez supprimer le fichier de sauvegarde choisi?",
		'name_notok' => "Le nom du fichier n'est pas valide!",
		'backup_deleted' => "Le fichier de sauvegarde %s a été supprimé",
		'error_delete' => "Le fichier de sauvegarde n'a pas pu être supprimé! Essayez de le supprimer par FTP du répertoire /webEdition/we_backup.",
		'core_info' => 'Tous les documents et modèles.',
		'object_info' => 'Objects et classes du module de base de données-/objects.',
		'binary_info' => 'Les données binaires - images, PDFs et d\'autre documents.',
		'user_info' => 'Données des utilisateurs et des comptes du module gestion utilisateur.',
		'customer_info' => 'Données des clients du module gestion clients.',
		'shop_info' => 'Données de commande du module boutique.',
		'workflow_info' => 'Données du module gestion de flux.',
		'todo_info' => 'Messages et tâches du module tâches/messageries.',
		'newsletter_info' => 'Données du module lettre d\'info.',
		'banner_info' => 'Données du module de banière/statistique.',
		'schedule_info' => 'Données du module Ordonnanceur.',
		'settings_info' => 'Préférences du logiciel webEdition.',
		'temporary_info' => 'Données des document et objects non publié.',
		'export_info' => 'Données du modul d\'export.',
		'glossary_info' => 'Data from the glossary.', // TRANSLATE
		'versions_info' => 'Data from Versioning.', // TRANSLATE
		'versions_binarys_info' => 'This option could take some time and memory because the folder /webEdition/we/versions/ could be very large. It is recommended to save this folder manually.', // TRANSLATE


		'import_voting_data' => "Restaure des données de vote",
		'export_voting_data' => "Exporter des données de vote",
		'voting_info' => 'Données du module de vote..',
		'we_backups' => 'Sauvegardes de webEdition',
		'other_files' => 'Fichiers divers',
		'filename_info' => 'Saisissez le nom du fichier de sauvegarde.',
		'backup_log_exp' => 'Le journal sera sauvegardé sous /webEdition/we_backup/data/lastlog.php',
		'export_backup_log' => 'Créer un journal',
		'download_file' => 'Télécharger le fichier',
		'import_file_found' => 'Le fichier ressemble à un fichier d\'import de webEdition. S\'il vous plaît utilisez l\'option \"Import/Export\" du menu \"Fichier\" pour importer les données.',
		'customer_import_file_found' => 'Le fichier ressemble à un fichier d\'import du gestion clients. S\'il vous plaît utilisez l\'option \"Import/Export\" du gestio clients (PRO) pour importer les données.',
		'import_file_found_question' => 'Voulez-vous fermer maintenant le dialogue actuel et démarrer l\'assistant-d\'import/export?',
		'format_unknown' => 'Le format du fichier est inconnu!',
		'upload_failed' => 'Le fichier ne peut pas être téléchargé. S\'il vous plaît verifiez, si la taille du fichier dépasse %s',
		'file_missing' => 'Le fichier de sauvegarde n\'existe pas!',
		'recover_option' => 'Options d\'import',
		'no_resource' => 'Fatal Error: There are not enough resources to finish the backup!', // TRANSLATE
		'error_compressing_backup' => 'An error occured while compressing the backup, so the backup could not be finished!', // TRANSLATE
		'error_timeout' => 'An timeout occured while creating the backup, so the backup could not be finished!', // TRANSLATE

		'export_spellchecker_data' => "Save spellchecker data", // TRANSLATE
		'import_spellchecker_data' => "Restore spellchecker data", // TRANSLATE
		'spellchecker_info' => 'Data for spellchecker: settings, general and personal dictionaries.', // TRANSLATE

		'import_banner_data' => "Restauer les données de bannière",
		'export_banner_data' => "Sauvegarder les données de bannière",
		'export_glossary_data' => "Save glossary data", // TRANSLATE
		'import_glossary_data' => "Restore glossary data", // TRANSLATE

		'protect' => "Protect backup file", // TRANSLATE
		'protect_txt' => "The backup file will be protected from unprivileged download with additional php code. This protection requires additional disk space for import!", // TRANSLATE

		'recover_backup_unsaved_changes' => "Some open files have unsaved changes. Please check these before you continue.", // TRANSLATE
		'file_not_readable' => "The backup file is not readable. Please check the file permissions.", // TRANSLATE

		'tools_import_desc' => "Here you can restore webEdition tools data. Please select the desired tools from the list.", // TRANSLATE
		'tools_export_desc' => "Here you can save webEdition tools data. Please select the desired tools from the list.", // TRANSLATE

		'ftp_hint' => "Attention! Use the Binary mode for the download by FTP if the backup file is zip compressed! A download in ASCII 	mode destroys the file, so that it cannot be recovered!", // TRANSLATE

		'convert_charset' => "Attention! Using this option in an existing site can lead to total loss of all data, please follow the instruction in http://documentation.webedition.org/de/webedition/administration/charset-conversion-of-legacy-sites", // TRANSLATE

		'convert_charset_data' => "While importing the backup, convert the site from ISO to UTF-8", // TRANSLATE

		'view_log' => "Backup-Log", // TRANSLATE
		'view_log_not_found' => "The backup log file was not found! ", // TRANSLATE
		'view_log_no_perm' => "You do not have the needed permissions to view the backup log file! ", // TRANSLATE
);