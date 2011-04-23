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
 * Language file: shop.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_shop["user_saved_ok"] = "Käyttäjä '%s' on tallennettu.";
$l_modules_shop["user_saved_nok"] = "Käyttäjää '%s' ei voitu tallentaa!";
$l_modules_shop["nothing_to_save"] = "Ei tallennettavaa!";
$l_modules_shop["username_exists"] = "Käyttäjänimi '%s' on jo olemassa!";
$l_modules_shop["username_empty"] = "Käyttäjän nimeä ei ole syötetty!";
$l_modules_shop["user_deleted"] = "Käyttäjä '%s' on poistettu. ";
$l_modules_shop["nothing_to_delete"] = "Ei poistettavaa!";
$l_modules_shop["delete_last_user"] = "Ainakin yksi järjestelmänvalvoja tarvitaan hallintaan.\\nEt voi poistaa järjestelmänvalvojaa.";
$l_modules_shop["modify_last_admin"] = "Ainakin yksi järjestelmänvalvoja tarvitaan hallintaan.\\nEt voi muuttaa järjestelmänvalvojan oikeuksia.";
$l_modules_shop["no_order_there"] = "Lajittelua ei avattu!";

$l_modules_shop["user_data"] = "Käyttäjän tiedot";
$l_modules_shop["first_name"] = "Etunimi";
$l_modules_shop["second_name"] = "Sukunimi";
$l_modules_shop["username"] = "Käyttäjänimi";
$l_modules_shop["password"] = "Salasana";

$l_modules_shop["workspace_specify"] = "Määritä työtilat ";
$l_modules_shop["permissions"] = "Oikeudet";
$l_modules_shop["user_permissions"] = "Editori";
$l_modules_shop["admin_permissions"] = "Järjestelmänvalvoja";

$l_modules_shop["password_alert"] = "Salasanan on oltava vähintään 4 merkkiä pitkä.";
$l_modules_shop["delete_alert"] = "Poistetaan kaikki käyttäjätiedot käyttältä '%s'.\\n Oletko varma?";

$l_modules_shop["created_by"] = "Luonut";
$l_modules_shop["changed_by"] = "Muuttanut";

$l_modules_shop["no_perms"] = "Sinulla ei ole oikeuksia käyttää tätä toimintoa!";
$l_modules_shop["ue_data"] = "Yleistiedot ";

$l_modules_shop["stat"] = "Statistiset tiedot";
$l_modules_shop["del_shop"] = "Oletko varma että haluat poistaa tilauksen?";
$l_modules_shop["order_liste"] = "Asiakkaan kaikki tilaukset:";

$l_modules_shop["einfueg"] = "Add item";
$l_modules_shop["pref"] = "Shop setting";
$l_modules_shop["waehrung"] = "Valuutta";
$l_modules_shop["mwst"] = "Sales tax";
$l_modules_shop["mwst_expl"] = "Versiosta 3.5 lähtien on mahdollista käyttää useira ALV arvoja jotka tallennetaan suoraan tuoteartikkeliin tilauksen sisälle. Tänne tallennettu arvo on vain vanhempien tilausten käyttöön. Muutos tähän arvoon vaikuttaa kaikkiin tilauksiin joilla ei ole vakio ALV-arvoa tai jonka tuotteisiin ei ole suoraan tallennettu omaa ALV-arvoa.";
$l_modules_shop["format"] = "Number format";
$l_modules_shop["pageMod"] = "Tietueita / Sivu";

$l_modules_shop["bestellungvom"] = "Käyttäjän tilaukset";
$l_modules_shop["keinedaten"] = "Parametria ei annettu";
$l_modules_shop["datum"] = "Päivämäärä";
$l_modules_shop["anzahl"] = "Määrä";
$l_modules_shop["umsatzgesamt"] = "Yhteensä";
$l_modules_shop["Artikel"] = "Tuote";
$l_modules_shop["Anzahl"] = "Määrä";
$l_modules_shop["variant"] = "Muunnelma";
$l_modules_shop["customField"] = "Custom kenttä";
$l_modules_shop["customFieldDesc"] = "Syötä muodossa: <i>nimi1=arvo1;nimi2=arvo2; ... </i>";
$l_modules_shop["Titel"] = "Otsikko";
$l_modules_shop["Beschreibung"] = "Kuvaus";
$l_modules_shop["Gesamt"] = "Yhteensä";
$l_modules_shop["jsanzahl"] = "Syötä määrä.";

$l_modules_shop["geloscht"] = "Tietue poistettu.";
$l_modules_shop["loscht"] = "Tietue poistettu.";
$l_modules_shop["orderDoesNotExist"] = "Tätä tilausta ei enää ole.";

$l_modules_shop["selectYear"] = "Valitse vuosi";
$l_modules_shop["selectMonth"] = "Valitse kuukausi";
$l_modules_shop["jsanz"] = "Syötä määrä.";
$l_modules_shop["keinezahl"] = "Syöte ei ole numero!";
$l_modules_shop["jsbetrag"] = "Kirjoita summa.";
$l_modules_shop["jsloeschen"] = "Oletko varma että haluat poistaa tuotteen? Toimintoa ei voida palauttaa.";
$l_modules_shop["Preis"] = "Hinta";
$l_modules_shop["MwSt"] = "Arvonlisävero";
$l_modules_shop["gesamtpreis"] = "Yhteensä";
$l_modules_shop["plusVat"] = "plus ALV";
$l_modules_shop["includedVat"] = "sisältää ALV:n";

$l_modules_shop["bestellnr"] = "Tilausnumero.:";
$l_modules_shop["bearbeitet"] = "Käsittelypäivämäärä:";
$l_modules_shop["bezahlt"] = "Maksettu:";
$l_modules_shop["bestaetigt"] = "Confirmed on:"; // TRANSLATE
$l_modules_shop["customA"] = "Status A on:"; // TRANSLATE
$l_modules_shop["customB"] = "Status B on:"; // TRANSLATE
$l_modules_shop["customC"] = "Status C on:"; // TRANSLATE
$l_modules_shop["customD"] = "Status D on:"; // TRANSLATE
$l_modules_shop["customE"] = "Status E on:"; // TRANSLATE
$l_modules_shop["customF"] = "Status F on:"; // TRANSLATE
$l_modules_shop["customG"] = "Status G on:"; // TRANSLATE
$l_modules_shop["customH"] = "Status H on:"; // TRANSLATE
$l_modules_shop["customI"] = "Status I on:"; // TRANSLATE
$l_modules_shop["customJ"] = "Status J on:"; // TRANSLATE
$l_modules_shop["storniert"] = "Cancelled on:"; // TRANSLATE
$l_modules_shop["beendet"] = "Finished on:"; // TRANSLATE
$l_modules_shop["datumeingabe"] = "Päivämäärä täytyy syöttää muodossa kk/pp/vv.";

$l_modules_shop["order_data"] = "Tilauksen ja<br>käyttäjän tiedot";
$l_modules_shop["ordered_articles"] = "Tuotteita tilattu";
$l_modules_shop["order_comments"] = "Muita kommentteja koskien tilausta";
$l_modules_shop["order_view"] = "Tilaus yhteenveto";
$l_modules_shop["bestelldatum"] = "Tilauspäivämäärä:";
$l_modules_shop["jsdatum"] = "Syötä päivämäärä.";
$l_modules_shop["unbearb"] = "Ei käsitelty";
$l_modules_shop["unbezahlt"] = "Ei maksettu";
$l_modules_shop["schonbezahlt"] = "Maksettu";
$l_modules_shop["monat"] = "Kuukausi";
$l_modules_shop["bestellung"] = "Järjestys";


$l_modules_shop["noRecordAlert"] = "Tietuita tälle <strong>Luokka-ID:lle</strong> ei löytynyt.<br />";
$l_modules_shop["noRecordAlert"] .=" Mene ominaisuuksiin muokataksesi !";
$l_modules_shop["einfueg"] = "Lisää tuotenimike";
$l_modules_shop["pref"] = "Ominaisuudet";
$l_modules_shop["paymentP"] = "Maksupalvelun tarjoaja";
$l_modules_shop["waehrung"] = "Valuutta";
$l_modules_shop["mwst"] = "ALV";
$l_modules_shop["format"] = "Numeromuoto";

$l_modules_shop["revenue_list"] = "Vuosittainen tuotto: ";
$l_modules_shop["anual"] = "Vuosi ";
$l_modules_shop["selYear"] = "Valinta ";

// shop_extend
$l_modules_shop["ArtList"] = "Kaikkien tuotenimikkeiden lista";
$l_modules_shop["ArtName"] = "Tuotteen nimi ";
$l_modules_shop["ArtID"] = "ID";
$l_modules_shop["docType"] = "Tyyppi";
$l_modules_shop["artCreate"] = "Luontipäivä";
$l_modules_shop["artCreateAlt"] = "Lajittelu luontipäivän mukaan";
$l_modules_shop["artMod"] = "Viimeksi päivitetty";
$l_modules_shop["artPub"] = "Julkaistu:";
$l_modules_shop["artModAlt"] = "Lajittelu viimeisen päivityksen mukaan";
$l_modules_shop["artHas"] = "Muunnelmat";
$l_modules_shop["artHasAlt"] = "Lajittele muunnelmien mukaan (on muunnelmia/ei ole muunnelmia)";
$l_modules_shop["artNameAlt"] = "Lajittele tuotenimikkeen mukaan ";
$l_modules_shop["artIDAlt"] = "Lajittele ID:n mukaan ";
$l_modules_shop["classSel"] = "Käytettävissä olevat kauppa-luokat: ";

// shop_revenue
$l_modules_shop["artName"] = "Tuotenimike";
$l_modules_shop["artPrice"] = "Hinta";
$l_modules_shop["artOrdD"] = "Tilauspäivä";
$l_modules_shop["artID"] = "Tuote-ID";
$l_modules_shop["artPay"] = "Maksettu";

$l_modules_shop["artTotal"] = "Tuoteartikkeleita yhteensä";
$l_modules_shop["currPage"] = "Sivu";

$l_modules_shop["noRecord"] = "Tietuetta ei löytynyt!";
$l_modules_shop["artNPay"] = "odottaa";
$l_modules_shop["isObj"] = "Objekti";
$l_modules_shop["isDoc"] = "Dokumentti";
$l_modules_shop["classID"] = "Luokka-ID";
$l_modules_shop["classIDext"] = "* Kauppaobjekti-ID [ID,ID,ID ..]";
$l_modules_shop["paypal"] = "PayPal";
$l_modules_shop["saferpay"] = "Saferpay";
$l_modules_shop["lc"] = "Maakoodi";
$l_modules_shop["paypalLcTxt"] = "* (ISO)";
$l_modules_shop["paypalbusiness"] = "Business";
$l_modules_shop["paypalbTxt"] = "* PayPal Sähköposti";



$l_modules_shop["paypalSB"] = "Tili";
$l_modules_shop["paypalSBTxt"] = " Testi- tai Livetili";
$l_modules_shop["saferpayTermLang"] = "Kieli";
$l_modules_shop["saferpayID"] = "Tili-ID";
$l_modules_shop["saferpayIDTxt"] = "* Sarja Nro";
$l_modules_shop["saferpayNo"] = "Ei";
$l_modules_shop["saferpayYes"] = "Kyllä";
$l_modules_shop["saferpayLcTxt"] = "* en, de, fr, it";
$l_modules_shop["saferpaybusiness"] = "Kaupan omistaja";
$l_modules_shop["saferpaybTxt"] = "* Huomautukset- sähköposti";
$l_modules_shop["saferpayAllowCollect"] = "salli \"vastaanottajan maksettava\"?";
$l_modules_shop["saferpayAllowCollectTxt"] = "* kts saferpay Manuaali !";
$l_modules_shop["saferpayDelivery"] = "lisätiedot. Lomake?";
$l_modules_shop["saferpayDeliveryTxt"] = "* Toimitusosoitteelle";
$l_modules_shop["saferpayUnotify"] = "Varmistus";
$l_modules_shop["saferpayUnotifyTxt"] = "* Varmistusmaili asiakkaalle";
$l_modules_shop["saferpayProviderset"] = "Toimittaja asetettu";
$l_modules_shop["saferpayProvidersetTxt"] = "* pilkkueroteltu!";
$l_modules_shop["saferpayCMDPath"] = "suorituspolku (exec-path)";
$l_modules_shop["saferpayCMDPathTxt"] = "* esim. /usr/local/bin/";
$l_modules_shop["saferpayconfPath"] = "conf-path";
$l_modules_shop["saferpayconfPathTxt"] = "* polku saferpaylle";
$l_modules_shop["saferpaydesc"] = "Kuvaus";
$l_modules_shop["saferpaydescTxt"] = "* esim. order";
$GLOBALS["l_shop"]["saferpayError"] = "Saferpay ei ole oikein asennettu. Varmista tiliasetukset. saferpay on palauttanut seuraavat muuttujat:\n<br/>";
$l_modules_shop["NoRevenue"] = "Valitulla ajanjaksolla ei ole myyntituloja";


$l_modules_shop["FormFieldsTxt"] = "Maksunpalvelun tarjoajalle lähetettävissä olevat kentät";
$l_modules_shop["fieldForname"] = "Etunimi";
$l_modules_shop["fieldSurname"] = "Sukunimi";
$l_modules_shop["fieldStreet"] = "Katuosoite";
$l_modules_shop["fieldZip"] = "Postinro.";
$l_modules_shop["fieldCity"] = "Paikkakunta";
$l_modules_shop["fieldEmail"] = "Sähköposti";
$l_modules_shop["SelectAll"] = "Kaikki";
$l_modules_shop["plzh"] = "jokerimerkki";
$l_modules_shop["lastOrder"] = "Viimeisin tilaus - Nro.: %s, %s";
$l_modules_shop["orderNo"] = "No.: %s, %s";
$l_modules_shop["sl"] = "-";
$l_modules_shop["treeYear"] = "Vuosi";


// vats dialogs
$l_modules_shop['vat']['save_success'] = 'ALV tallennettu onnistuneesti.';
$l_modules_shop['vat']['save_error'] = 'ALV arvoa ei voitu tallentaa';
$l_modules_shop['vat']['delete_success'] = 'Poistettiin ALV onnistuneesti.';
$l_modules_shop['vat']['delete_error'] = 'ALV arvoa ei voitu poistaa.';

$l_modules_shop['vat']['new_vat_name'] = 'Uusi ALV';
$l_modules_shop['vat']['js_confirm_delete'] = 'Haluatko varmasti poistaa ALV:n?';

$l_modules_shop['vat']['vat_form_id'] = 'Id';
$l_modules_shop['vat']['vat_form_name'] = 'Nimi';
$l_modules_shop['vat']['vat_form_vat'] = 'ALV arvo';
$l_modules_shop['vat']['vat_form_standard'] = 'Vakio';
$l_modules_shop['vat']['vat_edit_form_headline'] = 'Muokkaa ALV arvoa';
$l_modules_shop['vat']['vat_edit_form_headline_box'] = 'Muokkaa ALV arvoa';
$l_modules_shop['vat']['vat_edit_form_yes'] = 'Kyllä';
$l_modules_shop['vat']['vat_edit_form_no'] = 'Ei';

$l_modules_shop['vat_country']['box_headline'] = 'Sääntö: Minkä osavaltioiden asikkaiden pitää maksaa ALV';
$l_modules_shop['vat_country']['defaultReturn'] = 'Vakioarvo';
$l_modules_shop['vat_country']['defaultReturn_desc'] = 'Vakioarvo määrittää we:ifShopPayVat tagin tuloksen, jos mikään seuraavista säännöistä ei vastaa. Jos sääntöä ei ole määritetty, palautetaan vakioarvo';
$l_modules_shop['vat_country']['stateField'] = 'Maan kenttä';
$l_modules_shop['vat_country']['stateField_desc'] = 'Valitse asiakashallinnan kenttä joka sisältää kotimaan (laskutusosoiteen sijaintimaa). Sen perusteella päätetään tarvitseeko asiakkaan maksaa ALV vai ei.';
$l_modules_shop['vat_country']['statesLiableToVat'] = 'ALV velvolliset osavaltiot';
$l_modules_shop['vat_country']['statesLiableToVat_desc'] = 'Näistä maista tulevien asiakkaiden pitää maksaa ALV.';
$l_modules_shop['vat_country']['statesNotLiableToVat'] = 'Osavaltiot jotka EIVÄT ole velvollisia ALV:on.';
$l_modules_shop['vat_country']['statesNotLiableToVat_desc'] = 'Näistä maista olevien asiakkaiden ei tarvitse maksaa ALV:a.';

$l_modules_shop['vat_country']['statesSpecialRules'] = 'Osavaltiot joilla on erikoissääntöjä';
$l_modules_shop['vat_country']['statesSpecialRules_desc'] = 'Näistä maista olevien asiakkaiden tulee maksaa ALV vain jos erikoissääntö täyttyy.';
$l_modules_shop['vat_country']['statesSpecialRules_condition'] = 'Erikoissääntö';
$l_modules_shop['vat_country']['statesSpecialRules_result'] = 'Tulos';

$l_modules_shop['vat_country']['condition_is_empty'] = 'Tyhjä';
$l_modules_shop['vat_country']['condition_is_set'] = 'Täytetty';

// statusmails
$l_modules_shop['statusmails']['box_headline'] = 'Status display and behavior with E-Mails'; // TRANSLATE
$l_modules_shop['statusmails']['AnzeigeDaten'] = 'Dosplay data'; // TRANSLATE
$l_modules_shop['statusmails']['fieldname'] = 'Field ID'; // TRANSLATE
$l_modules_shop['statusmails']['hidefield'] = 'Field display'; // TRANSLATE
$l_modules_shop['statusmails']['hidefieldCOV'] = 'Customer overview'; // TRANSLATE
$l_modules_shop['statusmails']['hidefieldNein'] = 'display'; // TRANSLATE
$l_modules_shop['statusmails']['hidefieldJa'] = 'hidden'; // TRANSLATE
$l_modules_shop['statusmails']['fieldtext'] = 'Field name'; // TRANSLATE
$l_modules_shop['statusmails']['EMailssenden'] = 'Send e-mails'; // TRANSLATE
$l_modules_shop['statusmails']['EMailssendenNein'] = 'no'; // TRANSLATE
$l_modules_shop['statusmails']['EMailssendenHand'] = 'Button'; // TRANSLATE
$l_modules_shop['statusmails']['EMailssendenAuto'] = 'automatically'; // TRANSLATE
$l_modules_shop['statusmails']['EMailDaten'] = 'E-Mail Data'; // TRANSLATE
$l_modules_shop['statusmails']['hintEMailDaten'] = "Define the senders e-mail address and the name of the sender for the status e-mails. <br/> The e-mail address of the receiver will be extracted from the field of the customer management choosen below. The name of the receiver will be taken from the title-field choosen below and from forename and surname.<br/> Also define the name of the field (in the webedition document), which holds the subject of the mail to be send. <b>If this field is empty, the mail will not be send.</b> "; // TRANSLATE
$l_modules_shop['statusmails']['hintSprache'] = 'Define here, if different documents for each language should be used. Also define the field from customer management, which holds the language of the customer.<br/> The contents of this field will be available in the status mail document in the REQUEST variable $_REQUEST["we_userlanguage"], so you can distinct different languages (i.e. with &lt;we:ifVar type="request" name="we_userlanguage"&gt;).'; // TRANSLATE
$l_modules_shop['statusmails']['hintDokumente'] = 'Please chose for each relevant shop status a document ID, which defines the document to be send. The available languages are defined in the webEdition setting is the tag languages.<br/>The documents (templates) have access to the shop status by the  REQUEST variable $_REQUEST["we_shopstatus"] (in the form "Order", "Confirmation" etc.), so a distinction inside the document is possible.<br/>In the shop status mails, you can access the data of the order with the tag &lt;we:order&gt;, which uses the REQUEST variable $_REQUEST["we_orderid"]. Also see the example in the tag-reference.'; // TRANSLATE

$l_modules_shop['statusmails']['AbsenderAdresse'] = 'Senders e-mail addresse'; // TRANSLATE
$l_modules_shop['statusmails']['AbsenderName'] = 'Senders name'; // TRANSLATE
$l_modules_shop['statusmails']['EMailFeld'] = 'CM e-mail field'; // TRANSLATE
$l_modules_shop['statusmails']['DocumentSubjectField'] = 'Field name within the document for subject'; // TRANSLATE
$l_modules_shop['statusmails']['DocumentAttachmentFieldA'] = 'Field name (we:href) within the doc. Attachment A';// TRANSLATE
$l_modules_shop['statusmails']['DocumentAttachmentFieldB'] = 'Field name (we:href) within the doc. Attachment B';// TRANSLATE
$l_modules_shop['statusmails']['Spracheinstellungen'] = 'Lanugages'; // TRANSLATE
$l_modules_shop['statusmails']['useLanguages'] = 'Use documents<br/> depending on language <br/>(instead of default)'; // TRANSLATE
$l_modules_shop['statusmails']['SprachenFeld'] = 'CM language field'; // TRANSLATE
$l_modules_shop['statusmails']['hintISO'] = 'Only using ISO coded langauges, one can send documents depending on language.'; // TRANSLATE
$l_modules_shop['statusmails']['Dokumente'] = 'Documents'; // TRANSLATE
$l_modules_shop['statusmails']['defaultDocs'] = 'Default <br/> (no lang.<br/> support)'; // TRANSLATE
$l_modules_shop['statusmails']['EMail'] = 'e-mail'; // TRANSLATE
$l_modules_shop['statusmails']['TitelFeld'] = 'CM title field'; // TRANSLATE
$l_modules_shop['statusmails']['bcc'] = 'blind copy to address'; // TRANSLATE
$l_modules_shop['statusmails']['resent'] = 'Are you sure, that the e-mail should be send again?'; // TRANSLATE
$l_modules_shop['shipping']['shipping_package'] = 'Lähetys ja käsittely';
$l_modules_shop['shipping']['prices_are_net'] = 'Hinnat ovat nettohintoja';
$l_modules_shop['shipping']['insert_packaging'] = 'Voimassaolevat käsittely- ja lähetyshinnat';
$l_modules_shop['shipping']['payment_provider'] = 'Maksupalvelun tarjoaja';
$l_modules_shop['shipping']['revenue_view'] = 'Tuotenimikkeet- / Tulot';
$l_modules_shop['shipping']['name'] = 'Name'; // TRANSLATE
$l_modules_shop['shipping']['countries'] = 'Countries'; // TRANSLATE
$l_modules_shop['shipping']['costs'] = 'Costs'; // TRANSLATE
$l_modules_shop['shipping']['order_value'] = 'Order value'; // TRANSLATE
$l_modules_shop['shipping']['shipping_costs'] = 'Shipping_costs'; // TRANSLATE


$l_modules_shop['preferences']['customerFields'] = "Asiakaskentät<br />(Asiakashallintamoduuli)";
$l_modules_shop['preferences']['orderCustomerFields'] = 'Asiakaskentät<br />(Tilaus)';
$l_modules_shop['preferences']['CountryField'] = 'Field country'; // TRANSLATE
$l_modules_shop['preferences']['LanguageField'] = 'Field Language'; // TRANSLATE
$l_modules_shop['preferences']['ISO-Kodiert'] = 'Field is ISO coded'; // TRANSLATE

$l_modules_shop['preferences']['customerdata'] = 'Asiakastiedot';
$l_modules_shop['preferences']['explanation_customer_odercustomer'] = 'Selitys: Nämä tiedot tallennetaan vain tilauksen yhteyteen. Asiakashallinnan tietoihin ei kosketa.';

$l_modules_shop['order']['edit_order_customer'] = 'Muokkaa tämän tilauksen sisältämiä asiakastietoja.';
$l_modules_shop['order']['open_customer'] = 'Avaa tämä asiakas asiakashallintamoduulissa.';

$l_modules_shop['edit_order']['shipping_costs'] = 'Lähetyskulut';
$l_modules_shop['edit_order']['js_edit_custom_cart_field'] = 'Uusi arvo %s:.';
$l_modules_shop['edit_order']['js_edit_cart_field_noFieldname'] = 'Kirjoita kenttänimi.';
$l_modules_shop['edit_order']['js_saved_cart_field_success'] = 'Tallennettiin ostoskorin kenttä "%s".';
$l_modules_shop['edit_order']['js_saved_cart_field_error'] = 'Ostokorin kenttää "%s" ei saatu tallennettua.';
$l_modules_shop['edit_order']['js_delete_cart_field'] = 'Poistetaanko kenttä %s tilauksesta?';
$l_modules_shop['edit_order']['js_delete_cart_field_success'] = 'Kenttä %s poistettiin tilauksesta.';
$l_modules_shop['edit_order']['js_delete_cart_field_error'] = 'Kenttää %s ei voitu poistaa tilauksesta.';
$l_modules_shop['edit_order']['js_saved_shipping_success'] = 'Tallennetut lähetyskulut.';
$l_modules_shop['edit_order']['js_saved_shipping_error'] = 'Lähetyskuluja ei voitu tallentaa.';
$l_modules_shop['edit_order']['js_saved_customer_success'] = 'Asiakastiedot tallennettu onnistuneesti.';
$l_modules_shop['edit_order']['js_saved_customer_error'] = 'Asiakastietoja ei voitu tallentaa.';
$l_modules_shop['edit_order']['js_edit_vat'] = 'Aseta uusi ALV arvo.';

$l_modules_shop['edit_order']['calculate_vat'] = 'Laske ALV';
$l_modules_shop['edit_order']['js_saved_calculateVat_success'] = 'Tallenna muutokset.';
$l_modules_shop['edit_order']['js_saved_calculateVat_error'] = 'Muutoksia ei voitu tallentaa.';


$l_modules_shop['orderList']['noOrders'] = 'Asiakas ei ole tilannut mitään tähän mennessä';
$l_modules_shop['orderList']['order'] = 'tilaus';
$l_modules_shop['orderList']['orderDate'] = 'tilattu';
$l_modules_shop['orderList']['orderPayed'] = 'maksettu';
$l_modules_shop['orderList']['orderEdited'] = 'käsitelty';

$l_modules_shop['add_article']['title'] = 'Lisää tuotenimike';
$l_modules_shop['add_article']['entry_x_to_y_from_z'] = 'Merkinnät %s - %s / %s';
$l_modules_shop['add_article']['empty_articles'] = 'Tuotenimikkeitä ei löytynyt.';

$l_modules_shop['edit_shipping_cost']['title'] = 'Muokkaa lähetyskuluja';
$l_modules_shop['edit_shipping_cost']['vatRate'] = 'ALV arvo';
$l_modules_shop['edit_shipping_cost']['isNet'] = 'on netto';

$l_modules_shop['add_shop_field'] = 'Lisää kenttä tähän tilaukseen';
$l_modules_shop['field_name'] = 'Nimi';
$l_modules_shop['field_value'] = 'Arvo';
$l_modules_shop['field_empty_js_alert'] = 'Kentän nimi ei saa olla tyhjä';

$l_modules_shop['edit_article_variants'] = 'Muokkaa kauppa-artikkelin muuttujia';
$l_modules_shop['new_entry'] = 'Uusi kirjaus';
$l_modules_shop['paypal'] = array(
		'head_title' => 'Maksua prosessoidaan',
		'redirect_auto' => 'Ole hyvä ja odota kun maksuasi prosessoidaan. Sinut ohjataan PayPaliin hetken kuluttua.',
		'redirect_man' => 'Jos sivua ei automaattisesti ohjata eteenpäin 5 sekunnin kuluessa, klikkaa "PayPal" nappia.',
);