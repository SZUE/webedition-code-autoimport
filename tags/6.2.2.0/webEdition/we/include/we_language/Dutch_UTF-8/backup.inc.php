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
$l_backup = array(
		'save_not_checked' => " U heeft geen download locatie gekozen voor het backup bestand!",
		'wizard_title' => "Herstel Backup Hulp",
		'wizard_title_export' => "Backup Exporteer Hulp",
		'save_before' => "Tijdens het importeren wordt alle bestaande data gewist! Controleer of u uw data heeft bewaard",
		'save_question' => "Wilt u uw bestaande data bewaren?",
		'step1' => "Step 1/4 - Bewaar bestaande data",
		'step2' => "Step 2/4 - Kies bron voor importeren",
		'step3' => "Step 3/4 - Importeer opgeslagen data",
		'step4' => "Step 4/4 - Herstel voltooid",
		'extern' => "Herstel externe webEdition bestanden en mappen",
		'settings' => "Herstel voorkeuren",
		'rebuild' => "Automatische heropbouw",
		'select_upload_file' => "Upload import bestand vanaf lokaal bestand",
		'select_server_file' => "Kies uit deze lijst het backup bestand dat u wilt importeren.",
		'charset_warning' => "If you encounter problems when restoring a backup, please ensure that the <strong>target system uses the same character set as the source system</strong>. This applies both to the character set of the database (collation) as well as for the character set of the user interface language!", // TRANSLATE
		'defaultcharset_warning' => '<span style="color:ff0000">Attention! The standard charset is not defined.</span> For some server configurations, this can lead to problems while importing backups.!', // TRANSLATE
		'finished_success' => "Het importeren van de backup data is met succes voltooid.",
		'finished_fail' => "Het importeren van de backup data is mislukt.",
		'question_taketime' => "Het exporteren kan enige tijd duren.",
		'question_wait' => "Even geduld a.u.b.!",
		'export_title' => "Exporteren",
		'finished' => "Voltooid",
		'extern_files_size' => "Omdat de maximale bestandsgrootte is beperkt tot %.1f MB (%s byte) vanwege de database instellingen, kunnen er meerdere bestanden aangemaakt worden.",
		'extern_files_question' => "Bewaar externe webEdition bestanden en mappen.",
		'export_location' => "Specify where you want to save the backup file. If it is stored on the server, you find the file in '/webEdition/we_backup/data/'.", // TRANSLATE
		'export_location_server' => "Op de server",
		'export_location_send' => "Op een lokale harde schijf",
		'can_not_open_file' => "Bestand '%s' kan niet geopend worden.",
		'too_big_file' => "Bestand '%s' kan niet geschreven worden omdat het bestand te groot is.",
		'cannot_save_tmpfile' => "Kan geen tijdelijk bestand aanmaken. Controleer of u schrijf rechten hebt voor %s",
		'cannot_save_backup' => "Kan backup bestand niet bewaren.",
		'cannot_send_backup' => "Kan backup niet uitvoeren.",
		'finish' => "De backup is succescol afgerond.",
		'finish_error' => " Fout: Kan backup niet uitvoeren.",
		'finish_warning' => "Waarschuwing: Backup voltooid, maar sommige bestanden kunnen incompleet zijn.!",
		'export_step1' => "Stap 1 van 2 - Exporteer parameters",
		'export_step2' => "Stap 2 van 2 - Voltooi export",
		'unspecified_error' => "Er is een onbekende fout opgetreden!",
		'export_users_data' => "Bewaar gebruikersdata",
		'import_users_data' => "Herstel gebruikersdata",
		'import_from_server' => "Herstel data vanaf server",
		'import_from_local' => "Herstel vanaf lokaal bestand",
		'backup_form' => "Backup vanaf ",
		'nothing_selected' => "Niks geselecteerd!",
		'query_is_too_big' => "De backup bevat een bestand dat niet hersteld kan worden omdat het de limiet van %s bytes overschreid!",
		'show_all' => "Toon alle bestanden",
		'import_customer_data' => "Herstel klanten data",
		'import_shop_data' => "Herstel shop data",
		'export_customer_data' => "Bewaar klanten data",
		'export_shop_data' => "Beaar shop data",
		'working' => "Bezig...",
		'preparing_file' => "Bereid bestand voor op import...",
		'external_backup' => "Externe data wordt bewaard...",
		'import_content' => "Importeren inhoud",
		'import_files' => "Importeren bestanden",
		'import_doctypes' => "Herstel doctypes",
		'import_user_data' => "Herstel gebruikersdata",
		'import_templates' => "Importeren sjablonen",
		'export_content' => "Exporteren inhoud",
		'export_files' => "Exporteren bestanden",
		'export_doctypes' => "Bewaar document types",
		'export_user_data' => "Bewaar gebruikersdata",
		'export_templates' => "Exporteren sjablonen",
		'download_starting' => "Het downloaden van het backup bestand is gestart.<br><br>Als de download niet begint na 10 seconden,<br>",
		'download' => "Klik dan hier..",
		'download_failed' => "Of het gekozen bestand bestaat niet, of u bent niet bevoegd om het te downloaden.",
		'extern_backup_question_exp' => "U hebt de optie 'Bewaar externe webEdition bestanden en mappen' geselecteerd. Deze optie kan enige tijd duren en kan leiden tot systeem fouten. Wilt u toch doorgaan?",
		'extern_backup_question_exp_all' => "U hebt de optie 'Vink alles aan' geselecteerd. Daarbij hoort ook de optie 'Bewaar externe webEdition bestanden en mappen'. Deze optie kan enige tijd duren en kan leiden tot systeem fouten. <br><br>Wilt u toch de optie 'Bewaar externe webEdition bestanden en mappen' blijven behouden?",
		'extern_backup_question_imp' => "U hebt de optie 'Herstel externe webEdition bestanden en mappen' geselecteerd. Deze optie kan enige tijd duren en kan leiden tot systeem fouten. Wilt u toch doorgaan?",
		'extern_backup_question_imp_all' => "U hebt de optie 'Vink alles aan' geselecteerd. Daarbij hoort ook de optie 'Herstel externe webEdition bestanden en mappen'. Deze optie kan enige tijd duren en kan leiden tot systeem fouten. <br><br>Wilt u toch de optie 'Herstel externe webEdition bestanden en mappen' blijven behouden?",
		'nothing_selected_fromlist' => "Kies het bestand dat u wilt importeren uit de lijst om door te gaan!",
		'export_workflow_data' => "Bewaar workflow data",
		'export_todo_data' => "Bewaar taak/berichten data",
		'import_workflow_data' => "Herstel workflow data",
		'import_todo_data' => "Herstel taak/berichten data",
		'import_check_all' => "Vink alles aan",
		'export_check_all' => "Vink alles aan",
		'import_shop_dep' => "U hebt de optie 'Herstel winkel data' geselecteerd. De Winkel Module heeft de klanten data nodig, daarom is 'Herstel klanten data' automatisch geselecteerd.",
		'export_shop_dep' => "U hebt de optie 'Bewaar winkel data' geselecteerd. De Winkel Module heeft de klanten data nodig, daarom is 'Bewaar klanten data' automatisch geselecteerd.",
		'import_workflow_dep' => "U hebt de optie 'Herstel workflow data' geselecteerd. De Workflow Module heeft de documenten en gebruikersdata nodig, daarom is 'Herstel documenten en sjablonen' en 'Herstel gebruikers data' automatisch geselecteerd.",
		'export_workflow_dep' => "U hebt de optie 'Bewaar workflow data' geselecteerd. De Workflow Module heeft de documenten en gebruikersdata nodig, daarom is 'Bewaar documenten en sjablonen' en 'Bewaar workflow data' automatisch geselecteerd.",
		'import_todo_dep' => "U hebt de optie 'Herstel taak/berichten data' geselecteerd. De Taak/Berichten Module heeft de gebruikersdata nodig, daarom is 'Herstel gebruikersdata' automatisch geselecteerd.",
		'export_todo_dep' => "U hebt de optie 'Bewaar taak/berichten data' geselecteerd. De Taak/Berichten Module heeft de gebruikersdata nodig, daarom is 'Bewaar gebruikersdata' automatisch geselecteerd.",
		'export_newsletter_data' => "Bewaar nieuwsbrief data",
		'import_newsletter_data' => "Herstel nieuwsbrief data",
		'export_newsletter_dep' => "U hebt de optie 'Bewaar nieuwsbrief data' geselecteerd. De Nieuwsbrief Module heeft de documenten en gebruikers data nodig, daarom is 'Bewaar documenten en sjablonen' en 'Bewaar klanten data' automatisch geselecteerd.",
		'import_newsletter_dep' => "U hebt de optie 'Herstel nieuwsbrief data' geselecteerd. De Nieuwsbrief Module heeft de documenten en gebruikers data nodig, daarom is 'Herstel documenten en sjablonen' en 'Herstel klanten data' automatisch geselecteerd.",
		'warning' => "Waarschuwing",
		'error' => "Fout",
		'export_temporary_data' => "Bewaar tijdelijke data",
		'import_temporary_data' => "Herstel tijdelijke data",
		'export_banner_data' => "Bewaar banner data",
		'import_banner_data' => "Herstel banner data",
		'export_prefs' => "Bewaar voorkeuren",
		'import_prefs' => "Herstel voorkeuren",
		'export_links' => "Bewaar koppelingen",
		'import_links' => "Herstel kopprlingen",
		'export_indexes' => "Bewaar indexen",
		'import_indexes' => "Herstel indexen",
		'filename' => "Bestandsnaam",
		'compress' => "Comprimeer",
		'decompress' => "Decomprimeer",
		'option' => "Backup opties",
		'filename_compression' => "Hier kunt u het backup doel bestand een naam geven en compressie aanzetten. Het bestand zal gecomprimeerd worden met gebruik van gzip compressie, het uiteindelijke bestand zal een .gz extensie krijgen. Deze optie kan enige tijd duren!<br>Als de backup niet succesvol is verlopen, probeer dan a.u.b. de instellingen te veranderen.",
		'export_core_data' => "Bewaar documenten en sjablonen",
		'import_core_data' => "Herstel documenten en sjablonen",
		'export_object_data' => "Bewaar objecten en classen",
		'import_object_data' => "Herstel objecten en classen",
		'export_binary_data' => "Bewaar binaire data (afbeeldingen, pdf's, ...)",
		'import_binary_data' => "Herstel binaire data (afbeeldingen, pdf's, ...)",
		'export_schedule_data' => "Bewaar planner data",
		'import_schedule_data' => "Herstel planner data",
		'export_settings_data' => "Bewaar instellingen",
		'import_settings_data' => "Herstel instellingen",
		'export_extern_data' => "Bewaar externe bestanden/mappen",
		'import_extern_data' => "Herstel externe bestanden/mappen",
		'export_binary_dep' => "U hebt de optie 'Bewaar binaire data' geselecteerd. De binaire data heeft de documenten nodig, daarom is 'Bewaar documenten en sjablonen' automatisch geselecteerd.",
		'import_binary_dep' => "U hebt de optie 'Herstel binaire data' geselecteerd. De binaire data heeft de documenten data nodig, daarom is 'Herstel documenten en sjablonen' automatisch geselecteerd.",
		'export_schedule_dep' => "U hebt de optie 'Bewaar planner data' geselecteerd. De planner data heeft de documenten en objecten nodig, daarom is 'Bewaar documenten en sjablonen' en 'Bewaar objecten en classen' automatisch geselecteerd.",
		'import_schedule_dep' => "U hebt de optie 'Herstel planner data' geselecteerd. De planner data heeft de documenten data en objecten nodig, daarom is 'Herstel documenten en sjablonen' en 'Herstel objecten en classen' automatisch geselecteerd.",
		'export_temporary_dep' => "U hebt de optie 'Bewaar tijdelijke data' geselecteerd. De tijdelijke data heeft de documenten nodig, daarom is 'Bewaar documenten en sjablonen' automatisch geselecteerd.",
		'import_temporary_dep' => "U hebt de optie 'Herstel tijdelijke data' geselecteerd. De tijdelijke data heeft de documenten data nodig, daarom is 'Herstel documenten en sjablonen' automatisch geselecteerd.",
		'compress_file' => "Comprimeer bestand",
		'export_options' => "Selecteer de data die bewaard moet worden.",
		'import_options' => "Selecteer de data die hersteld moet worden.",
		'extern_exp' => "Deze optie kan enige tijd in beslag nemen en kan leiden tot systeem gerelateerde fouten.",
		'unselect_dep2' => "U heeft '%s' gedeselecteerd. De volgende opties worden automatisch gedeselecteerd.",
		'unselect_dep3' => "Deze opties kunnen opnieuw geselecteerd worden.",
		'gzip' => "gzip", // TRANSLATE
		'zip' => "zip", // TRANSLATE
		'bzip' => "bzip", // TRANSLATE
		'none' => "geen",
		'cannot_split_file' => "Kan bestand '%s' niet voorbereiden op herstel!",
		'cannot_split_file_ziped' => "Het bestand is gecomprimeerd met een niet ondersteunde compressie methode.",
		'export_banner_dep' => "U hebt de optie 'Bewaar banner data' geselecteerd. De banner data heeft de document data nodig, daarom is 'Bewaar documenten en sjablonen' automatisch geselecteerd.",
		'import_banner_dep' => "U hebt de optie 'Herstel banner data' geselecteerd. De banner data heeft de document data nodig, daarom is 'Herstel documenten en sjablonen' automatisch geselecteerd.",
		'delold_notice' => "Er wordt aangeraden dat u de oude bestanden verwijderd van de server om schijf ruimte te creëren.<br>Wilt u doorgaan?",
		'delold_confirm' => "Alle bestaande data zal verwijderd worden!\\nWeet u het zeker?",
		'delete_entry' => "Verwijder %s",
		'delete_nok' => "De bestanden kunnen niet verwijderd worden!",
		'nothing_to_delete' => "Er is niks om te verwijderen!",
		'files_not_deleted' => "Eén of meerdere bestanden konden niet gewist worden! Het is mogelijk dat deze beveiligd zijn tegen schrijven. Verwijder de bestanden handmatig. Het gaat om de volgende bestanden:",
		'delete_old_files' => "Verwijder oude bestanden...",
		'export_configuration_data' => "Bewaar configuratie",
		'import_configuration_data' => "Herstel configuratie",
		'import_export_data' => "Herstel export data",
		'export_export_data' => "Bewaar export data",
		'export_versions_data' => "Save version data", // TRANSLATE
		'export_versions_binarys_data' => "Save Version-Binary-Files", // TRANSLATE
		'import_versions_data' => "Restore version data", // TRANSLATE
		'import_versions_binarys_data' => "Restore Version-Binary-Files", // TRANSLATE

		'export_versions_dep' => "You have selected the option 'Save version data'. The version data need the documents, objects and version-binary-files and because of that, 'Save documents and templates', 'Save object and classes' and 'Save Version-Binary-Files' has been automatically selected.", // TRANSLATE
		'import_versions_dep' => "You have selected the option 'Restore version data'. The version data need the documents data, object data an version-binary-files and because of that, 'Restore documents and templates', 'Restore objects and classes and 'Restore Version-Binary-Files' has been automatically selected.", // TRANSLATE

		'export_versions_binarys_dep' => "You have selected the option 'Save Version-Binary-Files'. The Version-Binary-Files need the documents, objects and version data and because of that, 'Save documents and templates', 'Save object and classes' and 'Save version data' has been automatically selected.", // TRANSLATE
		'import_versions_binarys_dep' => "You have selected the option 'Restore Version-Binary-Files'. The Version-Binary-Files need the documents data, object data an version data and because of that, 'Restore documents and templates', 'Restore objects and classes and 'Restore version data' has been automatically selected.", // TRANSLATE

		'del_backup_confirm' => "Wilt u het geselecteerde backup bestand verwijderen?",
		'name_notok' => "De bestandsnaam is niet juist!",
		'backup_deleted' => "Het backup bestand %s is verwijderd",
		'error_delete' => "Het backup bestand kan niet verwijderd worden! U kunt proberen het bestand te verwijderen via FTP in de webEdition/we_backup map.",
		'core_info' => 'Alle documenten en sjablonen.',
		'object_info' => 'Objecten en classen vanuit de Database-/Object module.',
		'binary_info' => 'De binaire gegevens - afbeeldingen, PDFs en andere documenten.',
		'user_info' => 'Gebruikers en account gegevens vanuit de gebruikers module.',
		'customer_info' => 'Klant en account gegevens vanuit de Klant module.',
		'shop_info' => 'Bestelgegevens vanuit de Winkel module.',
		'workflow_info' => 'Gegevens vanuit de Workflow module.',
		'todo_info' => 'Taken en berichten vanuit de Taak-/Berichten module.',
		'newsletter_info' => 'Gegevens vanuit de Nieuwsbrief module.',
		'banner_info' => 'Banner en statistieken vanuit de Banner module.',
		'schedule_info' => 'Planner data vanuit de Planner module.',
		'settings_info' => 'webEdition applicatie instellingen.',
		'temporary_info' => 'Gegevens vanuit ongepubliceerde documenten en objecten.',
		'export_info' => 'Gegevens vanuit de Export module.',
		'glossary_info' => 'Data from the glossary.', // TRANSLATE
		'versions_info' => 'Data from Versioning.', // TRANSLATE
		'versions_binarys_info' => 'This option could take some time and memory because the folder /webEdition/we/versions/ could be very large. It is recommended to save this folder manually.', // TRANSLATE


		'import_voting_data' => "Herstel onderzoeksgegevens",
		'export_voting_data' => "Bewaar onderzoeksgegevens",
		'voting_info' => 'Gegevens uit de peiling module.',
		'we_backups' => 'webEdition backups',
		'other_files' => 'Andere bestanden',
		'filename_info' => 'Voer de naam van het backup bestand in.',
		'backup_log_exp' => 'De log wordt bewaard in /webEdition/we_backup/data/lastlog.php',
		'export_backup_log' => 'Maak log aan',
		'download_file' => 'Download bestand',
		'import_file_found' => 'Het bestand lijkt op een webEdition import bestand. Gebruik a.u.b de \"Importeer/Exporteer\" optie in het menu \"Bestand\" om de gegevens te importeren.',
		'customer_import_file_found' => 'Het bestand lijkt op een import bestand met klantgegevens. Gebruik a.u.b de \"Importeer/Exporteer\" optie vanuit de klanten module (PRO) om de gegevens te importeren.',
		'import_file_found_question' => 'Wilt u het huidige dialoog venster sluiten en de importeer/exporteer hulp starten?',
		'format_unknown' => 'Het bestands formaat is onbekend!',
		'upload_failed' => 'Het bestand kan niet geüpload worden. Controleer a.u.b. of het bestand groter is dan %s',
		'file_missing' => 'Het backup bestand ontbreekt!',
		'recover_option' => 'Importeer opties',
		'no_resource' => 'Kritieke fout: Er zijn niet genoeg onderdelen om de backup af te ronden!',
		'error_compressing_backup' => 'Er is een fout opgetreden tijdens het comprimeren van de backup, waardoor de backup niet kon worden afgerond!',
		'error_timeout' => 'Er is een timeout opgetreden tijdens het aanmaken van de backup, waardoor de backup niet kon worden afgerond!',
		'export_spellchecker_data' => "Bewaar gegevens spellingscontrole",
		'import_spellchecker_data' => "Herstel gegevens spellingscontrole",
		'spellchecker_info' => 'Gegevens voor spellingscontrole: instellingen, algemene en persoonlijke woordenboeken.',
		'import_banner_data' => "Herstel banner data",
		'export_banner_data' => "Bewaar banner data",
		'export_glossary_data' => "Save glossary data", // TRANSLATE
		'import_glossary_data' => "Restore glossary data", // TRANSLATE

		'protect' => "Bescherm backup bestand",
		'protect_txt' => "Het backup bestand wordt beschermd tegen onbevoegde download met additionele php code. Deze bescherming vereist additionele schijfruimte voor het importeren!",
		'recover_backup_unsaved_changes' => "Enkele geopende bestanden bevatten niet bewaarde wijzigingen. Controleer deze a.u.b. voordat u verder gaat.",
		'file_not_readable' => "Het backup bestand is niet leesbaar. Controleer a.u.b. de rechten van het bestand.",
		'tools_import_desc' => "Here you can restore webEdition tools data. Please select the desired tools from the list.", // TRANSLATE
		'tools_export_desc' => "Here you can save webEdition tools data. Please select the desired tools from the list.", // TRANSLATE

		'ftp_hint' => "Attention! Use the Binary mode for the download by FTP if the backup file is zip compressed! A download in ASCII 	mode destroys the file, so that it cannot be recovered!", // TRANSLATE

		'convert_charset' => "Attention! Using this option in an existing site can lead to total loss of all data, please follow the instruction in http://documentation.webedition.org/de/webedition/administration/charset-conversion-of-legacy-sites", // TRANSLATE

		'convert_charset_data' => "While importing the backup, convert the site from ISO to UTF-8", // TRANSLATE

		'view_log' => "Backup-Log", // TRANSLATE
		'view_log_not_found' => "The backup log file was not found! ", // TRANSLATE
		'view_log_no_perm' => "You do not have the needed permissions to view the backup log file! ", // TRANSLATE
);