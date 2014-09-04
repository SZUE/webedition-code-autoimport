<?php

/**
 * this file is only existent for compatibility reasons
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
 * @package deprecated
 * @deprecated since version 6.3.9
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
t_e('deprecated', 'call of ' . __FILE__ . ' is deprecated and will be removed. Use we_base_delete::deleteEntry() instead of deleteEntry()');
$notprotect = isset($GLOBALS['NOT_PROTECT']) && $GLOBALS['NOT_PROTECT'] && (!we_base_request::_(we_base_request::BOOL, 'NOT_PROTECT'));

//this file is only existent for compatibility reasons

if(!$notprotect){
	we_html_tools::protect();
}

function deleteTreeEntries($dontDeleteClassFolders = false){
	return weTree::deleteTreeEntries($dontDeleteClassFolders);
}

function checkDeleteEntry($id, $table){
	return we_base_delete::checkDeleteEntry($id, $table);
}

function deleteThumbsByImageID($id){
	we_thumbnail::deleteByImageID($id);
}

function deleteThumbsByThumbID($id){
	we_thumbnail::deleteByThumbID($id);
}

function deleteEntry($id, $table, $delR = true, $skipHook = false, we_database_base $DB_WE = null){
	we_base_delete::deleteEntry($id, $table, $delR, $skipHook, $DB_WE);
}
