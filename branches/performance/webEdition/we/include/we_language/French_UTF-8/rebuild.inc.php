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
 * Language file: rebuild.inc.php
 * Provides language strings.
 * Language: English
 */
$l_rebuild = array(
		'rebuild_documents' => "Rebuild - documents", // TRANSLATE
		'rebuild_maintable' => "Enregistrer de nouveau le tableau principal",
		'rebuild_tmptable' => "Enregistrer de nouveau le tableau temporaire",
		'rebuild_objects' => "Objects", // TRANSLATE
		'rebuild_index' => "Tableau Index",
		'rebuild_all' => "Tous les Documents et Modèles",
		'rebuild_templates' => "Tous les Modèles",
		'rebuild_filter' => "Les sites-webEdition statiques",
		'rebuild_thumbnails' => "Rebuild - Créer les Imagettes",
		'txt_rebuild_documents' => "With this option the documents and templates saved in the data base will be written to the file system.", // TRANSLATE
		'txt_rebuild_objects' => "Choisissez cette option pour récrire de nouveau les tableaux d'objects. Celà est seulement nécéssaire si les tableaux d'objects sont défectueux.",
		'txt_rebuild_index' => "If in search some documents can not be found or are being found several times, you can rewrite the index table thus the search index here.", // TRANSLATE
		'txt_rebuild_thumbnails' => "Ici vous pouvez récrire les imagettes de vos graphiques.",
		'txt_rebuild_all' => "Avec cette option tous les documents et modèles seront récrit.",
		'txt_rebuild_templates' => "Avec cette option tous les modèles seront récrit.",
		'txt_rebuild_filter' => "Ici vous pouvez definir quels sites statiques seront récrit. Si vous ne choisissez aucun critère tous le sites statiques seront récrit.",
		'rebuild' => "Rebuild", // TRANSLATE
		'dirs' => "Répertoires",
		'thumbdirs' => "Les graphiques pour les répertoires suivant",
		'thumbnails' => "Créer les Imagettes",
		'documents' => "Documents and templates", // TRANSLATE
		'catAnd' => "Opération ET",
		'finished' => "Le Rebuild a été terminé avec succès!",
		'nothing_to_rebuild' => "Il n'exite pas de documents, qui corresponds au critère choisi!",
		'no_thumbs_selected' => "S'il vous plaît choisissez au moins une imagettes!",
		'savingDocument' => "Enregistre le document: ",
		'navigation' => "Navigation", // TRANSLATE
		'rebuild_navigation' => "Rebuild - Navigation", // TRANSLATE
		'txt_rebuild_navigation' => "Here you can rewrite the navigation cache.", // TRANSLATE
		'rebuildStaticAfterNaviCheck' => 'Rebuild static documents afterwards.', // TRANSLATE
		'rebuildStaticAfterNaviHint' => 'For static navigation entries a rebuild of the corresponding documents is necessary, in addition.', // TRANSLATE
		'metadata' => 'Meta data fields', // TRANSLATE
		'txt_rebuild_metadata' => 'To import the meta data of your images subsequently, choose this option.', // TRANSLATE  // TRANSLATE
		'rebuild_metadata' => 'Rebuild - meta data fields', // TRANSLATE
		'onlyEmpty' => 'Import only empty meta data fields', // TRANSLATE
		'expl_rebuild_metadata' => 'Select the meta data fields you want to import. To import only fields which already have no content, select the option "Import only empty meta data fields".', // TRANSLATE // TRANSLATE
		'noFieldsChecked' => "Al least one meta data field must be selected!", // TRANSLATE // TRANSLATE
);