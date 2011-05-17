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

$l_we_tag['a']['description'] = "we:a tagi luon HTML-linkki tagin joka viittaa sis‰iseen, ID:ll‰ m‰‰ritelt‰v‰‰n webEdition dokumenttiin. Kaikki aloitus- ja lopetustagin v‰liin tuleva sis‰ltˆ toimii linkkin‰.".
$l_we_tag['a']['defaultvalue'] = "";
$l_we_tag['addDelNewsletterEmail']['description'] = "T‰t‰ tagia k‰ytet‰‰n ˆis‰‰m‰‰n tai poistamaan s‰hkˆpostiosoite uutiskirjeen tilaajalistalta. Attribuutissa \"path\" t‰ytyy antaa t‰ydellinen polku uutiskirjeen vastaanottajalistatiedostoon. Jos path alkaa ilman merkki‰ \"/\", lis‰t‰‰n annettu merkkijono DOCUMENT_ROOT arvoon. Jos k‰ytˆss‰ on useita listoja, voit antaa pathiin useita polkuja pilkkueroteltuna.";
$l_we_tag['addDelShopItem']['description'] = "K‰yt‰ we:addDelShopItem tagia lis‰t‰ksesi tai poistaaksesi tavaraa ostoskorista.";
$l_we_tag['addPercent']['description'] = "Tagi we:addPercent lis‰‰ arvoa m‰‰ritellyn prosenttim‰‰r‰n verran, esim. ALV:n verran.";
$l_we_tag['addPercent']['defaultvalue'] = ""; 
$l_we_tag['answers']['description'] = "Tagi n‰ytt‰‰ ‰‰nestyksen vastausvaihtoehdot.";
$l_we_tag['answers']['defaultvalue'] = ""; 
$l_we_tag['author']['description'] = "Tagi we:author n‰ytt‰‰ dokumentin luojan nimen. Jos attribuuttia 'type' ei ole m‰‰ritelty, n‰ytet‰‰n k‰ytt‰j‰tunnus. Jos type=\"name\", n‰ytet‰‰n k‰ytt‰j‰n etu- ja sukunimi. Jos nimi‰ ei ole m‰‰ritelty, n‰ytet‰‰n edelleen vain k‰ytt‰j‰tunnus.";
$l_we_tag['back']['description'] = "Tagi we:back tagi luo HTML-linkin joka viittaa we:listviewin edelliselle sivulle. Kaikki aloitus- ja lopetustagin v‰liin tuleva sis‰ltˆ toimii linkkin‰.";
$l_we_tag['back']['defaultvalue'] = ""; 
$l_we_tag['banner']['description'] = "K‰yt‰ we:banner tagia sis‰llytt‰‰ksesi bannerin Banneri Moduulista.";
$l_we_tag['bannerSelect']['description'] = "T‰m tagi n‰ytt‰‰ alasvatovalikon (&lt;select&gt;), jolla valita bannereita. Jos Asiakashallintamoduuli on asennettu ja attribuutti \"customer\" on asetettu, bannerit n‰ytet‰‰n vain kirjautuneille k‰ytt‰jille.";
$l_we_tag['bannerSum']['description'] = "Tagi we:bannerSum n‰ytt‰‰ kaikkien bannerin‰yttˆjen tai klikkausten summan. Tagi toimii vain listview type=\"banner\" sis‰ll‰.";
$l_we_tag['block']['description'] = "Tagi we:block tagi mahdollistaa laajennettavien blockien/listojen luonnin. Kaikki aloitus- ja lopetustagien v‰liin tuleva sis‰ltˆ (HTML-koodi, l‰hes kaikki we:tagit) lis‰t‰‰n sivulle plus-painikkeen painallukselle sivun muokkaustilassa.";
$l_we_tag['block']['defaultvalue'] = ""; 
$l_we_tag['calculate']['description'] = "we:calculate tagi mahdollistaa kaikkien PHP:n tarjoaminen matemaattisten operaatioiden k‰ytˆn, esim. *, /, +, -,(), sqrt..jne.";
$l_we_tag['calculate']['defaultvalue'] = ""; 
$l_we_tag['captcha']['description'] = "Tag generoi kuvan jossa on satunnainen koodi.";
$l_we_tag['category']['description'] = "we:category tagissa m‰‰ritellyt kategoriat korvataan kategorialla tai kategorioilla jotka m‰‰ritell‰‰n dokumentin Ominaisuudet- v‰lilehdell‰. Jos tagia k‰ytett‰ess‰ halutaan m‰‰ritell‰ useita kategorioita, ne t‰ytyy erotella pilkulla. Jos halutaan k‰ytt‰‰ muuta erotinta, t‰ytyy k‰ytett‰v‰ erotin m‰‰ritell‰ attribuutilla  \"tokken\.";
$l_we_tag['categorySelect']['description'] = "T‰t‰ tagia k‰ytt‰m‰ll‰ voidaan lis‰t‰ alasvetovalikko (&lt;select&gt;) webEdition dokumenttiin. M‰‰ritt‰m‰ll‰ lopetustagi heti aloitustagin j‰lkeen saadaan valikko n‰ytt‰m‰‰n kaikki webEditionin kategoriat.";
$l_we_tag['categorySelect']['defaultvalue'] = ""; 
$l_we_tag['charset']['description'] = "we:charset tagi luo HTML-metatagin joka m‰‰ritt‰‰ sivulla k‰ytett‰v‰n merkistˆkoodauksen. \"ISO-8859-1\" on yleens‰ k‰ytˆss‰ englannikielisill‰ sivuilla. T‰m‰ tagi on sijoitettavfa HTML-sivun head-osioon.";
$l_we_tag['charset']['defaultvalue'] = ""; 
$l_we_tag['checkForm']['description'] = "we:checkForm tagi luo JavaScript koodin jolla voi tarkistaa m‰‰ritellyn lomakkeen syˆtteet.<br/>Parametrien 'match' ja 'type' avulla m‰‰ritell‰‰n tarkistettavan lomakkeen 'name' tai 'id'.<br/>'mandatory' sis‰lt‰‰ pilkkuerotellun listan pakollisten kenttien nimist‰ ja 'email' sis‰lt‰‰ samaan malliin koostetun listan kentist‰ joiden aiotut syˆttet ovat tyypeilt‰‰n s‰hkˆpostiosoitteita. <br>Kent‰‰n 'password' on mahdollista kirjoittaa 2 kentt‰nime‰ joihin sovelletaan salasanatarkastusta, sek‰ kolmantena arvona numeerinen arvo joka m‰‰ritt‰‰ salasanan minimipituuden (esim: salasana,salasana2,5). <br>'onError' kohtaan voit m‰‰ritt‰‰ virhetilanteessa mahdollisesti kutsuttavan itse m‰‰rittelem‰si JavaScript -funktion nimen. T‰m‰ funktio saa parametrina taulukon josta lˆytyv‰t puuttuvien pakollisten kenttien nimet, ja 'flagin' siit‰ oliko salasanat oikein. Jos 'onError' j‰tet‰‰n m‰‰rittelem‰tt‰ tai funktiota ei ole lis‰tty sivupohjaan, n‰ytet‰‰n oletusarvot alert-ikkunassa.";
$l_we_tag['checkForm']['defaultvalue'] = ""; 
$l_we_tag['colorChooser']['description'] = "we:colorChooser tagi luo kontrollin jolla voidaan helposti valita v‰riarvo.";
$l_we_tag['condition']['description'] = "T‰t‰ tagia k‰ytet‰‰n yhdess‰ tagin &lt;we:conditionAdd&gt; kanssa kun halutaan dynaamisesti lis‰t‰ arvoja &lt;we:listview type=\"object\"&gt; attribuuttiin \"condition\". Ehdot voivat olla limitt‰isi‰.";
$l_we_tag['condition']['defaultvalue'] = "&lt;we:conditionAdd field=\"Type\" var=\"type\" compare=\"=\"/&gt;"; 
$l_we_tag['conditionAdd']['description'] = "Tagia k‰ytet‰‰n uuden ehdon tai s‰‰nnˆn lis‰‰miseen &lt;we:condition&gt; tagien sis‰ll‰.";
$l_we_tag['conditionAnd']['description'] = "Tagia k‰ytet‰‰n ehtojen lis‰‰miseen &lt;we:condition&gt; tagien sis‰ll‰. T‰m‰ on looginen operaattori AND, tarkoittaen sit‰ ett‰ molempien liitettyjen ehtojen tulee t‰ytty‰.";
$l_we_tag['conditionOr']['description'] = "Tagia k‰ytet‰‰n ehtojen lis‰‰miseen &lt;we:condition&gt; tagien sis‰ll‰. T‰m‰ on looginen operaattori OR, tarkoittaen ett‰ jomman kumman liitetyist‰ ehdoista tulee t‰ytty‰.";
$l_we_tag['content']['description'] = "&lt;we:content /&gt; k‰ytet‰‰n vain p‰‰sivupohjan sis‰ll‰ (mastertemplate). Se m‰‰rittelee paikan jonne p‰‰sivupohjaa k‰ytt‰v‰n muun sivupohjan sis‰ltˆ liitet‰‰n.";
$l_we_tag['controlElement']['description'] = "Tagia we:controlElement k‰ytet‰‰n dokumentin muokkaustilassa kontrollielementtien save, delete, publish jne. hallintaan. Painikkeita voidaan piilottaa, checkboxeja disabloida/rastittaa/piilottaa.";
$l_we_tag['cookie']['description'] = "T‰m‰ tagi on ‰‰nestysmoduulin vaatima ja se luo asiakaskoneelle ev‰steen joka est‰‰ useammat ‰‰nestyskerrat. Tagi t‰ytyy sijoittaa aivan sivupohjan alkuun (ts. mit‰‰n ei saa tulostaa ennen t‰t‰ tagia, ei edes v‰lilyˆntej‰ tai rivinvaihtoja).";
$l_we_tag['createShop']['description'] = "Tagia we:createShop tarvitaan kaikilla sivuilla joilla on tarkoitus tulostaa tietoja ostoksista.";
$l_we_tag['css']['description'] = "Css tagi luo HTML-tagin joka viittaa ID:ll‰ m‰‰riteltyyn webEditionin sis‰iseen CSS-tiedostoon.";
$l_we_tag['customer']['description'] = "Using this tag, data from any customer can be displayed. The customer data are displayed as in a listview or within the &lt;we:object&gt; tag with the tag &lt;we:field&gt;.<br /><br />Combining the attributes, this tag can be utilized in three ways:<br/>If name is set, the editor can select a customer by using a customer-select-Field. This customer is stored in the document within the field name.<br />If name is not set but instead the id, the customer with this id is displayed.<br />If neither name nor id is set, the tag expects the id of the customer by a request parameter. This is i.e. used by the customer-listview when the attribut hyperlink=\"true\" in the &lt;we:field&gt; tag is used. The name of the request parameter is we_cid.";
$l_we_tag['customer']['defaultvalue'] = "";
$l_we_tag['date']['description'] = "we:date tagi n‰ytt‰‰ kuluvan hetken p‰iv‰m‰‰r‰tiedot muodossa joka on m‰‰ritelty p‰iv‰m‰‰r‰n muotoilumerkkijonossa. Jos dokumentti on staattinen, tyyppi tulee asettaa muotoon &quot;js&quot;, jotta aika saadaan tulostettua JavaScriptill‰.";
$l_we_tag['dateSelect']['description'] = "we:dateSelect tagi tulostaa valintakent‰n p‰iv‰m‰‰r‰lle. T‰t‰ voidaan k‰ytt‰‰ yhdess‰ we:processDateSelect tagin kanssa jos halutaan lukea valittu arvo esim. muuttujaan joka on tyyppi‰ UNIX TIMESTAMP.";
$l_we_tag['delete']['description'] = "T‰ll‰ tagilla poistetaan dokumentteja joihin on menty &lt;we:a edit=\"document\" delete=\"true\"&gt; tai &lt;we:a edit=\"object\" delete=\"true\"&gt; kautta.";
$l_we_tag['deleteShop']['description'] = "we:deleteShop tagi poistaa koko ostoskorin.";
$l_we_tag['description']['description'] = "we:description tagi luo description- metatagin. Jos dokumentin kuvauskentt‰ Ominaisuudet- v‰lilehdell‰ on tyhj‰, k‰ytet‰‰n HTML-sivun koko sis‰ltˆ‰ kuvaustekstin‰.";
$l_we_tag['description']['defaultvalue'] = ""; 
$l_we_tag['DID']['description'] = "Tagi palauttaa webEdition dokumentin ID:n.";
$l_we_tag['docType']['description'] = "Tagi palauttaa webEdition dokumentin dokumenttityypin.";
$l_we_tag['else']['description'] = "T‰t‰ tagia k‰ytet‰‰n lis‰‰m‰‰n vaihtoehtoisia ehtohaaroja if-tyyppisten tagien sis‰lle. Esim.&lt;we:ifEditmode&gt;, &lt;we:ifNotVar&gt;, &lt;we:ifNotEmpty&gt;, &lt;we:ifFieldNotEmpty&gt;";
$l_we_tag['field']['description'] = "Tagi lis‰‰ \"name\" attribuutissa m‰‰ritellyn kent‰n sis‰llˆn k‰ytett‰ess‰ listviewi‰. Tagi toimii vain we:repeat tagien v‰liss‰.";
$l_we_tag['flashmovie']['description'] = "we:flashmovie tagi mahdollistaa Flash-esityksen lis‰‰misen sivun sis‰ltˆˆn. K‰ytett‰ess‰ t‰t‰ tagia dokumentin muokkaustilassa n‰ytet‰‰n tiedostoselaimen avaava esityksen valintapainike.";
$l_we_tag['form']['description'] = "we:form tagia k‰ytet‰‰n haku- ja mailiformien luontiin. Se toimii samaan tapaan kuin normaali HTML-lomakekin, mutta se antaa parserin lis‰t‰ tarvitsemiaan lis‰tietokentti‰ hidden muotoisena.";
$l_we_tag['form']['defaultvalue'] = ""; 
$l_we_tag['formfield']['description'] = "Tagia k‰ytet‰‰n lis‰tt‰ess‰ lomakekentti‰ front end lomakkeeseen.";
$l_we_tag['formmail']['description'] = "With activated Setting Call Formmail via webEdition document, the integration of the formmail script is realized with a webEdition document. For this, the (currently without attributes) we-Tag formmail will be used. <br />If the Captcha-check is used, &lt;we:formmail/&gt; is located within the we-Tag ifCaptcha."; 
$l_we_tag['hidden']['description'] = "we:hidden tagi luo piilotetun (hidden) kent‰n joka sis‰lt‰‰ saman nimisest‰ globaalista PHP-muuttujasta haetun muuttuja-arvon. K‰yt‰ t‰t‰ tagia kun haluat siirt‰‰ esim. lomakkeelta tulevia arvoja eteenp‰in.";
$l_we_tag['hidePages']['description'] = "we:hidePages mahdollistaa dokumentin tiettyjen v‰lilehtien piilottamisen webEditionin puolella. Voit esimerkiksi rajoittaa p‰‰sy‰ dokumentin Ominaisuudet- v‰lilehdelle.";
$l_we_tag['href']['description'] = "we:href tagi luo valinnan jolla voidaan m‰‰ritt‰‰ joko sis‰isen tai ulkoisen dokumentin URL dokumentin muokkaustilassa.";
$l_we_tag['icon']['description'] = "we:icon tagi luo HTML-tagin joka viitta webEditionin sis‰iseen ikonidokumenttiin we:tagille annetun ID:n perusteella. Ikonia k‰ytet‰‰n mm. selainten osoiterivill‰ ja kirjanmerkeiss‰.";
$l_we_tag['ifBack']['description'] = "Tagia k‰ytet‰‰n &lt;we:listview&gt; aloitus- ja lopetustagien v‰lill‰. we:back aloitus- ja lopetustagien sis‰‰n m‰‰ritelty sis‰ltˆ n‰ytet‰‰n vain jos listviewill‰ on olemassa edellinen sivu.";
$l_we_tag['ifBack']['defaultvalue'] = ""; 
$l_we_tag['ifCaptcha']['description'] = "T‰m‰n tagin sulkema sis‰ltˆ esitet‰‰n vain jos k‰ytt‰j‰n syˆtt‰m‰ koodi on oikein.";
$l_we_tag['ifCaptcha']['defaultvalue'] = ""; 
$l_we_tag['ifCat']['description'] = "we:ifCat tagia k‰ytet‰‰n rajaamaan n‰ytett‰vi‰ kategorioita. Categories-listalle lis‰t‰‰n n‰ytett‰v‰t kategoriat, joita verrataan dokumentin kategorioihin.";
$l_we_tag['ifCat']['defaultvalue'] = "";
$l_we_tag['ifNotCat']['description'] = "The we:ifNotCat tag ensures that everything located between the start tag and the end tag is only displayed if the categories which are entered under \"categories\" are none of the document's categories."; // TRANSLATE
$l_we_tag['ifNotCat']['defaultvalue'] = "";
$l_we_tag['ifClient']['description'] = "we:ifClient:n sis‰ll‰ oleva tieto n‰ytet‰‰n jos selain vastaa browser-kohtaan valittua selainta. Tagi toimii ainoastaan dynaamisilla sivuilla!";
$l_we_tag['ifClient']['defaultvalue'] = ""; 
$l_we_tag['ifConfirmFailed']['description'] = "Kun k‰ytet‰‰n DoubleOptIn tagia Newsletter moduulissa, niin we:ifConfirmFailed -tagi tarkastaa s‰hkˆpostiosoitteen oikeellisuuden.";
$l_we_tag['ifConfirmFailed']['defaultvalue'] = ""; 
$l_we_tag['ifCurrentDate']['description'] = "T‰m‰ tagi korostaa halutun p‰iv‰n kalenteri-listview:ss‰";
$l_we_tag['ifCurrentDate']['defaultvalue'] = ""; 
$l_we_tag['ifDeleted']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n jos dokumentti tai objekti poistettiin k‰ytt‰m‰ll‰ we:delete -tagia";
$l_we_tag['ifDeleted']['defaultvalue'] = ""; 
$l_we_tag['ifDoctype']['description'] = "Tagin sis‰ll‰ oleva tieto n‰ytet‰‰n jos dokumenttityyppi vastaa sivuston doctypeen.";
$l_we_tag['ifDoctype']['defaultvalue'] = ""; 
$l_we_tag['ifDoubleOptIn']['description'] = "Tagin sis‰ll‰ oleva tieto n‰ytet‰‰n ainoastaa double opt-in prosessin ensimm‰isess‰ vaiheessa.";
$l_we_tag['ifDoubleOptIn']['defaultvalue'] = ""; 
$l_we_tag['ifEditmode']['description'] = "Tagin sis‰ll‰ oleva tieto n‰ytet‰‰n ainoastaan editmodessa.";
$l_we_tag['ifEditmode']['defaultvalue'] = ""; 
$l_we_tag['ifEmailExists']['description'] = "Tagin sis‰ll‰ oleva tieto n‰ytet‰‰n ainoastaan jos m‰‰ritetty s‰hkˆpostiosoite lˆytyy uutiskirjeen osoitelistalta.";
$l_we_tag['ifEmailExists']['defaultvalue'] = ""; 
$l_we_tag['ifEmailInvalid']['description'] = "Tagin sis‰ll‰ oleva tieto n‰ytet‰‰n ainoastaan jos tietty s‰hkˆpostiosoiteen syntaksi on virheellinen.";
$l_we_tag['ifEmailInvalid']['defaultvalue'] = ""; 
$l_we_tag['ifEmailNotExists']['description'] = "Tagin sis‰ll‰ oleva tieto n‰ytet‰‰n ainoastaan jos kyseess‰oleva s‰hkˆpostiosoite ei ole uutiskirjeen osoitelistalla.";
$l_we_tag['ifEmailNotExists']['defaultvalue'] = ""; 
$l_we_tag['ifEmpty']['description'] = "Tagin sis‰ll‰ oleva tieto n‰ytet‰‰n ainoastaan jos kentt‰ on tyhj‰ jolla on sama nimi kuin match-arvona. Verrattavan kent‰n tyyppi t‰ytyy m‰‰ritt‰‰, 'img,flashmovie, href,object'";
$l_we_tag['ifEmpty']['defaultvalue'] = ""; 
$l_we_tag['ifEqual']['description'] = "we:ifEqual tagi vertaa kenttien sis‰ltˆ‰ 'name' ja 'eqname'. Jos sis‰ltˆ on molemmissa sama niin sis‰ltˆ n‰ytet‰‰n. Jos tagia k‰ytet‰‰n we:list:ss‰, we:block:ssa tai we:linklist:ss‰, vain yht‰ kentt‰‰ voidaan verrata. Jos attribuuttia 'value' k‰ytet‰‰n, 'eqname' hyl‰t‰‰n ja sillon sis‰ltˆ‰ verrataan 'value'-arvoon";
$l_we_tag['ifEqual']['defaultvalue'] = ""; 
$l_we_tag['ifFemale']['description'] = "Tagin sis‰ll‰ oleva tieto n‰ytet‰‰n ainoastaan jos k‰ytt‰j‰ on valinnut sukupuoleksi naisen.";
$l_we_tag['ifFemale']['defaultvalue'] = ""; 
$l_we_tag['ifField']['description'] = "Tagia k‰ytet‰‰n we:repeat -tagin sis‰ll‰. Kaikki sis‰ltˆ n‰ytet‰‰n jos attribuutin 'match' arvo on identtinen tietokannasta lˆytyv‰‰n kentt‰‰n joka on m‰‰ritetty listview:lle.";
$l_we_tag['ifField']['defaultvalue'] = ""; 
$l_we_tag['ifFieldEmpty']['description'] = "we:ifFieldEmpty varmistaa ett‰ kaikki tagin sis‰ll‰ oleva tieto n‰ytet‰‰n ainoastaan jos listview:n sis‰ll‰ oleva kentt‰ on tyhj‰ ja jonka nimi t‰sm‰‰ 'match'-arvoon. Kent‰n tyypin on m‰‰ritelt‰v‰.";
$l_we_tag['ifFieldEmpty']['defaultvalue'] = ""; 
$l_we_tag['ifFieldNotEmpty']['description'] = "we:ifFieldNotEmpty varmistaa ett‰ kaikki tagin sis‰ll‰ oleva tieto n‰ytet‰‰n ainoastaan jos listview:n sis‰ll‰ oleva kentt‰ ei ole tyhj‰ ja jonka nimi t‰sm‰‰ 'match'-arvoon. Kent‰n tyypin on m‰‰ritelt‰v‰.";
$l_we_tag['ifFieldNotEmpty']['defaultvalue'] = ""; 
$l_we_tag['ifFound']['description'] = "Tagin sis‰ll‰ oleva tieto n‰ytet‰‰n jos listview:ll‰ on hakutuloksia";
$l_we_tag['ifFound']['defaultvalue'] = ""; 
$l_we_tag['ifHasChildren']['description'] = "we:repeat -tagin sis‰ll‰ we:ifHasChildren:i‰ k‰ytet‰‰n alikansioiden tarkistukseen, jos niit‰ lˆytyy ne tulostetaan";
$l_we_tag['ifHasChildren']['defaultvalue'] = ""; 
$l_we_tag['ifHasCurrentEntry']['description'] = "we:ifHasCurrentEntry:‰ voidaan k‰ytt‰‰ we:navigationEntry type='folder':n sis‰ll‰ n‰ytt‰‰kseen aktiivista sis‰ltˆ‰";
$l_we_tag['ifHasCurrentEntry']['defaultvalue'] = ""; 
$l_we_tag['ifHasEntries']['description'] = "we:ifHasEntries:i‰ voidaan k‰ytt‰‰ tulostaakseen we:nagigationEntry:n mahdolliset alikansiot";
$l_we_tag['ifHasEntries']['defaultvalue'] = ""; 
$l_we_tag['ifHasShopVariants']['description'] = "we:ifHasShopVariants voi n‰ytt‰‰ sis‰ltˆ‰ riippuen muuttujien olemassaolosta objektissa tai dokumentissa. Voidaan kontrolloida we:listview type='shopVariant'. <b>This tag works in document and object templates attached by object-workspaces, but not inside the we:listview and we:object - tags</b>";// TRANSLATE
$l_we_tag['ifHasShopVariants']['defaultvalue'] = ""; 
$l_we_tag['ifHtmlMail']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n vain jos uutiskirjeen formaatti on HTML.";
$l_we_tag['ifHtmlMail']['defaultvalue'] = ""; 
$l_we_tag['ifIsDomain']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n jos palvelimen domain -nimi on sama kuin 'domain' -arvo. Sis‰llˆn voi n‰hd‰ ainoastaan valmiissa sivussa tai esikatselussa. Muokkaustilassa t‰t‰ tagia ei oteta huomioon.";
$l_we_tag['ifIsDomain']['defaultvalue'] = ""; 
$l_we_tag['ifIsNotDomain']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n jos palvelimen domain -nimi ei ole sama kuin 'domain' -arvo. Sis‰llˆn voi n‰hd‰ ainoastaan valmiissa sivussa tai esikatselussa. Muokkaustilassa t‰t‰ tagia ei oteta huomioon..";
$l_we_tag['ifIsNotDomain']['defaultvalue'] = ""; 
$l_we_tag['ifLastCol']['description'] = "T‰m‰n tagi havaitsee taulukosta rivin viimeisen viimeisen sarakkeen, kun k‰ytet‰‰n we:listview:n taulukkofunktioiden kanssa;";
$l_we_tag['ifLastCol']['defaultvalue'] = ""; 
$l_we_tag['ifLoginFailed']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain jos sis‰‰nkirjautuminen ep‰onnistui.";
$l_we_tag['ifLoginFailed']['defaultvalue'] = ""; 
$l_we_tag['ifLogin']['description'] = "Content enclosed by this tag is only displayed if a login occured on the page and allows for initialisation.";// TRANSLATE
$l_we_tag['ifLogin']['defaultvalue'] = "";
$l_we_tag['ifLogout']['description'] = "Content enclosed by this tag is only displayed if a logout occured on the page and allows for cleaning up.";// TRANSLATE
$l_we_tag['ifLogout']['defaultvalue'] = "";
$l_we_tag['ifTdEmpty']['description'] = "Content enclosed by this tag is only displayed if a table cell is empty (has no contents in a listview).";// TRANSLATE
$l_we_tag['ifTdEmpty']['defaultvalue'] = "";
$l_we_tag['ifTdNotEmpty']['description'] = "Content enclosed by this tag is only displayed if a table cell is not empty (has contents in a listview).";// TRANSLATE
$l_we_tag['ifTdNotEmpty']['defaultvalue'] = "";
$l_we_tag['ifMailingListEmpty']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain jos k‰ytt‰j‰ ei ole valinnut yht‰‰n uutiskirjett‰.";
$l_we_tag['ifMailingListEmpty']['defaultvalue'] = ""; 
$l_we_tag['ifMale']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain jos k‰ytt‰j‰ on mies. T‰t‰ tagia k‰ytet‰‰n uutiskirjeiden k‰yttjien sukupuolen tunnistuksessa."; 
$l_we_tag['ifMale']['defaultvalue'] = ""; 
$l_we_tag['ifNew']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain uudessa webEdition dokumentissa tai objektissa.";
$l_we_tag['ifNew']['defaultvalue'] = ""; 
$l_we_tag['ifNewsletterSalutationEmpty']['description'] = "Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is empty.";// TRANSLATE
$l_we_tag['ifNewsletterSalutationEmpty']['defaultvalue'] = "";
$l_we_tag['ifNewsletterSalutationNotEmpty']['description'] = "Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is not empty.";// TRANSLATE
$l_we_tag['ifNewsletterSalutationNotEmpty']['defaultvalue'] = "";
$l_we_tag['ifNotNewsletterSalutation']['description'] = "Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is not equal to match.";// TRANSLATE
$l_we_tag['ifNotNewsletterSalutation']['defaultvalue'] = "";
$l_we_tag['ifNext']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain jos 'Seuraavat' -objekteja on saatavilla";
$l_we_tag['ifNext']['defaultvalue'] = ""; 
$l_we_tag['ifNoJavaScript']['description'] = "T‰m‰ tagi uudelleenohjaa sivun toiselle sivulle ID:n perusteella jos selaimessa ei ole tukea JavaScript:lle tai jos JavaScript on pois p‰‰lt‰. T‰t‰ tagia voidaan k‰ytt‰‰ ainoastaan templaten head-osiossa..";
$l_we_tag['ifNotCaptcha']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain jos k‰ytt‰j‰n syˆtt‰m‰ koodi ei ole oikein.";
$l_we_tag['ifNotCaptcha']['defaultvalue'] = ""; 
$l_we_tag['ifNotDeleted']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain jos webEdition dokumenttia tai objektia ei voitu poistaa we:delete -tagilla";
$l_we_tag['ifNotDeleted']['defaultvalue'] = "";
$l_we_tag['ifNotDoctype']['description'] = "";
$l_we_tag['ifNotDoctype']['defaultvalue'] = "";
$l_we_tag['ifNotEditmode']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain jos ei olla sivun muokkaustilassa";
$l_we_tag['ifNotEditmode']['defaultvalue'] = ""; 
$l_we_tag['ifNotEmpty']['description'] = "T‰m‰ tagi varmistaa ett‰ t‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos sen sis‰ll‰ olevan we-tagin nimi vastaa 'match' atribuutin arvoon EIKƒ se ole tyhj‰. Tyyppi t‰ytyy m‰‰ritt‰‰.";
$l_we_tag['ifNotEmpty']['defaultvalue'] = ""; 
$l_we_tag['ifNotEqual']['description'] = "T‰m‰ tagi vertaa sis‰ll‰olevan we-tagin nimi atribuuttia 'eqname':n arvoon, jos se eiv‰t ole samat, sis‰ltˆ n‰ytet‰‰n. Jos attribuuttia 'value' k‰ytet‰‰n, 'eqname' hyl‰t‰‰n ja sillon sis‰ltˆ‰ verrataan 'value'-arvoon ";
$l_we_tag['ifNotEqual']['defaultvalue'] = ""; 
$l_we_tag['ifNotField']['description'] = "Tagia k‰ytet‰‰n we:repeat -tagin sis‰ll‰. Kaikki sis‰ltˆ n‰ytet‰‰n jos attribuutin 'match' arvo ei ole identtinen tietokannasta lˆytyv‰‰n kentt‰‰n joka on m‰‰ritetty listview:lle.";
$l_we_tag['ifNotField']['defaultvalue'] = ""; 
$l_we_tag['ifNotFound']['description'] = "Tagin sis‰ll‰ oleva tieto n‰ytet‰‰n jos listview:ll‰ ei ole hakutuloksia";
$l_we_tag['ifNotFound']['defaultvalue'] = ""; 
$l_we_tag['ifNotHtmlMail']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n vain jos uutiskirjeen formaatti ei ole HTML.";
$l_we_tag['ifNotHtmlMail']['defaultvalue'] = ""; 
$l_we_tag['ifNotNew']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain uudessa webEdition dokumentissa tai objektissa.";
$l_we_tag['ifNotNew']['defaultvalue'] = ""; 
$l_we_tag['ifNotObject']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n vain jos listview:n sis‰ltˆ ei ole objekti. Listviewin tyyppi t‰ytyy olla 'search';";
$l_we_tag['ifNotObject']['defaultvalue'] = ""; 
$l_we_tag['ifNotPosition']['description'] = "T‰m‰ tagi mahdollistaa toiminnon m‰‰rittelyn mit‰ EI tehd‰ tietyss‰ block:n, listview:n, linklist:n listdir:n kohdassa. Parametri 'position' hyv‰ksyy monipuolisia arvoja, 'first','last','all even','all odd', tai numeerisen m‰‰rittely (1,2,3...). Tyyppin‰ t‰ytyy olla block tai linklist ja nimi sill‰.";
$l_we_tag['ifNotPosition']['defaultvalue'] = ""; 
$l_we_tag['ifNotRegisteredUser']['description'] = "Tarkistaa onko k‰ytt‰j‰ rekisterˆitynyt.";
$l_we_tag['ifNotRegisteredUser']['defaultvalue'] = ""; 
$l_we_tag['ifNotReturnPage']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain luonnin tai muokkauksen j‰lkeen mik‰li paluuarvo we:a edit='true' on ep‰tosi tai ei m‰‰ritetty.";
$l_we_tag['ifNotReturnPage']['defaultvalue'] = ""; 
$l_we_tag['ifNotSearch']['description'] = "Tagin sis‰ltˆ tulostetaan vain jos hakutermi‰ ei l‰hetetty we:search:lt‰ tai se oli tyhj‰. Jos attribuutti 'set' = tosi, vain we:search:n pyydetyt muuttujat on varmennettu asettamattomiksi.";
$l_we_tag['ifNotSearch']['defaultvalue'] = ""; 
$l_we_tag['ifNotSeeMode']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n vain seeMode:n ulkopuolelle.";
$l_we_tag['ifNotSeeMode']['defaultvalue'] = ""; 
$l_we_tag['ifNotSelf']['description'] = "Mit‰‰n tietoa t‰m‰n tagin sis‰ll‰ ei n‰ytet‰ jos dokumentilla on yksik‰‰n tagiin syˆtetyist‰ ID:st‰. Jos tagi ei sijaitse we:linklist:n, we:listdir:n sis‰ll‰ 'id' on pakollinen kentt‰!";
$l_we_tag['ifNotSelf']['defaultvalue'] = ""; 
$l_we_tag['ifNotSidebar']['description'] = "This tag is used to display the enclosed contents only if the opened document is not located within the Sidebar."; // TRANSLATE
$l_we_tag['ifNotSidebar']['defaultvalue'] = ""; 
$l_we_tag['ifNotSubscribe']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain jos tilaus ep‰onnistui. T‰m‰ tagi pit‰isi olla 'Tilaa uutiskirje' templatessa we:addDelNewsletterEmail -tagin j‰lkeen.";
$l_we_tag['ifNotSubscribe']['defaultvalue'] = "";
$l_we_tag['ifNotTemplate']['description'] = "N‰yt‰ sis‰ltyv‰ tieto vain, jos nykyinen dokumentti ei perustu annettuun sivupohjaan.<br /><br />Lˆyd‰t lis‰tietoja we:ifTemplate -tagin referenssist‰.";
$l_we_tag['ifNotTemplate']['defaultvalue'] = "";
$l_we_tag['ifNotTop']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n vain jos se sijaitsee 'include' -dokumentissa.";
$l_we_tag['ifNotTop']['defaultvalue'] = ""; 
$l_we_tag['ifNotUnsubscribe']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n vain jos pyyntˆ ei toimi. T‰m‰ tagi pit‰isi olla 'Peru uutiskirjetilaus' templatessa we:addDelNewsletterEmail -tagin j‰lkeen.";
$l_we_tag['ifNotUnsubscribe']['defaultvalue'] = ""; 
$l_we_tag['ifNotVar']['description'] = "T‰m‰n tagin sis‰ll‰ olevaa tietoa ei n‰ytet‰ jos muuttujan 'name' -arvo on sama kuin 'match' -arvo. Muuttujan tyyppi voidaan m‰‰ritt‰‰ 'type'-attribuutilla";
$l_we_tag['ifNotVar']['defaultvalue'] = ""; 
$l_we_tag['ifNotVarSet']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n jos muuttujaa nimell‰ 'name' ei ole m‰‰ritetty. Huom! 'Ei asetettu' ei ole sama kuin tyhj‰!";
$l_we_tag['ifNotVarSet']['defaultvalue'] = ""; 
$l_we_tag['ifNotVote']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n jos ‰‰nestyst‰ ei tallennettu. 'type' -attribuutti m‰‰ritt‰‰ virheen tyypin.";
$l_we_tag['ifNotVote']['defaultvalue'] = ""; 
$l_we_tag['ifNotWebEdition']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n vain webEditionin ulkopuolelle.";
$l_we_tag['ifNotWebEdition']['defaultvalue'] = ""; 
$l_we_tag['ifNotWorkspace']['description'] = "Tarkastaa, sijaitseeko dokumentti jossain muualla kuin tyˆtilassa joka on m‰‰ritelty 'path' attribuutissa";
$l_we_tag['ifNotWorkspace']['defaultvalue'] = ""; 
$l_we_tag['ifNotWritten']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n vain jos tapahtuu virhe dokumentin tai objektin tallennusvaiheessa k‰ytt‰en we:write -tagia.";
$l_we_tag['ifNotWritten']['defaultvalue'] = ""; 
$l_we_tag['ifObject']['description'] = "T‰m‰n tagin sis‰ll‰ oleva tieto n‰ytet‰‰n jos lˆydettiin yksilˆllinen rivi we:listview type='search':lla joka on objekti.";
$l_we_tag['ifObject']['defaultvalue'] = ""; 
$l_we_tag['ifPosition']['description'] = "T‰m‰ tagi mahdollistaa toiminnon m‰‰rittelyn mit‰ tehd‰‰n tietyss‰ block:n, listview:n, linklist:n listdir:n kohdassa. Parametri 'position' hyv‰ksyy monipuolisia arvoja, 'first','last','all even','all odd', tai numeerisen m‰‰rittely (1,2,3...). Tyyppin‰ t‰ytyy olla block tai linklist ja nimi sill‰.";
$l_we_tag['ifPosition']['defaultvalue'] = ""; 
$l_we_tag['ifRegisteredUser']['description'] = "Tarkastaa, jos k‰ytt‰j‰ on rekisterˆitynyt.";
$l_we_tag['ifRegisteredUser']['defaultvalue'] = ""; 
$l_we_tag['ifRegisteredUserCanChange']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n vain jos rekisterˆitynyt k‰ytt‰j‰, joka on kirjautuneena sis‰‰n on oikeutettu muokkaamaan t‰m‰nhetkist‰ webEdition documenttia tai objektia.";
$l_we_tag['ifRegisteredUserCanChange']['defaultvalue'] = ""; 
$l_we_tag['ifReturnPage']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n webEdition dokumentin tai objektin luonnin tai muokkauksen j‰lkeen ja palautettava arvo 'result' on we:a edit='document' tai we:a edit='object' on tosi.";
$l_we_tag['ifReturnPage']['defaultvalue'] = ""; 
$l_we_tag['ifSearch']['description'] = "Tagin sis‰ltˆ tulostetaan vain jos hakutermi l‰hetettiin we:search:lle ja se ei ole tyhj‰. Jos attribuutti 'set' = tosi, vain we:search:n pyydetyt muuttujat on varmennettu asettamattomiksi.";
$l_we_tag['ifSearch']['defaultvalue'] = ""; 
$l_we_tag['ifSeeMode']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n vain seeMode:ssa.";
$l_we_tag['ifSeeMode']['defaultvalue'] = ""; 
$l_we_tag['ifSelf']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos dokumentilla on sama ID kuin mik‰ on m‰‰ritetty t‰h‰n tagiin. Jos tagi ei ole we:linklist tai we:listdir:n sis‰ll‰, ID on pakollinen!";
$l_we_tag['ifSelf']['defaultvalue'] = ""; 
$l_we_tag['ifShopEmpty']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos ostoskori on tyhj‰.";
$l_we_tag['ifShopEmpty']['defaultvalue'] = ""; 
$l_we_tag['ifShopNotEmpty']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos ostoskori ei ole tyhj‰.";
$l_we_tag['ifShopNotEmpty']['defaultvalue'] = ""; 
$l_we_tag['ifShopPayVat']['description'] = "Tagin sis‰ltˆ n‰ytet‰‰n jos kirjautuneen henkilˆn tulee maksaa alv.";
$l_we_tag['ifShopPayVat']['defaultvalue'] = ""; 
$l_we_tag['ifShopVat']['description'] = "T‰m‰ tagi tarkistaa alv:n kyseisest‰ dokumentista / ostoskorista. ID mahdollistaa alv:n tarkistuksen tietyist‰ artikkeleista.";
$l_we_tag['ifShopVat']['defaultvalue'] = ""; 
$l_we_tag['ifSidebar']['description'] = "This tag is used to display the enclosed contents only if the opened document is located within the Sidebar."; // TRANSLATE
$l_we_tag['ifSidebar']['defaultvalue'] = ""; 
$l_we_tag['ifSubscribe']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos uutiskirjeen tilaus onnistui. Tagia tulee k‰ytt‰‰ uutiskirjeen tilaustemplatessa heti addDelnewsletterEmail -tagin j‰lkeen.";
$l_we_tag['ifSubscribe']['defaultvalue'] = "";
$l_we_tag['ifTemplate']['description'] = "";
$l_we_tag['ifTemplate']['defaultvalue'] = "";
$l_we_tag['ifTop']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos tagi ei ole liitetyss‰ (included) dokumentissa.";
$l_we_tag['ifTop']['defaultvalue'] = ""; 
$l_we_tag['ifUnsubscribe']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos uutiskirjeen tilauksen peruuttaminen onnistui. Tagia tulee k‰ytt‰‰ uutiskirjeen tilaustemplatessa heti addDellnewsletterEmail -tagin j‰lkeen.";
$l_we_tag['ifUnsubscribe']['defaultvalue'] = ""; 
$l_we_tag['ifUserInputEmpty']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n tietty input-kentt‰ on tyhj‰.";
$l_we_tag['ifUserInputEmpty']['defaultvalue'] = ""; 
$l_we_tag['ifUserInputNotEmpty']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n tietty input-kentt‰ ei ole tyhj‰.";
$l_we_tag['ifUserInputNotEmpty']['defaultvalue'] = ""; 
$l_we_tag['ifVar']['description'] = "T‰m‰ tagin sis‰ltˆ n‰ytet‰‰n jos toisen we-tagin name-muuttujan arvo on sama kuin t‰m‰n tagin match-muuttujan arvo. Muuttujan tyyppi voidaan m‰‰ritt‰‰.";
$l_we_tag['ifVar']['defaultvalue'] = ""; 
$l_we_tag['ifVarEmpty']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos muuttuja on tyhj‰ jolla on sama nimi kuin match-attribuuttiin on m‰‰ritelty.";
$l_we_tag['ifVarEmpty']['defaultvalue'] = ""; 
$l_we_tag['ifVarNotEmpty']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos muuttuja on ei ole tyhj‰ ja jolla on sama nimi kuin match-attribuuttiin on m‰‰ritelty.";
$l_we_tag['ifVarNotEmpty']['defaultvalue'] = ""; 
$l_we_tag['ifVarSet']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos kohde muutujaa ei ole asetettu. Huom! 'asetettu' ei ole sama kuin 'ei tyhj‰'";
$l_we_tag['ifVarSet']['defaultvalue'] = ""; 
$l_we_tag['ifVote']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos ‰‰nestys tallennettiin onnistuneesti.";
$l_we_tag['ifVote']['defaultvalue'] = ""; 
$l_we_tag['ifVoteActive']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n jos ‰‰nestyksen aika ei ole umpeutunut.";
$l_we_tag['ifVoteActive']['defaultvalue'] = ""; 
$l_we_tag['ifWebEdition']['description'] = "T‰m‰n tagin sis‰ltˆ n‰ytet‰‰n vain webEditionin sis‰ll‰, mutta ei julkaistussa dokumentissa.";
$l_we_tag['ifWebEdition']['defaultvalue'] = ""; 
$l_we_tag['ifWorkspace']['description'] = "Tarkistaa, sijaitseeko dokumentti tyˆtilassa joka on m‰‰ritelty 'path' -attribuutissa.";
$l_we_tag['ifWorkspace']['defaultvalue'] = ""; 
$l_we_tag['ifWritten']['description'] = "T‰m‰n tagin sis‰ltˆ on k‰ytett‰viss‰ vian jos kirjoitus dokumenttiin tai objektiin onnisui. kts. we:write -tagi.";
$l_we_tag['ifWritten']['defaultvalue'] = ""; 
$l_we_tag['img']['description'] = "we:img tagilla voidaan lis‰t‰ kuva dokumentin muokkaus-tilassa. Jos mit‰‰n attribuutteja ei m‰‰ritet‰, k‰ytet‰‰n oletusarvoja. 'showimage':lla kuva voidaan piilottaa muokkaus-tilassa. 'showinputs':lla kuvan title- ja alt- attribuutit on pois k‰ytˆst‰..";
$l_we_tag['include']['description'] = "T‰ll‰ tagilla voidaan liitt‰‰ webEdition dokumentti tai HTML-sivu sivupohjaan. 'gethttp':ll‰ voidaan m‰‰ritt‰‰ halutaanko liitetty tiedosto siirt‰‰ HTTP:n avulla vai ei.'seeMode':lla m‰‰ritell‰‰n onko dokumentti muokattavissa 'seeModessa'.";
$l_we_tag['input']['description'] = "The we:input tag creates a single-line input box in the edit mode of the document based on this template, if the type = \"text\" is selected. For all other types, see the manual or help."; 
$l_we_tag['js']['description'] = "we:js tagi luo HTML-tagin joka viittaa webEditionin sis‰iseen JavaScript-dokumenttiin jonka ID on m‰‰ritelty listaan. JavaScriptit voi m‰‰ritt‰‰ myˆs erillisess‰ tiedostossa..";
$l_we_tag['keywords']['description'] = "we:keywords -tagi luo avainsana -metatagin.  Jos 'Ominaisuus' avainsana -kentt‰ on tyhj‰, t‰m‰n tagin sis‰ll‰ olevia sanoja k‰ytet‰‰n avainsanoina. Muuten k‰ytet‰‰n 'Ominaisuus':ssa m‰‰riteltyj‰ avainsanoja.";
$l_we_tag['link']['description'] = "we:link -tagi luo yksitt‰isen linkin jota voidaan muokata 'muokkaa'-napilla. Jos we:link:i‰ k‰ytet‰‰n we:linklist:n sis‰ll‰ 'nimi'-attribuuttia ei tule m‰‰ritell‰ we:link-tagiin, muutoin kyll‰. 'only' -attribuuttiin voidaan m‰‰ritell‰ attribuutti jonka linkki palauttaa, esim. 'only=\"content\"'.";
$l_we_tag['linklist']['description'] = "we:linklist -tagilla luodaan linkkilista. we:prelink -tagin sis‰ltˆ tulostetaan ennen linkki‰ muokkaustilassa..";
$l_we_tag['linklist']['defaultvalue'] = "&lt;we:link /&gt;&lt;we:postlink&gt;&lt;br /&gt;&lt;/we:postlink&gt;"; 
$l_we_tag['linkToSeeMode']['description'] = "T‰m‰ tagi luo linkin joka avautuu valittuun dokumenttiin 'seeMode':ssa.";
$l_we_tag['list']['description'] = "we:list -tagilla voit tehd‰ laajennettavia listoja. Tagien sis‰ll‰ oleva tieto liitet‰‰n listaan.";
$l_we_tag['list']['defaultvalue'] = ""; 
$l_we_tag['listdir']['description'] = "we:listdir -tagi luo listan joka n‰ytt‰‰ kaikki dokumentit jotka ovat samassa kansiossa. 'field' -attribuutilla voidaan m‰‰ritell‰ mink‰ kent‰n arvo n‰ytet‰‰n. Jos attribuutti on tyhj‰ tai ei ole olemassa, tiedoston nimi n‰ytet‰‰n. Mink‰ kent‰n halutaan n‰ytt‰v‰n kansioita tulee m‰‰ritt‰‰ attribuuttiin 'dirfield'. Jos attribuutti on tyhj‰ tai sit‰ ei ole olemassa, 'field'-kent‰n nimi on verrannollinen k‰ytetyn tiedoston nimeen. Jos k‰ytet‰‰n 'id'-attribuuttia, kansion tiedostot jossa on t‰m‰ sama id n‰ytet‰‰n.";
$l_we_tag['listdir']['defaultvalue'] = ""; 
$l_we_tag['listview']['description'] = "we:listview -tagilla luodaan listoja jotka generoidaan automaattisesti.";
$l_we_tag['listview']['defaultvalue'] = "&lt;we:repeat&gt;
&lt;we:field name=\"Title\" alt=\"we_path\" hyperlink=\"true\"/&gt;
&lt;br /&gt;
&lt;/we:repeat&gt;";
$l_we_tag['listviewEnd']['description'] = "T‰m‰ tagi n‰ytt‰‰ viimeisen rivin joka on we:listview:ll‰.";
$l_we_tag['listviewPageNr']['description'] = "T‰m‰ tagi palauttaa t‰m‰nhetkisen we:listview -sivun.";
$l_we_tag['listviewPages']['description'] = "T‰m‰ tagi palauttaa we:listview:n sivum‰‰r‰n.";
$l_we_tag['listviewRows']['description'] = "T‰m‰ tagi palauttaa lˆydettyjen rivien m‰‰r‰n we:listview:lt‰.";
$l_we_tag['listviewStart']['description'] = "T‰m‰ tagi n‰ytt‰‰ ensimm‰isen rivin joka on we:listview:ll‰.";
$l_we_tag['makeMail']['description'] = "T‰m‰ tagi pit‰‰ olla ensimm‰inen rivi jokaisessa sivupohjassa joka l‰hetet‰‰n we:sendMail:lla.";
$l_we_tag['master']['description'] = "";
$l_we_tag['master']['defaultvalue'] = "";
$l_we_tag['metadata']['description'] = "";
$l_we_tag['metadata']['defaultvalue'] = "&lt;we:field name=\"NameOfField\" /&gt;";
$l_we_tag['navigation']['description'] = "T‰t‰ tagia k‰ytet‰‰n alustamaan navigaatio joka luodaan navigaatio tyˆkalulla.";
$l_we_tag['navigationEntries']['description'] = "we:navigationEntry type='folder' tulostaa luotuja kansio-tyyppisi‰ navigaatiopisteit‰.";
$l_we_tag['navigationEntry']['description'] = "we:navigationEntry:ll‰ voidaan valita tulostetaanko 'folder' vai 'entry'-tyyppisi‰ navigaatioita. Lis‰attributeilla voidaan tarkentaa haluttua navigaatiotulostusta.";
$l_we_tag['navigationEntry']['defaultvalue'] = "&lt;a href=\"&lt;we:navigationField name=\"href\" /&gt;\"&gt;&lt;we:navigationField name=\"text\" /&gt;&lt;/a&gt;&lt;br /&gt;"; 
$l_we_tag['navigationField']['description'] = "&lt;we:navigationField&gt; is used within &lt;we:navigationEntry&gt; to print a value of the current navigation entry.<br/>Choose from <b>either</b> the attribute <i>name</i>, <b>or</b> from the attribute <i>attributes</i>, <b>or</b> from the attribute <i>complete</i>";// TRANSLATE
$l_we_tag['navigationWrite']['description'] = "Is used to write a we:navigation with given name"; 
$l_we_tag['newsletterConfirmLink']['description'] = "T‰t‰ tagia k‰ytet‰‰n 'double opt-in' vahvistuslinkin luomiseen.";
$l_we_tag['newsletterConfirmLink']['defaultvalue'] = "Todenna uutiskirje";
$l_we_tag['newsletterField']['description'] = "Displays a field from the recipient dataset within the newsletter."; // TRANSLATE
$l_we_tag['newsletterSalutation']['description'] = "T‰t‰ tagia k‰ytet‰‰n n‰ytett‰ess‰ 'puhuttelu''-kentti‰.";
$l_we_tag['newsletterUnsubscribeLink']['description'] = "Luo linkin uutiskirjeen perumiseen. Tagia voidaan k‰ytt‰‰ ainoastaan 's‰hkˆposti' sivupohjissa!";
$l_we_tag['next']['description'] = "Luo linkin joka viittaa seuraaviin sivuihin we:listview:ll‰.";
$l_we_tag['next']['defaultvalue'] = ""; 
$l_we_tag['noCache']['description'] = "PHP-koodi ajetaan aina kun v‰limuistissa olevaa dokumenttia kutsutaan.";
$l_we_tag['noCache']['defaultvalue'] = ""; 
$l_we_tag['object']['description'] = "we:object:lla n‰ytet‰‰n objekteja. Objektin kentti‰ voidaan n‰ytt‰‰ we:field -tagilla. Jos 'name'-attribuutti on m‰‰ritelty niin objektivalitsin n‰ytet‰‰n muokkaustilassa josta voi valita kaikki objektit kaikista luokista. Jos 'classid' on m‰‰ritelty objektivalitsimella voi valita kaikki objektit tietyst‰ luokasta. Pelk‰ll‰ 'id':ll‰ voidaan valita yksitt‰inen objekti..";
$l_we_tag['object']['defaultvalue'] = ""; 
$l_we_tag['pagelogger']['description'] = "The we:pagelogger tag generates, depending on the selected \"type\" attribute, the necessary capture code for pageLogger or the fileserver- respectively the download-code."; 
$l_we_tag['path']['description'] = "The we:path tag represents the path of the current document. If there is an index file in one of the subdirectories, a link is set on the respective directory. The used index files (separated by commas) can be specified in the attribute \"index\". If nothing is specified there, \"default.html\", \"index.htm\", \"index.php\", \"default. htm\", \"default.html\" and \"default.php\" are used as default settings. In the attribute \"home\" you can specify what to put at the very beginning. If nothing is specified, \"home\" is displayed automatically. The attribute separator describes the delimiter between the directories. If the attribute is empty, \"/\" is used as delimiter. The attribute \"field\" defines what sort of field (files, directories) is displayed. If the field is empty or non-existent, the filename will be displayed. The attribute \"dirfield\" defines which field is used for display in directories. If the field is empty or non-existent, the entry of \"field\" or the filename is used."; 
$l_we_tag['paypal']['description'] = "The tag we:paypal implements an interface to the payment provider paypal. To ensure that this tag works properly, add additional parameters in the backend of the shop module."; 
$l_we_tag['position']['description'] = "The tag we:position is used to return the actual position of a listview, block, linklist, listdir. Is \"type= block or linklist\" it is necessary to specify the name (reference) of the related block/linklist. The attribute \"format\" determines the format of the result."; 
$l_we_tag['postlink']['description'] = "we:postlink tagi varmistaa, ett‰ kaikki aloitus ja lopetus -tagien v‰lill‰ oleva sis‰ltˆ ei n‰y listan viimeisell‰ linkill‰.";
$l_we_tag['postlink']['defaultvalue'] = ""; 
$l_we_tag['prelink']['description'] = "we:prelink -tagi varmistaa, ett‰ kaikki aloitus ja lopetus -tagien v‰lill‰ oleva sis‰ltˆ ei n‰y linkkilistan ensimm‰isell‰ linkill‰.";
$l_we_tag['prelink']['defaultvalue'] = ""; 
$l_we_tag['printVersion']['description'] = "we:printVersion -tagi luo HTML linkin, joka osoittaa samaan tiedostoon mutta eri sivupohjaan. \"tid\" M‰‰ritt‰‰ sivupohjan id:n. Tagi linkitt‰‰ kaiken alku ja lopetus -tagien v‰liss‰ olevan sis‰llˆn.";
$l_we_tag['printVersion']['defaultvalue'] = ""; 
$l_we_tag['processDateSelect']['description'] = "&lt;we:processDateSelect&gt; -tagi prosessoi 3 arvoa we:dateSelect tagin valintakentist‰ UNIX timestamp -muotoiseksi. Arvo tallennetaan globaaliksi muuttujaksi, joka on nimetty kohtaan \"nimi&quuot;.";
$l_we_tag['quicktime']['description'] = "we:quicktime -tagilla voit lis‰t‰ Quicktime elokuvan tiedostoon. T‰h‰n sivupohjaan perustuvat tiedostot n‰ytt‰v‰t muokkausnapin muokkaustilassa. T‰m‰ napin klikkaaminen avaa tiedostohallinan, joka antaa sinun valita Quicktime elokuvan, jonka olet jo siirt‰ny webEditioniin. T‰ll‰ hetkell‰ ei ole xhtml-validia koodia, joka toimisi sek‰ IE:ss‰ ett‰ Mozillassa. T‰m‰nvuoksi, xml on aina asetettu arvoon \"ep‰tosi\"";
$l_we_tag['registeredUser']['description'] = "T‰m‰ tagi tulostaa asiakastiedot, jotka on tallennettu asiakashalllintamoduuliin.";
$l_we_tag['registerSwitch']['description'] = "T‰m‰ tagi luo kytkimen jolla voit vaihtaa rekisterˆityneen ja rekisterˆitym‰ttˆnm‰n k‰ytt‰j‰n statuksen v‰lill‰ muokkaustilassa. Jos olet k‰ytt‰nyt &lt;we:ifRegisteredUser&gt; ja &lt;we:ifNotRgisteredUser&gt; -tags, t‰m‰ tagi antaa sinun katsoa eri tiloja ja pit‰‰ sis‰llˆn muotoilu kunnossa.";
$l_we_tag['repeat']['description'] = "T‰m‰ tagin sis‰ltˆ toistetaan jokaiselle lˆydetylle kohdalle &lt;we:listview&gt; -tagissa. T‰t‰ tagia k‰ytet‰‰n vain &lt;we:listview&gt; yhteydess‰.";
$l_we_tag['repeat']['defaultvalue'] = ""; 
$l_we_tag['repeatShopItem']['description'] = "T‰m‰ tagi n‰ytt‰‰ kaiken ostoskorinsis‰llˆn.";
$l_we_tag['repeatShopItem']['defaultvalue'] = ""; 
$l_we_tag['returnPage']['description'] = "T‰t‰ tagia k‰ytet‰‰n l‰hdesivuun viittavan osoitteen n‰ytt‰miseen, jos \"palaa\" on asetettu arvoon \"tosi\" k‰ytett‰ess‰: &lt;we:a edit=\"document\"&gt; or &lt;we:a edit=\"object\"&gt;";
$l_we_tag['saferpay']['description'] = "we:saferpay implementoi rajapinnan saferpay maksuhallintaan. Varmistaaksesi tagin toiminnan, syˆt‰ lis‰tietoja Kauppamoduulin backendiin.";
$l_we_tag['saveRegisteredUser']['description'] = "T‰m‰ tagi tallentaa kaikki k‰ytt‰j‰tiedot, joita on syˆtetty istuntokenttiin.";
$l_we_tag['search']['description'] = "we:search -tagi luo syˆttˆkent‰n, jota k‰ytet‰‰n hakukentt‰n‰. Hakukent‰ll‰ on sis‰inen nimi \"we_search\". Kun hakulomake l‰hetet‰‰n, the PHP muuttuja \"we_search\" vastaanottavalla sivulla saa arvokseen hakukent‰n sis‰llˆn.";
$l_we_tag['select']['description'] = "we:select -tagi luo valintakent‰n muokkaustilaan. Jos kooksi on m‰‰ritelty \"1\" valintakentt‰‰ n‰ytet‰‰n pudotusvalikkona. Tagi toimii samoin kuin HTML select -tagi. Aloitus ja lopetus -tagien v‰liin syˆtett‰‰n normaalit HTML option -tagit.";
$l_we_tag['select']['defaultvalue'] = "&lt;option&gt;#1&lt;/option&gt;
&lt;option&gt;#2&lt;/option&gt;
&lt;option&gt;#3&lt;/option&gt;";
$l_we_tag['sendMail']['description'] = "T‰m‰ tagi l‰hett‰‰ webEdition sivun s‰hkˆpostina \"vastaanottaja\" kohtaan m‰‰riteltyyn osoitteeseen.";
$l_we_tag['sessionField']['description'] = "we:sessionField -tagi luo HTML input, select tai text area tagin. Sit‰ k‰ytet‰‰n sis‰llˆn syˆtt‰miseksi istuntokenttiin (esim. K‰ytt‰j‰tieto, jne.).";
$l_we_tag['sessionLogout']['description'] = "we:sessionLogout -tagi luo HTML linkki tagin joka osoittaa sis‰iseen webEdition tiedostoon, jolla on webEdition Tagi velhossa mainittu ID. Jos t‰ll‰ webEdition tiedostolla on we:sessionStart -tagi ja tiedosto on dynaaminen, aktiivinen istunto poistetaan ja suljetaan. Mit‰‰n tietoja ei tallenneta.";
$l_we_tag['sessionLogout']['defaultvalue'] = ""; 
$l_we_tag['sessionStart']['description'] = "T‰t‰ tagia k‰ytet‰‰n istunnon aloittamiseen tai aiemman istunnon jatkamiseen. T‰m‰ tagi vaaditaan sivupohjiin, jotka luovat seuraavan tyyppisi‰ sivuja: Sivuja, jotka on suojattu jollain tavoin K‰ytt‰j‰hallinta moduulilla, Kauppasivuja tai sivuja jotka tukevat front end input:ia.&lt;br /&gt;T‰m‰ tagin t‰ytyy olla ensimm‰inen tagi sivupohjan ensimm‰isell‰ rivill‰!";
$l_we_tag['setVar']['description'] = "T‰t‰ tagia k‰ytet‰‰n muuttujien arvojen asetukseen.<br/><strong>Attention:</strong> Without the attribute <strong>striptags=\"true\"</strong>, HTML- and PHP-Code is not filtered, this is a potenzial security risk!</strong>";// TRANSLATE
$l_we_tag['shipping']['description'] = "we:shipping -tagia k‰ytet‰‰n l‰hetyskulujen m‰‰rittelyyn. N‰m‰ kulut perustuvat ostoskorin arvoon, rekisterˆityneen k‰ytt‰j‰n kotimaahan ja l‰hetyskulujen m‰‰rittely s‰‰ntˆj‰ voidaan muokata Kauppa moduulissa. Parametriin \"summa\" m‰‰ritell‰‰n we:sum -tagin nimi. \"tyyppi\" -parametrilla m‰‰ritell‰‰n joko l‰hetyskulujen tyyppi.";
$l_we_tag['shopField']['description'] = "T‰m‰ tagi tallentaa useamman sis‰ltˆkent‰n suoraan tuotteesta tai ostoskorista. The p‰‰k‰ytt‰j‰ voi m‰‰ritt‰‰ tiettyj‰ arvoja joista k‰ytt‰j‰ voi valita tai syˆtt‰‰ omansa arvonsa. N‰in on mahdollista kartoittaa useampia toisintoja tuotteista helposti.";
$l_we_tag['ifShopFieldEmpty']['description'] = "Content enclosed by this tag is only displayed if the shopField named in attribute \"name\" is empty.";// TRANSLATE
$l_we_tag['ifShopFieldNotEmpty']['description'] = "Content enclosed by this tag is only displayed if the shopField named in attribute \"name\" is not empty.";// TRANSLATE
$l_we_tag['ifShopField']['description'] = "Everything between the start and end tags of this tag is displayed only if the value of the attribut \"match\" is identical with the value of the shopField ";// TRANSLATE
$l_we_tag['ifNotShopField']['description'] = "Everything between the start and end tags of this tag is displayed only if the value of the attribut \"match\" is not identical with the value of the shopField ";// TRANSLATE

$l_we_tag['shopVat']['description'] = "T‰t‰ tagia k‰ytet‰‰n alv.:in m‰‰rittelemiseksi tuotteelle. Hallitaksesi alv. -arvoja k‰yt‰ Kauppa moduulia. Annettu id tulostaa suoraan alv.:n kyseiselle tuotteelle.";
$l_we_tag['showShopItemNumber']['description'] = "we:showShopItemNumber -tagi n‰ytt‰‰ m‰‰ritettyjen nimikkeiden m‰‰r‰n ostoskorissa.";
$l_we_tag['sidebar']['description'] = ""; 
$l_we_tag['sidebar']['defaultvalue'] = "Avaa sivupalkin";
$l_we_tag['subscribe']['description'] = "T‰t‰ tagia k‰ytetet‰‰n yksitt‰isen syˆttˆkent‰n luomiseksi webEdition tiedostoon, jotta k‰ytt‰j‰ voi antaa s‰hkˆpostiosoitteensa tilatakseen uutiskirjeen.";
$l_we_tag['sum']['description'] = "we:sum -tagi yhdist‰‰ kaikki kohteet listalle.";
$l_we_tag['target']['description'] = "T‰t‰ tagia k‰ytet‰‰n linkin kohteen (target) valitsemiseksi &lt;we:linklist&gt; -tagista.";
$l_we_tag['textarea']['description'] = "we:textarea -tagi luo monirivisen sis‰llˆn syˆttˆalueen.";
$l_we_tag['title']['description'] = "we:title -tagi luo normaalin otsikko -tagin. Jos otsikkokentt‰ Ominaisuudet -v‰lilehdell‰ on tyhj‰, k‰ytet‰‰n vakio otsikkoa, muutoin k‰ytet‰‰n Ominaisuudet -v‰lilehdell‰ m‰‰ritelty‰ otsikkoa.";
$l_we_tag['tr']['description'] = "&lt;we:tr&gt; -tagi vastaa HTML:n &lt;tr&gt; -tagia.";
$l_we_tag['tr']['defaultvalue'] = ""; 
$l_we_tag['unsubscribe']['description'] = "T‰t‰ tagia k‰ytetet‰‰n yksitt‰isen syˆttˆkent‰n luomiseksi webEdition tiedostoon, jotta k‰ytt‰j‰ voi antaa s‰hkˆpostiosoitteensa lopettaakseen uutiskirjeen tilauksen.";
$l_we_tag['url']['description'] = "we:url -tagi luo sis‰isen webEdition URL-osoitteen, joka osoittaa dokumenttiin, jolla on alla annettu id.";
$l_we_tag['userInput']['description'] = "we:userInput -tagi luo syˆttˆkent‰t, joita voidaan k‰ytt‰‰ we:form type=\"document\" tai type=\"object\" yhteydess‰ tiedostojen tai objektien luomiseksi.";
$l_we_tag['useShopVariant']['description'] = "we:shopVariant -tagi k‰ytt‰‰ artikkelin toisinnon datan annetun nimen perusteella. Jos toisintoja annetulla nimell‰ ei ole, n‰ytet‰‰n vakio artikkeli.";
$l_we_tag['var']['description'] = "we:var -tagi esitt‰‰ alla annettuun nimeen liittyv‰n tiedosto-kent‰n globaalin php -muuttujan sis‰llˆn.";
$l_we_tag['voting']['description'] = "we:voting -tagia k‰ytet‰‰n ‰‰nestyksen luomiseen.";
$l_we_tag['voting']['defaultvalue'] = ""; 
$l_we_tag['votingField']['description'] = "The we:votingField-tag is required to display the content of a voting. The attribute \"name\" defines what to show. The attribute \"type\", how to display it. Valid name-type combinations are: question - text; result - count, percent, total; id - answer, select, radio, voting; answer - text,radio,checkbox (select multiple) select, textinput and textarea (free text answer field), image (all we:img attributes such as thumbnail are supported), media (delivers the path utilizing to and nameto); ";// TRANSLATE
$l_we_tag['votingList']['description'] = "T‰m‰ tagi luo automaattisesti ‰‰nestyslistat.";
$l_we_tag['votingList']['defaultvalue'] = "";
$l_we_tag['votingSelect']['description'] = "K‰yt‰ t‰t‰ tagia luodaksesi alasvetovalikon; (&lt;select&gt;) ‰‰nestyksen valintaan.";
$l_we_tag['write']['description'] = "T‰m‰ tagi tallentaa tiedoston tai objektin, jonka &lt;we:form type=\"document/object&gt; on luonut";
$l_we_tag['writeShopData']['description'] = "we:writeShopData tagi tallentaa kaikki sen hetkisten ostoskorien sis‰llˆt tietokantaan.";
$l_we_tag['writeVoting']['description'] = "Tagi tallentaa ‰‰nestyksen tietokantaan. Jos \"id\" on m‰‰ritelty, vain ‰‰nestys, jolla on kyseinen id tallennetaan.";
$l_we_tag['xmlfeed']['description'] = "Tagi lataa xml sis‰llˆn annetusta url-osoitteesta";
$l_we_tag['xmlnode']['description'] = "Tagi tulostaa xml-elementin annetusta syˆtteest‰ tai url-osoitteesta.";
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