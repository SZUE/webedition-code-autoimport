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
 * Language file: export.inc.php
 * Provides language strings.
 * Language: English
 */
$l_export = array(
		'save_changed_export' => "Export has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'auto_selection' => "Sélection automatique",
		'txt_auto_selection' => "Exporte automatiquement - d'après type-de-document ou classe- les documents ou objects choisis.",
		'txt_auto_selection_csv' => "Exports objects automatically according to their class.", // TRANSLATE
		'manual_selection' => "Sélection manuelle",
		'txt_manual_selection' => "Export manuel des document et object choisis.",
		'txt_manual_selection_csv' => "Exports manually selected objects.", // TRANSLATE
		'element' => "Sélection d'elements",
		'documents' => "Documents", // TRANSLATE
		'objects' => "Objects", // TRANSLATE
		'step1' => "Définir la séléction d'elements",
		'step2' => "Choisissez les elements à exporter",
		'step3' => "Export Générique",
		'step10' => "Export terminée",
		'step99' => "Erreur en exportant",
		'step99_notice' => "L'export n'est pas possible",
		'server_finished' => "Le fichier export a été enregistré sur le serveur.",
		'backup_finished' => "L'export a été terminé avec succès.",
		'download_starting' => "Le téléchargement du fichier a été démarré.<br><br>Si le téléchargement ne commence pas dans 10 secondes,<br>",
		'download' => "s'il vous plaît cliquez ici.",
		'download_failed' => "Le fichier demandé n'existe pas ou vous n'avez pas l'authorisation de le télécharger.",
		'file_format' => "Format de fichier",
		'export_to' => "Exporter à",
		'export_to_server' => "Serveur",
		'export_to_local' => "Disque dur local",
		'cdata' => "Codage",
		'export_xml_cdata' => "Ajouter des séctions-CDATA",
		'export_xml_entities' => "Remplacer les entités",
		'filename' => "Nom de fichier",
		'path' => "Chemin",
		'doctypename' => "Documents du Type-de-Document",
		'classname' => "Objects de la classe",
		'dir' => "Verzeichnis",
		'categories' => "Catégories",
		'wizard_title' => "Assistan d'Export",
		'xml_format' => "XML", // TRANSLATE
		'csv_format' => "CSV", // TRANSLATE
		'csv_delimiter' => "Séperateur",
		'csv_enclose' => "Limiteur de texte",
		'csv_escape' => "Éspace",
		'csv_lineend' => "Format de fichier",
		'csv_null' => "Remplacement-NULL",
		'csv_fieldnames' => "La première ligne contient le nom de champ",
		'windows' => "Format Windows",
		'unix' => "Format UNIX",
		'mac' => "Format Mac",
		'generic_export' => "Export Générique",
		'title' => "Assistan d'Export",
		'gxml_export' => "Export Générique XML ",
		'txt_gxml_export' => "Export de documents et objects de webEdition au format XMl \"plat\" (3 plans).",
		'csv_export' => "Export CSV",
		'txt_csv_export' => "Export d'objects webEdition au format CSV (Comma Separated Values).",
		'csv_params' => "Préférences",
		'error' => "L'export n'a pas été terminé avec succès!",
		'error_unknown' => "Une erreur inconnue s'est produit!",
		'error_object_module' => "L'export de document au forma CSV n'est pas supporté en ce moment!<br><br>Comme le modul de base de données/objects n'est pas installé, l'export au format CSV n'est pas disponible.",
		'error_nothing_selected_docs' => "L'export n'a pas été éffectué!<br><br>Aucun document choisi.",
		'error_nothing_selected_objs' => "L'export n'a pas été éffectué!<br><br>Aucun document ou object choisi.",
		'error_download_failed' => "Le fichier export ne pouvait pas être téléchargé..",
		'comma' => ", {Virgule}",
		'semicolon' => "; {Point Vigule}",
		'colon' => ": {Double Point}",
		'tab' => "\\t {Tab}",
		'space' => "  {Éspace}",
		'double_quote' => "\" {Guillemets}",
		'single_quote' => "' {Apostrophe}",
		'we_export' => 'wE Export',
		'wxml_export' => 'Export XML de webEdition',
		'txt_wxml_export' => 'Export de documents, modèles, objects et de classes de webEdition, correspondantent au DTD(Document-Type-Definition) spécifique de webEdition.',
		'options' => 'Options', // TRANSLATE
		'handle_document_options' => 'Documents', // TRANSLATE
		'handle_template_options' => 'Templates', // TRANSLATE
		'handle_def_templates' => 'Export default templates', // TRANSLATE
		'handle_document_includes' => 'Export included documents', // TRANSLATE
		'handle_document_linked' => 'Export linked documents', // TRANSLATE
		'handle_object_options' => 'Objects', // TRANSLATE
		'handle_def_classes' => 'Export default classes', // TRANSLATE
		'handle_object_includes' => 'Export included objects', // TRANSLATE
		'handle_classes_options' => 'Classes', // TRANSLATE
		'handle_class_defs' => 'Default value', // TRANSLATE
		'handle_object_embeds' => 'Export embedded objects', // TRANSLATE
		'handle_doctype_options' => 'Doctypes/<br>Categorys/<br>Navigation',
		'handle_doctypes' => 'Doctypes', // TRANSLATE
		'handle_categorys' => 'Categorys',
		'export_depth' => 'Export depth', // TRANSLATE
		'to_level' => 'to level', // TRANSLATE
		'select_export' => 'Pour exporter une entrée cochez la case à cocher correspondante dans l&rsquo;arbre de fichier. Important: Tous les elements marqués de tous les branches seront exporter et si vous exporter un répertoire tous les documents de ce répertoire seront exporter également!',
		'templates' => 'Templates', // TRANSLATE
		'classes' => 'Classes', // TRANSLATE

		'nothing_to_delete' => "Rien à supprimer.",
		'nothing_to_save' => 'Rien à enregistrer!',
		'no_perms' => 'Vous n\'êtes pas authorisé!',
		'new' => 'Nouveau',
		'export' => 'Export', // TRANSLATE
		'group' => 'Groupe',
		'save' => 'Enregistrer',
		'delete' => 'Supprimer',
		'quit' => 'Quitter',
		'property' => 'Préférences',
		'name' => 'Nom',
		'save_to' => 'Enregistrer sous:',
		'selection' => 'Séléction',
		'save_ok' => 'L\'Export a été enregistré.',
		'save_group_ok' => 'Le Groupe a été enregistré.',
		'log' => 'Journal',
		'start_export' => 'Export démarre',
		'prepare' => 'Préparation de l\'Export...',
		'doctype' => 'Type-de-Document',
		'category' => 'Catégorie',
		'end_export' => 'Export terminé',
		'newFolder' => "Nouveau Groupe",
		'folder_empty' => "Le Groupe est vide",
		'folder_path_exists' => "Ce Groupe existe déjà!",
		'wrongtext' => "Ce nom n'est pas valide!",
		'wrongfilename' => "The filename is not valid!", // TRANSLATE
		'folder_exists' => "Ce groupe existe déjà!",
		'delete_ok' => 'L\'Export a été supprimé.',
		'delete_nok' => 'Erreur: L\'Export n\'a pas été supprimé',
		'delete_group_ok' => 'Le Groupe a été supprimé.',
		'delete_group_nok' => 'Erreur: Le Groupe n\'a pas été supprimé',
		'delete_question' => 'Voulez-vous supprimé l\'Export actuel?',
		'delete_group_question' => 'Voulez-vous supprimé le Groupe actuel?',
		'download_starting2' => 'Le téléchargement du fichier Export a été démarré.',
		'download_starting3' => 'Si le téléchargement ne démarre pas dans 10 secondes,',
		'working' => 'en train...',
		'txt_document_options' => 'Le modèle standard est le modèle qui a été defini dans les propriétés du Document. Les documents inclus sont les documents internes qui sont inclus par les Tags we:include, we:form, we:url, we:linkToSeeMode, we:a, we:href, we:link, we:css, we:js et we:addDelNewsletterEmail dans le document exporté. Ces Objects sonst les object qui sont inclus par les Tags we:objekt et we:form  dans le document exporté. Les documenst liés sont les documents internes qui sont lié par les Tags-HTML body, a, img, table, td au document exporté.',
		'txt_object_options' => 'Die Standard-Klasse ist die Klasse die in den Objekt-Eigenschaften definiert wurde.',
		'txt_exportdeep_options' => 'La Profondeur d\'Export est la profondeur jusqu\'à la quelle les documents ou bien objects seront exportés. Le champ doit être numerique!',
		'name_empty' => 'Le nom ne doit pas être vide!',
		'name_exists' => 'Le nom existe déjà!',
		'help' => 'Aide',
		'info' => 'Info', // TRANSLATE
		'path_nok' => 'Le chemin n\'est pas valide',
		'must_save' => "L'export a été modififié.\\nVous devez d'abord enregistrer les données de l'export avant que vous puissiez démarrer l'export!",
		'handle_owners_option' => 'Données de l\'utilisateur',
		'handle_owners' => 'Exporter les données de l\'utilisateur.',
		'txt_owners' => 'Exporter les données de l\'utilisateur liées',
		'weBinary' => 'File', // TRANSLATE
		'handle_navigation' => 'Navigation', // TRANSLATE
		'weNavigation' => 'Navigation', // TRANSLATE
		'weNavigationRule' => 'Navigation rule', // TRANSLATE
		'weThumbnail' => 'Thumbnails', // TRANSLATE
		'handle_thumbnails' => 'Thumbnails', // TRANSLATE

		'navigation_hint' => 'To export the navigation entries, the template on which the navigation is displayed has also to be exported!',
		'title' => "Assistant d'Export",
		'selection_type' => 'Définer la séléction des éléments',
		'auto_selection' => 'Séléction automatique',
		'txt_auto_selection' => "Exporte automatiquement - d'après le type-de-document ou la classe - les documents et object séléctionés.",
		'manual_selection' => 'Séléction manuelle',
		'txt_manual_selection' => 'Exporte manuellement des documents ou objects séléctionés.',
		'element' => "Séléction d'Élément",
		'select_elements' => 'Séléctioner les éléments à importer.',
		'select_docType' => "S'il vous plaît séléctioner un typ de document ou un modèle.",
		'none' => '-- aucun --',
		'doctype' => 'Type',
		'template' => 'Modèle',
		'categories' => 'Catégories',
		'documents' => 'Documents', // TRANSLATE
		'objects' => 'Objects', // TRANSLATE
		'class' => 'Classe',
		'isDynamic' => 'Créer le site dynamiquement',
		'extension' => 'Extension', // TRANSLATE
		'wexml_export' => 'Export weXML',
		'filename' => 'Nom de fichier',
		'extra_data' => 'Donnée supplémentaire',
		'integrated_data' => 'Importer les données intégrées',
		'integrated_data_txt' => 'Choisissez cette option, si les données resp. documents inclues par les modèles doivent être exporter.',
		'max_level' => "jusqu'au plan",
		'export_doctypes' => 'exporter des types de document',
		'export_categories' => 'exporter des catégories',
		'export_location' => 'Exporter à',
		'local_drive' => 'Disc dur locale',
		'server' => 'Serveur',
		'export_progress' => 'Export en train',
		'prepare_progress' => 'Préparation',
		'finish_progress' => 'Terminé',
		'finish_export' => "L'export a été terminé avec succès!",
);