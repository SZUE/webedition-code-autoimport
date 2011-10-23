<?php
/** Generated language file of webEdition CMS */
$l_weTag=array(
	
	'addDelNewsletterEmail'=>array(
		'description'=>'This tag is used to add or remove an E-mail address from a newsletter address list. In attribute "path" the complete path to the newsletter list must be given. If the path begins without "/" the path will be emanated from DOCUMENT_ROOT. If you use several lists, you can enter several paths, separated by a comma.',
	),
	'addDelShopItem'=>array(
		'description'=>'Use the we:addDelShopItem tag to add or delete an article from the shopping cart.',
	),
	'addPercent'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:addPercent tag adds a specified percentage, for example, VAT.',
	),
	'answers'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag displays the response possibilities of a voting.',
	),
	'author'=>array(
		'description'=>'The we:author tag shows the creator of the document. If the attribute `type` is not set, the user name will be displayed. If type="name", the first and last name of the user will be displayed. If `type="initials", the initials of the user will be displayed. When no first or last name is entered, the username will be shown.',
	),
	'a'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:a tag creates an HTML link tag that references an internal webEdition document that has the ID listed below. The tag links any content found between the start tag and the end tag.',
	),
	'back'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:back tag creates an HTML link tag that references the previous we:listview page. The tag links any content found between the start tag and the end tag.',
	),
	'bannerSelect'=>array(
		'description'=>'This tag displays a drop-down menu (&lt;select&gt;), to select banners. If the Customer Management Module is installed and the attribute customer is set to true, only banners of the logged-in customer will be shown.',
	),
	'bannerSum'=>array(
		'description'=>'The we:bannerSum tag displays the number of all shown or clicked banners or their click rate. The tag only works within a listview with type="banner"',
	),
	'banner'=>array(
		'description'=>'Use the we:banner tag to include a banner from the Banner Module.',
	),
	'block'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:block tag allows you to create expandable blocks/lists. Everything located between the start tag and the end tag will be entered (any HTML and almost all we:tags), if you click the plus button in edit mode.',
	),
	'calculate'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:calculate tag allows all possible mathematical operations in PHP like *, /, +, -,(), sqrt..etc.',
	),
	'captcha'=>array(
		'description'=>'This tag generates an image with a random code.',
	),
	'categorySelect'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag is used to insert a drop-down (&lt;select&gt;) menu into a webEdition document. Use this tag to select a category. By placing an end tag immediatly after the start tag, you will cause the drop-down menu to contain all categories currently defined in webEdition.',
	),
	'category'=>array(
		'description'=>'The we:category tag is replaced by the category (or categories) that was / were allocated to the document in the Properties view. If several categories have been allocated, they must be delimited using commas. If you wish to use another delimiter, you must specify it using the "tokken" attribute. Example: tokken = " " (In this case, a space is being used to delimit categories).',
	),
	'charset'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:charset tag generates a meta tag which determines the used charset for the page. "ISO-8859-1" is usually used for English Web pages. This tag must be placed within the meta tag of the HTML page.',
	),
	'checkForm'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:checkForm tag validates the entries of a form with JavaScript.&lt;br /&gt;The combination of the parameters `match` and `type` determine the `name` or the `id` of the form to check.&lt;br /&gt;`mandatory` and `email` contain a commaseperated list of mandatory or e-mail fields. In `password` it is possible to insert 2 names of fields and a minimum length of inserted passwords.&lt;br /&gt;With `onError` you can choose the name of an individual JavaScript-function which is called in case of an error. This function will get an array with the names of missing mandatory and email fields and a flag, if the password was correct. If `onError` is not set or the function does not exist, the default value is displayed in an alert-box.',
	),
	'colorChooser'=>array(
		'description'=>'The we:colorChooser tag creates an input field for choosing a color value.',
	),
	'comment'=>array(
		'description'=>'The comment Tag is used to generate explicit comments in the specified language, or, to add comments to the template which are not delivered to the user browser.',
	),
	'conditionAdd'=>array(
		'description'=>'This tag is used to add a new rule or condition within a &lt;we:condition&gt; block.',
	),
	'conditionAnd'=>array(
		'description'=>'This tag is used to add conditions within a &lt;we:condition&gt;. This is a logical AND, meaning that both existing conditions must be fulfilled.',
	),
	'conditionOr'=>array(
		'description'=>'This tag is used to add conditions within a &lt;we:condition&gt;. This is a logical OR, meaning that either one or the other of the conditions must be fulfilled.',
	),
	'condition'=>array(
		'defaultvalue'=>'&lt;we:conditionAdd field="Type" var="type" compare="="/&gt;',
		'description'=>'This tag is used together with &lt;we:conditionAdd&gt; to dynamically add a condition to the attribute "condition" in a &lt;we:listview type="object"&gt;. Conditions may be interlaced.',
	),
	'content'=>array(
		'description'=>'&lt;we:content /&gt; is only used inside a mastertemplate. It determines the place where the content of the template is used in the mastertemplate.',
	),
	'controlElement'=>array(
		'description'=>'The tag we:controlElement can maniluate control elements in the edit view of a document. Buttons can be hidden. Checkboxes can be disabled, checked and/or hidden.',
	),
	'cookie'=>array(
		'description'=>'This tag is required with the Voting Module and sets a cookie, which denies more than one vote for a user. The tag needs to be placed at the very start of the template. There must not be any breaks or spaces in front of this tag.',
	),
	'createShop'=>array(
		'description'=>'The we:createShop tag is needed on every page that is supposed to contain shop data.',
	),
	'css'=>array(
		'description'=>'The css tag creates an HTML tag that references an internal webEdition CSS style sheet that has the ID listed below. You can define style sheets in a separate file.',
	),
	'customer'=>array(
		'defaultvalue'=>'',
		'description'=>'Using this tag, data from any customer can be displayed. The customer data are displayed as in a listview or within the &lt;we:object&gt; tag with the tag &lt;we:field&gt;.<br /><br />Combining the attributes, this tag can be utilized in three ways:<br/>If name is set, the editor can select a customer by using a customer-select-Field. This customer is stored in the document within the field name.<br />If name is not set but instead the id, the customer with this id is displayed.<br />If neither name nor id is set, the tag expects the id of the customer by a request parameter. This is i.e. used by the customer-listview when the attribut hyperlink="true" in the &lt;we:field&gt; tag is used. The name of the request parameter is we_cid.',
	),
	'dateSelect'=>array(
		'description'=>'The we:dateSelect tag displays a select field for dates, which can be used together with the we:processDateSelect tag to read the date value into a variable as a UNIX time stamp.',
	),
	'date'=>array(
		'description'=>'The we:date tag displays the current date on the page as specified by the format string. If the document is static, the type should be set to &quot;js&quot;, so that the date is generated using JavaScript.',
	),
	'deleteShop'=>array(
		'description'=>'The we:deleteShop tag deletes the complete shopping cart.',
	),
	'delete'=>array(
		'description'=>'This tag is used to delete webEdition documents accessed via &lt;we:a edit="document" delete="true"&gt; or &lt;we:a edit="object" delete="true"&gt;.',
	),
	'description'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:description tag generates a description meta tag. If the description field in the Properties view is empty, the content placed between the start tag and the end tag will be used as the default description.',
	),
	'DID'=>array(
		'description'=>'This tag returns the ID of a webEdition document.',
	),
	'docType'=>array(
		'description'=>'This tag returns the document type of a webEdition document.',
	),
	'else'=>array(
		'description'=>'This tag is used to add alternative conditions within an if-type tag e.g. &lt;we:ifEditmode&gt;, &lt;we:ifNotVar&gt;, &lt;we:ifNotEmpty&gt;, &lt;we:ifFieldNotEmpty&gt;',
	),
	'field'=>array(
		'description'=>'This tag inserts the content of the field that has the name defined in the "name" attribute into listviews. It may only be used between the start tag and end tag of we:repeat.',
	),
	'flashmovie'=>array(
		'description'=>'The we:flashmovie tag allows you to insert a Flash movie in the content of the document. Documents based on this template will display an edit button while in edit mode. Clicking on this button will launch a file manager, which allows you to select a Flash movie that you have already set up in webEdition.',
	),
	'formfield'=>array(
		'description'=>'This tag is used to generate fields in a front end form.',
	),
	'formmail'=>array(
		'description'=>'With activated Setting Call Formmail via webEdition document, the integration of the formmail script is realized with a webEdition document. For this, the (currently without attributes) we-Tag formmail will be used. <br />If the Captcha-check is used, &lt;we:formmail/&gt; is located within the we-Tag ifCaptcha.',
	),
	'form'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:form tag is used to search and mail forms. It works in the same fashion as the normal HTML form tag, but allows the parser to insert additional hidden fields.',
	),
	'hidden'=>array(
		'description'=>'The we:hidden tag creates a hidden input tag holding the value of the global PHP variable with the same name. Use this tag if you want to forward incoming variables.',
	),
	'hidePages'=>array(
		'description'=>'The we:hidePages tag allows to disable some modes of a document. You can use this tag i.e. , to restrict access to the properity page of a document. In this case, it is not possible to park this document any more.',
	),
	'href'=>array(
		'description'=>'The we:href tag creates a URL that can be entered in edit mode.',
	),
	'icon'=>array(
		'description'=>'The we:icon tag creates an HTML tag that references an internal webEdition icon that has the ID listed below. With this tag, you can include an icon that is displayed in the Internet Explorer, Mozilla, Safari and Opera while book marking your home page.',
	),
	'ifBack'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag is used between the start and end tags of &lt;we:listview&gt;. Everything between the start and end tags of this tag is displayed only if a previous page exists. For example, you can use this tag on the second page of a 20 item listview where 5 items are displayed per page.',
	),
	'ifbannerexists'=>array(
		'defaultvalue'=>'',
		'description'=>'Executes the enclosed code only, if the banner module is not deaktivated (settings dialog).',
	),
	'ifCaptcha'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if the code entered by the user is valid.',
	),
	'ifCat'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifCat tag ensures that everything located between the start tag and the end tag is only displayed if the categories which are entered under "categories" are one of the document`s categories.',
	),
	'ifClient'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifClient tag ensures that everything located between the start tag and the end tag will only be displayed if the client (browser) meets the established standards. This tag only works with dynamically saved pages!',
	),
	'ifConfirmFailed'=>array(
		'defaultvalue'=>'',
		'description'=>'When using DoubleOptIn with the Newsletter Module, &lt;we:ifConfirmFailed&gt; checks, if the E-Mail address was confirmed.',
	),
	'ifCurrentDate'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag highlights the current day within a calendar-listview.',
	),
	'ifcustomerexists'=>array(
		'defaultvalue'=>'',
		'description'=>'Executes the enclosed code only, if the customer module is not deaktivated (settings dialog).',
	),
	'ifDeleted'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by the start and end tags of this tag are only displayed if a particular document or object was deleted using &lt;we:delete/&gt;',
	),
	'ifDoctype'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifDoctype tag ensures that everything located between the start tag and the end tag is only displayed if the document type which is entered under "doctype" is the same as the document`s document type.',
	),
	'ifDoubleOptIn'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed during the first part of a double opt-in process.',
	),
	'ifEditmode'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag is used to display the enclosed content only in edit mode.',
	),
	'ifEmailExists'=>array(
		'defaultvalue'=>'',
		'description'=>'Contents enclosed by this tag are only displayed if a specified email address is in the newsletter address list.',
	),
	'ifEmailInvalid'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only visible if a particular email is incorrect in its syntax.',
	),
	'ifEmailNotExists'=>array(
		'defaultvalue'=>'',
		'description'=>'Contents enclosed by this tag are only displayed if the email address in question is not in the newsletter address list.',
	),
	'ifEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifEmpty tag ensures that everything located between the start tag and the end tag is only displayed if the field having the same name as entered under "match" is empty. The type of field must be specified in the attribute "type" if it is an "img", "flashmovie" or "href" field.',
	),
	'ifEqual'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifEqual tag compares the content of the fields "name" and "eqname". If the content of both fields is the same, everything between start tag and end tag will be displayed. If the tag is used in we:list, we:block or we:linklist, only one field within these tags can be compared with one field outside. In this case you have to set the attribute "name" to the name of the field within the we:block, we:list or we:linklist-tags. The attribute eqname then has to be set to the name of a field outside these tags. The tag can also be located within dynamically included webEdition pages. In this case, "name" is set to a field within the included page and "eqname" is set to the name of a field in the main page. If the attribute "value" is filled, "eqname" will be ignored and the content of the field "name" will be compared with the value filled in the attribute "value".',
	),
	'ifFemale'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag only appears if the user selects the female salutation.',
	),
	'ifFieldEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifFieldEmpty tag ensures that everything located between the start tag and the end tag is only displayed if the listview field with the name listed in "match" is empty. The type of field must be specified in the attribute "type" if it is an "img", "flashmovie" or "href" field.',
	),
	'ifFieldNotEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifFieldNotEmpty tag ensures that everything located between the start tag and the end tag is only displayed if the listview field with the name listed in "match" is not empty. The type of field must be specified in the attribute "type" if it is an "img", "flashmovie" or "href" field.',
	),
	'ifField'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag is used between the start tag and end tag of we:repeat. Everything between the start and end tags of this tag is displayed only if the value of the attribut "match" is identical with the value of database field of the associated listview entry.',
	),
	'ifFound'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if documents are found within a &lt;we:listview&gt;.',
	),
	'ifHasChildren'=>array(
		'defaultvalue'=>'',
		'description'=>'Within the &lt;we:repeat&gt; tag &lt;we:ifHasChildren&gt; is used to query if a category(folder) has child categories.',
	),
	'ifHasCurrentEntry'=>array(
		'defaultvalue'=>'',
		'description'=>'we:ifHasCurrentEntry can be used within we:navigationEntry type="folder" to show content, only if the navigation folder contains the activ entry',
	),
	'ifHasEntries'=>array(
		'defaultvalue'=>'',
		'description'=>'we:ifHasEntries can be used within we:navigationEntry to show content only, if the navigation entry contains entries.',
	),
	'ifHasShopVariants'=>array(
		'defaultvalue'=>'',
		'description'=>'The tag we:ifHasShopVariants can display content depending on the existance of variants in an object or document. With this, it can be controlled whether a &lt;we:listview type="shopVariant"&gt; should be displayed at all. <b>This tag works in document and object templates attached by object-workspaces, but not inside the we:listview and we:object - tags</b>',
	),
	'ifHtmlMail'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if the format of a newsletter is HTML.',
	),
	'ifIsDomain'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:iflsDomain tag ensures that everything located between the start tag and the end tag is only displayed if the domain name of the server has the same name as entered under "domain". The result can only be seen on the finished Web site or in the preview. In edit mode everything is displayed.',
	),
	'ifIsNotDomain'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:iflsNotDomain tag ensures that everything located between the start tag and the end tag is only displayed if the domain name of the server has not the same name as entered under "domain".The result can only be seen on the finished Web site or in the preview. In edit mode everything is displayed.',
	),
	'ifLastCol'=>array(
		'defaultvalue'=>'',
		'description'=>'&lt;we:ifLastCol&gt; can detect the last col of a table row, when using the table functions of a &lt;we:listview&gt;',
	),
	'ifLoginFailed'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if a login failed.',
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
		'description'=>'Content enclosed by this tag is only displayed if the user has not selected any newsletter.',
	),
	'ifMale'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if the user is male. This tag is used for the salutation in newsletters.',
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
		'description'=>'Content enclosed by this tag is only displayed in a new webEdition document or object.',
	),
	'ifNext'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is displayed only if a next page of items is available in a &lt;we:listview&gt;',
	),
	'ifNoJavaScript'=>array(
		'description'=>'This tag redirects a page to a specified ID if the browser used does not provide JavaScript support or if JavaScript support is deactivated. This tag can only appear in the &lt;head&gt; section of the template.',
	),
	'ifNotCaptcha'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if the code entered by the user is not valid.',
	),
	'ifNotCat'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifNotCat tag ensures that everything located between the start tag and the end tag is only displayed if the categories which are entered under "categories" are none of the document`s categories.',
	),
	'ifNotDeleted'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if a webEdition document or object could not be deleted by &lt;we:delete/&gt;',
	),
	'ifNotDoctype'=>array(
		'defaultvalue'=>'',
		'description'=>'',
	),
	'ifNotEditmode'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is not displayed in edit mode.',
	),
	'ifNotEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifNotEmpty tag ensures that everything located between the start tag and the end tag is only displayed if the field having the same name as entered under "match" is not empty. The type of field must be specified in the attribute "type", if it is a "img", "flashmovie" or "href" field.',
	),
	'ifNotEqual'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifNotEqual tag compares the content of the fields "name" and "eqname". If the content of both fields is the same, everything between start- and endtag will not be displayed. If the tag is used in we:list, we:block or we:linklist, only one field within these tags can be compared with one field outside. In this case you have to set the Attribute "name" to the name of the field within the we:block, we:list or we:linklist-tags. The attribute eqname then has to be set to the name of a field outside these tags. The tag can also be located within dynamically included webEdition - pages. In this case "name" is set to a field within the included page and "eqname" is set to the name of a field in the main page. If the attribute "value" is filled, "eqname" will be ignored and the content of the field "name" will be compared with the value filled in the attribute "value".',
	),
	'ifNotField'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag is used between the start tag and end tag of we:repeat. Everything between the start and end tags of this tag is displayed only if the value of the attribut "match" is not identical with the value of database field of the associated listview entry.',
	),
	'ifNotFound'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is displayed only if nothing is found by a &lt;we:listview&gt;.',
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
		'description'=>'Content enclosed by this tag is only displayed in an HTML newsletter document.',
	),
	'ifNotNewsletterSalutation'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is not equal to match.',
	),
	'ifNotNew'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed in an old webEdition document or object.',
	),
	'ifNotObjectLanguage'=>array(
		'defaultvalue'=>'',
		'description'=>'The tag we:ifNotObjectLanguage tests on the language setting in the properties tab of the object, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages',
	),
	'ifNotObject'=>array(
		'defaultvalue'=>'',
		'description'=>'The enclosed content is only displayed if the entry within &lt;we:listview type="search"&gt; is not an object.&lt;br /&gt;',
	),
	'ifNotPageLanguage'=>array(
		'defaultvalue'=>'',
		'description'=>'The tag we:ifNotPageLanguage tests on the language setting in the properties tab of the document, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages',
	),
	'ifNotPosition'=>array(
		'defaultvalue'=>'',
		'description'=>'The tag we:ifNotPosition allows to define an action which will NOT be done at a certain position of a block, a listview, a linklist or a listdir.  The parameter "position" can handle versatile values to control the first, last, all even, all odd or a specific position (1,2,3, ...). Is "type= block or linklist" it is necessary to specify the name (reference) of the related block/linklist.',
	),
	'ifNotRegisteredUser'=>array(
		'defaultvalue'=>'',
		'description'=>'Checks, if user is not registered.',
	),
	'ifNotReturnPage'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag will only be displayed after creation / modification and if the return value "return" from &lt;we:a edit="true"&gt; is "false" or not set.',
	),
	'ifNotSearch'=>array(
		'defaultvalue'=>'',
		'description'=>'By setting the &lt;we:ifNotSearch&gt;-tag, the contents between the start- and endtag are only displayed, if no search term has been transmitted by &lt;we:search&gt; or was empty. If the attribute "set" is set to "true", only the request-variable of &lt;we:search&gt; is validatetd for not being set.',
	),
	'ifNotSeeMode'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag is used to display the enclosed content only outside the seeMode.',
	),
	'ifNotSelf'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifNotSelf tag ensures that everything located between the start tag and the end tag will not be displayed if the document has one of the ID`s entered in the tag. If the tag is not located within we:linklist or we:listdir tags, "id" is a required field!',
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
		'description'=>'Content enclosed by this tag is only displayed if a subscription was not successful. This tag should appear in a template (for subscribing to newsletters) after &lt;we:addDelNewsletterEmail&gt;.',
	),
	'ifNotTemplate'=>array(
		'defaultvalue'=>'',
		'description'=>'Show enclosed content only if the current document is not based on the given template.<br /><br />You`ll find further information in the reference of the tag we:ifTemplate.',
	),
	'ifNotTop'=>array(
		'defaultvalue'=>'',
		'description'=>'The enclosed content is only displayed if this tag is located in an included document.',
	),
	'ifNotUnsubscribe'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if an unsubscribe request does not work as it should. This tag must appear in the template (for unsubscription) after a &lt;we:addDellnewsletterEmail&gt;.',
	),
	'ifNotVarSet'=>array(
		'defaultvalue'=>'',
		'description'=>'Contents enclosed by this tag are only displayed if the variable named "name" is not set. Note: "Not set" is not the same as "empty"!',
	),
	'ifNotVar'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifNotVar tag ensures that everything located between the start tag and the end tag is not displayed if the variable with the name "name" has the same value as entered under "match". The type of variable can be specified in the attribute "type".',
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
		'description'=>'Everything in between the start- and endtag is only displayed, if the voting was not saved. The attribute type specifies the type of the error.',
	),
	'ifNotVotingField'=>array(
		'description'=>'Checks if a votingField has not a value corresponding to the attribute match, the attribute combinations of name and type are the same as in the we:votingField tag',
	),
	'ifNotVotingIsRequired'=>array(
		'description'=>'Prints the enclosed content only, if the voting field is a required field',
	),
	'ifNotWebEdition'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only visible from outside webEdition.',
	),
	'ifNotWorkspace'=>array(
		'defaultvalue'=>'',
		'description'=>'Checks, whether the document is NOT located in the workspace specified in "path".',
	),
	'ifNotWritten'=>array(
		'defaultvalue'=>'',
		'description'=>'Contents enclosed by this tag are only displayed if an error occurs while writing a webEdition document or object using the &lt;we:write&gt; tag.',
	),
	'ifObjectLanguage'=>array(
		'defaultvalue'=>'',
		'description'=>'The tag we:ifObjectLanguage tests on the language setting in the properties tab of the object, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages',
	),
	'ifObject'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if the indvidual entry found by &lt;we:listview type="search"&gt; is an object.',
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
		'description'=>'The tag we:ifPosition allows to control the actual position of blocks, listviews, linklists or listdirs. The parameter "position" can handle versatile values to control the first, last, all even, all odd or a specific position (1,2,3, ...). Is "type= block or linklist" it is necessary to specify the name (reference) of the related block/linklist.',
	),
	'ifRegisteredUserCanChange'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag will only be displayed if a registered used who is logged in is allowed to edit the current webEdition document or object. In a listview the current document or object itteration is used.',
	),
	'ifRegisteredUser'=>array(
		'defaultvalue'=>'',
		'description'=>'Checks, if user is registered.',
	),
	'ifReturnPage'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed after a webEdition document or object is created or modified and the returned result "return" from &lt;we:a edit="document"&gt; or &lt;we:a edit="object"&gt; is "true".',
	),
	'ifSearch'=>array(
		'defaultvalue'=>'',
		'description'=>'By setting the &lt;we:ifSearch&gt;-tag, the contents between start- and endtag are only displayed, if a search term has been transmitted by &lt;we:search&gt; and is not empty. If the attribute "set" is set to "true", only the request-variable of &lt;we:search&gt; is validatetd for not being set.',
	),
	'ifSeeMode'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag is used to display the enclosed content only in seeMode.',
	),
	'ifSelf'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifSelf tag ensures that the content located between the start tag and the end tag will only be displayed if the document in question is specified with the attribute ID in the tag. If the tag is not located within we:linklist or we:listdir tags, "id" is a required field!',
	),
	'ifSendMail'=>array(
		'defaultvalue'=>'',
		'description'=>'Checks if a page is currently sent by we:sendMail and allows to exclude or include contents to the sent page',
	),
	'ifShopEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Everything between the start- and endtag will be shown if the shopping cart is empty.',
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
		'description'=>'Everything between the start- and endtag will be shown if the shopping cart is not empty.',
	),
	'ifShopPayVat'=>array(
		'defaultvalue'=>'',
		'description'=>'The enclosed content is only displayed if a logged in customer has to pay VAT.',
	),
	'ifShopVat'=>array(
		'defaultvalue'=>'',
		'description'=>'we:ifShopVat checks the VAT of the actual article (document/ shopping cart). The parameter Id allows to check the article`s VAT with for the inserted Id.',
	),
	'ifSidebar'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag is used to display the enclosed contents only if the opened document is located within the Sidebar.',
	),
	'ifSubscribe'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if a subscription to the newsletter was successful. It must be used in a subscription template after a &lt;we:addDelnewsletterEmail&gt; tag.',
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
		'description'=>'The enclosed content is only displayed if this tag is not located in an included document.',
	),
	'ifUnsubscribe'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if unsubscription of the newsletter was successful. It must be used in a subscription template after a &lt;we:addDellnewsletterEmail&gt; tag.',
	),
	'ifUserInputEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Contents enclosed by this tag is only displayed is the target user input field is empty.',
	),
	'ifUserInputNotEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Contents enclosed by this tag is only displayed is the target user input field is not empty.',
	),
	'ifVarEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if the variable named in attribute "match" is empty.',
	),
	'ifVarNotEmpty'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if the variable named in attribute "match" is not empty.',
	),
	'ifVarSet'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only displayed if the target variable is set. Note: "Set" is not the same as "not empty"!',
	),
	'ifVar'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:ifVar tag ensures that everything located between the start tag and the end tag is only displayed if the variable with the name "name" has the same value as entered under "match". The type of variable can be specified in the attribute "type".',
	),
	'ifVoteActive'=>array(
		'defaultvalue'=>'',
		'description'=>'Any content between the start- and endtag is only displayed, if the voting has not expired.',
	),
	'ifVoteIsRequired'=>array(
		'defaultvalue'=>'',
		'description'=>'Any content between the start- and endtag is only displayed, if the voting is a required field.',
	),
	'ifVote'=>array(
		'defaultvalue'=>'',
		'description'=>'Everything in between the start- and endtag is only displayed, if the voting was successfully saved.',
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
		'description'=>'Content enclosed by this tag is only displayed within webEdition, but not on the finished document. This tag is used for user prompts, etc.',
	),
	'ifWorkspace'=>array(
		'defaultvalue'=>'',
		'description'=>'Checks, whether the document is located in the workspace specified in "path" or "id".',
	),
	'ifWritten'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed by this tag is only available if the write process of  a webEdition document or object was successful. See &lt;we:write&gt;.',
	),
	'img'=>array(
		'description'=>'The we:img tag is required to insert an image in the content of the page. In edit mode, you can see an edit button. Clicking on the button will launch a file manager, which allows you to select an image that has been uploaded to or set up in webEdition. If the attributes "width", "height", "border", "hspace", "vspace", "alt", or "align" are set up, these attributes will be used for the image. Otherwise, the settings made for the image are in force. If the attribute ID is set up, the image will be used with this ID if no other image has been selected. The attribut showimage allows to hide the image itself in edit-mode, only the controlbuttons are shown then. With showinputs the input fields for alt and title can be deactivated.',
	),
	'include'=>array(
		'description'=>'This tag allows you to include a webEdition document or a HTML page in the template. This is particularly useful for navigation features or for sections that are the same on every template. If you work with the we:include tag, you do not need to change the navigation system in all the templates, changing it in the document you want to include will suffice. Afterwards, you only have to execute a "rebuild" and all the pages will be changed automatically. If all your pages are dynamic, you do not need to perform the "rebuild". Instead of the we:include tag, the page with the ID listed below will be inserted. With the attribute "gethttp" you can define whether the page should be transferred via HTTP or not.The attribute seem determines whether the document is editable in seeMode or not. This attribute only works when the document is included with the id.',
	),
	'input'=>array(
		'description'=>'The we:input tag creates a single-line input box in the edit mode of the document based on this template, if the type = "text" is selected. For all other types, see the manual or help.',
	),
	'js'=>array(
		'description'=>'The we:js tag creates an HTML tag that references an internal webEdition JavaScript document that has the ID listed below. You can define JavaScripts in a separate file.',
	),
	'keywords'=>array(
		'description'=>'The we:keywords tag generates a keywords meta tag. If the keywords field in the "Property" view is empty, the content placed between the start tag and the end tag will be used as the default keywords. Otherwise, the keywords from the Properties view will be entered.',
	),
	'linklist'=>array(
		'defaultvalue'=>'&lt;we:link /&gt;&lt;we:postlink&gt;&lt;br /&gt;&lt;/we:postlink&gt;',
		'description'=>'The we:linklist tag is used to generate link lists. A "+" button will appear in edit mode. Clicking this button will add a new link to the list. The appearance of the link list is determined by the HTML used in the list and by the use of "we:prelink" and "we:postlink" between &lt;we:link&gt; and &lt;/we:link&gt;. All the links inserted can be edited using an edit button and deleted using a delete button.',
	),
	'linkToSeeMode'=>array(
		'description'=>'This tag generates a link which opens the selected document in seeMode.',
	),
	'link'=>array(
		'description'=>'The we:link tag creates a single link which can be modified by using the "edit" button. The "name" attribute must not be specified between the we:linklist start tag and end tag. The "name" attribute must be specified outside the we:linklist tags."only" allows to return single attribut (only="name of attribute") of the link or only the content (only="content") of the link.',
	),
	'listdir'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:listdir tag creates a new list displaying all files in the same directory. In the attribute "field" you can specify the field which is to be displayed. If the field is empty or does not exist, the name of the file is displayed. Directories are examined regarding index files; if there is an index file, it will be displayed. Which field should be used to display directories can be specified in the attribute "dirfield". If the field is empty or does not exist, the entry of "field" respective to the name of the file is used. If the attribute "id" is set up, the files of the directory with the indicated ID are displayed.',
	),
	'listviewEnd'=>array(
		'description'=>'This tag displays the number of the last entry of the current &lt;we:listview&gt; page.',
	),
	'listviewPageNr'=>array(
		'description'=>'This tag returns the number of the current page of a &lt;we:listview&gt;.',
	),
	'listviewPages'=>array(
		'description'=>'This tag returns the number of pages of a &lt;we:listview&gt;.',
	),
	'listviewRows'=>array(
		'description'=>'This tag returns the number of entries found in a &lt;we:listview&gt;.',
	),
	'listviewStart'=>array(
		'description'=>'This tag displays the number of the first entry of the current &lt;we:listview&gt; page.',
	),
	'listview'=>array(
		'defaultvalue'=>'&lt;we:repeat&gt;
&lt;we:field name="Title" alt="we_path" hyperlink="true"/&gt;
&lt;br /&gt;
&lt;/we:repeat&gt;',
		'description'=>'The we:listview tag is the start tag and end tag of lists that are generated automatically (summary news pages etc.).',
	),
	'list'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:list tag allows you to create expandable lists. Everything located between the start tag and the end tag will be entered (any HTML and almost all we:tags) if you click the plus button in edit mode.',
	),
	'makeMail'=>array(
		'description'=>'This tag must be in the first line of every template in order to generate a webEdition document that is to be sent by &lt;we:sendMail/&gt;.',
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
		'description'=>'Within we:navigationEntry type="folder" this tag serves as a place holder for all entries of a folder of the navigation.',
	),
	'navigationEntry'=>array(
		'defaultvalue'=>'&lt;a href="&lt;we:navigationField name="href" /&gt;"&gt;&lt;we:navigationField name="text" /&gt;&lt;/a&gt;&lt;br /&gt;',
		'description'=>'With we:navigationEntry the look of an entry can be controlled within the navigation. With the attributes "type", "level", "current" and "position" single elements of various levels can be specifically picked and displayed.',
	),
	'navigationField'=>array(
		'description'=>'&lt;we:navigationField&gt; is used within &lt;we:navigationEntry&gt; to print a value of the current navigation entry.<br/>Choose from <b>either</b> the attribute <i>name</i>, <b>or</b> from the attribute <i>attributes</i>, <b>or</b> from the attribute <i>complete</i>',
	),
	'navigationWrite'=>array(
		'description'=>'Is used to write a we:navigation with given name',
	),
	'navigation'=>array(
		'description'=>'we:navigation is used to initialise a navigation made with the  navigation-tool.',
	),
	'newsletterConfirmLink'=>array(
		'defaultvalue'=>'Confirm newsletter',
		'description'=>'This tag is used to generate the double opt-in confirmation link.',
	),
	'newsletterField'=>array(
		'description'=>'Displays a field from the recipient dataset within the newsletter.',
	),
	'newsletterSalutation'=>array(
		'description'=>'This tag is used to display salutation fields.',
	),
	'newsletterUnsubscribeLink'=>array(
		'description'=>'Creates a link to unsubscribe from a newsletter list. This tag can only be used in mail templates!',
	),
	'next'=>array(
		'defaultvalue'=>'',
		'description'=>'Creates the HTML link tag that references the next page within listviews. The tag links any content found between the start tag and the end tag.',
	),
	'noCache'=>array(
		'defaultvalue'=>'',
		'description'=>'PHP-Code enclosed by this tag will be executed each time the cached document will be requested (Exception: Full-Cache)',
	),
	'objectLanguage'=>array(
		'defaultvalue'=>'',
		'description'=>'Shows the language of the object',
	),
	'object'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:object tag is used to display objects. The fields of an object can be displayed with we:field tags within the start tag and end tag. If just the attribute "name" for an object is set or has a value, the object selector will be displayed in the edit mode and the editor has the option to select all objects from all classes. If in addition the attribute "classid" has a value, the selection in the object selector will be reduced to all objects related to the class definded in "classid". With the attribute "id" you can define a preselection of a specific object defined by "classid" and "id". The attribute "triggerid" is used to display dynamic documents in a static object listview.',
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
		'defaultvalue'=>'',
		'description'=>'The we:postlink tag ensures that everything located between the start tag and the end tag will not be displayed for the last link in the link list.',
	),
	'prelink'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:prelink tag ensures that everything located between the start tag and the end tag will not be displayed for the first link in the link list.',
	),
	'printVersion'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:printVersion tag creates an HTML link tag that references to the same document, but with a different template. The attribute "tid" determines the ID of the template. The tag links any content between the start tag and the end tag.',
	),
	'processDateSelect'=>array(
		'description'=>'The tag &lt;we:processDateSelect&gt; processes the 3 values from the select boxes of the tag we:dateSelect into a UNIX timestamp. The value will be saved in a global variable with the name which was entered in the attribute "name&quuot;.',
	),
	'quicktime'=>array(
		'description'=>'The we:quicktime tag allows you to insert a Quicktime movie in the content of the document. Documents based on this template will display an edit button while in edit mode. Clicking on this button will launch a file manager, which allows you to select a Quicktime movie that you have already set up in webEdition. Currently there exists no xhtml-valid output working on both common browsers (IE, Mozilla). Therefore, xml is always set to "false"',
	),
	'registeredUser'=>array(
		'description'=>'This tag is used to print customer data stored in the customer modules.',
	),
	'registerSwitch'=>array(
		'description'=>'This tag generates a switch with which you can shift between the status of a registered and an unregistered user while in edit-mode. If you have used the &lt;we:ifRegisteredUser&gt; and &lt;we:ifNotRgisteredUser&gt; tags, this tag allows you to see the different views and and to keep control of the layout.',
	),
	'repeatShopItem'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag displays all articles in the shopping cart.',
	),
	'repeat'=>array(
		'defaultvalue'=>'',
		'description'=>'Content enclosed within this tag is repeated for every entry found by a &lt;we:listview&gt;. This tag is only used within a &lt;we:listview&gt; section.',
	),
	'returnPage'=>array(
		'description'=>'This tag is used to display the referring url of the source page, if the value of the attribute "return" was "true" when used in the tags: &lt;we:a edit="document"&gt; or &lt;we:a edit="object"&gt;',
	),
	'saferpay'=>array(
		'description'=>'we:saferpay implements an interface to the payment provider saferpay. To ensure that this tag works properly, add additional information in the backend of the shop module.',
	),
	'saveRegisteredUser'=>array(
		'description'=>'This tag saves all customer data entered by session fields.',
	),
	'search'=>array(
		'description'=>'The we:search tag creates an input box or a text box that is intended to be used for search queries. The search field has the internal name "we_search". When the search form is submitted, the PHP variable "we_search" on the receiving web page will be filled with the content from the input box.',
	),
	'select'=>array(
		'defaultvalue'=>'&lt;option&gt;#1&lt;/option&gt;
&lt;option&gt;#2&lt;/option&gt;
&lt;option&gt;#3&lt;/option&gt;',
		'description'=>'The we:select tag creates a select box for entry in edit mode. If "1" has been specified as size (size= "1" ), the select box appears as a pop-up menu. It behaves exactly as an HTML select tag does. Between the start tag and the end-tag, entries are determined by normal HTML option tags.',
	),
	'sendMail'=>array(
		'description'=>'This tag sends a webEdition page as an E-mail to the addresses which are defined in the attribute "recipient".',
	),
	'sessionField'=>array(
		'description'=>'The we:sessionField tag creates an HTML input, select or text area tag. It is used for any input in session fields (e. g. customer data, etc.).',
	),
	'sessionLogout'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:sessionLogout tag creates an HTML link tag referring to an internal webEdition document with the ID mentioned in the webEdition Tag Wizard. If this webEdition document has a we:sessionStart tag and holds the attribute "dynamic", the active session will be cleared and closed. No data will be saved.',
	),
	'sessionStart'=>array(
		'description'=>'This tag is used to start a session or to continue an existing one. This tag is required in templates that generate the following pages: Pages which are protected in some form by the Customer Mangement Module, Shop pages and pages which support front end input.&lt;br /&gt;This tag MUST be the first tag on the first line of the template!',
	),
	'setVar'=>array(
		'description'=>'This tag is used to set the values of various types of varibles.<br/><strong>Attention:</strong> Without the attribute <strong>striptags="true"</strong>, HTML- and PHP-Code is not filtered, this is a potenzial security risk!</strong>',
	),
	'shipping'=>array(
		'description'=>'In regard to the purchase we:shipping is used to determine shipping costs. These costs are based on the value of the shopping cart, the land of origin of the registered user and the shipping cost rules editable in the Shop Module. The parameter "sum" contains the name of a sum calculated with we:sum. The parameter "type" is used to determine either the net, gros as well as the amount of the VAT contained in the shipping costs.',
	),
	'shopField'=>array(
		'description'=>'This tag saves various input fields directly from an article or in the shopping cart (order). The administrator can define some values from which the customer can choose or enter an own value. It is therefore possible to map many article variants in a simple way.',
	),
	'shopVat'=>array(
		'description'=>'This tag is used to determine the VAT for an article. To adminstrate different VAT rates use the Shop Module. A given Id directly prints the VAT-Rate for this article.',
	),
	'showShopItemNumber'=>array(
		'description'=>'The we:showShopItemNumber tag shows the amount of specified items in the basket.',
	),
	'sidebar'=>array(
		'defaultvalue'=>'Open sidebar',
		'description'=>'',
	),
	'subscribe'=>array(
		'description'=>'This tag is used to add a single line input field to a webEdition document so that a user wanting to subscribe to a newsletter can enter his or her E-mail address.',
	),
	'sum'=>array(
		'description'=>'The we:sum tag sums up all figures in a list.',
	),
	'target'=>array(
		'description'=>'This tag is used to generate the link target from within &lt;we:linklist&gt;.',
	),
	'textarea'=>array(
		'description'=>'The we:textarea tag creates a multi-line input box.',
	),
	'title'=>array(
		'description'=>'The we:title tag creates a normal title tag. If the title field in the Properties view is empty, everything located between the start tag and the end tag will be used as the default title. Otherwise the title will be entered by the Properties view.',
	),
	'tr'=>array(
		'defaultvalue'=>'',
		'description'=>'The &lt;we:tr&gt; Tag corresponds to the HTML-tag &lt;tr&gt; and is used to define a table row.',
	),
	'unsubscribe'=>array(
		'description'=>'This tag is used to generate a single input field in a webEdition document so that a user can enter his or her E-mail address to unsubscribe from a news-letter.',
	),
	'url'=>array(
		'description'=>'The we:url tag creates an internal webEdition URL that references to the document that has the ID listed below.',
	),
	'userInput'=>array(
		'description'=>'The we:userInput tag creates input fields to use with we:form type="document" or type="object" in order to create documents or objects.',
	),
	'useShopVariant'=>array(
		'description'=>'The we:shopVariant tag uses the data of a article variant by the submitted name of the variant. Is there no variant with the given name the default article will be displayed.',
	),
	'var'=>array(
		'description'=>'The we:var tag displays the content of a global PHP variable respective to the content of a document field with the name listed below.',
	),
	'votingField'=>array(
		'description'=>'The we:votingField-tag is required to display the content of a voting. The attribute "name" defines what to show. The attribute "type", how to display it. Valid name-type combinations are: question - text; result - count, percent, total; id - answer, select, radio, voting; answer - text,radio,checkbox (select multiple) select, textinput and textarea (free text answer field), image (all we:img attributes such as thumbnail are supported), media (delivers the path utilizing to and nameto);',
	),
	'votingList'=>array(
		'defaultvalue'=>'',
		'description'=>'This tag automatically generates the voting lists.',
	),
	'votingSelect'=>array(
		'description'=>'Use this tag to generate a dropdown-menu; (&lt;select&gt;) to select voting.',
	),
	'votingSession'=>array(
		'description'=>'Generates an unique identifier which is stored in the voting log and allows to identify the answers to different questions which belong to a singele voting session',
	),
	'voting'=>array(
		'defaultvalue'=>'',
		'description'=>'The we:voting tag is used to display Votings.',
	),
	'writeShopData'=>array(
		'description'=>'The we:writeShopData tag writes all current shopping cart data into the database.',
	),
	'writeVoting'=>array(
		'description'=>'This tag writes a voting into the database. If the attribute "id" is defined, only the voting with the respective id will be saved.',
	),
	'write'=>array(
		'description'=>'This tag stores a document/object generated by &lt;we:form type="document/object&gt;',
	),
	'xmlfeed'=>array(
		'description'=>'The tag loads xml content from the given url',
	),
	'xmlnode'=>array(
		'defaultvalue'=>'',
		'description'=>'The tag prints a xml element from the given feed or url.',
));