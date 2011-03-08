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
 * Language file: navigation.inc.php
 * Provides language strings.
 * Language: English
 */
$l_navigation = array(
		'no_perms' => 'You do not have the permission to select this option.',
		'delete_alert' => 'Verwijder huidige invoer/map.\\n Weet u het zeker?',
		'nothing_to_delete' => 'De invoer kan niet verwijderd worden!',
		'nothing_to_save' => 'De invoer kan niet bewaard worden!',
		'nothing_selected' => 'Selecteer a.u.b. de invoer/map die verwijderd moet worden.',
		'we_filename_notValid' => 'De gebruikersnaam is niet juist!\\nAlfa-numerieke karakters, bovenkast en onderkast, evenals underscore, koppelteken, punt en leeg karakter {blank; space} (a-z, A-Z, 0-9, _,-.,) zijn geldig',
		'menu_new' => 'Nieuw',
		'menu_save' => 'Bewaar',
		'menu_delete' => 'Verwijder',
		'menu_exit' => 'Stop',
		'menu_options' => 'Opties',
		'menu_generate' => 'Genereer broncode',
		'menu_settings' => 'Instellingen',
		'menu_highlight_rules' => 'Regels voor Highlighting',
		'menu_info' => 'Info', // TRANSLATE
		'menu_help' => 'Help', // TRANSLATE

		'property' => 'Eigenschappen',
		'preview' => 'Voorvertoning',
		'preview_code' => 'Voorvertoning - broncode',
		'navigation' => 'Navigatie',
		'group' => 'Map',
		'name' => 'Naam',
		'newFolder' => 'Nieuwe map',
		'display' => 'Display', // TRANSLATE
		'save_group_ok' => 'De map is bewaard.',
		'save_ok' => 'De navigatie is bewaard.',
		'path_nok' => 'Het pad is niet juist!',
		'name_empty' => 'De naam mag niet leeg zijn!',
		'name_exists' => 'De naam bestaat al!',
		'wrongtext' => 'De naam is niet geldig!\\nGeldige karakters zijn letters van a tot z (bovenkast of onderkast), figuren, underscore (_), scheidingsteken (-), punt (.), lege karakters ( ) en appenstaart symbolen (@). ',
		'wrongTitleField' => 'The navigation folder could not be saved, because the given title field doesn\'t  exist. Please correct the title field on the "content" tab and save again.', // TRANSLATE
		'folder_path_exists' => 'Er bestaat al een invoer/map met dezelfde naam.',
		'navigation_deleted' => 'De invoer/map is succesvol verwijderd.',
		'group_deleted' => 'De map is succesvol verwijderd.',
		'selection' => 'Selectie',
		'icon' => 'Afbeelding',
		'presentation' => 'Representatie',
		'text' => 'Tekst',
		'title' => 'Titel',
		'dir' => 'Directorie',
		'categories' => 'Categoriën',
		'stat_selection' => 'Statische selectie',
		'dyn_selection' => 'Dynamische selectie',
		'manual_selection' => 'Handmatige selectie',
		'txt_dyn_selection' => 'Uitleg tekst voor de dynamische selectie',
		'txt_stat_selection' => 'Uitleg tekst voor de statische selectie. Gekoppeld aan het selecteerde document of object.',
		'sort' => 'Sortering',
		'ascending' => 'oplopend',
		'descending' => 'aflopend',
		'show_count' => 'Aantal te tonen invoeren',
		'title_field' => 'Titel veld',
		'select_field_txt' => 'Selecteer een veld',
		'content' => 'Content', // TRANSLATE
		'no_dyn_content' => '- Geen dynamische content -',
		'dyn_content' => 'De map bevat dynamische content',
		'link' => 'Koppeling',
		'docLink' => 'Intern document',
		'objLink' => 'Object', // TRANSLATE
		'catLink' => 'Categorie',
		'order' => 'Volgorde',
		'general' => 'Algemeen',
		'entry' => 'Invoer',
		'entries' => 'Invoeren',
		'save_populate_question' => 'U heeft de dynamische content voor de map gedefinieerd. Na het bewaren worden de gegenereerde invoeren toegevoegd of vernieuwd. Wilt u verder gaan?',
		'depopulate_question' => 'De dynamische content wordt nu verwijderd. Wilt u verder gaan?',
		'populate_question' => 'De dynamische invoeren zijn nu bijgewerkt. Wilt u verder gaan?',
		'depopulate_msg' => 'De dynamische invoeren zijn verwijderd.',
		'populate_msg' => 'De dynamische invoeren zijn toegevoegd.',
		'documents' => 'Documenten',
		'objects' => 'Objecten',
		'workspace' => 'Workspace', // TRANSLATE
		'no_workspace' => 'Het object heeft geen workspace! Daardoor kan het object niet geselecteerd worden!',
		'no_entry' => '--allemaal hetzelfde--',
		'parameter' => 'Parameter', // TRANSLATE
		'urlLink' => 'Extern document',
		'doctype' => 'Document type', // TRANSLATE
		'class' => 'Class', // TRANSLATE

		'parameter_text' => 'In de parameter kunnen de volgende variabelen van de navigatie worden gebruikt: $LinkID, FolderID, $DocTypID, $ClassID, $Ordn en $WorkspaceID',
		'intern' => 'Interne koppeling',
		'extern' => 'Externe koppeling',
		'linkSelection' => 'Koppeling selectie',
		'catParameter' => 'Naam van de categorie parameter',
		'navigation_rules' => "Navigatie regels",
		'available_rules' => "Beschikbare rules",
		'rule_name' => "Naam van de regel",
		'rule_navigation_link' => "Actief navigatie item",
		'rule_applies_for' => "Regel geldt voor",
		'rule_folder' => "In map",
		'rule_doctype' => "Document type", // TRANSLATE
		'rule_categories' => "Categorieën",
		'rule_class' => "Van class",
		'rule_workspace' => "Workspace", // TRANSLATE
		'invalid_name' => "De naam mag alleen bestaan uit letters, figuren, koppeltekens en underscore",
		'name_exists' => "De name \"%s\" bestaat al, voer a.u.b een andere naam in.",
		'saved_successful' => "De invoer: \"%s\" is bewaard.",
		'exit_doc_question' => 'Het lijkt erop dat u de navigatie gewijzigd heeft.<br>Wilt u de wijzigingen bewaren?',
		'add_navigation' => 'Voeg navigatie toe',
		'begin' => 'aan het begin',
		'end' => 'aan het eind',
		'del_question' => 'De invoer wordt definitief verwijderd. Weet u het zeker?',
		'dellall_question' => 'Alle invoeren worden definitief verwijderd. Weet u het zeker?',
		'charset' => 'Karakter codering',
		'more_attributes' => 'Meer eigenschappen',
		'less_attributes' => 'Minder eigenschappen',
		'attributes' => 'Attributen',
		'title' => 'Titel',
		'anchor' => 'Anker',
		'language' => 'Taal',
		'target' => 'Doel',
		'link_language' => 'Koppeling',
		'href_language' => 'Gekoppeld document',
		'keyboard' => 'Keyboard', // TRANSLATE
		'accesskey' => 'Accesskey', // TRANSLATE
		'tabindex' => 'Tabindex', // TRANSLATE
		'relation' => 'Relatie',
		'link_attribute' => 'Koppeling attributen',
		'popup' => 'Popup venster',
		'popup_open' => 'Open', // TRANSLATE
		'popup_center' => 'Centreer',
		'popup_x' => 'x positie',
		'popup_y' => 'y positie',
		'popup_width' => 'Breedte',
		'popup_height' => 'Hoogte',
		'popup_status' => 'Status', // TRANSLATE
		'popup_scrollbars' => 'Scrollbalken',
		'popup_menubar' => 'Menubalk',
		'popup_resizable' => 'Schaalaar',
		'popup_location' => 'Locatie',
		'popup_toolbar' => 'Knoppenbalk',
		'icon_properties' => 'Afbeelding eigenschappen',
		'icon_properties_out' => 'Verberg afbeelding eigenschappen',
		'icon_width' => 'Breedte',
		'icon_height' => 'Hoogte',
		'icon_border' => 'Rand',
		'icon_align' => 'Uitlijning',
		'icon_hspace' => 'horiz. uitlijnen',
		'icon_vspace' => 'vert. uitlijnen',
		'icon_alt' => 'Alt tekst',
		'icon_title' => 'Titel',
		'linkprops_desc' => 'Hier kunt u de additionele koppeling eigenschappen definieren. Bij dynamische onderdelen worden alleen doel koppeling en popup venster eigenschappen doorgevoerd.',
		'charset_desc' => 'De geselecteerde charset wordt doorgevoerd op de huidige map en alle map invoeren.',
		'customers' => 'Klanten',
		'limit_access' => 'Defineer klant toegang',
		'customer_access' => 'Alle klanten hebben toegang tot dit onderdeel',
		'filter' => 'Defineer filter',
		'and' => 'en',
		'or' => 'of',
		'selected_customers' => 'Alleen de volgende klanten hebben toegang tot dit onderdeel',
		'useDocumentFilter' => 'Use filter settings of document/object', // TRANSLATE
		'reset_customer_filter' => 'Reset all customer filters', // TRANSLATE
		'reset_customerfilter_done_message' => 'The customer filters were successfully reset!', // TRANSLATE
		'reset_customerfilter_question' => 'Do you realy want to reset all customer filters', // TRANSLATE

		'NoDeleteFromDocument' => "Navigation entry with subentries, can be edited from here, but deletion has to be done in the navigation tool.", // TRANSLATE
		'current_on_urlpar' => "Take into account at highlighting", // TRANSLATE
		'current_on_anker' => "Take into account at highlighting (using add. URL-Par. we_anchor)", // TRANSLATE
);