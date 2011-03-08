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
/**
 * Language file: enc_parser.inc.php
 * Provides language strings.
 * Language: English
 */
$l_parser = array(
		'delete' => "Poista",
		'wrong_type' => "Arvo \"tyyppi\" on virheellinen!",
		'error_in_template' => "Sivupohjavirhe!",
		'start_endtag_missing' => "Yksi tai useampi <code>&lt;we:%s&gt;</code> tagi puuttuu!",
		'tag_not_known' => "Tagi <code>'&lt;we:%s&gt;'</code> on tuntematon!",
		'else_start' => "Tagi <code>&lt;we:else/&gt;</code> on  <code>&lt;we:if...&gt;</code> ilman aloitustagia!",
		'else_end' => "Tagi <code>&lt;we:else/&gt;</code> <code>&lt;/we:if...&gt;</code> ilman lopetustagia!",
		'attrib_missing' => "Määritys '%s' tagista <code>&lt;we:%s&gt;</code> puuttuu tai on tyhjä!",
		'attrib_missing2' => "Määritys '%s' tagista <code>&lt;we:%s&gt;</code> puuttuu!",
		'module_missing' => "The module '%s' ist deaktivated, cannot execute the tag <code>&lt;we:%s&gt;</code>!", //TRANSLATE
		'missing_open_tag' => "<code>&lt;%s&gt;</code>: Aloitustagi puuttuu.",
		'missing_close_tag' => "<code>&lt;%s&gt;</code>: Lopetustagi puuttuu.",
		'name_empty' => "Tagin nimi <code>&lt;we:%s&gt;</code> on tyhjä!",
		'invalid_chars' => "The name of the tag <code>&lt;we:%s&gt;</code> virheellisiä kirjaimia. Vain alfa-numeeriset, kirjaimet/numero, '-' ja '_' ovat sallittuja!",
		'name_to_long' => "Tagin nimi <code>&lt;we:%s&gt;</code> liian pitkä! Tagi voi olla enintää 255 merkkiä pitkä!",
		'name_with_space' => "Tagin <code>&lt;we:%s&gt;</code> nimessä ei saa olla välilyöntejä!",
		'client_version' => "Määritys 'version' tagissa <code>&lt;we:ifClient&gt;</code> on virheellinen!",
		'html_tags' => "Sivupohja voi sisältää vain HTML -tageja <code>&lt;html&gt; &lt;head&gt; &lt;body&gt;</code> tai ei tageja ollenkaan. Muutoin, parseri ei toimi oikein!",
		'field_not_in_lv' => "Tagi  <code&gt;</code>&lt;we:field&gt;</code>-on suljettava <code&gt;</code>&lt;we:listview&gt;</code> tai <code&gt;</code>&lt;we:object&gt;</code> aloitus -ja lopetustagilla!",
		'setVar_lv_not_in_lv' => "Tagi <code>&lt;we:setVar from=\"listview\" ... &gt;</code> vaatii lopetustagin: <code>&lt;we:listview&gt;</code>!",
		'checkForm_jsIncludePath_not_found' => "Tagin <code>&lt;we:checkForm&gt;</code> määre jsIncludePath annettiin numerona (ID). Mutta järjestelmässä ei ole dokumenttia kyseisellä ID:llä!",
		'checkForm_password' => "Tagin <code>&lt;we:checkForm&gt;</code> määre password on oltava kolmeosainen, eroteltuna pilkuilla!",
		'missing_createShop' => "Tagia <code>&lt;we:%s&gt;</code> voidaan käyttää vain tagin <code>&lt;we:createShop&gt; jälkeen</code>.",
		'multi_object_name_missing_error' => "Error: The object field &quot;%s, specified in the attribute &quot;name&quot;, does not exist!",
		'multi_object_name_missing_error' => "Virhe: Objektin kenttä &quot;%s, joka on määreessä &quot;name&quot;, ei ole olemassa!",
		'template_recursion_error' => "Virhe: Liian paljon rekursiota!",
);