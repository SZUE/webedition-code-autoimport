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
 * Language file: global.inc.php
 * Provides language strings.
 * Language: English
 */
$l_global = array(
		'new_link' => "Uusi linkki", // It is important to use the GLOBALS ARRAY because in linklists, the file is included in a function.
		'load_menu_info' => "Ladataan tietoja!<br>Lataaminen voi kestää jonkin aikaa kun ladataan useita valikkoelementtejä ...",
		'text' => "Teksti",
		'yes' => "Kyllä",
		'no' => "Ei",
		'checked' => "Rastitettu",
		'max_file_size' => "Maksimi tiedoston koko (tavuina)",
		'default' => "Oletus",
		'values' => "Arvot",
		'name' => "Nimi",
		'type' => "Tyyppi",
		'attributes' => "Määreet",
		'formmailerror' => "Lomaketta ei voitu lähettä, koska:",
		'email_notallfields' => "Et ole täyttänyt kaikkia tarvittavia kenttiä!",
		'email_ban' => "Sinulla ei ole oikeuksia käyttää tätä skriptiä!",
		'email_recipient_invalid' => "Vastaanottajan osoite on virheellinen!",
		'email_no_recipient' => "Vastaanottajan osoitetta ei ole olemassa!",
		'email_invalid' => "<b>Sähköpostiosoitteesi</b> on virheellinen!",
		'captcha_invalid' => "Syötetty suojakoodi on väärä!",
		'question' => "Kysymys",
		'warning' => "Varoitus",
		'we_alert' => "Toimintoa ei ole webEditionin demoversiossa!",
		'index_table' => "Indeksitaulukko",
		'cannotconnect' => "Ei voitu yhdistää webEdition -palvelimeen!",
		'recipients' => "Formmail -vastaanottajat",
		'recipients_txt' => "Kirjoita kaikki sähköpostiosoitteet jotka voivat vastaanottaa lomakkeita jotka lähetetään formmail -funktiolla (&lt;we:form type=&quot;formmail&quot; ..&gt;). Jos et kirjoita sähköpostiosoitetta, et voi lähettää lomakkeita käyttämällä formmail -funktiota!",
		'std_mailtext_newObj' => "Uusi objekti %s Luokkaan %s on luotu!",
		'std_subject_newObj' => "Uusi objekti",
		'std_subject_newDoc' => "Uusi dokumentti",
		'std_mailtext_newDoc' => "Uusi dokumentti %s on luotu!",
		'std_subject_delObj' => "Objekti poistettu",
		'std_mailtext_delObj' => "Objekti %s on poistettu!",
		'std_subject_delDoc' => "Dokumentti poistettu",
		'std_mailtext_delDoc' => "Dokumentti %s on poistettu!",
		'we_make_same' => array(
				'text/html' => "Uusi sivu tallentamisen jälkeen",
				'text/webedition' => "Uusi sivu tallentamisen jälkeen",
				'objectFile' => "Uusi objekti tallennuksen jälkeen",
		),
		'no_entries' => "Tuloksia ei löytynyt!",
		'save_temporaryTable' => "Tallenna väliaikaiset dokumentit",
		'save_mainTable' => "Tallenna uudelleen päätietokannan taulu",
		'add_workspace' => "Lisää työtila",
		'folder_not_editable' => "Tätä hakemisto ei voida valita!",
		'modules' => "Moduulit",
		'modules_and_tools' => "Moduulit ja työkalut",
		'center' => "Keskellä",
		'jswin' => "Ponnahdusikkuna",
		'open' => "Avaa",
		'posx' => "x kohdistus",
		'posy' => "y kohdistus",
		'status' => "Tila",
		'scrollbars' => "Vierityspalkit",
		'menubar' => "Valikkopalkki",
		'toolbar' => "Työkalupalkki",
		'resizable' => "Muutettavissa",
		'location' => "Paikka",
		'title' => "Otsikko",
		'description' => "Kuvaus",
		'required_field' => "Pakolliset kentät",
		'from' => "Mistä",
		'to' => "Mihin",
		'search' => "Etsi",
		'in' => "mistä",
		'we_rebuild_at_save' => "Automaattinen uudelleen rakennus",
		'we_publish_at_save' => "Julkaise tallentamisen jälkeen",
		'we_new_doc_after_save' => "Uusi dokumentti tallentamisen jälkeen",
		'we_new_folder_after_save' => "Uusi hakemisto tallennuksen jälkeen",
		'we_new_entry_after_save' => "Uusi kohde tallennuksen jälkeen",
		'wrapcheck' => "Rivitys",
		'static_docs' => "Staattinen dokumentti",
		'save_templates_before' => "Tallenna sivupohjat ensin",
		'specify_docs' => "Dokumenttit tietyllä tavalla",
		'object_docs' => "Kaikki objektit",
		'all_docs' => "Kaikki dokumentit",
		'ask_for_editor' => "Kysy editoria",
		'cockpit' => "Aloitus",
		'introduction' => "Johdanto",
		'doctypes' => "Dokumenttityypit",
		'content' => "Sisältö",
		'site_not_exist' => "Sivua ei ole olemassa!",
		'site_not_published' => "Sivua ei ole julkaistu!",
		'required' => "Syöte vaaditaan!",
		'all_rights_reserved' => "Kaikki oikeudet pidätetään",
		'width' => "Leveys",
		'height' => "Korkeus",
		'new_username' => "Uusi käyttäjänimi",
		'username' => "Käyttäjänimi",
		'password' => "Salasana",
		'documents' => "Dokumentit",
		'templates' => "Sivupohjat",
		'objects' => "Objektit",
		'licensed_to' => "Lisenssin haltija",
		'left' => "Vasen",
		'right' => "Oikea",
		'top' => "Ylä",
		'bottom' => "Ala",
		'topleft' => "Ylävasen",
		'topright' => "Yläoikea",
		'bottomleft' => "Alavasen",
		'bottomright' => "Alaoikea",
		'true' => "Kyllä",
		'false' => "Ei",
		'showall' => "Näytä kaikki",
		'noborder' => "Ei reunusta",
		'border' => "Reunus",
		'align' => "Paikka",
		'hspace' => "Vaakaväli (Hspace)",
		'vspace' => "Pystyväli (Vspace)",
		'exactfit' => "Tarkka sovitus",
		'select_color' => "Valitse väri",
		'changeUsername' => "Vaihda käyttäjänimi",
		'changePass' => "Vaihda salasana",
		'oldPass' => "Vanha salasana",
		'newPass' => "Uusi salasana",
		'newPass2' => "Toista uusi salasana",
		'pass_not_confirmed' => "Salasanat eivät täsmää!",
		'pass_not_match' => "Vanha salasana on väärä!",
		'passwd_not_match' => "Salasanat eivät täsmää!",
		'pass_to_short' => "Salasanan pituus on oltava vähintaa 4 merkkiä!",
		'pass_changed' => "Salasana vaihdettu!",
		'pass_wrong_chars' => "Salasana voi sisältää vain alfa-numeerisia merkkejä (a-z, A-Z ja 0-9)!",
		'username_wrong_chars' => "Käyttäjätunnus voi sisältää vain alfa-numeerisia merkkejä (a-z, A-Z ja 0-9) ja '.', '_' tai '-'!",
		'all' => "Kaikki",
		'selected' => "Valittu",
		'username_to_short' => "Käyttäjänimessä on oltava vähintään 4 merkkiä!",
		'username_changed' => "Käyttäjänimi vaihdettu!",
		'published' => "Julkaistu",
		'help_welcome' => "Tervetuloa webEdition ohjeeseen",
		'edit_file' => "Muokkaa tiedostoa",
		'docs_saved' => "Dokumentit tallennettu!",
		'preview' => "Esikatsele",
		'close' => "Sulje ikkuna",
		'loginok' => "<strong>Kirjautuminen onnistui, Odota hetkinen!</strong><br>webEdition avautuu uuteen ikkunaan. Jos ikkuna ei avaudu, varmista ettet ole estänyt ponnahdusikkunoiden avautumista selaimestasi!",
		'apple' => "&#x2318;",
		'shift' => "SHIFT",
		'ctrl' => "CTRL",
		'required_fields' => "Pakolliset kentät",
		'no_file_uploaded' => "<p class=\"defaultfont\">Tuo tiedosto.</p>",
		'openCloseBox' => "Avaa/Sulje",
		'rebuild' => "Rakenna uudelleen",
		'unlocking_document' => "poistaa lukinnan webEdition -dokumentista",
		'variant_field' => "Muunnelmakenttä",
		'redirect_to_login_failed' => "Klikkaa seuraavaa linkkiä jos selaintasi ei uudelleenohjata seuraanvan 30 sekunnin kuluessa ",
		'redirect_to_login_name' => "webEdition kirjautumisnimi",
		'untitled' => "Nimetön",
		'no_document_opened' => "Dokumenttia ei ole avoinna!",
		'credits_team' => "webEdition Team", // TRANSLATE
		'developed_further_by' => "developed further by", // TRANSLATE
		'with' => "with the", // TRANSLATE
		'credits_translators' => "Translations", // TRANSLATE
		'credits_thanks' => "Thanks to", // TRANSLATE
		'unable_to_call_ping' => "Connection to server is lost - RPC: Ping!", // TRANSLATE
		'unable_to_call_setpagenr' => "Connection to server is lost - RPC: setPageNr!", // TRANSLATE
		'nightly-build' => "nightly Build", // TRANSLATE
		'alpha' => "Alpha", // TRANSLATE
		'beta' => "Beta", // TRANSLATE
		'rc' => "RC", // TRANSLATE
		'preview' => "preview", // TRANSLATE
		'release' => "official release", // TRANSLATE
		'categorys' => "Kategoriat",
		'navigation' => "Navigaatio",
);
