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
//

$lang["Wizard"]["MasterWizard"]["title"] = ""; 


//
// ---> Steps
//

// Startscreen
$lang["Step"]["Startscreen"]["title"] = "Tervetuloa"; 
$lang["Step"]["Startscreen"]["headline"] = "Tervetuloa"; 
$lang["Step"]["Startscreen"]["content"] = "Tervetuloa webEditionin aloitusvelhoon. Velho on tarkoitettu webEditionia ensi kertaa käyttäville, jotka haluavat saada sivuston perusulkoasun valmiiksi ilman että tarvitsisi opiskella tuntikausia ohjeistuksia. Velho tarjoaa myös kehittyneille käyttäjille mahdollisuuden luoda toimivan web-sivuston vain muutamalla hiiren klikkauksella, jota he voivat muokata myöhemmin enemmän tarpeisiinsa sopivaksi.<br /><br />Aloitusvelho opastaa seuraavilla sivuilla sinua ensimmäisen ulkoasun asennuksessa sivuillesi. Oikean puolen valikosta löydät vinkkejä ja ohjeita joka askeleelle.<br /><br />Ulkoasun asennuksen jälkeen voit lisätä sivuille toiminnallisuuksia laajennoksilla, kuten vieraskirja tai kuvagalleria. Aloitusvelho opastaa myös näiden asennuksessa, jonka käynnistys tapahtuu Tiedosto -> Uusi -> webEdition sivu -> Muu tiedosto -kohdasta.<br /><br />Kaipaatko webEditionin demosivuja? Demosivu-varmuuskopion voit ladata vapaasti webEdition-sivuilta: <a href=\"http://demo.en.webedition.info/\" target=\"_blank\" class=\"defaultfont\">http://demo.en.webedition.info</a><br />Tuonti tapahtuu valitsemalla Tiedosto -> Varmuuskopioi -> Palauta varmuuskopiosta...";
$lang["Step"]["Startscreen"]["description"] = "<b>Aloitusvelho</b> opastaa sinua webEdition-<b>Web-Sisällönhallintajärjestelmän (WCMS)</b> käytön aloituksessa.<br /><br />webEdition 6 ei asenna oletuksena mallisivuja. Tämän ansiosta sinulla on täysin käyttövalmis järjestelmä heti käytössäsi.<br /><br />Aloitusvelhossa käytettäviä ulkoasuja luodaan koko ajan lisää, voit tarkistaa uudet käynnistämällä Aloitusvelhon. Tämä tapahtuu valitsemalla Tiedosto -> Uusi -> Velhot... -> Aloitusvelho.<br /><br />Voit siirtyä edelliselle tai seuraavalle sivulle \\\"takaisin\\\" ja \\\"seuraava\\\" -painikkeilla.";
$lang["Step"]["Startscreen"]["no_connection"] = "Sivupohjapalvelimeen ei saatu yhteyttä."; 
$lang["Step"]["Startscreen"]["error"] = "Virhe"; 


// ChooseDesign
$lang["Step"]["ChooseDesign"]["title"] = "Valitse ulkoasu"; 
$lang["Step"]["ChooseDesign"]["headline"] = "Valitse ulkoasu"; 
$lang["Step"]["ChooseDesign"]["content"] = ""; 
$lang["Step"]["ChooseDesign"]["description"] = "Valitse täältä yksi ulkoasuvaihtoehto.<br /><br />Näkyvillä olevia ulkoasuja voi käyttää ja muokata vapaasti ilman maksuja.<br /><br />Voit myös vaihtaa sivustosi ulkoasua milloin vain toistamalla tämän toiminnon.<br /><br />Valitsemalla \\\"esikatselu\\\" saat kuvan suuremmaksi.<br /><br />webEdition-ryhmä tulee julkaisemaan uusia ulkoasuja tulevaisuudessa. Avaamalla Aloitusvelhon voit tarkistaa, onko uusia saatavilla.<br /><br />Asennuksen aikana luodaan pääsivupohja, johon kaikkien webEdition-sivujen ulkoasu perustuu.<br /><br />webEditionilla voit luoda sivuja, joita pääsee lukemaan myös esimerkiksi matkapuhelimilla ja kämmentietokoneilla.";
$lang["Step"]["ChooseDesign"]["no_import"] = "Et ole valinnut ulkoasua."; 


// DetermineFiles
$lang["Step"]["DetermineFiles"]["title"] = "Ladataan tarvittavat tiedostot."; 
$lang["Step"]["DetermineFiles"]["headline"] = "Ladataan tarvittavat tiedostot."; 
$lang["Step"]["DetermineFiles"]["content"] = "Valitun ulkoasun sivupohjat ladataan palvelimeltamme ja tuodaan webEditioniin. Tiedostoihin kuuluu pääsivupohja, sivupohja tekstisivuille, CSS-tyylit sekä ulkoasuun liittyvät tiedostot, kuten kuvat. Voit luoda mallisivut myöhemmin.<br /><br />Haetut tiedostot näytetään webEditionin hakemistopuussa onnistuneen tuonnin jälkeen.<br /><br />Hakemistorakenteen vasemmalta puolelta löytyvät välilehdet, joista voi vaihtaa tiedosto- tai sivupohjanäkymään. Tiedostoilla ja sivupohjilla on omat hakemistopuunsa ja sisältävät eri tiedostot.<br /><br />Aiemmin haetut ulkoasut korvataan uudemmalla! Jos haluat säilyttää entisen ulkoasun, siirrä niiden tiedostot uuteen hakemistoon.<br /><br />Jos haluat lisätä muita laajennoksia, käynnistä tuonnin jälkeen ensin Aloitusvelho uudelleen valitsemalla Tiedosto -> Uusi -> webEdition sivu -> Muu tiedosto";
$lang["Step"]["DetermineFiles"]["description"] = "Lataus voi kestää jonkin aikaa, riippuen tiedostojen koosta, määrästä sekä internet-yhteyden nopeudesta.<br /><br />webEdition erottelee tiukasti sisällön ulkoasusta, jonka avulla taataan sivuston yhtenäinen ulkoasu.<br /><br />Ulkoasut ladataan palvelimeltamme. Latauksen aikana mitään henkilökohtaisia tietoja ei koota tai tallenneta.<br /><br />Muokattavat alueet on merkitty webEditionissa ns. &lt;we:tageilla&gt;. Tällä hetkellä niitä on n. 200!<br /><br />Voit muokata sivupohjia omalla HTML-editorillasi Editori-laajennoksen avulla.";

// DownloadFiles
$lang["Step"]["DownloadFiles"]["title"] = $lang["Step"]["DetermineFiles"]["title"]; 
$lang["Step"]["DownloadFiles"]["headline"] = $lang["Step"]["DetermineFiles"]["headline"]; 
$lang["Step"]["DownloadFiles"]["content"] = $lang["Step"]["DetermineFiles"]["content"]; 
$lang["Step"]["DownloadFiles"]["description"] = $lang["Step"]["DetermineFiles"]["description"]; 

// ImportOptions
$lang["Step"]["ImportOptions"]["title"] = $lang["Step"]["DetermineFiles"]["title"]; 
$lang["Step"]["ImportOptions"]["headline"] = $lang["Step"]["DetermineFiles"]["headline"]; 
$lang["Step"]["ImportOptions"]["content"] = $lang["Step"]["DetermineFiles"]["content"]; 
$lang["Step"]["ImportOptions"]["description"] = $lang["Step"]["DetermineFiles"]["description"]; 

// ImportFiles
$lang["Step"]["ImportFiles"]["title"] = "Valmistellaan tarvittavia tiedostoja"; 
$lang["Step"]["ImportFiles"]["headline"] = "Valmistellaan tarvittavia tiedostoja"; 
$lang["Step"]["ImportFiles"]["content"] = "Uuden ulkoasun tiedostot on ladattu palvelimelle ja tässä kohtaa tiedostot tuodaan webEditioniin. Tuonnin aikana tiedostot kirjoitetaan tietokantaan ja webEditioniin luodaan hakemistorakenteet.<br /><br />Mukana on myös yksinkertainen tekstisivu, jonka avulla sisällön luonnissa pääsee alkuun. Sisältöä voi muokata WYSIWYG-editorin tekstikentistä.<br /><br />Uuden sivun voi luoda valitsemalla Tiedosto -> Uusi -> webEdition sivu -> Tekstisivu. Tällä sivulla voit luoda tervehdyn vierailijoillesi tai vaikkapa esitellä sivustoasi.<br /><br />Kaksitasoinen navigaatio on myös mukana, jota pääset muokkaamaan navigaatiotyökalusta: Extrat -> Navigaatio.<br /><br />Ennenkuin mitkään sisältömuutokset näkyvät ulospäin, sinun tulee julkaista sivu!<br /><br />Välilehtien avulla voit avata webEditioniin useampia tiedostoja tai sivupohjia kerrallaan auki. Tämän avulla voit vaihtaa nopeasti eri sivujen välillä tai tarkistaa sivupohjamuutokset heti varsinaisella sivulla.<br /><br />Tiedostoilla ja sivupohjilla on myös omat välilehtensä, joiden kautta näytetään eri tietoja, kuten milloin sivua on viimeksi muokattu yms.";
$lang["Step"]["ImportFiles"]["description"] = "Oletko tutustunut helppokäyttötilaan? Tämän yksinkertaistetun näkymän avulla voit navigoida sivustolla kuten se ulospäin näkyy. Helppokäyttötilan saa päälle klikkaamalla seeMode-valinnan päälle kirjautumisikkunasta.<br /><br />Voit leikata tai skaalata kuvia suoraan webEditionista valitsemalla kuvan auki hakemistopuusta. Uusi Editori-laajennos osaa linkittää eri tiedostotyyppejä, kuten .doc ja .jpg, suoraan alkuperäiseen ohjelmaan: Avaa editori, muokkaa tiedostoa ja tallenna - Valmis!<br /><br />Oikean we:tagin löytämisessä auttaa jokaisesta sivupohjasta löytyvä Tagivelho, jossa on lueteltu kaikki käytettävät tagit lyhyellä kuvauksella.";

// Finish
$lang["Step"]["Finish"]["title"] = "Ulkoasu on luotu"; 
$lang["Step"]["Finish"]["headline"] = "Ulkoasu on luotu..."; 
$lang["Step"]["Finish"]["content"] = "Uusi ulkoasu on nyt tuotu onnistuneesti!<br />Ennen muita muokkauksia kannattaa vielä suorittaa kaikkien tiedostojen täydellinen uudelleenrakennus valitsemalla Tiedosto -> Rakenna uudelleen.";
$lang["Step"]["Finish"]["description"] = "Sivupalkissa voidaan näyttää mikä tahansa webEdition-sivu: Esimerki Online-ohje tai yleiskuvaus kaikista verkkokaupan myytävistä kohteista.<br /><br />webEditionin Ohjausnäkymään voi lisätä uusia vimpaimia valitsemalla Aloitus -> Uusi vimpain.<br /><br />Sivuston varmuuskopiointi on tehty helpoksi: Tiedosto -> Varmuuskopioi -> Luo varmuuskopio...<br /><br />Mitä on uudelleenrakennus? webEdition luo sivuja sivupohjien perusteella. Jos muutat staattisen sivun sivupohjaa, tiedosto täytyy rakentaa uudelleen ennenkuin muutokset näkyvät ulospäin.";
$lang["Step"]["Finish"]["content_2"] = "Jos olet tuonut uuden ulkoasun, sinun täytyy suorittaa sivuston uudelleenrakennus! Sivupalkissa voit näyttää muita mahdollisuuksia uudelle sivustollesi. Voit navigoida suoraan uusiin tiedostoihin tai lisälaajennoksiin!<br /><br />Pidä hauskaa webEditionin parissa!";

?>