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
$l_modules_voting = array(
		'no_perms' => 'You do not have permission to use this option.',
		'delete_alert' => 'Verwijder de huidige peiling/groep.\\n Weet u het zeker?',
		'result_delete_alert' => 'Delete the current voting results.\\nAre you sure?', // TRANSLATE
		'nothing_to_delete' => 'Er is niks om te verwijderen!',
		'nothing_to_save' => 'Er is niks om te bewaren',
		'we_filename_notValid' => 'Ongeldige gebruikersnaam!\\nGeldige karakters zijn alfa numeriek, boven- en onderkast, evenals de underscore, koppelteken, punt en spatie (a-z, A-Z, 0-9, _, -, ., )',
		'menu_new' => 'Nieuw',
		'menu_save' => 'Bewaar',
		'menu_delete' => 'Verwijder',
		'menu_exit' => 'Sluit',
		'menu_info' => 'Info', // TRANSLATE
		'menu_help' => 'Help', // TRANSLATE
		'headline' => 'Namen en achternamen',
		'headline_name' => 'Naam',
		'headline_publish_date' => 'Creëer Datum',
		'headline_data' => 'Onderzoeksgegevens',
		'publish_date' => 'Datum',
		'publish_format' => 'Formaat',
		'published_on' => 'Gepubliceerd op',
		'total_voting' => 'Totaal peiling',
		'reset_scores' => 'Herstel uitslag',
		'inquiry_question' => 'Vraag',
		'inquiry_answers' => 'Antwoord',
		'question_empty' => 'De vraag is leeg, voer a.u.b. een vraag in!',
		'answer_empty' => 'Eén of meer antwoorden zijn leeg, voer a.u.b een antwoord in!',
		'invalid_score' => 'De uitslag moet een nummer zijn, probeer het a.u.b opnieuw!',
		'headline_revote' => 'Controle over herstemming',
		'headline_help' => 'Help', // TRANSLATE

		'inquiry' => 'Onderzoek',
		'browser_vote' => 'Browser kan niet opnieuw stemmen voor',
		'one_hour' => '1 uur',
		'feethteen_minutes' => '15 min.', // TRANSLATE
		'thirthty_minutes' => '30 min.', // TRANSLATE
		'one_day' => '1 dag',
		'never' => '--nooit--',
		'always' => '--altijd--',
		'cookie_method' => 'Door middel van cookie methode',
		'ip_method' => 'Door middel van IP methode',
		'time_after_voting_again' => 'Tijd tot opnieuw stemmen',
		'cookie_method_help' => 'Als u niet de IP methode kunt gebruiken, selecteer deze. Onthou dat sommige gebruikers cookies uitgeschakeld hebben in hun browser.',
		'ip_method_help' => 'Indien uw website alleen intranet toegang heeft, en uw gebruikers niet verbinden via een proxy, selecteer dan deze methode. Onthou dat sommige servers dynamisch een IP toekennen.',
		'time_after_voting_again_help' => 'Om meerdere stemmen te verkomen van één specifiek browser/IP, kies een aangewezen tijdstip waarna gestemd kan worden vanaf die browser. Als u wilt dat er vanaf een specifieke browser/computer slechts één keer gestemd kan worden, selecteer dan \"nooit\".',
		'property' => 'Eigenschappen',
		'variant' => 'Versie',
		'voting' => 'Peiling',
		'result' => 'Resultaat',
		'group' => 'Groep',
		'name' => 'Naam',
		'newFolder' => 'Nieuwe groep',
		'save_group_ok' => 'De groep is bewaard.',
		'save_ok' => 'De peiling is bewaard.',
		'path_nok' => 'Het pad is niet correct!',
		'name_empty' => 'De naam mag niet leeg zijn!',
		'name_exists' => 'De naam bestaat al!',
		'wrongtext' => 'De naam is niet geldig!',
		'voting_deleted' => 'De peiling is succesvol verwijderd.',
		'group_deleted' => 'De groep is succesvol verwijderd.',
		'access' => 'Toegang',
		'limit_access' => 'Beperk toegang',
		'limit_access_text' => 'Verleen toegang voor de volgende gebruikers',
		'variant_limit' => 'Er moet minstens één versie aanwezig zijn in het onderzoek!',
		'answer_limit' => 'Het onderzoek moet uit minstens twee antwoorden bestaan!',
		'valid_txt' => 'De checkbox "actief" moet geactiveerd worden, zodat de peiling op uw pagina bewaard wordt en wordt "geparkeerd" aan het eind van de geldigheid. Bepaal met de dropdown menus de datum en de tijd waarin de peiling zou moeten lopen. Na deze datum worden er geen stemmingen meer geaccepteerd.',
		'active_till' => 'Actief tot',
		'valid' => 'Geldigheid',
		'export' => 'Exporteer',
		'export_txt' => 'Exporteer peilings gegevens naar een CSV bestand (komma gescheiden waardes).',
		'csv_download' => "Download CSV bestand",
		'csv_export' => "Bestand '%s' is bewaard.",
		'fallback' => 'Terugval IP methode',
		'save_user_agent' => 'Bewaar/Vergelijk gegevens van de user-agent',
		'save_changed_voting' => "Voting has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'voting_log' => 'Protocol Stemmen',
		'forbid_ip' => 'Blokkeer de volgende IP adressen',
		'until' => 'tot',
		'options' => 'Opties',
		'control' => 'Controleer',
		'data_deleted_info' => 'De gegevens zijn verwijderd!',
		'time' => 'Tijd',
		'ip' => 'IP', // TRANSLATE
		'user_agent' => 'User-agent', // TRANSLATE
		'cookie' => 'Cookie', // TRANSLATE
		'delete_ipdata_question' => 'U wilt alle opgeslagen IP gegevens verwijderen. Weet u dit zeker?',
		'delete_log_question' => 'U wilt alle Stemmings Log Invoeren verwijderen.  Weet u dit zeker?',
		'delete_ipdata_text' => 'De opgeslagen gegevens beslaan %s Bytes van het geheugen. Verwijder deze d.m.v. de knop \Verwijder\'. Neem in overweging dat alle opgeslagen IP gegevens verwijderd worden waardoor de stemming herhaald kan worden.',
		'status' => 'Status', // TRANSLATE
		'log_success' => 'Gelukt',
		'log_error' => 'Fout',
		'log_error_active' => 'Fout: niet actief',
		'log_error_revote' => 'Fout: nieuwe stemming',
		'log_error_blackip' => 'Fout: Geblokkeerde IP',
		'log_is_empty' => 'De log is leeg!',
		'enabled' => 'Geactivateerd',
		'disabled' => 'Gedeactiveerd',
		'log_fallback' => 'Terugvallen',
		'new_ip_add' => 'Voer a.u.b. het nieuwe IP adres in!',
		'not_valid_ip' => 'Het IP adres is niet geldig',
		'not_active' => 'The entered datum is in the past!', // TRANSLATE

		'headline_datatype' => 'Type of Inquiry', // TRANSLATE
		'AllowFreeText' => 'Allow free text', // TRANSLATE
		'AllowImages' => 'Allow images', // TRANSLATE
		'AllowSuccessor' => 'redirect to:', // TRANSLATE
		'AllowSuccessors' => 'allow individual redirects', // TRANSLATE
		'csv_charset' => "Export charset", // TRANSLATE
		'imageID_text' => "Image ID", // TRANSLATE
		'successorID_text' => "Successor ID", // TRANSLATE
		'mediaID_text' => "Media-ID", // TRANSLATE
		'AllowMedia' => 'Allow Media such as Audio or video files', // TRANSLATE
		'voting-id' => 'Voting ID', // TRANSLATE
		'voting-session' => 'Voting Session', // TRANSLATE
		'voting-successor' => 'successor', // TRANSLATE
		'voting-additionalfields' => 'add. data', // TRANSLATE
		'answerID' => 'answer ID', // TRANSLATE
		'answerText' => 'answer text', // TRANSLATE

		'userid_method' => 'For logged in Users (customer management), compare to saved customer ID (the log has to be active)', // TRANSLATE
		'IsRequired' => 'This is a required field', // TRANSLATE

		'answer_limit' => 'The inquiry must consist of at least two - in case free text answers are allowd one - answers!', // TRANSLATE
		'folder_path_exists' => "Folder already exists!", // TRANSLATE
);