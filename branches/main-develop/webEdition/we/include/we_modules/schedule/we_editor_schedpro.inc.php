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


include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_html_tools.inc.php");
include_once(WE_SCHEDULE_MODULE_DIR."we_schedpro.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/html/we_multibox.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/html/we_button.inc.php");

if(defined("SCHEDULE_TABLE")){
	trigger_schedule();
}

htmlTop();

include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_editors/we_editor_script.inc.php");
print STYLESHEET;
echo we_htmlElement::jsScript(JS_DIR.'windows.js');?>
	</head>
	<body  class="weEditorBody" onunload="doUnload()">
<form name="we_form" onsubmit="return false"><?php $we_doc->pHiddenTrans();

$parts = array();

foreach($we_doc->schedArr as $i=>$sched){
	$schedObj = new we_schedpro($sched,$i);

	$ofT = defined("OBJECT_FILES_TABLE") ? OBJECT_FILES_TABLE : "";

	array_push($parts, array(	"headline"=>"",
								"html"=>$schedObj->getHTML($GLOBALS["we_doc"]->Table==$ofT),
								"space"=>0
							)
				);

}
array_push($parts, array(		"headline"=>"",
								"html"=>htmlAlertAttentionBox(g_l('modules_schedule',"[descriptiontext]"),2,"700").'<br><br>'. we_button::create_button("image:btn_add_schedule", "javascript:we_cmd('add_schedule')"),
								"space"=>0
						)
				);
print we_multiIconBox::getJS();
print we_multiIconBox::getHTML("","100%",$parts,20,"",-1,"","",false);

?>
</form>
	</body>
</html>