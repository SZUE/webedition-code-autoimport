<?php
/** Generated language file of webEdition CMS */
$l_weTag=array(
	'addDelNewsletterEmail'=>array(
		'description'=>'Tätä tagia käytetään öisäämään tai poistamaan sähköpostiosoite uutiskirjeen tilaajalistalta. Attribuutissa "path" täytyy antaa täydellinen polku uutiskirjeen vastaanottajalistatiedostoon. Jos path alkaa ilman merkkiä "/", lisätään annettu merkkijono DOCUMENT_ROOT arvoon. Jos käytössä on useita listoja, voit antaa pathiin useita polkuja pilkkueroteltuna.',
	),
	'addDelShopItem'=>array(
		'description'=>'Käytä we:addDelShopItem tagia lisätäksesi tai poistaaksesi tavaraa ostoskorista.',
	),
	'addPercent'=>array(
		'description'=>'Tagi we:addPercent lisää arvoa määritellyn prosenttimäärän verran, esim. ALV:n verran.',
	),
	'answers'=>array(
		'description'=>'Tagi näyttää äänestyksen vastausvaihtoehdot.',
	),
	'author'=>array(
		'description'=>'Tagi we:author näyttää dokumentin luojan nimen. Jos attribuuttia `type` ei ole määritelty, näytetään käyttäjätunnus. Jos type="name", näytetään käyttäjän etu- ja sukunimi. Jos nimiä ei ole määritelty, näytetään edelleen vain käyttäjätunnus.',
	),
	'a'=>array(
		'description'=>'we:a tagi luon HTML-linkki tagin joka viittaa sisäiseen, ID:llä määriteltävään webEdition dokumenttiin. Kaikki aloitus- ja lopetustagin väliin tuleva sisältö toimii linkkinä.',
	),
	'back'=>array(
		'description'=>'Tagi we:back tagi luo HTML-linkin joka viittaa we:listviewin edelliselle sivulle. Kaikki aloitus- ja lopetustagin väliin tuleva sisältö toimii linkkinä.',
	),
	'bannerSelect'=>array(
		'description'=>'Täm tagi näyttää alasvatovalikon (&lt;select&gt;), jolla valita bannereita. Jos Asiakashallintamoduuli on asennettu ja attribuutti "customer" on asetettu, bannerit näytetään vain kirjautuneille käyttäjille.',
	),
	'bannerSum'=>array(
		'description'=>'Tagi we:bannerSum näyttää kaikkien bannerinäyttöjen tai klikkausten summan. Tagi toimii vain listview type="banner" sisällä.',
	),
	'banner'=>array(
		'description'=>'Käytä we:banner tagia sisällyttääksesi bannerin Banneri Moduulista.',
	),
	'block'=>array(
		'description'=>'Tagi we:block tagi mahdollistaa laajennettavien blockien/listojen luonnin. Kaikki aloitus- ja lopetustagien väliin tuleva sisältö (HTML-koodi, lähes kaikki we:tagit) lisätään sivulle plus-painikkeen painallukselle sivun muokkaustilassa.',
	),
	'calculate'=>array(
		'description'=>'we:calculate tagi mahdollistaa kaikkien PHP:n tarjoaminen matemaattisten operaatioiden käytön, esim. *, /, +, -,(), sqrt..jne.',
	),
	'captcha'=>array(
		'description'=>'Tag generoi kuvan jossa on satunnainen koodi.',
	),
	'categorySelect'=>array(
		'description'=>'Tätä tagia käyttämällä voidaan lisätä alasvetovalikko (&lt;select&gt;) webEdition dokumenttiin. Määrittämällä lopetustagi heti aloitustagin jälkeen saadaan valikko näyttämään kaikki webEditionin kategoriat.',
	),
	'category'=>array(
		'description'=>'we:category tagissa määritellyt kategoriat korvataan kategorialla tai kategorioilla jotka määritellään dokumentin Ominaisuudet- välilehdellä. Jos tagia käytettäessä halutaan määritellä useita kategorioita, ne täytyy erotella pilkulla. Jos halutaan käyttää muuta erotinta, täytyy käytettävä erotin määritellä attribuutilla  "tokken\.',
	),
	'charset'=>array(
		'description'=>'we:charset tagi luo HTML-metatagin joka määrittää sivulla käytettävän merkistökoodauksen. "ISO-8859-1" on yleensä käytössä englannikielisillä sivuilla. Tämä tagi on sijoitettavfa HTML-sivun head-osioon.',
	),
	'checkForm'=>array(
		'description'=>'we:checkForm tagi luo JavaScript koodin jolla voi tarkistaa määritellyn lomakkeen syötteet.<br/>Parametrien `match` ja `type` avulla määritellään tarkistettavan lomakkeen `name` tai `id`.<br/>`mandatory` sisältää pilkkuerotellun listan pakollisten kenttien nimistä ja `email` sisältää samaan malliin koostetun listan kentistä joiden aiotut syöttet ovat tyypeiltään sähköpostiosoitteita. <br/>Kentään `password` on mahdollista kirjoittaa 2 kenttänimeä joihin sovelletaan salasanatarkastusta, sekä kolmantena arvona numeerinen arvo joka määrittää salasanan minimipituuden (esim: salasana,salasana2,5). <br/>`onError` kohtaan voit määrittää virhetilanteessa mahdollisesti kutsuttavan itse määrittelemäsi JavaScript -funktion nimen. Tämä funktio saa parametrina taulukon josta löytyvät puuttuvien pakollisten kenttien nimet, ja `flagin` siitä oliko salasanat oikein. Jos `onError` jätetään määrittelemättä tai funktiota ei ole lisätty sivupohjaan, näytetään oletusarvot alert-ikkunassa.',
	),
	'colorChooser'=>array(
		'description'=>'we:colorChooser tagi luo kontrollin jolla voidaan helposti valita väriarvo.',
	),
	'comment'=>array(
		'description'=>'The comment Tag is used to generate explicit comments in the specified language, or, to add comments to the template which are not delivered to the user browser.',
	),
	'conditionAdd'=>array(
		'description'=>'Tagia käytetään uuden ehdon tai säännön lisäämiseen &lt;we:condition&gt; tagien sisällä.',
	),
	'conditionAnd'=>array(
		'description'=>'Tagia käytetään ehtojen lisäämiseen &lt;we:condition&gt; tagien sisällä. Tämä on looginen operaattori AND, tarkoittaen sitä että molempien liitettyjen ehtojen tulee täyttyä.',
	),
	'conditionOr'=>array(
		'description'=>'Tagia käytetään ehtojen lisäämiseen &lt;we:condition&gt; tagien sisällä. Tämä on looginen operaattori OR, tarkoittaen että jomman kumman liitetyistä ehdoista tulee täyttyä.',
	),
	'condition'=>array(
		'description'=>'Tätä tagia käytetään yhdessä tagin &lt;we:conditionAdd&gt; kanssa kun halutaan dynaamisesti lisätä arvoja &lt;we:listview type="object"&gt; attribuuttiin "condition". Ehdot voivat olla limittäisiä.',
	),
	'content'=>array(
		'description'=>'&lt;we:content /&gt; käytetään vain pääsivupohjan sisällä (mastertemplate). Se määrittelee paikan jonne pääsivupohjaa käyttävän muun sivupohjan sisältö liitetään.',
	),
	'controlElement'=>array(
		'description'=>'Tagia we:controlElement käytetään dokumentin muokkaustilassa kontrollielementtien save, delete, publish jne. hallintaan. Painikkeita voidaan piilottaa, checkboxeja disabloida/rastittaa/piilottaa.',
	),
	'cookie'=>array(
		'description'=>'Tämä tagi on äänestysmoduulin vaatima ja se luo asiakaskoneelle evästeen joka estää useammat äänestyskerrat. Tagi täytyy sijoittaa aivan sivupohjan alkuun (ts. mitään ei saa tulostaa ennen tätä tagia, ei edes välilyöntejä tai rivinvaihtoja).',
	),
	'createShop'=>array(
		'description'=>'Tagia we:createShop tarvitaan kaikilla sivuilla joilla on tarkoitus tulostaa tietoja ostoksista.',
	),
	'css'=>array(
		'description'=>'Css tagi luo HTML-tagin joka viittaa ID:llä määriteltyyn webEditionin sisäiseen CSS-tiedostoon.',
	),
	'customer'=>array(
		'description'=>'Using this tag, data from any customer can be displayed. The customer data are displayed as in a listview or within the &lt;we:object&gt; tag with the tag &lt;we:field&gt;.<br/><br/>Combining the attributes, this tag can be utilized in three ways:<br/>If name is set, the editor can select a customer by using a customer-select-Field. This customer is stored in the document within the field name.<br/>If name is not set but instead the id, the customer with this id is displayed.<br/>If neither name nor id is set, the tag expects the id of the customer by a request parameter. This is i.e. used by the customer-listview when the attribut hyperlink="true" in the &lt;we:field&gt; tag is used. The name of the request parameter is we_cid.',
	),
	'dateSelect'=>array(
		'description'=>'we:dateSelect tagi tulostaa valintakentän päivämäärälle. Tätä voidaan käyttää yhdessä we:processDateSelect tagin kanssa jos halutaan lukea valittu arvo esim. muuttujaan joka on tyyppiä UNIX TIMESTAMP.',
	),
	'date'=>array(
		'description'=>'we:date tagi näyttää kuluvan hetken päivämäärätiedot muodossa joka on määritelty päivämäärän muotoilumerkkijonossa. Jos dokumentti on staattinen, tyyppi tulee asettaa muotoon &quot;js&quot;, jotta aika saadaan tulostettua JavaScriptillä.',
	),
	'deleteShop'=>array(
		'description'=>'we:deleteShop tagi poistaa koko ostoskorin.',
	),
	'delete'=>array(
		'description'=>'Tällä tagilla poistetaan dokumentteja joihin on menty &lt;we:a edit="document" delete="true"&gt; tai &lt;we:a edit="object" delete="true"&gt; kautta.',
	),
	'description'=>array(
		'description'=>'we:description tagi luo description- metatagin. Jos dokumentin kuvauskenttä Ominaisuudet- välilehdellä on tyhjä, käytetään HTML-sivun koko sisältöä kuvaustekstinä.',
	),
	'DID'=>array(
		'description'=>'Tagi palauttaa webEdition dokumentin ID:n.',
	),
	'docType'=>array(
		'description'=>'Tagi palauttaa webEdition dokumentin dokumenttityypin.',
	),
	'else'=>array(
		'description'=>'Tätä tagia käytetään lisäämään vaihtoehtoisia ehtohaaroja if-tyyppisten tagien sisälle. Esim.&lt;we:ifEditmode&gt;, &lt;we:ifNotVar&gt;, &lt;we:ifNotEmpty&gt;, &lt;we:ifFieldNotEmpty&gt;',
	),
	'field'=>array(
		'description'=>'Tagi lisää "name" attribuutissa määritellyn kentän sisällön käytettäessä listviewiä. Tagi toimii vain we:repeat tagien välissä.',
	),
	'flashmovie'=>array(
		'description'=>'we:flashmovie tagi mahdollistaa Flash-esityksen lisäämisen sivun sisältöön. Käytettäessä tätä tagia dokumentin muokkaustilassa näytetään tiedostoselaimen avaava esityksen valintapainike.',
	),
	'formfield'=>array(
		'description'=>'Tagia käytetään lisättäessä lomakekenttiä front end lomakkeeseen.',
	),
	'formmail'=>array(
		'description'=>'With activated Setting Call Formmail via webEdition document, the integration of the formmail script is realized with a webEdition document. For this, the (currently without attributes) we-Tag formmail will be used. <br/>If the Captcha-check is used, &lt;we:formmail/&gt; is located within the we-Tag ifCaptcha.',
	),
	'form'=>array(
		'description'=>'we:form tagia käytetään haku- ja mailiformien luontiin. Se toimii samaan tapaan kuin normaali HTML-lomakekin, mutta se antaa parserin lisätä tarvitsemiaan lisätietokenttiä hidden muotoisena.',
	),
	'hidden'=>array(
		'description'=>'we:hidden tagi luo piilotetun (hidden) kentän joka sisältää saman nimisestä globaalista PHP-muuttujasta haetun muuttuja-arvon. Käytä tätä tagia kun haluat siirtää esim. lomakkeelta tulevia arvoja eteenpäin.',
	),
	'hidePages'=>array(
		'description'=>'we:hidePages mahdollistaa dokumentin tiettyjen välilehtien piilottamisen webEditionin puolella. Voit esimerkiksi rajoittaa pääsyä dokumentin Ominaisuudet- välilehdelle.',
	),
	'href'=>array(
		'description'=>'we:href tagi luo valinnan jolla voidaan määrittää joko sisäisen tai ulkoisen dokumentin URL dokumentin muokkaustilassa.',
	),
	'icon'=>array(
		'description'=>'we:icon tagi luo HTML-tagin joka viitta webEditionin sisäiseen ikonidokumenttiin we:tagille annetun ID:n perusteella. Ikonia käytetään mm. selainten osoiterivillä ja kirjanmerkeissä.',
	),
	'ifBack'=>array(
		'description'=>'Tagia käytetään &lt;we:listview&gt; aloitus- ja lopetustagien välillä. we:back aloitus- ja lopetustagien sisään määritelty sisältö näytetään vain jos listviewillä on olemassa edellinen sivu.',
	),
	'ifbannerexists'=>array(
		'description'=>'Executes the enclosed code only, if the banner module is not deaktivated (settings dialog).',
	),
	'ifCaptcha'=>array(
		'description'=>'Tämän tagin sulkema sisältö esitetään vain jos käyttäjän syöttämä koodi on oikein.',
	),
	'ifCat'=>array(
		'description'=>'we:ifCat tagia käytetään rajaamaan näytettäviä kategorioita. Categories-listalle lisätään näytettävät kategoriat, joita verrataan dokumentin kategorioihin.',
	),
	'ifClient'=>array(
		'description'=>'we:ifClient:n sisällä oleva tieto näytetään jos selain vastaa browser-kohtaan valittua selainta. Tagi toimii ainoastaan dynaamisilla sivuilla!',
	),
	'ifConfirmFailed'=>array(
		'description'=>'Kun käytetään DoubleOptIn tagia Newsletter moduulissa, niin we:ifConfirmFailed -tagi tarkastaa sähköpostiosoitteen oikeellisuuden.',
	),
	'ifCurrentDate'=>array(
		'description'=>'Tämä tagi korostaa halutun päivän kalenteri-listview:ssä',
	),
	'ifcustomerexists'=>array(
		'description'=>'Executes the enclosed code only, if the customer module is not deaktivated (settings dialog).',
	),
	'ifDeleted'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään jos dokumentti tai objekti poistettiin käyttämällä we:delete -tagia',
	),
	'ifDoctype'=>array(
		'description'=>'Tagin sisällä oleva tieto näytetään jos dokumenttityyppi vastaa sivuston doctypeen.',
	),
	'ifDoubleOptIn'=>array(
		'description'=>'Tagin sisällä oleva tieto näytetään ainoastaa double opt-in prosessin ensimmäisessä vaiheessa.',
	),
	'ifEditmode'=>array(
		'description'=>'Tagin sisällä oleva tieto näytetään ainoastaan editmodessa.',
	),
	'ifEmailExists'=>array(
		'description'=>'Tagin sisällä oleva tieto näytetään ainoastaan jos määritetty sähköpostiosoite löytyy uutiskirjeen osoitelistalta.',
	),
	'ifEmailInvalid'=>array(
		'description'=>'Tagin sisällä oleva tieto näytetään ainoastaan jos tietty sähköpostiosoiteen syntaksi on virheellinen.',
	),
	'ifEmailNotExists'=>array(
		'description'=>'Tagin sisällä oleva tieto näytetään ainoastaan jos kyseessäoleva sähköpostiosoite ei ole uutiskirjeen osoitelistalla.',
	),
	'ifEmpty'=>array(
		'description'=>'Tagin sisällä oleva tieto näytetään ainoastaan jos kenttä on tyhjä jolla on sama nimi kuin match-arvona. Verrattavan kentän tyyppi täytyy määrittää, `img,flashmovie, href,object`',
	),
	'ifEqual'=>array(
		'description'=>'we:ifEqual tagi vertaa kenttien sisältöä `name` ja `eqname`. Jos sisältö on molemmissa sama niin sisältö näytetään. Jos tagia käytetään we:list:ssä, we:block:ssa tai we:linklist:ssä, vain yhtä kenttää voidaan verrata. Jos attribuuttia `value` käytetään, `eqname` hylätään ja sillon sisältöä verrataan `value`-arvoon',
	),
	'ifFemale'=>array(
		'description'=>'Tagin sisällä oleva tieto näytetään ainoastaan jos käyttäjä on valinnut sukupuoleksi naisen.',
	),
	'ifFieldEmpty'=>array(
		'description'=>'we:ifFieldEmpty varmistaa että kaikki tagin sisällä oleva tieto näytetään ainoastaan jos listview:n sisällä oleva kenttä on tyhjä ja jonka nimi täsmää `match`-arvoon. Kentän tyypin on määriteltävä.',
	),
	'ifFieldNotEmpty'=>array(
		'description'=>'we:ifFieldNotEmpty varmistaa että kaikki tagin sisällä oleva tieto näytetään ainoastaan jos listview:n sisällä oleva kenttä ei ole tyhjä ja jonka nimi täsmää `match`-arvoon. Kentän tyypin on määriteltävä.',
	),
	'ifField'=>array(
		'description'=>'Tagia käytetään we:repeat -tagin sisällä. Kaikki sisältö näytetään jos attribuutin `match` arvo on identtinen tietokannasta löytyvään kenttään joka on määritetty listview:lle.',
	),
	'ifFound'=>array(
		'description'=>'Tagin sisällä oleva tieto näytetään jos listview:llä on hakutuloksia',
	),
	'ifHasChildren'=>array(
		'description'=>'we:repeat -tagin sisällä we:ifHasChildren:iä käytetään alikansioiden tarkistukseen, jos niitä löytyy ne tulostetaan',
	),
	'ifHasCurrentEntry'=>array(
		'description'=>'we:ifHasCurrentEntry:ä voidaan käyttää we:navigationEntry type=`folder`:n sisällä näyttääkseen aktiivista sisältöä',
	),
	'ifHasEntries'=>array(
		'description'=>'we:ifHasEntries:iä voidaan käyttää tulostaakseen we:nagigationEntry:n mahdolliset alikansiot',
	),
	'ifHasShopVariants'=>array(
		'description'=>'we:ifHasShopVariants voi näyttää sisältöä riippuen muuttujien olemassaolosta objektissa tai dokumentissa. Voidaan kontrolloida we:listview type=`shopVariant`. <b>This tag works in document and object templates attached by object-workspaces, but not inside the we:listview and we:object - tags</b>',
	),
	'ifHtmlMail'=>array(
		'description'=>'Tämän tagin sisältö näytetään vain jos uutiskirjeen formaatti on HTML.',
	),
	'ifIsDomain'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään jos palvelimen domain -nimi on sama kuin `domain` -arvo. Sisällön voi nähdä ainoastaan valmiissa sivussa tai esikatselussa. Muokkaustilassa tätä tagia ei oteta huomioon.',
	),
	'ifIsNotDomain'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään jos palvelimen domain -nimi ei ole sama kuin `domain` -arvo. Sisällön voi nähdä ainoastaan valmiissa sivussa tai esikatselussa. Muokkaustilassa tätä tagia ei oteta huomioon..',
	),
	'ifLastCol'=>array(
		'description'=>'Tämän tagi havaitsee taulukosta rivin viimeisen viimeisen sarakkeen, kun käytetään we:listview:n taulukkofunktioiden kanssa;',
	),
	'ifLoginFailed'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain jos sisäänkirjautuminen epäonnistui.',
	),
	'ifLogin'=>array(
		'description'=>'Content enclosed by this tag is only displayed if a login occured on the page and allows for initialisation.',
	),
	'ifLogout'=>array(
		'description'=>'Content enclosed by this tag is only displayed if a logout occured on the page and allows for cleaning up.',
	),
	'ifMailingListEmpty'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain jos käyttäjä ei ole valinnut yhtään uutiskirjettä.',
	),
	'ifMale'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain jos käyttäjä on mies. Tätä tagia käytetään uutiskirjeiden käyttjien sukupuolen tunnistuksessa.',
	),
	'ifnewsletterexists'=>array(
		'description'=>'Executes the enclosed code only, if the newsletter module is not deaktivated (settings dialog).',
	),
	'ifNewsletterSalutationEmpty'=>array(
		'description'=>'Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is empty.',
	),
	'ifNewsletterSalutationNotEmpty'=>array(
		'description'=>'Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is not empty.',
	),
	'ifNew'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain uudessa webEdition dokumentissa tai objektissa.',
	),
	'ifNext'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain jos `Seuraavat` -objekteja on saatavilla',
	),
	'ifNoJavaScript'=>array(
		'description'=>'Tämä tagi uudelleenohjaa sivun toiselle sivulle ID:n perusteella jos selaimessa ei ole tukea JavaScript:lle tai jos JavaScript on pois päältä. Tätä tagia voidaan käyttää ainoastaan templaten head-osiossa..',
	),
	'ifNotCaptcha'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain jos käyttäjän syöttämä koodi ei ole oikein.',
	),
	'ifNotCat'=>array(
		'description'=>'The we:ifNotCat tag ensures that everything located between the start tag and the end tag is only displayed if the categories which are entered under "categories" are none of the document`s categories.',
	),
	'ifNotDeleted'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain jos webEdition dokumenttia tai objektia ei voitu poistaa we:delete -tagilla',
	),
	'ifNotDoctype'=>array(
		'description'=>'Show enclosed content, if doctype of document is not listed within attribute "doctypes"',
	),
	'ifNotEditmode'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain jos ei olla sivun muokkaustilassa',
	),
	'ifNotEmpty'=>array(
		'description'=>'Tämä tagi varmistaa että tämän tagin sisältö näytetään jos sen sisällä olevan we-tagin nimi vastaa `match` atribuutin arvoon EIKÄ se ole tyhjä. Tyyppi täytyy määrittää.',
	),
	'ifNotEqual'=>array(
		'description'=>'Tämä tagi vertaa sisälläolevan we-tagin nimi atribuuttia `eqname`:n arvoon, jos se eivät ole samat, sisältö näytetään. Jos attribuuttia `value` käytetään, `eqname` hylätään ja sillon sisältöä verrataan `value`-arvoon',
	),
	'ifNotField'=>array(
		'description'=>'Tagia käytetään we:repeat -tagin sisällä. Kaikki sisältö näytetään jos attribuutin `match` arvo ei ole identtinen tietokannasta löytyvään kenttään joka on määritetty listview:lle.',
	),
	'ifNotFound'=>array(
		'description'=>'Tagin sisällä oleva tieto näytetään jos listview:llä ei ole hakutuloksia',
	),
	'ifNotHasChildren'=>array(
		'description'=>'Within the &lt;we:repeat&gt; tag &lt;we:ifNotHasChildren&gt; is used to query if a category(folder) has child categories.',
	),
	'ifNotHasCurrentEntry'=>array(
		'description'=>'we:ifNotHasCurrentEntry can be used within we:navigationEntry type="folder" to show some content, only if the navigation folder does not contain the activ entry',
	),
	'ifNotHasEntries'=>array(
		'description'=>'we:ifNotHasEntries can be used within we:navigationEntry to show content only, if the navigation entry does not contain entries.',
	),
	'ifNotHasShopVariants'=>array(
		'description'=>'The tag we:ifHasShopVariants can display content depending on the existance of variants in an object or document. With this, it can be controlled whether a &lt;we:listview type="shopVariant"&gt; should be displayed at all or some alternative.',
	),
	'ifNotHtmlMail'=>array(
		'description'=>'Tämän tagin sisältö näytetään vain jos uutiskirjeen formaatti ei ole HTML.',
	),
	'ifNotNewsletterSalutation'=>array(
		'description'=>'Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is not equal to match.',
	),
	'ifNotNew'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain uudessa webEdition dokumentissa tai objektissa.',
	),
	'ifNotObjectLanguage'=>array(
		'description'=>'The tag we:ifNotObjectLanguage tests on the language setting in the properties tab of the object, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages',
	),
	'ifNotObject'=>array(
		'description'=>'Tämän tagin sisältö näytetään vain jos listview:n sisältö ei ole objekti. Listviewin tyyppi täytyy olla `search`;',
	),
	'ifNotPageLanguage'=>array(
		'description'=>'The tag we:ifNotPageLanguage tests on the language setting in the properties tab of the document, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages',
	),
	'ifNotPosition'=>array(
		'description'=>'Tämä tagi mahdollistaa toiminnon määrittelyn mitä EI tehdä tietyssä block:n, listview:n, linklist:n listdir:n kohdassa. Parametri `position` hyväksyy monipuolisia arvoja, `first`,`last`,`all even`,`all odd`, tai numeerisen määrittely (1,2,3...). Tyyppinä täytyy olla block tai linklist ja nimi sillä.',
	),
	'ifNotRegisteredUser'=>array(
		'description'=>'Tarkistaa onko käyttäjä rekisteröitynyt.',
	),
	'ifNotReturnPage'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain luonnin tai muokkauksen jälkeen mikäli paluuarvo we:a edit=`true` on epätosi tai ei määritetty.',
	),
	'ifNotSearch'=>array(
		'description'=>'Tagin sisältö tulostetaan vain jos hakutermiä ei lähetetty we:search:ltä tai se oli tyhjä. Jos attribuutti `set` = tosi, vain we:search:n pyydetyt muuttujat on varmennettu asettamattomiksi.',
	),
	'ifNotSeeMode'=>array(
		'description'=>'Tämän tagin sisältö näytetään vain seeMode:n ulkopuolelle.',
	),
	'ifNotSelf'=>array(
		'description'=>'Mitään tietoa tämän tagin sisällä ei näytetä jos dokumentilla on yksikään tagiin syötetyistä ID:stä. Jos tagi ei sijaitse we:linklist:n, we:listdir:n sisällä `id` on pakollinen kenttä!',
	),
	'ifNotSendMail'=>array(
		'description'=>'Checks if a page is currently sent by we:sendMail and allows to exclude or include contents to the sent page',
	),
	'ifNotShopField'=>array(
		'description'=>'Everything between the start and end tags of this tag is displayed only if the value of the attribut "match" is not identical with the value of the shopField',
	),
	'ifNotSidebar'=>array(
		'description'=>'This tag is used to display the enclosed contents only if the opened document is not located within the Sidebar.',
	),
	'ifNotSubscribe'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain jos tilaus epäonnistui. Tämä tagi pitäisi olla `Tilaa uutiskirje` templatessa we:addDelNewsletterEmail -tagin jälkeen.',
	),
	'ifNotTemplate'=>array(
		'description'=>'Näytä sisältyvä tieto vain, jos nykyinen dokumentti ei perustu annettuun sivupohjaan.<br/><br/>Löydät lisätietoja we:ifTemplate -tagin referenssistä.',
	),
	'ifNotTop'=>array(
		'description'=>'Tämän tagin sisältö näytetään vain jos se sijaitsee `include` -dokumentissa.',
	),
	'ifNotUnsubscribe'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään vain jos pyyntö ei toimi. Tämä tagi pitäisi olla `Peru uutiskirjetilaus` templatessa we:addDelNewsletterEmail -tagin jälkeen.',
	),
	'ifNotVarSet'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään jos muuttujaa nimellä `name` ei ole määritetty. Huom! `Ei asetettu` ei ole sama kuin tyhjä!',
	),
	'ifNotVar'=>array(
		'description'=>'Tämän tagin sisällä olevaa tietoa ei näytetä jos muuttujan `name` -arvo on sama kuin `match` -arvo. Muuttujan tyyppi voidaan määrittää `type`-attribuutilla',
	),
	'ifNotVoteActive'=>array(
		'description'=>'Any content between the start- and endtag is only displayed, if the voting has expired.',
	),
	'ifNotVoteIsRequired'=>array(
		'description'=>'Any content between the start- and endtag is only displayed, if the voting ist not required to be filled out.',
	),
	'ifNotVote'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään jos äänestystä ei tallennettu. `type` -attribuutti määrittää virheen tyypin.',
	),
	'ifNotVotingField'=>array(
		'description'=>'Checks if a votingField has not a value corresponding to the attribute match, the attribute combinations of name and type are the same as in the we:votingField tag',
	),
	'ifNotVotingIsRequired'=>array(
		'description'=>'Prints the enclosed content only, if the voting field is a required field',
	),
	'ifNotWebEdition'=>array(
		'description'=>'Tämän tagin sisältö näytetään vain webEditionin ulkopuolelle.',
	),
	'ifNotWorkspace'=>array(
		'description'=>'Tarkastaa, sijaitseeko dokumentti jossain muualla kuin työtilassa joka on määritelty `path` attribuutissa',
	),
	'ifNotWritten'=>array(
		'description'=>'Tämän tagin sisältö näytetään vain jos tapahtuu virhe dokumentin tai objektin tallennusvaiheessa käyttäen we:write -tagia.',
	),
	'ifObjectLanguage'=>array(
		'description'=>'The tag we:ifObjectLanguage tests on the language setting in the properties tab of the object, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages',
	),
	'ifObject'=>array(
		'description'=>'Tämän tagin sisällä oleva tieto näytetään jos löydettiin yksilöllinen rivi we:listview type=`search`:lla joka on objekti.',
	),
	'ifobjektexists'=>array(
		'description'=>'Executes the enclosed code only, if the object module is not deaktivated (settings dialog).',
	),
	'ifPageLanguage'=>array(
		'description'=>'The tag we:ifPageLanguage tests on the language setting in the properties tab of the document, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages',
	),
	'ifPosition'=>array(
		'description'=>'Tämä tagi mahdollistaa toiminnon määrittelyn mitä tehdään tietyssä block:n, listview:n, linklist:n listdir:n kohdassa. Parametri `position` hyväksyy monipuolisia arvoja, `first`,`last`,`all even`,`all odd`, tai numeerisen määrittely (1,2,3...). Tyyppinä täytyy olla block tai linklist ja nimi sillä.',
	),
	'ifRegisteredUserCanChange'=>array(
		'description'=>'Tämän tagin sisältö näytetään vain jos rekisteröitynyt käyttäjä, joka on kirjautuneena sisään on oikeutettu muokkaamaan tämänhetkistä webEdition documenttia tai objektia.',
	),
	'ifRegisteredUser'=>array(
		'description'=>'Tarkastaa, jos käyttäjä on rekisteröitynyt.',
	),
	'ifReturnPage'=>array(
		'description'=>'Tämän tagin sisältö näytetään webEdition dokumentin tai objektin luonnin tai muokkauksen jälkeen ja palautettava arvo `result` on we:a edit=`document` tai we:a edit=`object` on tosi.',
	),
	'ifSearch'=>array(
		'description'=>'Tagin sisältö tulostetaan vain jos hakutermi lähetettiin we:search:lle ja se ei ole tyhjä. Jos attribuutti `set` = tosi, vain we:search:n pyydetyt muuttujat on varmennettu asettamattomiksi.',
	),
	'ifSeeMode'=>array(
		'description'=>'Tämän tagin sisältö näytetään vain seeMode:ssa.',
	),
	'ifSelf'=>array(
		'description'=>'Tämän tagin sisältö näytetään jos dokumentilla on sama ID kuin mikä on määritetty tähän tagiin. Jos tagi ei ole we:linklist tai we:listdir:n sisällä, ID on pakollinen!',
	),
	'ifSendMail'=>array(
		'description'=>'Checks if a page is currently sent by we:sendMail and allows to exclude or include contents to the sent page',
	),
	'ifShopEmpty'=>array(
		'description'=>'Tämän tagin sisältö näytetään jos ostoskori on tyhjä.',
	),
	'ifshopexists'=>array(
		'description'=>'Executes the enclosed code only, if the shop module is not deaktivated (settings dialog).',
	),
	'ifShopFieldEmpty'=>array(
		'description'=>'Content enclosed by this tag is only displayed if the shopField named in attribute "name" is empty.',
	),
	'ifShopFieldNotEmpty'=>array(
		'description'=>'Content enclosed by this tag is only displayed if the shopField named in attribute "name" is not empty.',
	),
	'ifShopField'=>array(
		'description'=>'Everything between the start and end tags of this tag is displayed only if the value of the attribut "match" is identical with the value of the shopField',
	),
	'ifShopNotEmpty'=>array(
		'description'=>'Tämän tagin sisältö näytetään jos ostoskori ei ole tyhjä.',
	),
	'ifShopPayVat'=>array(
		'description'=>'Tagin sisältö näytetään jos kirjautuneen henkilön tulee maksaa alv.',
	),
	'ifShopVat'=>array(
		'description'=>'Tämä tagi tarkistaa alv:n kyseisestä dokumentista / ostoskorista. ID mahdollistaa alv:n tarkistuksen tietyistä artikkeleista.',
	),
	'ifSidebar'=>array(
		'description'=>'This tag is used to display the enclosed contents only if the opened document is located within the Sidebar.',
	),
	'ifSubscribe'=>array(
		'description'=>'Tämän tagin sisältö näytetään jos uutiskirjeen tilaus onnistui. Tagia tulee käyttää uutiskirjeen tilaustemplatessa heti addDelnewsletterEmail -tagin jälkeen.',
	),
	'ifTdEmpty'=>array(
		'description'=>'Content enclosed by this tag is only displayed if a table cell is empty (has no contents in a listview).',
	),
	'ifTdNotEmpty'=>array(
		'description'=>'Content enclosed by this tag is only displayed if a table cell is not empty (has contents in a listview).',
	),
	'ifTemplate'=>array(
		'description'=>'Show document, if current document is created by the given template.',
	),
	'ifTop'=>array(
		'description'=>'Tämän tagin sisältö näytetään jos tagi ei ole liitetyssä (included) dokumentissa.',
	),
	'ifUnsubscribe'=>array(
		'description'=>'Tämän tagin sisältö näytetään jos uutiskirjeen tilauksen peruuttaminen onnistui. Tagia tulee käyttää uutiskirjeen tilaustemplatessa heti addDellnewsletterEmail -tagin jälkeen.',
	),
	'ifUserInputEmpty'=>array(
		'description'=>'Tämän tagin sisältö näytetään tietty input-kenttä on tyhjä.',
	),
	'ifUserInputNotEmpty'=>array(
		'description'=>'Tämän tagin sisältö näytetään tietty input-kenttä ei ole tyhjä.',
	),
	'ifVarEmpty'=>array(
		'description'=>'Tämän tagin sisältö näytetään jos muuttuja on tyhjä jolla on sama nimi kuin match-attribuuttiin on määritelty.',
	),
	'ifVarNotEmpty'=>array(
		'description'=>'Tämän tagin sisältö näytetään jos muuttuja on ei ole tyhjä ja jolla on sama nimi kuin match-attribuuttiin on määritelty.',
	),
	'ifVarSet'=>array(
		'description'=>'Tämän tagin sisältö näytetään jos kohde muutujaa ei ole asetettu. Huom! `asetettu` ei ole sama kuin `ei tyhjä`',
	),
	'ifVar'=>array(
		'description'=>'Tämä tagin sisältö näytetään jos toisen we-tagin name-muuttujan arvo on sama kuin tämän tagin match-muuttujan arvo. Muuttujan tyyppi voidaan määrittää.',
	),
	'ifVoteActive'=>array(
		'description'=>'Tämän tagin sisältö näytetään jos äänestyksen aika ei ole umpeutunut.',
	),
	'ifVoteIsRequired'=>array(
		'description'=>'Any content between the start- and endtag is only displayed, if the voting is a required field.',
	),
	'ifVote'=>array(
		'description'=>'Tämän tagin sisältö näytetään jos äänestys tallennettiin onnistuneesti.',
	),
	'ifvotingexists'=>array(
		'description'=>'Executes the enclosed code only, if the voting module is not deaktivated (settings dialog).',
	),
	'ifVotingFieldEmpty'=>array(
		'description'=>'Checks if a votingField is empty, the attribute combinations of name and type are the same as in the we:votingField tag',
	),
	'ifVotingFieldNotEmpty'=>array(
		'description'=>'Checks if a votingField is not empty, the attribute combinations of name and type are the same as in the we:votingField tag',
	),
	'ifVotingField'=>array(
		'description'=>'Checks if a votingField has a value corresponding to the attribute match, the attribute combinations of name and type are the same as in the we:votingField tag',
	),
	'ifVotingIsRequired'=>array(
		'description'=>'Prints the enclosed content only, if the voting field is a required field',
	),
	'ifWebEdition'=>array(
		'description'=>'Tämän tagin sisältö näytetään vain webEditionin sisällä, mutta ei julkaistussa dokumentissa.',
	),
	'ifWorkspace'=>array(
		'description'=>'Tarkistaa, sijaitseeko dokumentti työtilassa joka on määritelty `path` -attribuutissa.',
	),
	'ifWritten'=>array(
		'description'=>'Tämän tagin sisältö on käytettävissä vian jos kirjoitus dokumenttiin tai objektiin onnisui. kts. we:write -tagi.',
	),
	'img'=>array(
		'description'=>'we:img tagilla voidaan lisätä kuva dokumentin muokkaus-tilassa. Jos mitään attribuutteja ei määritetä, käytetään oletusarvoja. `showimage`:lla kuva voidaan piilottaa muokkaus-tilassa. `showinputs`:lla kuvan title- ja alt- attribuutit on pois käytöstä..',
	),
	'include'=>array(
		'description'=>'Tällä tagilla voidaan liittää webEdition dokumentti tai HTML-sivu sivupohjaan. `gethttp`:llä voidaan määrittää halutaanko liitetty tiedosto siirtää HTTP:n avulla vai ei.`seeMode`:lla määritellään onko dokumentti muokattavissa `seeModessa`.',
	),
	'input'=>array(
		'description'=>'The we:input tag creates a single-line input box in the edit mode of the document based on this template, if the type = "text" is selected. For all other types, see the manual or help.',
	),
	'js'=>array(
		'description'=>'we:js tagi luo HTML-tagin joka viittaa webEditionin sisäiseen JavaScript-dokumenttiin jonka ID on määritelty listaan. JavaScriptit voi määrittää myös erillisessä tiedostossa..',
	),
	'keywords'=>array(
		'description'=>'we:keywords -tagi luo avainsana -metatagin.  Jos `Ominaisuus` avainsana -kenttä on tyhjä, tämän tagin sisällä olevia sanoja käytetään avainsanoina. Muuten käytetään `Ominaisuus`:ssa määriteltyjä avainsanoja.',
	),
	'linklist'=>array(
		'description'=>'we:linklist -tagilla luodaan linkkilista. we:prelink -tagin sisältö tulostetaan ennen linkkiä muokkaustilassa..',
	),
	'linkToSeeMode'=>array(
		'description'=>'Tämä tagi luo linkin joka avautuu valittuun dokumenttiin `seeMode`:ssa.',
	),
	'link'=>array(
		'description'=>'we:link -tagi luo yksittäisen linkin jota voidaan muokata `muokkaa`-napilla. Jos we:link:iä käytetään we:linklist:n sisällä `nimi`-attribuuttia ei tule määritellä we:link-tagiin, muutoin kyllä. `only` -attribuuttiin voidaan määritellä attribuutti jonka linkki palauttaa, esim. `only="content"`.',
	),
	'listdir'=>array(
		'description'=>'we:listdir -tagi luo listan joka näyttää kaikki dokumentit jotka ovat samassa kansiossa. `field` -attribuutilla voidaan määritellä minkä kentän arvo näytetään. Jos attribuutti on tyhjä tai ei ole olemassa, tiedoston nimi näytetään. Minkä kentän halutaan näyttävän kansioita tulee määrittää attribuuttiin `dirfield`. Jos attribuutti on tyhjä tai sitä ei ole olemassa, `field`-kentän nimi on verrannollinen käytetyn tiedoston nimeen. Jos käytetään `id`-attribuuttia, kansion tiedostot jossa on tämä sama id näytetään.',
	),
	'listviewEnd'=>array(
		'description'=>'Tämä tagi näyttää viimeisen rivin joka on we:listview:llä.',
	),
	'listviewPageNr'=>array(
		'description'=>'Tämä tagi palauttaa tämänhetkisen we:listview -sivun.',
	),
	'listviewPages'=>array(
		'description'=>'Tämä tagi palauttaa we:listview:n sivumäärän.',
	),
	'listviewRows'=>array(
		'description'=>'Tämä tagi palauttaa löydettyjen rivien määrän we:listview:ltä.',
	),
	'listviewStart'=>array(
		'description'=>'Tämä tagi näyttää ensimmäisen rivin joka on we:listview:llä.',
	),
	'listview'=>array(
		'description'=>'we:listview -tagilla luodaan listoja jotka generoidaan automaattisesti.',
	),
	'list'=>array(
		'description'=>'we:list -tagilla voit tehdä laajennettavia listoja. Tagien sisällä oleva tieto liitetään listaan.',
	),
	'master'=>array(
		'description'=>'Used inside a template which has a master template. The content between start and endtag is inserted in the master template where we:content is defined. The link between master and content is made via the name attribute.<br/><br/>Content which is not encapsulated in a master tag is inserted in the master-template where we:conent is defined without a name.',
	),
	'metadata'=>array(
		'description'=>'The we:metadata-Tag is used to show meta data from images, flash- and quicktime movies. use the we:field tag inside the start and end tag to display the value',
	),
	'navigationEntries'=>array(
		'description'=>'we:navigationEntry type=`folder` tulostaa luotuja kansio-tyyppisiä navigaatiopisteitä.',
	),
	'navigationEntry'=>array(
		'description'=>'we:navigationEntry:llä voidaan valita tulostetaanko `folder` vai `entry`-tyyppisiä navigaatioita. Lisäattributeilla voidaan tarkentaa haluttua navigaatiotulostusta.',
	),
	'navigationField'=>array(
		'description'=>'&lt;we:navigationField&gt; is used within &lt;we:navigationEntry&gt; to print a value of the current navigation entry.<br/>Choose from <b>either</b> the attribute <i>name</i>, <b>or</b> from the attribute <i>attributes</i>, <b>or</b> from the attribute <i>complete</i>',
	),
	'navigationWrite'=>array(
		'description'=>'Is used to write a we:navigation with given name',
	),
	'navigation'=>array(
		'description'=>'Tätä tagia käytetään alustamaan navigaatio joka luodaan navigaatio työkalulla.',
	),
	'newsletterConfirmLink'=>array(
		'defaultvalue'=>'Todenna uutiskirje',
		'description'=>'Tätä tagia käytetään `double opt-in` vahvistuslinkin luomiseen.',
	),
	'newsletterField'=>array(
		'description'=>'Displays a field from the recipient dataset within the newsletter.',
	),
	'newsletterSalutation'=>array(
		'description'=>'Tätä tagia käytetään näytettäessä `puhuttelu``-kenttiä.',
	),
	'newsletterUnsubscribeLink'=>array(
		'description'=>'Luo linkin uutiskirjeen perumiseen. Tagia voidaan käyttää ainoastaan `sähköposti` sivupohjissa!',
	),
	'next'=>array(
		'description'=>'Luo linkin joka viittaa seuraaviin sivuihin we:listview:llä.',
	),
	'noCache'=>array(
		'description'=>'PHP-koodi ajetaan aina kun välimuistissa olevaa dokumenttia kutsutaan.',
	),
	'objectLanguage'=>array(
		'description'=>'Shows the language of the object',
	),
	'object'=>array(
		'description'=>'we:object:lla näytetään objekteja. Objektin kenttiä voidaan näyttää we:field -tagilla. Jos `name`-attribuutti on määritelty niin objektivalitsin näytetään muokkaustilassa josta voi valita kaikki objektit kaikista luokista. Jos `classid` on määritelty objektivalitsimella voi valita kaikki objektit tietystä luokasta. Pelkällä `id`:llä voidaan valita yksittäinen objekti..',
	),
	'orderitem'=>array(
		'description'=>'Using this tag, one can display a single item on an order on a webEdition page. Similar to the Listview or the <we:object> tag, the fields are displayed with the <we:field> tag.',
	),
	'order'=>array(
		'description'=>'Using this tag, one can display an order on a webEdition page. Similar to the Listview or the <we:object> tag, the fields are displayed with the <we:field> tag.',
	),
	'pageLanguage'=>array(
		'description'=>'Shows the language of the document',
	),
	'pagelogger'=>array(
		'description'=>'The we:pagelogger tag generates, depending on the selected "type" attribute, the necessary capture code for pageLogger or the fileserver- respectively the download-code.',
	),
	'path'=>array(
		'description'=>'The we:path tag represents the path of the current document. If there is an index file in one of the subdirectories, a link is set on the respective directory. The used index files (separated by commas) can be specified in the attribute "index". If nothing is specified there, "default.html", "index.htm", "index.php", "default. htm", "default.html" and "default.php" are used as default settings. In the attribute "home" you can specify what to put at the very beginning. If nothing is specified, "home" is displayed automatically. The attribute separator describes the delimiter between the directories. If the attribute is empty, "/" is used as delimiter. The attribute "field" defines what sort of field (files, directories) is displayed. If the field is empty or non-existent, the filename will be displayed. The attribute "dirfield" defines which field is used for display in directories. If the field is empty or non-existent, the entry of "field" or the filename is used.',
	),
	'paypal'=>array(
		'description'=>'The tag we:paypal implements an interface to the payment provider paypal. To ensure that this tag works properly, add additional parameters in the backend of the shop module.',
	),
	'position'=>array(
		'description'=>'The tag we:position is used to return the actual position of a listview, block, linklist, listdir. Is "type= block or linklist" it is necessary to specify the name (reference) of the related block/linklist. The attribute "format" determines the format of the result.',
	),
	'postlink'=>array(
		'description'=>'we:postlink tagi varmistaa, että kaikki aloitus ja lopetus -tagien välillä oleva sisältö ei näy listan viimeisellä linkillä.',
	),
	'prelink'=>array(
		'description'=>'we:prelink -tagi varmistaa, että kaikki aloitus ja lopetus -tagien välillä oleva sisältö ei näy linkkilistan ensimmäisellä linkillä.',
	),
	'printVersion'=>array(
		'description'=>'we:printVersion -tagi luo HTML linkin, joka osoittaa samaan tiedostoon mutta eri sivupohjaan. "tid" Määrittää sivupohjan id:n. Tagi linkittää kaiken alku ja lopetus -tagien välissä olevan sisällön.',
	),
	'processDateSelect'=>array(
		'description'=>'&lt;we:processDateSelect&gt; -tagi prosessoi 3 arvoa we:dateSelect tagin valintakentistä UNIX timestamp -muotoiseksi. Arvo tallennetaan globaaliksi muuttujaksi, joka on nimetty kohtaan "nimi&quuot;.',
	),
	'quicktime'=>array(
		'description'=>'we:quicktime -tagilla voit lisätä Quicktime elokuvan tiedostoon. Tähän sivupohjaan perustuvat tiedostot näyttävät muokkausnapin muokkaustilassa. Tämä napin klikkaaminen avaa tiedostohallinan, joka antaa sinun valita Quicktime elokuvan, jonka olet jo siirtäny webEditioniin. Tällä hetkellä ei ole xhtml-validia koodia, joka toimisi sekä IE:ssä että Mozillassa. Tämänvuoksi, xml on aina asetettu arvoon "epätosi"',
	),
	'registeredUser'=>array(
		'description'=>'Tämä tagi tulostaa asiakastiedot, jotka on tallennettu asiakashalllintamoduuliin.',
	),
	'registerSwitch'=>array(
		'description'=>'Tämä tagi luo kytkimen jolla voit vaihtaa rekisteröityneen ja rekisteröitymättönmän käyttäjän statuksen välillä muokkaustilassa. Jos olet käyttänyt &lt;we:ifRegisteredUser&gt; ja &lt;we:ifNotRgisteredUser&gt; -tags, tämä tagi antaa sinun katsoa eri tiloja ja pitää sisällön muotoilu kunnossa.',
	),
	'repeatShopItem'=>array(
		'description'=>'Tämä tagi näyttää kaiken ostoskorinsisällön.',
	),
	'repeat'=>array(
		'description'=>'Tämä tagin sisältö toistetaan jokaiselle löydetylle kohdalle &lt;we:listview&gt; -tagissa. Tätä tagia käytetään vain &lt;we:listview&gt; yhteydessä.',
	),
	'returnPage'=>array(
		'description'=>'Tätä tagia käytetään lähdesivuun viittavan osoitteen näyttämiseen, jos "palaa" on asetettu arvoon "tosi" käytettäessä: &lt;we:a edit="document"&gt; or &lt;we:a edit="object"&gt;',
	),
	'saferpay'=>array(
		'description'=>'we:saferpay implementoi rajapinnan saferpay maksuhallintaan. Varmistaaksesi tagin toiminnan, syötä lisätietoja Kauppamoduulin backendiin.',
	),
	'saveRegisteredUser'=>array(
		'description'=>'Tämä tagi tallentaa kaikki käyttäjätiedot, joita on syötetty istuntokenttiin.',
	),
	'search'=>array(
		'description'=>'we:search -tagi luo syöttökentän, jota käytetään hakukenttänä. Hakukentällä on sisäinen nimi "we_search". Kun hakulomake lähetetään, the PHP muuttuja "we_search" vastaanottavalla sivulla saa arvokseen hakukentän sisällön.',
	),
	'select'=>array(
		'description'=>'we:select -tagi luo valintakentän muokkaustilaan. Jos kooksi on määritelty "1" valintakenttää näytetään pudotusvalikkona. Tagi toimii samoin kuin HTML select -tagi. Aloitus ja lopetus -tagien väliin syötettään normaalit HTML option -tagit.',
	),
	'sendMail'=>array(
		'description'=>'Tämä tagi lähettää webEdition sivun sähköpostina "vastaanottaja" kohtaan määriteltyyn osoitteeseen.',
	),
	'sessionField'=>array(
		'description'=>'we:sessionField -tagi luo HTML input, select tai text area tagin. Sitä käytetään sisällön syöttämiseksi istuntokenttiin (esim. Käyttäjätieto, jne.).',
	),
	'sessionLogout'=>array(
		'description'=>'we:sessionLogout -tagi luo HTML linkki tagin joka osoittaa sisäiseen webEdition tiedostoon, jolla on webEdition Tagi velhossa mainittu ID. Jos tällä webEdition tiedostolla on we:sessionStart -tagi ja tiedosto on dynaaminen, aktiivinen istunto poistetaan ja suljetaan. Mitään tietoja ei tallenneta.',
	),
	'sessionStart'=>array(
		'description'=>'Tätä tagia käytetään istunnon aloittamiseen tai aiemman istunnon jatkamiseen. Tämä tagi vaaditaan sivupohjiin, jotka luovat seuraavan tyyppisiä sivuja: Sivuja, jotka on suojattu jollain tavoin Käyttäjähallinta moduulilla, Kauppasivuja tai sivuja jotka tukevat front end input:ia.&lt;br /&gt;Tämä tagin täytyy olla ensimmäinen tagi sivupohjan ensimmäisellä rivillä!',
	),
	'setVar'=>array(
		'description'=>'Tätä tagia käytetään muuttujien arvojen asetukseen.<br/><strong>Attention:</strong> Without the attribute <strong>striptags="true"</strong>, HTML- and PHP-Code is not filtered, this is a potenzial security risk!</strong>',
	),
	'shipping'=>array(
		'description'=>'we:shipping -tagia käytetään lähetyskulujen määrittelyyn. Nämä kulut perustuvat ostoskorin arvoon, rekisteröityneen käyttäjän kotimaahan ja lähetyskulujen määrittely sääntöjä voidaan muokata Kauppa moduulissa. Parametriin "summa" määritellään we:sum -tagin nimi. "tyyppi" -parametrilla määritellään joko lähetyskulujen tyyppi.',
	),
	'shopField'=>array(
		'description'=>'Tämä tagi tallentaa useamman sisältökentän suoraan tuotteesta tai ostoskorista. The pääkäyttäjä voi määrittää tiettyjä arvoja joista käyttäjä voi valita tai syöttää omansa arvonsa. Näin on mahdollista kartoittaa useampia toisintoja tuotteista helposti.',
	),
	'shopVat'=>array(
		'description'=>'Tätä tagia käytetään alv.:in määrittelemiseksi tuotteelle. Hallitaksesi alv. -arvoja käytä Kauppa moduulia. Annettu id tulostaa suoraan alv.:n kyseiselle tuotteelle.',
	),
	'showShopItemNumber'=>array(
		'description'=>'we:showShopItemNumber -tagi näyttää määritettyjen nimikkeiden määrän ostoskorissa.',
	),
	'sidebar'=>array(
		'defaultvalue'=>'Avaa sivupalkin',
		'description'=>'This tag display a button in the edit mode which opens an website in the sidebar',
	),
	'subscribe'=>array(
		'description'=>'Tätä tagia käytetetään yksittäisen syöttökentän luomiseksi webEdition tiedostoon, jotta käyttäjä voi antaa sähköpostiosoitteensa tilatakseen uutiskirjeen.',
	),
	'sum'=>array(
		'description'=>'we:sum -tagi yhdistää kaikki kohteet listalle.',
	),
	'target'=>array(
		'description'=>'Tätä tagia käytetään linkin kohteen (target) valitsemiseksi &lt;we:linklist&gt; -tagista.',
	),
	'textarea'=>array(
		'description'=>'we:textarea -tagi luo monirivisen sisällön syöttöalueen.',
	),
	'title'=>array(
		'description'=>'we:title -tagi luo normaalin otsikko -tagin. Jos otsikkokenttä Ominaisuudet -välilehdellä on tyhjä, käytetään vakio otsikkoa, muutoin käytetään Ominaisuudet -välilehdellä määriteltyä otsikkoa.',
	),
	'tr'=>array(
		'description'=>'&lt;we:tr&gt; -tagi vastaa HTML:n &lt;tr&gt; -tagia.',
	),
	'unsubscribe'=>array(
		'description'=>'Tätä tagia käytetetään yksittäisen syöttökentän luomiseksi webEdition tiedostoon, jotta käyttäjä voi antaa sähköpostiosoitteensa lopettaakseen uutiskirjeen tilauksen.',
	),
	'url'=>array(
		'description'=>'we:url -tagi luo sisäisen webEdition URL-osoitteen, joka osoittaa dokumenttiin, jolla on alla annettu id.',
	),
	'userInput'=>array(
		'description'=>'we:userInput -tagi luo syöttökentät, joita voidaan käyttää we:form type="document" tai type="object" yhteydessä tiedostojen tai objektien luomiseksi.',
	),
	'useShopVariant'=>array(
		'description'=>'we:shopVariant -tagi käyttää artikkelin toisinnon datan annetun nimen perusteella. Jos toisintoja annetulla nimellä ei ole, näytetään vakio artikkeli.',
	),
	'var'=>array(
		'description'=>'we:var -tagi esittää alla annettuun nimeen liittyvän tiedosto-kentän globaalin php -muuttujan sisällön.',
	),
	'votingField'=>array(
		'description'=>'The we:votingField-tag is required to display the content of a voting. The attribute "name" defines what to show. The attribute "type", how to display it. Valid name-type combinations are: question - text; result - count, percent, total; id - answer, select, radio, voting; answer - text,radio,checkbox (select multiple) select, textinput and textarea (free text answer field), image (all we:img attributes such as thumbnail are supported), media (delivers the path utilizing to and nameto);',
	),
	'votingList'=>array(
		'description'=>'Tämä tagi luo automaattisesti äänestyslistat.',
	),
	'votingSelect'=>array(
		'description'=>'Käytä tätä tagia luodaksesi alasvetovalikon; (&lt;select&gt;) äänestyksen valintaan.',
	),
	'votingSession'=>array(
		'description'=>'Generates an unique identifier which is stored in the voting log and allows to identify the answers to different questions which belong to a singele voting session',
	),
	'voting'=>array(
		'description'=>'we:voting -tagia käytetään äänestyksen luomiseen.',
	),
	'writeShopData'=>array(
		'description'=>'we:writeShopData tagi tallentaa kaikki sen hetkisten ostoskorien sisällöt tietokantaan.',
	),
	'writeVoting'=>array(
		'description'=>'Tagi tallentaa äänestyksen tietokantaan. Jos "id" on määritelty, vain äänestys, jolla on kyseinen id tallennetaan.',
	),
	'write'=>array(
		'description'=>'Tämä tagi tallentaa tiedoston tai objektin, jonka &lt;we:form type="document/object&gt; on luonut',
	),
	'xmlfeed'=>array(
		'description'=>'Tagi lataa xml sisällön annetusta url-osoitteesta',
	),
	'xmlnode'=>array(
		'description'=>'Tagi tulostaa xml-elementin annetusta syötteestä tai url-osoitteesta.',
));