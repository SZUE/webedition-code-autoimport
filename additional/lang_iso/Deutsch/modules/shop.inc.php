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
 * Language file: shop.inc.php
 *
 * Provides language strings.
 *
 * Language: Deutsch
 */
$l_shop = array();

$l_shop["user_saved_ok"] = "Der Benutzer '%s' wurde erfolgreich gespeichert";
$l_shop["user_saved_nok"] = "Der Benutzer '%s' kann nicht gespeichert werden!";
$l_shop["nothing_to_save"] = "Es gibt nichts zu speichern!";
$l_shop["username_exists"] = "Der Benutzername '%s' existiert schon!";
$l_shop["username_empty"] = "Der Benutzername ist nicht ausgef&uuml;llt!";
$l_shop["user_deleted"] = "Der Benutzer '%s' wurde erfolgreich gel�scht!";
$l_shop["nothing_to_delete"] = "Zum dauerhaften Entfernen einer Bestellung �ffnen Sie zun�chst eine existierende!";
$l_shop["delete_last_user"] = "Zur Verwaltung wird mindestens ein Administrator ben�tigt.\\nSie k�nnen den  Administrator nicht l�schen.";
$l_shop["modify_last_admin"] = "Zur Verwaltung wird mindestens ein Administrator ben�tigt.\\nSie k�nnen die Rechte des Administrators nicht �ndern.";
$l_shop["no_order_there"] = "Um einen Artikel hinzuzuf�gen, �ffnen Sie bitte zun�chst eine existierende Bestellung!";

$l_shop["user_data"] = "Benutzerdaten";
$l_shop["first_name"] = "Vorname";
$l_shop["second_name"] = "Nachname";
$l_shop["username"] = "Benutzername";
$l_shop["password"] = "Kennwort";

$l_shop["workspace_specify"] = "Arbeitsbereich spezifizieren";
$l_shop["permissions"] = "Rechte";
$l_shop["user_permissions"] = "Redakteur";
$l_shop["admin_permissions"] = "Administrator";

$l_shop["password_alert"] = "Das Kennwort muss mindestens 4 Zeichen lang sein";
$l_shop["delete_alert"] = "Alle Benutzerdaten von Benutzer '%s' werden gel�scht.\\n Sind Sie sicher?";

$l_shop["created_by"] = "Erstellt von";
$l_shop["changed_by"] = "Ge�ndert von";

$l_shop["no_perms"] = "Sie haben keine Berechtigung, diese Option zu benutzen!";
$l_shop["ue_data"] = "&Uuml;bersicht f&uuml;r ";

$l_shop["stat"] = "Statistische Auswertung";
$l_shop["del_shop"] = "Sind Sie sicher, dass Sie diese Bestellung l�schen wollen?";
$l_shop["order_liste"] = "Alle Bestellungen des Kunden:";

$l_shop["noRecordAlert"] = "In der Datenbank konnte zu der von Ihnen eingegebenen <strong>Klassen-ID</strong> kein Datensatz ermittelt werden.<br />";
$l_shop["noRecordAlert"] .="Bitte wechseln Sie zu den Shop Einstellungen und korrigieren Sie Ihre Eingabe!";
$l_shop["einfueg"] = "Artikel hinzuf&uuml;gen";
$l_shop["pref"] = "Grundeinstellungen";
$l_shop["paymentP"] = "Payment Provider";
$l_shop["waehrung"] = "W&auml;hrung";
$l_shop["mwst"] = "Mehrwertsteuer";
$l_shop["mwst_expl"] = "Seit Version 3.5 haben sie die M�glichkeit mehrere Steuers�tze zu verwenden, die mit dem Artikel innerhalb der Bestellung gespeichert werden. Der hier eingesetzte Wert gilt dann nur noch f�r alle ehemaligen Bestellungen. �nderungen die sie hier machen, wirken sich auf alle Bestellungen aus, die ohne Standard-Mehrwertsteuersatz, bzw. direkt beim Artikel eingestellten Mehrwertsteuersatz durchgef�hrt werden.";
$l_shop["format"] = "Nummernformat";
$l_shop["pageMod"] = "Datens�tze pro Seite";

$l_shop["revenue_list"] = "Umsatz im Jahr: ";
$l_shop["anual"] = "Jahr";
$l_shop["selYear"] = "Auswahl ";

$l_shop["ArtList"] = "Auflistung aller Artikel";
$l_shop["ArtName"] = "Artikelname ";
$l_shop["ArtID"] = "ID";
$l_shop["docType"] = "Typ";
$l_shop["artCreate"] = "Erstellt am";
$l_shop["artCreateAlt"] = "Nach Erstellungsdatum sortieren";
$l_shop["artMod"] = "Letzte &Auml;nderung";
$l_shop["artPub"] = "Ver�ffentlicht am";
$l_shop["artModAlt"] = "Nach &Auml;nderungsdatum sortieren";
$l_shop["artHas"] = "Varianten";
$l_shop["artHasAlt"] = "Nach Variante vorhanden/nicht vorhanden sortieren";


$l_shop["artName"] = "Artikelname";
$l_shop["classSel"] = "vorhandene Shopklassen: ";

$l_shop["artNameAlt"] = "Nach Artikelname sortieren";
$l_shop["artIDAlt"] = "Nach ID sortieren";


$l_shop["artPrice"] = "Preis";
$l_shop["artOrdD"] = "Bestellung vom";
$l_shop["artID"] = "Artikel-ID";
$l_shop["artPay"] = "Bezahlt am";

$l_shop["artTotal"] = "Artikel gesamt";
$l_shop["currPage"] = "Seite";

$l_shop["noRecord"] = "Kein Eintrag gefunden!";
$l_shop["artNPay"] = "noch offen";
$l_shop["isObj"] = "Objekt";
$l_shop["isDoc"] = "Dokument";
$l_shop["classID"] = "Klassen-ID";
$l_shop["classIDext"] = "* Shop-Objekt-ID [ID,ID,ID ..]";
$l_shop["paypal"] = "PayPal";
$l_shop["saferpay"] = "Saferpay";
$l_shop["lc"] = "L�nderkennung";
$l_shop["paypalLcTxt"] = "* Country Code (ISO)";
$l_shop["paypalbusiness"] = "Business";
$l_shop["paypalbTxt"] = "* PayPal Email";
$l_shop["paypalSB"] = "Account"; //
$l_shop["paypalSBTxt"] = " Test oder Live Account";

$l_shop["saferpayTermLang"] = "Sprachausgabe"; // ab hier neu
$l_shop["saferpayID"] = "Account-ID";
$l_shop["saferpayIDTxt"] = "* Seriennummer";
$l_shop["saferpayNo"] = "Nein";
$l_shop["saferpayYes"] = "Ja";
$l_shop["saferpayLcTxt"] = "* en, de, fr, it";
$l_shop["saferpaybusiness"] = "Shop Betreiber";
$l_shop["saferpaybTxt"] = "* Notify Email";
$l_shop["saferpayAllowCollect"] = "Sammeln erlauben?";
$l_shop["saferpayAllowCollectTxt"] = "* saferpay Handbuch beachten!";
$l_shop["saferpayDelivery"] = "zus�tzl. Formular?";
$l_shop["saferpayDeliveryTxt"] = "* f�r Lieferadresse";
$l_shop["saferpayUnotify"] = "Best�tigung";
$l_shop["saferpayUnotifyTxt"] = "* Best�tigungsmail an Kunden";
$l_shop["saferpayProviderset"] = "Providerset";
$l_shop["saferpayProvidersetTxt"] = "* kommasepariert!";
$l_shop["saferpayCMDPath"] = "exec-path";
$l_shop["saferpayCMDPathTxt"] = "* z. B. /usr/local/bin/";
$l_shop["saferpayconfPath"] = "conf-path";
$l_shop["saferpayconfPathTxt"] = "* Pfad zu saferpay";
$l_shop["saferpaydesc"] = "Beschreibung";
$l_shop["saferpaydescTxt"] = "* z. B. Bestellung";
$GLOBALS['l_shop']["saferpayError"] = "Saferpay ist nicht korrekt installiert. Bitte konfigurieren Sie Ihren Account. saferpay gibt die folgenden Variablen zur�ck:\n<br/>";
$l_shop["NoRevenue"] = "Im ausgew�hlten Zeitraum existieren keine Ums�tze";

                                                 // bis hier neu
$l_shop["FormFieldsTxt"] = "Auswahl der Felder zur �bermittlung an den Payment Provider";
$l_shop["fieldForname"] = "Vorname";
$l_shop["fieldSurname"] = "Nachname";
$l_shop["fieldStreet"] = "Strasse";
$l_shop["fieldZip"] = "PLZ";
$l_shop["fieldCity"] = "Ort";
$l_shop["fieldEmail"] = "E-Mail";
$l_shop["SelectAll"]= "Alle";
$l_shop["plzh"] = "Platzhalter";
$l_shop["bestelldatum"] = "Bestelldatum:";
$l_shop["jsdatum"] = "Bitte geben Sie das Datum ein";
$l_shop["unbearb"] = "unbearbeitet";
$l_shop["schonbezahlt"] = "Davon bezahlt";
$l_shop["unbezahlt"] = "Davon unbezahlt";
$l_shop["monat"] = "Monat";
$l_shop["bestellung"] = "Bestellung";
$l_shop["lastOrder"] = "Letzte Bestellung - Nr.: %s vom %s";
$l_shop["orderNo"] = "Nr.: %s vom %s";
$l_shop["sl"] = "-";
$l_shop["treeYear"] = "Jahr";


$l_shop["bestellungvom"] = "Bestellung vom";
$l_shop["keinedaten"] = "Kein Parameter &uuml;bergeben";
$l_shop["datum"] = "Datum";
$l_shop["anzahl"] = "Bestellungen";
$l_shop["umsatzgesamt"] = "Umsatz gesamt";
$l_shop["Artikel"] = "Artikel";
$l_shop["Anzahl"] = "Anzahl";
$l_shop["variant"] = "Variante";
$l_shop["customField"] = "Eigenes Feld";
$l_shop["customFieldDesc"] = "Eingabe in der Form: <i>name1=wert1;name2=wert2</i>";
$l_shop["Titel"] = "Titel";
$l_shop["Beschreibung"] = "Beschreibung";
$l_shop["Gesamt"] = "Gesamt";
$l_shop["jsanzahl"] = "Bitte geben Sie die Anzahl des Artikels an";

$l_shop["geloscht"] = "Der Datensatz wurde erfolgreich gel&ouml;scht.";
$l_shop["loscht"] = "Datensatz gel&ouml;scht";
$l_shop["orderDoesNotExist"] = "Diese Bestellung existiert nicht mehr.";


$l_shop["selectYear"] = "Jahr ausw�hlen";
$l_shop["selectMonth"] = "Monat ausw�hlen";
$l_shop["jsanz"] = "Bitte geben Sie die Anzahl ein";
$l_shop["keinezahl"] = "Ihre Eingabe ist keine Zahl";
$l_shop["jsbetrag"] = "Bitte geben Sie den Betrag ein";
$l_shop["jsloeschen"] = "Sind Sie sicher, dass Sie diesen Artikel wirklich l�schen wollen? Dieser Vorgang ist nicht umkehrbar.";
$l_shop["Preis"] = "Preis";
$l_shop["MwSt"] = "MwSt.";
$l_shop["gesamtpreis"] = "Gesamtpreis";
$l_shop["plusVat"] = "zuz�glich Mehrwertsteuer";
$l_shop["includedVat"] = "enthaltene Mehrwertsteuer";


$l_shop["bestellnr"] = "Bestellnummer:";
$l_shop["bearbeitet"] = "Bearbeitet am:";
$l_shop["bezahlt"] = "Bezahlt am:";
$l_shop["bestaetigt"] = "Best�tigt am:";
$l_shop["customA"] = "Status A am:";
$l_shop["customB"] = "Status B am:";
$l_shop["customC"] = "Status C am:";
$l_shop["customD"] = "Status D am:";
$l_shop["customE"] = "Status E am:";
$l_shop["customF"] = "Status F am:";
$l_shop["customG"] = "Status G am:";
$l_shop["customH"] = "Status H am:";
$l_shop["customI"] = "Status I am:";
$l_shop["customJ"] = "Status J am:";
$l_shop["storniert"] = "Storniert am:";
$l_shop["beendet"] = "Abgeschlossen am:";
$l_shop["datumeingabe"] = "Sie muessen das Datum im format dd.mm.yy angeben.";

$l_shop["order_data"] = "Bestell- und<br />Kundendaten";
$l_shop["ordered_articles"] = "Bestellte Artikel";
$l_shop["order_comments"] = "Weitere Angaben zu dieser Bestellung";
$l_shop["order_view"] = "Bestellungs�bersicht";


// vats dialogs
$l_shop['vat']['save_success'] = 'Mehrwertsteuersatz erfolgreich gespeichert.';
$l_shop['vat']['save_error'] = 'Konnte Mehrwertsteuersatz nicht speichern.';
$l_shop['vat']['delete_success'] = 'Mehrwertsteuersatz erfolgreich gel�scht.';
$l_shop['vat']['delete_error'] = 'Konnte Mehrwertsteuersatz nicht l�schen.';

$l_shop['vat']['new_vat_name'] = 'Neuer Steuersatz';
$l_shop['vat']['js_confirm_delete'] = 'Soll der ausgew�hlte Mehrwertsteuersatz wirklich gel�scht werden?';

$l_shop['vat']['vat_form_id'] = 'Id';
$l_shop['vat']['vat_form_name'] = 'Name';
$l_shop['vat']['vat_form_vat'] = 'Steuersatz';
$l_shop['vat']['vat_form_standard'] = 'Standard';
$l_shop['vat']['vat_edit_form_headline'] = 'Mehrwertsteuersatz bearbeiten';
$l_shop['vat']['vat_edit_form_headline_box'] = 'Mehrwertsteuers�tze bearbeiten';
$l_shop['vat']['vat_edit_form_yes'] = 'Ja';
$l_shop['vat']['vat_edit_form_no'] = 'Nein';

// vat_country dialog
$l_shop['vat_country']['box_headline'] = 'Regelwerk: Kunden welcher L�nder m�ssen Mehrwertsteuer zahlen';
$l_shop['vat_country']['defaultReturn'] = 'Standardwert';
$l_shop['vat_country']['defaultReturn_desc'] = 'Mit dem Standardwert wird festgelegt, was we:ifShopPayVat zur�ckliefert, wenn keine der folgenden Regeln zutrifft. Wird keine Regel definiert, wird automatisch der Standardwert verwendet.';
$l_shop['vat_country']['stateField'] = 'Feld mit Landangaben';
$l_shop['vat_country']['stateField_desc'] = 'In dieses Feld tragen Sie bitte das Feld in der Kundenverwaltung ein, das das Herkunftsland (Rechnungsadresse) des Kunden enth�lt. Es wird verwendet, um zu ermitteln ob der Kunde Mehrwertsteuern zahlen muss oder nicht.';
$l_shop['vat_country']['statesLiableToVat'] = 'Mehrwertsteuerpflichtige L�nder';
$l_shop['vat_country']['statesLiableToVat_desc'] = 'Bei Kunden aus diesen L�ndern, wird die Mehrwertsteuer ber�cksichtigt.';
$l_shop['vat_country']['statesNotLiableToVat'] = 'Nicht mehrwertsteuerpflichtige L�nder';
$l_shop['vat_country']['statesNotLiableToVat_desc'] = 'Bei Kunden aus diesen L�ndern wird die Mehrwertsteuer nicht ber�cksichtigt.';

$l_shop['vat_country']['statesSpecialRules'] = 'L�nder mit Sonderregeln';
$l_shop['vat_country']['statesSpecialRules_desc'] = 'Kunden aus L�ndern, die hier eingetragen sind, wird die Mehrwertsteuer nur berechnet, wenn zus�tzlich eine bestimmte Bedingung erf�llt ist.';
$l_shop['vat_country']['statesSpecialRules_condition'] = 'zus�tzliche Bedingung';
$l_shop['vat_country']['statesSpecialRules_result'] = 'Ergebnis';

$l_shop['vat_country']['condition_is_empty'] = 'Leer';
$l_shop['vat_country']['condition_is_set'] = 'Gesetzt';

// statusmails
$l_shop['statusmails']['box_headline'] = 'Statusanzeigen und Verhalten beim E-Mail Versand';
$l_shop['statusmails']['AnzeigeDaten'] = 'Anzeige Daten';
$l_shop['statusmails']['fieldname'] = 'Feld-ID';
$l_shop['statusmails']['hidefield'] = 'Feld Anzeige';
$l_shop['statusmails']['hidefieldCOV'] = 'Kunden�bersicht';// TRANSLATE
$l_shop['statusmails']['hidefieldNein'] = 'anzeigen';
$l_shop['statusmails']['hidefieldJa'] = 'nicht anzeigen';
$l_shop['statusmails']['fieldtext'] = 'Feldbezeichnung';
$l_shop['statusmails']['EMailssenden'] = 'E-Mail-Versand';
$l_shop['statusmails']['EMailssendenNein'] = 'keine';
$l_shop['statusmails']['EMailssendenHand'] = 'Button';
$l_shop['statusmails']['EMailssendenAuto'] = 'automatisch';
$l_shop['statusmails']['EMailDaten'] = 'E-Mail Daten';
$l_shop['statusmails']['hintEMailDaten'] ="Definieren Sie hier die Absender E-Mail-Adresse und den Namen des Absenders f�r die Statusmails. <br/> Die E-Mail Adresse des Empf�ngers wird aus dem unten zu w�hlenden Feld aus der Kundenverwaltung entnommen. Der Empf�ngername dann aus dem unten zu w�hlenden Feld f�r den Titel sowie aus Vor- und Nachnahme gebildet.<br/> Definieren Sie au�erdem den Feldnamen im zu versenden Dokument, dessen Inhalt f�r den Betreff des E-Mails verwendet wird. <b>Dieses Feld darf im zu versendenden Dokument nicht leer sein.</b> ";
$l_shop['statusmails']['hintSprache'] ='W�hlen Sie hier, ob Sie verschiedene Dokumente f�r jede Sprache nutzen wollen, und das Feld der Kundenverwaltung, das die Sprache des Nutzers enth�lt.<br/> Der Inhalt dieses Feldes wird dem Status-Mail Dokument in der REQUEST-Variablen $_REQUEST["we_userlanguage"] �bermittelt, sodass Sie dort (z.B. mit &lt;we:ifVar type="request" name="we_userlanguage"&gt;) eine Fallunterscheidung treffen k�nnen.';
$l_shop['statusmails']['hintDokumente'] ='W�hlen Sie hier f�r jeden relevanten Shopstatus eine Dokumenten-ID aus, die das zu versendende Dokument definiert. Die zur Verf�gung stehenden Sprachen werden in den webEdition Einstellungen im Tab Sprachen definiert.<br/>Die Dokumente (besser Vorlagen) bekommen den Shop-Status in der REQUEST-Variablen $_REQUEST["we_shopstatus"] (in der Form "Order", "Confirmation" usw.) �bermittelt, so dass auch in einem Dokument eine Fallunterscheidung m�glich ist.<br/> In den Status-Mail Dokumenten haben Sie Zugriff auf die Daten der Bestellung �ber das Tag &lt;we:order&gt;, welches die REQUEST-Variable $_REQUEST["we_orderid"] auswertet. Siehe auch das Beispiel in der Tag-Referenz.';
$l_shop['statusmails']['AbsenderAdresse'] = 'Absender E-Mail Adresse';
$l_shop['statusmails']['AbsenderName'] = 'Absender Name';
$l_shop['statusmails']['EMailFeld'] = 'KV E-Mail Feld';
$l_shop['statusmails']['DocumentSubjectField'] = 'Feldname im Dokument f�r E-Mail Betreff';
$l_shop['statusmails']['Spracheinstellungen'] = 'Sprachen';
$l_shop['statusmails']['useLanguages'] = 'Nutze verschiedene <br/>Mail-Dokumente je <br/>Sprache (statt den Standard)';
$l_shop['statusmails']['SprachenFeld'] = 'SprachenFeld';
$l_shop['statusmails']['hintISO'] = 'Nur bei Nutzung eines ISO kodierten Sprachfeldes ist eine Sprachzuordnung der Mails m�glich.';
$l_shop['statusmails']['Dokumente'] = 'Dokumente';
$l_shop['statusmails']['defaultDocs'] = 'Standard <br/> (ohne Sp.<br/> Zuordnung)';
$l_shop['statusmails']['EMail'] = 'E-Mail';
$l_shop['statusmails']['TitelFeld'] = 'KV Titel Feld';
$l_shop['statusmails']['bcc'] = 'Blindcopy an Adresse';
$l_shop['statusmails']['resent'] = 'Sind Sie sicher, dass die E-Mail erneut gesendet werden soll?';

$l_shop['shipping']['shipping_package'] = 'Porto und Verpackung';
$l_shop['shipping']['prices_are_net'] = 'Preise sind Nettoangaben';
$l_shop['shipping']['insert_packaging'] = 'Eingetragene Versandkosten';
$l_shop['shipping']['payment_provider'] = 'Payment Provider';
$l_shop['shipping']['revenue_view'] = 'Artikel- / Umsatz�bersicht';
$l_shop['shipping']['name'] = 'Name';
$l_shop['shipping']['countries'] = 'L�nder';
$l_shop['shipping']['costs'] = 'Kosten';
$l_shop['shipping']['order_value'] = 'Bestellwert';
$l_shop['shipping']['shipping_costs'] = 'Versandkosten';


$l_shop['preferences']['customerFields'] = "Kundenfelder<br />(Kundenverwaltung)";
$l_shop['preferences']['orderCustomerFields'] = 'Kundenfelder<br />(Bestellung)';

$l_shop['preferences']['customerdata'] = 'Kundendaten';
$l_shop['preferences']['explanation_customer_odercustomer'] = 'Erkl�rung: Diese Daten werden nur innerhalb dieser Bestellung gespeichert, die Daten der Kundenverwaltung bleiben erhalten.';

$l_shop['order']['edit_order_customer'] = 'Kundendaten innerhalb der Bestellung bearbeiten.';
$l_shop['order']['open_customer'] = 'Kunde in Kundenverwaltung �ffnen.';

$l_shop['edit_order']['shipping_costs'] = 'Versandkosten';
$l_shop['edit_order']['js_edit_custom_cart_field'] = 'Neuer Wert f�r %s:.';
$l_shop['edit_order']['js_edit_cart_field_noFieldname'] = 'Bitte geben Sie einen Feldnamen ein.';
$l_shop['edit_order']['js_saved_cart_field_success'] = 'Warenkorbfeld "%s" gespeichert.';
$l_shop['edit_order']['js_saved_cart_field_error'] = 'Warenkorbfeld "%s" konnte nicht gespeichert werden.';
$l_shop['edit_order']['js_delete_cart_field'] = 'Soll das Feld %s aus der Bestellung gel�scht werden?';
$l_shop['edit_order']['js_delete_cart_field_success'] = 'Das Feld %s wurde aus der Bestellung gel�scht.';
$l_shop['edit_order']['js_delete_cart_field_error'] = 'Das Feld %s konnte nicht aus der Bestellung gel�scht werden.';
$l_shop['edit_order']['js_saved_shipping_success'] = 'Versandkosten gesichert.';
$l_shop['edit_order']['js_saved_shipping_error']   = 'Versandkosten konnten nicht gesichert werden.';
$l_shop['edit_order']['js_saved_customer_success'] = 'Kundendaten erfolgreich gesichert.';
$l_shop['edit_order']['js_saved_customer_error']   = 'Kundendaten konnten nicht gesichert werden.';
$l_shop['edit_order']['js_edit_vat']   = 'Bitte geben die die neue Mehrwertsteuer ein.';


$l_shop['edit_order']['calculate_vat']   = 'Mehrwertsteuer berechnen';
$l_shop['edit_order']['js_saved_calculateVat_success'] = '�nderung gespeichert.';
$l_shop['edit_order']['js_saved_calculateVat_error'] = 'Konnte �nderung nicht speichern.';

$l_shop['orderList']['noOrders'] = 'Dieser Kunde hat noch nichts bestellt.';
$l_shop['orderList']['order'] = 'Bestellung';
$l_shop['orderList']['orderDate'] = 'bestellt am';
$l_shop['orderList']['orderPayed'] = 'bezahlt am';
$l_shop['orderList']['orderEdited'] = 'bearbeitet am';

$l_shop['add_article']['title'] = 'Artikel hinzuf�gen';
$l_shop['add_article']['entry_x_to_y_from_z'] = 'Eintrag %s bis %s von %s';
$l_shop['add_article']['empty_articles'] = 'Es konnten keine Artikel gefunden werden.';

$l_shop['edit_shipping_cost']['title'] = 'Versandkosten �ndern';
$l_shop['edit_shipping_cost']['vatRate'] = 'Mehrwertsteuersatz';
$l_shop['edit_shipping_cost']['isNet'] = 'Netto Angabe';

$l_shop['add_shop_field'] = 'Angabe zu der Bestellung hinzuf�gen';
$l_shop['field_name'] = 'Name';
$l_shop['field_value'] = 'Wert';
$l_shop['field_empty_js_alert'] = 'Sie m�ssen einen Feldnamen angeben.';

$l_shop['edit_article_variants'] = 'Artikel Varianten bearbeiten';
$l_shop['new_entry'] = 'Neuer Eintrag';

$l_paypal['head_title']    = 'Verarbeitung der Zahlung';
$l_paypal['redirect_auto'] = 'Bitte warten Sie, Ihre Zahlung wird verarbeitet und Sie werden zu PayPal weitergeleitet.';
$l_paypal['redirect_man']  = 'Wenn Sie innerhalb von 5 Sekunden nicht automatisch weitergeleitet werden, klicken Sie auf den Button "PayPal"';
?>