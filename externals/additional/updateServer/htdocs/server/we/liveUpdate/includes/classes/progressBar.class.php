<?php

class progressBar extends progressBarBase {

	/**
	 * @return string
	 */
	function getProgressBarHtml() {

		return '<table cellpadding="0" cellspacing="0" width="100%" border="0" class="defaultfont">
		<tr>
			<td id="progressBarBackground">
				<div id="progressBar">&nbsp;</div>
			</td>
			<td valign="top" align="right" id="progressBarPercent">0%</td>
		</tr>
		</table>';

	}


	/**
	 * @return string
	 */
	function getProgressBarJs() {
		return '
<script type="text/javascript">

	function setProgressBar(width) {

		var progressPercentText = document.getElementById("progressBarPercent");
		var progressPercentBar = document.getElementById("progressBar");

		if (progressPercentText) {
			progressPercentText.innerHTML = width + "%";
		}

		if (progressPercentBar) {
			progressPercentBar.style.width = width + "%";
		}
	}
</script>';

	}

}

?>