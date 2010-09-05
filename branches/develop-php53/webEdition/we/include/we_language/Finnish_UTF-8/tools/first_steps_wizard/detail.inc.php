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


//
// ---> Template
//

$lang["Template"]["headline"] = "Aloitusvelho";
$lang["Template"]["title"] = "Aloitusvelho";
$lang["Template"]["autocontinue"] = "Uudelleenohjataan % sekunnin kuluessa.";


//
// ---> Buttons
//

$lang["Buttons"]["next"] = "seuraava";
$lang["Buttons"]["back"] = "takaisin";


//
// ---> Wizards
//

$lang["Wizard"]["DetailWizard"]["title"] = ""; 


//
// ---> Steps
//

// Startscreen
$lang["Step"]["Startscreen"]["title"] = "Tervetuloa"; 
$lang["Step"]["Startscreen"]["headline"] = "Tervetuloa"; 
$lang["Step"]["Startscreen"]["content"] = "Tervetuloa laajennosten Aloitusvelhoon.<br /><br />Laajennosten tuonti tapahtuu linjassa uuden ulkoasun luonnin kanssa Aloitusvelhon avulla.<br /><br />Tuonti sisältää useampia tiedostoja: Sivupohjia, tiedostoja, esimerkkejä tai kuvia.<br /><br />Sivuiltamme (<a href=\"http://www.webedition.de/en/\" target=\"_blank\" class=\"defaultfont\">http://www.webedition.de/en</a>) löydät mitä kaikkia laajennoksia on saatavilla.<br /><br />Sivuiltamme löydät myös esimerkkisivut: a href=\"http://demo.en.webedition.info/\" target=\"_blank\" class=\"defaultfont\">http://demo.en.webedition.info</a><br />Esimerkkisivujen tuonti onnistuu Varmuuskopion palautus -toiminnolla: Tiedosto -> Varmuuskopio -> Palauta varmuuskopio...";
$lang["Step"]["Startscreen"]["description"] = "webDition 5 -versiosta lähtien vientimoduuli tulee perusversion mukana. Sen avulla voit viedä toimivia kokonaisuuksia, kuten esim. vieraskirjan, ja tarjota sitä käytettäväksi muille.<br /><br />Paljon muitakin moduuleja on integroitu webEditioniin: Äänestysmoduuli, Banneri/Statistiikkamoduuli, Käyttäjänhallinta jne...<br /><br />Voit palata takaisin edelliselle sivulle tai siirtyä seuraavalla valitsemalla \\\"takaisin\\\" ja \\\"seuraava\\\".";
$lang["Step"]["Startscreen"]["no_connection"] = "Sivupohjapalvelimeen ei saatu yhteyttä."; 
$lang["Step"]["Startscreen"]["error"] = "Virhe"; 


// ChooseDesign
$lang["Step"]["ChooseDesign"]["title"] = "Valitse laajennos"; 
$lang["Step"]["ChooseDesign"]["headline"] = "Valitse laajennos"; 
$lang["Step"]["ChooseDesign"]["content"] = ""; 
$lang["Step"]["ChooseDesign"]["description"] = "Valitse täältä yksi laajennosvaihtoehto.<br /><br />Näkyvillä olevia laajennoksia voi käyttää ja muokata vapaasti ilman maksuja.<br /><br />Toistamalla tämän toiminnon voit myös lisätä uusia laajennoksia milloin vain.<br /><br />Valitsemalla \\\"esikatselu\\\" saat kuvan suuremmaksi.<br /><br />webEdition-ryhmä tulee julkaisemaan uusia laajennoksia tulevaisuudessa. Avaamalla Aloitusvelhon voit tarkistaa, onko uusia saatavilla.<br /><br />Asennuksen aikana luodaan sivupohjia ja tiedostoja, joihon kaikkien laajennosten toiminta perustuu.<br /><br />webEditionilla voit luoda sivuja, joita pääsee lukemaan myös esimerkiksi matkapuhelimilla ja kämmentietokoneilla.";
$lang["Step"]["ChooseDesign"]["no_import"] = "Et ole valinnut laajennosta."; 


// DetermineFiles
$lang["Step"]["DetermineFiles"]["title"] = "Ladataan tarvittavat tiedostot."; 
$lang["Step"]["DetermineFiles"]["headline"] = "Ladataan tarvittavat tiedostot."; 
$lang["Step"]["DetermineFiles"]["content"] = "Valitun laajennoksen tiedostot ladataan palvelimeltamme ja tuodaan webEditioniin. Tiedostoihin kuuluu sivupohjia, CSS-tyylejä sekä ulkoasuun liittyvät tiedostoja, kuten kuvia. Voit luoda mallisivuja myöhemmin.<br /><br />Haetut tiedostot näytetään webEditionin hakemistopuussa onnistuneen tuonnin jälkeen.<br /><br />Hakemistorakenteen vasemmalta puolelta löytyvät välilehdet, joista voit vaihtaa tiedosto- tai sivupohjanäkymään. Tiedostoilla ja sivupohjilla on omat hakemistopuunsa ja sisältävät eri tiedostot.<br /><br />Aiemmin haetut ulkoasut korvataan uudemmalla! Jos haluat säilyttää entisen ulkoasun, siirrä niiden tiedostot uuteen hakemistoon.<br /><br />Jos haluat lisätä muita laajennoksia, käynnistä tuonnin jälkeen ensin Aloitusvelho uudelleen valitsemalla Tiedosto -> Uusi -> webEdition sivu -> Muu tiedosto";
$lang["Step"]["DetermineFiles"]["description"] = "Lataus voi kestää jonkin aikaa, riippuen tiedostojen koosta, määrästä sekä internet-yhteyden nopeudesta.<br /><br />webEdition erottelee tiukasti sisällön ulkoasusta, jonka avulla taataan sivuston yhtenäinen ulkoasu.<br /><br />Ulkoasut ladataan palvelimeltamme. Latauksen aikana mitään henkilökohtaisia tietoja ei koota tai tallenneta.<br /><br />Muokattavat alueet on merkitty webEditionissa ns. &lt;we:tageilla&gt;. Tällä hetkellä niitä on n. 200!<br /><br />Voit muokata sivupohjia omalla HTML-editorillasi Editori-laajennoksen avulla.";

// DownloadFiles
$lang["Step"]["DownloadFiles"]["title"] = $lang["Step"]["DetermineFiles"]["title"]; 
$lang["Step"]["DownloadFiles"]["headline"] = $lang["Step"]["DetermineFiles"]["headline"]; 
$lang["Step"]["DownloadFiles"]["content"] = $lang["Step"]["DetermineFiles"]["content"]; 
$lang["Step"]["DownloadFiles"]["description"] = $lang["Step"]["DetermineFiles"]["description"]; 

// PostDownloadFiles
$lang["Step"]["PostDownloadFiles"]["title"] = $lang["Step"]["DetermineFiles"]["title"]; 
$lang["Step"]["PostDownloadFiles"]["headline"] = $lang["Step"]["DetermineFiles"]["headline"]; 
$lang["Step"]["PostDownloadFiles"]["content"] = $lang["Step"]["DetermineFiles"]["content"]; 
$lang["Step"]["PostDownloadFiles"]["description"] = $lang["Step"]["DetermineFiles"]["description"]; 

// ImportOptions
$lang["Step"]["ImportOptions"]["title"] = "Asetukset"; 
$lang["Step"]["ImportOptions"]["headline"] = "Asetukset"; 
$lang["Step"]["ImportOptions"]["content"] = "Tässä voit valita mitä pääsivupohjaa käytetään tuontiin. Jos olet jo aiemmin hakenut ulkoasun Aloitusvelhon avulla, oikea sivupohja pitäisi olla valittuna automaattisesti. Voit tietysti käyttää myös omia sivupohjia.<br /><br />Valitse \"Luo tiedostoja\", jos haluat luoda valitsemaasi hakemistoon uuden laajennoksen webEdition-sivuja jo valmiiksi.<br /><br />Valitse \"Lisää navigaatiolinkit\", jos haluat päästä uusiin dokumentteihin heti navigaation kautta.";
$lang["Step"]["ImportOptions"]["description"] = "<b>Pääsivupohja</b> on pääpohja kaikille webEdition-sivuille. Siellä voit määritellä pääelementit, jotka säilyvät samoina kaikilla sivuilla, kuten logo, banneri, navigaatio jne. Näin varmistat parhaiten sivuston yhtenäisyyden.<br /><br />Uuden sivupohjan luonnissa voit määritellä sille Pääsivupohjan, johon uusi perustuu. Tämä nopeuttaa huomattavasti perusrakenteen luontia.<br /><br />Voit muokata navigaatiota navigaatiotyökalun avulla: Extrat -> Navigaatio...<br /><br />Jos et valinnut \"Luo tiedostoja\" -vaihtoehtoa, vain sivupohjat tuodaan. Voit lisätä tiedostoja myöhemmin ja valita oikean sivupohjan Ominaisuudet-välilehden kautta.";
$lang["Step"]["ImportOptions"]["choose_mastertemplate"] = "Mihin Pääsivupohjaan sivupohjat tulevat perustumaan:"; 
$lang["Step"]["ImportOptions"]["labelUseDocuments"] = "Luo tiedostoja"; 
$lang["Step"]["ImportOptions"]["choose_document_path"] = "Valitse hakemisto johon tiedostot luodaan:"; 
$lang["Step"]["ImportOptions"]["labelUseNavigation"] = "Lisää navigaatiolinkit"; 
$lang["Step"]["ImportOptions"]["choose_navigation_path"] = "Valitse hakemisto johon navigaatiolinkit luodaan:"; 

// ImportFiles
$lang["Step"]["ImportFiles"]["title"] = "Valmistele tarvittavat tiedostot"; 
$lang["Step"]["ImportFiles"]["headline"] = "Valmistele tarvittavat tiedostot"; 
$lang["Step"]["ImportFiles"]["content"] = "Uusien laajennosten tiedostot on ladattu palvelimelle ja tässä kohtaa tiedostot tuodaan webEditioniin. Tuonnin aikana tiedostot kirjoitetaan tietokantaan ja webEditioniin luodaan hakemistorakenteet.<br /><br />Tuonti voi kestää jonkin aikaa, riippuen tiedostomäärästä. Alla näkyy toiminnon eteneminen.<br /><br />Mikäli valitsit tämän vaihtoehdon aiemmassa vaiheessa, uudet sivut lisätään automaattisesti navigaatioon ja niihin pääsee suoraan sivustolta.<br /><br />Ennenkuin mitään muutoksia näkyy sivustolla, sinun tulee julkaista tiedostot!<br /><br />Välilehtien avulla voit avata webEditioniin useampia tiedostoja tai sivupohjia kerrallaan auki. Tämän avulla voit vaihtaa nopeasti eri sivujen välillä tai tarkistaa sivupohjamuutokset heti varsinaisella sivulla.<br /><br />Tiedostoilla ja sivupohjilla on myös omat välilehtensä, joiden kautta näytetään eri tietoja, kuten milloin sivua on viimeksi muokattu yms.";
$lang["Step"]["ImportFiles"]["description"] = "Oletko tutustunut helppokäyttötilaan? Tämän yksinkertaistetun näkymän avulla voit navigoida sivustolla kuten se ulospäin näkyy. Helppokäyttötilan saa päälle klikkaamalla seeMode-valinnan päälle kirjautumisikkunasta.<br /><br />Voit leikata tai skaalata kuvia suoraan webEditionista valitsemalla kuvan auki hakemistopuusta. Uusi Editori-laajennos osaa linkittää eri tiedostotyyppejä, kuten .doc ja .jpg, suoraan alkuperäiseen ohjelmaan: Avaa editori, muokkaa tiedostoa ja tallenna - Valmis!<br /><br />Oikean we:tagin löytämisessä auttaa jokaisesta sivupohjasta löytyvä Tagivelho, jossa on lueteltu kaikki käytettävät tagit lyhyellä kuvauksella.";

// Finish
$lang["Step"]["Finish"]["title"] = "Ulkoasu on luotu"; 
$lang["Step"]["Finish"]["headline"] = "Ulkoasu on luotu..."; 
$lang["Step"]["Finish"]["content"] = "Uusi ulkosu on nyt tuotu onnistuneesti!<br />Ennen muita muokkauksia kannattaa vielä suorittaa kaikkien tiedostojen täydellinen uudelleenrakennus valitsemalla Tiedosto -> Rakenna uudelleen.";
$lang["Step"]["Finish"]["description"] = "Sivupalkissa voidaan näyttää mikä tahansa webEdition-sivu: Esimerki Online-ohje tai yleiskuvaus kaikista verkkokaupan myytävistä kohteista.<br /><br />webEditionin Ohjausnäkymään voi lisätä uusia vimpaimia valitsemalla Aloitus -> Uusi vimpain.<br /><br />Sivuston varmuuskopiointi on tehty helpoksi: Tiedosto -> Varmuuskopioi -> Luo varmuuskopio...<br /><br />Mitä on uudelleenrakennus? webEdition luo sivuja sivupohjien perusteella. Jos muutat staattisen sivun sivupohjaa, tiedosto täytyy rakentaa uudelleen ennenkuin muutokset näkyvät ulospäin.";
$lang["Step"]["Finish"]["content_2"] = "Sivupalkissa voit näyttää muita mahdollisuuksia uudelle sivustollesi. Voit navigoida suoraan uusiin tiedostoihin tai lisälaajennoksiin!<br /><br />Pidä hauskaa webEditionin parissa!";
?>