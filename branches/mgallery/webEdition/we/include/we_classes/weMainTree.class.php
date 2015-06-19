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
class weMainTree extends weTree{

	function getJSStartTree(){
		return '
we_scrollY["' . FILE_TABLE . '"] = 0;
we_scrollY["' . TEMPLATES_TABLE . '"] = 0;' .
			(defined('OBJECT_TABLE') ? '
we_scrollY["' . OBJECT_TABLE . '"] = 0;
we_scrollY["' . OBJECT_FILES_TABLE . '"] = 0;' :
				'') . '
treeData.table="' . FILE_TABLE . '";';
	}

	function getJSUpdateTreeScript($doc, $select = true){

		$published = ((($doc->Published != 0) && ($doc->Published < $doc->ModDate) && ($doc->ContentType == we_base_ContentTypes::HTML || $doc->ContentType == we_base_ContentTypes::WEDOCUMENT || $doc->ContentType === we_base_ContentTypes::OBJECT_FILE)) ? -1 : $doc->Published);

//	This is needed in SeeMode
		$s = '
isEditInclude = false;
weWindow = top;
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
		foreach($doc->schedArr as $sched){
			$hasSched|=$sched['active'];
		}
		$s .= '
if(weWindow.treeData){
	var obj = weWindow.treeData;
	var isIn = false;' .
			($select ? '
	weWindow.treeData.selection_table="' . $doc->Table . '";
	weWindow.treeData.selection="' . $doc->ID . '";' :
				'weWindow.treeData.unselectnode();') . '
	if(weWindow.treeData.table == "' . $doc->Table . '"){
		if(weWindow.treeData[top.indexOfEntry(' . $doc->ParentID . ')]){
				var attribs={
				"id":' . $doc->ID . ',
				"parentid":' . $doc->ParentID . ',
				"text":\'' . $doc->Text . '\',
				"published":' . $published . ',
				"table":\'' . $doc->Table . '\',
				"inschedule":\'' . intval($hasSched) . '\'
				};

				var visible=(' . $this->topFrame . '.indexOfEntry(' . $doc->ParentID . ')!=-1?
					' . $this->topFrame . '.treeData[' . $this->topFrame . '.indexOfEntry(' . $doc->ParentID . ')].open:
						0);

				if(' . $this->topFrame . '.indexOfEntry(' . $doc->ID . ')!=-1){
						isIn=true;
						var ai = 1;
						var elem;
						while (ai <= ' . $this->topFrame . '.treeData.len) {
							elem=' . $this->topFrame . '.treeData[ai];
							if (elem.id==attribs["id"]){
								elem.text=attribs["text"];
								elem.parentid=attribs["parentid"];
								elem.table=attribs["table"];
								elem.published=attribs["published"];
								elem.inschedule=attribs["inschedule"];
							}
							++ai;
						}
			}else{
				attribs["contenttype"]=\'' . $doc->ContentType . '\';
				attribs["isclassfolder"]=\'' . (isset($doc->IsClassFolder) ? $doc->IsClassFolder : false) . '\';
				attribs["checked"]=\'0\';
				attribs["typ"]=\'' . ($doc->IsFolder ? "group" : "item") . '\';
				attribs["open"]=\'0\';
				attribs["disabled"]=\'0\';
				attribs["tooltip"]=\'' . $doc->ID . '\';

				' . $this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));
		}
		weWindow.drawTree();
	}else if(' . $this->topFrame . '.indexOfEntry(' . $doc->ID . ')!=-1){
		' . $this->topFrame . '.deleteEntry(' . $doc->ID . ');
	}
}
}';


		return $s;
	}

	function customJSFile(){
		return parent::customJSFile() . we_html_element::jsScript(JS_DIR . 'main_tree.js');
	}

	function getJSLoadTree(array $treeItems){
		$js = 'var attribs;';

		if(is_array($treeItems)){
			foreach($treeItems as $item){
				$js.= 'if(' . $this->topFrame . ".indexOfEntry('" . $item["id"] . "')<0){"
					. "attribs={";
				foreach($item as $k => $v){
					$js.='"' . strtolower($k) . '":' . ($v === 1 || $v === 0 || $v === true || $v === 'true' || $v === 'false' || $v === false ?
							intval($v) :
							'\'' . addslashes($v) . '\'') . ',';
				}

				$js.='};' . $this->topFrame . '.treeData.add(new ' . $this->topFrame . '.node(attribs));
					}';
			}
		}
		$js.=$this->topFrame . '.drawTree();';

		return $js;
	}

}
