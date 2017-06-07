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
class we_widget_sct extends we_widget_base{
	private $sc = '';

	public function __construct($curID = '', $aProps = []){
		$aCols = explode(';', $aProps ? $aProps[3] : we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0));
		$disableNew = true;
		$cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "');";
		if(we_base_permission::hasPerm("NEW_WEBEDITIONSITE")){
			if(we_base_permission::hasPerm("NO_DOCTYPE")){
				$disableNew = false;
			} else {
				$dtq = we_docTypes::getDoctypeQuery($GLOBALS['DB_WE']);
				$id = f('SELECT dt.ID FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where'] . ' LIMIT 1');
				if($id){
					$disableNew = false;
					$cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','" . $id . "')";
				} else {
					$disableNew = true;
				}
			}
		} else {
			$disableNew = true;
		}

		$disableObjects = false;
		if(defined('OBJECT_TABLE')){
			$allClasses = we_users_util::getAllowedClasses($GLOBALS['DB_WE']);
			if(empty($allClasses)){
				$disableObjects = true;
			}
		}

		$js = [];

		if(defined('FILE_TABLE') && we_base_permission::hasPerm("CAN_SEE_DOCUMENTS")){
			$js["open_document"] = "top.we_cmd('open_document');";
		}
		if(defined('FILE_TABLE') && we_base_permission::hasPerm("CAN_SEE_DOCUMENTS") && we_base_permission::hasPerm("CAN_SEE_PROPERTIES") && !$disableNew){
			$js["new_document"] = $cmdNew;
		}
		if(defined('TEMPLATES_TABLE') && we_base_permission::hasPerm("NEW_TEMPLATE") && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
			$js["new_template"] = "top.we_cmd('new','" . TEMPLATES_TABLE . "','','" . we_base_ContentTypes::TEMPLATE . "');";
		}
		if(we_base_permission::hasPerm("NEW_DOC_FOLDER")){
			$js["new_directory"] = "top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::FOLDER . "')";
		}
		if(defined('FILE_TABLE') && we_base_permission::hasPerm("CAN_SEE_DOCUMENTS")){
			$js["unpublished_pages"] = "top.we_cmd('openUnpublishedPages');";
		}
		if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm("CAN_SEE_OBJECTFILES") && !$disableObjects){
			$js["unpublished_objects"] = "top.we_cmd('openUnpublishedObjects');";
		}
		if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm("NEW_OBJECTFILE") && we_base_permission::hasPerm("CAN_SEE_PROPERTIES") && !$disableObjects){
			$js["new_object"] = "top.we_cmd('new_objectFile');";
		}
		if(defined('OBJECT_TABLE') && we_base_permission::hasPerm("NEW_OBJECT") && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
			$js["new_class"] = "top.we_cmd('new_object');";
		}
		if(we_base_permission::hasPerm("EDIT_SETTINGS")){
			$js["preferences"] = "top.we_cmd('openPreferences');";
		}
		if(we_base_permission::hasPerm('NEW_GRAFIK')){
			$js['btn_add_image'] = "top.we_cmd('new','tblFile','','image/*')";
		}


		$shortcuts = [];
		foreach($aCols as $sCol){
			$shortcuts[] = explode(',', $sCol);
		}

		$sSctOut = '';
		$col = 0;

		foreach($shortcuts as $sctCol){
			$sSctOut .= '<div class="sct_row" style="display: block; margin-right: 1em; float: left;"><table class="default" style="width:100%;">';
			$iCurrSctRow = 0;
			foreach($sctCol as $label){
				if(isset($js[$label])){
					$icon = '';
					switch($label){
						case 'new_directory':
							$icon = we_base_ContentTypes::FOLDER;
							break;
						case 'unpublished_pages':
						case 'open_document':
						case 'new_document':
							$icon = we_base_ContentTypes::WEDOCUMENT;
							break;
						case 'unpublished_objects':
						case 'new_object':
							$icon = we_base_ContentTypes::OBJECT_FILE;
							break;
						case 'new_template':
							$icon = we_base_ContentTypes::TEMPLATE;
							break;
						case 'new_class':
							$icon = we_base_ContentTypes::OBJECT;
							break;
						case 'btn_add_image':
							$icon = we_base_ContentTypes::IMAGE;
							break;
						case 'preferences':
							$icon = 'settings';
							break;
					}

					$sSctOut .= '<tr onclick="' . $js[$label] . '"><td class="sctFileIcon" data-contenttype="' . $icon . '"></td><td class="middlefont sctText">' . g_l('button', '[' . $label . '][value]') . '</tr>';
				}
				$iCurrSctRow++;
			}
			$sSctOut .= '</table></div>';
			$col++;
		}

		$this->sc = $sSctOut;
	}

	public function getInsertDiv($iCurrId, $aProps, we_base_jsCmd $jsCmd){
		$cfg = self::getDefaultConfig();
		$oTblDiv = we_html_element::htmlDiv(["id" => "m_" . $iCurrId . "_inline",
				'style' => "width:100%;height:" . ($cfg["height"] - 25) . "px;overflow:auto;"
				], $this->sc);
		$aLang = [g_l('cockpit', '[shortcuts]'), ''];
		$jsCmd->addCmd('setIconOfDocClass', 'sctFileIcon');
		return [$oTblDiv, $aLang];
	}

	public static function getDefaultConfig(){
		return
			[
				'width' => self::WIDTH_SMALL,
				'expanded' => 0,
				'height' => 210,
				'res' => 0,
				'cls' => 'red',
				'csv' => ';',
				'dlgHeight' => 435,
				'isResizable' => 1
		];
	}

	public static function showDialog(){
		list($jsFile, $oSelCls) = self::getDialogPrefs();
		$disableNew = true;
		$cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "');";
		if(we_base_permission::hasPerm("NEW_WEBEDITIONSITE")){
			if(we_base_permission::hasPerm("NO_DOCTYPE")){
				$disableNew = false;
			} else {
				$dtq = we_docTypes::getDoctypeQuery($GLOBALS['DB_WE']);
				$id = f('SELECT dt.ID FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where'] . ' LIMIT 1');

				if($id){
					$disableNew = false;
					$cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','" . $id . "')";
				} else {
					$disableNew = true;
				}
			}
		} else {
			$disableNew = true;
		}

		$disableObjects = false;
		if(defined('OBJECT_TABLE')){
			$allClasses = we_users_util::getAllowedClasses($GLOBALS['DB_WE']);
			if(empty($allClasses)){
				$disableObjects = true;
			}
		}

		$shortcuts = array_filter([
			'open_document' => (defined('FILE_TABLE') && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS') ? g_l('button', '[open_document][value]') : ''),
			'new_document' => (defined('FILE_TABLE') && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS') && !$disableNew ? g_l('button', '[new_document][value]') : ''),
			'new_template' => (defined('TEMPLATES_TABLE') && we_base_permission::hasPerm('NEW_TEMPLATE') ? g_l('button', '[new_template][value]') : ''),
			'new_directory' => (we_base_permission::hasPerm('NEW_DOC_FOLDER') ? g_l('button', '[new_directory][value]') : ''),
			'unpublished_pages' => (defined('FILE_TABLE') && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS') ? g_l('button', '[unpublished_pages][value]') : ''),
			'unpublished_objects' => (defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES') && !$disableObjects ? g_l('button', '[unpublished_objects][value]') : ''),
			'new_object' => (defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('NEW_OBJECTFILE') && !$disableObjects ? g_l('button', '[new_object][value]') : ''),
			'new_class' => (defined('OBJECT_TABLE') && we_base_permission::hasPerm('NEW_OBJECT') ? g_l('button', '[new_class][value]') : ''),
			'preferences' => (we_base_permission::hasPerm('EDIT_SETTINGS') ? g_l('button', '[preferences][value]') : ''),
			'btn_add_image' => (we_base_permission::hasPerm('NEW_GRAFIK') ? g_l('button', '[btn_add_image][alt]') : '')
		]);

		$oSctPool = new we_html_select([
			"name" => "sct_pool",
			'class' => 'defaultfont',
			"onchange" => "addBtn(_fo.list11,this.options[this.selectedIndex].text,this.options[this.selectedIndex].value,true);this.options[0].selected=true;"
			]
		);
		$oSctPool->insertOption(0, " ", "", true);
		$iCurrOpt = 1;
		foreach($shortcuts as $key => $value){
			$oSctPool->insertOption($iCurrOpt, $key, $value, true);
			$iCurrOpt++;
		}

		$oSctList11 = new we_html_select(["multiple" => "multiple",
			"name" => "list11",
			"size" => 10,
			'style' => "width:200px;",
			'class' => 'defaultfont',
			"onDblClick" => "moveSelectedOptions(this.form.list11,this.form.list21,false);"
		]);
		$oSctList21 = new we_html_select(["multiple" => "multiple",
			"name" => "list21",
			"size" => 10,
			'style' => "width:200px;",
			'class' => 'defaultfont',
			"onDblClick" => "moveSelectedOptions(this.form.list21,this.form.list11,false);"
		]);

		$oBtnDelete = we_html_button::create_button(we_html_button::DELETE, "javascript:removeOption(document.forms[0].list11);removeOption(document.forms[0].list21);", false, -1, -1, "", "", false, false);
		$oShortcutsRem = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[sct_rem]'), we_html_tools::TYPE_INFO, 420);

		$oPool = new we_html_table(["width" => 420, 'class' => 'default'], 3, 3);
		$oPool->setCol(0, 0, null, $oSctList11->getHTML());
		$oPool->setCol(0, 1, ['style' => 'text-align:center;vertical-align:middle;'], we_html_element::htmlA(["href" => "#",
				"onclick" => "moveOptionUp(document.forms[0].list11);moveOptionUp(document.forms[0].list21);return false;"
				], '<i class="fa fa-lg fa-caret-up"></i>') .
			we_html_element::htmlBr() . we_html_element::htmlBr() .
			we_html_element::htmlA(["href" => "#",
				"onclick" => "moveSelectedOptions(document.forms[0].list11,document.forms[0].list21,false);return false;"
				], '<i class="fa fa-lg fa-caret-right"></i>') .
			we_html_element::htmlBr() . we_html_element::htmlBr() .
			we_html_element::htmlA(["href" => "#",
				"onclick" => "moveSelectedOptions(document.forms[0].list21,document.forms[0].list11,false);return false;"
				], '<i class="fa fa-lg fa-caret-left"></i>') .
			we_html_element::htmlBr() . we_html_element::htmlBr() .
			we_html_element::htmlA(["href" => "#",
				"onclick" => "moveOptionDown(document.forms[0].list11);moveOptionDown(document.forms[0].list21);return false;"
				], '<i class="fa fa-lg fa-caret-down"></i>'
		));
		$oPool->setCol(0, 2, null, $oSctList21->getHTML());
		$oPool->setCol(1, 0, ["colspan" => 3, 'style' => 'text-align:center;padding-top:5px;'], $oBtnDelete);

		$content = $oShortcutsRem . we_html_element::htmlBr() . we_html_tools::htmlFormElementTable(
				$oSctPool->getHTML(), g_l('cockpit', '[select_buttons]'), "left", "defaultfont") . we_html_element::htmlBr() . $oPool->getHTML();

		$parts = [
			["headline" => "", "html" => $content,
			],
			["headline" => "", "html" => $oSelCls->getHTML(),
			]
		];

		$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();");
		$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();");
		$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
		$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

		$sTblWidget = we_html_element::jsScript(JS_DIR . 'multiIconBox.js') . we_html_multiIconBox::getHTML("sctProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[shortcuts]'));

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[shortcuts]'), '', '', $jsFile .
			we_html_element::jsScript(JS_DIR . 'widgets/sct.js', '', ['id' => 'loadVarWidget', 'data-widget' => setDynamicVar([
					'aLang' => $shortcuts,
				])]
			), we_html_element::htmlBody(
				["class" => "weDialogBody", "onload" => "init();"
				], we_html_element::htmlForm("", $sTblWidget)));
	}

	public function showPreview(){
		$jsCmd = new we_base_jsCmd();
		$jsCmd->addCmd('initPreview', [
			'id' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5),
			'type' => 'sct',
			'tb' => g_l('cockpit', '[shortcuts]'),
		]);
		$jsCmd->addCmd('setIconOfDocClass', 'sctFileIcon');
		echo we_html_tools::getHtmlTop(g_l('cockpit', '[shortcuts]'), '', '', $jsCmd->getCmds()
			, we_html_element::htmlBody(
				['style' => 'margin:10px 15px;',
				], we_html_element::htmlDiv(["id" => "sct"
					], $this->sc)));
	}

}
