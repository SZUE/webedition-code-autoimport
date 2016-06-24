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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

$tools = we_tool_lookup::getAllTools(true, true);

$whiteList = [];
foreach($tools as $k => $v){
	if(isset($v['name'])){
		$whiteList[] = $v['name'];
	}
}
$tool = we_base_request::_(we_base_request::STRING, 'tool');
if(!$tool || !in_array($tool, $whiteList)){
	exit();
}

if($tool === 'weSearch'){
	require_once(WE_INCLUDES_PATH . 'we_tools/weSearch/edit_weSearch_frameset.php');
	return;
}

//check if bootstrap file exists of specific app
if(file_exists(WEBEDITION_PATH . 'apps/' . $tool . '/index.php')){

	header('Location: ' . WEBEDITION_DIR . 'apps/' . $tool . '/index.php/frameset/index' . //redirect.php/
		(isset($REQUEST['modelid']) ? '/modelId/' . intval($REQUEST['modelid']) : '') .
		(isset($REQUEST['tab']) ? '/tab/' . intval($REQUEST['tab']) : ''));
	exit();
}
