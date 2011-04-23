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
		FILE_TABLE => array(
				'in_wf_warning' => "Dokumentti täytyy tallentaa ennenkuin se voidaan asettaa työnkulkuun!\\nHaluatko tallentaa dokumentin?",
				'not_im_ws' => "Tiedosto ei sijaitse työtilassasi!",
		),
		TEMPLATES_TABLE => array(
				'in_wf_warning' => "Sivupohja täytyy tallentaa ennekuin se voidaan asettaa työnkulkuun!\\nHaluatko tallentaa sivupohjan?",
				'not_im_ws' => "Sivupohja ei sijaitse työtilassasi!",
		),
		'folder' => array(
				'not_im_ws' => "Hakemisto ei sijaitse työtilassasi!",
		),
		'nonew' => array(
				'objectFile' => "Sinulla ei ole oikeuksia luoda objekteja!<br>Sinulla ei ole joko oikeuksia tai Sellaista luokkaa ei ole olemassa jossa työtilasi olisi oikea!",
		),
		'wrong_file' => array(
				'image/*' => "Valitsemasi tiedosto ei ole kuva!",
				'application/x-shockwave-flash' => "Valitsemasi tiedosto ei ole Flash -tiedosto!",
				'video/quicktime' => "Valitsemasi tiedosto ei ole Quicktime -tiedosto!",
				'text/css' => "Tiedostoa ei saatu tallennettua. Joko se ei ole CSS-tiedosto tai levytila on loppu!",
		),
		'no_views' => array(
				'headline' => 'Huomio',
				'description' => 'Tälle dokumentille ei ole näkymää.',
		),
		'navigation' => array(
				'last_document' => 'Muokkaat viimeisintä dokumenttia.',
				'first_document' => 'Muokkaat ensimmäistä dokumenttia.',
				'doc_not_found' => 'Sopivaa dokumenttia ei löytynyt.',
				'no_entry' => 'Ei merkintöjä sivuhistoriassa.',
				'no_open_document' => 'Dokumentteja ei ole auki.',
		),
		'delete_single' => array(
				'confirm_delete' => 'Poistetaanko tämä dokumentti?',
				'no_delete' => 'Dokumenttia ei voitu poistaa.',
				'return_to_start' => 'Dokumentti poistettiin. \\nPalaa helppokäyttötilan alkuun.',
		),
		'move_single' => array(
				'return_to_start' => 'Dokumentti siirrettiin. \\nPalaa helppokäyttötilan aloitusdokumenttiin.',
				'no_delete' => 'Dokumenttia ei voitu siirtää',
		),
		'notice' => "Huomautus",
		'warning' => "Varoitus",
		'error' => "Virhe",
		'noRightsToDelete' => "\\'%s\\' ei voida poistaa! Sinulla ei ole käyttöoikeutta suorittaa tätä toimintoa!",
		'noRightsToMove' => "\\'%s\\' ei voida siirtää! Sinulla ei ole oikeuksia tähän toimenpiteeseen!",
		'delete_recipient' => "Haluatko varmasti poistaa valitut sähköpostiosoitteet?",
		'recipient_exists' => "Sähköpostiosoite on jo olemassa!",
		'input_name' => "Uusi sähköpostiosoite",
		'input_file_name' => "Anna tiedostonimi.",
		'max_name_recipient' => "Sähköpostiosoitteen pituus voi olla maksimissaan 255 merkkiä pitkä!",
		'not_entered_recipient' => "Sähköpostiosoitetta ei ole annettu!",
		'recipient_new_name' => "Muuta sähköpostiosoitetta!",
		'required_field_alert' => "Kenttä '%s' on pakollinen!",
		'phpError' => "webEdition -järjestelmää ei voida käynnistää!",
		'3timesLoginError' => "Kirjautuminen epäonnistui %s kertaa! Odota %s minuuttia ja yritä uudelleen!",
		'popupLoginError' => "webEdition -ikkunaa ei voitu avata!\\n\\nwebEdition -järjestelmä voidaan käynnistää voin jos selaimesi ei estä ponnahdusikkunoiden avautumista.",
		'publish_when_not_saved_message' => "Dokumenttia ei ole tallennettu! Haluatko julkaista sen jokatapauksessa?",
		'template_in_use' => "Sivupohja on käytössä, joten sitä ei voida poistaa!",
		'no_cookies' => "Et ole aktivoinut keksejä. Aktivoi keksit selaimestasi!",
		'doctype_hochkomma' => "Virheellinen nimi! Ei sallitut merkit ovat ' (hipsu) ja , (pilkku)!",
		'thumbnail_hochkomma' => "Virheellinen nimi! Virheellisiä merkkejä ovat ' (heittomerkki) and , (pilkku)!",
		'can_not_open_file' => "Tiedostoa %s ei voida avata!",
		'no_perms_title' => "Pääsy estetty!",
		'no_perms_action' => "Sinulla ei ole oikeuksia suorittaa tätä toimintoa.",
		'access_denied' => "Pääsy estetty!",
		'no_perms' => "Ota yhteyttä omistajaan (%s) tai järjestelmänvalvojaan<br>jos tarvitset oikeuksia!",
		'temporaere_no_access' => "Ei pääsyä!",
		'temporaere_no_access_text' => "Tiedosto \"%s\" on käytössä käyttäjällä \"%s\".",
		'file_locked_footer' => "Dokumentti on käytössä käyttäjällä \"%s\".",
		'file_no_save_footer' => "Sinulla ei ole oikeuksia tallentaa tiedostoa.",
		'login_failed' => "Väärä käyttäjänimi ja/tai salasana!",
		'login_failed_security' => "webEdition -järjestelmää!\\n\\nTurvallisuussyistä kirjautuminen on keskeytetty, koska kirjautumisen maksimiaika kului umpeen!\\n\\nKirjaudu uudelleen järjestelmään.",
		'perms_no_permissions' => "Sinulla ei ole oikeuksia suorittaa toimintoa!",
		'no_image' => "Valitsemasi tiedosto ei ole kuva!",
		'delete_ok' => "Tiedostot ja kansiot on poistettu!",
		'delete_cache_ok' => "Välimuisti onnistuneesti tyhjennetty!",
		'nothing_to_delete' => "Mitään ei ole valittu poistettavaksi!",
		'delete' => "Poista valitut tiedostot?\\nHaluatko jatkaa?",
		'delete_cache' => "Tyhjennä välimuisti valituilta osin?\\nHaluatko jatkaa?",
		'delete_folder' => "Poista valitut hakemistot?\\nHuomaa: Hakemistoa poistettaessa, myös kaikki dokumentit poistettavan hakemiston sisällä poistetaan automaattisesti!\\nHaluatko jatkaa?",
		'delete_nok_error' => "Tiedostoa '%s' ei voitu poistaa.",
		'delete_nok_file' => "Tiedostoa '%s' ei voitu poistaa.\\nTiedosto on voitu kirjoitussuojata. ",
		'delete_nok_folder' => "Hakemistoa '%s' ei voitu poistaa.\\nHakemisto on voitu kirjoitussuojata.",
		'delete_nok_noexist' => "Tiedostoa '%s' ei ole olemassa!",
		'noResourceTitle' => "Kohdetta ei löydy!",
		'noResource' => "Tiedosto tai hakemisto ei ole olemassa!",
		'move_exit_open_docs_question' => "Ennen kuin dokumentteja voi siitää, ne täytyy sulkea. Kaikki tallentamattomat muutokset menetetään sulkemisen yhteydessä. Seuraavat dokumentit suljetaan:\\n\\n",
		'move_exit_open_docs_continue' => 'Jatka?',
		'move' => "Siirrä valitut?\\nHaluatko jatkaa?",
		'move_ok' => "Tiedostot onnistuneesti siirretty!",
		'move_duplicate' => "Kohdehakemistossa on samannimisiä tiedostoja!\\nTiedostoja ei voida siirtää!.",
		'move_nofolder' => "Valittuja tiedostoja ei voida siirtää.\\nHakemistojen siirto ei ole mahdollista.",
		'move_onlysametype' => "Valittuja objekteja ei voida siirtää.\\nObjekteja voidaan siirtää vain oman luokkahakemistonsa sisäisesti.",
		'move_no_dir' => "Valitse kohdehakemisto!",
		'document_move_warning' => "Dokumentin siirron jälkeen on tarpeen suorittaa uudelleenrakennus.<br />Haluatko tehdä sen nyt?",
		'nothing_to_move' => "Mitään ei ole merkitty siirrettäväksi!",
		'move_of_files_failed' => "Yhtä tai useampaa tiedostoa ei voitu siirtää! Siirrä tiedostot manuaalisesti.\\nSeuraavat tiedostot vaikuttuivat:\\n%s",
		'template_save_warning' => "Tämä sivupohja on käytössä %s julkaistulla dokumentilla. Tallennetaanko ne uudelleen? Huomautus: Tämä prosessi voi kestää hetkisen jos dokumentteja on useita!",
		'template_save_warning1' => "Tämä sivupohja on käytössä yhdellä julkaistulla dokumentilla. Tallennetaanko tämä dokumentti uudelleen?",
		'template_save_warning2' => "Tämä sivupohja on käytössä muilla sivupohjilla tai dokumenteilla, tallennetaanko ne uudelleen?",
		'thumbnail_exists' => "Esikatselukuva on jo olemassa!",
		'thumbnail_not_exists' => "Esikatselukuvaa ei ole olemassa!",
		'thumbnail_empty' => "You must enter a name for the new thumbnail!", // TRANSLATE
		'doctype_exists' => "Dokumenttityyppi on jo olemassa!",
		'doctype_empty' => "Sinun on annettava nimi dokumenttityypille!",
		'delete_cat' => "Haluatko varmasti poistaa valitun kategorian?",
		'delete_cat_used' => "Kategoriaa ei voida tuhota!",
		'cat_exists' => "Kategoria on jo olemassa!",
		'cat_changed' => "Kategoria on käytössä! Tallenna uudelleen dokumentit jotka käyttävät tätä kategoriaa!\\nMuutetaanko kategoriaa siitä huolimatta?",
		'max_name_cat' => "Kategorian nimi voi olla maksimissaan 32 merkkiä pitkä!",
		'not_entered_cat' => "Kategorian nimeä ei annettu!",
		'cat_new_name' => "Anna uusi nimi kategorialle!",
		'we_backup_import_upload_err' => "Varmuuskopiotiedostoa ladattaessa tapahtui virha! Ladattavien tiedostojen maksimikoko on %s. Jos varmuuskopiotiedoston koko ylittää rajan, lataa tiedosto hakemistoon webEdition/we_backup käyttäen FTP -tiedostonsiirtoa ja valitse '" . g_l('backup', "[import_from_server]") . "'",
		'rebuild_nodocs' => "Ei dokumentteja jotka vastaisivat valittuja määreitä.",
		'we_name_not_allowed' => "Termit 'we' and 'webEdition' ovat varattuja sanoja joten niitä ei voida käyttää!",
		'we_filename_empty' => "Hakemistolle tai tiedostolle ei ole annettu nimeä!",
		'exit_multi_doc_question' => "Useat avoinna olevat dokumentit sisältävät tallentamattomia muutoksia. Jos jatkat kaikki tallentamattomat muutokset menetetään. Haluatko jatkaa ja hylätä kaikki tekemäsi muutokset?",
		'exit_doc_question_' . FILE_TABLE => "Dokumenttia on muutettu.<br>Haluatko tallentaa muutokset?",
		'exit_doc_question_' . TEMPLATES_TABLE => "Sivupohjaa on muutettu.<br>Haluatko tallentaa muutokset?",
		'deleteTempl_notok_used' => "Yksi tai useampi sivupohja on käytössä ja niitä ei voida poistaa!",
		'deleteClass_notok_used' => "Yksi tai useampi luokka on käytössä ja niitä ei voida poistaa!",
		'delete_notok' => "Virhe poistettaessa!",
		'nothing_to_save' => "Ei tallennettavaa!",
		'nothing_to_publish' => "Julkaisutoiminto on toistaiseksi poistettu käytöstä!",
		'we_filename_notValid' => "Virheellinen tiedoston nimi!\\nSallitut merkit ovat alfa-numeerisia (isot ja pienet kirjaimet), alaviiva, tavuviiva ja piste (a-z, A-Z, 0-9, _, -, .)",
		'empty_image_to_save' => "Valittu kuva on tyhjä.\\n Jatketaanko?",
		'path_exists' => "Tiedostoa tai dokumenttia %s ei voitu tallentaa koska samanniminen dokumentti on kohteessa!",
		'folder_not_empty' => "Yksi tai useampi hakemisto ei ole täysin tyhjä joten poistaminen ei onnistunut! Poista tiedostot käsin.\\nSeuraavat tiedostot on poistettava käsin:\\n%s",
		'name_nok' => "Nimet eivät saa sisältää merkkejä kuten '<' or '>'!",
		'found_in_workflow' => "Yksi ja useampi valinta on työnkulussa! Haluatko poistaa valinnat työnkulusta?",
		'import_we_dirs' => "Yrität tuoda webEdition hakemistosta!\\nHakemistot on suojattu webEdition -järjestelmässä ja niitä ei voida käyttää tuomiseen!",
		'no_file_selected' => "Tuontitiedostoa ei ole valittu!",
		'browser_crashed' => "Ikkunaa ei voitu avata koska selaimessa tapahtui virhe! Tallenna työt ja käynnistä selain uudelleen.",
		'copy_folders_no_id' => "Tallenna nykyinen hakemisto ensin!",
		'copy_folder_not_valid' => "Samaa hakemistoa tai juurihakemistoja ei voida kopioida!",
		'cockpit_not_activated' => 'Toimintoa ei voi suorittaa koska pika-aloitus ei ole auki.',
		'cockpit_reset_settings' => 'Haluatko varmasti poistaa nykyiset pika-aloituksen asetukset ja palauttaa oletusasetukset?',
		'save_error_fields_value_not_valid' => 'Korostetut kentät sisältävät virheellisiä syötteitä.\\nOle hyvä ja syötä kelvollista dataa.',
		'eplugin_exit_doc' => "Dokumenttia on muokattu ulkoisella ohjelmalla. Yhteys ulkoisen ohjelman ja webEditionin välillä suljetaan ja sisältöä ei enää synkronoida.\\nHaluatko sulkea dokumentin?",
		'delete_workspace_user' => "Hakemistoa %s ei voitu poistaa! Se on asetettu työtilaksi seuraaville käyttäjäryhmille tai käyttäjille:\\n%s",
		'delete_workspace_user_r' => "Hakemistoa %s ei voitu poistaa! Hakemiston sisällä on alihakemistoja jotka on asetettu työtilaksi seuraaville käyttäjäryhmille tai käyttäjille:\\n%s",
		'delete_workspace_object' => "Hakemistoa %s ei voitu poistaa! Se on asetettu työtilaksi seuraaville objekteille:\\n%s",
		'delete_workspace_object_r' => "Hakemistoa %s ei voitu poistaa! Hakemiston sisällä on alihakemistoja jotka on asetettu työtilaksi seuraaville objekteille:\\n%s",
		'field_contains_incorrect_chars' => "Kenttä (tyyppiä %s) sisältää virheellisiä merkkejä.",
		'field_input_contains_incorrect_length' => "Kenttätyypin \'Tekstikenttä\' maksimipituus on 255 merkkiä. Jos tarvitset enemmän tilaa, käytä kenttätyyppiä \'Iso tekstikenttä\'.",
		'field_int_contains_incorrect_length' => "Kenttätyypin \'Kokonaisluku\' maksimipituus on 10 merkkiä.",
		'field_int_value_to_height' => "Kenttätyypin \'Kokonaisluku\' maksimiarvo on 2147483647.",
		'we_filename_notValid' => "Virheellinen tiedoston nimi\\nSallitut merkit ovat alfa-numeerisia, isot ja pienet kirjaimet, alaviiva, tavuviiva ja piste (a-z, A-Z, 0-9, _, -, .)", // CHECK

		'error_fields_value_not_valid' => 'Invalid entries in input fields!',
		'discard_changed_data' => 'Tallentamattomat muutokset menetetään, haluatko jatkaa ?',
		'login_denied_for_user' => "Kirjautuminen epäonnistui. Käyttäjäkirjautuminen ei ole käytössä",
		'no_perm_to_delete_single_document' => "Sinulla ei ole tarvittavia oikeuksia dokumentin poistoon.",
		'confirm' => array(
				'applyWeDocumentCustomerFiltersDocument' => "Tiedosto on siirretty hakemistoon, jossa on poikkeavat käyttäjäoikeudet. Otetaanko hakemiston asetukset käyttöön tässä dokumentissa?",
				'applyWeDocumentCustomerFiltersFolder' => "Hakemisto on siirretty hakemistoon, jossa on poikkeavat käyttäjäoikeudet. Otetaanko hakemiston asetukset käyttöön tässä hakemistossa ja kaikissa sen alihakemistoissa? ",
		),
		'field_in_tab_notvalid_pre' => "Asetuksia ei voitu tallentaa, koska seuraavat kentät sisältävät virheellisiä arvoja:",
		'field_in_tab_notvalid' => ' - kenttä %s välilehdellä %s',
		'field_in_tab_notvalid_post' => 'Korjaa kentät ennen asetusten tallennusta.',
		'discard_changed_data' => 'Tallentamattomat tiedot menetetään, haluatko jatkaa ?',
);


if (defined("OBJECT_FILES_TABLE")) {
	$l_alert = array_merge($l_alert, array(
			'in_wf_warning' => "Dokumentti täytyy tallentaa ennenkuin se voidaan asettaa työnkulkuun!\\nHaluatko tallentaa dokumentin?",
			'in_wf_warning' => "Luokka täytyy tallentaa ennenkuin se voidaan asettaa työnkulkuun!\\nHaluatko tallentaa luokan?",
			'exit_doc_question_' . OBJECT_TABLE => "Luokkaa on muutettu.<BR>Haluatko tallentaa muutokset?",
			'exit_doc_question_' . OBJECT_FILES_TABLE => "Objektia on muutettu.<br>Haluatko tallentaa muutokset?",
					));
}