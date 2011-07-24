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
		'auto_selection' => "Automatische selectie",
		'txt_auto_selection' => "Exporteert documenten of objecten automatisch a.d.h.v. document type of class.",
		'txt_auto_selection_csv' => "Exporteert objecten automatisch a.d.h.v. class.",
		'manual_selection' => "Handmatige selectie",
		'txt_manual_selection' => "Exporteert handmatig geselecteerde documenten of objecten.",
		'txt_manual_selection_csv' => "Exporteert handmatig geselecteerde objecten.",
		'element' => "Selecteer element",
		'documents' => "Documenten",
		'objects' => "Objecten",
		'step1' => "Selecteer parameters",
		'step2' => "Selecteer te exporteren items",
		'step3' => "Selecteer export parameters",
		'step10' => "Export voltooid",
		'step99' => "Fout tijdens exporteren",
		'step99_notice' => "Exporteren niet mogelijk",
		'server_finished' => "Het export bestand is bewaard op de server.",
		'backup_finished' => "De export was succesvol.",
		'download_starting' => "Het downloaden van het export bestand is begonnen.<br><br>Wanneer de download niet begint na 10 seconden,<br>",
		'download' => "Klik dan hier a.u.b.",
		'download_failed' => "Of het gekozen bestand bestaat niet, of u heeft niet de juiste rechten om het te downloaden.",
		'file_format' => "Bestandsformaat",
		'export_to' => "Exporteer naar",
		'export_to_server' => "Server", // TRANSLATE
		'export_to_local' => "Lokale harde schijf",
		'cdata' => "Codering",
		'export_xml_cdata' => "Voeg CDATA secties toe",
		'export_xml_entities' => "Vervang entiteiten",
		'filename' => "Bestandsnaam",
		'path' => "Pad",
		'doctypename' => "Documenten of document typen",
		'classname' => "Objecten of classen",
		'dir' => "Directorie",
		'categories' => "Categorieën",
		'wizard_title' => "Exporteer Hulp",
		'xml_format' => "XML", // TRANSLATE
		'csv_format' => "CSV", // TRANSLATE
		'csv_delimiter' => "Baken af",
		'csv_enclose' => "Omsluit karakter",
		'csv_escape' => "Verwijder karakter",
		'csv_lineend' => "Bestandsformaat",
		'csv_null' => "NULL vervanging",
		'csv_fieldnames' => "Plaats veldnamen in eerste rij",
		'windows' => "Windows formaat",
		'unix' => "UNIX formaat",
		'mac' => "Mac formaat",
		'generic_export' => "Generieke export",
		'title' => "Exporteer Hulp",
		'gxml_export' => "Generieke XML export",
		'txt_gxml_export' => "Exporteer webEdition documenten en objecten naar een \"plat\" XML bestand (3 niveau).",
		'csv_export' => "CSV export", // TRANSLATE
		'txt_csv_export' => "Exporteer webEdition objecten naar een CSV bestand (komma gescheiden waardes).",
		'csv_params' => "Instellingen",
		'error' => "Het exporteer proces was niet succesvol!",
		'error_unknown' => "Er is een onbekende fout opgetreden!",
		'error_object_module' => "Het exporteren van documenten in CSV bestanden is wordt momenteel niet ondersteund!<br><br>Omdat de Database/Object Module niet is geïnstalleerd, is het exporteren van CSV bestanden niet mogelijk.",
		'error_nothing_selected_docs' => "De export is niet uitgevoerd!<br><br>Er zijn geen documenten geselecteerd.",
		'error_nothing_selected_objs' => "De export is niet uitgevoerd!<br><br>Er zijn geen documenten of objecten geselecteerd.",
		'error_download_failed' => "Het downloaden van het export bestand is mislukt.",
		'comma' => ", {komma}",
		'semicolon' => "; {punt komma}",
		'colon' => ": {dubbele punt}",
		'tab' => "\\t {tab}", // TRANSLATE
		'space' => "  {spatie}",
		'double_quote' => "\" {dubbele aanhalingstekens}",
		'single_quote' => "' {enkele aanhalingsteken}",
		'we_export' => 'webEdition export', // TRANSLATE
		'wxml_export' => 'webEdition XML export', // TRANSLATE
		'txt_wxml_export' => 'Exporteren van webEdition documenten, sjablonen, objecten en classen volgens de webEdition specifieke DTD (document type definitie).',
		'options' => 'Opties',
		'handle_document_options' => 'Documenten',
		'handle_template_options' => 'Sjablonen',
		'handle_def_templates' => 'Exporteer standaard sjablonen',
		'handle_document_includes' => 'Exporteer ingevoegde documenten',
		'handle_document_linked' => 'Exporteer gekoppelde documenten',
		'handle_object_options' => 'Objecten',
		'handle_def_classes' => 'Exporteer standaard classes',
		'handle_object_includes' => 'Exporteer ingevoegde objecten',
		'handle_classes_options' => 'Classes', // TRANSLATE
		'handle_class_defs' => 'Standaard waarde',
		'handle_object_embeds' => 'Exporteer embedded objecten',
		'handle_doctype_options' => 'Doctypes/<br>Categoriën/<br>Navigatie',
		'handle_doctypes' => 'Doctypes', // TRANSLATE
		'handle_categorys' => 'Categoriën',
		'export_depth' => 'Exporteer diepte',
		'to_level' => 'naar niveau',
		'select_export' => 'Om een invoer te exporteren, markeer a.u.b. het refererende check box in de boom structuur. Belangrijk: Alle gemarkeerde onderdelen uit alle takken worden gexporteerd en wanneer u een directory exporteert worden alle documenten binnen deze directory ook gexporteerd!',
		'templates' => 'Sjablonen',
		'classes' => 'Classes', // TRANSLATE

		'nothing_to_delete' => 'Er is niks om te verwijderen.',
		'nothing_to_save' => 'Er is niks om te bewaren!',
		'no_perms' => 'Geen toestemmingen!',
		'new' => 'Nieuw',
		'export' => 'Exporteer',
		'group' => 'Groep',
		'save' => 'Bewaar',
		'delete' => 'Verwijder',
		'quit' => 'Stop',
		'property' => 'Eigenschap',
		'name' => 'Naam',
		'save_to' => 'Bewaar naar:',
		'selection' => 'Selectie',
		'save_ok' => 'Export is bewaard.',
		'save_group_ok' => 'Groep is bewaard.',
		'log' => 'Details', // TRANSLATE
		'start_export' => 'Begin export',
		'prepare' => 'Bereid export voor...',
		'doctype' => 'Document-Type', // TRANSLATE
		'category' => 'Categorie',
		'end_export' => 'Export voltooid',
		'newFolder' => "Nieuwe Groep",
		'folder_empty' => "Map is leeg!",
		'folder_path_exists' => "Map bestaat al!",
		'wrongtext' => "Naam is niet geldig",
		'wrongfilename' => "De bestandsnaam is niet geldig!",
		'folder_exists' => "Map bestaat al",
		'delete_ok' => 'De export is verwijderd.',
		'delete_nok' => 'FOUT: De export is niet verwijderd',
		'delete_group_ok' => 'De groep is verwijderd.',
		'delete_group_nok' => 'FOUT: De groep is niet verwijderd',
		'delete_question' => 'Wilt u de huidige export verwijderen?',
		'delete_group_question' => 'Wilt u de huidige groep verwijderen?',
		'download_starting2' => 'Het downloaden van het export bestand is gestart.',
		'download_starting3' => 'Indien de download niet start na 10 seconden,',
		'working' => 'Bezig',
		'txt_document_options' => 'Het standaard sjabloon is het sjabloon dat is gedefinieerd in de document eigenschappen. De ingesloten documenten zijn interne documenten die zijn ingesloten in het export document met  de tags we:include, we:form, we:url, we:linkToSeeMode, we:a, we:href, we:link, we:css, we:js en we:addDelNewsletterEmail. De ingesloten objecten zijn objecten die zijn ingesloten in het export document met de tags we:object en we:form. De gekoppelde documenten zijn interne documenten die zijn gekoppeld aan het export document met de HTML tags body, a, img, table en td.',
		'txt_object_options' => 'De standaard class is de class die is gedefinieerd in de object eigenschappen. Om interne documenten met ingevoegde objecten te exporteren, bijv. afbeeldingen, activeer dan a.u.b de optie "Exporteer ingevoegde documenten"',
		'txt_exportdeep_options' => 'De export diepte definieert het niveau voor de export van de ingesloten documenten. Het veld moet een nummer zijn!',
		'name_empty' => 'De naam kan niet leeg zijn!',
		'name_exists' => 'De naam bestaat al!',
		'help' => 'Help', // TRANSLATE
		'info' => 'Info', // TRANSLATE
		'path_nok' => 'Het pad is niet correct!',
		'must_save' => "De export is gewijzigd.\\nU moet de export data bewaren voordat u kunt exporteren!",
		'handle_owners_option' => 'Eigenaars data',
		'handle_owners' => 'Exporteer eigenaars gegevens',
		'txt_owners' => 'Exporteer gekoppelde eigenaars gegevens.',
		'weBinary' => 'Bestand',
		'handle_navigation' => 'Navigatie',
		'weNavigation' => 'Navigatie',
		'weNavigationRule' => 'Navigatie regel',
		'weThumbnail' => 'Thumbnails', // TRANSLATE
		'handle_thumbnails' => 'Thumbnails', // TRANSLATE

		'navigation_hint' => 'To export the navigation entries, the template on which the navigation is displayed has also to be exported!',
		'title' => 'Exporteer Hulp',
		'selection_type' => 'Stel element selectie vast',
		'auto_selection' => 'Automatische selectie',
		'txt_auto_selection' => 'Exporteert automatisch - volgens de doctype of class - geselecteerde documenten of objecten.',
		'manual_selection' => 'Handmatige selectie',
		'txt_manual_selection' => 'Exporteert handmatig geselecteerde documenten of objecten.',
		'element' => 'Element selectie',
		'select_elements' => 'Selecteer elementen voor export',
		'select_docType' => 'Kies a.ub. een doctype of een sjabloon.',
		'none' => '-- geen --',
		'doctype' => 'Doctype', // TRANSLATE
		'template' => 'Sjabloon',
		'categories' => 'Categorieën',
		'documents' => 'Documenten',
		'objects' => 'Objecten',
		'class' => 'Classen',
		'isDynamic' => 'Genereer pagina dynamisch',
		'extension' => 'Extensie',
		'wexml_export' => 'weXML Export', // TRANSLATE
		'filename' => 'Bestandsnaam',
		'extra_data' => 'Extra data', // TRANSLATE
		'integrated_data' => 'Exporteer bijgevoegde data',
		'integrated_data_txt' => 'Selecteer deze optie om de bijgevoegde data in documenten of sjablonen te exporteren.',
		'max_level' => 'naar niveau',
		'export_doctypes' => 'Exporteer doctypes',
		'export_categories' => 'Exporteer categorieën',
		'export_location' => 'Exporteer naar',
		'local_drive' => 'Lokale schijf',
		'server' => 'Server', // TRANSLATE
		'export_progress' => 'Bezig met exporteren',
		'prepare_progress' => 'Bezig met voorbereiden',
		'finish_progress' => 'Voltooid',
		'finish_export' => 'Het exporteren was succesvol!',
);