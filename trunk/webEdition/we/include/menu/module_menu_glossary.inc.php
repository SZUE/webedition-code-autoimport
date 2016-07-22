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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
//
// ---> Menu File / Glossary
//
$we_menu_glossary = array(
	'new' => array(
		'text' => g_l('modules_glossary', '[menu_new]'),
	),
	'glossary' => array(
		'text' => g_l('modules_glossary', '[glossary]'),
	),
	array(
		'text' => g_l('modules_glossary', '[menu_save]'),
		'parent' => 'glossary',
		'cmd' => 'save_glossary',
		'perm' => 'EDIT_GLOSSARY || NEW_GLOSSARY || ADMINISTRATOR',
	),
	array(
		'text' => g_l('modules_glossary', '[menu_delete]'),
		'parent' => 'glossary',
		'cmd' => 'delete_glossary',
		'perm' => 'DELETE_GLOSSARY || ADMINISTRATOR',
	),
	array('parent' => 'glossary'
	), // separator
	array(
		'text' => g_l('modules_glossary', '[menu_exit]'),
		'parent' => 'glossary',
		'cmd' => 'exit_glossary',
	),
//
// ---> Menu Options
//
	'options' => array(
		'text' => g_l('modules_glossary', '[menu_options]'),
		'perm' => 'ADMINISTRATOR',
	),
	array(
		'text' => g_l('modules_glossary', '[menu_settings]'),
		'parent' => 'options',
		'cmd' => 'glossary_settings',
		'perm' => 'ADMINISTRATOR',
	),
//
// ---> Menu Help
//
	'help' => array(
		'text' => g_l('modules_glossary', '[menu_help]'),
	),
	array(
		'text' => g_l('modules_glossary', '[menu_help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',
	),
	array(
		'text' => g_l('modules_glossary', '[menu_info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_modules',
	)
);

$langs = getWeFrontendLanguagesForBackend();
foreach($langs as $key => $language){
	$we_menu_glossary[$language] = array(
		'text' => $language,
		'parent' => 'new',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	);

	$we_menu_glossary[] = array(
		'text' => g_l('modules_glossary', '[abbreviation]'),
		'parent' => $language,
		'cmd' => 'GlossaryXYZnew_glossary_abbreviationXYZ$key',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	);

	$we_menu_glossary[] = array(
		'text' => g_l('modules_glossary', '[acronym]'),
		'parent' => $language,
		'cmd' => 'GlossaryXYZnew_glossary_acronymXYZ$key',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	);

	$we_menu_glossary[] = array(
		'text' => g_l('modules_glossary', '[foreignword]'),
		'parent' => $language,
		'cmd' => 'GlossaryXYZnew_glossary_foreignwordXYZ$key',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	);

	$we_menu_glossary[] = array(
		'text' => g_l('modules_glossary', '[link]'),
		'parent' => $language,
		'cmd' => 'GlossaryXYZnew_glossary_linkXYZ$key',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	);

	$we_menu_glossary[] = array(
		'text' => g_l('modules_glossary', '[textreplacement]'),
		'parent' => $language,
		'cmd' => 'GlossaryXYZnew_glossary_textreplacementXYZ$key',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	);
}

return $we_menu_glossary;