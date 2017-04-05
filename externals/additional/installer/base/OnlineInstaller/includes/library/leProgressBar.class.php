<?php
/**
 * $Id: leProgressBar.class.php 13539 2017-03-12 11:39:19Z mokraemer $
 */

class leProgressBar{

	static function get($Id){

		return '<table id="' . $Id . '" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td id="' . $Id . 'Background">
		<div id="' . $Id . 'Bar">&nbsp;</div>
	</td>
	<td valign="top" align="right" id="' . $Id . 'Percent">0%</td>
</tr>
</table>';
	}

}
