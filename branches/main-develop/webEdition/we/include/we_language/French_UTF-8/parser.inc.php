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
		'wrong_type' => "La valeur du \"type\" n'est pas licite!",
		'error_in_template' => "Erreur dans le modèle",
		'start_endtag_missing' => "Un ou plusiers <code>&lt;we:%s&gt;</code> Tags manquent d'un repère d'ouverture ou repère de fermeture!",
		'tag_not_known' => "Le Tag <code>'&lt;we:%s&gt;'</code> est inconnu!",
		'else_start' => "Il y a un <code>&lt;we:else/&gt;</code> Tag  sans  <code>&lt;we:if...&gt;</code> un repère d'ouverture!",
		'else_end' => "Il y a un <code>&lt;we:else/&gt;</code> Tag sans <code>&lt;/we:if...&gt;</code> un repère de fermeture!",
		'attrib_missing' => "L'attribut '%s' dans le Tag <code>&lt;we:%s&gt;</code> ne doit pas manquer ou être vide!",
		'attrib_missing2' => "L'attribut '%s' dans le Tag <code>&lt;we:%s&gt;</code> ne doit pas manquer!",
		'module_missing' => "The module '%s' ist deaktivated, cannot execute the tag <code>&lt;we:%s&gt;</code>!", //TRANSLATE
		'missing_open_tag' => "<code>&lt;%s&gt;</code>: The opening tag is missing.", // TRANSLATE
		'missing_close_tag' => "<code>&lt;%s&gt;</code>: The closing tag is missing.", // TRANSLATE
		'name_empty' => "Le nom du Tag <code>&lt;we:%s&gt;</code> est vide!",
		'invalid_chars' => "Le nom du Tag <code>&lt;we:%s&gt;</code> contient des signe illicite. Permit sont les lettres, chiffres, '-' et '_'!",
		'name_to_long' => "Le nom du Tag we:%s est trop long! Langeur maximale est 255 chiffres!",
		'name_with_space' => "Le nom du Tag we:%s ne doit pas contenir des éspace!",
		'client_version' => "La syntax de l'attribut 'version' dans le Tag <code>&lt;we:ifClient&gt;</code> n'est pas correcte!",
		'html_tags' => "Le modèle doit ou contenir les Tags-HTML <code>&lt;html&gt; &lt;head&gt; &lt;body&gt;</code> ou aucun de ces Tags, pour que l'nalyseur syntaxique travaille correctement!",
		'field_not_in_lv' => "Le Tag <code>&lt;we:field&gt;</code> doit être entre des rèperes d'ouverture et de fermeture de <code>&lt;we:listview&gt;</code> ou <code>&lt;we:object&gt;</code>!",
		'setVar_lv_not_in_lv' => "Le Tag <code>&lt;we:setVar from=\"listview\" ... &gt;</code> doit être entre des rèperes d'ouverture et de fermeture de <code>&lt;we:listview&gt;</code>!",
		'checkForm_jsIncludePath_not_found' => "L'attribut jsIncludePath du Tag <code>&lt;we:checkForm&gt;</code> a été saisi comme nombre (ID). Un document avec cette ID n'a pas été trouvé!",
		'checkForm_password' => "L'attribut password du Tag <code>&lt;we:checkForm&gt;</code> attend 3 valeurs séparées par des virgules!",
		'missing_createShop' => "The tag <code>&lt;we:%s&gt;</code> can only be used after<code>&lt;we:createShop&gt;</code>.", // TRANSLATE
		'multi_object_name_missing_error' => "Error: The object field &quot;%s, specified in the attribute &quot;name&quot;, does not exist!", // TRANSLATE
		'template_recursion_error' => "Error: Too much recursion!", // TRANSLATE
);