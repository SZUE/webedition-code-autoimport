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
switch($cmd){

	case 'glossary_edit_acronym':
	case 'glossary_edit_abbreviation':
	case 'glossary_edit_foreignword':
	case 'glossary_edit_link':
	case 'glossary_edit_textreplacement':
	case 'glossary_edit_ifthere':
	case 'glossary_view_folder':
	case 'glossary_view_type':
	case 'glossary_view_exception':
		$GLOBALS['mod'] = 'glossary';
		return 'we_modules/show_frameset.php';

	case 'glossary_settings':
		return 'we_modules/glossary/edit_glossary_settings_frameset.php';

	case 'glossary_dictionaries':
		return 'we_modules/glossary/edit_glossary_dictionaries_frameset.php';

	case 'glossary_check':
		$GLOBALS['mod'] = 'glossary';
		return 'we_modules/glossary/add_items.inc.php';
}
