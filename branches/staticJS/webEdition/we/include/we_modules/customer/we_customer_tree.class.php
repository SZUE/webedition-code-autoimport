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
class we_customer_tree extends weTree{

	function getJSCustomDraw(){
		return array(
			"sort" => 'row+=drawCustomerSort(nf, ai,zweigEintrag);',
			"group" => 'row+=drawCustomerGroup(nf, ai,zweigEintrag);'
		);
	}

	function customJSFile(){
		return we_html_element::jsScript(WE_JS_CUSTOMER_MODULE_DIR . 'customer_tree.js');
	}

	function getJSOpenClose(){
		return '';
	}

	function getJSUpdateItem(){
		return '';
	}

	function getJSTreeFunctions(){
		return parent::getJSTreeFunctions() . '
';
	}

	function getJSStartTree(){
		return '
function startTree(){
	frames={
	"top":' . $this->topFrame . ',
	"cmd":' . $this->cmdFrame . '
};
treeData.frames=frames;
	treeData.frames=frames;
	frames.cmd.location=treeData.frameset+"?pnt=cmd&pid=0";
	drawTree();
}';
	}

	function getJSLoadTree($rootID, $treeItems){
		$days = array(
			'Sunday' => 0,
			'Monday' => 1,
			'Tuesday' => 2,
			'Wednesday' => 3,
			'Thursday' => 4,
			'Friday' => 5,
			'Saturday' => 6
		);

		$months = array(
			'January' => 0,
			'February' => 1,
			'March' => 2,
			'April' => 3,
			'May' => 4,
			'June' => 5,
			'July' => 6,
			'August' => 7,
			'September' => 8,
			'October' => 9,
			'November' => 10,
			'December' => 11
		);

		$js = (!$rootID ?
				$this->topFrame . '.treeData.clear();' .
				$this->topFrame . '.treeData.add(new ' . $this->topFrame . '.rootEntry(\'' . $rootID . '\',\'root\',\'root\'));' : '') .
			'var attribs={};';
		foreach($treeItems as $item){
			$js.='if(' . $this->topFrame . '.indexOfEntry(\'' . str_replace(array("\n", "\r", '\''), '', $item["id"]) . '\')<0){' .
				'attribs={';

			foreach($item as $k => $v){
				if($k === 'text'){
					if(in_array($v, array_keys($days))){
						$v = g_l('date', '[day][long][' . $days[$v] . ']');
					}
					if(in_array($v, array_keys($months))){
						$v = g_l('date', '[month][long][' . $months[$v] . ']');
					}
				}
				$js.='"' . strtolower($k) . '":\'' . addslashes(stripslashes(str_replace(array("\n", "\r", '\''), '', $v))) . '\',';
			}

			$js.='};' .
				$this->topFrame . '.treeData.add(new ' . $this->topFrame . '.node(attribs));
				}';
		}
		$js.=$this->topFrame . '.drawTree();';

		return $js;
	}

	function getJSShowSegment(){
		return '';
	}

	function getJSGetLayout(){
		return '';
	}

}
