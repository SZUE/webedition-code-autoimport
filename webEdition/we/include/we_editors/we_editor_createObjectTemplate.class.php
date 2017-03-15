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
class we_editor_createObjectTemplate extends we_editor_base{

	private $usedIDs = [];
	protected $filename = '';
	protected $pid = 0;

	public static function cmd(){
		$GLOBALS['we_transaction'] = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', 0, 3);
		$editor = new self();
		$jscmd = $editor->getJsCmd();

		$nr = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
		$sid = we_base_request::_(we_base_request::STRING, 'SID');

		$GLOBALS['we_doc'] = new we_template();
		$GLOBALS['we_doc']->Table = TEMPLATES_TABLE;
		$GLOBALS['we_doc']->we_new();

		$editor->filename = we_base_request::_(we_base_request::FILE, 'we_' . $sid . '_Filename');

		$GLOBALS['we_doc']->Filename = $editor->filename;
		$GLOBALS['we_doc']->Extension = '.tmpl';
		$GLOBALS['we_doc']->setParentID(we_base_request::_(we_base_request::INT, 'we_' . $sid . '_ParentID'));
		$GLOBALS['we_doc']->Path = $GLOBALS['we_doc']->ParentPath . (($GLOBALS['we_doc']->ParentPath != '/') ? '/' : '') . $editor->filename . '.tmpl';
		$GLOBALS['we_doc']->ContentType = we_base_ContentTypes::TEMPLATE;

		$GLOBALS['we_doc']->Table = TEMPLATES_TABLE;


//  $_SESSION['weS']['content'] is only used for generating a default template, it is
//  set in WE_OBJECT_MODULE_PATH\we_object_createTemplate.inc.php
		$GLOBALS['we_doc']->elements['data']['dat'] = $_SESSION['weS']['content'];
		$GLOBALS['we_doc']->elements['data']['type'] = 'txt';
		unset($_SESSION['weS']['content']);


		if(($we_responseText = $GLOBALS['we_doc']->checkFieldsOnSave())){
			$jscmd->addCmd($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			echo $editor->show();
			return;
		}
		if($GLOBALS['we_doc']->we_save()){
			$jscmd->addMsg(sprintf(g_l('weEditor', '[' . $GLOBALS['we_doc']->ContentType . '][response_save_ok]'), $GLOBALS['we_doc']->Path), we_message_reporting::WE_MESSAGE_NOTICE);
			$jscmd->addCmd('we_cmd', ["object_changeTempl_ob", $nr, $GLOBALS['we_doc']->ID]);
			$jscmd->addCmd('close');
			echo $jscmd->getCmds();
			return;
		}
		$we_responseText = sprintf(g_l('weEditor', '[' . $GLOBALS['we_doc']->ContentType . '][response_save_notok]'), $GLOBALS['we_doc']->Path);
		$jscmd->addCmd($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
		echo $editor->show();
	}

	private function getObjectTags($id, $isField = false){
		$tableInfo = we_objectFile::getSortedTableInfo($id, true, new DB_WE());
		$content = '<table style="border:1px solid black;width:400px">';
		$regs = [];
		foreach($tableInfo as $cur){
			if(preg_match('/(.+?)_(.*)/', $cur['name'], $regs)){
				$content .= $this->getTmplTableRow($regs[1], $regs[2], $isField);
			}
		}
		$content .= '</table>';
		return $content;
	}

	private function getMultiObjectTags($name){
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
//			$content .= $this->getTmplTableRow($regs[1], $regs[2], true);
			}
		}
		$content .= '</table>';
		return $content;
	}

	private function getTemplTag($type, $name, $isField = false){
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
				return (!in_array($name, $this->usedIDs) ?
						$this->getObjectTags($name, $isField) : '');
			case we_objectFile::TYPE_MULTIOBJECT:
				return $this->getMultiObjectTags($name);
		}
		return '';
	}

	private function getTmplTableRow($type, $name, $isField = false){
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
			<we:repeat>' . $this->getTemplTag($type, $name) . '</we:repeat>
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
	<td style="width:300px;">' . $this->getTemplTag($type, $name, $isField) . '</td>
</tr>';
		}
	}

	public function show(){
		$cmd3 = we_base_request::_(we_base_request::RAW, 'we_cmd', '', 3);
		$tmpl = new we_object_createTemplate();

		$tmpl->we_new();
		$tmpl->Filename = $this->filename;
		$tmpl->Extension = ".tmpl";

		$tmpl->setParentID($this->pid);
		$tmpl->Path = $tmpl->ParentPath . $this->filename . ".tmpl";

		$this->usedIDs = [$_SESSION['weS']['we_data'][$cmd3][0]["ID"]];

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

				$content .= $this->getTmplTableRow($type, $name);
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

					$content .= $this->getTmplTableRow($type, $name, true);
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
						we_html_button::create_button(we_html_button::SAVE, "javascript:if(document.we_form.we_" . $tmpl->Name . "_Filename.value != ''){ document.we_form.action=WE().consts.dirs.WEBEDITION_DIR+'we_cmd.php?we_cmd[0]=object_createTemplatecmd';document.we_form.submit();}else{ " . we_message_reporting::getShowMessageCall(g_l('alert', '[input_file_name]'), we_message_reporting::WE_MESSAGE_ERROR) . " }"), null, we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();")
		);

		$this->title = g_l('weClass', '[generateTemplate]');
		return parent::getPage(we_html_tools::htmlDialogLayout($tmpl->formPath(), g_l('weClass', '[generateTemplate]'), $buttons) .
						we_html_element::htmlHiddens(["SID" => $tmpl->Name,
							"we_cmd[3]" => $cmd3,
							"we_cmd[2]" => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 2)
						]));
	}

}
