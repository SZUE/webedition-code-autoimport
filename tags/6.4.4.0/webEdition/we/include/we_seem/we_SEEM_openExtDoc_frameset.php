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
we_html_tools::protect();
$_text = we_base_request::_(we_base_request::URL, 'we_cmd', '', 1); // Path
$param = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2);
$_url = $_text . $param; // + Parameters

if(!$_url || (substr($_url, 0, 7) != 'http://' && substr($_url, 0, 8) != 'https://')){

	$serveradress = getServerUrl();

	$_url = (!$_url || $_url{0} != '/' ?
			$serveradress . '/' . $_url :
			$serveradress . $_url);
}
//  extract the path to the file without parameters for file_exists -> we_SEEM_openExtDoc_content.php
$arr = parse_url($_url);
$newUrl = $arr['scheme'] . '://' . $arr['host'] . ( isset($arr['port']) ? (':' . $arr['port']) : '' ) . (isset($arr['path']) ? $arr['path'] : '' );


//	we also need some functionality here to check if the location of the doc was cahnged
echo we_html_tools::getHtmlTop('', '', 'frameset');
?>
<script type="text/javascript"><!--

	var _EditorFrame = top.weEditorFrameController.getEditorFrame(window.name);

	_EditorFrame.initEditorFrameData({
		EditorType: "none_webedition",
		EditorDocumentText: "<?php echo $arr["path"] ?>",
		EditorDocumentPath: "<?php echo $newUrl; ?>",
		EditorContentType: "none_webedition",
		EditorUrl: "<?php echo $_text; ?>",
		EditorDocumentParameters: "<?php echo $param; ?>"
	});

	function checkDocument() {

		loc = null;

		try {
			loc = String(extDocContent.location);
		} catch (e) {

		}

		_EditorFrame.setEditorIsHot(false);

		if (loc) {	//	Page is on webEdition-Server, open it with matching command

			// close existing editor, it was closed very hard
			top.weEditorFrameController.closeDocument(_EditorFrame.getFrameId());

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
			extDocFooter.location = "<?php echo WEBEDITION_DIR . 'we/include/we_seem/we_SEEM_openExtDoc_footer.php' ?>";
		}
	}
	//-->
</script>
</head>
<frameset onload="_EditorFrame.initEditorFrameData({'EditorIsLoading': false});" rows="40,*,40" framespacing="0" border="0" frameborder="NO">

	<frame src="<?php echo WEBEDITION_DIR . "we/include/we_seem/"; ?>we_SEEM_openExtDoc_header.php?filepath=<?php echo urlencode($_url); ?>&url=<?php echo $newUrl ?>" name="extDocHeader" noresize scrolling="no">
	<frame onload="if (openedWithWE == 0) {
				checkDocument();
			}
			openedWithWE = 0;" src="<?php echo WEBEDITION_DIR . 'we/include/we_seem/'; ?>we_SEEM_openExtDoc_content.php?filepath=<?php echo urlencode($_url); ?>&url=<?php echo $newUrl ?>&paras=<?php echo (isset($parastr) ? urlencode($parastr) : ''); ?>" name="extDocContent" noresize>
	<frame src="<?php echo WEBEDITION_DIR . 'we/include/we_seem/'; ?>we_SEEM_openExtDoc_footer.php" name="extDocFooter" noresize>
</frameset><noframes></noframes>
</html>