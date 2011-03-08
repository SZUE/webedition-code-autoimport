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
		'delete' => "Delete",
		'wrong_type' => "Value of \"type\" is invalid!",
		'error_in_template' => "Template error!",
		'start_endtag_missing' => "One or more <code>&lt;we:%s&gt;</code> tags are missing a  start or end tag!",
		'tag_not_known' => "The tag <code>'&lt;we:%s&gt;'</code> is unknown!",
		'else_start' => "There is a <code>&lt;we:else/&gt;</code> tag without <code>&lt;we:if...&gt;</code> a start tag!",
		'else_end' => "There is a <code>&lt;we:else/&gt;</code> tag without <code>&lt;/we:if...&gt;</code> an end tag!",
		'attrib_missing' => "The attribute '%s' of the tag <code>&lt;we:%s&gt;</code> is missing or empty!",
		'attrib_missing2' => "The attribute '%s' of the tag <code>&lt;we:%s&gt;</code> is missing!",
		'module_missing' => "The module '%s' ist deaktivated, cannot execute the tag <code>&lt;we:%s&gt;</code>!", //TRANSLATE
		'missing_open_tag' => "<code>&lt;%s&gt;</code>: The opening tag is missing.",
		'missing_close_tag' => "<code>&lt;%s&gt;</code>: The closing tag is missing.",
		'name_empty' => "The name of the tag <code>&lt;we:%s&gt;</code> is empty!",
		'invalid_chars' => "The name of the tag <code>&lt;we:%s&gt;</code> contains invalid characters. Only alphabetic characters, numbers, '-' and '_' are allowed!",
		'name_to_long' => "The name of the tag <code>&lt;we:%s&gt;</code> is too long! It may only contain a maximum of 255 characters!",
		'name_with_space' => "The name of the tag <code>&lt;we:%s&gt;</code> may not contain any blank characters!",
		'client_version' => "The syntax of the attribute 'version' of the tag <code>&lt;we:ifClient&gt;</code> is wrong!",
		'html_tags' => "The template must either include the HTML tags <code>&lt;html&gt; &lt;head&gt; &lt;body&gt;</code> or none of these tags. Otherwise, the parser cannot work correctly!",
		'field_not_in_lv' => "The  <code>&lt;we:field&gt;</code>-tag has to be enclosed by a <code>&gt;</code>&lt;we:listview&gt;</code> or <code&gt;</code>&lt;we:object&gt;</code> start- and endtag!",
		'setVar_lv_not_in_lv' => "The <code>&lt;we:setVar from=\"listview\" ... &gt;</code>-tag has to be enclosed by a <code>&lt;we:listview&gt;</code> start- and endtag!",
		'checkForm_jsIncludePath_not_found' => "The attribute jsIncludePath of the tag <code>&lt;we:checkForm&gt;</code> was given as number(ID). But there is no document with such id!",
		'checkForm_password' => "The attribute password of the <code>&lt;we:checkForm&gt;</code> must be 3 values separated by commas!",
		'missing_createShop' => "The tag <code>&lt;we:%s&gt;</code> can only be used after<code>&lt;we:createShop&gt;</code>.",
		'multi_object_name_missing_error' => "Error: The object field &quot;%s, specified in the attribute &quot;name&quot;, does not exist!",
		'template_recursion_error' => "Error: Too much recursion!",
);