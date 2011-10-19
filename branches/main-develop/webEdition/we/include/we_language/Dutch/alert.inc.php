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
 * Language file: alert.inc.php
 * Provides language strings.
 * Language: English
 */
$l_alert = array(
		'tblFile' => array(
				'in_wf_warning' => "Het document moet eerst bewaard worden voordat het in de workflow geplaatst kan worden!\\nWilt u het document nu bewaren?",
				'not_im_ws' => "Het bestand bevindt zich niet in uw werkgebied!",
		'exit_doc_question' => "Het document is gewijzigd.<BR> Wilt u de wijzigingen bewaren?",
		),
		'tblTemplates' => array(
				'in_wf_warning' => "Het sjabloon moet eerst bewaard worden voordat het in de workflow geplaatst kan worden!\\nWilt u het sjabloon nu bewaren?",
				'not_im_ws' => "Het sjabloon bevindt zich niet in uw werkgebied!",
		'exit_doc_question' => "Het sjabloon is gewijzigd.<BR> Wilt u de wijzigingen bewaren?",
		),
	'tblObjectFiles'=>array(
			'in_wf_warning' => "Het object moet eerst bewaard worden voordat het in de workflow geplaatst kan worden!\\nWilt u het object nu bewaren?",
			'exit_doc_question' => "Het object is gewijzigd.<BR> Wilt u de wijzigingen bewaren?",
),
	'tblObject'=>array(
			'in_wf_warning' => "De class moet eerst bewaard worden voordat deze in de workflow geplaatst kan worden!\\nWilt u de class nu bewaren?",
			'exit_doc_question' => "De classe is gewijzigd.<BR> Wilt u de wijzigingen bewaren?",
),

		'folder' => array(
				'not_im_ws' => "De map bevindt zich niet in uw werkgebied!",
		),
		'nonew' => array(
				'objectFile' => "U mag geen nieuwe objecten aanmaken!<br>Of u heeft niet de juiste rechten of er is geen classe waar één van uw werkgebieden geldig is!",
		),
		'wrong_file' => array(
				'image/*' => "Het bestand kon niet opgeslagen worden. Of het is geen afbeelding of uw server is vol!",
				'application/x-shockwave-flash' => "Het bestand kon niet opgeslagen worden. Of het is geen Flash film of uw schijf is vol!",
				'video/quicktime' => "Het bestand kon niet opgeslagen worden. Of het is geen Quicktime film of uw schijf is vol!",
				'text/css' => "The file could not be stored. Either it is not a CSS file or your disk space is exhausted!", // TRANSLATE
		),
		'no_views' => array(
				'headline' => 'Attentie',
				'description' => 'Er is geen weergave beschikbaar voor dit document.',
		),
		'navigation' => array(
				'last_document' => 'U wijzigt het laatste document.',
				'first_document' => 'U wijzigt het eerste document.',
				'doc_not_found' => 'Kon geen bijpassend document vinden.',
				'no_entry' => 'Geen invoer gevonden in geschiedenis.',
				'no_open_document' => 'Er is geen geopend document.',
		),
		'delete_single' => array(
				'confirm_delete' => 'Dit document verwijderen?',
				'no_delete' => 'Dit document kon niet verwijderd worden.',
				'return_to_start' => 'Het document is verwijderd. \\nTerug naar seeModus startdocument.',
		),
		'move_single' => array(
				'return_to_start' => 'Het document is verplaatst. \\nTerug naar het seeMode startdocument.',
				'no_delete' => 'Dit document kon niet verplaatst worden',
		),
		'notice' => "Notice",
		'warning' => "Waarschuwing",
		'error' => "Fout",
		'noRightsToDelete' => "\\'%s\\' kan niet verwijderd worden! U bent niet bevoegd om deze actie uit te voeren!",
		'noRightsToMove' => "\\'%s\\' kan niet verplaatst worden! U heeft niet de juiste rechten om deze actie uit te voeren!",
		'delete_recipient' => "Weet u zeker dat u het geselecteerde e-mail adres wilt verwijderen?",
		'recipient_exists' => "Dit e-mail adres bestaat al!",
		'input_name' => "Voer een nieuw e-mail adres in!",
		'input_file_name' => "Voer een bestandsnaam in.",
		'max_name_recipient' => "Een e-mail adres mag slechts 255 karakters bevatten!",
		'not_entered_recipient' => "Er is geen e-mail adres ingevoerd!",
		'recipient_new_name' => "Wijzig e-mail adres!",
		'required_field_alert' => "Het veld '%s' is verplicht en moet ingevuld worden!",
		'phpError' => "webEdition kan niet opgestart worden!",
		'3timesLoginError' => "Het inloggen is %s mislukt! Wacht a.u.b. %s minuten en probeer het opnieuw!",
		'popupLoginError' => "Het webEdition venster kon niet geopend worden!\\n\\nwebEdition kan alleen opgestart worden wanneer uw browser geen pop-up vensters blokkeert.",
		'publish_when_not_saved_message' => "Het document is nog niet bewaard! Wilt u het toch publiceren?",
		'template_in_use' => "Het sjabloon is in gebruik en kan niet verwijderd worden!",
		'no_cookies' => "U heeft geen cookies geactiveerd. Activeer a.u.b. de cookies in uw browser!",
		'doctype_hochkomma' => "Ongeldige naam! Ongeldige karakters zijn de ' (apostrof) en de , (komma)!",
		'thumbnail_hochkomma' => "Ongeldige naam! Ongeldige karakters zijn de ' (apostrof) en de , (komma)!",
		'can_not_open_file' => "Het bestand %s kon niet geopend worden!",
		'no_perms_title' => "Geen toestemming!",
		'no_perms_action' => "You don't have the permission to perform this action.", // TRANSLATE
		'access_denied' => "Toegang geweigerd!",
		'no_perms' => "Neem a.u.b. contact op met de eigenaar (%s) of een admin<br>als u toegang nodig hebt!",
		'temporaere_no_access' => "Toegang niet mogelijk!",
		'temporaere_no_access_text' => "Het bestand \"%s\" wordt op dit moment gewijzigd door \"%s\".",
		'file_locked_footer' => "Dit document wordt op dit moment gewijzigd door \"%s\".",
		'file_no_save_footer' => "U heeft niet de juiste rechten om dit bestand te bewaren.",
		'login_failed' => "Verkeerde gebruikersnaam en/of wachtwoord!",
		'login_failed_security' => "webEdition kon niet opgestart worden!\\n\\nOm veiligheids redenen is het inlog proces afgebroken, omdat de maximale inlog tijd in webEdition is overschreden!\\n\\nLog a.u.b. opnieuw in.",
		'perms_no_permissions' => "U bent niet bevoegd om deze actie uit te voeren!",
		'no_image' => "Het bestand dat u hebt geselecteerd is geen afbeelding!",
		'delete_ok' => "Bestanden of directories succesvol verwijderd!",
		'delete_cache_ok' => "Cache succesvol geleegd!",
		'nothing_to_delete' => "Er is niks geselecteerd om te verwijderen!",
		'delete' => "Geselecteerde onderdelen verwijderen?\\nWilt u doorgaan?",
		'delete_cache' => "Cace legen voor de geselecteerde onderdelen?\\nWilt u doorgaan?",
		'delete_folder' => "Geselecteerde directory verwijderen?\\nLet op: Wanneer u een directory verwijderd worden automatisch alle documenten en directories binnen de directory gewist!\\nWilt u doorgaan?",
		'delete_nok_error' => "Het bestand '%s' kan niet verwijderd worden.",
		'delete_nok_file' => "Het bestand '%s' kan niet verwijderd worden.\\nHet is mogelijk beveiligd tegen schrijven. ",
		'delete_nok_folder' => "De directory '%s' kan niet verwijderd worden.\\nHet is mogelijk beveiligd tegen schrijven.",
		'delete_nok_noexist' => "Bestand '%s' bestaat niet!",
		'noResourceTitle' => "No Item!", // TRANSLATE
		'noResource' => "The document or directory does not exist!", // TRANSLATE
		'move_exit_open_docs_question' => "Voordat documenten verplaatst kunnen worden, moeten ze eerst gesloten worden. Alle niet bewaarde wijzigingen zullen verloren gaan tijdens het proces. Het volgende document wordt afgesloten:\\n\\n",
		'move_exit_open_docs_continue' => 'Doorgaan?',
		'move' => "Geselecteerde items verplaatsen?\\nWilt u verder gaan?",
		'move_ok' => "Bestand succesvol verplaatst!",
		'move_duplicate' => "Er bevinden zich bestanden met dezelfde naam in de doel directorie!\\nDe bestanden kunnen niet verplaatst worden.",
		'move_nofolder' => "De geselecteerde bestanden kunnen niet verplaatst worden.\\nHet is niet mogelijk om directories te verplaatsen.",
		'move_onlysametype' => "De geselecteerde objecten kunnen niet verplaatst worden..\\nObjecten kunnen alleen verplaatst worden in hun eigen classdirectorie.",
		'move_no_dir' => "Kies a.u.b. een doel directorie!",
		'document_move_warning' => "Na het verplaatsen van documenten moeten deze opnieuw opgebouwd worden.<br />Wilt u dit nu doen?",
		'nothing_to_move' => "Er is niks gemarkeerd om te verplaatsen!",
		'move_of_files_failed' => "Een of meer bestanden konder niet verplaatst worden! Verplaats de bestanden a.u.b. handmatig.\\nHet gaat om de volgende bestanden:\\n%s",
		'template_save_warning' => "Dit sjabloon wordt gebruikt door %s gepubliceerde documenten. Moeten deze opnieuw bewaard worden? Attentie: Dit kan enige tijd duren als het om veel documenten gaat!",
		'template_save_warning1' => "Dit sjabloon wordt gebruikt door één gepubliceerd document. Moet deze opnieuw bewaard worden?",
		'template_save_warning2' => "Dit sjabloon wordt gebruikt door andere sjablonen en documenten, moeten deze opnieuw bewaard worden?",
		'thumbnail_exists' => 'Deze thumbnail bestaat al!',
		'thumbnail_not_exists' => 'Deze thumbnail bestaat niet!',
		'thumbnail_empty' => "You must enter a name for the new thumbnail!", // TRANSLATE
		'doctype_exists' => "Dit document type bestaat al!",
		'doctype_empty' => "U moet een naam invoeren voor het nieuwe document type!",
		'delete_cat' => "Weet u zeker dat u de geselecteerde categorie wilt verwijderen?",
		'delete_cat_used' => "Deze category is in gebruik en kan niet verwijderd worden!",
		'cat_exists' => "Die categorie bestaal al!",
		'cat_changed' => "De categorie is in gebruik! Bewaar de documenten opnieuw die gebruik maken van de categorie!\\nMoet de categorie toch gewijzigd worden?",
		'max_name_cat' => "Een categorienaam mag slechts 32 karakters bevatten!",
		'not_entered_cat' => "Er is geen categorienaam ingevoerd!",
		'cat_new_name' => "Voer de nieuwe categorienaam in!",
		'we_backup_import_upload_err' => "Er is een fout opgetreden tijdens het uploaden van het backup bestand! De maximale bestandsgrootte voor uploads is %s. Als uw backup bestand de limiet overschrijdt, upload het dan a.u.b. in de directory webEdition/we_Backup via FTP en kies '" . g_l('backup', "[import_from_server]") . "'",
		'rebuild_nodocs' => "Er zijn geen documenten die passen bij de geselecteerde attributen.",
		'we_name_not_allowed' => "De termen 'we' en 'webEdition' zijn gereserveerde woorden en mogen niet gebruikt worden!",
		'we_filename_empty' => "Er is geen naam ingevoerd voor dit document of directory!",
		'exit_multi_doc_question' => "Meerdere geopende documenten bevatten niet bewaarde wijzigingen. Als u doorgaat gaan alle wijzigingen verloren. Wilt u doorgaan en de wijzigingen negeren?",
		'deleteTempl_notok_used' => "Eén of meerdere sjablonen zijn in gebruik en konden niet verwijderd worden!",
		'deleteClass_notok_used' => "One or more of the classes are in use and could not be deleted!", // TRANSLATE
		'delete_notok' => "Fout tijdens het verwijderen!",
		'nothing_to_save' => "De bewaar functie is uitgeschakeld op dit moment!",
		'nothing_to_publish' => "De publiceer functie is op dit moment uitgeschakeld!",
		'we_filename_notValid' => "Invalid filename\\nValid characters are alpha-numeric, upper and lower case, as well as underscore, hyphen and dot (a-z, A-Z, 0-9, _, -, .)",
		'empty_image_to_save' => "De geselecteerde afbeelding is leeg.\\n Doorgaan?",
		'path_exists' => "Het bestand of document %s  kan niet bewaard worden omdat een ander document zich al op deze plek bevind!",
		'folder_not_empty' => "Eén of meerdere directories zijn niet geheel leeg en kunnen daardoor niet verwijderd worden! Wis de bestanden handmatig.\\n Het gaat om de volgende bestanden:\\n%s",
		'name_nok' => "De namen moeten karakters bevatten als '<' of '>'!",
		'found_in_workflow' => "Eén of meerdere geselecteerde invoeren zitten in het worklfow proces! Wilt u ze uit het workflow proces halen?",
		'import_we_dirs' => "U probeert vanuit een webEdition directory te importeren!\\n Die directories worden gebruikt en beschermd door webEdition en kunnen daardoor niet gebruikt worden voor import!",
		'no_file_selected' => "Er is geen bestand gekozen voor upload!",
		'browser_crashed' => "Het venster kon niet geopend worden vanwege een fout in uw browser!  Sla a.u.b. uw werk op en herstart de browser.",
		'copy_folders_no_id' => "Bewaar a.u.b. eerst de huidige directory!",
		'copy_folder_not_valid' => "Dezelfde directory of één van de hoofd directories kon niet gekopiëerd worden!",
		'cockpit_not_activated' => 'Deze actie kon niet uitgevoerd worden omdat de cockpit niet geactiveerd is.',
		'cockpit_reset_settings' => 'Weet u zeker dat u de huidige cockpit instellingen wilt verwijderen en de standaard instellingen terug wilt zetten?',
		'save_error_fields_value_not_valid' => 'De uitgelichte velden bevatten ongeldige data.\\nVoer a.u.b. geldige data in.',
		'eplugin_exit_doc' => "Het document is gewijzigd met een externe editor. De verbinding tussen webEdition en de externe editor wordt afgesloten en wordt niet meer gesynchroniseerd.\\nWilt u het document sluiten?",
		'delete_workspace_user' => "De directory %s kon niet verwijderd worden! Deze is gedefinieerd als werkgebied voor de volgende gebruikers of groepen:\\n%s",
		'delete_workspace_user_r' => "De directory %s kon niet verwijderd worden! Binnen deze directory bevinden zich andere directories die zijn gedefinieerd als werkgebied voor de volgende gebruikers of groepen:\\n%s",
		'delete_workspace_object' => "De directory %s kon niet verwijderd worden! Deze is gedefinieerd als werkgebied in de volgende objecten:\\n%s",
		'delete_workspace_object_r' => "De directory %s kon niet verwijderd worden! Binnen deze directory bevinden zich andere directories die zijn gedefinieerd als werkgebied in de volgende objecten:\\n%s",
		'field_contains_incorrect_chars' => "Een veld (van het type %s) bevat ongeldige karakters.",
		'field_input_contains_incorrect_length' => "De maximale lengte van een veld met het type \'Text input\' is 255 karakters. Indien u meer karakters nodig hebt, maak dan gebruik van het veld met het type \'Textarea\'.",
		'field_int_contains_incorrect_length' => "De maximale lengte van een veld met het type \'Integer\' is 10 karakters.",
		'field_int_value_to_height' => "De maximale waarde van een veld met het type \'Integer\' is 2147483647.",
		'we_filename_notValid' => "Ongeldige bestandsnaam\\nGeldige karakters zijn alfa-numeriek, boven- en onderkast, evenals de underscore, koppelteken en punt (a-z, A-Z, 0-9, _, -, .)",
		'login_denied_for_user' => "The user cannot login. The user access is disabled.", // TRANSLATE
		'no_perm_to_delete_single_document' => "You have not the needed permissions to delete the active document.", // TRANSLATE

		'confirm' => array(
				'applyWeDocumentCustomerFiltersDocument' => "The document has been moved to a folder with divergent customer account policies. Should the settings of the folder be transmitted to this document?", // TRANSLATE
				'applyWeDocumentCustomerFiltersFolder' => "The directory has been moved to a folder with divergent customers account policies. Should the settings be adopted for this directory and all subelements? ", // TRANSLATE
		),
		'field_in_tab_notvalid_pre' => "The settings could not be saved, because the following fields contain invalid values:", // TRANSLATE
		'field_in_tab_notvalid' => ' - field %s on tab %s', // TRANSLATE
		'field_in_tab_notvalid_post' => 'Correct the fields before saving the settings.', // TRANSLATE
		'discard_changed_data' => 'There are unsaved changes that will be discarded. Are you sure?', // TRANSLATE
);
