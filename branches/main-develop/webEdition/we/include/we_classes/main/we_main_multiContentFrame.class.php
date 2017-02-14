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
abstract class we_main_multiContentFrame{

	public static function getHTML(){

		$isAppleTouch = (we_base_browserDetect::inst()->getSystem() == we_base_browserDetect::SYS_IPAD || we_base_browserDetect::inst()->getSystem() == we_base_browserDetect::SYS_IPHONE);



		$cmd_string = [];

		if(we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include')){
			for($i = 1; $i < 4; $i++){
				$cmd_string[] = we_base_request::_(we_base_request::STRING, 'we_cmd', '', $i);
			}
			$cmd_string[] = 'SEEM_edit_include';
		}

		$count = (isset($_SESSION) && isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE) ? 1 : 32;
		$frames = '';

		for($i = 0; $i < $count; $i++){
			//'overflow:hidden;' removed to fix bug #6540
			$frames .= '<iframe style="' . ($i == 0 ? '' : (we_base_browserDetect::isChrome() ? 'display:none;' : 'width:0px;height:0px;')) . '" src="' . WEBEDITION_DIR . 'editors/blank_editor.html" name="multiEditFrame_' . $i . '" id="multiEditFrame_' . $i . '" noresize ></iframe>';
		}

		echo we_html_tools::getHtmlTop('', '', '', we_html_element::cssLink(CSS_DIR . 'multiEditor.css') .
			we_html_element::jsScript(JS_DIR . 'multiEditor/EditorFrameController.js'), we_html_element::htmlBody([
				'id' => 'multiEditor',
				'onresize' => "if(WE().layout.multiTabs){WE().layout.multiTabs.setFrameSize()}",
				'onload' => 'startMultiEditor();' . we_gui_multiEditor::start($cmd_string)
				], '
<div id="multiEditorDocumentTabsFrameDiv">
	<div id="weMultiTabs">
		<div id="tabContainer" name="tabContainer"></div>
		<div class="hidden" id="tabDummy" title="" name="" onclick="WE().layout.multiTabs.selectFrame(this)">
			<span class="spacer status" id="###loadId###" title="" ></span>
			<span id="###tabTextId###" class="cutText text"></span>
			<span class="spacer trailing">
				<i class="fa fa-asterisk modified" id="###modId###"></i>
				<span class="close" id="###closeId###" onclick="WE().layout.multiTabs.onCloseTab(this)">
					<i class="fa fa-close fa-lg "></i>
				</span>
		</div>
	</div>
</div>
<div id="multiEditorEditorFramesetsDiv" class="' . ($isAppleTouch ? 'iframeScrollIpad' : '') . '">' .
				$frames . '
</div>'
			)
		);
	}

}
