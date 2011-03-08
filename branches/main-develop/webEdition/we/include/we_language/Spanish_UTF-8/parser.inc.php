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
		'wrong_type' => "El valor de \"tipo\" es no válido!",
		'error_in_template' => "Error de plantilla!",
		'start_endtag_missing' => "A uno o más rótulos <código>&lt;we:%s&gt;</código> les faltan un rótulo inicio o un rótulo final!",
		'tag_not_known' => "El rótulo <código>'&lt;we:%s&gt;'</código> es desconocido!",
		'else_start' => "Hay un rótulo <código>&lt;we:else/&gt;</código> sin un rótulo inicio <código>&lt;we:if...&gt;</código> !",
		'else_end' => "Hay un rótulo <código>&lt;we:else/&gt;</código> sin un rótulo final <código>&lt;we:if...&gt;</código>!",
		'attrib_missing' => "El atributo '%s' del rótulo <código>&lt;we:%s&gt;</código> no se encuentra o está vacío!",
		'attrib_missing2' => "El atributo '%s' del rótulo <código>&lt;we:%s&gt;</código> no se encuentra!",
		'module_missing' => "The module '%s' ist deaktivated, cannot execute the tag <code>&lt;we:%s&gt;</code>!", //TRANSLATE
		'missing_open_tag' => "<code>&lt;%s&gt;</code>: The opening tag is missing.", // TRANSLATE
		'missing_close_tag' => "<code>&lt;%s&gt;</code>: The closing tag is missing.", // TRANSLATE
		'name_empty' => "El nombre del rótulo <código>&lt;we:%s&gt;</código> está vacío!",
		'invalid_chars' => "El nombre del rótulo <código>&lt;we:%s&gt;</código> contiene carácteres no válidos. Solamente son permitidos los carácteres alfabéticos, números, '-' y '_'!",
		'name_to_long' => "El nombre del rótulo <código>&lt;we:%s&gt;</código> es demasiado largo! Solamente debe contener un máximo de 255 carácteres!",
		'name_with_space' => "¡El nombre del rótulo <code>&lt;we:%s&gt;</code> no debe contener espacios (tales como la tecla semejante a la tecla del ENTER, el tabulador, alimentación de linea, etc.)!",
		'client_version' => "La sintaxis del atributo 'versión' del rótulo <código>&lt;we:ifClient&gt;</código> es incorrecta!",
		'html_tags' => "La plantilla debe, o incluir los rótulos HTML <código>&lt;html&gt; &lt;head&gt; &lt;body&gt;</código> o ninguno de estos rótulos. De lo contrario, el programa analizador sintáctico no podrá trabajar correctamente!",
		'field_not_in_lv' => "La etiqueta <code>&lt;we:field&gt;</code>-  tiene que estar encerrada por un <code&gt;</code>&lt;we:listview&gt;</code> o <code&gt;</code>&lt;we:object&gt;</code> comienzo- y fin de etiqueta!",
		'setVar_lv_not_in_lv' => "La etiqueta <code>&lt;we:setVar from=\"listview\" ... &gt;</code>- tiene que estar encerrada por un <code>&lt;we:listview&gt;</code> comienzo- y fin de etiqueta!",
		'checkForm_jsIncludePath_not_found' => "El atributo jsIncludePath de la etiqueta <code>&lt;we:checkForm&gt;</code> fue dado como un número (ID). Sin embargo no hay documento con tal id!",
		'checkForm_password' => "El atributo contraseña de <code>&lt;we:checkForm&gt;</code> tiene que ser 3 valores separados por comas!",
		'missing_createShop' => "The tag <code>&lt;we:%s&gt;</code> can only be used after<code>&lt;we:createShop&gt;</code>.", // TRANSLATE
		'multi_object_name_missing_error' => "Error: The object field &quot;%s, specified in the attribute &quot;name&quot;, does not exist!", // TRANSLATE
		'template_recursion_error' => "Error: Too much recursion!", // TRANSLATE
);