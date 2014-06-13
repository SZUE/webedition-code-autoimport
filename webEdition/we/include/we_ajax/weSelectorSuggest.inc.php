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
header('Content-type: text/plain');


we_html_tools::protect();
$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 2);
$search = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);

if(!$search || !$table){
	exit();
}

$selectorSuggest = new we_selector_query();
$contentTypes = explode(",", we_base_request::_(we_base_request::STRING, 'we_cmd', null, 3));
$selectorSuggest->search($search, $table, $contentTypes);
$suggests = $selectorSuggest->getResult();
$return = "";
if(is_array($suggests)){
	foreach($suggests as $sug){
		$return .= $sug['Path'] . "	" . $sug['ID'] . "\n";
	}
}
echo $return;
