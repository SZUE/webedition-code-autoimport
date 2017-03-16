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
class we_widget_mdc extends we_widget_base{
	private $mdc = '';
	private $binary = '';
	private $splitMdc = [];

	//FIXME: $aProps
	public function __construct($curID = '', $aProps = []){
		$DB_WE = $GLOBALS['DB_WE'];
		$this->mdc = "";
		if(!$curID){//preview requested
			$this->splitMdc = [
				0, //unused - compatibility
				we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
				we_base_request::_(we_base_request::INTLIST, 'we_cmd', '', 1)
			];
		} else {
			$this->splitMdc = explode(';', $aProps[3]);
		}

		if($this->splitMdc && count($this->splitMdc) == 3){
			$this->binary = $this->splitMdc[1];
			$csv = $this->splitMdc[2];
			$table = ($this->binary{1}) ? OBJECT_FILES_TABLE : FILE_TABLE;
		} else {
			$csv = '';
		}

		if($csv){
			if($this->binary{0}){
				$ids = explode(',', $csv);
				$paths = id_to_path($ids, $table, null, true);
				$where = [];
				foreach($paths as $path){
					$where[] = 'Path LIKE "' . $path . '%" ';
				}
				$query = ($where ?
					'SELECT ID,Path,Text,ContentType FROM ' . $GLOBALS['DB_WE']->escape($table) . ' WHERE (' . implode(' OR ', $where) . ') AND IsFolder=0' :
					false);
			} else {
				list($folderID, $folderPath) = explode(",", $csv);
				$q_path = 'Path LIKE "' . $folderPath . '%"';
				$q_dtTid = ($this->splitMdc[3] != 0) ? (!$this->binary{1} ? 'DocType' : 'TableID') . '="' . $this->splitMdc[3] . '"' : '';
				if($this->splitMdc[4] != ""){
					$cats = explode(",", $this->splitMdc[4]);
					$categories = [];
					foreach($cats as $myCat){
						$id = f('SELECT ID FROM ' . CATEGORY_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape(base64_decode($myCat)) . '"', 'ID', $GLOBALS['DB_WE']);
						$categories[] = 'Category LIKE ",' . intval($id) . ',"';
					}
				}
				$query = 'SELECT ID,Path,Text,ContentType FROM ' . $GLOBALS['DB_WE']->escape($table) . ' WHERE ' . $q_path . (($q_dtTid) ? ' AND ' . $q_dtTid : '') . ((isset(
						$categories)) ? ' AND (' . implode(' OR ', $categories) . ')' : '') . ' AND IsFolder=0;';
			}

			if($query && $DB_WE->query($query)){
				$this->mdc .= '<table class="default">';
				while($DB_WE->next_record()){
					$this->mdc .= '<tr><td class="mdcIcon" data-contenttype="' . $DB_WE->f('ContentType') . '"></td><td style="vertical-align:middle" class="middlefont">' . we_html_element::htmlA([
							"href" => "javascript:WE().layout.weEditorFrameController.openDocument('" . $table . "','" . $DB_WE->f('ID') . "','" . $DB_WE->f('ContentType') . "');",
							"title" => $DB_WE->f("Path"),
							'style' => "color:#000000;text-decoration:none;"
							], $DB_WE->f("Path")) . '</td></tr>';
				}
				$this->mdc .= '</table>';
			}
		}
	}

	public function getInsertDiv($iCurrId, we_base_jsCmd $jsCmd){
		$cfg = self::getDefaultConfig();
		$oTblDiv = we_html_element::htmlDiv(["id" => "m_" . $iCurrId . "_inline",
				'style' => "width:100%;height:" . ($cfg["height"] - 25) . "px;overflow:auto;"
				], $this->mdc);
		$aLang = [($this->splitMdc[0]) ? base64_decode($this->splitMdc[0]) : g_l('cockpit', (empty($this->splitMdc[1][1]) ? '[my_documents]' : '[my_objects]')), ""];
		$jsCmd->addCmd('setIconOfDocClass', 'mdcIcon');

		return [$oTblDiv, $aLang];
	}

	public static function getDefaultConfig(){
		return [
			'width' => self::WIDTH_SMALL,
			'expanded' => 0,
			'height' => 307,
			'res' => 0,
			'cls' => 'white',
			'csv' => ';10;',
			'dlgHeight' => 450,
			'isResizable' => 1
		];
	}

	private static function getHTMLDirSelector($selType){
		global $showAC;
		$showAC = true;
		$rootDirID = 0;
		$folderID = 0;
		$button_doc = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.FolderID.value,'" . FILE_TABLE . "','FolderID','FolderPath','','','" . $rootDirID . "')");
		$button_obj = defined('OBJECT_TABLE') ? we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.FolderID.value,'" . OBJECT_FILES_TABLE . "','FolderID','FolderPath','','','" . $rootDirID . "')") : '';

		$buttons = '<div id="docFolder" style="display: ' . (!$selType ? "inline" : "none") . '">' . $button_doc . "</div>" . '<div id="objFolder" style="display: ' . ($selType ? "inline" : "none") . '">' . $button_obj . "</div>";
		$path = id_to_path($folderID, (!$selType ? FILE_TABLE : (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : "")));

		//FIXME: autocompleter?!

		return we_html_element::htmlDiv(['style' => "margin-top:10px;"
				], we_html_tools::htmlFormElementTable(
					we_html_tools::htmlTextInput("FolderPath", 58, $path, "", 'onchange="" id="yuiAcInputDoc"', "text", (420 - 120), 0), g_l('cockpit', '[dir]'), "left", "defaultfont", we_html_element::htmlHidden("FolderID", $folderID, "yuiAcIdDoc"), $buttons));
	}

	private static function getHTMLCategory(&$widgetData){
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category',0,'" . CATEGORY_TABLE . "','','','add_cat')", '', 0, 0, "", "", (!we_base_permission::hasPerm("EDIT_KATEGORIE")));
		$del_but = we_html_button::create_button(we_html_button::TRASH, 'javascript:#####placeHolder#####;top.mark();');
		$widgetData['cats'] = [
			'del' => $del_but,
			'items' => []
		];

		$table = new we_html_table(['id' => 'CategoriesBlock',
			'style' => 'display: block;margin-top: 5px;',
			'class' => 'default'
			], 5, 1);

		$table->setCol(1, 0, ['class' => 'defaultfont'], "Kategorien");
		$table->setColContent(2, 0, we_html_element::htmlDiv(['id' => 'categories',
				'class' => 'blockWrapper',
				'style' => 'width:420px;height:60px;border:#AAAAAA solid 1px;margin-bottom:5px;'
		]));

		$table->setCol(4, 0, ['colspan' => 2, 'style' => 'text-align:right'], we_html_button::create_button(we_html_button::DELETE_ALL, 'javascript:removeAllCats()') . $addbut);

		return $table->getHtml();
	}

	public static function showDialog(){
		$DB_WE = $GLOBALS['DB_WE'];
		list($jsFile, $oSelCls) = self::getDialogPrefs();
		$jsCmd = new we_base_jsCmd();
		$widgetData = [];

		list($sTitle, $selBinary, $sCsv) = explode(";", we_base_request::_(we_base_request::STRING, 'we_cmd', ';;', 1));
		$title = base64_decode($sTitle);
		$selection = (bool) $selBinary{0};
		$selType = (bool) $selBinary{1};

		if($selection){
			$_SESSION['weS']['exportVars_session'][($selType ? "selObjs" : "selDocs")] = $sCsv;
		}

		$selTable = ($selType && defined('OBJECT_FILES_TABLE')) ? OBJECT_FILES_TABLE : FILE_TABLE;
		$docTypes = [0 => g_l('cockpit', '[no_entry]')];

		$dtq = we_docTypes::getDoctypeQuery($DB_WE);
		$DB_WE->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
		while($DB_WE->next_record()){
			$docTypes[$DB_WE->f("ID")] = $DB_WE->f("DocType");
		}
		$doctypeElement = we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect("DocTypeID", $docTypes, 1, 0, false, ['onchange' => "", 'style' => "width:420px; border: #AAAAAA solid 1px;"], 'value'), g_l('cockpit', '[doctype]'));

		$cls = new we_html_select(["name" => "classID",
			'class' => 'defaultfont',
			'style' => "width:420px; border: #AAAAAA solid 1px"
		]);
		$optid = 0;
		$cls->insertOption($optid, 0, g_l('cockpit', '[no_entry]'));
		$ac = implode(',', we_users_util::getAllowedClasses($DB_WE));
		if($ac){
			$DB_WE->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' ' . ($ac ? ' WHERE ID IN(' . $ac . ') ' : '') . 'ORDER BY Text');
			while($DB_WE->next_record()){
				$optid++;
				$cls->insertOption($optid, $DB_WE->f("ID"), $DB_WE->f("Text"));
				if($DB_WE->f("ID") == -1){
					$cls->selectOption($DB_WE->f("ID"));
				}
			}
		}


		$seltype = ['doctype' => g_l('cockpit', '[documents]')];
		if(defined('OBJECT_TABLE')){
			$seltype['classname'] = g_l('cockpit', '[objects]');
		}

		$tree = new we_export_tree($jsCmd, 'top', 'top', 'cmd');

		$captions = [];
		if(we_base_permission::hasPerm("CAN_SEE_DOCUMENTS")){
			$captions[FILE_TABLE] = g_l('export', '[documents]');
		}
		if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm("CAN_SEE_OBJECTFILES")){
			$captions[OBJECT_FILES_TABLE] = g_l('export', '[objects]');
		}


		$divContent = we_html_element::htmlDiv(['style' => "display:block;"
				], we_html_tools::htmlSelect("Selection", ["dynamic" => g_l('cockpit', '[dyn_selection]'), "static" => g_l('cockpit', '[stat_selection]')
					], 1, ($selection ? "static" : "dynamic"), false, ['style' => "width:420px;border:#AAAAAA solid 1px;", 'onchange' => "closeAllSelection();we_submit();"], 'value') .
				we_html_element::htmlBr() .
				we_html_tools::htmlSelect("headerSwitch", $captions, 1, (!$selType ? FILE_TABLE : OBJECT_FILES_TABLE), false, ['style' => "width:420px;border:#AAAAAA solid 1px;margin-top:10px;",
					'onchange' => "setHead(this.value);we_submit();"], 'value', 420) .
				we_html_element::htmlDiv(["id" => "static", 'style' => ($selection ? "display:block;" : "display:none;")], we_html_element::htmlDiv(["id" => "treeContainer"], $tree->getHTMLMultiExplorer(420, 180, false)) . '<iframe name="cmd" src="about:blank" style="visibility:hidden; width: 0px; height: 0px;"></iframe>') .
				we_html_element::htmlDiv(["id" => "dynamic", 'style' => (!$selection ? 'display:block;' : 'display:none;')
					], self::getHTMLDirSelector($selType) . we_html_element::htmlBr() . ((!$selType) ? $doctypeElement : we_html_tools::htmlFormElementTable(
						$cls->getHTML(), g_l('cockpit', '[class]'))) . we_html_element::htmlBr() . self::getHTMLCategory($widgetData)) .
				we_html_element::htmlBr() .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("title", 55, $title, 255, "", "text", 420, 0), g_l('cockpit', '[title]'), "left", "defaultfont"));

		$parts = [
			["headline" => "",
				"html" => $divContent,
			],
			["headline" => "",
				"html" => $oSelCls->getHTML(),
			]
		];

		$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();");
		$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();");
		$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
		$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

		$sTblWidget = we_html_multiIconBox::getHTML("mdcProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[my_documents]'));

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[my_documents]'), '', '', $jsFile .
			we_html_element::jsScript(JS_DIR . 'widgets/mdc.js', '', ['id' => 'loadVarWidget', 'data-widget' => setDynamicVar($widgetData)]) .
			$jsCmd->getCmds(), we_html_element::htmlBody(
				["class" => "weDialogBody", "onload" => "init('" . $selTable . "','" . $sTitle . "','" . $selBinary . "','" . $sCsv . "');"
				], we_html_element::htmlForm(
					"", we_html_element::htmlHiddens(["table" => "",
						"FolderID" => 0,
						"CategoriesControl" => we_base_request::_(we_base_request::INT, 'CategoriesControl', 0)
					]) . $sTblWidget)));
	}

	public function showPreview(){

		$cmd4 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 4);
		$jsCmd = new we_base_jsCmd();
		$jsCmd->addCmd('initPreview', [
			'id' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5),
			'type' => 'mdc',
			'tb' => ($cmd4 ?: g_l('cockpit', (($this->binary{1} ? '[my_objects]' : '[my_documents]')))),
		]);
		$jsCmd->addCmd('setIconOfDocClass', 'mdcIcon');
		echo we_html_tools::getHtmlTop(g_l('cockpit', '[my_documents]'), '', '', $jsCmd->getCmds(), we_html_element::htmlBody(
				[
				'style' => 'margin:10px 15px;',
				], we_html_element::htmlDiv([
					"id" => "mdc"
					], $this->mdc)));
	}

}
