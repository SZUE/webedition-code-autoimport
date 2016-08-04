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
we_html_tools::protect();
echo we_html_tools::getHtmlTop(g_l('global', '[question]'));

$yesCmd = "yes_cmd_pressed();";
$cancelCmd = "self.close();";

$nextCmd = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);

$allowedCmds = array("dologout", "close_all_documents");
if(!in_array($nextCmd, $allowedCmds)){
	$nextCmd = "";
}


$ctLngs = [];

foreach(g_l('contentTypes', '') as $key => $lng){
	$ctLngs [] = '"' . $key . '": "' . $lng . '"';
}

echo
we_html_element::jsElement('
var ctLngs = {' . implode(',', $ctLngs) . '};
var nextCmd="' . $nextCmd . '";
') .
 we_html_element::jsScript(JS_DIR . 'we_exit_multi_doc_question.js');
?>
</head>
<body class="weEditorBody" onload="setHotDocuments();" onBlur="self.focus();">
	<?= we_html_tools::htmlYesNoCancelDialog('
<div>
	' . g_l('alert', '[exit_multi_doc_question]') . '
	<br />
	<br />
	<div style="width: 350px; height: 150px; background: white; overflow: auto;">
		<ul id="ulHotDocuments">

		</ul>
	</div>
</div>', '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', true, false, true, $yesCmd, "", $cancelCmd); ?>
</body>
</html>