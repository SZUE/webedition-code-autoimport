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
		'delete' => "Delete", // TRANSLATE
		'wrong_type' => "Waarde van \"type\" is ongeldig!",
		'error_in_template' => "sjabloon fout!",
		'start_endtag_missing' => "Eén of meerdere <code>&lt;we:%s&gt;</code> tags missen een begin of eind tag!",
		'tag_not_known' => "The tag <code>'&lt;we:%s&gt;'</code> is onbekend!",
		'else_start' => "Er is een <code>&lt;we:else/&gt;</code> tag zonder <code>&lt;we:if...&gt;</code> een begin tag!",
		'else_end' => "Er is een <code>&lt;we:else/&gt;</code> tag zonder <code>&lt;/we:if...&gt;</code> een eind tag!",
		'attrib_missing' => "Het attribuut '%s' van de tag <code>&lt;we:%s&gt;</code> ontbreekt of is leeg!",
		'attrib_missing2' => "Het attribuut '%s' van de tag <code>&lt;we:%s&gt;</code> ontbreekt!",
		'module_missing' => "The module '%s' ist deaktivated, cannot execute the tag <code>&lt;we:%s&gt;</code>!", //TRANSLATE
		'missing_open_tag' => "<code>&lt;%s&gt;</code>: De openings tag ontbreekt.",
		'missing_close_tag' => "<code>&lt;%s&gt;</code>: De sluit tag ontbreekt.",
		'name_empty' => "De naam van de tag <code>&lt;we:%s&gt;</code> is leeg!",
		'invalid_chars' => "De naam van de tag <code>&lt;we:%s&gt;</code> bevat ongeldige karakters. Alleen alphabetische karakters, nummers, '-' en '_' zijn toegestaan!",
		'name_to_long' => "De naam van de tag <code>&lt;we:%s&gt;</code> is te lang! Deze mag maximaal 255 karakters bevatten!",
		'name_with_space' => "De naam van de tag <code>&lt;we:%s&gt;</code> mag geen blanco karakters bevatten!",
		'client_version' => "De syntax van het attribuut 'version' van de tag <code>&lt;we:ifClient&gt;</code> is verkeerd!",
		'html_tags' => "Het sjabloon moet of HTML tags bevatten <code>&lt;html&gt; &lt;head&gt; &lt;body&gt;</code> of geen van deze tags. Anders functioneert de parser niet correct!",
		'field_not_in_lv' => "De  <code>&lt;we:field&gt;</code>-tag moet omsloten worden door <code>&gt;</code>&lt;we:listview&gt;</code> of <code&gt;</code>&lt;we:object&gt;</code> begin- en eindtag!",
		'setVar_lv_not_in_lv' => "De <code>&lt;we:setVar from=\"listview\" ... &gt;</code>-tag moet omsloten worden door een <code>&lt;we:listview&gt;</code> begin- en eindtag!",
		'checkForm_jsIncludePath_not_found' => "Aan het attribuut 'jsIncludePath' van de tag <code>&lt;we:checkForm&gt;</code> is een nummer toegekend(ID). Maar er is geen document met dit ID!",
		'checkForm_password' => "Het attribuut wachtwoord van de <code>&lt;we:checkForm&gt;</code> moet 3 komma gescheiden waardes bevatten!",
		'missing_createShop' => "De tag <code>&lt;we:%s&gt;</code> kan alleen gebruikt worden na<code>&lt;we:createShop&gt;</code>.",
		'multi_object_name_missing_error' => "Error: The object field &quot;%s, specified in the attribute &quot;name&quot;, does not exist!", // TRANSLATE
		'template_recursion_error' => "Error: Too much recursion!", // TRANSLATE
);