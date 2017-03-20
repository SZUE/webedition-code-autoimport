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

/**
 * Class we_widget()
 *
 * Use this class to add a widget to the Cockpit.
 */
abstract class we_widget_base{
	const WIDTH_SMALL = 202;
	const WIDTH_LARGE = 432;

	static $json = [];

	abstract public function __construct($curID = '', $aProps = []);

	abstract public function getInsertDiv($iCurrId, we_base_jsCmd $jsCmd);

	abstract public static function getDefaultConfig();

	abstract public static function showDialog();

	abstract public function showPreview();

	static function getDialogPrefs(array $dynVar = []){
		$dynVar['_sObjId'] = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
		$jsFile = we_html_element::jsScript(JS_DIR . 'widgets/dlg_prefs.js', '', ['id' => 'loadVarDlg_prefs', 'data-prefs' => setDynamicVar($dynVar)]);

		$oSctCls = new we_html_select([
			"name" => "sct_cls",
			'class' => 'defaultfont',
			'style' => "width:120px;border:#AAAAAA solid 1px"
		]);
		$oSctCls->insertOption(0, "white", g_l('cockpit', '[white]'));
		$oSctCls->insertOption(1, "lightCyan", g_l('cockpit', '[lightcyan]'));
		$oSctCls->insertOption(2, "blue", g_l('cockpit', '[blue]'));
		$oSctCls->insertOption(3, "green", g_l('cockpit', '[green]'));
		$oSctCls->insertOption(4, "orange", g_l('cockpit', '[orange]'));
		$oSctCls->insertOption(5, "yellow", g_l('cockpit', '[yellow]'));
		$oSctCls->insertOption(6, "red", g_l('cockpit', '[red]'));

		$oSelCls = new we_html_table(['class' => 'default'], 1, 2);
		$oSelCls->setCol(0, 0, ["width" => 130, 'class' => 'defaultfont'], g_l('cockpit', '[bgcolor]'));
		$oSelCls->setCol(0, 1, null, $oSctCls->getHTML());

		return [$jsFile, $oSelCls, $oSctCls->getHTML()];
	}

	/**
	 * To add a widget give a unique id ($iId). Currently supported widget types ($sType) are Shortcuts (sct), RSS Reader (rss),
	 * Last modified (mfd), Users Online (usr), and Unpublished docs and objs (ubp).
	 *
	 * @param      int $iId
	 * @param      string $sType
	 * @param      object $oContent
	 * @param      array $aLabel
	 * @param      string $sCls
	 * @param      int $iRes
	 * @param      string $sCsv
	 * @param      int $w
	 * @param      int $h
	 * @param      bool $resize
	 * @return     object Returns the we_html_table object
	 */
	static function create($iId, $sType, $oContent, $aLabel = ['', ''], $sCls = "white", $iRes = 0, $sCsv = "", $w = 0, $h = 0, $resize = true){
		$oIco_prc = new we_html_table([], 1, 3);
		$oIco_prc->setCol(0, 0, [], '<span class="fa-stack" title="' . g_l('cockpit', '[properties]') . '" onclick="propsWidget(\'' . $sType . '\',\'' . $iId . '\',document.getElementById(\'' . $iId . '_csv\').value);this.blur();">
		  <i class="fa fa-align-justify"></i>
		  </span>'
		);
		$oIco_prc->setCol(0, 1, [], '<span id="' . $iId . '_icon_resize" class="fa-stack" title="' . g_l('cockpit', ($iRes == 0 ? '[increase_size]' : '[reduce_size]')) . '" onclick="resizeWidget(\'' . $iId . '\');this.blur();">
		  <i class="fa fa-expand"></i>
		  </span>'
		);
		$oIco_prc->setCol(0, 2, [], '<span title="' . g_l('cockpit', '[close]') . '" onclick="removeWidget(\'' . $iId . '\');this.blur();">
		  <i class="fa fa-close"></i>
		  </span>');
		$oIco_pc = new we_html_table([], 1, 2);
		$oIco_pc->setCol(0, 0, [], '<span title="' . g_l('cockpit', '[properties]') . '" onclick="propsWidget(\'' . $sType . '\',\'' . $iId . '\',document.getElementById(\'' . $iId . '_csv\').value);this.blur();">
		  <i class="fa fa-align-justify"></i>
		  </span>');
		$oIco_pc->setCol(0, 1, [], '<span title="' . g_l('cockpit', '[close]') . '" onclick="removeWidget(\'' . $iId . '\');this.blur();">
		  <i class="fa fa-close"></i>
		  </span>');

		$sIco = ($sType != "_reCloneType_") ? ($resize ? $oIco_prc->getHtml() : $oIco_pc->getHtml()) :
			we_html_element::htmlDiv(["id" => $iId . "_ico_prc", 'style' => "display:block;"], $oIco_prc->getHtml()) .
			we_html_element::htmlDiv(["id" => $iId . "_ico_pc", 'style' => "display:none;"], $oIco_pc->getHtml());

		$oTb = new we_html_table(["id" => $iId . "_tb", 'class' => 'widget_controls'], 1, 2);
		$oTb->setCol(0, 0, ['id' => $iId . "_h", 'class' => 'dragBar'], '');
		$oTb->setColContent(0, 1, $sIco);

		if($iId != 'clone'){
			self::$json[] = [
				$iId,
				str_replace("'", "\'", $aLabel[0]),
				str_replace("'", "\'", $aLabel[1])
			];
		}
		return we_html_element::htmlDiv(["id" => $iId . "_bx", "class" => 'widget bgc_' . $sCls . ' ' . ($w > self::WIDTH_SMALL ? 'cls_expand' : 'cls_collapse')], $oTb->getHtml() .
				we_html_element::htmlDiv(["id" => $iId . "_lbl", "class" => "label widgetTitle",]) .
				we_html_element::htmlDiv(["id" => $iId . "_wrapper", "class" => "content"], we_html_element::htmlDiv(["id" => $iId . "_content"], $oContent) .
					we_html_element::htmlHidden($iId . '_prefix', $aLabel[0], $iId . '_prefix') .
					we_html_element::htmlHidden($iId . '_postfix', $aLabel[1], $iId . '_postfix') .
					we_html_element::htmlHidden($iId . '_res', $iRes, $iId . '_res') .
					we_html_element::htmlHidden($iId . '_type', $sType, $iId . '_type') .
					we_html_element::htmlHidden($iId . '_cls', $sCls, $iId . '_cls') .
					we_html_element::htmlHidden($iId . '_csv', $sCsv, $iId . '_csv')
				)
		);
	}

	public static function getJson(){
		return self::$json;
	}

}
