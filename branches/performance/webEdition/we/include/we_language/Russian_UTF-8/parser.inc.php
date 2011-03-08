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
		'wrong_type' => "Значение &quot;type&quot; недопустимо!",
		'error_in_template' => "Ошибка шаблона",
		'start_endtag_missing' => "Для одного из тегов <code>&lt;we:%s&gt;</code> не задан начальный или конечный тег!",
		'tag_not_known' => "Тег <code>'&lt;we:%s&gt;'</code> неизвестен!",
		'else_start' => "Для тега <code>&lt;we:else/&gt;</code> не задан начальный тег <code>&lt;we:if...&gt;</code>!",
		'else_end' => "Для тега <code>&lt;we:else/&gt;</code> не задан конечный тег <code>&lt;we:if...&gt;</code>!",
		'attrib_missing' => "Атрибут '%s' тега <code>&lt;we:%s&gt;</code> не должен быть незаполненным!",
		'attrib_missing2' => "Атрибут '%s' тега <code>&lt;we:%s&gt;</code> не должен отсутствовать!",
		'module_missing' => "The module '%s' ist deaktivated, cannot execute the tag <code>&lt;we:%s&gt;</code>!", //TRANSLATE
		'missing_open_tag' => "<code>&lt;%s&gt;</code>: The opening tag is missing.", // TRANSLATE
		'missing_close_tag' => "<code>&lt;%s&gt;</code>: The closing tag is missing.", // TRANSLATE
		'name_empty' => "Имя тега <code>&lt;we:%s&gt;</code> не заполнено!",
		'invalid_chars' => "Имя тега <code>&lt;we:%s&gt;</code> содержит недопустимые символы. Допустимыми символами являются буквы латинского алфавита, цифры, символы: '-' и '_'!",
		'name_to_long' => "Имя тега <code>&lt;we:%s&gt;</code> слишком длинное! Имя не должно превышать 255 символов!",
		'name_with_space' => "Имя тега <code>&lt;we:%s&gt;</code> не должно включать пробелы!",
		'client_version' => "Синтаксис атрибута 'version' тега  <code>&lt;we:ifClient&gt;</code> неверен!",
		'html_tags' => "Шаблон должен содержать либо все нижеследующие теги HTML <code>&lt;html&gt; &lt;head&gt; &lt;body&gt;</code> либо ни одного из них. В противном случае не обеспечивается корректная работа парсера!",
		'field_not_in_lv' => "Тег <code&gt;</code>&lt;we:field&gt;</code> должен находиться между начальным и конечным тегом  <code>&lt;we:listview&gt;</code> или <code>&lt;we:object&gt;</code>!",
		'setVar_lv_not_in_lv' => "Тег <code>&lt;we:setVar from=\"listview\" ... &gt;</code> вкладывается с помощью начального и конечного тегов <code>&lt;we:listview&gt;</code>!",
		'checkForm_jsIncludePath_not_found' => "Атрибут jsIncludePath тега  <code>&lt;we:checkForm&gt;</code> задан в виде порядкового номера (ID). Документа с таким порядковым номером не существует!",
		'checkForm_password' => "Пароль атрибута тега <code>&lt;we:checkForm&gt;</code> должен состоять из 3 знаков, разделенных запятыми!",
		'missing_createShop' => "The tag <code>&lt;we:%s&gt;</code> can only be used after<code>&lt;we:createShop&gt;</code>.", // TRANSLATE
		'multi_object_name_missing_error' => "Error: The object field &quot;%s, specified in the attribute &quot;name&quot;, does not exist!", // TRANSLATE
		'template_recursion_error' => "Error: Too much recursion!", // TRANSLATE
);