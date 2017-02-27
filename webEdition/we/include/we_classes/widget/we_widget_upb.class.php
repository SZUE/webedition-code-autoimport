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
class we_widget_upb extends we_widget_base{
	private $ct = '';
	private $bTypeDoc = false;
	private $bTypeObj = false;

	public function __construct($curID = '', $aProps = []){
		$aProps = $aProps ? $aProps : [
			0,
			0,
			0,
			we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)
		];

// widget UNPUBLISHED
		$this->bTypeDoc = (bool) $aProps[3]{0};
		$this->bTypeObj = (bool) $aProps[3]{1};
		$objectFilesTable = defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : "";
		$numRows = 25;

		$tbls = [];
		if($this->bTypeDoc && $this->bTypeObj){
			if(defined('FILE_TABLE')){
				$tbls[] = FILE_TABLE;
			}
			if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES')){
				$tbls[] = OBJECT_FILES_TABLE;
			}
		} else {
			if($this->bTypeDoc && defined('FILE_TABLE')){
				$tbls[] = FILE_TABLE;
			}
			if($this->bTypeObj && defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES')){
				$tbls[] = OBJECT_FILES_TABLE;
			}
		}

		$cont = [];
		$db = $GLOBALS['DB_WE'];
		foreach($tbls as $table){
			if(defined('WORKFLOW_TABLE')){
				$myWfDocsArray = we_workflow_utility::getWorkflowDocsForUser($_SESSION['user']['ID'], $table, we_base_permission::hasPerm('ADMINISTRATOR'), we_base_permission::hasPerm("PUBLISH"), ($table == $objectFilesTable) ? '' : get_ws($table));
				$myWfDocsCSV = implode(',', $myWfDocsArray);
				$wfDocsArray = we_workflow_utility::getAllWorkflowDocs($table, $db);
				$wfDocsCSV = implode(',', $wfDocsArray);
			} else {
				$wfDocsCSV = $myWfDocsCSV = '';
			}

			$offset = we_base_request::_(we_base_request::INT, 'offset', 0);
			$order = we_base_request::_(we_base_request::STRING, 'order', "ModDate DESC");

			#### get workspace query ###


			$parents = $childs = [];
			$parentlist = $childlist = '';

			if($table == FILE_TABLE){
				if(($wsArr = get_ws($table, true))){
					foreach($wsArr as $i){
						$parents[] = $i;
						$childs[] = $i;
						we_readParents($i, $parents, $table, 'ContentType', we_base_ContentTypes::FOLDER, $db);
						we_readChilds($i, $childs, $table, true, '', 'ContentType', we_base_ContentTypes::FOLDER, $db);
					}
					$childlist = implode(',', $childs);
					$parentlist = implode(',', $parents);

					$wsQuery = ($parentlist ? ' t.ID IN(' . $parentlist . ') ' . ($childlist ? ' OR ' : '') : '') .
						($childlist ? ' t.ParentID IN(' . $childlist . ') ' : '');
				}
			}

			#####
			$sqld = g_l('weEditorInfo', '[mysql_date_only_format]');


			$s = 'SELECT ' . ($wfDocsCSV ? '(t.ID IN(' . $wfDocsCSV . ')) AS wforder,' : '') . ' ' . ($myWfDocsCSV ? '(t.ID IN(' . $myWfDocsCSV . ')) AS mywforder,' : '') . ' '
				. 't.ContentType,t.ID,t.Text,t.ParentID,t.Path,t.ModDate,'
				. 'IF(t.Published>0,FROM_UNIXTIME(t.Published,"' . $sqld . '"),"-") AS Published,'
				. 'IF(t.ModDate>0,FROM_UNIXTIME(t.ModDate,"' . $sqld . '"),"-") AS Modified,'
				. 'IF(t.CreationDate>0,FROM_UNIXTIME(t.CreationDate,"' . $sqld . '"),"-") AS CreationDate,'
				. 'u2.username AS Modifier,'
				. 'u1.username AS Creator ';
			$q = 'FROM ' . $db->escape($table) . ' t LEFT JOIN ' . USER_TABLE . ' u1 ON u1.ID=t.CreatorID LEFT JOIN ' . USER_TABLE . ' u2 ON u2.ID=t.ModifierID ' .
				" WHERE (((t.Published=0 OR t.Published<t.ModDate) AND t.ContentType IN ('" . we_base_ContentTypes::WEDOCUMENT . "','" . we_base_ContentTypes::HTML . "','" . we_base_ContentTypes::OBJECT_FILE . "'))" .
				($myWfDocsCSV ? ' OR (t.ID IN(' . $myWfDocsCSV . ')) ' : '') . ')' .
				(isset($wsQuery) ? ' AND (' . $wsQuery . ') ' : '');
			$order = ' ORDER BY ' . ($myWfDocsCSV ? 'mywforder DESC,' : '') . $order;

			$anz = f('SELECT COUNT(1) ' . $q, '', $db);

			$db->query($s . $q . $order . ' LIMIT ' . intval($offset) . ',' . intval($numRows));
			$content = [];

			while($db->next_record()){
				$cont[$db->f("ModDate")] = $path = '<tr><td class="upbIcon" data-contenttype="' . $db->f('ContentType') . '"></td><td class="upbEntry middlefont ' . ($db->f("Published") != '-' ? 'changed' : "notpublished") . '" onclick="WE().layout.weEditorFrameController.openDocument(\'' . $table . '\',' . $db->f("ID") . ',\'' . $db->f("ContentType") . '\')" title="' . $db->f("Path") . '">' . $db->f("Path") . '</td></tr>';
				$row = [
					['dat' => $path],
				];
				if(defined('WORKFLOW_TABLE')){
					if($db->f("wforder")){
						$step = we_workflow_utility::findLastActiveStep($db->f("ID"), $table) + 1;
						$steps = count(we_workflow_utility::getNumberOfSteps($db->f("ID"), $table));
						$row[] = ['dat' => $step . '&nbsp;' . g_l('resave', '[of]') . '&nbsp;' . $steps . '&nbsp;<i class="fa fa-lg fa-circle" style="color:#' . ($db->f("mywforder") ? '006DB8' : 'E7E7E7') . ';"></i>'];
					} else {
						$row[] = ['dat' => "-"];
					}
				}
				$content[] = $row;
			}
		}

		asort($cont);
		$this->ct = '<table class="default">' . implode('', $cont) . '</table>';
	}

	public function getInsertDiv($iCurrId, we_base_jsCmd $jsCmd){
		$cfg = self::getDefaultConfig();
		$oTblDiv = we_html_element::htmlDiv(["id" => "m_" . $iCurrId . "_inline",
				'style' => "height:" . ($cfg["height"] - 25) . "px;overflow:auto;"
				], $this->ct);

		$sTb = g_l('cockpit', ($this->bTypeDoc && $this->bTypeObj ? '[upb_docs_and_objs]' : ($this->bTypeDoc ? '[upb_docs]' : ($this->bTypeObj ? '[upb_objs]' : '[upb_docs_and_objs]'))));

		$jsCmd->addCmd('setIconOfDocClass', 'upbIcon');

		return [$oTblDiv, [$sTb, ""]];
	}

	public static function getDefaultConfig(){
		$shortCuts = (defined('FILE_TABLE') && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS') ? '1' : '0') .
			(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES') ? '1' : '0');

		return [
			'width' => self::WIDTH_SMALL,
			'expanded' => 0,
			'height' => 210,
			'res' => 0,
			'cls' => 'lightCyan',
			'csv' => $shortCuts,
			'dlgHeight' => 190,
			'isResizable' => 1
		];
	}

	public static function showDialog(){
		list($jsFile, $oSelCls) = self::getDialogPrefs();

		$oChbxDocs = we_html_forms::checkbox(0, true, "chbx_type", g_l('cockpit', '[documents]'), true, "defaultfont", "", false, "", 0, 0);
		$oChbxObjs = we_html_forms::checkbox(0, true, "chbx_type", g_l('cockpit', '[objects]'), true, "defaultfont", "", false, "", 0, 0);

		$dbTableType = '<table><tr>' .
			(defined('FILE_TABLE') ? '<td>' . $oChbxDocs . ' </td>' : '') .
			(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm("CAN_SEE_OBJECTFILES") ? "<td>" . $oChbxObjs . "</td>" : '') .
			"</tr></table>";

		$parts = [["headline" => g_l('cockpit', '[type]'), "html" => $dbTableType, 'space' => we_html_multiIconBox::SPACE_MED],
			["headline" => "", "html" => $oSelCls->getHTML(),],
		];

		$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();");
		$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();");
		$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
		$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

		$sTblWidget = we_html_multiIconBox::getHTML("mfdProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[unpublished]'));

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[unpublished]'), '', '', $jsFile .
			we_html_element::jsScript(JS_DIR . 'widgets/upb.js')
			, we_html_element::htmlBody(
				['class' => "weDialogBody", "onload" => "init();"], we_html_element::htmlForm("", $sTblWidget)));
	}

	public function showPreview(){
		echo we_html_tools::getHtmlTop(g_l('cockpit', '[unpublished]'), '', '', we_html_element::jsScript(JS_DIR . 'widgets/preview.js', '', [
				'id' => 'loadVarPreview',
				'data-preview' => setDynamicVar([
					'id' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5),
					'type' => 'upb',
					'tb' => g_l('cockpit', ($this->bTypeDoc && $this->bTypeObj ? '[upb_docs_and_objs]' : ($this->bTypeDoc ? '[upb_docs]' : ($this->bTypeObj ? '[upb_objs]' : '[upb_docs_and_objs]')))),
					'iconClass' => 'upbIcon'
			])]), we_html_element::htmlBody(['style' => 'margin:10px 15px;',
				"onload" => 'if(parent!=self){init();}'
				], we_html_element::htmlDiv(["id" => "upb"
					], $this->ct)));
	}

}
