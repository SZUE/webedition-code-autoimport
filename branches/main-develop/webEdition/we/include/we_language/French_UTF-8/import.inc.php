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
 * Language file: import.inc.php
 * Provides language strings.
 * Language: English
 */
$l_import = array(
		'title' => 'Assistant-d\'Import',
		'wxml_import' => 'Import XML de webEdition  ',
		'gxml_import' => 'Import Générique de XML ',
		'csv_import' => 'Import de CSV',
		'import' => 'Importer',
		'none' => '-- Aucun --',
		'any' => '-- Sans --',
		'source_file' => 'Fichier de Source',
		'import_dir' => 'Répertoire de Cible',
		'select_source_file' => 'S\'il vous plaît choisissez un fichier source.',
		'we_title' => 'Titre',
		'we_description' => 'Texte de Déscription',
		'we_keywords' => 'Mots-clé',
		'uts' => 'Unix-Timestamp', // TRANSLATE
		'unix_timestamp' => 'Le timestamp est un format qui indique le nombre de secondes écoulées depuis le début de lépoque Unix (01.01.1970).',
		'gts' => 'GMT Timestamp', // TRANSLATE
		'gmt_timestamp' => 'General Mean Time ou bien Greenwich Mean Time (GMT).',
		'fts' => 'Propre format',
		'format_timestamp' => 'Dans la chaîne format est symbol suivant sont permit: Y (Année, 4 chiffres), y (Année, 2 chiffres), m (Mois au format numérique, avec zéros initiaux), n (Mois sans les zéros initiaux), d (Mois sans les zéros initiaux), j (Mois sans les zéros initiaux), H (Heure, au format 24h, avec les zéros initiaux), G (	Heure, au format 24h, sans les zéros initiaux), i (Minutes avec les zéros initiaux), s (	Secondes, avec zéros initiaux)',
		'import_progress' => 'Importer',
		'prepare_progress' => 'Préparation',
		'finish_progress' => 'Terminé',
		'finish_import' => 'L\'import a été terminé avec succès!',
		'import_file' => 'Import de fichier',
		'import_data' => 'Import de données',
		'import_templates' => 'Template import', // TRANSLATE
		'template_import' => 'First Steps Wizard', // TRANSLATE
		'txt_template_import' => 'Import ready example templates and template sets from the webEdition server', // TRANSLATE
		'file_import' => 'Importer des fichiers locals',
		'txt_file_import' => 'Importer une ou plusieurs fichiers du disque dur local.',
		'site_import' => 'Importer des fichiers du serveur',
		'site_import_isp' => 'Importer',
		'txt_site_import_isp' => 'Importer des graphique d\'un répertoire serveur.Choisissez, quelles graphiques doivent être importées.',
		'txt_site_import' => 'Importer des fichiers d\'un répertoire de serveur. Choisissez par les options de filtre si des Graphiques, des site-HTML, des vidéo-Flash, Fichier-JavaScript,des Fichier-CSS, des Document de texte clair ou autre document doivent être importés',
		'txt_wxml_import' => 'Des fichiers XML de webEdition contiennent des informations sur les documents de webEdition webEdition. Définissez dans quel répertoire les objects et documents seront importés.',
		'txt_gxml_import' => 'Import "flat" XML files, such as those provided by phpMyAdmin. The dataset fields have to be allocated to the webEdition dataset fields. Use this to import XML files exported from webEdition without the export module.', // TRANSLATE
		'txt_csv_import' => 'Import des fichier-CSV (Comma Separated Values) ou des formats comparables (p.ex. *.txt). Les champs d\'enregistrement vont être assignés à des champs de webEdtion.',
		'add_expat_support' => 'L\'interface d\'import XML nécessite l\'extension XML expat de James Clark. Compilez PHP de nouveau avec l\'extension expat, pour que la functionalité de l\'import XML est garanti.',
		'xml_file' => 'Fichier-XML',
		'templates' => 'Modèle',
		'classes' => 'Classes', // TRANSLATE
		'predetermined_paths' => 'Chemins allégués',
		'maintain_paths' => 'Conserver les chemin',
		'import_options' => 'Outils d\'Import',
		'file_collision' => 'Si un fichier existe',
		'collision_txt' => 'Important des fichiers dans un répertoire, qui contient déja un fichier avec le même nom, peut créer des conflits. Définissez comment l\'assistant-d\'import doit traiter ces fichiers.',
		'replace' => 'Remplacer',
		'replace_txt' => 'Supprimer le fichier existant et remplacer avec les entrées du fichier actuel.',
		'rename' => 'Renommer',
		'rename_txt' => 'Au nom du fichier sera ajouté une ID univalent. Tous les liens, qui mennent au fichier, seront réajusté.',
		'skip' => 'Enjamber',
		'skip_txt' => 'En enjambant le fichier actuel le fichier existant sera préservé.',
		'extra_data' => 'Données extra',
		'integrated_data' => 'Importer les données inlcues',
		'integrated_data_txt' => 'Choisissez cette option, si les données ou bien documents inclues par les modèles doivent être importés.',
		'max_level' => 'jusqu\'au plan',
		'import_doctypes' => 'Importer des types de documents',
		'import_categories' => 'Importer des catégories',
		'invalid_wxml' => 'Il n\'est possible d\'importer que des fichiers-XML, qui correspondent à la Document-Type-Definition (DTD) de webEdition.',
		'valid_wxml' => 'Le fichier-XML est bien formé est valide, c\'est-à-dire il correspond à la Document-Type-Definition (DTD) de webEdition.',
		'specify_docs' => 'S\'il vous plaît choisissez les documents, que vous voulez importer.',
		'specify_objs' => 'S\'il vous plaît choisissez les objects, que vous voulez importer.',
		'specify_docs_objs' => 'S\'il vous plaît choisissez les documents et/ou documents que vous voulez importer.',
		'no_object_rights' => 'Vous n\'êtes pas authorisé d\'importer des objects .',
		'display_validation' => 'afficher la Validation-XML',
		'xml_validation' => 'Validation-XML',
		'warning' => 'Avertissement',
		'attribute' => 'Attribut',
		'invalid_nodes' => 'noeud-XML invalide à la position ',
		'no_attrib_node' => 'Manque de l\'element-XML "attrib" à la position ',
		'invalid_attributes' => 'Attributs invalides à la position ',
		'attrs_incomplete' => 'la liste des attributs définits comme #required et #fixed est imcomplète à la position ',
		'wrong_attribute' => 'le nom d\'attribut n\'a été défini ni comme #required et ni comme #implied à la position ',
		'documents' => 'Documents', // TRANSLATE
		'objects' => 'Objects', // TRANSLATE
		'fileselect_server' => 'Charger le fichier source du serveur',
		'fileselect_local' => 'Charger le fichier source du disque dur local',
		'filesize_local' => 'Le fichier à télécharger ne doit être plus grand que %s à cause des limitation de PHP!',
		'xml_mime_type' => 'Le fichier choisi ne peut pas être importé. Type-Mime:',
		'invalid_path' => 'Le chemin du fichier source est invalide.',
		'ext_xml' => 'S\'il vous plaît choisissez un fichier source avec une extension de fichier ".xml".',
		'store_docs' => 'Répertoire source des documents',
		'store_tpls' => 'Répertoire source des modèles',
		'store_objs' => 'Répertoire source des objects',
		'doctype' => 'Document type',
		'gxml' => 'XML Générique',
		'data_import' => 'Importer des Données',
		'documents' => 'Documents', // TRANSLATE
		'objects' => 'Objects', // TRANSLATE
		'type' => 'Typ',
		'template' => 'Modèle',
		'class' => 'Classe',
		'categories' => 'Catégories',
		'isDynamic' => 'Créer le site dynamiquement',
		'extension' => 'Extension', // TRANSLATE
		'filetype' => 'Typ de fichier',
		'directory' => 'Répertoire',
		'select_data_set' => 'Choisir un enregistrement',
		'select_docType' => 'S\'il vous plaît choisissez un modèle.',
		'file_exists' => 'Le fichier source choisi n\'existe pas. S\'il vous plaît vérifié le chemin, chemin: ',
		'file_readable' => 'Le fichier source choisi, n\'a pas lisible et ne peut pas être importé alors.',
		'asgn_rcd_flds' => 'Assigner les champs de données',
		'we_flds' => 'Champs de webEdition',
		'rcd_flds' => 'Champs d\'enregistrement',
		'name' => 'Nom',
		'auto' => 'automatiquement',
		'asgnd' => 'assigné',
		'pfx' => 'Préfix',
		'pfx_doc' => 'Document', // TRANSLATE
		'pfx_obj' => 'Object', // TRANSLATE
		'rcd_fld' => 'Champ d\'enregistrement',
		'import_settings' => 'Préférences d\'Import',
		'xml_valid_1' => 'Le fichier-XML est valide et contient',
		'xml_valid_s2' => 'Elements. Choisissez les elements, que vous voulez importés.',
		'xml_valid_m2' => 'Des noeuds-XML-enfant au premier plan avec des noms differents. S\'il vous plaît choisissez le noeud-XML et le nombre d\'elements que vous voulez importer.',
		'well_formed' => 'Le document-XML est bien formé.',
		'not_well_formed' => 'Le document-XML n\'est pas bien formé et ne peut pas être importé.',
		'missing_child_node' => 'Le document-XML est bien formé, mais ne contient pas de noeuds-XML et ne peut pas être importé alors.',
		'select_elements' => 'S\'il vous plaît choisissez les enregistrement, que vous voulez importer.',
		'num_elements' => 'S\'il vous plaît choisissez un nombre d\'enregistrement entre 1 et ',
		'xml_invalid' => '', // TRANSLATE
		'option_select' => 'Séléction..',
		'num_data_sets' => 'Enregistrements:',
		'to' => 'à',
		'assign_record_fields' => 'Assigner les champs donnée',
		'we_fields' => 'Champs de webEdition',
		'record_fields' => 'Champs d\'enregistrement',
		'record_field' => 'Champ d\'enregistrement',
		'attributes' => 'Attribut',
		'settings' => 'Préférences',
		'field_options' => 'Outils de champ',
		'csv_file' => 'Fichier CSV',
		'csv_settings' => 'Préférences CSV',
		'xml_settings' => 'Préférences XML',
		'file_format' => 'Format de fichier',
		'field_delimiter' => 'Séperateur',
		'comma' => ', {Virgule}',
		'semicolon' => '; {Point Virgule}',
		'colon' => ': {Double-Point}',
		'tab' => "\\t {Tab}",
		'space' => '  {Éspace}',
		'text_delimiter' => 'Limiteur de Texte',
		'double_quote' => '" {Guillemets}',
		'single_quote' => '\' {Apostrophe}',
		'contains' => 'La première ligne contient le nom de champ',
		'split_xml' => 'Importer les enregistrements un par un',
		'wellformed_xml' => 'Vérifier si le document est bien formé',
		'validate_xml' => 'Validation-XML',
		'select_csv_file' => 'S\'il vous plaît choissisez un fichier source CSV.',
		'select_seperator' => 'S\'il vous plaît choissisez un sépérateur.',
		'format_date' => 'Format de date',
		'info_sdate' => 'Choisissez le format de date pour le champ webEdition',
		'info_mdate' => 'Choisissez le format de date pour les champs webEdition',
		'remark_csv' => 'Vous pouvez importer des fichiers-CSV(Comma Separated Values) ou des formats comparable (p.ex. *.txt). Avec l\'import de ces format de fichier il est possible de choisir le séperateur (p.ex , ; Tab, Éspace) et le limiteur de texte (= le signe, qui emballe le texte).',
		'remark_xml' => 'Choisissez l\'option \"Importez les enregistrement isolément \", pour que des grandes fichier peuvent être importé dans le temps de timeout d\'un script PHP.<br> Si vous n\'êtes pas sûr que le fichier séléctioné est un document webEdition-XML, vous pouvez vérifier s\'il est bien formé et valide.',
		'import_docs' => "Importer des Documents",
		'import_templ' => "Importer des Modèles",
		'import_objs' => "Importer des Objects",
		'import_classes' => "Importer des Classes",
		'import_doctypes' => "Importer des types de documents",
		'import_cats' => "Importer des Catégories",
		'documents_desc' => "S\'il vous plaît saisissez un répertoire, dans lequel les documents seront importés . Si l\'option \"Conserver les chemin\" est choisi les chemins correspondant seront réproduit automatiquement, autrement les chemins seront ignorés.",
		'templates_desc' => "S\'il vous plaît saisissez un répertoire, dans lequel les modèles seront importés . Si l\'option \"Conserver les chemin\" est choisi les chemins correspondant seront réproduit automatiquement, autrement les chemins seront ignorés.",
		'handle_document_options' => 'Documents', // TRANSLATE
		'handle_template_options' => 'Modèles',
		'handle_object_options' => 'Objects', // TRANSLATE
		'handle_class_options' => 'Classe',
		'handle_doctype_options' => "Type-de-Document",
		'handle_category_options' => "Categories",
		'log' => 'Détails',
		'start_import' => 'Import démarre',
		'prepare' => 'Préparation...',
		'update_links' => 'Actualisations-Liens...',
		'doctype' => 'Type-de-Document',
		'category' => 'Catégorie',
		'end_import' => 'Import terminé',
		'handle_owners_option' => 'Données de l\'utilisateur',
		'txt_owners' => 'Importer les données de l\'utilisateur.',
		'handle_owners' => 'Restaurer les données de l\'utilisateur',
		'notexist_overwrite' => 'Si l\'utilisateur n\'existe pas, l\'option "Effacer les données de l\'utilisateur" sera utilisée',
		'owner_overwrite' => 'Effacer les données de l\'utilisateur',
		'name_collision' => 'Collision de nom',
		'item' => 'Article', // TRANSLATE
		'backup_file_found' => 'Le fichier ressemble à un fichier de sauvegarde de webEdition. S\'il vous plaît utilisez l\'option \"Sauvegarde\" du menu \"Fichier\" pour importer les données.',
		'backup_file_found_question' => 'Voulez-vous fermer maintenant le dialogue actuel et démarrer l\'assistant-de sauvegarde?',
		'close' => 'Fermer',
		'handle_file_options' => 'Fichiers',
		'import_files' => 'Importer les fichiers',
		'weBinary' => 'Fichier',
		'format_unknown' => 'Le format du fichier est inconnu!',
		'customer_import_file_found' => 'Le fichier ressemble à un fichier d\'import du gestion clients. S\'il vous plaît utilisez l\'option \"Import/Export\" du gestio clients (PRO) pour importer les données.',
		'upload_failed' => 'Le fichier ne peut pas être téléchargé. S\'il vous plaît verifiez, si la taille du fichier dépasse %s',
		'import_navigation' => 'Import navigation', // TRANSLATE
		'weNavigation' => 'Navigation', // TRANSLATE
		'navigation_desc' => 'Select the directory where the navigation will be imported.', // TRANSLATE
		'weNavigationRule' => 'Navigation rule', // TRANSLATE
		'weThumbnail' => 'Thumbnail', // TRANSLATE
		'import_thumbnails' => 'Import thumbnails', // TRANSLATE
		'rebuild' => 'Rebuild', // TRANSLATE
		'rebuild_txt' => 'Automatic rebuild', // TRANSLATE
		'finished_success' => 'The import of the data was successful.', // TRANSLATE

		'encoding_headline' => 'Charset', // TRANSLATE
		'encoding_noway' => 'A conversion  is only possible between ISO-8859-1 and UTF-8 <br/>and with a set default charset (settings dialog)', // TRANSLATE
		'encoding_change' => "Change, from '", // TRANSLATE
		'encoding_XML' => '', // TRANSLATE
		'encoding_to' => "' (XML file) to '", // TRANSLATE
		'encoding_default' => "' (standard)", // TRANSLATE
);