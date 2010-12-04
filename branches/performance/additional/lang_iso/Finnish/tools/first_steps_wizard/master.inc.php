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
$lang["Step"]["Startscreen"]["content"] = "Tervetuloa webEditionin aloitusvelhoon. Velho on tarkoitettu webEditionia ensi kertaa k�ytt�ville, jotka haluavat saada sivuston perusulkoasun valmiiksi ilman ett� tarvitsisi opiskella tuntikausia ohjeistuksia. Velho tarjoaa my�s kehittyneille k�ytt�jille mahdollisuuden luoda toimivan web-sivuston vain muutamalla hiiren klikkauksella, jota he voivat muokata my�hemmin enemm�n tarpeisiinsa sopivaksi.<br /><br />Aloitusvelho opastaa seuraavilla sivuilla sinua ensimm�isen ulkoasun asennuksessa sivuillesi. Oikean puolen valikosta l�yd�t vinkkej� ja ohjeita joka askeleelle.<br /><br />Ulkoasun asennuksen j�lkeen voit lis�t� sivuille toiminnallisuuksia laajennoksilla, kuten vieraskirja tai kuvagalleria. Aloitusvelho opastaa my�s n�iden asennuksessa, jonka k�ynnistys tapahtuu Tiedosto -> Uusi -> webEdition sivu -> Muu tiedosto -kohdasta.<br /><br />Kaipaatko webEditionin demosivuja? Demosivu-varmuuskopion voit ladata vapaasti webEdition-sivuilta: <a href=\"http://demo.en.webedition.info/\" target=\"_blank\" class=\"defaultfont\">http://demo.en.webedition.info</a><br />Tuonti tapahtuu valitsemalla Tiedosto -> Varmuuskopioi -> Palauta varmuuskopiosta...";
$lang["Step"]["Startscreen"]["description"] = "<b>Aloitusvelho</b> opastaa sinua webEdition-<b>Web-Sis�ll�nhallintaj�rjestelm�n (WCMS)</b> k�yt�n aloituksessa.<br /><br />webEdition 6 ei asenna oletuksena mallisivuja. T�m�n ansiosta sinulla on t�ysin k�ytt�valmis j�rjestelm� heti k�yt�ss�si.<br /><br />Aloitusvelhossa k�ytett�vi� ulkoasuja luodaan koko ajan lis��, voit tarkistaa uudet k�ynnist�m�ll� Aloitusvelhon. T�m� tapahtuu valitsemalla Tiedosto -> Uusi -> Velhot... -> Aloitusvelho.<br /><br />Voit siirty� edelliselle tai seuraavalle sivulle \\\"takaisin\\\" ja \\\"seuraava\\\" -painikkeilla.";
$lang["Step"]["Startscreen"]["no_connection"] = "Sivupohjapalvelimeen ei saatu yhteytt�."; 
$lang["Step"]["Startscreen"]["error"] = "Virhe"; 


// ChooseDesign
$lang["Step"]["ChooseDesign"]["title"] = "Valitse ulkoasu"; 
$lang["Step"]["ChooseDesign"]["headline"] = "Valitse ulkoasu"; 
$lang["Step"]["ChooseDesign"]["content"] = ""; 
$lang["Step"]["ChooseDesign"]["description"] = "Valitse t��lt� yksi ulkoasuvaihtoehto.<br /><br />N�kyvill� olevia ulkoasuja voi k�ytt�� ja muokata vapaasti ilman maksuja.<br /><br />Voit my�s vaihtaa sivustosi ulkoasua milloin vain toistamalla t�m�n toiminnon.<br /><br />Valitsemalla \\\"esikatselu\\\" saat kuvan suuremmaksi.<br /><br />webEdition-ryhm� tulee julkaisemaan uusia ulkoasuja tulevaisuudessa. Avaamalla Aloitusvelhon voit tarkistaa, onko uusia saatavilla.<br /><br />Asennuksen aikana luodaan p��sivupohja, johon kaikkien webEdition-sivujen ulkoasu perustuu.<br /><br />webEditionilla voit luoda sivuja, joita p��see lukemaan my�s esimerkiksi matkapuhelimilla ja k�mmentietokoneilla.";
$lang["Step"]["ChooseDesign"]["no_import"] = "Et ole valinnut ulkoasua."; 


// DetermineFiles
$lang["Step"]["DetermineFiles"]["title"] = "Ladataan tarvittavat tiedostot."; 
$lang["Step"]["DetermineFiles"]["headline"] = "Ladataan tarvittavat tiedostot."; 
$lang["Step"]["DetermineFiles"]["content"] = "Valitun ulkoasun sivupohjat ladataan palvelimeltamme ja tuodaan webEditioniin. Tiedostoihin kuuluu p��sivupohja, sivupohja tekstisivuille, CSS-tyylit sek� ulkoasuun liittyv�t tiedostot, kuten kuvat. Voit luoda mallisivut my�hemmin.<br /><br />Haetut tiedostot n�ytet��n webEditionin hakemistopuussa onnistuneen tuonnin j�lkeen.<br /><br />Hakemistorakenteen vasemmalta puolelta l�ytyv�t v�lilehdet, joista voi vaihtaa tiedosto- tai sivupohjan�kym��n. Tiedostoilla ja sivupohjilla on omat hakemistopuunsa ja sis�lt�v�t eri tiedostot.<br /><br />Aiemmin haetut ulkoasut korvataan uudemmalla! Jos haluat s�ilytt�� entisen ulkoasun, siirr� niiden tiedostot uuteen hakemistoon.<br /><br />Jos haluat lis�t� muita laajennoksia, k�ynnist� tuonnin j�lkeen ensin Aloitusvelho uudelleen valitsemalla Tiedosto -> Uusi -> webEdition sivu -> Muu tiedosto";
$lang["Step"]["DetermineFiles"]["description"] = "Lataus voi kest�� jonkin aikaa, riippuen tiedostojen koosta, m��r�st� sek� internet-yhteyden nopeudesta.<br /><br />webEdition erottelee tiukasti sis�ll�n ulkoasusta, jonka avulla taataan sivuston yhten�inen ulkoasu.<br /><br />Ulkoasut ladataan palvelimeltamme. Latauksen aikana mit��n henkil�kohtaisia tietoja ei koota tai tallenneta.<br /><br />Muokattavat alueet on merkitty webEditionissa ns. &lt;we:tageilla&gt;. T�ll� hetkell� niit� on n. 200!<br /><br />Voit muokata sivupohjia omalla HTML-editorillasi Editori-laajennoksen avulla.";

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
$lang["Step"]["ImportFiles"]["content"] = "Uuden ulkoasun tiedostot on ladattu palvelimelle ja t�ss� kohtaa tiedostot tuodaan webEditioniin. Tuonnin aikana tiedostot kirjoitetaan tietokantaan ja webEditioniin luodaan hakemistorakenteet.<br /><br />Mukana on my�s yksinkertainen tekstisivu, jonka avulla sis�ll�n luonnissa p��see alkuun. Sis�lt�� voi muokata WYSIWYG-editorin tekstikentist�.<br /><br />Uuden sivun voi luoda valitsemalla Tiedosto -> Uusi -> webEdition sivu -> Tekstisivu. T�ll� sivulla voit luoda tervehdyn vierailijoillesi tai vaikkapa esitell� sivustoasi.<br /><br />Kaksitasoinen navigaatio on my�s mukana, jota p��set muokkaamaan navigaatioty�kalusta: Extrat -> Navigaatio.<br /><br />Ennenkuin mitk��n sis�lt�muutokset n�kyv�t ulosp�in, sinun tulee julkaista sivu!<br /><br />V�lilehtien avulla voit avata webEditioniin useampia tiedostoja tai sivupohjia kerrallaan auki. T�m�n avulla voit vaihtaa nopeasti eri sivujen v�lill� tai tarkistaa sivupohjamuutokset heti varsinaisella sivulla.<br /><br />Tiedostoilla ja sivupohjilla on my�s omat v�lilehtens�, joiden kautta n�ytet��n eri tietoja, kuten milloin sivua on viimeksi muokattu yms.";
$lang["Step"]["ImportFiles"]["description"] = "Oletko tutustunut helppok�ytt�tilaan? T�m�n yksinkertaistetun n�kym�n avulla voit navigoida sivustolla kuten se ulosp�in n�kyy. Helppok�ytt�tilan saa p��lle klikkaamalla seeMode-valinnan p��lle kirjautumisikkunasta.<br /><br />Voit leikata tai skaalata kuvia suoraan webEditionista valitsemalla kuvan auki hakemistopuusta. Uusi Editori-laajennos osaa linkitt�� eri tiedostotyyppej�, kuten .doc ja .jpg, suoraan alkuper�iseen ohjelmaan: Avaa editori, muokkaa tiedostoa ja tallenna - Valmis!<br /><br />Oikean we:tagin l�yt�misess� auttaa jokaisesta sivupohjasta l�ytyv� Tagivelho, jossa on lueteltu kaikki k�ytett�v�t tagit lyhyell� kuvauksella.";

// Finish
$lang["Step"]["Finish"]["title"] = "Ulkoasu on luotu"; 
$lang["Step"]["Finish"]["headline"] = "Ulkoasu on luotu..."; 
$lang["Step"]["Finish"]["content"] = "Uusi ulkoasu on nyt tuotu onnistuneesti!<br />Ennen muita muokkauksia kannattaa viel� suorittaa kaikkien tiedostojen t�ydellinen uudelleenrakennus valitsemalla Tiedosto -> Rakenna uudelleen.";
$lang["Step"]["Finish"]["description"] = "Sivupalkissa voidaan n�ytt�� mik� tahansa webEdition-sivu: Esimerki Online-ohje tai yleiskuvaus kaikista verkkokaupan myyt�vist� kohteista.<br /><br />webEditionin Ohjausn�kym��n voi lis�t� uusia vimpaimia valitsemalla Aloitus -> Uusi vimpain.<br /><br />Sivuston varmuuskopiointi on tehty helpoksi: Tiedosto -> Varmuuskopioi -> Luo varmuuskopio...<br /><br />Mit� on uudelleenrakennus? webEdition luo sivuja sivupohjien perusteella. Jos muutat staattisen sivun sivupohjaa, tiedosto t�ytyy rakentaa uudelleen ennenkuin muutokset n�kyv�t ulosp�in.";
$lang["Step"]["Finish"]["content_2"] = "Jos olet tuonut uuden ulkoasun, sinun t�ytyy suorittaa sivuston uudelleenrakennus! Sivupalkissa voit n�ytt�� muita mahdollisuuksia uudelle sivustollesi. Voit navigoida suoraan uusiin tiedostoihin tai lis�laajennoksiin!<br /><br />Pid� hauskaa webEditionin parissa!";

?>