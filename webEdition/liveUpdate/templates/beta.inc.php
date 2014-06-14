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
/*
 * This is the template for tab update. It contains the information screen
 * before searching for an update
 *
 */

if(defined('WE_VERSION_SUPP') && WE_VERSION_SUPP != 'release'){
	$ischecked = 1;
}else{
	$ischecked = we_base_request::_(we_base_request::BOOL,"setTestUpdate");
}
$conf = we_base_file::load(LIVEUPDATE_DIR . 'conf/conf.inc.php');
if($ischecked){
	if(strpos($conf, '$' . "_REQUEST['testUpdate'] = 0;") !== false){
		$conf = str_replace('$' . "_REQUEST['testUpdate'] = 0;", '$' . "_REQUEST['testUpdate'] = 1;", $conf);
		we_base_file::save(LIVEUPDATE_DIR . 'conf/conf.inc.php', $conf);
	}
} else {
	if(strpos($conf, '$' . "_REQUEST['testUpdate'] = 1;") !== false){
		$conf = str_replace('$' . "_REQUEST['testUpdate'] = 1;", '$' . "_REQUEST['testUpdate'] = 0;", $conf);
		we_base_file::save(LIVEUPDATE_DIR . 'conf/conf.inc.php', $conf);
	}
}

$content = '
<table class="defaultfont" width="100%">
<tr>
	<td>' . g_l('liveUpdate', '[update][actualVersion]') . '</td>
	<td>' . $GLOBALS['LU_Variables']['clientVersion'] . '</td>
</tr>
<tr>
	<td>' . g_l('liveUpdate', '[update][lastUpdate]') . '</td>
	<td>' . $this->Data['lastUpdate'] . '</td>
</tr>
<tr>
	<td></td><td>
		<br />
		<br />
	</td>
</tr>
<tr>
	<td></td><td><form name="betaform" action="' . $_SERVER['SCRIPT_NAME'] . '?section=beta" method="post">' . we_html_forms::checkboxWithHidden($ischecked, 'setTestUpdate', $GLOBALS['l_liveUpdate']['beta']['lookForUpdate'], '', 'defaultfont', 'betaform.submit()') . '</form>
		<br />
		<br />
	</td>
</tr>
<tr>
	<td colspan="2">' . g_l('liveUpdate', '[beta][warning]') . '
		<br />
		<br />
	</td>
</tr>

</table>
';

echo liveUpdateTemplates::getHtml(g_l('liveUpdate', '[beta][headline]'), $content);
