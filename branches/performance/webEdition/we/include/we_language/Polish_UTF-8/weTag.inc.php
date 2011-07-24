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

$l_weTag['a']['description'] = "The we:a tag creates an HTML link tag that references an internal webEdition document that has the ID listed below. The tag links any content found between the start tag and the end tag."; // TRANSLATE
$l_weTag['a']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['addDelNewsletterEmail']['description'] = "This tag is used to add or remove an E-mail address from a newsletter address list. In attribute \"path\" the complete path to the newsletter list must be given. If the path begins without \"/\" the path will be emanated from DOCUMENT_ROOT. If you use several lists, you can enter several paths, separated by a comma.";

$l_weTag['addDelShopItem']['description'] = "Use the we:addDelShopItem tag to add or delete an article from the shopping cart."; // TRANSLATE
$l_weTag['addPercent']['description'] = "The we:addPercent tag adds a specified percentage, for example, VAT."; // TRANSLATE
$l_weTag['addPercent']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['answers']['description'] = "This tag displays the response possibilities of a voting."; // TRANSLATE
$l_weTag['answers']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['author']['description'] = "The we:author tag shows the creator of the document. If the attribute 'type' is not set, the user name will be displayed. If type=\"name\", the first and last name of the user will be displayed. If 'type=\"initials\", the initials of the user will be displayed. When no first or last name is entered, the username will be shown."; // TRANSLATE
$l_weTag['back']['description'] = "The we:back tag creates an HTML link tag that references the previous we:listview page. The tag links any content found between the start tag and the end tag."; // TRANSLATE
$l_weTag['back']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['banner']['description'] = "Use the we:banner tag to include a banner from the Banner Module."; // TRANSLATE
$l_weTag['bannerSelect']['description'] = "This tag displays a drop-down menu (&lt;select&gt;), to select banners. If the Customer Management Module is installed and the attribute customer is set to true, only banners of the logged-in customer will be shown."; // TRANSLATE
$l_weTag['bannerSum']['description'] = "The we:bannerSum tag displays the number of all shown or clicked banners or their click rate. The tag only works within a listview with type=\"banner\""; // TRANSLATE
$l_weTag['block']['description'] = "The we:block tag allows you to create expandable blocks/lists. Everything located between the start tag and the end tag will be entered (any HTML and almost all we:tags), if you click the plus button in edit mode."; // TRANSLATE
$l_weTag['block']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['calculate']['description'] = "The we:calculate tag allows all possible mathematical operations in PHP like *, /, +, -,(), sqrt..etc."; // TRANSLATE
$l_weTag['calculate']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['captcha']['description'] = "This tag generates an image with a random code. "; // TRANSLATE
$l_weTag['category']['description'] = "The we:category tag is replaced by the category (or categories) that was / were allocated to the document in the Properties view. If several categories have been allocated, they must be delimited using commas. If you wish to use another delimiter, you must specify it using the \"tokken\" attribute. Example: tokken = \" \" (In this case, a space is being used to delimit categories)."; // CHECK
// changed from: "The we:category tag is replaced by the category ( or categories ) that was / were allocated to the document in the Properties view. If several categories have been allocated, they must be delimited using commas. If you wish to use another delimiter, you must specify it using the \"delimiter\" attribute. Example: delimiter = \" \" ( In this case, a space is being used to delimit categories )."
// changed to  : "The we:category tag is replaced by the category (or categories) that was / were allocated to the document in the Properties view. If several categories have been allocated, they must be delimited using commas. If you wish to use another delimiter, you must specify it using the \"delimiter\" attribute. Example: delimiter = \" \" (In this case, a space is being used to delimit categories)."

$l_weTag['categorySelect']['description'] = "This tag is used to insert a drop-down (&lt;select&gt;) menu into a webEdition document. Use this tag to select a category. By placing an end tag immediatly after the start tag, you will cause the drop-down menu to contain all categories currently defined in webEdition."; // TRANSLATE
$l_weTag['categorySelect']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['charset']['description'] = "The we:charset tag generates a meta tag which determines the used charset for the page. \"ISO-8859-1\" is usually used for English Web pages. This tag must be placed within the meta tag of the HTML page."; // TRANSLATE
$l_weTag['charset']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['checkForm']['description'] = "The we:checkForm tag validates the entries of a form with JavaScript.&lt;br /&gt;The combination of the parameters 'match' and 'type' determine the 'name' or the 'id' of the form to check.&lt;br /&gt;'mandatory' and 'email' contain a commaseperated list of mandatory or e-mail fields. In 'password' it is possible to insert 2 names of fields and a minimum length of inserted passwords.&lt;br /&gt;With 'onError' you can choose the name of an individual JavaScript-function which is called in case of an error. This function will get an array with the names of missing mandatory and email fields and a flag, if the password was correct. If 'onError' is not set or the function does not exist, the default value is displayed in an alert-box.";
$l_weTag['checkForm']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['colorChooser']['description'] = "The we:colorChooser tag creates an input field for choosing a color value."; // TRANSLATE
$l_weTag['comment']['description'] = 'The comment Tag is used to generate explicit comments in the specified language, or, to add comments to the template which are not delivered to the user browser.';//TRANSLATE
$l_weTag['condition']['description'] = "This tag is used together with &lt;we:conditionAdd&gt; to dynamically add a condition to the attribute \"condition\" in a &lt;we:listview type=\"object\"&gt;. Conditions may be interlaced."; // TRANSLATE
$l_weTag['condition']['defaultvalue'] = "&lt;we:conditionAdd field=\"Type\" var=\"type\" compare=\"=\"/&gt;"; // TRANSLATE
$l_weTag['conditionAdd']['description'] = "This tag is used to add a new rule or condition within a &lt;we:condition&gt; block."; // TRANSLATE
$l_weTag['conditionAnd']['description'] = "This tag is used to add conditions within a &lt;we:condition&gt;. This is a logical AND, meaning that both existing conditions must be fulfilled."; // TRANSLATE
$l_weTag['conditionOr']['description'] = "This tag is used to add conditions within a &lt;we:condition&gt;. This is a logical OR, meaning that either one or the other of the conditions must be fulfilled."; // TRANSLATE
$l_weTag['content']['description'] = "&lt;we:content /&gt; is only used inside a mastertemplate. It determines the place where the content of the template is used in the mastertemplate."; // TRANSLATE
$l_weTag['controlElement']['description'] = "The tag we:controlElement can maniluate control elements in the edit view of a document. Buttons can be hidden. Checkboxes can be disabled, checked and/or hidden."; // TRANSLATE
$l_weTag['cookie']['description'] = "This tag is required with the Voting Module and sets a cookie, which denies more than one vote for a user. The tag needs to be placed at the very start of the template. There must not be any breaks or spaces in front of this tag."; // TRANSLATE
$l_weTag['createShop']['description'] = "The we:createShop tag is needed on every page that is supposed to contain shop data."; // TRANSLATE
$l_weTag['css']['description'] = "The css tag creates an HTML tag that references an internal webEdition CSS style sheet that has the ID listed below. You can define style sheets in a separate file."; // TRANSLATE
$l_weTag['customer']['description'] = "Using this tag, data from any customer can be displayed. The customer data are displayed as in a listview or within the &lt;we:object&gt; tag with the tag &lt;we:field&gt;.<br /><br />Combining the attributes, this tag can be utilized in three ways:<br/>If name is set, the editor can select a customer by using a customer-select-Field. This customer is stored in the document within the field name.<br />If name is not set but instead the id, the customer with this id is displayed.<br />If neither name nor id is set, the tag expects the id of the customer by a request parameter. This is i.e. used by the customer-listview when the attribut hyperlink=\"true\" in the &lt;we:field&gt; tag is used. The name of the request parameter is we_cid."; // TRANSLATE
$l_weTag['customer']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['date']['description'] = "The we:date tag displays the current date on the page as specified by the format string. If the document is static, the type should be set to &quot;js&quot;, so that the date is generated using JavaScript."; // TRANSLATE
$l_weTag['dateSelect']['description'] = "The we:dateSelect tag displays a select field for dates, which can be used together with the we:processDateSelect tag to read the date value into a variable as a UNIX time stamp."; // TRANSLATE
$l_weTag['delete']['description'] = "This tag is used to delete webEdition documents accessed via &lt;we:a edit=\"document\" delete=\"true\"&gt; or &lt;we:a edit=\"object\" delete=\"true\"&gt;."; // TRANSLATE
$l_weTag['deleteShop']['description'] = "The we:deleteShop tag deletes the complete shopping cart."; // TRANSLATE
$l_weTag['description']['description'] = "The we:description tag generates a description meta tag. If the description field in the Properties view is empty, the content placed between the start tag and the end tag will be used as the default description."; // TRANSLATE
$l_weTag['description']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['DID']['description'] = "This tag returns the ID of a webEdition document."; // TRANSLATE
$l_weTag['docType']['description'] = "This tag returns the document type of a webEdition document."; // TRANSLATE
$l_weTag['else']['description'] = "This tag is used to add alternative conditions within an if-type tag e.g. &lt;we:ifEditmode&gt;, &lt;we:ifNotVar&gt;, &lt;we:ifNotEmpty&gt;, &lt;we:ifFieldNotEmpty&gt;"; // TRANSLATE
$l_weTag['field']['description'] = "This tag inserts the content of the field that has the name defined in the \"name\" attribute into listviews. It may only be used between the start tag and end tag of we:repeat."; // TRANSLATE
$l_weTag['flashmovie']['description'] = "The we:flashmovie tag allows you to insert a Flash movie in the content of the document. Documents based on this template will display an edit button while in edit mode. Clicking on this button will launch a file manager, which allows you to select a Flash movie that you have already set up in webEdition."; // TRANSLATE
$l_weTag['form']['description'] = "The we:form tag is used to search and mail forms. It works in the same fashion as the normal HTML form tag, but allows the parser to insert additional hidden fields."; // TRANSLATE
$l_weTag['form']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['formfield']['description'] = "This tag is used to generate fields in a front end form."; // TRANSLATE
$l_weTag['formmail']['description'] = "With activated Setting Call Formmail via webEdition document, the integration of the formmail script is realized with a webEdition document. For this, the (currently without attributes) we-Tag formmail will be used. <br />If the Captcha-check is used, &lt;we:formmail/&gt; is located within the we-Tag ifCaptcha."; // TRANSLATE
$l_weTag['hidden']['description'] = "The we:hidden tag creates a hidden input tag holding the value of the global PHP variable with the same name. Use this tag if you want to forward incoming variables."; // TRANSLATE
$l_weTag['hidePages']['description'] = "The we:hidePages tag allows to disable some modes of a document. You can use this tag i.e. , to restrict access to the properity page of a document. In this case, it is not possible to park this document any more."; // TRANSLATE
$l_weTag['href']['description'] = "The we:href tag creates a URL that can be entered in edit mode."; // TRANSLATE
$l_weTag['icon']['description'] = "The we:icon tag creates an HTML tag that references an internal webEdition icon that has the ID listed below. With this tag, you can include an icon that is displayed in the Internet Explorer, Mozilla, Safari and Opera while book marking your home page."; // TRANSLATE
$l_weTag['ifBack']['description'] = "This tag is used between the start and end tags of &lt;we:listview&gt;. Everything between the start and end tags of this tag is displayed only if a previous page exists. For example, you can use this tag on the second page of a 20 item listview where 5 items are displayed per page."; // TRANSLATE
$l_weTag['ifBack']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifCaptcha']['description'] = "Content enclosed by this tag is only displayed if the code entered by the user is valid."; // TRANSLATE
$l_weTag['ifCaptcha']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifCat']['description'] = "The we:ifCat tag ensures that everything located between the start tag and the end tag is only displayed if the categories which are entered under \"categories\" are one of the document's categories."; // TRANSLATE
$l_weTag['ifCat']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotCat']['description'] = "The we:ifNotCat tag ensures that everything located between the start tag and the end tag is only displayed if the categories which are entered under \"categories\" are none of the document's categories."; // TRANSLATE
$l_weTag['ifNotCat']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifClient']['description'] = "The we:ifClient tag ensures that everything located between the start tag and the end tag will only be displayed if the client (browser) meets the established standards. This tag only works with dynamically saved pages!"; // TRANSLATE
$l_weTag['ifClient']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifConfirmFailed']['description'] = "When using DoubleOptIn with the Newsletter Module, &lt;we:ifConfirmFailed&gt; checks, if the E-Mail address was confirmed."; // TRANSLATE
$l_weTag['ifConfirmFailed']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifCurrentDate']['description'] = "This tag highlights the current day within a calendar-listview."; // TRANSLATE
$l_weTag['ifCurrentDate']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifDeleted']['description'] = "Content enclosed by the start and end tags of this tag are only displayed if a particular document or object was deleted using &lt;we:delete/&gt;"; // TRANSLATE
$l_weTag['ifDeleted']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifDoctype']['description'] = "The we:ifDoctype tag ensures that everything located between the start tag and the end tag is only displayed if the document type which is entered under \"doctype\" is the same as the document's document type."; // TRANSLATE
$l_weTag['ifDoctype']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifDoubleOptIn']['description'] = "Content enclosed by this tag is only displayed during the first part of a double opt-in process."; // TRANSLATE
$l_weTag['ifDoubleOptIn']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifEditmode']['description'] = "This tag is used to display the enclosed content only in edit mode."; // TRANSLATE
$l_weTag['ifEditmode']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifEmailExists']['description'] = "Contents enclosed by this tag are only displayed if a specified email address is in the newsletter address list."; // TRANSLATE
$l_weTag['ifEmailExists']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifEmailInvalid']['description'] = "Content enclosed by this tag is only visible if a particular email is incorrect in its syntax."; // TRANSLATE
$l_weTag['ifEmailInvalid']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifEmailNotExists']['description'] = "Contents enclosed by this tag are only displayed if the email address in question is not in the newsletter address list."; // TRANSLATE
$l_weTag['ifEmailNotExists']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifEmpty']['description'] = "The we:ifEmpty tag ensures that everything located between the start tag and the end tag is only displayed if the field having the same name as entered under \"match\" is empty. The type of field must be specified in the attribute \"type\" if it is an \"img\", \"flashmovie\" or \"href\" field."; // TRANSLATE
$l_weTag['ifEmpty']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifEqual']['description'] = "The we:ifEqual tag compares the content of the fields \"name\" and \"eqname\". If the content of both fields is the same, everything between start tag and end tag will be displayed. If the tag is used in we:list, we:block or we:linklist, only one field within these tags can be compared with one field outside. In this case you have to set the attribute \"name\" to the name of the field within the we:block, we:list or we:linklist-tags. The attribute eqname then has to be set to the name of a field outside these tags. The tag can also be located within dynamically included webEdition pages. In this case, \"name\" is set to a field within the included page and \"eqname\" is set to the name of a field in the main page. If the attribute \"value\" is filled, \"eqname\" will be ignored and the content of the field \"name\" will be compared with the value filled in the attribute \"value\"."; // TRANSLATE
$l_weTag['ifEqual']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifFemale']['description'] = "Content enclosed by this tag only appears if the user selects the female salutation."; // TRANSLATE
$l_weTag['ifFemale']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifField']['description'] = "This tag is used between the start tag and end tag of we:repeat. Everything between the start and end tags of this tag is displayed only if the value of the attribut \"match\" is identical with the value of database field of the associated listview entry."; // TRANSLATE
$l_weTag['ifField']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifFieldEmpty']['description'] = "The we:ifFieldEmpty tag ensures that everything located between the start tag and the end tag is only displayed if the listview field with the name listed in \"match\" is empty. The type of field must be specified in the attribute \"type\" if it is an \"img\", \"flashmovie\" or \"href\" field."; // TRANSLATE
$l_weTag['ifFieldEmpty']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifFieldNotEmpty']['description'] = "The we:ifFieldNotEmpty tag ensures that everything located between the start tag and the end tag is only displayed if the listview field with the name listed in \"match\" is not empty. The type of field must be specified in the attribute \"type\" if it is an \"img\", \"flashmovie\" or \"href\" field."; // TRANSLATE
$l_weTag['ifFieldNotEmpty']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifFound']['description'] = "Content enclosed by this tag is only displayed if documents are found within a &lt;we:listview&gt;."; // TRANSLATE
$l_weTag['ifFound']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifHasChildren']['description'] = "Within the &lt;we:repeat&gt; tag &lt;we:ifHasChildren&gt; is used to query if a category(folder) has child categories."; // TRANSLATE
$l_weTag['ifHasChildren']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifHasCurrentEntry']['description'] = "we:ifHasCurrentEntry can be used within we:navigationEntry type=\"folder\" to show content, only if the navigation folder contains the activ entry"; // TRANSLATE
$l_weTag['ifHasCurrentEntry']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifHasEntries']['description'] = "we:ifHasEntries can be used within we:navigationEntry to show content only, if the navigation entry contains entries."; // TRANSLATE
$l_weTag['ifHasEntries']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifHasShopVariants']['description'] = "The tag we:ifHasShopVariants can display content depending on the existance of variants in an object or document. With this, it can be controlled whether a &lt;we:listview type=\"shopVariant\"&gt; should be displayed at all. <b>This tag works in document and object templates attached by object-workspaces, but not inside the we:listview and we:object - tags</b>";// TRANSLATE
$l_weTag['ifHasShopVariants']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifHtmlMail']['description'] = "Content enclosed by this tag is only displayed if the format of a newsletter is HTML."; // TRANSLATE
$l_weTag['ifHtmlMail']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifIsDomain']['description'] = "The we:iflsDomain tag ensures that everything located between the start tag and the end tag is only displayed if the domain name of the server has the same name as entered under \"domain\". The result can only be seen on the finished Web site or in the preview. In edit mode everything is displayed."; // TRANSLATE
$l_weTag['ifIsDomain']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifIsNotDomain']['description'] = "The we:iflsNotDomain tag ensures that everything located between the start tag and the end tag is only displayed if the domain name of the server has not the same name as entered under \"domain\".The result can only be seen on the finished Web site or in the preview. In edit mode everything is displayed."; // TRANSLATE
$l_weTag['ifIsNotDomain']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifLastCol']['description'] = "&lt;we:ifLastCol&gt; can detect the last col of a table row, when using the table functions of a &lt;we:listview&gt;"; // TRANSLATE
$l_weTag['ifLastCol']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifLoginFailed']['description'] = "Content enclosed by this tag is only displayed if a login failed."; // TRANSLATE
$l_weTag['ifLoginFailed']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifLogin']['description'] = "Content enclosed by this tag is only displayed if a login occured on the page and allows for initialisation.";// TRANSLATE
$l_weTag['ifLogin']['defaultvalue'] = "";
$l_weTag['ifLogout']['description'] = "Content enclosed by this tag is only displayed if a logout occured on the page and allows for cleaning up.";// TRANSLATE
$l_weTag['ifLogout']['defaultvalue'] = "";
$l_weTag['ifTdEmpty']['description'] = "Content enclosed by this tag is only displayed if a table cell is empty (has no contents in a listview).";// TRANSLATE
$l_weTag['ifTdEmpty']['defaultvalue'] = "";
$l_weTag['ifTdNotEmpty']['description'] = "Content enclosed by this tag is only displayed if a table cell is not empty (has contents in a listview).";// TRANSLATE
$l_weTag['ifTdNotEmpty']['defaultvalue'] = "";
$l_weTag['ifMailingListEmpty']['description'] = "Content enclosed by this tag is only displayed if the user has not selected any newsletter."; // TRANSLATE
$l_weTag['ifMailingListEmpty']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifMale']['description'] = "Content enclosed by this tag is only displayed if the user is male. This tag is used for the salutation in newsletters."; // TRANSLATE
$l_weTag['ifMale']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNew']['description'] = "Content enclosed by this tag is only displayed in a new webEdition document or object."; // TRANSLATE
$l_weTag['ifNew']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNewsletterSalutationEmpty']['description'] = "Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is empty.";// TRANSLATE
$l_weTag['ifNewsletterSalutationEmpty']['defaultvalue'] = "";
$l_weTag['ifNewsletterSalutationNotEmpty']['description'] = "Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is not empty.";// TRANSLATE
$l_weTag['ifNewsletterSalutationNotEmpty']['defaultvalue'] = "";
$l_weTag['ifNotNewsletterSalutation']['description'] = "Content enclosed by this tag is only displayed within a newsletter, if the salutation field defined in type is not equal to match.";// TRANSLATE
$l_weTag['ifNotNewsletterSalutation']['defaultvalue'] = "";
$l_weTag['ifNext']['description'] = "Content enclosed by this tag is displayed only if a next page of items is available in a &lt;we:listview&gt;"; // TRANSLATE
$l_weTag['ifNext']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNoJavaScript']['description'] = "This tag redirects a page to a specified ID if the browser used does not provide JavaScript support or if JavaScript support is deactivated. This tag can only appear in the &lt;head&gt; section of the template."; // TRANSLATE
$l_weTag['ifNotCaptcha']['description'] = "Content enclosed by this tag is only displayed if the code entered by the user is not valid."; // TRANSLATE
$l_weTag['ifNotCaptcha']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotDeleted']['description'] = "Content enclosed by this tag is only displayed if a webEdition document or object could not be deleted by &lt;we:delete/&gt;"; // TRANSLATE
$l_weTag['ifNotDeleted']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotDoctype']['description'] = ""; // TRANSLATE
$l_weTag['ifNotDoctype']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotEditmode']['description'] = "Content enclosed by this tag is not displayed in edit mode."; // TRANSLATE
$l_weTag['ifNotEditmode']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotEmpty']['description'] = "The we:ifNotEmpty tag ensures that everything located between the start tag and the end tag is only displayed if the field having the same name as entered under \"match\" is not empty. The type of field must be specified in the attribute \"type\", if it is a \"img\", \"flashmovie\" or \"href\" field."; // TRANSLATE
$l_weTag['ifNotEmpty']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotEqual']['description'] = "The we:ifNotEqual tag compares the content of the fields \"name\" and \"eqname\". If the content of both fields is the same, everything between start- and endtag will not be displayed. If the tag is used in we:list, we:block or we:linklist, only one field within these tags can be compared with one field outside. In this case you have to set the Attribute \"name\" to the name of the field within the we:block, we:list or we:linklist-tags. The attribute eqname then has to be set to the name of a field outside these tags. The tag can also be located within dynamically included webEdition - pages. In this case \"name\" is set to a field within the included page and \"eqname\" is set to the name of a field in the main page. If the attribute \"value\" is filled, \"eqname\" will be ignored and the content of the field \"name\" will be compared with the value filled in the attribute \"value\"."; // TRANSLATE
$l_weTag['ifNotEqual']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotField']['description'] = "This tag is used between the start tag and end tag of we:repeat. Everything between the start and end tags of this tag is displayed only if the value of the attribut \"match\" is not identical with the value of database field of the associated listview entry."; // TRANSLATE
$l_weTag['ifNotField']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotFound']['description'] = "Content enclosed by this tag is displayed only if nothing is found by a &lt;we:listview&gt;."; // TRANSLATE
$l_weTag['ifNotFound']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotHtmlMail']['description'] = "Content enclosed by this tag is only displayed in an HTML newsletter document."; // TRANSLATE
$l_weTag['ifNotHtmlMail']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotNew']['description'] = "Content enclosed by this tag is only displayed in an old webEdition document or object."; // TRANSLATE
$l_weTag['ifNotNew']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotObject']['description'] = "The enclosed content is only displayed if the entry within &lt;we:listview type=\"search\"&gt; is not an object.&lt;br /&gt;";
$l_weTag['ifNotObject']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotPosition']['description'] = "The tag we:ifNotPosition allows to define an action which will NOT be done at a certain position of a block, a listview, a linklist or a listdir.  The parameter \"position\" can handle versatile values to control the first, last, all even, all odd or a specific position (1,2,3, ...). Is \"type= block or linklist\" it is necessary to specify the name (reference) of the related block/linklist."; // TRANSLATE
$l_weTag['ifNotPosition']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotRegisteredUser']['description'] = "Checks, if user is not registered."; // TRANSLATE
$l_weTag['ifNotRegisteredUser']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotReturnPage']['description'] = "Content enclosed by this tag will only be displayed after creation / modification and if the return value \"return\" from &lt;we:a edit=\"true\"&gt; is \"false\" or not set."; // TRANSLATE
$l_weTag['ifNotReturnPage']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotSearch']['description'] = "By setting the &lt;we:ifNotSearch&gt;-tag, the contents between the start- and endtag are only displayed, if no search term has been transmitted by &lt;we:search&gt; or was empty. If the attribute \"set\" is set to \"true\", only the request-variable of &lt;we:search&gt; is validatetd for not being set."; // TRANSLATE
$l_weTag['ifNotSearch']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotSeeMode']['description'] = "This tag is used to display the enclosed content only outside the seeMode."; // TRANSLATE
$l_weTag['ifNotSeeMode']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotSelf']['description'] = "The we:ifNotSelf tag ensures that everything located between the start tag and the end tag will not be displayed if the document has one of the ID's entered in the tag. If the tag is not located within we:linklist or we:listdir tags, \"id\" is a required field!"; // TRANSLATE
$l_weTag['ifNotSelf']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotSidebar']['description'] = "This tag is used to display the enclosed contents only if the opened document is not located within the Sidebar."; // TRANSLATE
$l_weTag['ifNotSidebar']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotSubscribe']['description'] = "Content enclosed by this tag is only displayed if a subscription was not successful. This tag should appear in a template (for subscribing to newsletters) after &lt;we:addDelNewsletterEmail&gt;."; // TRANSLATE
$l_weTag['ifNotSubscribe']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotTemplate']['description'] = "Show enclosed content only if the current document is not based on the given template.<br /><br />You'll find further information in the reference of the tag we:ifTemplate."; // TRANSLATE
$l_weTag['ifNotTemplate']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotTop']['description'] = "The enclosed content is only displayed if this tag is located in an included document."; // TRANSLATE
$l_weTag['ifNotTop']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotUnsubscribe']['description'] = "Content enclosed by this tag is only displayed if an unsubscribe request does not work as it should. This tag must appear in the template (for unsubscription) after a &lt;we:addDellnewsletterEmail&gt;."; // TRANSLATE
$l_weTag['ifNotUnsubscribe']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotVar']['description'] = "The we:ifNotVar tag ensures that everything located between the start tag and the end tag is not displayed if the variable with the name \"name\" has the same value as entered under \"match\". The type of variable can be specified in the attribute \"type\"."; // TRANSLATE
$l_weTag['ifNotVar']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotVarSet']['description'] = "Contents enclosed by this tag are only displayed if the variable named \"name\" is not set. Note: \"Not set\" is not the same as \"empty\"!"; // TRANSLATE
$l_weTag['ifNotVarSet']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotVote']['description'] = "Everything in between the start- and endtag is only displayed, if the voting was not saved. The attribute type specifies the type of the error."; // TRANSLATE
$l_weTag['ifNotVote']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotWebEdition']['description'] = "Content enclosed by this tag is only visible from outside webEdition."; // TRANSLATE
$l_weTag['ifNotWebEdition']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotWorkspace']['description'] = "Checks, whether the document is NOT located in the workspace specified in \"path\"."; // TRANSLATE
$l_weTag['ifNotWorkspace']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotWritten']['description'] = "Contents enclosed by this tag are only displayed if an error occurs while writing a webEdition document or object using the &lt;we:write&gt; tag."; // TRANSLATE
$l_weTag['ifNotWritten']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifObject']['description'] = "Content enclosed by this tag is only displayed if the indvidual entry found by &lt;we:listview type=\"search\"&gt; is an object."; // TRANSLATE
$l_weTag['ifObject']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifPosition']['description'] = "The tag we:ifPosition allows to control the actual position of blocks, listviews, linklists or listdirs. The parameter \"position\" can handle versatile values to control the first, last, all even, all odd or a specific position (1,2,3, ...). Is \"type= block or linklist\" it is necessary to specify the name (reference) of the related block/linklist."; // TRANSLATE
$l_weTag['ifPosition']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifRegisteredUser']['description'] = "Checks, if user is registered."; // TRANSLATE
$l_weTag['ifRegisteredUser']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifRegisteredUserCanChange']['description'] = "Content enclosed by this tag will only be displayed if a registered used who is logged in is allowed to edit the current webEdition document or object. In a listview the current document or object itteration is used."; // TRANSLATE
$l_weTag['ifRegisteredUserCanChange']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifReturnPage']['description'] = "Content enclosed by this tag is only displayed after a webEdition document or object is created or modified and the returned result \"return\" from &lt;we:a edit=\"document\"&gt; or &lt;we:a edit=\"object\"&gt; is \"true\"."; // TRANSLATE
$l_weTag['ifReturnPage']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifSearch']['description'] = "By setting the &lt;we:ifSearch&gt;-tag, the contents between start- and endtag are only displayed, if a search term has been transmitted by &lt;we:search&gt; and is not empty. If the attribute \"set\" is set to \"true\", only the request-variable of &lt;we:search&gt; is validatetd for not being set."; // TRANSLATE
$l_weTag['ifSearch']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifSeeMode']['description'] = "This tag is used to display the enclosed content only in seeMode."; // TRANSLATE
$l_weTag['ifSeeMode']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifSelf']['description'] = "The we:ifSelf tag ensures that the content located between the start tag and the end tag will only be displayed if the document in question is specified with the attribute ID in the tag. If the tag is not located within we:linklist or we:listdir tags, \"id\" is a required field!"; // TRANSLATE
$l_weTag['ifSelf']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifShopEmpty']['description'] = "Everything between the start- and endtag will be shown if the shopping cart is empty."; // TRANSLATE
$l_weTag['ifShopEmpty']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifShopNotEmpty']['description'] = "Everything between the start- and endtag will be shown if the shopping cart is not empty."; // TRANSLATE
$l_weTag['ifShopNotEmpty']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifShopPayVat']['description'] = "The enclosed content is only displayed if a logged in customer has to pay VAT."; // TRANSLATE
$l_weTag['ifShopPayVat']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifShopVat']['description'] = "we:ifShopVat checks the VAT of the actual article (document/ shopping cart). The parameter Id allows to check the article\"s VAT with for the inserted Id."; // CHECK
// changed from: "we:ifShopVat checks the VAT of the actual article (document/ shopping cart). The parameter Id allows to check the article\"s VAT with for the inserted Id."
// changed to  : "we:ifShopVat checks the VAT of the actual article (document/ shopping cart). The parameter Id allows to check the article's VAT with for the inserted Id."

$l_weTag['ifShopVat']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifSidebar']['description'] = "This tag is used to display the enclosed contents only if the opened document is located within the Sidebar."; // TRANSLATE
$l_weTag['ifSidebar']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifSubscribe']['description'] = "Content enclosed by this tag is only displayed if a subscription to the newsletter was successful. It must be used in a subscription template after a &lt;we:addDelnewsletterEmail&gt; tag."; // TRANSLATE
$l_weTag['ifSubscribe']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifTemplate']['description'] = ""; // TRANSLATE
$l_weTag['ifTemplate']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifTop']['description'] = "The enclosed content is only displayed if this tag is not located in an included document."; // TRANSLATE
$l_weTag['ifTop']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifUnsubscribe']['description'] = "Content enclosed by this tag is only displayed if unsubscription of the newsletter was successful. It must be used in a subscription template after a &lt;we:addDellnewsletterEmail&gt; tag."; // TRANSLATE
$l_weTag['ifUnsubscribe']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifUserInputEmpty']['description'] = "Contents enclosed by this tag is only displayed is the target user input field is empty."; // TRANSLATE
$l_weTag['ifUserInputEmpty']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifUserInputNotEmpty']['description'] = "Contents enclosed by this tag is only displayed is the target user input field is not empty."; // TRANSLATE
$l_weTag['ifUserInputNotEmpty']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifVar']['description'] = "The we:ifVar tag ensures that everything located between the start tag and the end tag is only displayed if the variable with the name \"name\" has the same value as entered under \"match\". The type of variable can be specified in the attribute \"type\"."; // TRANSLATE
$l_weTag['ifVar']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifVarEmpty']['description'] = "Content enclosed by this tag is only displayed if the variable named in attribute \"match\" is empty."; // TRANSLATE
$l_weTag['ifVarEmpty']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifVarNotEmpty']['description'] = "Content enclosed by this tag is only displayed if the variable named in attribute \"match\" is not empty."; // TRANSLATE
$l_weTag['ifVarNotEmpty']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifVarSet']['description'] = "Content enclosed by this tag is only displayed if the target variable is set. Note: \"Set\" is not the same as \"not empty\"!"; // TRANSLATE
$l_weTag['ifVarSet']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifVote']['description'] = "Everything in between the start- and endtag is only displayed, if the voting was successfully saved."; // TRANSLATE
$l_weTag['ifVote']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifVoteActive']['description'] = "Any content between the start- and endtag is only displayed, if the voting has not expired."; // TRANSLATE
$l_weTag['ifVoteActive']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifWebEdition']['description'] = "Content enclosed by this tag is only displayed within webEdition, but not on the finished document. This tag is used for user prompts, etc."; // TRANSLATE
$l_weTag['ifWebEdition']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifWorkspace']['description'] = "Checks, whether the document is located in the workspace specified in \"path\" or \"id\"."; // TRANSLATE
$l_weTag['ifWorkspace']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifWritten']['description'] = "Content enclosed by this tag is only available if the write process of  a webEdition document or object was successful. See &lt;we:write&gt;.";
$l_weTag['ifWritten']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['img']['description'] = "The we:img tag is required to insert an image in the content of the page. In edit mode, you can see an edit button. Clicking on the button will launch a file manager, which allows you to select an image that has been uploaded to or set up in webEdition. If the attributes \"width\", \"height\", \"border\", \"hspace\", \"vspace\", \"alt\", or \"align\" are set up, these attributes will be used for the image. Otherwise, the settings made for the image are in force. If the attribute ID is set up, the image will be used with this ID if no other image has been selected. The attribut showimage allows to hide the image itself in edit-mode, only the controlbuttons are shown then. With showinputs the input fields for alt and title can be deactivated."; // TRANSLATE
$l_weTag['include']['description'] = "This tag allows you to include a webEdition document or a HTML page in the template. This is particularly useful for navigation features or for sections that are the same on every template. If you work with the we:include tag, you do not need to change the navigation system in all the templates, changing it in the document you want to include will suffice. Afterwards, you only have to execute a \"rebuild\" and all the pages will be changed automatically. If all your pages are dynamic, you do not need to perform the \"rebuild\". Instead of the we:include tag, the page with the ID listed below will be inserted. With the attribute \"gethttp\" you can define whether the page should be transferred via HTTP or not.The attribute seem determines whether the document is editable in seeMode or not. This attribute only works when the document is included with the id."; // TRANSLATE
$l_weTag['input']['description'] = "The we:input tag creates a single-line input box in the edit mode of the document based on this template, if the type = \"text\" is selected. For all other types, see the manual or help."; // TRANSLATE
$l_weTag['js']['description'] = "The we:js tag creates an HTML tag that references an internal webEdition JavaScript document that has the ID listed below. You can define JavaScripts in a separate file."; // TRANSLATE
$l_weTag['keywords']['description'] = "The we:keywords tag generates a keywords meta tag. If the keywords field in the \"Property\" view is empty, the content placed between the start tag and the end tag will be used as the default keywords. Otherwise, the keywords from the Properties view will be entered."; // TRANSLATE
$l_weTag['link']['description'] = "The we:link tag creates a single link which can be modified by using the \"edit\" button. The \"name\" attribute must not be specified between the we:linklist start tag and end tag. The \"name\" attribute must be specified outside the we:linklist tags.\"only\" allows to return single attribut (only=\"name of attribute\") of the link or only the content (only=\"content\") of the link."; // TRANSLATE
$l_weTag['linklist']['description'] = "The we:linklist tag is used to generate link lists. A \"+\" button will appear in edit mode. Clicking this button will add a new link to the list. The appearance of the link list is determined by the HTML used in the list and by the use of \"we:prelink\" and \"we:postlink\" between &lt;we:link&gt; and &lt;/we:link&gt;. All the links inserted can be edited using an edit button and deleted using a delete button."; // TRANSLATE
$l_weTag['linklist']['defaultvalue'] = "&lt;we:link /&gt;&lt;we:postlink&gt;&lt;br /&gt;&lt;/we:postlink&gt;"; // TRANSLATE
$l_weTag['linkToSeeMode']['description'] = "This tag generates a link which opens the selected document in seeMode."; // TRANSLATE
$l_weTag['list']['description'] = "The we:list tag allows you to create expandable lists. Everything located between the start tag and the end tag will be entered (any HTML and almost all we:tags) if you click the plus button in edit mode."; // TRANSLATE
$l_weTag['list']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['listdir']['description'] = "The we:listdir tag creates a new list displaying all files in the same directory. In the attribute \"field\" you can specify the field which is to be displayed. If the field is empty or does not exist, the name of the file is displayed. Directories are examined regarding index files; if there is an index file, it will be displayed. Which field should be used to display directories can be specified in the attribute \"dirfield\". If the field is empty or does not exist, the entry of \"field\" respective to the name of the file is used. If the attribute \"id\" is set up, the files of the directory with the indicated ID are displayed."; // TRANSLATE
$l_weTag['listdir']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['listview']['description'] = "The we:listview tag is the start tag and end tag of lists that are generated automatically (summary news pages etc.)."; // TRANSLATE
$l_weTag['listview']['defaultvalue'] = "&lt;we:repeat&gt;
&lt;we:field name=\"Title\" alt=\"we_path\" hyperlink=\"true\"/&gt;
&lt;br /&gt;
&lt;/we:repeat&gt;";
$l_weTag['listviewEnd']['description'] = "This tag displays the number of the last entry of the current &lt;we:listview&gt; page."; // TRANSLATE
$l_weTag['listviewPageNr']['description'] = "This tag returns the number of the current page of a &lt;we:listview&gt;."; // TRANSLATE
$l_weTag['listviewPages']['description'] = "This tag returns the number of pages of a &lt;we:listview&gt;."; // TRANSLATE
$l_weTag['listviewRows']['description'] = "This tag returns the number of entries found in a &lt;we:listview&gt;."; // TRANSLATE
$l_weTag['listviewStart']['description'] = "This tag displays the number of the first entry of the current &lt;we:listview&gt; page."; // TRANSLATE
$l_weTag['makeMail']['description'] = "This tag must be in the first line of every template in order to generate a webEdition document that is to be sent by &lt;we:sendMail/&gt;."; // TRANSLATE
$l_weTag['master']['description'] = ""; // TRANSLATE
$l_weTag['master']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['metadata']['description'] = ""; // TRANSLATE
$l_weTag['metadata']['defaultvalue'] = "&lt;we:field name=\"NameOfField\" /&gt;"; // TRANSLATE
$l_weTag['navigation']['description'] = "we:navigation is used to initialise a navigation made with the  navigation-tool."; // TRANSLATE
$l_weTag['navigationEntries']['description'] = "Within we:navigationEntry type=\"folder\" this tag serves as a place holder for all entries of a folder of the navigation."; // TRANSLATE
$l_weTag['navigationEntry']['description'] = "With we:navigationEntry the look of an entry can be controlled within the navigation. With the attributes \"type\", \"level\", \"current\" and \"position\" single elements of various levels can be specifically picked and displayed."; // TRANSLATE
$l_weTag['navigationEntry']['defaultvalue'] = "&lt;a href=\"&lt;we:navigationField name=\"href\" /&gt;\"&gt;&lt;we:navigationField name=\"text\" /&gt;&lt;/a&gt;&lt;br /&gt;"; // TRANSLATE
$l_weTag['navigationField']['description'] = "&lt;we:navigationField&gt; is used within &lt;we:navigationEntry&gt; to print a value of the current navigation entry.<br/>Choose from <b>either</b> the attribute <i>name</i>, <b>or</b> from the attribute <i>attributes</i>, <b>or</b> from the attribute <i>complete</i>";// TRANSLATE
$l_weTag['navigationWrite']['description'] = "Is used to write a we:navigation with given name"; // TRANSLATE
$l_weTag['newsletterConfirmLink']['description'] = "This tag is used to generate the double opt-in confirmation link."; // TRANSLATE
$l_weTag['newsletterConfirmLink']['defaultvalue'] = "Confirm newsletter"; // TRANSLATE
$l_weTag['newsletterField']['description'] = "Displays a field from the recipient dataset within the newsletter."; // TRANSLATE
$l_weTag['newsletterSalutation']['description'] = "This tag is used to display salutation fields."; // TRANSLATE
$l_weTag['newsletterUnsubscribeLink']['description'] = "Creates a link to unsubscribe from a newsletter list. This tag can only be used in mail templates!"; // TRANSLATE
$l_weTag['next']['description'] = "Creates the HTML link tag that references the next page within listviews. The tag links any content found between the start tag and the end tag."; // TRANSLATE
$l_weTag['next']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['noCache']['description'] = "PHP-Code enclosed by this tag will be executed each time the cached document will be requested (Exception: Full-Cache)"; // TRANSLATE
$l_weTag['noCache']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['object']['description'] = "The we:object tag is used to display objects. The fields of an object can be displayed with we:field tags within the start tag and end tag. If just the attribute \"name\" for an object is set or has a value, the object selector will be displayed in the edit mode and the editor has the option to select all objects from all classes. If in addition the attribute \"classid\" has a value, the selection in the object selector will be reduced to all objects related to the class definded in \"classid\". With the attribute \"id\" you can define a preselection of a specific object defined by \"classid\" and \"id\". The attribute \"triggerid\" is used to display dynamic documents in a static object listview."; // TRANSLATE
$l_weTag['object']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['pagelogger']['description'] = "The we:pagelogger tag generates, depending on the selected \"type\" attribute, the necessary capture code for pageLogger or the fileserver- respectively the download-code."; // TRANSLATE
$l_weTag['path']['description'] = "The we:path tag represents the path of the current document. If there is an index file in one of the subdirectories, a link is set on the respective directory. The used index files (separated by commas) can be specified in the attribute \"index\". If nothing is specified there, \"default.html\", \"index.htm\", \"index.php\", \"default. htm\", \"default.html\" and \"default.php\" are used as default settings. In the attribute \"home\" you can specify what to put at the very beginning. If nothing is specified, \"home\" is displayed automatically. The attribute separator describes the delimiter between the directories. If the attribute is empty, \"/\" is used as delimiter. The attribute \"field\" defines what sort of field (files, directories) is displayed. If the field is empty or non-existent, the filename will be displayed. The attribute \"dirfield\" defines which field is used for display in directories. If the field is empty or non-existent, the entry of \"field\" or the filename is used."; // TRANSLATE
$l_weTag['paypal']['description'] = "The tag we:paypal implements an interface to the payment provider paypal. To ensure that this tag works properly, add additional parameters in the backend of the shop module."; // TRANSLATE
$l_weTag['position']['description'] = "The tag we:position is used to return the actual position of a listview, block, linklist, listdir. Is \"type= block or linklist\" it is necessary to specify the name (reference) of the related block/linklist. The attribute \"format\" determines the format of the result."; // TRANSLATE
$l_weTag['postlink']['description'] = "The we:postlink tag ensures that everything located between the start tag and the end tag will not be displayed for the last link in the link list."; // TRANSLATE
$l_weTag['postlink']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['prelink']['description'] = "The we:prelink tag ensures that everything located between the start tag and the end tag will not be displayed for the first link in the link list."; // TRANSLATE
$l_weTag['prelink']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['printVersion']['description'] = "The we:printVersion tag creates an HTML link tag that references to the same document, but with a different template. The attribute \"tid\" determines the ID of the template. The tag links any content between the start tag and the end tag."; // TRANSLATE
$l_weTag['printVersion']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['processDateSelect']['description'] = "The tag &lt;we:processDateSelect&gt; processes the 3 values from the select boxes of the tag we:dateSelect into a UNIX timestamp. The value will be saved in a global variable with the name which was entered in the attribute \"name&quuot;."; // TRANSLATE
$l_weTag['quicktime']['description'] = "The we:quicktime tag allows you to insert a Quicktime movie in the content of the document. Documents based on this template will display an edit button while in edit mode. Clicking on this button will launch a file manager, which allows you to select a Quicktime movie that you have already set up in webEdition. Currently there exists no xhtml-valid output working on both common browsers (IE, Mozilla). Therefore, xml is always set to \"false\""; // TRANSLATE
$l_weTag['registeredUser']['description'] = "This tag is used to print customer data stored in the customer modules."; // TRANSLATE
$l_weTag['registerSwitch']['description'] = "This tag generates a switch with which you can shift between the status of a registered and an unregistered user while in edit-mode. If you have used the &lt;we:ifRegisteredUser&gt; and &lt;we:ifNotRgisteredUser&gt; tags, this tag allows you to see the different views and and to keep control of the layout."; // TRANSLATE
$l_weTag['repeat']['description'] = "Content enclosed within this tag is repeated for every entry found by a &lt;we:listview&gt;. This tag is only used within a &lt;we:listview&gt; section."; // TRANSLATE
$l_weTag['repeat']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['repeatShopItem']['description'] = "This tag displays all articles in the shopping cart."; // TRANSLATE
$l_weTag['repeatShopItem']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['returnPage']['description'] = "This tag is used to display the referring url of the source page, if the value of the attribute \"return\" was \"true\" when used in the tags: &lt;we:a edit=\"document\"&gt; or &lt;we:a edit=\"object\"&gt;"; // TRANSLATE
$l_weTag['saferpay']['description'] = "we:saferpay implements an interface to the payment provider saferpay. To ensure that this tag works properly, add additional information in the backend of the shop module."; // TRANSLATE
$l_weTag['saveRegisteredUser']['description'] = "This tag saves all customer data entered by session fields."; // TRANSLATE
$l_weTag['search']['description'] = "The we:search tag creates an input box or a text box that is intended to be used for search queries. The search field has the internal name \"we_search\". When the search form is submitted, the PHP variable \"we_search\" on the receiving web page will be filled with the content from the input box."; // TRANSLATE
$l_weTag['select']['description'] = "The we:select tag creates a select box for entry in edit mode. If \"1\" has been specified as size (size= \"1\" ), the select box appears as a pop-up menu. It behaves exactly as an HTML select tag does. Between the start tag and the end-tag, entries are determined by normal HTML option tags."; // TRANSLATE
$l_weTag['select']['defaultvalue'] = "&lt;option&gt;#1&lt;/option&gt;
&lt;option&gt;#2&lt;/option&gt;
&lt;option&gt;#3&lt;/option&gt;";
$l_weTag['sendMail']['description'] = "This tag sends a webEdition page as an E-mail to the addresses which are defined in the attribute \"recipient\"."; // CHECK
// changed from: "This tag sends a webEdition page as an E-mail to the addresses which are defined in the attribute \"recipient\"."
// changed to  : "This tag sends a webEdition page as an email to the addresses which are defined in the attribute \"recipient\"."

$l_weTag['sessionField']['description'] = "The we:sessionField tag creates an HTML input, select or text area tag. It is used for any input in session fields (e. g. customer data, etc.)."; // TRANSLATE
$l_weTag['sessionLogout']['description'] = "The we:sessionLogout tag creates an HTML link tag referring to an internal webEdition document with the ID mentioned in the webEdition Tag Wizard. If this webEdition document has a we:sessionStart tag and holds the attribute \"dynamic\", the active session will be cleared and closed. No data will be saved."; // TRANSLATE
$l_weTag['sessionLogout']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['sessionStart']['description'] = "This tag is used to start a session or to continue an existing one. This tag is required in templates that generate the following pages: Pages which are protected in some form by the Customer Mangement Module, Shop pages and pages which support front end input.&lt;br /&gt;This tag MUST be the first tag on the first line of the template!";
$l_weTag['setVar']['description'] = "This tag is used to set the values of various types of varibles.<br/><strong>Attention:</strong> Without the attribute <strong>striptags=\"true\"</strong>, HTML- and PHP-Code is not filtered, this is a potenzial security risk!</strong>"; // TRANSLATE
$l_weTag['shipping']['description'] = "In regard to the purchase we:shipping is used to determine shipping costs. These costs are based on the value of the shopping cart, the land of origin of the registered user and the shipping cost rules editable in the Shop Module. The parameter \"sum\" contains the name of a sum calculated with we:sum. The parameter \"type\" is used to determine either the net, gros as well as the amount of the VAT contained in the shipping costs."; // TRANSLATE
$l_weTag['shopField']['description'] = "This tag saves various input fields directly from an article or in the shopping cart (order). The administrator can define some values from which the customer can choose or enter an own value. It is therefore possible to map many article variants in a simple way."; // TRANSLATE
$l_weTag['ifShopFieldEmpty']['description'] = "Content enclosed by this tag is only displayed if the shopField named in attribute \"name\" is empty.";// TRANSLATE
$l_weTag['ifShopFieldNotEmpty']['description'] = "Content enclosed by this tag is only displayed if the shopField named in attribute \"name\" is not empty.";// TRANSLATE
$l_weTag['ifShopField']['description'] = "Everything between the start and end tags of this tag is displayed only if the value of the attribut \"match\" is identical with the value of the shopField ";// TRANSLATE
$l_weTag['ifNotShopField']['description'] = "Everything between the start and end tags of this tag is displayed only if the value of the attribut \"match\" is not identical with the value of the shopField ";// TRANSLATE

$l_weTag['shopVat']['description'] = "This tag is used to determine the VAT for an article. To adminstrate different VAT rates use the Shop Module. A given Id directly prints the VAT-Rate for this article."; // TRANSLATE
$l_weTag['showShopItemNumber']['description'] = "The we:showShopItemNumber tag shows the amount of specified items in the basket."; // TRANSLATE
$l_weTag['sidebar']['description'] = ""; // TRANSLATE
$l_weTag['sidebar']['defaultvalue'] = "Open sidebar"; // TRANSLATE
$l_weTag['subscribe']['description'] = "This tag is used to add a single line input field to a webEdition document so that a user wanting to subscribe to a newsletter can enter his or her E-mail address."; // CHECK
// changed from: "This tag is used to add a single line input field to a webEdition document so that a user wanting to subscribe to a newsletter can enter his or her E-mail address."
// changed to  : "This tag is used to add a single line input field to a webEdition document so that a user wanting to subscribe to a newsletter can enter his or her email address."

$l_weTag['sum']['description'] = "The we:sum tag sums up all figures in a list."; // TRANSLATE
$l_weTag['target']['description'] = "This tag is used to generate the link target from within &lt;we:linklist&gt;."; // TRANSLATE
$l_weTag['textarea']['description'] = "The we:textarea tag creates a multi-line input box."; // TRANSLATE
$l_weTag['title']['description'] = "The we:title tag creates a normal title tag. If the title field in the Properties view is empty, everything located between the start tag and the end tag will be used as the default title. Otherwise the title will be entered by the Properties view."; // TRANSLATE
$l_weTag['tr']['description'] = "The &lt;we:tr&gt; Tag corresponds to the HTML-tag &lt;tr&gt; and is used to define a table row."; // TRANSLATE
$l_weTag['tr']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['unsubscribe']['description'] = "This tag is used to generate a single input field in a webEdition document so that a user can enter his or her E-mail address to unsubscribe from a news-letter."; // CHECK
// changed from: "This tag is used to generate a single input field in a webEdition document so that a user can enter his or her E-mail address to unsubscribe from a news-letter."
// changed to  : "This tag is used to generate a single input field in a webEdition document so that a user can enter his or her email address to unsubscribe from a news-letter."

$l_weTag['url']['description'] = "The we:url tag creates an internal webEdition URL that references to the document that has the ID listed below."; // TRANSLATE
$l_weTag['userInput']['description'] = "The we:userInput tag creates input fields to use with we:form type=\"document\" or type=\"object\" in order to create documents or objects."; // TRANSLATE
$l_weTag['useShopVariant']['description'] = "The we:shopVariant tag uses the data of a article variant by the submitted name of the variant. Is there no variant with the given name the default article will be displayed."; // TRANSLATE
$l_weTag['var']['description'] = "The we:var tag displays the content of a global PHP variable respective to the content of a document field with the name listed below."; // TRANSLATE
$l_weTag['voting']['description'] = "The we:voting tag is used to display Votings."; // TRANSLATE
$l_weTag['voting']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['votingField']['description'] = "The we:votingField-tag is required to display the content of a voting. The attribute \"name\" defines what to show. The attribute \"type\", how to display it. Valid name-type combinations are: question - text; result - count, percent, total; id - answer, select, radio, voting; answer - text,radio,checkbox (select multiple) select, textinput and textarea (free text answer field), image (all we:img attributes such as thumbnail are supported), media (delivers the path utilizing to and nameto); ";// TRANSLATE
$l_weTag['votingList']['description'] = "This tag automatically generates the voting lists."; // TRANSLATE
$l_weTag['votingList']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['votingSelect']['description'] = "Use this tag to generate a dropdown-menu; (&lt;select&gt;) to select voting."; // TRANSLATE
$l_weTag['write']['description'] = "This tag stores a document/object generated by &lt;we:form type=\"document/object&gt;"; // TRANSLATE
$l_weTag['writeShopData']['description'] = "The we:writeShopData tag writes all current shopping cart data into the database."; // TRANSLATE
$l_weTag['writeVoting']['description'] = "This tag writes a voting into the database. If the attribute \"id\" is defined, only the voting with the respective id will be saved.";
$l_weTag['xmlfeed']['description'] = "The tag loads xml content from the given url"; // TRANSLATE
$l_weTag['xmlnode']['description'] = "The tag prints a xml element from the given feed or url."; // TRANSLATE
$l_weTag['xmlnode']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifbannerexists']['description'] = "Executes the enclosed code only, if the banner module is not deaktivated (settings dialog)."; // TRANSLATE
$l_weTag['ifbannerexists']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifcustomerexists']['description'] = "Executes the enclosed code only, if the customer module is not deaktivated (settings dialog)."; // TRANSLATE
$l_weTag['ifcustomerexists']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifnewsletterexists']['description'] = "Executes the enclosed code only, if the newsletter module is not deaktivated (settings dialog)."; // TRANSLATE
$l_weTag['ifnewsletterexists']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifobjektexists']['description'] = "Executes the enclosed code only, if the object module is not deaktivated (settings dialog)."; // TRANSLATE
$l_weTag['ifobjektexists']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifshopexists']['description'] = "Executes the enclosed code only, if the shop module is not deaktivated (settings dialog)."; // TRANSLATE
$l_weTag['ifshopexists']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifvotingexists']['description'] = "Executes the enclosed code only, if the voting module is not deaktivated (settings dialog)."; // TRANSLATE
$l_weTag['ifvotingexists']['defaultvalue'] = ""; // TRANSLATE

$l_weTag['ifNotHasChildren']['description'] = "Within the &lt;we:repeat&gt; tag &lt;we:ifNotHasChildren&gt; is used to query if a category(folder) has child categories."; // TRANSLATE
$l_weTag['ifNotHasChildren']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotHasCurrentEntry']['description'] = "we:ifNotHasCurrentEntry can be used within we:navigationEntry type=\"folder\" to show some content, only if the navigation folder does not contain the activ entry"; // TRANSLATE
$l_weTag['ifNotHasCurrentEntry']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotHasEntries']['description'] = "we:ifNotHasEntries can be used within we:navigationEntry to show content only, if the navigation entry does not contain entries."; // TRANSLATE
$l_weTag['ifNotHasEntries']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotHasShopVariants']['description'] = "The tag we:ifHasShopVariants can display content depending on the existance of variants in an object or document. With this, it can be controlled whether a &lt;we:listview type=\"shopVariant\"&gt; should be displayed at all or some alternative."; // TRANSLATE
$l_weTag['ifNotHasShopVariants']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifPageLanguage']['description'] = "The tag we:ifPageLanguage tests on the language setting in the properties tab of the document, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages"; // TRANSLATE
$l_weTag['ifPageLanguage']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotPageLanguage']['description'] = "The tag we:ifNotPageLanguage tests on the language setting in the properties tab of the document, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages"; // TRANSLATE
$l_weTag['ifNotPageLanguage']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifObjectLanguage']['description'] = "The tag we:ifObjectLanguage tests on the language setting in the properties tab of the object, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages"; // TRANSLATE
$l_weTag['ifObjectLanguage']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotObjectLanguage']['description'] = "The tag we:ifNotObjectLanguage tests on the language setting in the properties tab of the object, several values can be separated by comma (OR relation). The possible values are taken from the general properties dialog, tab languages"; // TRANSLATE
$l_weTag['ifNotObjectLanguage']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifSendMail']['description'] = "Checks if a page is currently sent by we:sendMail and allows to exclude or include contents to the sent page"; // TRANSLATE
$l_weTag['ifSendMail']['defaultvalue'] = ""; // TRANSLATE
$l_weTag['ifNotSendMail']['description'] = "Checks if a page is currently sent by we:sendMail and allows to exclude or include contents to the sent page"; // TRANSLATE
$l_weTag['ifNotSendMail']['defaultvalue'] = ""; // TRANSLATE

$l_weTag['ifNotVoteActive']['description'] = "Any content between the start- and endtag is only displayed, if the voting has expired.";// TRANSLATE
$l_weTag['ifNotVoteActive']['defaultvalue'] = "";
$l_weTag['ifNotVoteIsRequired']['description'] = "Any content between the start- and endtag is only displayed, if the voting ist not required to be filled out.";// TRANSLATE
$l_weTag['ifNotVoteIsRequired']['defaultvalue'] = "";
$l_weTag['ifVoteIsRequired']['description'] = "Any content between the start- and endtag is only displayed, if the voting is a required field.";// TRANSLATE
$l_weTag['ifVoteIsRequired']['defaultvalue'] = "";
$l_weTag['pageLanguage']['description'] = "Shows the language of the document";// TRANSLATE
$l_weTag['pageLanguage']['defaultvalue'] = "";
$l_weTag['objectLanguage']['description'] = "Shows the language of the object";// TRANSLATE
$l_weTag['objectLanguage']['defaultvalue'] = "";
$l_weTag['order']['description'] = "Using this tag, one can display an order on a webEdition page. Similar to the Listview or the <we:object> tag, the fields are displayed with the <we:field> tag.";// TRANSLATE
$l_weTag['order']['defaultvalue'] = "";
$l_weTag['orderitem']['description'] = "Using this tag, one can display a single item on an order on a webEdition page. Similar to the Listview or the <we:object> tag, the fields are displayed with the <we:field> tag.";// TRANSLATE
$l_weTag['orderitem']['defaultvalue'] = "";


$l_weTag['ifVotingField']['description'] = "Checks if a votingField has a value corresponding to the attribute match, the attribute combinations of name and type are the same as in the we:votingField tag";// TRANSLATE
$l_weTag['ifNotVotingField']['description'] = "Checks if a votingField has not a value corresponding to the attribute match, the attribute combinations of name and type are the same as in the we:votingField tag";// TRANSLATE
$l_weTag['ifVotingFieldEmpty']['description'] = "Checks if a votingField is empty, the attribute combinations of name and type are the same as in the we:votingField tag";// TRANSLATE
$l_weTag['ifVotingFieldNotEmpty']['description'] = "Checks if a votingField is not empty, the attribute combinations of name and type are the same as in the we:votingField tag";// TRANSLATE
$l_weTag['ifVotingIsRequired']['description'] = "Prints the enclosed content only, if the voting field is a required field";// TRANSLATE
$l_weTag['ifNotVotingIsRequired']['description'] = "Prints the enclosed content only, if the voting field is a required field";// TRANSLATE
$l_weTag['votingSession']['description'] = "Generates an unique identifier which is stored in the voting log and allows to identify the answers to different questions which belong to a singele voting session";// TRANSLATE

?>
