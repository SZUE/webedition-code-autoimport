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
abstract class we_base_widget{
	static $json = [];

	/**
	 * To add a widget give a unique id ($iId). Currently supported widget types ($sType) are Shortcuts (sct), RSS Reader (rss),
	 * Last modified (mfd), ToDo/Messaging (msg), Users Online (usr), and Unpublished docs and objs (ubp).
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
		return we_html_element::htmlDiv(["id" => $iId . "_bx", "class" => 'widget bgc_' . $sCls . ' ' . ($w > 400 ? 'cls_expand' : 'cls_collapse')], $oTb->getHtml() .
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
