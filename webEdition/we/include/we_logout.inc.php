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
//set the new user to requested documents
$DB_WE->query('UPDATE ' . LOCK_TABLE . ' SET sessionID=x\'00\',UserID=releaseRequestID WHERE releaseRequestID IS NOT NULL AND releaseRequestID!=UserID AND UserID=' . intval($_SESSION['user']['ID']) . ' AND sessionID=x\'' . session_id() . '\'');

$DB_WE->query('DELETE FROM ' . LOCK_TABLE . ' WHERE UserID=' . intval($_SESSION['user']['ID']) . ' AND sessionID=x\'' . session_id() . '\'');
//FIXME: table is set to false value, if 2 sessions are open; but this is updated shortly - so ignore it now
//TODO: update to time if still locked files open
$DB_WE->query('UPDATE ' . USER_TABLE . ' SET Ping=NULL WHERE ID=' . intval($_SESSION['user']['ID']));

we_base_file::cleanTempFiles(true);

//	getJSCommand
/*$path = (isset($_SESSION['weS']['SEEM']['startId']) ? // logout from webEdition opened with tag:linkToSuperEasyEditMode
		$_SESSION['weS']['SEEM']['startPath'] :
		WEBEDITION_DIR);
*/

we_base_sessionHandler::makeNewID(true);

if(!isset($GLOBALS['isIncluded']) || !$GLOBALS['isIncluded']){
	echo we_html_tools::getHtmlTop() . we_html_element::jsScript(JS_DIR . 'logout.js');
}
