<?php
/**
 * webEdition CMS
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
 * @package    webEdition_update
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/*
 * This is the template for tab update. It contains the information screen
 * before searching for an update
 * 
 */



$content = '
<table class="defaultfont" width="100%">
<tr>
	<td>' . $GLOBALS['l_liveUpdate']['update']['actualVersion'] . '</td>
	<td>' . $GLOBALS['LU_Variables']['clientVersion'] . '</td>
</tr>
<tr>
	<td>' . $GLOBALS['l_liveUpdate']['update']['lastUpdate'] . '</td>
	<td>' . $this->Data['lastUpdate'] . '</td>
</tr>
<tr>
	<td></td><td>
		<br />
		<br />
	</td>
</tr>
<tr>
	<td></td><td><form name="betaform" action="'.$_SERVER['SCRIPT_NAME'] . '?section=beta" method="post">'.we_forms::checkboxWithHidden( (isset($_REQUEST["testUpdate"]) ? $_REQUEST["testUpdate"]: 0), 'setTestUpdate', $GLOBALS['l_liveUpdate']['beta']['lookForUpdate'], '','defaultfont' , 'betaform.submit()').'</form>
		<br />
		<br />
	</td>
</tr>
<tr>
	<td colspan="2">'. $GLOBALS['l_liveUpdate']['beta']['warning'].'
		<br />
		<br />
	</td>
</tr>

</table>
';

print liveUpdateTemplates::getHtml($GLOBALS['l_liveUpdate']['beta']['headline'], $content);

?>