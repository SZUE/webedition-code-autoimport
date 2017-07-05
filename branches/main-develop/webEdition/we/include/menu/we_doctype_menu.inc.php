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
return [
	'new' => [
		'text' => g_l('modules_voting', '[menu_new]'),
		'icon' => 'fa fa-plus-circle',
	],
	['text' => g_l('weClass','[new_doc_type]'),
		'parent' => 'new',
		'cmd' => 'newDocType',
		'perm' => 'EDIT_DOCTYPE || ADMINISTRATOR',
	],
];
