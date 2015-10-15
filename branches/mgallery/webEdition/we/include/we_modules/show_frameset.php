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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
echo we_html_tools::getHtmlTop() .
 we_html_element::jsElement('
var makeNewEntryCheck = 0;
var publishWhenSave = 0;
var weModuleWindow = true;

function we_cmd() {
			var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.content.we_cmd.apply(this, args);
	}
');
?>
</head>
<body id="weMainBody" onload="setFrameSize()" onresize="setFrameSize()">
	<?php
	$_REQUEST['mod'] = $mod = (isset($mod) ? $mod : we_base_request::_(we_base_request::STRING, 'mod'));


//when opened by navigation hook
	/*if(we_base_request::_(we_base_request::STRING, 'tool') === 'navigation'){
		t_e('eleminate this call');
		$_REQUEST['mod'] = $mod = 'navigation';
	}*/

	//TODO: we should loop through all we_cmd and process them in respective we_module_frames.class only
	$cmd1 = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1); //to be used only for IDs or integer constants!
	$sid = we_base_request::_(we_base_request::RAW, 'sid');
	$bid = $mod === 'shop' && $cmd1 !== false ? $cmd1 : we_base_request::_(we_base_request::RAW, 'bid');

	echo we_html_element::htmlExIFrame('navi', WE_MODULES_PATH . 'navi.inc.php', 'background-color:white;position:absolute;top:0px;height:21px;left:0px;right:0px;overflow: hidden;') .
	we_html_element::htmlIFrame('content', WE_MODULES_DIR . 'show.php?mod=' . $mod . ($cmd1 === false ? '' : '&msg_param=' . $cmd1) . ($sid !== false ? '&sid=' . $sid : '') . ($bid !== false ? '&bid=' . $bid : ''), 'position:absolute;top:21px;bottom:0px;left:0px;right:0px;overflow: hidden;', '', '', false)
	;
	?></body></html>