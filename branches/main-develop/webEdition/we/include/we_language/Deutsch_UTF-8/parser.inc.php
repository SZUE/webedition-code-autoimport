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
 * Language file: parser.inc.php
 * Provides language strings.
 * Language: Deutsch
 */

$l_parser = array(
		'delete' => "Löschen",
		'wrong_type' => "Der Wert von \"type\" ist nicht zulässig!",
		'error_in_template' => "Fehler in der Vorlage",
		'start_endtag_missing' => "Bei einem <code>&lt;we:%s&gt;</code> Tag fehlt entweder das Start- oder das Endtag!",
		'tag_not_known' => "Das Tag <code>'&lt;we:%s&gt;'</code> ist nicht bekannt!",
		'else_start' => "Bei einem <code>&lt;we:else/&gt;</code> Tag fehlt das dazugehörige <code>&lt;we:if...&gt;</code> Starttag!",
		'else_end' => "Bei einem <code>&lt;we:else/&gt;</code> Tag fehlt das dazugehörige <code>&lt;/we:if...&gt;</code> Endtag!",
		'attrib_missing' => "Das Attribut '%s' im Tag <code>&lt;we:%s&gt;</code> darf nicht fehlen oder leer sein!",
		'attrib_missing2' => "Das Attribut '%s' im Tag <code>&lt;we:%s&gt;</code> darf nicht fehlen!",
		'module_missing' => "Das Modul '%s' ist deaktiviert, Ausführung des Tags <code>&lt;we:%s&gt;</code> nicht möglich!",
		'missing_open_tag' => "<code>&lt;%s&gt;</code>: Das öffnende Tag fehlt.",
		'missing_close_tag' => "<code>&lt;%s&gt;</code>: Das schließende Tag fehlt.",
		'name_empty' => "Der Name des Tags <code>&lt;we:%s&gt;</code> ist leer!",
		'invalid_chars' => "Der Name des Tags <code>&lt;we:%s&gt;</code> enthält ungültige Zeichen. Erlaubt sind nur Buchstaben, Zahlen, '-' und '_'!",
		'name_to_long' => "Der Name des Tags we:%s ist zu lang! Er darf maximal nur 255 Zeichen lang sein!",
		'name_with_space' => "Der Name des Tags we:%s darf kein Leerzeichen enthalten!",
		'client_version' => "Die Syntax des Attributs 'version' im Tag <code>&lt;we:ifClient&gt;</code> ist falsch!",
		'html_tags' => "Die Vorlage muß entweder die HTML-Tags <code>&lt;html&gt; &lt;head&gt; &lt;body&gt;</code> enthalten oder keine dieser Tags, damit der Parser korrekt arbeitet!",
		'field_not_in_lv' => "Das Tag <code>&lt;we:field&gt;</code> muss sich innerhalb eines <code>&lt;we:listview&gt;</code> oder <code>&lt;we:object&gt;</code> Start- und Endtags befinden!",
		'setVar_lv_not_in_lv' => "Das Tag <code>&lt;we:setVar from=\"listview\" ... &gt;</code> muss sich innerhalb eines <code>&lt;we:listview&gt;</code> Start- und Endtags befinden!",
		'checkForm_jsIncludePath_not_found' => "Das Attribut jsIncludePath des Tags <code>&lt;we:checkForm&gt;</code> wurde als Zahl (ID) angegeben. Ein Dokument mit dieser ID konnte aber nicht gefunden werden!",
		'checkForm_password' => "Das Attribut password des Tags <code>&lt;we:checkForm&gt;</code> erwartet 3 kommaseparierte Werte!",
		'missing_createShop' => "Der Tag <code>&lt;we:%s&gt;</code> kann nur nach <code>&lt;we:createShop&gt;</code> eingesetzt werdern.",
		'multi_object_name_missing_error' => "Fehler: Das im we:listview Attribut &quot;name&quot; angegebene Objektfeld &quot;%s&quot; existiert nicht!",
		'template_recursion_error' => "Fehler: Zu viel Rekursion!",
);
