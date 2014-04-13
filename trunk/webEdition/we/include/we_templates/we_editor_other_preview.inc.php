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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
// force the download of this document
if(weRequest('string', 'we_cmd', '', 3) == 'download'){
	$file = (file_exists($_SERVER['DOCUMENT_ROOT'] . $we_doc->Path) ? $_SERVER['DOCUMENT_ROOT'] . $we_doc->Path : $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $we_doc->Path);
	$_filename = $we_doc->Filename . $we_doc->Extension;
	if(file_exists($file)){
		if(we_isHttps()){ // Additional headers to make downloads work using IE in HTTPS mode.
			header('Pragma: ');
			header('Cache-Control: ');
			header('Expires: ' . gmdate("D, d M Y H:i:s") . ' GMT');
			header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP 1.1
			header('Cache-Control: post-check=0, pre-check=0', false);
		} else {
			header('Cache-control: private, max-age=0, must-revalidate');
		}

		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . trim(htmlentities($_filename)) . '"');
		header('Content-Description: ' . trim(htmlentities($_filename)));
		header('Content-Length: ' . filesize($file));
		readfile($file);
		exit;
	}
}


echo we_html_tools::getHtmlTop() .
 (substr(weRequest('string', 'we_cmd', '', 0), 0, 15) == 'doImage_convert' ?
	we_html_element::jsElement('parent.frames[0].we_setPath("' . $we_doc->Path . '","' . $we_doc->Text . '", "' . $we_doc->ID . '");') : ''
) .
 we_html_element::jsScript(JS_DIR . 'windows.js');
require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

echo STYLESHEET;
?>
</head>

<body class="weEditorBody">
	<form name="we_form" method="post">
		<?php
		echo we_class::hiddenTrans();

		switch(strtolower($we_doc->Extension)){
			case '.pdf':
				$previewAvailable = true;
				break;
			default:
				$previewAvailable = false;
				break;
		}

		if($previewAvailable && $we_doc->ID){
			echo we_html_element::htmlIFrame('preview', $we_doc->Path, '');
		} else {
			$parts = array(
				array("headline" => g_l('weClass', "[preview]"), "html" => we_html_tools::htmlAlertAttentionBox(g_l('weClass', "[no_preview_available]"), we_html_tools::TYPE_ALERT), "space" => 120)
			);

			if($we_doc->ID){
				$_we_transaction = weRequest('transaction', 'we_transaction', 0);
				$link = "<a href='" . getServerUrl() . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=" . weRequest('raw', 'we_cmd', '', 0) . "&we_cmd[1]=" . weRequest('raw', 'we_cmd', '', 1) . "&we_cmd[2]=" . weRequest('raw', 'we_cmd', '', 2) . "&we_cmd[3]=download&we_transaction=" . $_we_transaction . "'>" . $http = $we_doc->getHttpPath() . "</a>";
			} else {
				$link = g_l('weClass', "[file_not_saved]");
			}
			$parts[] = array("headline" => g_l('weClass', "[download]"), "html" => $link, "space" => 120);

			echo we_html_multiIconBox::getHTML('weOtherDocPrev', '100%', $parts, 20);
		}
		?>
		<input type="hidden" name="we_complete_request" value="1"/>
	</form>
</body>

</html>
