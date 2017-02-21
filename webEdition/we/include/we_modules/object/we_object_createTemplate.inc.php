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
function getObjectTags($id, $isField = false){
	$tableInfo = we_objectFile::getSortedTableInfo($id, true, new DB_WE());
	$content = '<table style="border:1px solid black;width:400px">';
	$regs = [];
	foreach($tableInfo as $cur){
		if(preg_match('/(.+?)_(.*)/', $cur['name'], $regs)){
			$content .= getTmplTableRow($regs[1], $regs[2], $isField);
		}
	}
	$content .= '</table>';
	return $content;
}

function getMultiObjectTags($name){
	$cmd3 = we_base_request::_(we_base_request::RAW, 'we_cmd', '', 3);
	if(!isset($_SESSION['weS']['we_data'][$cmd3][0]["elements"][we_objectFile::TYPE_MULTIOBJECT . '_' . $name . "class"]["dat"])){
		return '';
	}
	$id = $_SESSION['weS']['we_data'][$cmd3][0]["elements"][we_objectFile::TYPE_MULTIOBJECT . '_' . $name . "class"]["dat"];
	$tableInfo = we_objectFile::getSortedTableInfo($id, true, new DB_WE());
	$content = '<table style="border:1px solid black;width:400px">';

	//FIXME: causes internal server error
	$regs = [];
	foreach($tableInfo as $cur){
		if(preg_match('/(.+?)_(.*)/', $cur['name'], $regs)){
//			$content .= getTmplTableRow($regs[1], $regs[2], true);
		}
	}
	$content .= '</table>';
	return $content;
}

function getTemplTag($type, $name, $isField = false){
	switch($type){
		case 'meta':
			return $isField ? '<we:field type="select" name="' . $name . '"/>' : '<we:var type="select" name="' . $name . '"/>';
		default:
		case 'input':
		case 'text':
		case 'int':
		case 'float':
			return $isField ? '<we:field name="' . $name . '"/>' : '<we:var name="' . $name . '"/>';
		case 'link':
			return $isField ? '<we:field type="link" name="' . $name . '"/>' : '<we:var type="link" name="' . $name . '"/>';
		case 'href':
			return $isField ? '<we:field type="href" name="' . $name . '"/>' : '<we:var type="href" name="' . $name . '"/>';
		case 'img':
			return $isField ? '<we:field type="img" name="' . $name . '"/>' : '<we:var type="img" name="' . $name . '"/>';
		case 'checkbox':
			return $isField ? '<we:field type="checkbox" name="' . $name . '"/>' : '<we:var type="checkbox" name="' . $name . '"/>';
		case 'date':
			return $isField ? '<we:field type="date" name="' . $name . '"/>' : '<we:var type="date" name="' . $name . '"/>';
		case 'object':
			return (!in_array($name, $GLOBALS["usedIDs"]) ?
					getObjectTags($name, $isField) : '');
		case we_objectFile::TYPE_MULTIOBJECT:
			return getMultiObjectTags($name);
	}
	return '';
}

function getTmplTableRow($type, $name, $isField = false){
	if($type == we_objectFile::TYPE_MULTIOBJECT){
		if($isField){
			$open = '<we:ifFieldNotEmpty match="' . $name . '" type="' . $type . '">';
			$close = "</we:ifFieldNotEmpty>";
		} else {
			$open = '<we:ifVarNotEmpty match="' . $name . '" type="' . $type . '">';
			$close = "</we:ifVarNotEmpty>";
		}
		return '
<tr>
	<td style="width:100px;"><b>' . $name . '</b></td>
	<td style="width:300px">
		' . $open . '
		<we:listview type="multiobject" name="' . $name . '">
			<we:repeat>' . getTemplTag($type, $name) . '</we:repeat>
		</we:listview>
		<we:else/>
			' . g_l('global', '[no_entries]') . '
		' . $close . '
	</td>
</tr>';
	} else {
		return '
<tr>
	<td style="width:100px;"><b>' . (($type != "object") ? $name : "") . '</b></td>
	<td style="width:300px;">' . getTemplTag($type, $name, $isField) . '</td>
</tr>';
	}
}

$cmd3 = we_base_request::_(we_base_request::RAW, 'we_cmd', '', 3);

echo we_html_tools::getHtmlTop(g_l('weClass', '[generateTemplate]'), '', '', we_editor_script::get()) .
 '<body class="weDialogBody"><form name="we_form">';
$tmpl = new we_object_createTemplate();

$tmpl->we_new();
$tmpl->Filename = isset($filename) ? $filename : "";
$tmpl->Extension = ".tmpl";

$tmpl->setParentID(isset($pid) ? $pid : 0 );
$tmpl->Path = $tmpl->ParentPath . (isset($filename) ? $filename : "") . ".tmpl";

$usedIDs = [$_SESSION['weS']['we_data'][$cmd3][0]["ID"]];

$sort = $_SESSION['weS']['we_data'][$cmd3][0]["elements"]["we_sort"]["dat"];

$content = '<html>
	<head>
		<we:title></we:title>
		<we:description></we:description>
		<we:keywords></we:keywords>
	</head>
	<body>
		<table style="border1px solid black;width:400px">
';

if($sort){
	foreach($sort as $key => $val){
		$name = $_SESSION['weS']['we_data'][$cmd3][0]["elements"][$_SESSION['weS']['we_data'][$cmd3][0]["elements"]["wholename" . $key]["dat"]]["dat"];
		$type = $_SESSION['weS']['we_data'][$cmd3][0]["elements"][$_SESSION['weS']['we_data'][$cmd3][0]["elements"]["wholename" . $key]["dat"] . we_object::ELEMENT_TYPE]["dat"];

		$content .= getTmplTableRow($type, $name);
	}
}

$content .= '</table>';
if($_SESSION['weS']['we_data'][$cmd3][0]["ID"]){
	$content .= '
		<p>
		<we:listview type="object" classid="' . $_SESSION['weS']['we_data'][$cmd3][0]["ID"] . '" rows="10">
			<we:repeat>
		<p><table style="border:1px solid black;width:400px">';


	if($sort){
		foreach($sort as $key => $val){
			$name = $_SESSION['weS']['we_data'][$cmd3][0]["elements"][$_SESSION['weS']['we_data'][$cmd3][0]["elements"]["wholename" . $key]["dat"]]["dat"];
			$type = $_SESSION['weS']['we_data'][$cmd3][0]["elements"][$_SESSION['weS']['we_data'][$cmd3][0]["elements"]["wholename" . $key]["dat"] . we_object::ELEMENT_TYPE]["dat"];

			$content .= getTmplTableRow($type, $name, true);
		}
	}

	$content .= '</table></p>
			</we:repeat>
			<we:ifFound>
				<p><table class="default" style="width:400px;">
					<tr>
						<we:ifBack>
							<td><we:back>back</we:back></td>
						</we:ifBack>
						<we:ifNext>
							<td style="text-align:right"><we:next>next</we:next></td>
						</we:ifNext>
					</tr>
				</table></p>
			<we:else/>
				' . g_l('global', '[no_entries]') . '
			</we:ifFound>
		</we:listview>
';
}
$content .= '
	</body>
</html>';


//  $_SESSION['weS']["content"] is only used for generating a default template, it is
//  used only in WE_OBJECT_MODULE_PATH/we_object_createTemplatecmd.php
$_SESSION['weS']['content'] = $content;

$buttons = we_html_button::position_yes_no_cancel(
				we_html_button::create_button(we_html_button::SAVE, "javascript:if(document.we_form.we_" . $tmpl->Name . "_Filename.value != ''){ document.we_form.action=WE().consts.dirs.WE_MODULES_DIR+'object/we_object_createTemplatecmd.php';document.we_form.submit();}else{ " . we_message_reporting::getShowMessageCall(g_l('alert', '[input_file_name]'), we_message_reporting::WE_MESSAGE_ERROR) . " }"), null, we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();")
);


echo we_html_tools::htmlDialogLayout($tmpl->formPath(), g_l('weClass', '[generateTemplate]'), $buttons) .
 we_html_element::htmlHiddens(["SID" => $tmpl->Name,
	"we_cmd[3]" => $cmd3,
	"we_cmd[2]" => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 2)
]) . '
</form>
</body></html>';
