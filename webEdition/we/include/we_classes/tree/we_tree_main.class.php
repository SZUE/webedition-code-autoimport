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
class we_tree_main extends we_tree_base{

	public function __construct(we_base_jsCmd $jsCmd, $frameset = '', $topFrame = '', $treeFrame = '', $cmdFrame = ''){
		parent::__construct($jsCmd, $frameset, $topFrame, $treeFrame, $cmdFrame);
		$this->autoload = false;
		$this->extraClasses = 'withFooter';
	}

	public static function getJSUpdateTreeScript($doc, $select = true, we_base_jsCmd $jsCmd = null, $asCmd = false){

		switch($doc->ContentType){
			case we_base_ContentTypes::HTML:
			case we_base_ContentTypes::WEDOCUMENT:
			case we_base_ContentTypes::OBJECT_FILE:
				$published = ($doc->Published && ($doc->Published < $doc->ModDate) ? -1 : $doc->Published);
				break;
			default:
				$published = $doc->Published;
		}

//	This is needed in SeeMode
		if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
			return '';
		}

		$hasSched = false;
		if(!empty($doc->schedArr) && is_array($doc->schedArr)){
			foreach($doc->schedArr as $sched){
				$hasSched |= $sched['active'];
			}
		}
		$main = [
			'id' => $doc->ID,
			'parentid' => $doc->ParentID,
			'text' => $doc->Text,
			'published' => $published,
			'table' => $doc->Table,
			'inschedule' => intval($hasSched),
		];
		$adv = [
			'contenttype' => $doc->ContentType,
			'isclassfolder' => (isset($doc->IsClassFolder) ? $doc->IsClassFolder : false),
			'checked' => 0,
			'typ' => ($doc->IsFolder ? "group" : "item"),
			'open' => 0,
			'disabled' => 0,
			'tooltip' => $doc->ID,
		];
		if($jsCmd){
			$jsCmd->addCmd('updateMainTree', intval($select), $main, $adv);
		} elseif($asCmd){
			return ['updateMainTree', intval($select), $main, $adv];
		} else {
			return 'we_cmd("updateMainTree",' . intval($select) . ',' . we_serialize($main, SERIALIZE_JSON) . ',' . we_serialize($adv, SERIALIZE_JSON) . ');';
		}
	}

	protected function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'main_tree.js', 'initTree();');
	}

	function getJSLoadTree(array $treeItems){
		$ret = [];

		if(is_array($treeItems)){
			foreach($treeItems as $item){
				$cur = [];
				foreach($item as $k => $v){
					$cur[strtolower($k)] = ($v === 1 || $v === 0 || $v === 'true' || $v === 'false' || is_int($v) ?
						intval($v) :
						$v);
				}
				$ret[] = $cur;
			}
		}
		return $ret;
	}

	public static function getItems($ParentID, $offset = 0, $segment = 500, $sort = false){
		//unused
	}

}
