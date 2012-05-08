<?php
class leProgressBar {

	function get($Id) {

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