<?php
/*
 * This is the template for tab update. It contains the information screen
 * before searching for an update
 * 
 */

$we_button = new we_button();
$searchButton = $we_button->create_button('search', $_SERVER['PHP_SELF'] . '?section=upgrade&update_cmd=upgrade&detail=lookForUpgrade');

$content = '
<table class="defaultfont" width="100%">
<tr>
	<td>' . $GLOBALS['l_liveUpdate']['upgrade']['actualVersion'] . '</td>
	<td>' . $GLOBALS['LU_Variables']['clientVersion'] . '</td>
</tr>
<tr>
	<td>
		<br />
		<br />
	</td>
</tr>
<tr>
	<td>' . $GLOBALS['l_liveUpdate']['upgrade']['lookForUpdate'] . '</td>
	<td>' . $searchButton . '</td>
</tr>
</table>
';

print liveUpdateTemplates::getHtml($GLOBALS['l_liveUpdate']['upgrade']['headline'], $content);

?>