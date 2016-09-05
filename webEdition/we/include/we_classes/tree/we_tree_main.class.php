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

	function getHTMLContruct($classes = ''){
		return parent::getHTMLContruct('withFooter');
	}

	function getJSStartTree(){
		return '
var we_scrollY={};
treeData.table="' . FILE_TABLE . '";';
	}

	function getJSUpdateTreeScript($doc, $select = true){

		$published = ((($doc->Published != 0) && ($doc->Published < $doc->ModDate) && ($doc->ContentType == we_base_ContentTypes::HTML || $doc->ContentType == we_base_ContentTypes::WEDOCUMENT || $doc->ContentType === we_base_ContentTypes::OBJECT_FILE)) ? -1 : $doc->Published);

//	This is needed in SeeMode
		$s = '
var isEditInclude = false;
var weWindow = top;
while(1){
	if(!weWindow.top.opener || weWindow.treeData){
			break;
	} else {
		 isEditInclude = true;
		 weWindow = weWindow.opener.top;
	}
}';
		if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
			return $s;
		}

		$hasSched = false;
		if(!empty($doc->schedArr) && is_array($doc->schedArr)){
			foreach($doc->schedArr as $sched){
				$hasSched|=$sched['active'];
			}
		}
		$s .= '
if(weWindow.treeData){
	var obj = weWindow.treeData;' .
			($select ? '
	weWindow.treeData.selection_table="' . $doc->Table . '";
	weWindow.treeData.selection="' . $doc->ID . '";' :
				'weWindow.treeData.unselectNode();') . '
	if(weWindow.treeData.table == "' . $doc->Table . '"){
		if(weWindow.treeData[top.treeData.indexOfEntry(' . $doc->ParentID . ')]){
			var attribs={
				id:' . $doc->ID . ',
				parentid:' . $doc->ParentID . ',
				text:\'' . addcslashes($doc->Text, '\'') . '\',
				published:' . $published . ',
				table:\'' . $doc->Table . '\',
				inschedule:\'' . intval($hasSched) . '\'
			};

			var visible=(top.treeData.indexOfEntry(' . $doc->ParentID . ')!=-1?
				top.treeData[top.treeData.indexOfEntry(' . $doc->ParentID . ')].open:
					0);
			if(top.treeData.indexOfEntry(' . $doc->ID . ')!=-1){
				top.treeData.updateEntry(attribs);
			}else{
			//FIXME: makenewentry!
				attribs.contenttype=\'' . $doc->ContentType . '\';
				attribs.isclassfolder=\'' . (isset($doc->IsClassFolder) ? $doc->IsClassFolder : false) . '\';
				attribs.checked=0;
				attribs.typ=\'' . ($doc->IsFolder ? "group" : "item") . '\';
				attribs.open=0;
				attribs.disabled=0;
				attribs.tooltip=' . $doc->ID . ';
				top.treeData.addSort(new top.node(attribs));
			}
			weWindow.drawTree();
		}else if(top.treeData.indexOfEntry(' . $doc->ID . ')!=-1){
			top.treeData.deleteEntry(' . $doc->ID . ');
		}
	}
}';
		return $s;
	}

	protected function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'main_tree.js');
	}

	function getJSLoadTree($clear, array $treeItems){
		$js = '';

		if(is_array($treeItems)){
			foreach($treeItems as $item){
				$js.= ($clear ? '' : 'if(top.treeData.indexOfEntry("' . $item['id'] . '")<0){') .
					'top.treeData.add(new top.node({';
				foreach($item as $k => $v){
					$js.= strtolower($k) . ':' . ($v === 1 || $v === 0 || is_bool($v) || $v === 'true' || $v === 'false' || is_int($v) ?
							intval($v) :
							'\'' . str_replace(array('"', '\'', '\\'), '', $v) . '\'') . ',';
				}
				$js.='}));' . ($clear ? '' : '}');
			}
		}
		$js.= 'top.drawTree();';

		return $js;
	}

}
