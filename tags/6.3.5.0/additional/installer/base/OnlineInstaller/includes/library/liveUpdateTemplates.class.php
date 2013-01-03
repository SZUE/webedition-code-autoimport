<?php
/**
 * LiveUpdateTemplates is a helper function taking care of the view of the
 * update process. The functions here are only called from templates!
 *
 */
class liveUpdateTemplates {

	/**
	 * returns standard html container for output
	 *
	 * @param string $headline
	 * @param string $content
	 * @param integer $width
	 * @param integer $height
	 * @return string
	 */
	function getContainer($headline, $content) {


		return "
		<div id=\"leContent\" class=\"defaultfont\">
			<h1>{$headline}</h1>
			<p>
				{$content}
			</p>
		</div>";

	}

	/**
	 * returns header of template
	 *
	 * @return string
	 */
	function getHtmlHead() {

		return "";
	}

	/**
	 * Returns a html page as response
	 *
	 * @param string $headline
	 * @param string $content
	 * @param string $header
	 * @param string $buttons
	 * @param integer $contentWidth
	 * @param integer $contentHeight
	 * @return string
	 */
	function getHtml($headline, $content, $header='', $append = false) {

		if($append) {
			$PushJs = 'top.leContent.appendElement(document.getElementById("leContent"));';

		} else {
			$PushJs = 'top.leContent.replaceElement(document.getElementById("leContent"));';

		}

		return '<html>
	<head>
	' . liveUpdateTemplates::getHtmlHead() . '
	' . $header . '
	</head>
	<body>
	' . liveUpdateTemplates::getContainer($headline, $content) . '
	<script type="text/javascript">
	' . $PushJs . '
	</script>
	</body>
</html>';
	}
}