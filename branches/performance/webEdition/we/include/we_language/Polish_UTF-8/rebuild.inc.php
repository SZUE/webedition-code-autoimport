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
		'rebuild_maintable' => "Zapisz tabelę główną",
		'rebuild_tmptable' => "Tymczasową tabelę zapisać na nowo",
		'rebuild_objects' => "Obiekty",
		'rebuild_index' => "Index-Tabela",
		'rebuild_all' => "Wszystkie dokumenty i szablony",
		'rebuild_templates' => "Wszystkie szablony",
		'rebuild_filter' => "Statyczne strony webEdition",
		'rebuild_thumbnails' => "Rebuild - utwórz miniaturę",
		'txt_rebuild_documents' => "With this option the documents and templates saved in the data base will be written to the file system.", // TRANSLATE
		'txt_rebuild_objects' => "Należy wybrać tą opcję aby zapisać tabelę od nowa. Jest to tylko wtedy konieczne kiedy wystąpił błąd w tabeli.",
		'txt_rebuild_index' => "If in search some documents can not be found or are being found several times, you can rewrite the index table thus the search index here.", // TRANSLATE
		'txt_rebuild_thumbnails' => "Tutaj można od nowa zapisać podgląd miniatury obrazków.",
		'txt_rebuild_all' => "Tą opcją można wszystkie dokumenty i szablony zapisać od nowa.",
		'txt_rebuild_templates' => "Tą opcją można wszystkie szablony zapisać od nowa.",
		'txt_rebuild_filter' => "Tutaj można wyszczególnić, które statyczne strony webEdition mają zostać zapisane od nowa. Nie wybranie żadnych kryteriów powoduje zapisanie wszystkich dokumentów od nowa.",
		'rebuild' => "Rebuild", // TRANSLATE
		'dirs' => "Katalogi",
		'thumbdirs' => "Dla grafik z następujących katalogów",
		'thumbnails' => "Utwórz miniatury",
		'documents' => "Documents and templates", // TRANSLATE
		'catAnd' => "Funkcja AND",
		'finished' => "Rebuild zakończono pomyślnie!",
		'nothing_to_rebuild' => "Brak dokumentów, które odpowiadałyby kryteriom!",
		'no_thumbs_selected' => "Proszę wybrać przynajmniej jedną miniaturę!",
		'savingDocument' => "Zapisz dokument: ",
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