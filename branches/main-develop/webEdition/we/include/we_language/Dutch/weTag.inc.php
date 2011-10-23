<?php
/** Generated language file of webEdition CMS */
$l_weTag=array(
	
	'addDelNewsletterEmail'=>array(
		'description'=>'Deze tag wordt gebuikt om een e-mail adres toe te voegen of te verwijderen uit een nieuwsbrief lijst. In het attribuut &quot;path&quot moet het complete pad naar de nieuwsbrief lijst gegeven worden. Wanneer het pad begint zonder &quot;/&quot; zal het pad voortkomen uit de DOCUMENT_ROOT. Wanneer u meerdere lijsten gerbuikt, kunt u meerdere paden opgeven, gescheiden door een komma',
	),
	'addDelShopItem'=>array(
		'description'=>'Gebruik de we:addDelShopItem tag om een artikel toe te voegen of te verwijderen uit de winkelmand.',
	),
	'addPercent'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:addPercent tag voegt een gespecificeerd percentage toe, bijvoorbeeld, BTW.',
	),
	'answers'=>array(
		'defaultvalue'=>'',
		'description'=>'Deze tag toont de reactie mogelijkheden van een peiling.',
	),
	'author'=>array(
		'description'=>'De we:author tag toont de maker van het document. Wanneer het attribuut `type` niet ingevuld is, wordt de gebruikersnaam getoont. Wanneer type=&quot;name&quot;, worden de voor- en achter naam van de gebruiker getoont. Wanneer `type=&quot;initials&quot;, worden de initialen van de gebruiker getoond. Indien er geen voor- of achter naam is ingevoerd, wordt de gebruikersnaam getoond.',
	),
	'a'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:a tag creeert een HTML link tag die refereert aan een intern webEdition document met onderstaand ID. De tag koppelt alle content tussen de start tag en de eind tag.',
	),
	'back'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:back tag creeert een HTML link tag die refereert aan de vorige we:listview pagina. De tag koppelt alle content tussen de start tag en de eind tag.',
	),
	'bannerSelect'=>array(
		'description'=>'Deze tag toont een uitklap menu (&lt;select&gt;), voor het selecteren van banners. Als de Klanten Beheer Module is geïnstalleerd en het attribuut klant heeft als waarde ja, dan worden alleen banners van de ingelogde klant getoond.',
	),
	'bannerSum'=>array(
		'description'=>'De we:bannerSum tag toont het aantal getoonde, bezochte banners of het aantal bezoeken. De tag werkt alleen binnen een listview met type=&quot;banner&quot;',
	),
	'banner'=>array(
		'description'=>'Gebruik de we:banner tag om een banner in te voegen vanuit de Banner/Statistieken Module.',
	),
	'block'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:block tag geeft de mogelijkheid om uitbreidbare blokken/lijsten aan te maken. Alles binnen de start en eind tag wordt herhaald (elke HTML en bijna alle we:tags), wanneer u op de plus knop drukt in de edit modus.',
	),
	'calculate'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:calculate tag staat allerlei soorten wiskundige berekeningen toe.(*, /, +, -,(), sqrt.....)',
	),
	'captcha'=>array(
		'description'=>'Deze tag genereert een afbeelding met een random code.',
	),
	'categorySelect'=>array(
		'defaultvalue'=>'',
		'description'=>'Deze tag wordt gebruik om een uitklapmenu (&lt;select&gt;) in een webEdition document in te voegen. Gebruik deze tag om een categorie te selecteren. Door de eind tag direct achter de begin tag te plaatsen, zal het uitklapmenu alle, in webEdition gedefinieerde, categorieën bevatten.',
	),
	'category'=>array(
		'description'=>'De we:category tag wordt vervangen door de categorie (of categorieën) die is / zijn toegekend aan het document in de eigenschappen venster. Als er meer categorieën zijn toegekend, gebruik dan een komma als scheidingsteken. Als u gebruik wenst te maken van een ander scheidingsteken, dan moet u die specificeren door middel van het `tokken` attribuut. Bijvoorbeeld: tokken=`&nbsp;` (in dit geval wordt er een spatie gebruikt om categorieën te scheiden).',
	),
	'charset'=>array(
		'defaultvalue'=>'',
		'description'=>'De we_charset tag genereert een meta tag die de karakterset voor de pagina bepaald. `ISO-8859-1` is gebruikelijk voor Nederlandse webpagina`s. Deze tag moet binnen de meta tag van de HTML pagina worden geplaatst.',
	),
	'checkForm'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:checkForm tag valideert de invoer van een formulier mbv. JavaScript. <br/> De combinatie van de parameters `match` en `type` bepalen de `name` of het `id` van het te conroleren formulier. <br/> `mandatory` en `email` bevatten een komma gescheiden lijst van verplichte velden of e-mailvelden. In `password` is het mogelijk om 2 veldnamen en een minimum lengte van ingevoerde wachtwoorden te bepalen.<br/> Met `onError` kunt u de naam van een individuele JavaScript functie kiezen, die wordt aangeroepen in het geval van een fout. Deze functie geeft een opsomming en een markering van de ontbrekende verplichte velden en e-mailvelden, indien het wachtwoord juist is. Als `onError` niet is gedefinieerd of de functie bestaat niet dan wordt de standaard waarde weergegeven in een dialoog venster.',
	),
	'colorChooser'=>array(
		'description'=>'De we:colorChooser tag maakt een invoerveld aan, waarmee een kleur gekzoen kan worden.',
	),
	'comment'=>array(
		'description'=>'The comment Tag is used to generate explicit comments in the specified language, or, to add comments to the template which are not delivered to the user browser.',
	),
	'conditionAdd'=>array(
		'description'=>'Deze tag wordt gebruikt om een nieuwe regel of conditie aan te maken binnen een &lt;we:condition&gt; block.',
	),
	'conditionAnd'=>array(
		'description'=>'Deze tag wordt gebruikt om condities toe te voegen binnen een &lt;we:condition&gt;. Dit is een logische AND, wat betekent dat aan beide bestaande condities moet worden voldaan.',
	),
	'conditionOr'=>array(
		'description'=>'Deze tag wordt gebruikt om condities toe te voegen binnen een a &lt;we:condition&gt;. Dit is een logische OR, wat betekent dat aan één van de twee condities moet worden voldaan.',
	),
	'condition'=>array(
		'defaultvalue'=>'&lt;we:conditionAdd field="Type" var="type" compare="="/&gt;',
		'description'=>'Deze tag wordt gebruikt in combinatie met &lt;we:conditionAdd&gt; om in een &lt;we:listview type=`object`&gt; dynamisch een voorwaarde toe te voegen aan het attribuut `condition` . Voorwaarden kunnen ingenesteld worden.',
	),
	'content'=>array(
		'description'=>'&lt;we:content /&gt; wordt alleen gebruikt binnen een hoofdsjabloon. Dit bepaalt de plek waar de content van het sjabloon wordt gebruikt in het hoofdsjabloon.',
	),
	'controlElement'=>array(
		'description'=>'De tag we:controlElement kan controle elementen beïnvloeden in het edit venster van een document. Knoppen kunnen worden verborgen. Checkboxen kunnen uitgeschakeld, aangevinkt en/of verborgen worden.',
	),
	'cookie'=>array(
		'description'=>'Deze tag is vereist binnen de Peiling module en stelt een cookie in, welke ervoor zorgt dat een gebruiker slechts één keer kan stemmen. De tag moet aan het begin vna het sjabloon geplaatst worden. Er mogen geen breaks of spaties zijn voor deze tag.',
	),
	'createShop'=>array(
		'description'=>'De we:createShop tag is vereist voor iedere pagina die winkel data bevat.',
	),
	'css'=>array(
		'description'=>'De we:css tag genereert een HTML tag die refereert aan een intern webEdition CSS stylesheet met onderstaand ID. U kunt stylesheets in een apart bestand definiëren.',
	),
	'customer'=>array(
		'defaultvalue'=>'',
		'description'=>'Using this tag, data from any customer can be displayed. The customer data are displayed as in a listview or within the &lt;we:object&gt; tag with the tag &lt;we:field&gt;.<br /><br />Combining the attributes, this tag can be utilized in three ways:<br/>If name is set, the editor can select a customer by using a customer-select-Field. This customer is stored in the document within the field name.<br />If name is not set but instead the id, the customer with this id is displayed.<br />If neither name nor id is set, the tag expects the id of the customer by a request parameter. This is i.e. used by the customer-listview when the attribut hyperlink="true" in the &lt;we:field&gt; tag is used. The name of the request parameter is we_cid.',
	),
	'dateSelect'=>array(
		'description'=>'De we:dateSelect tag geeft een keuzeveld weer voor data, welke gebruikt kunnen worden in combinatie met de we:processDateSelect tag bij het uitlezen van de datum gegevens naar een variabele zoals een UNIX tijdstempel.',
	),
	'date'=>array(
		'description'=>'De we:date tag geeft de huidige datum weer op een pagina volgens de ingevoerde specificaties in onderstaand `format` veld. Als het een statische pagina betreft, kiest u bij type `js`, zodat de datum gegeneerd wordt d.m.v. JavaScript.',
	),
	'deleteShop'=>array(
		'description'=>'De we:deleteShop tag verwijdert de volledige winkelmand.',
	),
	'delete'=>array(
		'description'=>'De we:delete tag wordt gebruikt om webEdition documenten via &lt;we:a edit=`document` delete=`true`&gt; of &lt;we:a edit=`object` delete=`true`&gt; te verwijderen.',
	),
	'description'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:description tag genereert de HTML meta tag `omschrijving`. Als het omschrijvingsveld in het Eigenschappen venster leeg is, dan zal de inhoud tussen de begin en eind tag worden gebruikt als standaard omschrijving.',
	),
	'DID'=>array(
		'description'=>'Deze tag stuurt het ID terug van een webEdition document.',
	),
	'docType'=>array(
		'description'=>'Deze tag stuurt het document type terug van een webEdition document.',
	),
	'else'=>array(
		'description'=>'Deze tag wordt gebruikt om alternatieve condities toe te voegen binnen een if-type tag bijv. &lt;we:ifEditmode&gt;, &lt;we:ifNotVar&gt;, &lt;we:ifNotEmpty&gt;, &lt;we:ifFieldNotEmpty&gt;',
	),
	'field'=>array(
		'description'=>'De we:field tag voegt de inhoud van het veld met de naam gedefinieerd in het attribuut `name` in. Het kan alleen gebruikt worden tussen de begin en eind tag van we:repeat.',
	),
	'flashmovie'=>array(
		'description'=>'Met de we:flashmovie tag is het mogelijk een Flash film in een document in te voegen. Documenten die gebaseerd zijn op dit sjabloon, bevatten in de wijzig modus een wijzig knop. Wanneer u op deze knop drukt zal er een venster openen, waarbinnen u een Flash film kan kiezen die zich reeds binnen webEdition bevindt.',
	),
	'formfield'=>array(
		'description'=>'De we:formfield tag wordt gebruikt om een veld te generen aan de voorkant van de site.',
	),
	'formmail'=>array(
		'description'=>'With activated Setting Call Formmail via webEdition document, the integration of the formmail script is realized with a webEdition document. For this, the (currently without attributes) we-Tag formmail will be used. <br />Indien de Captcha-controle gebruitk wordt, bevind &lt;we:formmail/&gt; zich binnen de we-Tag ifCaptcha.',
	),
	'form'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:form tag wordt gebruikt voor zoek en e-mail formulieren. Het werkt hetzelfde als de normale HTML formulier tag, maar geeft de parser de mogelijkheid om extra verborgen velden toe te voegen.',
	),
	'hidden'=>array(
		'description'=>'De we:hidden tag creëert een verborgen input tag die de globale PHP variabelen met dezelfde naam bevat. Gebruik deze tag als u inkomende variabelen wilt doorsturen.',
	),
	'hidePages'=>array(
		'description'=>'De we:hidePages tag maakt het mogelijk om sommige modi van een document uit te schakelen. Deze tag kunt u bijvoorbeeld gebruiken om de toegang tot het Eigenschappen venster van een document te blokkeren. In dit geval is het niet mogelijk om document eigenschappen te wijzigen.',
	),
	'href'=>array(
		'description'=>'De we:href tag maakt een URL aan die in de wijzig modus kan worden ingevoerd.',
	),
	'icon'=>array(
		'description'=>'De we:icon tag creëert een HTML tag die refereert aan een intern webEdition icoon met onderstaand ID. Hiermee kunt u een icoon bijvoegen die getoond wordt in Internet Explorer, Mozilla, Sarafi and Opera bij het bookmarken van uw homepage.',
	),
	'ifBack'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:if_back tag wordt gebruikt tussen de begin en de eind tags van &lt;we:listview&gt;. Alles binnen de begin en de eind tags van deze tag wordt getoond als er een `vorige` pagina is. Bijv. U kunt de tag gebruiken op de tweede pagina van een listview met 20 onderdelen, en bijv. 5 onderdelen per pagina.',
	),
	'ifbannerexists'=>array(
		'defaultvalue'=>'',
		'description'=>'Executes the enclosed code only, if the banner module is not deaktivated (settings dialog).',
	),
	'ifCaptcha'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen weergegeven indien de juiste code is ingevoerd door de gebruiker.',
	),
	'ifCat'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifCat tag zorgt ervoor dat alles wat zich tussen de begin tag en de eind tag bevindt alleen getoond wordt als één of meer van de onder `categories` ingevoerde categorieën de document categorieën zijn.',
	),
	'ifClient'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifClient tag zorgt ervoor dat alles wat zich tussen de begin tag en de eind tag bevindt alleen getoond wordt als de client (browser) zich meet met de gevestigde standaards. Deze tag werkt alleen met dynamisch bewaarde pagina`s!',
	),
	'ifConfirmFailed'=>array(
		'defaultvalue'=>'',
		'description'=>'Bij gebruik van DoubleOptIn met de nieuwsbrief module, controleert &lt;we:ifConfirmFailed&gt; of het e-mailadres bevestigd is.',
	),
	'ifCurrentDate'=>array(
		'defaultvalue'=>'',
		'description'=>'Deze tag belicht de huidige dag binnen een kalender listview.',
	),
	'ifcustomerexists'=>array(
		'defaultvalue'=>'',
		'description'=>'Executes the enclosed code only, if the customer module is not deaktivated (settings dialog).',
	),
	'ifDeleted'=>array(
		'defaultvalue'=>'',
		'description'=>'Content binnen de begin tag en de eind tag wordt alleen getoond als een specifiek document of object verwijderd is met gebruik van &lt;we:delete/&gt;',
	),
	'ifDoctype'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifDocType tag zorgt ervoor dat alles wat zich tussen de begin tag en de eind tag bevindt alleen getoond wordt als het onder `doctype` ingevoerde document type hetzelfde is als het document type van het document.',
	),
	'ifDoubleOptIn'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond tijdens het eerste deel van een double opt-in proces.',
	),
	'ifEditmode'=>array(
		'defaultvalue'=>'',
		'description'=>'Deze tag wordt gebruikt om content binnen deze tags alleen te tone in de edit mode.',
	),
	'ifEmailExists'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien een gespecificeerd e-mailadres zich in de nieuwsbrief adreslijst bevind.',
	),
	'ifEmailInvalid'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag is alleen zichtbaar indien een specifiek e-mailadres niet correct is.',
	),
	'ifEmailNotExists'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien het e-mailadres zich niet in de nieuwsbrief adreslijst bevind.',
	),
	'ifEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifEmpty tag zorgt ervoor dat alles wat zich tussen de begin tag en de eind tag bevindt alleen getoond wordt als het veld met dezelfde naam als ingevoerd onder `match` leeg is. Het type veld moet gespecificeerd worden in het attribuut `type`, als het een `img`, `flashmovie` of `href` veld is.',
	),
	'ifEqual'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifEqual tag vergelijkt de content van de velden `name` en `eqname`. Als de content van beide velden hetzelfde is, wordt alles tussen de begin en eind tag getoond. Als de tag gebruikt wordt in we:list, we:block of we:linklist, kan slechts één veld binnen deze tags vergeleken met één veld erbuiten. In dit geval moet u het attribuut `name` instellen op de naam van het veld binnen de we:block, we:list of we:linklist-tags. Het attribuut `eqname` moet dan ingesteld worden op de naam van een veld buiten deze tags. De tag kan ook geplaatst worden in dynamisch ingevoegde webEdition pagina`s. In dit geval wordt `name` ingesteld op een veld binnen de bijgevoegde pagina en `eqname` wordt ingesteld op de naam van een veld in de hoofd pagina. Als het attribuut `value` ingevuld is, wordt `eqname` genegeerd en wordt de content van het veld `name` vergeleken met de waarde ingevuld in het attribuut `value`.',
	),
	'ifFemale'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien de gebruiker bij aanhef selectbox vrouw selecteert.',
	),
	'ifFieldEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifFieldEmpty tag zorgt ervoor dat alles wat zich tussen de begin tag en de eind tag bevindt alleen getoond wordt als het lijstweergave veld met dezelfde naam als opgegeven in `match` leeg is. Het type veld moet gespecificeerd worden in het attribuut `type` als het een `img`, `flashmovie` of `href` veld is.',
	),
	'ifFieldNotEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifFieldNotEmpty tag zorgt ervoor dat alles wat zich tussen de begin tag en de eind tag bevindt alleen getoond wordt als het lijstweergave veld met dezelfde naam als opgegeven in `match` niet leeg is. Het type veld moet gespecificeerd worden in het attribuut `type` als het een `img`, `flashmovie` of `href` veld is.',
	),
	'ifField'=>array(
		'defaultvalue'=>'',
		'description'=>'Deze tag wordt gebruikt tussen de begin- en eind tag van we:repeat. Alles binnen de begin- en eind tags wordt alleen getoond indien de waarde van het attribuut "match" gelijk is aan de waarde van het database veld van de listview invoer.',
	),
	'ifFound'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien er documenten gevonden worden binnen een &lt;we:listview&gt;.',
	),
	'ifHasChildren'=>array(
		'defaultvalue'=>'',
		'description'=>'Binnen de &lt;we:repeat&gt; tag wordt &lt;we:ifHasChildren&gt; gebruikt om op te vragen of een categorie(map) child categorieën heeft.',
	),
	'ifHasCurrentEntry'=>array(
		'defaultvalue'=>'',
		'description'=>'we:ifHasCurrentEntry kan gebruikt worden binnen we:navigationEntry type="folder" om alleen content te tonen indien de navigatie map de actieve invoer bevat.',
	),
	'ifHasEntries'=>array(
		'defaultvalue'=>'',
		'description'=>'we:ifHasEntries kan gebruikt worden binnen we:navigationEntry om alleen content te tonen indien de navigatie invoer gegevens bevat.',
	),
	'ifHasShopVariants'=>array(
		'defaultvalue'=>'',
		'description'=>'De tag we:ifHasShopVariants kan content tonen afhankelijk van het bestaan van varianten in een object of document. Hiermee kan geregeld worden of een &lt;we:listview type="shopVariant"&gt; getoond moet worden. <b>This tag works in document and object templates attached by object-workspaces, but not inside the we:listview and we:object - tags</b>',
	),
	'ifHtmlMail'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien het nieuwsbrief formaat HTML is.',
	),
	'ifIsDomain'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:iflsDomain tag zorgt ervoor dat alles wat zich tussen de begin tag en de eind tag bevindt alleen getoond wordt als de domein-naam van de server hetzelfde is als opgegeven in `domain`. Het resultaat kan alleen bekeken worden in de eigenlijke website of in de voorvertoning. In de Wijzig modus wordt alles getoond.',
	),
	'ifIsNotDomain'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:iflsNotDomain tag zorgt ervoor dat alles wat zich tussen de begin tag en de eind tag bevindt alleen getoond wordt als de domein-naam van de server niet hetzelfde is als opgegeven in `domain`. Het resultaat kan alleen bekeken worden in de eigenlijke website of in de voorvertoning. In de Wijzig modus wordt alles getoond.',
	),
	'ifLastCol'=>array(
		'defaultvalue'=>'',
		'description'=>'&lt;we:ifLastCol&gt; kan de laatste kolom detecteren van een tabel rij bij gebruik van de tabel functies van een &lt;we:listview&gt;',
	),
	'ifLoginFailed'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien het inloggen is mislukt.',
	),
	'ifLogin'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if a login occured on the page and allows for initialisation.',
	),
	'ifLogout'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if a logout occured on the page and allows for cleaning up.',
	),
	'ifMailingListEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien de gebruiker geen nieuwsbrief heeft geselecteerd.',
	),
	'ifMale'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien de gebruiker mannelijk is. Deze tag wordt gebruikt voor de aanhef in nieuwsbrieven.',
	),
	'ifnewsletterexists'=>array(
		'defaultvalue'=>'',
		'description'=>'Executes the enclosed code only, if the newsletter module is not deaktivated (settings dialog).',
	),
	'ifNewsletterSalutationEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is empty.',
	),
	'ifNewsletterSalutationNotEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is not empty.',
	),
	'ifNew'=>array(
		'defaultvalue'=>'',
		'description'=>'omsloten door deze tag wordt alleen getoond in een nieuw webEdition document of object.',
	),
	'ifNext'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien er een volgende pagina met items beschikbaar is in een &lt;we:listview&gt;',
	),
	'ifNoJavaScript'=>array(
		'description'=>'De we:ifNoJavaScript tag creëert een HTML tag die refereert aan een intern webEdition document met onderstaand ID.  Deze tag kan alleen gebruikt worden tussen de &lt;head&gt; tags van een sjabloon.',
	),
	'ifNotCaptcha'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien de door de gebruiker ingevoerde code onjuist is.',
	),
	'ifNotCat'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifNotCat tag ensures that everything located between the start tag and the end tag is only displayed if the categories which are entered under "categories" are none of the document`s categories.',
	),
	'ifNotDeleted'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond als een webEdition document of object niet verwijderd kon worden door middel van &lt;we:delete/&gt;',
	),
	'ifNotDoctype'=>array(
		'defaultvalue'=>'',
		'description'=>'',
	),
	'ifNotEditmode'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt niet getoond in de edit mode.',
	),
	'ifNotEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifNotEmpty zorgt ervoor dat alles wat zich tussen de begin tag en de eind tag bevindt alleen getoond wordt als het als het lijstweergave veld met dezelfde naam als opgegeven in `match` niet leeg is. Het type veld moet gespecificeerd worden in het attribuut `type`, als het een `img`, `flashmovie` of `href` veld is.',
	),
	'ifNotEqual'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifNotEqual tag vergelijkt de content van de velden `name` en `eqname`. Als de content van beide velden hetzelfde is, wordt alles tussen de begin en eind tag niet getoond. Als de tag gebruikt wordt in we:list, we:block of we:linklist, kan slechts één veld binnen deze tags vergeleken met één veld erbuiten. In dit geval moet u het attribuut `name` instellen op de naam van het veld binnen de we:block, we:list of we:linklist-tags. Het attribuut `eqname` moet dan ingesteld worden op de naam van een veld buiten deze tags. De tag kan ook geplaatst worden in dynamisch ingevoegde webEdition pagina`s. In dit geval wordt `name` ingesteld op een veld binnen de bijgevoegde pagina en `eqname` wordt ingesteld op de naam van een veld in de hoofd pagina. Als het attribuut `value` ingevuld is, wordt `eqname` genegeerd en wordt de content van het veld `name` vergeleken met de waarde ingevuld in het attribuut `value`.',
	),
	'ifNotField'=>array(
		'defaultvalue'=>'',
		'description'=>'Deze tag wordt gebruikt tussen de begin- en eind tag van een we:repeat. Alles tussen de begin- en eind tags wordt alleen getoond als de waarde van het attribuut "match" niet gelijk is aan het database veld van de listview invoer.',
	),
	'ifNotFound'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien er niks gevonden is door een &lt;we:listview&gt;.',
	),
	'ifNotHasChildren'=>array(
		'defaultvalue'=>'',
		'description'=>'Within the &lt;we:repeat&gt; tag &lt;we:ifNotHasChildren&gt; is used to query if a category(folder) has child categories.',
	),
	'ifNotHasCurrentEntry'=>array(
		'defaultvalue'=>'',
		'description'=>'we:ifNotHasCurrentEntry can be used within we:navigationEntry type="folder" to show some content, only if the navigation folder does not contain the activ entry',
	),
	'ifNotHasEntries'=>array(
		'defaultvalue'=>'',
		'description'=>'we:ifNotHasEntries can be used within we:navigationEntry to show content only, if the navigation entry does not contain entries.',
	),
	'ifNotHasShopVariants'=>array(
		'defaultvalue'=>'',
		'description'=>'The tag we:ifHasShopVariants can display content depending on the existance of variants in an object or document. With this, it can be controlled whether a &lt;we:listview type="shopVariant"&gt; should be displayed at all or some alternative.',
	),
	'ifNotHtmlMail'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond in een HTML nieuwsbrief document.',
	),
	'ifNotNewsletterSalutation'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is not equal to match.',
	),
	'ifNotNew'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond in een oud webEdition document of object.',
	),
	'ifNotObjectLanguage'=>array(
		'defaultvalue'=>'',
		'description'=>'The tag we:ifNotObjectLanguage tests on the language setting in the properties tab of the object, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages',
	),
	'ifNotObject'=>array(
		'defaultvalue'=>'',
		'description'=>'De omsloten content wordt alleen getoond indien de invoer binnen &lt;we:listview type="search"&gt; geen object is.&lt;br /&gt;',
	),
	'ifNotPageLanguage'=>array(
		'defaultvalue'=>'',
		'description'=>'The tag we:ifNotPageLanguage tests on the language setting in the properties tab of the document, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages',
	),
	'ifNotPosition'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifNotPosition tag geeft de mogelijkheid om een actie te definiëren welke niet uitgevoerd wordt op een bepaalde positie van een block, een listview, een linklist of een listdir. De parameter "position"  kan veelzijdige waardes aan voor het bepalen van de eerste-, laatste-, alle even-, alle oneven- of een specifieke positie (1,2,3, ...). Wanneer  "type= block or linklist" is het noodzakelijk de naam te specificeren (referentie) van de gerelateerde block/linklist.',
	),
	'ifNotRegisteredUser'=>array(
		'defaultvalue'=>'',
		'description'=>'Controleert of een gebruiker zich niet geregistreerd heeft.',
	),
	'ifNotReturnPage'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond na aanmaak/aanpassing en als de return waarde "return" van &lt;we:a edit="true"&gt; is "false" id niet ingesteld.',
	),
	'ifNotSearch'=>array(
		'defaultvalue'=>'',
		'description'=>'Door instellen van de  &lt;we:ifNotSearch&gt;-tag wordt de content tussen de begin- en eind tag alleen getoond wanneer er geen zoekterm verzonden is door &lt;we:search&gt; of leeg was. Als het attribuut &quot;set&quot; ingesteld is op &quot;true&quot;, wordt alleen de variabele `request` van &lt;we:search&gt; gevalideerd.',
	),
	'ifNotSeeMode'=>array(
		'defaultvalue'=>'',
		'description'=>'Deze tag wordt gebruikt om de omsloten content alleen te tonen buiten de seeMode.',
	),
	'ifNotSelf'=>array(
		'defaultvalue'=>'',
		'description'=>'De  we:ifNotSelf tag zorgt ervoor dat alles wat zich tussen de begin tag en de eind tag bevindt niet getoond wordt als het document ID is ingevoerd in de tag. Als de tag zich niet bevindt binnen de we:linklist of we:listdir tags, is `id` een vereist veld!',
	),
	'ifNotSendMail'=>array(
		'defaultvalue'=>'',
		'description'=>'Checks if a page is currently sent by we:sendMail and allows to exclude or include contents to the sent page',
	),
	'ifNotShopField'=>array(
		'description'=>'Everything between the start and end tags of this tag is displayed only if the value of the attribut "match" is not identical with the value of the shopField',
	),
	'ifNotSidebar'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag is used to display the enclosed contents only if the opened document is not located within the Sidebar.',
	),
	'ifNotSubscribe'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien een inschrijving niet succesvol is afgerond. Deze tag komt voor in een sjabloon (voor inschrijven van nieuwsbrieven) na &lt;we:addDelNewsletterEmail&gt;.',
	),
	'ifNotTemplate'=>array(
		'defaultvalue'=>'',
		'description'=>'Show enclosed content only if the current document is not based on the given template.<br /><br />You`ll find further information in the reference of the tag we:ifTemplate.',
	),
	'ifNotTop'=>array(
		'defaultvalue'=>'',
		'description'=>'De omsloten content wordt alleen getoond indien deze tag zich bevind in een ingevoegd document.',
	),
	'ifNotUnsubscribe'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond indien een verzoek voor inschrijving niet verloopt als plan. Deze tag moet geplaatst worden in het sjabloon (voor uitschrijving) na een &lt;we:addDellnewsletterEmail&gt;.',
	),
	'ifNotVarSet'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond als de variabele `name` niet ingesteld is. Let op: &quot;Not set&quot; is niet hetzelfde als &quot;empty&quot;!',
	),
	'ifNotVar'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifNotVar tag zorgt ervoor dat alles wat zich binnen de begin en de eind tag niet zichtbaar is als de variabele `name` dezelfde waarde heeft als ingevoerd onder `match`. Het type variabele kan gespecificeerd worden in het attribuut `type`.',
	),
	'ifNotVoteActive'=>array(
		'defaultvalue'=>'',
		'description'=>'Any content between the start- and endtag is only displayed, if the voting has expired.',
	),
	'ifNotVoteIsRequired'=>array(
		'defaultvalue'=>'',
		'description'=>'Any content between the start- and endtag is only displayed, if the voting ist not required to be filled out.',
	),
	'ifNotVote'=>array(
		'defaultvalue'=>'',
		'description'=>'Alels tussen de begin- en eind tag wordt alleen getoond indien de peiling niet bewaard is. Het attribuut type specificeert het soort fout.',
	),
	'ifNotVotingField'=>array(
		'description'=>'Checks if a votingField has not a value corresponding to the attribute match, the attribute combinations of name and type are the same as in the we:votingField tag',
	),
	'ifNotVotingIsRequired'=>array(
		'description'=>'Prints the enclosed content only, if the voting field is a required field',
	),
	'ifNotWebEdition'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag is alleen zichtbaar buiten webEdition.',
	),
	'ifNotWorkspace'=>array(
		'defaultvalue'=>'',
		'description'=>'Controleert of het document zich NIET bevind in de workspace gespecificeerd in "path".',
	),
	'ifNotWritten'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond als er een fout optreed tijdens het schrijven van een webEdition document of object met gebruik van de &lt;we:write&gt; tag.',
	),
	'ifObjectLanguage'=>array(
		'defaultvalue'=>'',
		'description'=>'The tag we:ifObjectLanguage tests on the language setting in the properties tab of the object, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages',
	),
	'ifObject'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond als de individuele invoer gevonden door &lt;we:listview type="search"&gt; een object is.',
	),
	'ifobjektexists'=>array(
		'defaultvalue'=>'',
		'description'=>'Executes the enclosed code only, if the object module is not deaktivated (settings dialog).',
	),
	'ifPageLanguage'=>array(
		'defaultvalue'=>'',
		'description'=>'The tag we:ifPageLanguage tests on the language setting in the properties tab of the document, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages',
	),
	'ifPosition'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifPosition tag geeft de mogelijkheid om de positie van blocks, listviews, linklists or listdirs te bepalen. De parameter "position"  kan veelzijdige waardes aan voor het bepalen van de eerste-, laatste-, alle even-, alle oneven- of een specifieke positie (1,2,3, ...). Wanneer  "type= block or linklist" is het noodzakelijk de naam te specificeren (referentie) van de gerelateerde block/linklist.',
	),
	'ifRegisteredUserCanChange'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond als een geregistreerde gebruiker die is ingelogd, toestemming heeft om het huidige webEdition document of object te wijzigen.',
	),
	'ifRegisteredUser'=>array(
		'defaultvalue'=>'',
		'description'=>'Controleert of een gebruiker geregistreerd is.',
	),
	'ifReturnPage'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond nadat een webEdition document of object is aangemaakt of aangepast en het teruggestuurde resultaat "return" vanaf &lt;we:a edit="document"&gt; of &lt;we:a edit="object"&gt; is "true".',
	),
	'ifSearch'=>array(
		'defaultvalue'=>'',
		'description'=>'Door instellen van de  &lt;we:ifSearch&gt;-tag wordt de content tussen de begin- en eind tag alleen getoond wanneer er een zoekterm verzonden is door &lt;we:search&gt; en niet leeg is. Als het attribuut &quot;set&quot; ingesteld is op &quot;true&quot;, wordt alleen de variabele `request` van &lt;we:search&gt; gevalideerd.',
	),
	'ifSeeMode'=>array(
		'defaultvalue'=>'',
		'description'=>'Deze tag wordt gebruikt om de omsloten content alleen te tonen in de seeMode.',
	),
	'ifSelf'=>array(
		'defaultvalue'=>'',
		'description'=>'De  we:ifSelf tag zorgt ervoor dat alles wat zich tussen de begin tag en de eind tag bevindt alleen getoond wordt als het document ID is ingevoerd in de tag. Als de tag zich niet bevindt binnen de we:linklist of we:listdir tags, is `id` een vereist veld!',
	),
	'ifSendMail'=>array(
		'defaultvalue'=>'',
		'description'=>'Checks if a page is currently sent by we:sendMail and allows to exclude or include contents to the sent page',
	),
	'ifShopEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Alles tussen de begin- en eind tag wordt getoond als de winkelmand leeg is.',
	),
	'ifshopexists'=>array(
		'defaultvalue'=>'',
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
		'defaultvalue'=>'',
		'description'=>'Alles tussen de begin- en eind tag wordt getoond als de winkelmand niet leeg is.',
	),
	'ifShopPayVat'=>array(
		'defaultvalue'=>'',
		'description'=>'De omsloten content wordt alleen getoond als een ingelogde gebruiker BTW moet betalen.',
	),
	'ifShopVat'=>array(
		'defaultvalue'=>'',
		'description'=>'we:ifShopVat controleert de BTW van het artikel (document/ winkelwagen). De parameter ID geeft de mogelijkheid om de BTW van een artikel te controleren a.d.h.v. het opgegeven ID.',
	),
	'ifSidebar'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag is used to display the enclosed contents only if the opened document is located within the Sidebar.',
	),
	'ifSubscribe'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond als een inschrijving van een nieuwsbrief succesvol is afgerond. Deze tag moet geplaatst worden in een inschrijvings sjabloon na een &lt;we:addDelnewsletterEmail&gt; tag.',
	),
	'ifTdEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if a table cell is empty (has no contents in a listview).',
	),
	'ifTdNotEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if a table cell is not empty (has contents in a listview).',
	),
	'ifTemplate'=>array(
		'defaultvalue'=>'',
		'description'=>'',
	),
	'ifTop'=>array(
		'defaultvalue'=>'',
		'description'=>'De omsloten content wordt alleen getoond als deze tag zicht niet bevind in een ingevoeegs document.',
	),
	'ifUnsubscribe'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond als een uitschrijving van een nieuwsbrief succesvol is afgerond. Deze tag moet geplaatst worden in een uitschrijvings sjabloon na een &lt;we:addDelnewsletterEmail&gt; tag.',
	),
	'ifUserInputEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag is alleen zichtbaar indien het doelgebruikers invoer veld leeg is.',
	),
	'ifUserInputNotEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag is alleen zichtbaar indien het doelgebruikers invoer veld niet leeg is.',
	),
	'ifVarEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag is alleen zichtbaar indien de variabele genoemd in het attribuut `match` leeg is.',
	),
	'ifVarNotEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag is alleen zichtbaar indien de variabele genoemd in het attribuut `match` niet leeg is.',
	),
	'ifVarSet'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag is alleen zichtbaar wanneer de doel variabele opgegeven is. Let op: &quot;Set&quot; is niet hetzelfde als &quot;not empty&quot;!',
	),
	'ifVar'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:ifVar tag zorgt ervoor dat alles wat zich binnen de begin en de eind tag alleen zichtbaar is indien de variabele `name` dezelfde waarde heeft als ingevoerd onder `match`. Het type variabele kan gespecificeerd worden in het attribuut `type`.',
	),
	'ifVoteActive'=>array(
		'defaultvalue'=>'',
		'description'=>'Alle content tussen de begin- en eind tag wordt alleen getoond indien de peiling niet verlopen is.',
	),
	'ifVoteIsRequired'=>array(
		'defaultvalue'=>'',
		'description'=>'Any content between the start- and endtag is only displayed, if the voting is a required field.',
	),
	'ifVote'=>array(
		'defaultvalue'=>'',
		'description'=>'Alles tussen de begin- en eind tag wordt alleen getoond indien de peiling succesvol is bewaard.',
	),
	'ifvotingexists'=>array(
		'defaultvalue'=>'',
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
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag wordt alleen getoond binnen webEdition, maar niet op het uiteindelijke document. Deze tag wordt gebruikt voor gebruikers meldingen, etc.',
	),
	'ifWorkspace'=>array(
		'defaultvalue'=>'',
		'description'=>'Controleert of een document zich bevind in de workspace gespecificeerd in "path" or "id".',
	),
	'ifWritten'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten door deze tag is alleen beschikbaar indien het schrijf proces van een webEdition document of object succesvol was. Zie &lt;we:write&gt;.',
	),
	'img'=>array(
		'description'=>'De we:img tag is vereist om een afbeelding te plaatsen in de content van de pagina. In de Wijzig modus is een wijzig knop zichtbaar. Wanneer u op de knop drukt opent de bestandsmanager, waarmee u een afbeelding kunt selecteren binnen webEdition. Als de attributen `width`, `height`, `border`, `hspace`, `vspace`, `alt`, of `align` zijn ingesteld, worden deze gebruikt voor de afbeelding. Anders zijn de opgegeven instellingen van kracht. Als het attribuut ID is ingesteld, wordt de afbeelding gebruikt met dit ID, indien er geen andere afbeelding is geselecteerd. Het attribuut `showimage` geeft de mogelijkheid om de afbeelding te verbergen in de Wijzig modus, slechts de aanpas knoppen zijn zichtbaar. Met `showinputs` kunnen de invoer velden voor `alt` en `titel` gedeactiveerd worden.',
	),
	'include'=>array(
		'description'=>'De we:include tag geeft u de mogelijkheid om een webEdition document of een HTML pagina bij te voegen in het sjabloon. Dit is vooral handig in het geval van navigatie of onderdelen die op meerdere sjablonen terugkeren. Als u met de we:include tag werkt hoeft u niet in elk sjabloon de navigatie aan te passen. Het wijzigen van het bijgevoegde document volstaat. Naderhand hoeft u alleen een `heropbouw` uit te voeren en alle pagina`s worden automatisch aangepast. Indien al uw pagina`s dynamisch zijn hoeft u geen `heropbouw` uit te voeren. Op de plek van de we:include tag wordt de pagina met onderstaand ID ingevoegd. Met het attribuut `gethttp` kunt u aangeven of de pagina verzonden moet worden via HTTP of niet.<br>Het attribuut `seem` bepaalt of het document aanpasbaar is in de seeMode. Dit attribuut werkt alleen wanneer het document ID opgegeven is.',
	),
	'input'=>array(
		'description'=>'De we:input tag creëert een single-line input box in de Wijzig modus van het document gebaseerd op dit sjabloon, wanneer type = `text` is geselecteerd. Voor alle andere types kunt u de handleiding of de help functie raadplegen.',
	),
	'js'=>array(
		'description'=>'De we:jstag creëert een HTML tag die refereert aan een intern webEdition JavaScript document met onderstaand ID. U kunt JavaScripts definiëren in een apart bestand.',
	),
	'keywords'=>array(
		'description'=>'De we:keywords tag genereert een keywords meta teg. Als het keywords veld in de &quot;Eigenschappen&quot; weergave leeg is, wordt de content tussen de begin tag en de eind tag gebruikt als standaard keywords. Anders worden de keywords van de Eigenschappen weergave ingevoerd.',
	),
	'linklist'=>array(
		'defaultvalue'=>'&lt;we:link /&gt;&lt;we:postlink&gt;&lt;br /&gt;&lt;/we:postlink&gt;',
		'description'=>'De we:linklist tag wordt gebruikt om koppeling lijsten aan te maken. Een `plus` knop is zichtbaar in de Wijzig modus. Wanneer u op de knop drukt komt er een nieuwe link bij in de lijst. De uitstraling van de link list wordt bepaald door de gebruikte HTML in de link list en het gebruik van `we:prelink` en `we:postlink`  tussen  <we:link> en </we:link>. Alle koppelingen kunnen worden verwijderd met een verwijder knop en gewijzigd worden met  wijzig knop.',
	),
	'linkToSeeMode'=>array(
		'description'=>'Deze tag genereert een koppeling die het geselecteerde document opent in de seeMode.',
	),
	'link'=>array(
		'description'=>'De we:link tag creeert een enkele koppeling welke gewijzigd kan worden door middel van de `wijzig` knop. De `name` attribuut mag niet gespecificeerd worden tussen de we:linklist begin tag en eind tag. De `name` attribuut moet gespecificeerd worden buiten de we:linklist tags. `only` geeft de mogelijkheid om één enkel attribuut (only=`attribuut naam`) van de koppeling of alleen de content (only=`content`) van de koppeling op te vragen.',
	),
	'listdir'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:listdir tag creëert  een nieuwe lijst die alle bestanden in dezelfde directory toont. In het attribuut `field` kunt u bepalen welk veld getoond moet worden. Als het veld leeg is of niet bestaat, wordt de bestandsnaam gebruikt. Directories worden doorzocht op index bestanden; indien er een index bestand is, wordt deze getoond. Welk veld er gebruikt moet worden om directories te tonen kunt u bepalen in het attribuut `dirfield`. Als het veld leeg is of niet bestaat, wordt de invoer van `field` respectief tot de naam van het bestand gebruikt. Als het attribuut `id` ingesteld is worden de bestanden of de directory met het aangegeven ID getoond.',
	),
	'listviewEnd'=>array(
		'description'=>'Deze tag toont het nummer van de laatste invoer van de huidige &lt;we:listview&gt; pagina.',
	),
	'listviewPageNr'=>array(
		'description'=>'Deze tag geeft het nummer op van de huidige pagina in een &lt;we:listview&gt;.',
	),
	'listviewPages'=>array(
		'description'=>'Deze tag geeft het aantal pagina`s op in een &lt;we:listview&gt;.',
	),
	'listviewRows'=>array(
		'description'=>'Deze tag geeft het aantal gevonden invoeren op in een &lt;we:listview&gt;.',
	),
	'listviewStart'=>array(
		'description'=>'Deze tag toont het nummer van de eerste invoer van de huidige &lt;we:listview&gt; pagina.',
	),
	'listview'=>array(
		'defaultvalue'=>'&lt;we:repeat&gt;
&lt;we:field name="Title" alt="we_path" hyperlink="true"/&gt;
&lt;br /&gt;
&lt;/we:repeat&gt;',
		'description'=>'De we:listview tag is de begin tag en de eind tag van automatisch gegenereerde lijsten (nieuwspagina overzichten etc.).',
	),
	'list'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:list tag geeft u de mogelijkheid om expandable lists te maken. Alles binnen de begin tag en de eind tag wordt ingevoerd (alle HTML en bijna alle we:tags) als u op de `plus` knop drukt in de Wijzig modus.',
	),
	'makeMail'=>array(
		'description'=>'Deze tag moet geplaatst worden op de eerste regel van elk sjabloon om een webEdition document te genereren die verstuurd kan worden met &lt;we:sendMail/&gt;.',
	),
	'master'=>array(
		'defaultvalue'=>'',
		'description'=>'',
	),
	'metadata'=>array(
		'defaultvalue'=>'&lt;we:field name="NameOfField" /&gt;',
		'description'=>'',
	),
	'navigationEntries'=>array(
		'description'=>'Binnen we:navigationEntry type="folder" maakt deze tag een place holder aan voor alle invoeren van een navigatie map.',
	),
	'navigationEntry'=>array(
		'defaultvalue'=>'&lt;a href="&lt;we:navigationField name="href" /&gt;"&gt;&lt;we:navigationField name="text" /&gt;&lt;/a&gt;&lt;br /&gt;',
		'description'=>'Met we:navigationEntry kan de weergave van een invoer gecontroleerd worden binnen de navigatie. Met de attributen "type", "level", "current" en "position" kunnen individuele elementen van verschillende niveau`s specifiek gekozen en getoond worden.',
	),
	'navigationField'=>array(
		'description'=>'&lt;we:navigationField&gt; is used within &lt;we:navigationEntry&gt; to print a value of the current navigation entry.<br/>Choose from <b>either</b> the attribute <i>name</i>, <b>or</b> from the attribute <i>attributes</i>, <b>or</b> from the attribute <i>complete</i>',
	),
	'navigationWrite'=>array(
		'description'=>'Is used to write a we:navigation with given name',
	),
	'navigation'=>array(
		'description'=>'we:navigation wordt gebruikt om een navigatie te initialiseren die gemaakt is met de navigatie-tool.',
	),
	'newsletterConfirmLink'=>array(
		'defaultvalue'=>'Bevestig nieuwsbrief',
		'description'=>'Deze tag wordt gebruikt om de double opt-in bevestigings-koppeling te genereren.',
	),
	'newsletterField'=>array(
		'description'=>'Displays a field from the recipient dataset within the newsletter.',
	),
	'newsletterSalutation'=>array(
		'description'=>'Deze tag wordt gebruikt om aanhef-velden weer te geven.',
	),
	'newsletterUnsubscribeLink'=>array(
		'description'=>'Creëert een koppeling voor uitschrijving van een nieuwsbrief lijst. Deze tag kan alleen gebruikt worden in e-mail sjablonen!',
	),
	'next'=>array(
		'defaultvalue'=>'',
		'description'=>'Crëert de HTML koppeling tag die refereert aan de volgende pagina binnen listviews. De tag koppelt alle content gevonden tussen de begin- en eind tag.',
	),
	'noCache'=>array(
		'defaultvalue'=>'',
		'description'=>'PHP-Code omsloten door deze tag wordt elke keer uitgevoerd als het ge-cachde document opgevraagd wordt (Uitzondering: Volledige-Cache)',
	),
	'objectLanguage'=>array(
		'defaultvalue'=>'',
		'description'=>'Shows the language of the object',
	),
	'object'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:object tag wordt gebruikt om objecten te tonen. De velden van een object kunnen getoond worden met de we:field tags binnen de begin tag en de eind tag. Als slechts het attribuut `name` is ingevoerd voor een object, of als deze een waarde heeft, wordt de object kiezer getoond in de Wijzig modus, en heeft de editor de keuze alle objecten te selecteren uit alle classen. Waneer ook het attribuut `classid` een waarde heeft, wordt de selectie in de object kiezer beperkt tot alle objecten gerelateerd aan de in `classid` gedefinieerde class. Met het attribuut `id` kunt u een voorselectie definiëren van een specifiek object gedefinieerd door `classid` en `id`. Het attribuut `triggerid` wordt gebruikt om dynamische pagina`s  te tonen in een statische object listview.',
	),
	'orderitem'=>array(
		'defaultvalue'=>'',
		'description'=>'Using this tag, one can display a single item on an order on a webEdition page. Similar to the Listview or the <we:object> tag, the fields are displayed with the <we:field> tag.',
	),
	'order'=>array(
		'defaultvalue'=>'',
		'description'=>'Using this tag, one can display an order on a webEdition page. Similar to the Listview or the <we:object> tag, the fields are displayed with the <we:field> tag.',
	),
	'pageLanguage'=>array(
		'defaultvalue'=>'',
		'description'=>'Shows the language of the document',
	),
	'pagelogger'=>array(
		'description'=>'De we:pagelogger tag genereert, aan de hand van het geselecteerde &quot;type&quot; attribuut, de benodigde code voor pageLogger of de bestandsserver - respectievelijk de download code.',
	),
	'path'=>array(
		'description'=>'De we:path tag representeert het pad van het huidige document. Als er zich een index bestand bevindt in één van de subdirectories, wordt een koppeling ingesteld op de respectieve directory. De gebruikte index bestanden (komma gescheiden) kunnen gespecificeerd worden in het attribuut `index`. Als er niks gekozen is worden `default.html`, `index.htm`, `index.php`, `default. htm`, `default.html` en `default.php` gebruikt als standaard instellingen. In het attribuut `home` kunt u kiezen wat er aan het begin moet komen. Als er niks gekozen is wordt `home` automatisch getoond. De attribuut seperator omschrijft de afbakening tussen de directories. Als het attribuut leeg is wordt `/` gebruikt als scheiding. Het attribuut `field` definieert welk soort veld (bestanden, directories) word getoond. Het attribuut `dirfield` definieert welk veld wordt gebruikt bij vertoning in directories. Als het veld leeg is of niet bestaat wordt de invoer van `field` of de bestandsnaam gebruikt.',
	),
	'paypal'=>array(
		'description'=>'we:paypal implementeert een interface naar de betalings aanbieder paypal. Voor optimale werking van deze tag dient u additionele informatie toe te voegen in de backend van de winkel module.',
	),
	'position'=>array(
		'description'=>'De tag we:position wordt gebruikt om de eigenlijke positie van een listview, block, linklist, listdir op te vragen. Als "type= block or linklist" dan is het noodzakelijk om de naam (referentie) van de gerelateerde block/linklist te specificeren. Het attribuut "format" bepaalt het format van het resultaat.',
	),
	'postlink'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:postlink tag zorgt ervoor dat alles wat zich binnen de begin en eind tag bevindt, niet getoond wordt bij de laatste koppeling in de link list.',
	),
	'prelink'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:prelink tag zorgt ervoor dat alles wat zich binnen de begin en eind tag bevindt, niet getoond wordt bij de eerste link in de link list.',
	),
	'printVersion'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:printVersion tag creëert een HTML koppeling tag die refereert aan hetzelfde document, met een ander sjabloon. Het attribuut `tid` bepaalt het sjabloon ID. De tag koppelt alle content binnen de begin tag en de eind tag.',
	),
	'processDateSelect'=>array(
		'description'=>'De we:processDateSelect tag verwerkt de 3 waardes uit de select boxes van de we:dateSelect tag naar een UNIX tijdstempel. De waarde wordt bewaard naar een globale variabele met de naam die was ingevoerd in het attribuut &quot;name&quuot;.',
	),
	'quicktime'=>array(
		'description'=>'De we:quicktime tag geeft de mogelijkheid een QuickTime film in te voegen in de content van een document. Documenten gebasseerd op dit sjabloon bevatten een Wijzig knop in de Wijzig modus. Wanneer u op deze knop drukt, opent u de Bestands manager waarmee u een QuickTime film kunt selecteren binnen webEdition. Er bestaat nog geen xhtml-valid output die werkt in gebruikelijke browsers (IE, Mozilla). Daarom staat xml altijd op `false`',
	),
	'registeredUser'=>array(
		'description'=>'De we:registeredUser tag wordt gebruikt om klant data, opgeslagen in de klant module, te printen.',
	),
	'registerSwitch'=>array(
		'description'=>'Deze tag genereert een switch waarmee u kan schakelen tussen de status van een geregistreerde en een ongeregistreerde gebruiker in de edit-mode. Indien u de &lt;we:ifRegisteredUser&gt; en &lt;we:ifNotRgisteredUser&gt; tags gebruikt, deze tag geeft de mogelijkheid veschillende weergaven te zien en controle te houden over de lay-out.',
	),
	'repeatShopItem'=>array(
		'defaultvalue'=>'',
		'description'=>'Deze tag toont alle artikelen in de winkelmand.',
	),
	'repeat'=>array(
		'defaultvalue'=>'',
		'description'=>'Content omsloten in deze tag wordt herhaald voor elke invoer gevonden door een &lt;we:listview&gt;. Deze tag wordt alleen gebruikt binnen een &lt;we:listview&gt; sectie.',
	),
	'returnPage'=>array(
		'description'=>'De we:returnPage tag wordt gebruikt om de refererende URL te tonen, als de waarde van het attribuut `return` op `true` stond bij gebruik in de tags: &lt;we:a edit=`document`&gt; or &lt;we:a edit=`object`&gt;',
	),
	'saferpay'=>array(
		'description'=>'we:saferpay implementeert een interface naar de betalings aanbieder saferpay. Voor optimale werking van deze tag dient u additionele informatie toe te voegen in de backend van de winkel module.',
	),
	'saveRegisteredUser'=>array(
		'description'=>'De we:saveRegisteredUser tag bewaart alle klantdata ingevoerd door middel van sessie velden.',
	),
	'search'=>array(
		'description'=>'De we:search tag creeert een input box of een tekst box die is bedoeld voor zoek opdrachten. Het zoek veld heeft de interne naam "we_search". Met als gevolg, als het zoek-formulier is voorgelegd, De PHP variabele "we_search" op de ontvangende internet pagina wordt gevuld met de inhoud van de input box.',
	),
	'select'=>array(
		'defaultvalue'=>'&lt;option&gt;#1&lt;/option&gt;
&lt;option&gt;#2&lt;/option&gt;
&lt;option&gt;#3&lt;/option&gt;',
		'description'=>'De we:select tag creeert een select box voor invoer in de Wijzig modus. Als "1" is gespecificeerd als grootte (size= "1" ), verschijnt de select box als een pop-up menu. Dit werkt hetzelfde als een HTML select tag. Binnen de begin tag en de eind-tag, worden invoeren bepaald door middel van normale HTML option tags.',
	),
	'sendMail'=>array(
		'description'=>'De we:sendMail tag verstuurt een webEdition pagina als e-mail naar de adresssen die zijn opgegeven in het attribuut `recipient`.',
	),
	'sessionField'=>array(
		'description'=>'De we:sessionField tag creëert een HTML input, select of text area tag. Het wordt gebruikt voor elke invoer in sessie velden (bijv. Userdata, etc.).',
	),
	'sessionLogout'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:sessionLogout tag creëert een HTML koppeling tag die refereert aan een intern webEdition document met het ID genoemd in de webEdition Tag Wizard. Indien dit webEdition document een we:sessionStart tag bevat met het attribuut "dynamic", dan wordt de active sessie geleegd en afgesloten. Er worden geen gegevens bewaard.',
	),
	'sessionStart'=>array(
		'description'=>'Deze tag wordt gebruikt om een sessie te starten of om een bestaande sessie te hervatten. Deze tag is vereist in sjablonen die de volgende pagian`s genereren: Pagina`s die afgeschermd zijn met de Klant beheer module, Winkel pagina`s en pagina`s die front end invoer ondersteunen.&lt;br /&gt;Deze tag MOET geplaatst worden op de eerste regel van het sjabloon!',
	),
	'setVar'=>array(
		'description'=>'De we:setVar tag wordt gebruikt om de waardes van verschillende types variabelen in te stellen.<br/><strong>Attention:</strong> Without the attribute <strong>striptags="true"</strong>, HTML- and PHP-Code is not filtered, this is a potenzial security risk!</strong>',
	),
	'shipping'=>array(
		'description'=>'Met betrekking tot de aankoop wordt we:shipping gebruikt om de verzend kosten te bepalen. Deze kosten zijn gebaseerd op de waarde van de winkelwagen, het land van herkomst van de geregistreerde gebruiker en de verzend overeenkomsten, te wijzigen in de Winkel module. De parameter "sum" bevat de naam van een met we:sum berekende som. De parameter type wordt gebruikt bij het bepalen van de netto waarde, bruto waarde of het aantal van de BTW toebehorend aan de verzendkosten.',
	),
	'shopField'=>array(
		'description'=>'Deze tag geeft de mogelijkheid om meerdere invoervelden direct aan een artikel/winkelwagen (bestelling) toe te voegen. De beheerder kan sommige waardes vooraf definiëren waaruit de klant een eigen waarde kan selecteren of invoeren. Hierdoor is het mogelijk om meerdere artikel varianten eenvoudig in kaart te brengen.',
	),
	'shopVat'=>array(
		'description'=>'Deze tag wordt gebruikt voor het bepalen van de BTW van een artikel. Gebruik om verschillende BTW waardes te beheren de Winkel module. Een opgegeven Id geeft direct de BTW waarde van dit artikel weer.',
	),
	'showShopItemNumber'=>array(
		'description'=>'De we:showShopItemNumber tag toont het aantal gespecificeerde onderdelen in de winkelmand.',
	),
	'sidebar'=>array(
		'defaultvalue'=>'Open sidebar',
		'description'=>'',
	),
	'subscribe'=>array(
		'description'=>'De we:subscribe tag wordt gebruikt om een single input veld toe te voegen aan een webEdition document zodat de gebruiker zich kan inschrijven voor een nieuwsbrief.',
	),
	'sum'=>array(
		'description'=>'De we:sum tag sommeert alle figuren in een lijst.',
	),
	'target'=>array(
		'description'=>'Deze tag wordt gebruikt om de doel van een koppeling te genereren binnen een &lt;we:linklist&gt;.',
	),
	'textarea'=>array(
		'description'=>'De we:textarea tag creeert een multi-line invoer box.',
	),
	'title'=>array(
		'description'=>'De we:title tag creeert een normale titel tag. Als het titelveld in de Eigenschappen weergave leeg is, wordt alles tussen de  begin en eind tag gebruikt als standaard titel.',
	),
	'tr'=>array(
		'defaultvalue'=>'',
		'description'=>'De &lt;we:tr&gt; Tag correspondeert aan de HTML-tag &lt;tr&gt; en wordt gebruikt om een tabel rij te definieren.',
	),
	'unsubscribe'=>array(
		'description'=>'De we:unsubscribe tag wordt gebruikt om een single input veld te genereren op een webEdition document zodat de gebruiker zijn e-mailadres kan invoeren voor uitschrijving van een nieuwsbrief.',
	),
	'url'=>array(
		'description'=>'De we:url tag creëert een interne webEdition URL die refereert aan het document met onderstaand ID.',
	),
	'userInput'=>array(
		'description'=>'De we:userInput tag creërt invoervelden voor gebruik met we:form type=&quot;document&quot; of type=&quot;object&quot; om documenten of objecten aan te kunnen maken.',
	),
	'useShopVariant'=>array(
		'description'=>'De we:shopVariant tag gebruikt de gegevens van een artikel variant a.d.h.v. de opgegeven naam van de variant. Indien er geen variant bestaat met de opgegeven naam wordt het standaard artikel getoond.',
	),
	'var'=>array(
		'description'=>'De we:var tag toont de inhoud van een globaal PHP variable respectief tot de inhoud van een documentveld met onderstaande naam.',
	),
	'votingField'=>array(
		'description'=>'The we:votingField-tag is required to display the content of a voting. The attribute "name" defines what to show. The attribute "type", how to display it. Valid name-type combinations are: question - text; result - count, percent, total; id - answer, select, radio, voting; answer - text,radio,checkbox (select multiple) select, textinput and textarea (free text answer field), image (all we:img attributes such as thumbnail are supported), media (delivers the path utilizing to and nameto);',
	),
	'votingList'=>array(
		'defaultvalue'=>'',
		'description'=>'Deze tag genereert automatisch de peiling lijsten.',
	),
	'votingSelect'=>array(
		'description'=>'Gebruik deze tag voor het genereren van een dropdown menu; (&lt;select&gt;) voor het selecteren van een peiling.',
	),
	'votingSession'=>array(
		'description'=>'Generates an unique identifier which is stored in the voting log and allows to identify the answers to different questions which belong to a singele voting session',
	),
	'voting'=>array(
		'defaultvalue'=>'',
		'description'=>'De we:voting tag wordt gebruikt om peilingen weer te geven.',
	),
	'writeShopData'=>array(
		'description'=>'De we:writeShopData tag schrijft alle huidige winkelmand data naar de database.',
	),
	'writeVoting'=>array(
		'description'=>'Deze tag schrijft een peiling naar de database. Als het attribuut "id" gedefinieerd is, wordt alleen de peiling met het respectievelijke id bewaard.',
	),
	'write'=>array(
		'description'=>'Deze tag parkeert een document/object gegenereerd door &lt;we:form type="document/object&gt;',
	),
	'xmlfeed'=>array(
		'description'=>'Deze tag laad xml content vanaf de opgegeven url',
	),
	'xmlnode'=>array(
		'defaultvalue'=>'',
		'description'=>'Deze tag print een xml element vanaf de opgegeven feed of url.',
));