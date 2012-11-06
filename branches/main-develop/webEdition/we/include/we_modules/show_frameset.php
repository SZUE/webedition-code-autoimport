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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
we_html_tools::htmlTop();
print we_html_element::jsElement('
	function toggleBusy(){
	}
	var makeNewEntry = 0;
	var publishWhenSave = 0;
	var weModuleWindow = true;

function we_cmd() {
		args = "";
		for(var i = 0; i < arguments.length; i++) {
					args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
		}
		eval("top.content.we_cmd("+args+")");
	}
');
print we_html_element::jsScript(JS_DIR . "keyListener.js");
if(isset($_REQUEST['mod']) && !isset($mod)){
	$mod = $_REQUEST['mod'];
}
$_REQUEST['mod'] = $mod;
?>
</head>
<body style="background-color:grey;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;" onload="setFrameSize()" onresize="setFrameSize()">
	<?php
	echo we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;'), we_html_element::htmlExIFrame('navi', WE_MODULES_PATH . 'navi.php', 'background-color:white;position:absolute;top:0px;height:21px;left:0px;right:0px;overflow: hidden;') .
		we_html_element::htmlIFrame('content', WE_MODULES_DIR . 'show.php?mod=' . $mod . (empty($_REQUEST['we_cmd'][1]) ? '' : "&msg_param=" . $_REQUEST['we_cmd'][1]) . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : '') . (isset($_REQUEST['bid']) ? '&bid=' . $_REQUEST['bid'] : ''), 'position:absolute;top:21px;bottom:0px;left:0px;right:0px;overflow: hidden;')
	);
	?></body></html>