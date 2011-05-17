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

$l_we_tag['a']['description'] = "we:a tagi luon HTML-linkki tagin joka viittaa sisäiseen, ID:llä määriteltävään webEdition dokumenttiin. Kaikki aloitus- ja lopetustagin väliin tuleva sisältö toimii linkkinä.".
$l_we_tag['a']['defaultvalue'] = "";
$l_we_tag['addDelNewsletterEmail']['description'] = "Tätä tagia käytetään öisäämään tai poistamaan sähköpostiosoite uutiskirjeen tilaajalistalta. Attribuutissa \"path\" täytyy antaa täydellinen polku uutiskirjeen vastaanottajalistatiedostoon. Jos path alkaa ilman merkkiä \"/\", lisätään annettu merkkijono DOCUMENT_ROOT arvoon. Jos käytössä on useita listoja, voit antaa pathiin useita polkuja pilkkueroteltuna.";
$l_we_tag['addDelShopItem']['description'] = "Käytä we:addDelShopItem tagia lisätäksesi tai poistaaksesi tavaraa ostoskorista.";
$l_we_tag['addPercent']['description'] = "Tagi we:addPercent lisää arvoa määritellyn prosenttimäärän verran, esim. ALV:n verran.";
$l_we_tag['addPercent']['defaultvalue'] = "";
$l_we_tag['answers']['description'] = "Tagi näyttää äänestyksen vastausvaihtoehdot.";
$l_we_tag['answers']['defaultvalue'] = "";
$l_we_tag['author']['description'] = "Tagi we:author näyttää dokumentin luojan nimen. Jos attribuuttia 'type' ei ole määritelty, näytetään käyttäjätunnus. Jos type=\"name\", näytetään käyttäjän etu- ja sukunimi. Jos nimiä ei ole määritelty, näytetään edelleen vain käyttäjätunnus.";
$l_we_tag['back']['description'] = "Tagi we:back tagi luo HTML-linkin joka viittaa we:listviewin edelliselle sivulle. Kaikki aloitus- ja lopetustagin väliin tuleva sisältö toimii linkkinä.";
$l_we_tag['back']['defaultvalue'] = "";
$l_we_tag['banner']['description'] = "Käytä we:banner tagia sisällyttääksesi bannerin Banneri Moduulista.";
$l_we_tag['bannerSelect']['description'] = "Täm tagi näyttää alasvatovalikon (&lt;select&gt;), jolla valita bannereita. Jos Asiakashallintamoduuli on asennettu ja attribuutti \"customer\" on asetettu, bannerit näytetään vain kirjautuneille käyttäjille.";
$l_we_tag['bannerSum']['description'] = "Tagi we:bannerSum näyttää kaikkien bannerinäyttöjen tai klikkausten summan. Tagi toimii vain listview type=\"banner\" sisällä.";
$l_we_tag['block']['description'] = "Tagi we:block tagi mahdollistaa laajennettavien blockien/listojen luonnin. Kaikki aloitus- ja lopetustagien väliin tuleva sisältö (HTML-koodi, lähes kaikki we:tagit) lisätään sivulle plus-painikkeen painallukselle sivun muokkaustilassa.";
$l_we_tag['block']['defaultvalue'] = "";
$l_we_tag['calculate']['description'] = "we:calculate tagi mahdollistaa kaikkien PHP:n tarjoaminen matemaattisten operaatioiden käytön, esim. *, /, +, -,(), sqrt..jne.";
$l_we_tag['calculate']['defaultvalue'] = "";
$l_we_tag['captcha']['description'] = "Tag generoi kuvan jossa on satunnainen koodi.";
$l_we_tag['category']['description'] = "we:category tagissa määritellyt kategoriat korvataan kategorialla tai kategorioilla jotka määritellään dokumentin Ominaisuudet- välilehdellä. Jos tagia käytettäessä halutaan määritellä useita kategorioita, ne täytyy erotella pilkulla. Jos halutaan käyttää muuta erotinta, täytyy käytettävä erotin määritellä attribuutilla  \"tokken\.";
$l_we_tag['categorySelect']['description'] = "Tätä tagia käyttämällä voidaan lisätä alasvetovalikko (&lt;select&gt;) webEdition dokumenttiin. Määrittämällä lopetustagi heti aloitustagin jälkeen saadaan valikko näyttämään kaikki webEditionin kategoriat.";
$l_we_tag['categorySelect']['defaultvalue'] = "";
$l_we_tag['charset']['description'] = "we:charset tagi luo HTML-metatagin joka määrittää sivulla käytettävän merkistökoodauksen. \"ISO-8859-1\" on yleensä käytössä englannikielisillä sivuilla. Tämä tagi on sijoitettavfa HTML-sivun head-osioon.";
$l_we_tag['charset']['defaultvalue'] = "";
$l_we_tag['checkForm']['description'] = "we:checkForm tagi luo JavaScript koodin jolla voi tarkistaa määritellyn lomakkeen syötteet.<br/>Parametrien 'match' ja 'type' avulla määritellään tarkistettavan lomakkeen 'name' tai 'id'.<br/>'mandatory' sisältää pilkkuerotellun listan pakollisten kenttien nimistä ja 'email' sisältää samaan malliin koostetun listan kentistä joiden aiotut syöttet ovat tyypeiltään sähköpostiosoitteita. <br>Kentään 'password' on mahdollista kirjoittaa 2 kenttänimeä joihin sovelletaan salasanatarkastusta, sekä kolmantena arvona numeerinen arvo joka määrittää salasanan minimipituuden (esim: salasana,salasana2,5). <br>'onError' kohtaan voit määrittää virhetilanteessa mahdollisesti kutsuttavan itse määrittelemäsi JavaScript -funktion nimen. Tämä funktio saa parametrina taulukon josta löytyvät puuttuvien pakollisten kenttien nimet, ja 'flagin' siitä oliko salasanat oikein. Jos 'onError' jätetään määrittelemättä tai funktiota ei ole lisätty sivupohjaan, näytetään oletusarvot alert-ikkunassa.";
$l_we_tag['checkForm']['defaultvalue'] = "";
$l_we_tag['colorChooser']['description'] = "we:colorChooser tagi luo kontrollin jolla voidaan helposti valita väriarvo.";
$l_we_tag['comment']['description'] = 'The comment Tag is used to generate explicit comments in the specified language, or, to add comments to the template which are not delivered to the user browser.';//TRANSLATE
$l_we_tag['condition']['description'] = "Tätä tagia käytetään yhdessä tagin &lt;we:conditionAdd&gt; kanssa kun halutaan dynaamisesti lisätä arvoja &lt;we:listview type=\"object\"&gt; attribuuttiin \"condition\". Ehdot voivat olla limittäisiä.";
$l_we_tag['condition']['defaultvalue'] = "&lt;we:conditionAdd field=\"Type\" var=\"type\" compare=\"=\"/&gt;";
$l_we_tag['conditionAdd']['description'] = "Tagia käytetään uuden ehdon tai säännön lisäämiseen &lt;we:condition&gt; tagien sisällä.";
$l_we_tag['conditionAnd']['description'] = "Tagia käytetään ehtojen lisäämiseen &lt;we:condition&gt; tagien sisällä. Tämä on looginen operaattori AND, tarkoittaen sitä että molempien liitettyjen ehtojen tulee täyttyä.";
$l_we_tag['conditionOr']['description'] = "Tagia käytetään ehtojen lisäämiseen &lt;we:condition&gt; tagien sisällä. Tämä on looginen operaattori OR, tarkoittaen että jomman kumman liitetyistä ehdoista tulee täyttyä.";
$l_we_tag['content']['description'] = "&lt;we:content /&gt; käytetään vain pääsivupohjan sisällä (mastertemplate). Se määrittelee paikan jonne pääsivupohjaa käyttävän muun sivupohjan sisältö liitetään.";
$l_we_tag['controlElement']['description'] = "Tagia we:controlElement käytetään dokumentin muokkaustilassa kontrollielementtien save, delete, publish jne. hallintaan. Painikkeita voidaan piilottaa, checkboxeja disabloida/rastittaa/piilottaa.";
$l_we_tag['cookie']['description'] = "Tämä tagi on äänestysmoduulin vaatima ja se luo asiakaskoneelle evästeen joka estää useammat äänestyskerrat. Tagi täytyy sijoittaa aivan sivupohjan alkuun (ts. mitään ei saa tulostaa ennen tätä tagia, ei edes välilyöntejä tai rivinvaihtoja).";
$l_we_tag['createShop']['description'] = "Tagia we:createShop tarvitaan kaikilla sivuilla joilla on tarkoitus tulostaa tietoja ostoksista.";
$l_we_tag['css']['description'] = "Css tagi luo HTML-tagin joka viittaa ID:llä määriteltyyn webEditionin sisäiseen CSS-tiedostoon.";
$l_we_tag['customer']['description'] = "Using this tag, data from any customer can be displayed. The customer data are displayed as in a listview or within the &lt;we:object&gt; tag with the tag &lt;we:field&gt;.<br /><br />Combining the attributes, this tag can be utilized in three ways:<br/>If name is set, the editor can select a customer by using a customer-select-Field. This customer is stored in the document within the field name.<br />If name is not set but instead the id, the customer with this id is displayed.<br />If neither name nor id is set, the tag expects the id of the customer by a request parameter. This is i.e. used by the customer-listview when the attribut hyperlink=\"true\" in the &lt;we:field&gt; tag is used. The name of the request parameter is we_cid.";
$l_we_tag['customer']['defaultvalue'] = "";
$l_we_tag['date']['description'] = "we:date tagi näyttää kuluvan hetken päivämäärätiedot muodossa joka on määritelty päivämäärän muotoilumerkkijonossa. Jos dokumentti on staattinen, tyyppi tulee asettaa muotoon &quot;js&quot;, jotta aika saadaan tulostettua JavaScriptillä.";
$l_we_tag['dateSelect']['description'] = "we:dateSelect tagi tulostaa valintakentän päivämäärälle. Tätä voidaan käyttää yhdessä we:processDateSelect tagin kanssa jos halutaan lukea valittu arvo esim. muuttujaan joka on tyyppiä UNIX TIMESTAMP.";
$l_we_tag['delete']['description'] = "Tällä tagilla poistetaan dokumentteja joihin on menty &lt;we:a edit=\"document\" delete=\"true\"&gt; tai &lt;we:a edit=\"object\" delete=\"true\"&gt; kautta.";
$l_we_tag['deleteShop']['description'] = "we:deleteShop tagi poistaa koko ostoskorin.";
$l_we_tag['description']['description'] = "we:description tagi luo description- metatagin. Jos dokumentin kuvauskenttä Ominaisuudet- välilehdellä on tyhjä, käytetään HTML-sivun koko sisältöä kuvaustekstinä.";
$l_we_tag['description']['defaultvalue'] = "";
$l_we_tag['DID']['description'] = "Tagi palauttaa webEdition dokumentin ID:n.";
$l_we_tag['docType']['description'] = "Tagi palauttaa webEdition dokumentin dokumenttityypin.";
$l_we_tag['else']['description'] = "Tätä tagia käytetään lisäämään vaihtoehtoisia ehtohaaroja if-tyyppisten tagien sisälle. Esim.&lt;we:ifEditmode&gt;, &lt;we:ifNotVar&gt;, &lt;we:ifNotEmpty&gt;, &lt;we:ifFieldNotEmpty&gt;";
$l_we_tag['field']['description'] = "Tagi lisää \"name\" attribuutissa määritellyn kentän sisällön käytettäessä listviewiä. Tagi toimii vain we:repeat tagien välissä.";
$l_we_tag['flashmovie']['description'] = "we:flashmovie tagi mahdollistaa Flash-esityksen lisäämisen sivun sisältöön. Käytettäessä tätä tagia dokumentin muokkaustilassa näytetään tiedostoselaimen avaava esityksen valintapainike.";
$l_we_tag['form']['description'] = "we:form tagia käytetään haku- ja mailiformien luontiin. Se toimii samaan tapaan kuin normaali HTML-lomakekin, mutta se antaa parserin lisätä tarvitsemiaan lisätietokenttiä hidden muotoisena.";
$l_we_tag['form']['defaultvalue'] = "";
$l_we_tag['formfield']['description'] = "Tagia käytetään lisättäessä lomakekenttiä front end lomakkeeseen.";
$l_we_tag['formmail']['description'] = "With activated Setting Call Formmail via webEdition document, the integration of the formmail script is realized with a webEdition document. For this, the (currently without attributes) we-Tag formmail will be used. <br />If the Captcha-check is used, &lt;we:formmail/&gt; is located within the we-Tag ifCaptcha.";
$l_we_tag['hidden']['description'] = "we:hidden tagi luo piilotetun (hidden) kentän joka sisältää saman nimisestä globaalista PHP-muuttujasta haetun muuttuja-arvon. Käytä tätä tagia kun haluat siirtää esim. lomakkeelta tulevia arvoja eteenpäin.";
$l_we_tag['hidePages']['description'] = "we:hidePages mahdollistaa dokumentin tiettyjen välilehtien piilottamisen webEditionin puolella. Voit esimerkiksi rajoittaa pääsyä dokumentin Ominaisuudet- välilehdelle.";
$l_we_tag['href']['description'] = "we:href tagi luo valinnan jolla voidaan määrittää joko sisäisen tai ulkoisen dokumentin URL dokumentin muokkaustilassa.";
$l_we_tag['icon']['description'] = "we:icon tagi luo HTML-tagin joka viitta webEditionin sisäiseen ikonidokumenttiin we:tagille annetun ID:n perusteella. Ikonia käytetään mm. selainten osoiterivillä ja kirjanmerkeissä.";
$l_we_tag['ifBack']['description'] = "Tagia käytetään &lt;we:listview&gt; aloitus- ja lopetustagien välillä. we:back aloitus- ja lopetustagien sisään määritelty sisältö näytetään vain jos listviewillä on olemassa edellinen sivu.";
$l_we_tag['ifBack']['defaultvalue'] = "";
$l_we_tag['ifCaptcha']['description'] = "Tämän tagin sulkema sisältö esitetään vain jos käyttäjän syöttämä koodi on oikein.";
$l_we_tag['ifCaptcha']['defaultvalue'] = "";
$l_we_tag['ifCat']['description'] = "we:ifCat tagia käytetään rajaamaan näytettäviä kategorioita. Categories-listalle lisätään näytettävät kategoriat, joita verrataan dokumentin kategorioihin.";
$l_we_tag['ifCat']['defaultvalue'] = "";
$l_we_tag['ifNotCat']['description'] = "The we:ifNotCat tag ensures that everything located between the start tag and the end tag is only displayed if the categories which are entered under \"categories\" are none of the document's categories."; // TRANSLATE
$l_we_tag['ifNotCat']['defaultvalue'] = "";
$l_we_tag['ifClient']['description'] = "we:ifClient:n sisällä oleva tieto näytetään jos selain vastaa browser-kohtaan valittua selainta. Tagi toimii ainoastaan dynaamisilla sivuilla!";
$l_we_tag['ifClient']['defaultvalue'] = "";
$l_we_tag['ifConfirmFailed']['description'] = "Kun käytetään DoubleOptIn tagia Newsletter moduulissa, niin we:ifConfirmFailed -tagi tarkastaa sähköpostiosoitteen oikeellisuuden.";
$l_we_tag['ifConfirmFailed']['defaultvalue'] = "";
$l_we_tag['ifCurrentDate']['description'] = "Tämä tagi korostaa halutun päivän kalenteri-listview:ssä";
$l_we_tag['ifCurrentDate']['defaultvalue'] = "";
$l_we_tag['ifDeleted']['description'] = "Tämän tagin sisällä oleva tieto näytetään jos dokumentti tai objekti poistettiin käyttämällä we:delete -tagia";
$l_we_tag['ifDeleted']['defaultvalue'] = "";
$l_we_tag['ifDoctype']['description'] = "Tagin sisällä oleva tieto näytetään jos dokumenttityyppi vastaa sivuston doctypeen.";
$l_we_tag['ifDoctype']['defaultvalue'] = "";
$l_we_tag['ifDoubleOptIn']['description'] = "Tagin sisällä oleva tieto näytetään ainoastaa double opt-in prosessin ensimmäisessä vaiheessa.";
$l_we_tag['ifDoubleOptIn']['defaultvalue'] = "";
$l_we_tag['ifEditmode']['description'] = "Tagin sisällä oleva tieto näytetään ainoastaan editmodessa.";
$l_we_tag['ifEditmode']['defaultvalue'] = "";
$l_we_tag['ifEmailExists']['description'] = "Tagin sisällä oleva tieto näytetään ainoastaan jos määritetty sähköpostiosoite löytyy uutiskirjeen osoitelistalta.";
$l_we_tag['ifEmailExists']['defaultvalue'] = "";
$l_we_tag['ifEmailInvalid']['description'] = "Tagin sisällä oleva tieto näytetään ainoastaan jos tietty sähköpostiosoiteen syntaksi on virheellinen.";
$l_we_tag['ifEmailInvalid']['defaultvalue'] = "";
$l_we_tag['ifEmailNotExists']['description'] = "Tagin sisällä oleva tieto näytetään ainoastaan jos kyseessäoleva sähköpostiosoite ei ole uutiskirjeen osoitelistalla.";
$l_we_tag['ifEmailNotExists']['defaultvalue'] = "";
$l_we_tag['ifEmpty']['description'] = "Tagin sisällä oleva tieto näytetään ainoastaan jos kenttä on tyhjä jolla on sama nimi kuin match-arvona. Verrattavan kentän tyyppi täytyy määrittää, 'img,flashmovie, href,object'";
$l_we_tag['ifEmpty']['defaultvalue'] = "";
$l_we_tag['ifEqual']['description'] = "we:ifEqual tagi vertaa kenttien sisältöä 'name' ja 'eqname'. Jos sisältö on molemmissa sama niin sisältö näytetään. Jos tagia käytetään we:list:ssä, we:block:ssa tai we:linklist:ssä, vain yhtä kenttää voidaan verrata. Jos attribuuttia 'value' käytetään, 'eqname' hylätään ja sillon sisältöä verrataan 'value'-arvoon";
$l_we_tag['ifEqual']['defaultvalue'] = "";
$l_we_tag['ifFemale']['description'] = "Tagin sisällä oleva tieto näytetään ainoastaan jos käyttäjä on valinnut sukupuoleksi naisen.";
$l_we_tag['ifFemale']['defaultvalue'] = "";
$l_we_tag['ifField']['description'] = "Tagia käytetään we:repeat -tagin sisällä. Kaikki sisältö näytetään jos attribuutin 'match' arvo on identtinen tietokannasta löytyvään kenttään joka on määritetty listview:lle.";
$l_we_tag['ifField']['defaultvalue'] = "";
$l_we_tag['ifFieldEmpty']['description'] = "we:ifFieldEmpty varmistaa että kaikki tagin sisällä oleva tieto näytetään ainoastaan jos listview:n sisällä oleva kenttä on tyhjä ja jonka nimi täsmää 'match'-arvoon. Kentän tyypin on määriteltävä.";
$l_we_tag['ifFieldEmpty']['defaultvalue'] = "";
$l_we_tag['ifFieldNotEmpty']['description'] = "we:ifFieldNotEmpty varmistaa että kaikki tagin sisällä oleva tieto näytetään ainoastaan jos listview:n sisällä oleva kenttä ei ole tyhjä ja jonka nimi täsmää 'match'-arvoon. Kentän tyypin on määriteltävä.";
$l_we_tag['ifFieldNotEmpty']['defaultvalue'] = "";
$l_we_tag['ifFound']['description'] = "Tagin sisällä oleva tieto näytetään jos listview:llä on hakutuloksia";
$l_we_tag['ifFound']['defaultvalue'] = "";
$l_we_tag['ifHasChildren']['description'] = "we:repeat -tagin sisällä we:ifHasChildren:iä käytetään alikansioiden tarkistukseen, jos niitä löytyy ne tulostetaan";
$l_we_tag['ifHasChildren']['defaultvalue'] = "";
$l_we_tag['ifHasCurrentEntry']['description'] = "we:ifHasCurrentEntry:ä voidaan käyttää we:navigationEntry type='folder':n sisällä näyttääkseen aktiivista sisältöä";
$l_we_tag['ifHasCurrentEntry']['defaultvalue'] = "";
$l_we_tag['ifHasEntries']['description'] = "we:ifHasEntries:iä voidaan käyttää tulostaakseen we:nagigationEntry:n mahdolliset alikansiot";
$l_we_tag['ifHasEntries']['defaultvalue'] = "";
$l_we_tag['ifHasShopVariants']['description'] = "we:ifHasShopVariants voi näyttää sisältöä riippuen muuttujien olemassaolosta objektissa tai dokumentissa. Voidaan kontrolloida we:listview type='shopVariant'. <b>This tag works in document and object templates attached by object-workspaces, but not inside the we:listview and we:object - tags</b>";// TRANSLATE
$l_we_tag['ifHasShopVariants']['defaultvalue'] = "";
$l_we_tag['ifHtmlMail']['description'] = "Tämän tagin sisältö näytetään vain jos uutiskirjeen formaatti on HTML.";
$l_we_tag['ifHtmlMail']['defaultvalue'] = "";
$l_we_tag['ifIsDomain']['description'] = "Tämän tagin sisällä oleva tieto näytetään jos palvelimen domain -nimi on sama kuin 'domain' -arvo. Sisällön voi nähdä ainoastaan valmiissa sivussa tai esikatselussa. Muokkaustilassa tätä tagia ei oteta huomioon.";
$l_we_tag['ifIsDomain']['defaultvalue'] = "";
$l_we_tag['ifIsNotDomain']['description'] = "Tämän tagin sisällä oleva tieto näytetään jos palvelimen domain -nimi ei ole sama kuin 'domain' -arvo. Sisällön voi nähdä ainoastaan valmiissa sivussa tai esikatselussa. Muokkaustilassa tätä tagia ei oteta huomioon..";
$l_we_tag['ifIsNotDomain']['defaultvalue'] = "";
$l_we_tag['ifLastCol']['description'] = "Tämän tagi havaitsee taulukosta rivin viimeisen viimeisen sarakkeen, kun käytetään we:listview:n taulukkofunktioiden kanssa;";
$l_we_tag['ifLastCol']['defaultvalue'] = "";
$l_we_tag['ifLoginFailed']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain jos sisäänkirjautuminen epäonnistui.";
$l_we_tag['ifLoginFailed']['defaultvalue'] = "";
$l_we_tag['ifLogin']['description'] = "Content enclosed by this tag is only displayed if a login occured on the page and allows for initialisation.";// TRANSLATE
$l_we_tag['ifLogin']['defaultvalue'] = "";
$l_we_tag['ifLogout']['description'] = "Content enclosed by this tag is only displayed if a logout occured on the page and allows for cleaning up.";// TRANSLATE
$l_we_tag['ifLogout']['defaultvalue'] = "";
$l_we_tag['ifTdEmpty']['description'] = "Content enclosed by this tag is only displayed if a table cell is empty (has no contents in a listview).";// TRANSLATE
$l_we_tag['ifTdEmpty']['defaultvalue'] = "";
$l_we_tag['ifTdNotEmpty']['description'] = "Content enclosed by this tag is only displayed if a table cell is not empty (has contents in a listview).";// TRANSLATE
$l_we_tag['ifTdNotEmpty']['defaultvalue'] = "";
$l_we_tag['ifMailingListEmpty']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain jos käyttäjä ei ole valinnut yhtään uutiskirjettä.";
$l_we_tag['ifMailingListEmpty']['defaultvalue'] = "";
$l_we_tag['ifMale']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain jos käyttäjä on mies. Tätä tagia käytetään uutiskirjeiden käyttjien sukupuolen tunnistuksessa.";
$l_we_tag['ifMale']['defaultvalue'] = "";
$l_we_tag['ifNew']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain uudessa webEdition dokumentissa tai objektissa.";
$l_we_tag['ifNew']['defaultvalue'] = "";
$l_we_tag['ifNewsletterSalutationEmpty']['description'] = "Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is empty.";// TRANSLATE
$l_we_tag['ifNewsletterSalutationEmpty']['defaultvalue'] = "";
$l_we_tag['ifNewsletterSalutationNotEmpty']['description'] = "Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is not empty.";// TRANSLATE
$l_we_tag['ifNewsletterSalutationNotEmpty']['defaultvalue'] = "";
$l_we_tag['ifNotNewsletterSalutation']['description'] = "Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is not equal to match.";// TRANSLATE
$l_we_tag['ifNotNewsletterSalutation']['defaultvalue'] = "";
$l_we_tag['ifNext']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain jos 'Seuraavat' -objekteja on saatavilla";
$l_we_tag['ifNext']['defaultvalue'] = "";
$l_we_tag['ifNoJavaScript']['description'] = "Tämä tagi uudelleenohjaa sivun toiselle sivulle ID:n perusteella jos selaimessa ei ole tukea JavaScript:lle tai jos JavaScript on pois päältä. Tätä tagia voidaan käyttää ainoastaan templaten head-osiossa..";
$l_we_tag['ifNotCaptcha']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain jos käyttäjän syöttämä koodi ei ole oikein.";
$l_we_tag['ifNotCaptcha']['defaultvalue'] = "";
$l_we_tag['ifNotDeleted']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain jos webEdition dokumenttia tai objektia ei voitu poistaa we:delete -tagilla";
$l_we_tag['ifNotDeleted']['defaultvalue'] = "";
$l_we_tag['ifNotDoctype']['description'] = "";
$l_we_tag['ifNotDoctype']['defaultvalue'] = "";
$l_we_tag['ifNotEditmode']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain jos ei olla sivun muokkaustilassa";
$l_we_tag['ifNotEditmode']['defaultvalue'] = "";
$l_we_tag['ifNotEmpty']['description'] = "Tämä tagi varmistaa että tämän tagin sisältö näytetään jos sen sisällä olevan we-tagin nimi vastaa 'match' atribuutin arvoon EIKÄ se ole tyhjä. Tyyppi täytyy määrittää.";
$l_we_tag['ifNotEmpty']['defaultvalue'] = "";
$l_we_tag['ifNotEqual']['description'] = "Tämä tagi vertaa sisälläolevan we-tagin nimi atribuuttia 'eqname':n arvoon, jos se eivät ole samat, sisältö näytetään. Jos attribuuttia 'value' käytetään, 'eqname' hylätään ja sillon sisältöä verrataan 'value'-arvoon ";
$l_we_tag['ifNotEqual']['defaultvalue'] = "";
$l_we_tag['ifNotField']['description'] = "Tagia käytetään we:repeat -tagin sisällä. Kaikki sisältö näytetään jos attribuutin 'match' arvo ei ole identtinen tietokannasta löytyvään kenttään joka on määritetty listview:lle.";
$l_we_tag['ifNotField']['defaultvalue'] = "";
$l_we_tag['ifNotFound']['description'] = "Tagin sisällä oleva tieto näytetään jos listview:llä ei ole hakutuloksia";
$l_we_tag['ifNotFound']['defaultvalue'] = "";
$l_we_tag['ifNotHtmlMail']['description'] = "Tämän tagin sisältö näytetään vain jos uutiskirjeen formaatti ei ole HTML.";
$l_we_tag['ifNotHtmlMail']['defaultvalue'] = "";
$l_we_tag['ifNotNew']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain uudessa webEdition dokumentissa tai objektissa.";
$l_we_tag['ifNotNew']['defaultvalue'] = "";
$l_we_tag['ifNotObject']['description'] = "Tämän tagin sisältö näytetään vain jos listview:n sisältö ei ole objekti. Listviewin tyyppi täytyy olla 'search';";
$l_we_tag['ifNotObject']['defaultvalue'] = "";
$l_we_tag['ifNotPosition']['description'] = "Tämä tagi mahdollistaa toiminnon määrittelyn mitä EI tehdä tietyssä block:n, listview:n, linklist:n listdir:n kohdassa. Parametri 'position' hyväksyy monipuolisia arvoja, 'first','last','all even','all odd', tai numeerisen määrittely (1,2,3...). Tyyppinä täytyy olla block tai linklist ja nimi sillä.";
$l_we_tag['ifNotPosition']['defaultvalue'] = "";
$l_we_tag['ifNotRegisteredUser']['description'] = "Tarkistaa onko käyttäjä rekisteröitynyt.";
$l_we_tag['ifNotRegisteredUser']['defaultvalue'] = "";
$l_we_tag['ifNotReturnPage']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain luonnin tai muokkauksen jälkeen mikäli paluuarvo we:a edit='true' on epätosi tai ei määritetty.";
$l_we_tag['ifNotReturnPage']['defaultvalue'] = "";
$l_we_tag['ifNotSearch']['description'] = "Tagin sisältö tulostetaan vain jos hakutermiä ei lähetetty we:search:ltä tai se oli tyhjä. Jos attribuutti 'set' = tosi, vain we:search:n pyydetyt muuttujat on varmennettu asettamattomiksi.";
$l_we_tag['ifNotSearch']['defaultvalue'] = "";
$l_we_tag['ifNotSeeMode']['description'] = "Tämän tagin sisältö näytetään vain seeMode:n ulkopuolelle.";
$l_we_tag['ifNotSeeMode']['defaultvalue'] = "";
$l_we_tag['ifNotSelf']['description'] = "Mitään tietoa tämän tagin sisällä ei näytetä jos dokumentilla on yksikään tagiin syötetyistä ID:stä. Jos tagi ei sijaitse we:linklist:n, we:listdir:n sisällä 'id' on pakollinen kenttä!";
$l_we_tag['ifNotSelf']['defaultvalue'] = "";
$l_we_tag['ifNotSidebar']['description'] = "This tag is used to display the enclosed contents only if the opened document is not located within the Sidebar."; // TRANSLATE
$l_we_tag['ifNotSidebar']['defaultvalue'] = "";
$l_we_tag['ifNotSubscribe']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain jos tilaus epäonnistui. Tämä tagi pitäisi olla 'Tilaa uutiskirje' templatessa we:addDelNewsletterEmail -tagin jälkeen.";
$l_we_tag['ifNotSubscribe']['defaultvalue'] = "";
$l_we_tag['ifNotTemplate']['description'] = "Näytä sisältyvä tieto vain, jos nykyinen dokumentti ei perustu annettuun sivupohjaan.<br /><br />Löydät lisätietoja we:ifTemplate -tagin referenssistä.";
$l_we_tag['ifNotTemplate']['defaultvalue'] = "";
$l_we_tag['ifNotTop']['description'] = "Tämän tagin sisältö näytetään vain jos se sijaitsee 'include' -dokumentissa.";
$l_we_tag['ifNotTop']['defaultvalue'] = "";
$l_we_tag['ifNotUnsubscribe']['description'] = "Tämän tagin sisällä oleva tieto näytetään vain jos pyyntö ei toimi. Tämä tagi pitäisi olla 'Peru uutiskirjetilaus' templatessa we:addDelNewsletterEmail -tagin jälkeen.";
$l_we_tag['ifNotUnsubscribe']['defaultvalue'] = "";
$l_we_tag['ifNotVar']['description'] = "Tämän tagin sisällä olevaa tietoa ei näytetä jos muuttujan 'name' -arvo on sama kuin 'match' -arvo. Muuttujan tyyppi voidaan määrittää 'type'-attribuutilla";
$l_we_tag['ifNotVar']['defaultvalue'] = "";
$l_we_tag['ifNotVarSet']['description'] = "Tämän tagin sisällä oleva tieto näytetään jos muuttujaa nimellä 'name' ei ole määritetty. Huom! 'Ei asetettu' ei ole sama kuin tyhjä!";
$l_we_tag['ifNotVarSet']['defaultvalue'] = "";
$l_we_tag['ifNotVote']['description'] = "Tämän tagin sisällä oleva tieto näytetään jos äänestystä ei tallennettu. 'type' -attribuutti määrittää virheen tyypin.";
$l_we_tag['ifNotVote']['defaultvalue'] = "";
$l_we_tag['ifNotWebEdition']['description'] = "Tämän tagin sisältö näytetään vain webEditionin ulkopuolelle.";
$l_we_tag['ifNotWebEdition']['defaultvalue'] = "";
$l_we_tag['ifNotWorkspace']['description'] = "Tarkastaa, sijaitseeko dokumentti jossain muualla kuin työtilassa joka on määritelty 'path' attribuutissa";
$l_we_tag['ifNotWorkspace']['defaultvalue'] = "";
$l_we_tag['ifNotWritten']['description'] = "Tämän tagin sisältö näytetään vain jos tapahtuu virhe dokumentin tai objektin tallennusvaiheessa käyttäen we:write -tagia.";
$l_we_tag['ifNotWritten']['defaultvalue'] = "";
$l_we_tag['ifObject']['description'] = "Tämän tagin sisällä oleva tieto näytetään jos löydettiin yksilöllinen rivi we:listview type='search':lla joka on objekti.";
$l_we_tag['ifObject']['defaultvalue'] = "";
$l_we_tag['ifPosition']['description'] = "Tämä tagi mahdollistaa toiminnon määrittelyn mitä tehdään tietyssä block:n, listview:n, linklist:n listdir:n kohdassa. Parametri 'position' hyväksyy monipuolisia arvoja, 'first','last','all even','all odd', tai numeerisen määrittely (1,2,3...). Tyyppinä täytyy olla block tai linklist ja nimi sillä.";
$l_we_tag['ifPosition']['defaultvalue'] = "";
$l_we_tag['ifRegisteredUser']['description'] = "Tarkastaa, jos käyttäjä on rekisteröitynyt.";
$l_we_tag['ifRegisteredUser']['defaultvalue'] = "";
$l_we_tag['ifRegisteredUserCanChange']['description'] = "Tämän tagin sisältö näytetään vain jos rekisteröitynyt käyttäjä, joka on kirjautuneena sisään on oikeutettu muokkaamaan tämänhetkistä webEdition documenttia tai objektia.";
$l_we_tag['ifRegisteredUserCanChange']['defaultvalue'] = "";
$l_we_tag['ifReturnPage']['description'] = "Tämän tagin sisältö näytetään webEdition dokumentin tai objektin luonnin tai muokkauksen jälkeen ja palautettava arvo 'result' on we:a edit='document' tai we:a edit='object' on tosi.";
$l_we_tag['ifReturnPage']['defaultvalue'] = "";
$l_we_tag['ifSearch']['description'] = "Tagin sisältö tulostetaan vain jos hakutermi lähetettiin we:search:lle ja se ei ole tyhjä. Jos attribuutti 'set' = tosi, vain we:search:n pyydetyt muuttujat on varmennettu asettamattomiksi.";
$l_we_tag['ifSearch']['defaultvalue'] = "";
$l_we_tag['ifSeeMode']['description'] = "Tämän tagin sisältö näytetään vain seeMode:ssa.";
$l_we_tag['ifSeeMode']['defaultvalue'] = "";
$l_we_tag['ifSelf']['description'] = "Tämän tagin sisältö näytetään jos dokumentilla on sama ID kuin mikä on määritetty tähän tagiin. Jos tagi ei ole we:linklist tai we:listdir:n sisällä, ID on pakollinen!";
$l_we_tag['ifSelf']['defaultvalue'] = "";
$l_we_tag['ifShopEmpty']['description'] = "Tämän tagin sisältö näytetään jos ostoskori on tyhjä.";
$l_we_tag['ifShopEmpty']['defaultvalue'] = "";
$l_we_tag['ifShopNotEmpty']['description'] = "Tämän tagin sisältö näytetään jos ostoskori ei ole tyhjä.";
$l_we_tag['ifShopNotEmpty']['defaultvalue'] = "";
$l_we_tag['ifShopPayVat']['description'] = "Tagin sisältö näytetään jos kirjautuneen henkilön tulee maksaa alv.";
$l_we_tag['ifShopPayVat']['defaultvalue'] = "";
$l_we_tag['ifShopVat']['description'] = "Tämä tagi tarkistaa alv:n kyseisestä dokumentista / ostoskorista. ID mahdollistaa alv:n tarkistuksen tietyistä artikkeleista.";
$l_we_tag['ifShopVat']['defaultvalue'] = "";
$l_we_tag['ifSidebar']['description'] = "This tag is used to display the enclosed contents only if the opened document is located within the Sidebar."; // TRANSLATE
$l_we_tag['ifSidebar']['defaultvalue'] = "";
$l_we_tag['ifSubscribe']['description'] = "Tämän tagin sisältö näytetään jos uutiskirjeen tilaus onnistui. Tagia tulee käyttää uutiskirjeen tilaustemplatessa heti addDelnewsletterEmail -tagin jälkeen.";
$l_we_tag['ifSubscribe']['defaultvalue'] = "";
$l_we_tag['ifTemplate']['description'] = "";
$l_we_tag['ifTemplate']['defaultvalue'] = "";
$l_we_tag['ifTop']['description'] = "Tämän tagin sisältö näytetään jos tagi ei ole liitetyssä (included) dokumentissa.";
$l_we_tag['ifTop']['defaultvalue'] = "";
$l_we_tag['ifUnsubscribe']['description'] = "Tämän tagin sisältö näytetään jos uutiskirjeen tilauksen peruuttaminen onnistui. Tagia tulee käyttää uutiskirjeen tilaustemplatessa heti addDellnewsletterEmail -tagin jälkeen.";
$l_we_tag['ifUnsubscribe']['defaultvalue'] = "";
$l_we_tag['ifUserInputEmpty']['description'] = "Tämän tagin sisältö näytetään tietty input-kenttä on tyhjä.";
$l_we_tag['ifUserInputEmpty']['defaultvalue'] = "";
$l_we_tag['ifUserInputNotEmpty']['description'] = "Tämän tagin sisältö näytetään tietty input-kenttä ei ole tyhjä.";
$l_we_tag['ifUserInputNotEmpty']['defaultvalue'] = "";
$l_we_tag['ifVar']['description'] = "Tämä tagin sisältö näytetään jos toisen we-tagin name-muuttujan arvo on sama kuin tämän tagin match-muuttujan arvo. Muuttujan tyyppi voidaan määrittää.";
$l_we_tag['ifVar']['defaultvalue'] = "";
$l_we_tag['ifVarEmpty']['description'] = "Tämän tagin sisältö näytetään jos muuttuja on tyhjä jolla on sama nimi kuin match-attribuuttiin on määritelty.";
$l_we_tag['ifVarEmpty']['defaultvalue'] = "";
$l_we_tag['ifVarNotEmpty']['description'] = "Tämän tagin sisältö näytetään jos muuttuja on ei ole tyhjä ja jolla on sama nimi kuin match-attribuuttiin on määritelty.";
$l_we_tag['ifVarNotEmpty']['defaultvalue'] = "";
$l_we_tag['ifVarSet']['description'] = "Tämän tagin sisältö näytetään jos kohde muutujaa ei ole asetettu. Huom! 'asetettu' ei ole sama kuin 'ei tyhjä'";
$l_we_tag['ifVarSet']['defaultvalue'] = "";
$l_we_tag['ifVote']['description'] = "Tämän tagin sisältö näytetään jos äänestys tallennettiin onnistuneesti.";
$l_we_tag['ifVote']['defaultvalue'] = "";
$l_we_tag['ifVoteActive']['description'] = "Tämän tagin sisältö näytetään jos äänestyksen aika ei ole umpeutunut.";
$l_we_tag['ifVoteActive']['defaultvalue'] = "";
$l_we_tag['ifWebEdition']['description'] = "Tämän tagin sisältö näytetään vain webEditionin sisällä, mutta ei julkaistussa dokumentissa.";
$l_we_tag['ifWebEdition']['defaultvalue'] = "";
$l_we_tag['ifWorkspace']['description'] = "Tarkistaa, sijaitseeko dokumentti työtilassa joka on määritelty 'path' -attribuutissa.";
$l_we_tag['ifWorkspace']['defaultvalue'] = "";
$l_we_tag['ifWritten']['description'] = "Tämän tagin sisältö on käytettävissä vian jos kirjoitus dokumenttiin tai objektiin onnisui. kts. we:write -tagi.";
$l_we_tag['ifWritten']['defaultvalue'] = "";
$l_we_tag['img']['description'] = "we:img tagilla voidaan lisätä kuva dokumentin muokkaus-tilassa. Jos mitään attribuutteja ei määritetä, käytetään oletusarvoja. 'showimage':lla kuva voidaan piilottaa muokkaus-tilassa. 'showinputs':lla kuvan title- ja alt- attribuutit on pois käytöstä..";
$l_we_tag['include']['description'] = "Tällä tagilla voidaan liittää webEdition dokumentti tai HTML-sivu sivupohjaan. 'gethttp':llä voidaan määrittää halutaanko liitetty tiedosto siirtää HTTP:n avulla vai ei.'seeMode':lla määritellään onko dokumentti muokattavissa 'seeModessa'.";
$l_we_tag['input']['description'] = "The we:input tag creates a single-line input box in the edit mode of the document based on this template, if the type = \"text\" is selected. For all other types, see the manual or help.";
$l_we_tag['js']['description'] = "we:js tagi luo HTML-tagin joka viittaa webEditionin sisäiseen JavaScript-dokumenttiin jonka ID on määritelty listaan. JavaScriptit voi määrittää myös erillisessä tiedostossa..";
$l_we_tag['keywords']['description'] = "we:keywords -tagi luo avainsana -metatagin.  Jos 'Ominaisuus' avainsana -kenttä on tyhjä, tämän tagin sisällä olevia sanoja käytetään avainsanoina. Muuten käytetään 'Ominaisuus':ssa määriteltyjä avainsanoja.";
$l_we_tag['link']['description'] = "we:link -tagi luo yksittäisen linkin jota voidaan muokata 'muokkaa'-napilla. Jos we:link:iä käytetään we:linklist:n sisällä 'nimi'-attribuuttia ei tule määritellä we:link-tagiin, muutoin kyllä. 'only' -attribuuttiin voidaan määritellä attribuutti jonka linkki palauttaa, esim. 'only=\"content\"'.";
$l_we_tag['linklist']['description'] = "we:linklist -tagilla luodaan linkkilista. we:prelink -tagin sisältö tulostetaan ennen linkkiä muokkaustilassa..";
$l_we_tag['linklist']['defaultvalue'] = "&lt;we:link /&gt;&lt;we:postlink&gt;&lt;br /&gt;&lt;/we:postlink&gt;";
$l_we_tag['linkToSeeMode']['description'] = "Tämä tagi luo linkin joka avautuu valittuun dokumenttiin 'seeMode':ssa.";
$l_we_tag['list']['description'] = "we:list -tagilla voit tehdä laajennettavia listoja. Tagien sisällä oleva tieto liitetään listaan.";
$l_we_tag['list']['defaultvalue'] = "";
$l_we_tag['listdir']['description'] = "we:listdir -tagi luo listan joka näyttää kaikki dokumentit jotka ovat samassa kansiossa. 'field' -attribuutilla voidaan määritellä minkä kentän arvo näytetään. Jos attribuutti on tyhjä tai ei ole olemassa, tiedoston nimi näytetään. Minkä kentän halutaan näyttävän kansioita tulee määrittää attribuuttiin 'dirfield'. Jos attribuutti on tyhjä tai sitä ei ole olemassa, 'field'-kentän nimi on verrannollinen käytetyn tiedoston nimeen. Jos käytetään 'id'-attribuuttia, kansion tiedostot jossa on tämä sama id näytetään.";
$l_we_tag['listdir']['defaultvalue'] = "";
$l_we_tag['listview']['description'] = "we:listview -tagilla luodaan listoja jotka generoidaan automaattisesti.";
$l_we_tag['listview']['defaultvalue'] = "&lt;we:repeat&gt;
&lt;we:field name=\"Title\" alt=\"we_path\" hyperlink=\"true\"/&gt;
&lt;br /&gt;
&lt;/we:repeat&gt;";
$l_we_tag['listviewEnd']['description'] = "Tämä tagi näyttää viimeisen rivin joka on we:listview:llä.";
$l_we_tag['listviewPageNr']['description'] = "Tämä tagi palauttaa tämänhetkisen we:listview -sivun.";
$l_we_tag['listviewPages']['description'] = "Tämä tagi palauttaa we:listview:n sivumäärän.";
$l_we_tag['listviewRows']['description'] = "Tämä tagi palauttaa löydettyjen rivien määrän we:listview:ltä.";
$l_we_tag['listviewStart']['description'] = "Tämä tagi näyttää ensimmäisen rivin joka on we:listview:llä.";
$l_we_tag['makeMail']['description'] = "Tämä tagi pitää olla ensimmäinen rivi jokaisessa sivupohjassa joka lähetetään we:sendMail:lla.";
$l_we_tag['master']['description'] = "";
$l_we_tag['master']['defaultvalue'] = "";
$l_we_tag['metadata']['description'] = "";
$l_we_tag['metadata']['defaultvalue'] = "&lt;we:field name=\"NameOfField\" /&gt;";
$l_we_tag['navigation']['description'] = "Tätä tagia käytetään alustamaan navigaatio joka luodaan navigaatio työkalulla.";
$l_we_tag['navigationEntries']['description'] = "we:navigationEntry type='folder' tulostaa luotuja kansio-tyyppisiä navigaatiopisteitä.";
$l_we_tag['navigationEntry']['description'] = "we:navigationEntry:llä voidaan valita tulostetaanko 'folder' vai 'entry'-tyyppisiä navigaatioita. Lisäattributeilla voidaan tarkentaa haluttua navigaatiotulostusta.";
$l_we_tag['navigationEntry']['defaultvalue'] = "&lt;a href=\"&lt;we:navigationField name=\"href\" /&gt;\"&gt;&lt;we:navigationField name=\"text\" /&gt;&lt;/a&gt;&lt;br /&gt;";
$l_we_tag['navigationField']['description'] = "&lt;we:navigationField&gt; is used within &lt;we:navigationEntry&gt; to print a value of the current navigation entry.<br/>Choose from <b>either</b> the attribute <i>name</i>, <b>or</b> from the attribute <i>attributes</i>, <b>or</b> from the attribute <i>complete</i>";// TRANSLATE
$l_we_tag['navigationWrite']['description'] = "Is used to write a we:navigation with given name";
$l_we_tag['newsletterConfirmLink']['description'] = "Tätä tagia käytetään 'double opt-in' vahvistuslinkin luomiseen.";
$l_we_tag['newsletterConfirmLink']['defaultvalue'] = "Todenna uutiskirje";
$l_we_tag['newsletterField']['description'] = "Displays a field from the recipient dataset within the newsletter."; // TRANSLATE
$l_we_tag['newsletterSalutation']['description'] = "Tätä tagia käytetään näytettäessä 'puhuttelu''-kenttiä.";
$l_we_tag['newsletterUnsubscribeLink']['description'] = "Luo linkin uutiskirjeen perumiseen. Tagia voidaan käyttää ainoastaan 'sähköposti' sivupohjissa!";
$l_we_tag['next']['description'] = "Luo linkin joka viittaa seuraaviin sivuihin we:listview:llä.";
$l_we_tag['next']['defaultvalue'] = "";
$l_we_tag['noCache']['description'] = "PHP-koodi ajetaan aina kun välimuistissa olevaa dokumenttia kutsutaan.";
$l_we_tag['noCache']['defaultvalue'] = "";
$l_we_tag['object']['description'] = "we:object:lla näytetään objekteja. Objektin kenttiä voidaan näyttää we:field -tagilla. Jos 'name'-attribuutti on määritelty niin objektivalitsin näytetään muokkaustilassa josta voi valita kaikki objektit kaikista luokista. Jos 'classid' on määritelty objektivalitsimella voi valita kaikki objektit tietystä luokasta. Pelkällä 'id':llä voidaan valita yksittäinen objekti..";
$l_we_tag['object']['defaultvalue'] = "";
$l_we_tag['pagelogger']['description'] = "The we:pagelogger tag generates, depending on the selected \"type\" attribute, the necessary capture code for pageLogger or the fileserver- respectively the download-code.";
$l_we_tag['path']['description'] = "The we:path tag represents the path of the current document. If there is an index file in one of the subdirectories, a link is set on the respective directory. The used index files (separated by commas) can be specified in the attribute \"index\". If nothing is specified there, \"default.html\", \"index.htm\", \"index.php\", \"default. htm\", \"default.html\" and \"default.php\" are used as default settings. In the attribute \"home\" you can specify what to put at the very beginning. If nothing is specified, \"home\" is displayed automatically. The attribute separator describes the delimiter between the directories. If the attribute is empty, \"/\" is used as delimiter. The attribute \"field\" defines what sort of field (files, directories) is displayed. If the field is empty or non-existent, the filename will be displayed. The attribute \"dirfield\" defines which field is used for display in directories. If the field is empty or non-existent, the entry of \"field\" or the filename is used.";
$l_we_tag['paypal']['description'] = "The tag we:paypal implements an interface to the payment provider paypal. To ensure that this tag works properly, add additional parameters in the backend of the shop module.";
$l_we_tag['position']['description'] = "The tag we:position is used to return the actual position of a listview, block, linklist, listdir. Is \"type= block or linklist\" it is necessary to specify the name (reference) of the related block/linklist. The attribute \"format\" determines the format of the result.";
$l_we_tag['postlink']['description'] = "we:postlink tagi varmistaa, että kaikki aloitus ja lopetus -tagien välillä oleva sisältö ei näy listan viimeisellä linkillä.";
$l_we_tag['postlink']['defaultvalue'] = "";
$l_we_tag['prelink']['description'] = "we:prelink -tagi varmistaa, että kaikki aloitus ja lopetus -tagien välillä oleva sisältö ei näy linkkilistan ensimmäisellä linkillä.";
$l_we_tag['prelink']['defaultvalue'] = "";
$l_we_tag['printVersion']['description'] = "we:printVersion -tagi luo HTML linkin, joka osoittaa samaan tiedostoon mutta eri sivupohjaan. \"tid\" Määrittää sivupohjan id:n. Tagi linkittää kaiken alku ja lopetus -tagien välissä olevan sisällön.";
$l_we_tag['printVersion']['defaultvalue'] = "";
$l_we_tag['processDateSelect']['description'] = "&lt;we:processDateSelect&gt; -tagi prosessoi 3 arvoa we:dateSelect tagin valintakentistä UNIX timestamp -muotoiseksi. Arvo tallennetaan globaaliksi muuttujaksi, joka on nimetty kohtaan \"nimi&quuot;.";
$l_we_tag['quicktime']['description'] = "we:quicktime -tagilla voit lisätä Quicktime elokuvan tiedostoon. Tähän sivupohjaan perustuvat tiedostot näyttävät muokkausnapin muokkaustilassa. Tämä napin klikkaaminen avaa tiedostohallinan, joka antaa sinun valita Quicktime elokuvan, jonka olet jo siirtäny webEditioniin. Tällä hetkellä ei ole xhtml-validia koodia, joka toimisi sekä IE:ssä että Mozillassa. Tämänvuoksi, xml on aina asetettu arvoon \"epätosi\"";
$l_we_tag['registeredUser']['description'] = "Tämä tagi tulostaa asiakastiedot, jotka on tallennettu asiakashalllintamoduuliin.";
$l_we_tag['registerSwitch']['description'] = "Tämä tagi luo kytkimen jolla voit vaihtaa rekisteröityneen ja rekisteröitymättönmän käyttäjän statuksen välillä muokkaustilassa. Jos olet käyttänyt &lt;we:ifRegisteredUser&gt; ja &lt;we:ifNotRgisteredUser&gt; -tags, tämä tagi antaa sinun katsoa eri tiloja ja pitää sisällön muotoilu kunnossa.";
$l_we_tag['repeat']['description'] = "Tämä tagin sisältö toistetaan jokaiselle löydetylle kohdalle &lt;we:listview&gt; -tagissa. Tätä tagia käytetään vain &lt;we:listview&gt; yhteydessä.";
$l_we_tag['repeat']['defaultvalue'] = "";
$l_we_tag['repeatShopItem']['description'] = "Tämä tagi näyttää kaiken ostoskorinsisällön.";
$l_we_tag['repeatShopItem']['defaultvalue'] = "";
$l_we_tag['returnPage']['description'] = "Tätä tagia käytetään lähdesivuun viittavan osoitteen näyttämiseen, jos \"palaa\" on asetettu arvoon \"tosi\" käytettäessä: &lt;we:a edit=\"document\"&gt; or &lt;we:a edit=\"object\"&gt;";
$l_we_tag['saferpay']['description'] = "we:saferpay implementoi rajapinnan saferpay maksuhallintaan. Varmistaaksesi tagin toiminnan, syötä lisätietoja Kauppamoduulin backendiin.";
$l_we_tag['saveRegisteredUser']['description'] = "Tämä tagi tallentaa kaikki käyttäjätiedot, joita on syötetty istuntokenttiin.";
$l_we_tag['search']['description'] = "we:search -tagi luo syöttökentän, jota käytetään hakukenttänä. Hakukentällä on sisäinen nimi \"we_search\". Kun hakulomake lähetetään, the PHP muuttuja \"we_search\" vastaanottavalla sivulla saa arvokseen hakukentän sisällön.";
$l_we_tag['select']['description'] = "we:select -tagi luo valintakentän muokkaustilaan. Jos kooksi on määritelty \"1\" valintakenttää näytetään pudotusvalikkona. Tagi toimii samoin kuin HTML select -tagi. Aloitus ja lopetus -tagien väliin syötettään normaalit HTML option -tagit.";
$l_we_tag['select']['defaultvalue'] = "&lt;option&gt;#1&lt;/option&gt;
&lt;option&gt;#2&lt;/option&gt;
&lt;option&gt;#3&lt;/option&gt;";
$l_we_tag['sendMail']['description'] = "Tämä tagi lähettää webEdition sivun sähköpostina \"vastaanottaja\" kohtaan määriteltyyn osoitteeseen.";
$l_we_tag['sessionField']['description'] = "we:sessionField -tagi luo HTML input, select tai text area tagin. Sitä käytetään sisällön syöttämiseksi istuntokenttiin (esim. Käyttäjätieto, jne.).";
$l_we_tag['sessionLogout']['description'] = "we:sessionLogout -tagi luo HTML linkki tagin joka osoittaa sisäiseen webEdition tiedostoon, jolla on webEdition Tagi velhossa mainittu ID. Jos tällä webEdition tiedostolla on we:sessionStart -tagi ja tiedosto on dynaaminen, aktiivinen istunto poistetaan ja suljetaan. Mitään tietoja ei tallenneta.";
$l_we_tag['sessionLogout']['defaultvalue'] = "";
$l_we_tag['sessionStart']['description'] = "Tätä tagia käytetään istunnon aloittamiseen tai aiemman istunnon jatkamiseen. Tämä tagi vaaditaan sivupohjiin, jotka luovat seuraavan tyyppisiä sivuja: Sivuja, jotka on suojattu jollain tavoin Käyttäjähallinta moduulilla, Kauppasivuja tai sivuja jotka tukevat front end input:ia.&lt;br /&gt;Tämä tagin täytyy olla ensimmäinen tagi sivupohjan ensimmäisellä rivillä!";
$l_we_tag['setVar']['description'] = "Tätä tagia käytetään muuttujien arvojen asetukseen.<br/><strong>Attention:</strong> Without the attribute <strong>striptags=\"true\"</strong>, HTML- and PHP-Code is not filtered, this is a potenzial security risk!</strong>";// TRANSLATE
$l_we_tag['shipping']['description'] = "we:shipping -tagia käytetään lähetyskulujen määrittelyyn. Nämä kulut perustuvat ostoskorin arvoon, rekisteröityneen käyttäjän kotimaahan ja lähetyskulujen määrittely sääntöjä voidaan muokata Kauppa moduulissa. Parametriin \"summa\" määritellään we:sum -tagin nimi. \"tyyppi\" -parametrilla määritellään joko lähetyskulujen tyyppi.";
$l_we_tag['shopField']['description'] = "Tämä tagi tallentaa useamman sisältökentän suoraan tuotteesta tai ostoskorista. The pääkäyttäjä voi määrittää tiettyjä arvoja joista käyttäjä voi valita tai syöttää omansa arvonsa. Näin on mahdollista kartoittaa useampia toisintoja tuotteista helposti.";
$l_we_tag['ifShopFieldEmpty']['description'] = "Content enclosed by this tag is only displayed if the shopField named in attribute \"name\" is empty.";// TRANSLATE
$l_we_tag['ifShopFieldNotEmpty']['description'] = "Content enclosed by this tag is only displayed if the shopField named in attribute \"name\" is not empty.";// TRANSLATE
$l_we_tag['ifShopField']['description'] = "Everything between the start and end tags of this tag is displayed only if the value of the attribut \"match\" is identical with the value of the shopField ";// TRANSLATE
$l_we_tag['ifNotShopField']['description'] = "Everything between the start and end tags of this tag is displayed only if the value of the attribut \"match\" is not identical with the value of the shopField ";// TRANSLATE

$l_we_tag['shopVat']['description'] = "Tätä tagia käytetään alv.:in määrittelemiseksi tuotteelle. Hallitaksesi alv. -arvoja käytä Kauppa moduulia. Annettu id tulostaa suoraan alv.:n kyseiselle tuotteelle.";
$l_we_tag['showShopItemNumber']['description'] = "we:showShopItemNumber -tagi näyttää määritettyjen nimikkeiden määrän ostoskorissa.";
$l_we_tag['sidebar']['description'] = "";
$l_we_tag['sidebar']['defaultvalue'] = "Avaa sivupalkin";
$l_we_tag['subscribe']['description'] = "Tätä tagia käytetetään yksittäisen syöttökentän luomiseksi webEdition tiedostoon, jotta käyttäjä voi antaa sähköpostiosoitteensa tilatakseen uutiskirjeen.";
$l_we_tag['sum']['description'] = "we:sum -tagi yhdistää kaikki kohteet listalle.";
$l_we_tag['target']['description'] = "Tätä tagia käytetään linkin kohteen (target) valitsemiseksi &lt;we:linklist&gt; -tagista.";
$l_we_tag['textarea']['description'] = "we:textarea -tagi luo monirivisen sisällön syöttöalueen.";
$l_we_tag['title']['description'] = "we:title -tagi luo normaalin otsikko -tagin. Jos otsikkokenttä Ominaisuudet -välilehdellä on tyhjä, käytetään vakio otsikkoa, muutoin käytetään Ominaisuudet -välilehdellä määriteltyä otsikkoa.";
$l_we_tag['tr']['description'] = "&lt;we:tr&gt; -tagi vastaa HTML:n &lt;tr&gt; -tagia.";
$l_we_tag['tr']['defaultvalue'] = "";
$l_we_tag['unsubscribe']['description'] = "Tätä tagia käytetetään yksittäisen syöttökentän luomiseksi webEdition tiedostoon, jotta käyttäjä voi antaa sähköpostiosoitteensa lopettaakseen uutiskirjeen tilauksen.";
$l_we_tag['url']['description'] = "we:url -tagi luo sisäisen webEdition URL-osoitteen, joka osoittaa dokumenttiin, jolla on alla annettu id.";
$l_we_tag['userInput']['description'] = "we:userInput -tagi luo syöttökentät, joita voidaan käyttää we:form type=\"document\" tai type=\"object\" yhteydessä tiedostojen tai objektien luomiseksi.";
$l_we_tag['useShopVariant']['description'] = "we:shopVariant -tagi käyttää artikkelin toisinnon datan annetun nimen perusteella. Jos toisintoja annetulla nimellä ei ole, näytetään vakio artikkeli.";
$l_we_tag['var']['description'] = "we:var -tagi esittää alla annettuun nimeen liittyvän tiedosto-kentän globaalin php -muuttujan sisällön.";
$l_we_tag['voting']['description'] = "we:voting -tagia käytetään äänestyksen luomiseen.";
$l_we_tag['voting']['defaultvalue'] = "";
$l_we_tag['votingField']['description'] = "The we:votingField-tag is required to display the content of a voting. The attribute \"name\" defines what to show. The attribute \"type\", how to display it. Valid name-type combinations are: question - text; result - count, percent, total; id - answer, select, radio, voting; answer - text,radio,checkbox (select multiple) select, textinput and textarea (free text answer field), image (all we:img attributes such as thumbnail are supported), media (delivers the path utilizing to and nameto); ";// TRANSLATE
$l_we_tag['votingList']['description'] = "Tämä tagi luo automaattisesti äänestyslistat.";
$l_we_tag['votingList']['defaultvalue'] = "";
$l_we_tag['votingSelect']['description'] = "Käytä tätä tagia luodaksesi alasvetovalikon; (&lt;select&gt;) äänestyksen valintaan.";
$l_we_tag['write']['description'] = "Tämä tagi tallentaa tiedoston tai objektin, jonka &lt;we:form type=\"document/object&gt; on luonut";
$l_we_tag['writeShopData']['description'] = "we:writeShopData tagi tallentaa kaikki sen hetkisten ostoskorien sisällöt tietokantaan.";
$l_we_tag['writeVoting']['description'] = "Tagi tallentaa äänestyksen tietokantaan. Jos \"id\" on määritelty, vain äänestys, jolla on kyseinen id tallennetaan.";
$l_we_tag['xmlfeed']['description'] = "Tagi lataa xml sisällön annetusta url-osoitteesta";
$l_we_tag['xmlnode']['description'] = "Tagi tulostaa xml-elementin annetusta syötteestä tai url-osoitteesta.";
$l_we_tag['xmlnode']['defaultvalue'] = "";
$l_we_tag['ifbannerexists']['description'] = "Executes the enclosed code only, if the banner module is not deaktivated (settings dialog)."; // TRANSLATE
$l_we_tag['ifbannerexists']['defaultvalue'] = "";
$l_we_tag['ifcustomerexists']['description'] = "Executes the enclosed code only, if the customer module is not deaktivated (settings dialog)."; // TRANSLATE
$l_we_tag['ifcustomerexists']['defaultvalue'] = "";
$l_we_tag['ifnewsletterexists']['description'] = "Executes the enclosed code only, if the newsletter module is not deaktivated (settings dialog)."; // TRANSLATE
$l_we_tag['ifnewsletterexists']['defaultvalue'] = "";
$l_we_tag['ifobjektexists']['description'] = "Executes the enclosed code only, if the object module is not deaktivated (settings dialog)."; // TRANSLATE
$l_we_tag['ifobjektexists']['defaultvalue'] = "";
$l_we_tag['ifshopexists']['description'] = "Executes the enclosed code only, if the shop module is not deaktivated (settings dialog)."; // TRANSLATE
$l_we_tag['ifshopexists']['defaultvalue'] = "";
$l_we_tag['ifvotingexists']['description'] = "Executes the enclosed code only, if the voting module is not deaktivated (settings dialog)."; // TRANSLATE
$l_we_tag['ifvotingexists']['defaultvalue'] = "";

$l_we_tag['ifNotHasChildren']['description'] = "Within the &lt;we:repeat&gt; tag &lt;we:ifNotHasChildren&gt; is used to query if a category(folder) has child categories."; // TRANSLATE
$l_we_tag['ifNotHasChildren']['defaultvalue'] = "";
$l_we_tag['ifNotHasCurrentEntry']['description'] = "we:ifNotHasCurrentEntry can be used within we:navigationEntry type=\"folder\" to show some content, only if the navigation folder does not contain the activ entry"; // TRANSLATE
$l_we_tag['ifNotHasCurrentEntry']['defaultvalue'] = "";
$l_we_tag['ifNotHasEntries']['description'] = "we:ifNotHasEntries can be used within we:navigationEntry to show content only, if the navigation entry does not contain entries."; // TRANSLATE
$l_we_tag['ifNotHasEntries']['defaultvalue'] = "";
$l_we_tag['ifNotHasShopVariants']['description'] = "The tag we:ifHasShopVariants can display content depending on the existance of variants in an object or document. With this, it can be controlled whether a &lt;we:listview type=\"shopVariant\"&gt; should be displayed at all or some alternative."; // TRANSLATE
$l_we_tag['ifNotHasShopVariants']['defaultvalue'] = "";
$l_we_tag['ifPageLanguage']['description'] = "The tag we:ifPageLanguage tests on the language setting in the properties tab of the document, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages"; // TRANSLATE
$l_we_tag['ifPageLanguage']['defaultvalue'] = "";
$l_we_tag['ifNotPageLanguage']['description'] = "The tag we:ifNotPageLanguage tests on the language setting in the properties tab of the document, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages";// TRANSLATE
$l_we_tag['ifNotPageLanguage']['defaultvalue'] = "";
$l_we_tag['ifObjectLanguage']['description'] = "The tag we:ifObjectLanguage tests on the language setting in the properties tab of the object, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages";// TRANSLATE
$l_we_tag['ifObjectLanguage']['defaultvalue'] = "";
$l_we_tag['ifNotObjectLanguage']['description'] = "The tag we:ifNotObjectLanguage tests on the language setting in the properties tab of the object, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages";// TRANSLATE
$l_we_tag['ifNotObjectLanguage']['defaultvalue'] = "";
$l_we_tag['ifSendMail']['description'] = "Checks if a page is currently sent by we:sendMail and allows to exclude or include contents to the sent page"; // TRANSLATE
$l_we_tag['ifSendMail']['defaultvalue'] = "";
$l_we_tag['ifNotSendMail']['description'] = "Checks if a page is currently sent by we:sendMail and allows to exclude or include contents to the sent page"; // TRANSLATE
$l_we_tag['ifNotSendMail']['defaultvalue'] = "";

$l_we_tag['ifNotVoteActive']['description'] = "Any content between the start- and endtag is only displayed, if the voting has expired.";// TRANSLATE
$l_we_tag['ifNotVoteActive']['defaultvalue'] = "";
$l_we_tag['ifNotVoteIsRequired']['description'] = "Any content between the start- and endtag is only displayed, if the voting ist not required to be filled out.";// TRANSLATE
$l_we_tag['ifNotVoteIsRequired']['defaultvalue'] = "";
$l_we_tag['ifVoteIsRequired']['description'] = "Any content between the start- and endtag is only displayed, if the voting is a required field.";// TRANSLATE
$l_we_tag['ifVoteIsRequired']['defaultvalue'] = "";

$l_we_tag['pageLanguage']['description'] = "Shows the language of the document";// TRANSLATE
$l_we_tag['pageLanguage']['defaultvalue'] = "";
$l_we_tag['objectLanguage']['description'] = "Shows the language of the object";// TRANSLATE
$l_we_tag['objectLanguage']['defaultvalue'] = "";
$l_we_tag['order']['description'] = "Using this tag, one can display an order on a webEdition page. Similar to the Listview or the <we:object> tag, the fields are displayed with the <we:field> tag.";// TRANSLATE
$l_we_tag['order']['defaultvalue'] = "";
$l_we_tag['orderitem']['description'] = "Using this tag, one can display a single item on an order on a webEdition page. Similar to the Listview or the <we:object> tag, the fields are displayed with the <we:field> tag.";// TRANSLATE
$l_we_tag['orderitem']['defaultvalue'] = "";


$l_we_tag['ifVotingField']['description'] = "Checks if a votingField has a value corresponding to the attribute match, the attribute combinations of name and type are the same as in the we:votingField tag";// TRANSLATE
$l_we_tag['ifNotVotingField']['description'] = "Checks if a votingField has not a value corresponding to the attribute match, the attribute combinations of name and type are the same as in the we:votingField tag";// TRANSLATE
$l_we_tag['ifVotingFieldEmpty']['description'] = "Checks if a votingField is empty, the attribute combinations of name and type are the same as in the we:votingField tag";// TRANSLATE
$l_we_tag['ifVotingFieldNotEmpty']['description'] = "Checks if a votingField is not empty, the attribute combinations of name and type are the same as in the we:votingField tag";// TRANSLATE
$l_we_tag['ifVotingIsRequired']['description'] = "Prints the enclosed content only, if the voting field is a required field";// TRANSLATE
$l_we_tag['ifNotVotingIsRequired']['description'] = "Prints the enclosed content only, if the voting field is a required field";// TRANSLATE
$l_we_tag['votingSession']['description'] = "Generates an unique identifier which is stored in the voting log and allows to identify the answers to different questions which belong to a singele voting session";// TRANSLATE

?>