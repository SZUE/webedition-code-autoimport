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
		'wrong_type' => "Wartość \"type\" jest niedozwolona!",
		'error_in_template' => "Błąd w szablonie",
		'start_endtag_missing' => "W <code>&lt;we:%s&gt;</code> brakuje tagu początkowego albo końcowego!",
		'tag_not_known' => "Tag <code>'&lt;we:%s&gt;'</code> nie jest znany!",
		'else_start' => "W <code>&lt;we:else/&gt;</code> brakuje wymaganego tagu startowego <code>&lt;we:if...&gt;</code>!",
		'else_end' => "W <code>&lt;we:else/&gt;</code> brakuje wymaganego tagu końcowego <code>&lt;/we:if...&gt;</code>!",
		'attrib_missing' => "Atrybut '%s' w tagu <code>&lt;we:%s&gt;</code> nie może być pusty!",
		'attrib_missing2' => "Atrybut '%s' w tagu <code>&lt;we:%s&gt;</code> nie może być pusty!",
		'module_missing' => "The module '%s' ist deaktivated, cannot execute the tag <code>&lt;we:%s&gt;</code>!", //TRANSLATE
		'missing_open_tag' => "<code>&lt;%s&gt;</code>: The opening tag is missing.", // TRANSLATE
		'missing_close_tag' => "<code>&lt;%s&gt;</code>: The closing tag is missing.", // TRANSLATE
		'name_empty' => "Nazwa tagu  <code>&lt;we:%s&gt;</code> jest pusta!",
		'invalid_chars' => "Nazwa tagu<code>&lt;we:%s&gt;</code> zawiera niedozwolone znaki. Dozwolone są jedynie litery, cyfry, '-' i '_'!",
		'name_to_long' => "Nazwa tagu we:%s jest za długa! Może mieć maksymalnie 255 znaków!",
		'name_with_space' => "Nazwa tagu we:%s nie może być pusta!",
		'client_version' => "Składnia atrybutu 'version' w tagu <code>&lt;we:ifClient&gt;</code> jest błędna!",
		'html_tags' => "Szablon musi zawierać tagi HTML <code>&lt;html&gt; &lt;head&gt; &lt;body&gt;</code> albo żadnych, tak aby fraza była poprawna!",
		'field_not_in_lv' => "Tag <code>&lt;we:field&gt;</code> musi znajdować się w obrębie <code>&lt;we:listview&gt;</code> lub <code>&lt;we:object&gt;</code> tagu startowego i końcowego!",
		'setVar_lv_not_in_lv' => "Tag <code>&lt;we:setVar from=\"listview\" ... &gt;</code> musi znajdować się w obrębie <code>&lt;we:listview&gt;</code> tagu startowego i końcowego!",
		'checkForm_jsIncludePath_not_found' => "Atrybut jsIncludePath tagu <code>&lt;we:checkForm&gt;</code> został podany jako liczba (ID). Dokument z tym ID nie mógł jednak zostać znaleziony!",
		'checkForm_password' => "Atrubut password tagu <code>&lt;we:checkForm&gt;</code> wymaga 3 oddzielonych przecinkami wartości!",
		'missing_createShop' => "The tag <code>&lt;we:%s&gt;</code> can only be used after<code>&lt;we:createShop&gt;</code>.", // TRANSLATE
		'multi_object_name_missing_error' => "Error: The object field &quot;%s, specified in the attribute &quot;name&quot;, does not exist!", // TRANSLATE
		'template_recursion_error' => "Error: Too much recursion!", // TRANSLATE
);