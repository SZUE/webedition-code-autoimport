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
$we_menu_glossary = [
	'new' => [
		'text' => g_l('modules_glossary', '[menu_new]'),
		'icon' => 'fa fa-plus-circle',
	],
	'glossary' => [
		'text' => g_l('modules_glossary', '[glossary]'),
		'icon' => 'fa fa-commenting'
	],
	['text' => g_l('modules_glossary', '[menu_save]'),
		'parent' => 'glossary',
		'cmd' => 'save_glossary',
		'perm' => 'EDIT_GLOSSARY || NEW_GLOSSARY || ADMINISTRATOR',
	],
	['text' => g_l('modules_glossary', '[menu_delete]'),
		'parent' => 'glossary',
		'cmd' => 'delete_glossary',
		'perm' => 'DELETE_GLOSSARY || ADMINISTRATOR',
	],
	['parent' => 'glossary'], // separator
	['text' => g_l('modules_glossary', '[menu_exit]'),
		'parent' => 'glossary',
		'cmd' => 'exit_glossary',
	],
//
// ---> Menu Options
//
	'options' => [
		'text' => g_l('modules_glossary', '[menu_options]'),
		'perm' => 'ADMINISTRATOR',
		'icon' => 'fa fa-cog'
	],
	['text' => g_l('modules_glossary', '[menu_settings]'),
		'parent' => 'options',
		'cmd' => 'glossary_settings',
		'perm' => 'ADMINISTRATOR',
	],
//
// ---> Menu Help
//
	'help' => [
		'text' => g_l('modules_glossary', '[menu_help]'),
		'icon' => 'fa fa-question-circle'
	],
	['text' => g_l('modules_glossary', '[menu_help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',
	],
	['text' => g_l('modules_glossary', '[menu_info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_modules',
	]
];

$langs = getWeFrontendLanguagesForBackend();
foreach($langs as $key => $language){
	$we_menu_glossary[$language] = ['text' => $language,
		'parent' => 'new',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	];

	$we_menu_glossary[] = ['text' => g_l('modules_glossary', '[abbreviation]'),
		'parent' => $language,
		'cmd' => ['new_glossary_abbreviation', $key],
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	];

	$we_menu_glossary[] = ['text' => g_l('modules_glossary', '[acronym]'),
		'parent' => $language,
		'cmd' => ['new_glossary_acronym', $key],
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	];

	$we_menu_glossary[] = ['text' => g_l('modules_glossary', '[foreignword]'),
		'parent' => $language,
		'cmd' => ['new_glossary_foreignword', $key],
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	];

	$we_menu_glossary[] = ['text' => g_l('modules_glossary', '[link]'),
		'parent' => $language,
		'cmd' => ['new_glossary_link', $key],
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	];

	$we_menu_glossary[] = ['text' => g_l('modules_glossary', '[textreplacement]'),
		'parent' => $language,
		'cmd' => ['new_glossary_textreplacement', $key],
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
	];
}

return $we_menu_glossary;
