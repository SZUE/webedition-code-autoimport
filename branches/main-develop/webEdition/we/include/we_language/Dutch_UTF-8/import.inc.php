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
		'title' => 'Importeer Hulp',
		'wxml_import' => 'webEdition XML import', // TRANSLATE
		'gxml_import' => 'Generieke XML import',
		'csv_import' => 'CSV import', // TRANSLATE
		'import' => 'Bezig met importeren',
		'none' => '-- geen --',
		'any' => '-- geen --',
		'source_file' => 'Bron bestand',
		'import_dir' => 'Doel directory',
		'select_source_file' => 'Kies a.u.b. een bron bestand.',
		'we_title' => 'Titel',
		'we_description' => 'Omschrijving',
		'we_keywords' => 'Kernwoorden',
		'uts' => 'Unix-Tijdstempel',
		'unix_timestamp' => 'De unix tijd stempel is een manier om de tijd te volgen naar het totaal aantal verstreken seconden. Deze telling begon tijdens de Unix Expo op 1 Januari 1970.',
		'gts' => 'GMT Tijdstempel',
		'gmt_timestamp' => 'General Mean Time bijv. Greenwich Mean Time (GMT).',
		'fts' => 'Gespecificeerd format',
		'format_timestamp' => 'De volgende karakters zijn herkend in de format parameter string: Y (een volledig numerieke representatie van een jaar, 4 getallen), y (een 2-cijferige representatie van een jaar), m (numerieke representatie van een maand, voorafgaand door een 0), n (numerieke representatie van een maand, zonder 0), d (dag van de maand, 2 cijfers voorafgaand door een 0), j (dag van de maand, zonder 0), H (24-uurs aanduiding van een uur voorafgaand door een 0), G (24-uurs aanduiding van een uur, zonder 0), i (minuten voorafgaand door een 0), s (voorafgaand door een 0)',
		'import_progress' => 'Bezig met importeren',
		'prepare_progress' => 'Bezig met voorbereiden',
		'finish_progress' => 'Voltooid',
		'finish_import' => 'Het importeren was succesvol!',
		'import_file' => 'Bestands import',
		'import_data' => 'Gegevens import',
		'import_templates' => 'Template import', // TRANSLATE
		'template_import' => 'First Steps Wizard', // TRANSLATE
		'txt_template_import' => 'Import ready example templates and template sets from the webEdition server', // TRANSLATE
		'file_import' => 'Importeer lokale bestanden',
		'txt_file_import' => 'Importeer één of meerdere bestand vanaf lokale harde schijf.',
		'site_import' => 'Importeer bestanden vanaf server',
		'site_import_isp' => 'Importeer afbeeldingen vanaf server',
		'txt_site_import_isp' => 'Importeer afbeeldingen vanaf de hoofd-directory van de server. Stel filter opties in om te kiezen welke afbeeldingen er geïmporteerd moeten worden.',
		'txt_site_import' => 'Importeer bestanden vanaf de hoofd-directory van de server. Stel filter opties in om te kiezen of afbeeldingen, HTML pagina s, Flash, JavaScript, of CSS bestanden, platte-tekst documenten, of andere bestands typen geïmporteerd moeten worden.',
		'txt_wxml_import' => 'webEdition XML bestanden bevatten imformatie over webEdition documenten, sjablonen of objecten. Kies een directory waarin de bestanden geïmporteerd moeten worden.',
		'txt_gxml_import' => 'Importeer "platte" XML bestanden, zoals geleverd door phpMyAdmin. De dataset velden moeten toegekend worden aan de webEdition dataset velden. Gebruik dit om XML bestanden te importeren die geëxporteerd zijn met webEdition zonder de export module.',
		'txt_csv_import' => 'Importeer CSV bestanden (Komma gescheiden waardes) of aangepaste tekst formaten (bijv. *.txt). De dataset velden zijn ingedeeld bij de webEdition velden.',
		'add_expat_support' => 'Om support voor de XML expat parser te implementeren, moet u de PHP opnieuw samenstellen om support toe te voegen (voor deze bibliotheek) aan uw PHP opbouw. De expat extensie, gecreeërd door James Clark, kunt u vinden bij http://www.jclark.com/xml/.',
		'xml_file' => 'XML bestand',
		'templates' => 'Sjablonen',
		'classes' => 'Classen',
		'predetermined_paths' => 'Pad instellingen',
		'maintain_paths' => 'Behoud paden',
		'import_options' => 'Import opties',
		'file_collision' => 'Bestands botsing',
		'collision_txt' => 'Wanneer u een bestand importeert in een folder die een bestand bevat met dezelfde naam, onstaat er een bestandsnaam botsing. U kunt opgeven wat de importeer wizard moet doen met de nieuwe en bestaande bestanden.',
		'replace' => 'Vervang',
		'replace_txt' => 'Verwijder het huidige bestand en vervang het door het nieuwe bestand.',
		'rename' => 'Hernoem',
		'rename_txt' => 'Ken een unieke naam toe aan het nieuwe bestand. Alle koppelingen worden aangepast aan de nieuwe bestands naam.',
		'skip' => 'Sla over',
		'skip_txt' => 'Sla het huidige bestand over en behoud beide kopieën op de originele locaties.',
		'extra_data' => 'Extra gegevens',
		'integrated_data' => 'Importeer geïntegreerde data',
		'integrated_data_txt' => 'Selecteer deze optie om geïntegreerde data van sjablonen of documenten te importeren.',
		'max_level' => 'naar niveau',
		'import_doctypes' => 'Importeer doctypes',
		'import_categories' => 'Importeer categorieën',
		'invalid_wxml' => 'Het XML document is goed gevormd maar niet geldig. Het webEdition document type definitie (DTD) ontbreekt.',
		'valid_wxml' => 'Het XML document is goed gevormd en geldig.  Het webEdition document type definitie (DTD) is aanwezig.',
		'specify_docs' => 'Kies a.u.b. de documenten voor import.',
		'specify_objs' => 'Kies a.u.b. de objecten voor import.',
		'specify_docs_objs' => 'Kies a.u.b. of documenten en objecten geïmporteerd moeten worden.',
		'no_object_rights' => 'U heeft geen toestemming om objecten te importeren.',
		'display_validation' => 'Toon XML validatie',
		'xml_validation' => 'XML validatie',
		'warning' => 'Waarschuwing',
		'attribute' => 'Attribuut',
		'invalid_nodes' => 'Ongeldige XML node op plek ',
		'no_attrib_node' => 'Geen XML element "attrib" op plek ',
		'invalid_attributes' => 'Ongeldige attributen op plek ',
		'attrs_incomplete' => 'De lijst van #vereiste en #vaste attributen is inkompleet op plek ',
		'wrong_attribute' => 'De attribuut naam niet gedefineerd als #vereist nog als #geïmpliceerd op plek ',
		'documents' => 'Documenten',
		'objects' => 'Objecten',
		'fileselect_server' => 'Laad bestand vanaf server',
		'fileselect_local' => 'Upload bestand vanaf lokale hard disk',
		'filesize_local' => 'Vanwege restricties binnen PHP, mag het te uploaden bestand niet groter zijn dan %s.',
		'xml_mime_type' => 'Het geselecteerde bestand kan niet geïmporteerd worden. Mime-type:',
		'invalid_path' => 'Het pad van het bron bestand is ongeldig.',
		'ext_xml' => 'Selecteer a.u.b. een bron bestand met de extensie ".xml".',
		'store_docs' => 'Doel directory documenten',
		'store_tpls' => 'Doel directory sjablonen',
		'store_objs' => 'Doel directory objecten',
		'doctype' => 'Document type',
		'gxml' => 'Generiek XML',
		'data_import' => 'Importeer data',
		'documents' => 'Documenten',
		'objects' => 'Objecten',
		'type' => 'Type', // TRANSLATE
		'template' => 'Sjabloon',
		'class' => 'Class', // TRANSLATE
		'categories' => 'Categorieën',
		'isDynamic' => 'Genereer pagina dynamisch',
		'extension' => 'Extensie',
		'filetype' => 'Bestandstype',
		'directory' => 'Directorie',
		'select_data_set' => 'Selecteer dataset',
		'select_docType' => 'Kies a.u.b. een document type of een sjabloon.',
		'file_exists' => 'Het gekozen bron bestand bestaat niet. Controleer a.u.b. het opgegeven pad. Pad: ',
		'file_readable' => 'Het gekozen bron bestand is niet leesbaar en kan daardoor niet geïmporteerd worden.',
		'asgn_rcd_flds' => 'Ken data velden toe',
		'we_flds' => 'webEdition&nbsp;velden',
		'rcd_flds' => 'Dataset&nbsp;velden',
		'name' => 'Naam',
		'auto' => 'Automatisch',
		'asgnd' => 'Toegekend',
		'pfx' => 'Voorvoegsel',
		'pfx_doc' => 'Document', // TRANSLATE
		'pfx_obj' => 'Object', // TRANSLATE
		'rcd_fld' => 'Dataset veld',
		'import_settings' => 'Import instellingen',
		'xml_valid_1' => 'Het XML bestand is geldig en bevat',
		'xml_valid_s2' => 'elementen. Selecteer de te importeren elementen.',
		'xml_valid_m2' => 'XML child node in het eerste niveau met verschillende namen. Kies a.u.b. de XML node en het aantal elementen die geïmporteerd moeten worden.',
		'well_formed' => 'Het XML document is goed gevormd.',
		'not_well_formed' => 'Het XML document is niet goed gevormd en kan niet geïmporteerd worden.',
		'missing_child_node' => 'Het XML document is goed gevormd, maar bevat geen XML nodes en kan daardoor niet geïmporteerd worden.',
		'select_elements' => 'Kies a.u.b. de te importeren datasets.',
		'num_elements' => 'Kies a.u.b. het aantal datasets van 1 tot ',
		'xml_invalid' => '', // TRANSLATE
		'option_select' => 'Selectie..',
		'num_data_sets' => 'Datasets:', // TRANSLATE
		'to' => 'tot',
		'assign_record_fields' => 'Ken data velden toe',
		'we_fields' => 'webEdition velden',
		'record_fields' => 'Dataset velden',
		'record_field' => 'Dataset veld ',
		'attributes' => 'Attributen',
		'settings' => 'Instellingen',
		'field_options' => 'Veld opties',
		'csv_file' => 'CSV bestand',
		'csv_settings' => 'CSV instellingen',
		'xml_settings' => 'XML instellingen',
		'file_format' => 'Bestands formaat',
		'field_delimiter' => 'Scheidingsteken',
		'comma' => ', {komma}',
		'semicolon' => '; {punt komma}',
		'colon' => ': {dubbele punt}',
		'tab' => "\\t {tab}", // TRANSLATE
		'space' => '  {spatie}',
		'text_delimiter' => 'Tekst scheiding',
		'double_quote' => '" {dubbele quote}',
		'single_quote' => '\' {enkele quote}',
		'contains' => 'Eerste regel bevat veld naam',
		'split_xml' => 'Importeer datasets sequentieël',
		'wellformed_xml' => 'Validatie voor goed gevormde XML',
		'validate_xml' => 'XML validiatie',
		'select_csv_file' => 'Kies a.u.b. een CSV bron bestand.',
		'select_seperator' => 'Kies a.u.b. een scheidingsteken.',
		'format_date' => 'Datum formaat',
		'info_sdate' => 'Selecteer het datum formaat voor het webEdition veld',
		'info_mdate' => 'Selecteer het datum formaat voor de webEdition velden',
		'remark_csv' => 'U heeft de mogelijkheid om CSV bestanden (Comma Seperated Values) of aangepaste tekst formaten (e. g. *.txt) te importeren. Het afbakenen van velden (bijv. , ; tab, spatie) en tekst (= welke de tekst invoer kort samenvat) kan vooraf ingesteld worden bij het importeren van deze bestands formaten.',
		'remark_xml' => 'Om de vooraf bepaalde timeout van een PHP-script te omzeilen, selecteer de optie "Importeer data-sets afzonderlijk", bij het importeren van grote bestanden.<br>Als u niet zeker weet of het geselecteerde bestand webEdition XML is of niet, kunt u het bestand testen voor validiteit en syntax.',
		'import_docs' => "Importeer documenten",
		'import_templ' => "Importeer sjablonen",
		'import_objs' => "Importeer objecten",
		'import_classes' => "Importeer classen",
		'import_doctypes' => "Importeer DocTypes",
		'import_cats' => "Importeer categorieën",
		'documents_desc' => "Selecteer de directory waar de documenten geïmporteerd moeten worden. Als de optie \"Behoud paden\" is gekozen, worden de document paden hersteld, anders worden de document paden genegeerd.",
		'templates_desc' => "Selecteer de directory waar de sjablonen geïmporteerd moeten worden. Als de optie \"Behoud paden\" is gekozen, worden de sjabloon paden hersteld, anders worden de sjabloon paden genegeerd.",
		'handle_document_options' => 'Documenten',
		'handle_template_options' => 'Sjablonen',
		'handle_object_options' => 'Objecten',
		'handle_class_options' => 'Classen',
		'handle_doctype_options' => "Doctype", // TRANSLATE
		'handle_category_options' => "Categorie",
		'log' => 'Details', // TRANSLATE
		'start_import' => 'Begin import',
		'prepare' => 'Bereid voor...',
		'update_links' => 'Werk koppelingen bij...',
		'doctype' => 'Document-Type', // TRANSLATE
		'category' => 'Categorie',
		'end_import' => 'Importeren voltooid',
		'handle_owners_option' => 'Gegevens eigenaar',
		'txt_owners' => 'Importeer gekoppelde gegevens eigenaar.',
		'handle_owners' => 'Herstel gegevens eigenaar',
		'notexist_overwrite' => 'Indien de gebruiker niet bestaat, wordt de optie "Overschrijf gegevens eigenaar" toegekend',
		'owner_overwrite' => 'Overschrijf gegevens eigenaar',
		'name_collision' => 'Naam botsing',
		'item' => 'Artikel',
		'backup_file_found' => 'Het bestand lijkt op een webEdition backup bestand. Gebruik a.u.b de \"Backup\" optie in het menu \"Bestand\" om de data te importeren.',
		'backup_file_found_question' => 'Wilt u het huidige dialoog venster sluiten en de backup hulp starten?',
		'close' => 'Sluit',
		'handle_file_options' => 'Bestanden',
		'import_files' => 'Importeer bestanden',
		'weBinary' => 'Bestand',
		'format_unknown' => 'Het bestandformaat is onbekend!',
		'customer_import_file_found' => 'Het bestand lijkt op een import bestand met klant gegevens. Gebruik a.u.b. de \"Importeer/Exporteer\" optie in de klanten module (PRO) om de data te importeren.',
		'upload_failed' => 'Het bestand kon niet ge-upload worden. Controleer a.u.b of het bestand groter is dan %s',
		'import_navigation' => 'Importeer navigatie',
		'weNavigation' => 'Navigatie',
		'navigation_desc' => 'Selecteer de directorie waar de navigatie geïmporteerd word.',
		'weNavigationRule' => 'Navigatie regel',
		'weThumbnail' => 'Thumbnail', // TRANSLATE
		'import_thumbnails' => 'Importeer thumbnails',
		'rebuild' => 'Herbouw',
		'rebuild_txt' => 'Automatisch herbouwen',
		'finished_success' => 'Het importeren van de gegevens is succesvol afgerond.',
		'encoding_headline' => 'Charset', // TRANSLATE
		'encoding_noway' => 'A conversion  is only possible between ISO-8859-1 and UTF-8 <br/>and with a set default charset (settings dialog)', // TRANSLATE
		'encoding_change' => "Change, from '", // TRANSLATE
		'encoding_XML' => '', // TRANSLATE
		'encoding_to' => "' (XML file) to '", // TRANSLATE
		'encoding_default' => "' (standard)", // TRANSLATE
);