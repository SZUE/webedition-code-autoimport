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
/* the parent class of storagable webEdition classes */


class we_modules_view implements we_modules_viewIF{
	var $db;
	var $frameset;
	var $topFrame;
	var $Model;

	public function __construct($frameset = '', $topframe = 'top.content'){
		$this->db = new DB_WE();
		$this->frameset = $frameset;
		$this->topFrame = $topframe;
	}

	function getCommonHiddens($cmds = []){
		return we_html_element::htmlHiddens([
				'cmd' => (isset($cmds['cmd']) ? $cmds['cmd'] : ''),
				'cmdid' => (isset($cmds['cmdid']) ? $cmds['cmdid'] : ''),
				'pnt' => (isset($cmds['pnt']) ? $cmds['pnt'] : ''),
				'tabnr' => (isset($cmds['tabnr']) ? $cmds['tabnr'] : '')
		]);
	}

	function getJSTop(){
		return '';
	}

	function getJSProperty(){
		return '';
	}

	function getJSSubmitFunction($def_target = "edbody"){
		//only by customer + user
		return '
function submitForm(target,action,method,form) {
	var f = form ? self.document.forms[form] : self.document.we_form;
	f.target = target?target:"' . $def_target . '";
	f.action = action?action:"' . $this->frameset . '";
	f.method = method?method:"post";

	f.submit();
}';
	}

	public function processCommands(){
		switch(we_base_request::_(we_base_request::STRING, 'cmd', '')){
			case 'switchPage':
				break;
			default:
		}
	}

	public function processVariables(){
		$this->page = we_base_request::_(we_base_request::INT, 'page', $this->page);
	}

	public function getActualHomeScreen($mod, $icon, $content, $body = ''){
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? $modData['text'] : '';

		$row = 0;
		$starttable = new we_html_table(["cellpadding" => 7], 3, 1);
		$starttable->setCol($row++, 0, ["class" => "defaultfont titleline", "colspan" => 3], $title);
		$starttable->setCol($row++, 0, ['class' => 'defaultfont', "colspan" => 3], "");
		$starttable->setCol($row++, 0, ["style" => "text-align:center"], $content);

		ob_start();
		echo we_html_tools::getHtmlTop('', '', '', STYLESHEET . we_html_element::cssLink(CSS_DIR . 'tools_home.css') . $this->getJSProperty() . (empty($GLOBALS['extraJS']) ? '' : $GLOBALS['extraJS']));
		?>
		<body class="home" onload="loaded = true;
				var we_is_home = 1;">
			<div id="tabelle"><?= $starttable->getHtml(); ?></div>
			<div id="modimage"><img src="<?= IMAGE_DIR . "startscreen/" . $icon; ?>" style="width:335px;height:329px;" /></div>
				<?= $body; ?>
		</body>
		</html>
		<?php
		return ob_get_clean();
	}

}
