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
//	frameset called when opened a none webEdition-document from webEdition
//	here all parameters are dealt and submitted to the document
$text = we_base_request::_(we_base_request::URL, 'we_cmd', '', 1); // Path
$param = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2);
$url = $text . $param; // + Parameters

if(!$url || (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://')){

	$serveradress = getServerUrl();

	$url = (!$url || $url{0} != '/' ?
			$serveradress . '/' . $url :
			$serveradress . $url);
}
//  extract the path to the file without parameters for file_exists -> we_SEEM_openExtDoc_content.php
$arr = parse_url($url);
$newUrl = $arr['scheme'] . '://' . $arr['host'] . ( isset($arr['port']) ? (':' . $arr['port']) : '' ) . (isset($arr['path']) ? $arr['path'] : '' );


//	we also need some functionality here to check if the location of the doc was cahnged
echo we_html_tools::getHtmlTop('', '', 'frameset');
?>
<script><!--
	var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrame(window.name);

	_EditorFrame.initEditorFrameData({
		EditorType: "none_webedition",
		EditorDocumentText: "<?php echo str_replace('"', '', $arr["path"]); ?>",
		EditorDocumentPath: "<?php echo str_replace('"', '', $newUrl); ?>",
		EditorContentType: "none_webedition",
		EditorUrl: "<?php echo str_replace('"', '', $text); ?>",
		EditorDocumentParameters: "<?php echo str_replace('"', '', $param); ?>"
	});

	function checkDocument() {

		loc = null;

		try {
			loc = extDocContent.location;
		} catch (e) {

		}

		_EditorFrame.setEditorIsHot(false);

		if (loc) {	//	Page is on webEdition-Server, open it with matching command

			// close existing editor, it was closed very hard
			WE().layout.weEditorFrameController.closeDocument(_EditorFrame.getFrameId());

			// build command for this location
			top.we_cmd("open_url_in_editor", loc);

		} else {	//	Page is not known - replace top and bottom frame of editor
			//	Fill upper and lower Frame with white
			//	If the document is editable with webedition, it will be replaced
			//	Location not known - empty top and footer

			_EditorFrame.initEditorFrameData({
				EditorType: "none_webedition",
				EditorContentType: "none_webedition",
				EditorDocumentText: "Unknown",
				EditorDocumentPath: "Unknown"
			});

			extDocHeader.location = "about:blank";
			extDocFooter.location = WE().consts.dirs.WEBEDITION_DIR+ "we/include/we_seem/we_SEEM_openExtDoc_footer.php";
		}
	}
	//-->
</script>
</head>
<body onload="_EditorFrame.initEditorFrameData({'EditorIsLoading': false});">
	<?php
	$headerSize = 35;
	echo we_html_element::htmlIFrame('extDocHeader', we_class::url(WEBEDITION_DIR . "we/include/we_seem/we_SEEM_openExtDoc_header.php?filepath=" . urlencode($url) . "&url=" . $newUrl), 'position:absolute;top:0px;left:0px;right:0px;height:' . $headerSize . 'px;', '', '', false) .
	we_html_element::htmlIFrame('extDocContent', we_class::url(WEBEDITION_DIR . "we/include/we_seem/we_SEEM_openExtDoc_content.php?filepath=" . urlencode($url) . '&url=' . $newUrl . '&paras=' . (isset($parastr) ? urlencode($parastr) : "") . '&we_complete_request=1'), 'position:absolute;top:' . $headerSize . 'px;left:0px;right:0px;bottom:40px;', '', 'if (openedWithWE==false) {checkDocument();}openedWithWE=false;') .
	we_html_element::htmlIFrame('extDocFooter', we_class::url(WEBEDITION_DIR . "we/include/we_seem/we_SEEM_openExtDoc_footer.php"), 'position:absolute;bottom:0px;left:0px;right:0px;height:40px;', '', '', false);
	?>
</body>
</html>