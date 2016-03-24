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
class we_base_linklist{
	private $name = "";
	private $sString = "";
	private $listArray;
	private $db;
	private $rollScript = "";
	private $rollAttribs = array();
	private $cache = array();
	private $hidedirindex = false;
	private $objectseourls = false;
	private $docName;
	private $attribs;
	private $show = -1;
	private $cnt = 0;
	private $pos = -1;

	function __construct($sString, $hidedirindex = false, $objectseourls = false, $docName = '', $attribs = array()){
		$this->sString = $sString;
		$this->hidedirindex = $hidedirindex;
		$this->objectseourls = $objectseourls;
		$this->docName = $docName;
		$this->attribs = $attribs;
		$this->listArray = $sString && $sString{0} === 'a' ? unserialize($sString) : array();
		if(!is_array($this->listArray)){
			$this->listArray = array();
		} else {
		ksort($this->listArray, SORT_NUMERIC);
		$this->listArray = array_values($this->listArray);
		}
		$limit = isset($attribs['limit']) && $attribs['limit'] > 0 ? abs($attribs['limit']) : 0;
		$editmode = (isset($GLOBALS["we_editmode"]) && $GLOBALS["we_editmode"] && (!isset($GLOBALS["lv"])));
		if(!$editmode){
			$this->show = count($this->listArray);
			if($limit > 0 && $this->show > $limit){
				$this->show = $limit;
			}
		}

		$this->db = new DB_WE();
		reset($this->listArray);
	}

	function setName($name){
		$this->name = $name;
	}

	function getName(){
		return $this->name;
	}

	function getID($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return isset($cur['id']) ? $cur['id'] : null;
	}

	function getObjID($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return isset($cur['obj_id']) ? $cur['obj_id'] : "";
	}

	function getLink(){
		switch($this->getType()){
			case we_base_link::TYPE_INT:
				return $this->getUrl();
			case we_base_link::TYPE_EXT:
				return $this->getHref();
			case we_base_link::TYPE_MAIL:
				return $this->getHref();
			case we_base_link::TYPE_OBJ:
				$link = we_objectFile::getObjectHref($this->getObjID(), $GLOBALS['WE_MAIN_DOC']->ParentID, $GLOBALS['WE_MAIN_DOC']->Path, $this->db, $this->hidedirindex, $this->objectseourls);
				if(isset($GLOBALS['we_link_not_published'])){
					unset($GLOBALS['we_link_not_published']);
				}
				return $link;
			default:
				return '';
		}
	}

	function getHref($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return $cur['href'];
	}

	function getAttribs($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return isset($cur["attribs"]) ? $cur["attribs"] : "";
	}

	function getTarget($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return $cur['target'];
	}

	function getTitle($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return isset($cur["title"]) ? $cur["title"] : "";
	}

	function getLinktag($link = '', $tagAttr = ''){
		$link = $link? : $this->getLink();
		$target = $this->getTarget();
		$attribs = $this->getAttribs();
		$anchor = $this->getAnchor();
		$accesskey = $this->getAccesskey();
		$tabindex = $this->getTabindex();
		$lang = $this->getLang();
		$hreflang = $this->getHreflang();
		$rel = $this->getRel();
		$rev = $this->getRev();
		$params = $this->getParams();
		$title = $this->getTitle();
		$jswinAttribs = $this->getJsWinAttribs();

		$lattribs = we_tag_tagParser::makeArrayFromAttribs($attribs);

		$lattribs['target'] = $target;
		$lattribs['title'] = $title;
		$lattribs['accesskey'] = $accesskey;
		$lattribs['tabindex'] = $tabindex;
		$lattribs['lang'] = $lang;
		$lattribs['hreflang'] = $hreflang;
		$lattribs['rel'] = $rel;
		$lattribs['rev'] = $rev;
		$lattribs = array_filter($lattribs);

		$rollOverAttribsArr = $this->rollAttribs;

		if(is_array($tagAttr)){
			foreach($tagAttr as $n => $v){
				$lattribs[$n] = $v;
			}
		}

		// overwrite rolloverattribs
		foreach($rollOverAttribsArr as $n => $v){
			$lattribs[$n] = $v;
		}

		if(isset($jswinAttribs) && is_array($jswinAttribs) && isset($jswinAttribs["jswin"]) && $jswinAttribs["jswin"]){ //popUp
			$js = "var we_winOpts = '';";
			if($jswinAttribs["jscenter"] && $jswinAttribs["jswidth"] && $jswinAttribs["jsheight"]){
				$js .= 'if (window.screen) {var w = ' . $jswinAttribs["jswidth"] . ';var h = ' . $jswinAttribs["jsheight"] . ';var screen_height = screen.availHeight - 70;var screen_width = screen.availWidth-10;var w = Math.min(screen_width,w);var h = Math.min(screen_height,h);var x = (screen_width - w) / 2;var y = (screen_height - h) / 2;we_winOpts = \'left=\'+x+\',top=\'+y;}else{we_winOpts=\'\';};';
			} elseif($jswinAttribs["jsposx"] != "" || $jswinAttribs["jsposy"] != ""){
				if($jswinAttribs["jsposx"] != ""){
					$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'left=' . $jswinAttribs["jsposx"] . '\';';
				}
				if($jswinAttribs["jsposy"] != ""){
					$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'top=' . $jswinAttribs["jsposy"] . '\';';
				}
			}
			$js.=
				($jswinAttribs["jswidth"] ? 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'width=' . $jswinAttribs["jswidth"] . '\';' : '') .
				($jswinAttribs["jsheight"] ? 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'height=' . $jswinAttribs["jsheight"] . '\';' : '') .
				'we_winOpts += (we_winOpts ? \',\' : \'\')+\'status=' . ($jswinAttribs["jsstatus"] ? 'yes' : 'no') . '\';' .
				'we_winOpts += (we_winOpts ? \',\' : \'\')+\'scrollbars=' . ($jswinAttribs["jsscrollbars"] ? 'yes' : 'no') . '\';' .
				'we_winOpts += (we_winOpts ? \',\' : \'\')+\'menubar=' . ($jswinAttribs["jsmenubar"] ? 'yes' : 'no') . '\';' .
				'we_winOpts += (we_winOpts ? \',\' : \'\')+\'resizable=' . ($jswinAttribs["jsresizable"] ? 'yes' : 'no') . '\';' .
				'we_winOpts += (we_winOpts ? \',\' : \'\')+\'location=' . ($jswinAttribs["jslocation"] ? 'yes' : 'no') . '\';' .
				'we_winOpts += (we_winOpts ? \',\' : \'\')+\'toolbar=' . (isset($jswinAttribs["jstoolbar"]) && $jswinAttribs["jstoolbar"] ? 'yes' : 'no') . '\';';
			$foo = $js . "var we_win = window.open('','" . "we_ll_" . key($this->listArray) . "',we_winOpts);";

			$lattribs = removeAttribs($lattribs, array('name', 'href', 'onClick'));

			$lattribs['target'] = 'we_ll_' . key($this->listArray);
			$lattribs['onclick'] = $foo;
		} else { //  no popUp
			$lattribs = removeAttribs($lattribs, array('name', 'href'));
		}
		$lattribs['href'] = $link . str_replace('&', '&amp;', $params . $anchor);

		if(isset($lattribs['only'])){
			switch($lattribs['only']){
				case 'text':
					return $this->getText();
				case 'id':
					return $this->getID();
				default:
					return $lattribs[$lattribs['only']];
			}
		}

		return $this->rollScript . getHtmlTag('a', $lattribs, '', false, true);
	}

	function getUrl($params = ""){
		$id = $this->getID();
		if(!$id){
			return we_base_link::EMPTY_EXT;
		}
		if(!isset($this->cache[$id])){
			$this->cache[$id] = getHash('SELECT IsDynamic,Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), $this->db);
		}
		$row = $this->cache[$id];
		if(isset($row['Path']) && $this->hidedirindex){
			$path_parts = pathinfo($row["Path"]);
			if(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
				$row["Path"] = ($path_parts['dirname'] != DIRECTORY_SEPARATOR ? $path_parts['dirname'] : '') . DIRECTORY_SEPARATOR;
			}
		}

		return (isset($row["Path"]) ? $row["Path"] : '') . ($params ? ('?' . $params) : "");
	}

	function getImageID($nr = -1){
		$cur = ($nr != -1 ? $this->listArray[$nr] : current($this->listArray));
		return isset($cur["img_id"]) ? $cur["img_id"] : "";
	}

	function getImageAttribs($nr = -1){
		$cur = ($nr != -1 ? $this->listArray[$nr] : current($this->listArray));
		return (isset($cur['img_attribs']) ? $cur['img_attribs'] : array());
	}

	function getImageAttrib($nr, $key){
		$foo = $this->getImageAttribs($nr);
		return (isset($foo[$key]) ? $foo[$key] : '');
	}

	function getJsWinAttrib($nr, $key){
		$foo = $this->getJsWinAttribs();
		return (isset($foo[$key]) ? $foo[$key] : '');
	}

	function getJsWinAttribs($nr = -1){
		$cur = ($nr != -1 ? $this->listArray[$nr] : current($this->listArray));
		return (isset($cur["jswin_attribs"]) ? $cur["jswin_attribs"] : array());
	}

	function getImageSrc($nr = -1){
		$cur = ($nr != -1 ? $this->listArray[$nr] : current($this->listArray));
		return isset($cur["img_src"]) ? $cur["img_src"] : "";
	}

	function getText($nr = -1){
		$cur = ($nr != -1 ? $this->listArray[$nr] : current($this->listArray));
		return $cur["text"];
	}

	function getAnchor($nr = -1){
		$cur = ($nr != -1 ? $this->listArray[$nr] : current($this->listArray));
		return isset($cur['anchor']) ? $cur['anchor'] : '';
	}

	function getAccesskey($nr = -1){
		$cur = ($nr != -1 ? $this->listArray[$nr] : current($this->listArray));
		return isset($cur['accesskey']) ? $cur['accesskey'] : '';
	}

	function getTabindex($nr = -1){
		$cur = ($nr != -1 ? $this->listArray[$nr] : current($this->listArray));
		return isset($cur['tabindex']) ? $cur['tabindex'] : '';
	}

	function getLang($nr = -1){
		$cur = ($nr != -1 ? $this->listArray[$nr] : current($this->listArray));
		return (isset($cur['lang']) ? $cur['lang'] : '');
	}

	function getRel($nr = -1){
		$cur = ($nr != -1 ? $this->listArray[$nr] : current($this->listArray));
		return (isset($cur['rel']) ? $cur['rel'] : '');
	}

	function getRev($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return (isset($cur['rev']) ? $cur['rev'] : '');
	}

	function getHreflang($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return (isset($cur['hreflang']) ? $cur['hreflang'] : '');
	}

	function getHidedirindex($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return (isset($cur['hidedirindex']) ? $cur['hidedirindex'] : '');
	}

	function getObjectseourls($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return (isset($cur['objectseourls']) ? $cur['objectseourls'] : '');
	}

	function getParams($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return (isset($cur['params']) ? $cur['params'] : '');
	}

	function getHrefInt($nr = -1){
		$id = $this->getID($nr);
		return ($id ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'Path', $this->db) : '');
	}

	function getHrefObj($nr = -1){
		$id = $this->getObjID($nr);
		return ($id && defined('OBJECT_FILES_TABLE') ? f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id), '', $this->db) : '');
	}

	function getImageSrcInt($nr = -1){
		$id = $this->getImageID($nr);
		return ($id ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'Path', $this->db) : '');
	}

	function getString(){
		return ($this->listArray ? serialize($this->listArray) : '');
	}

	//added for #7269
	function getBcc($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return isset($cur['bcc']) ? $cur['bcc'] : '';
	}

	function getCc($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return isset($cur['cc']) ? $cur['cc'] : '';
	}

	function getSubject($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return isset($cur['subject']) ? $cur['subject'] : '';
	}

	function setID($nr, $val){
		$this->listArray[$nr]["id"] = $val;
	}

	function setObjID($nr, $val){
		$this->listArray[$nr]["obj_id"] = $val;
	}

	function setHref($nr, $val){
		$this->listArray[$nr]["href"] = $val;
	}

	function setAnchor($nr, $val){
		$this->listArray[$nr]["anchor"] = $val;
	}

	function setAccesskey($nr, $val){
		$this->listArray[$nr]["accesskey"] = $val;
	}

	function setTabindex($nr, $val){
		$this->listArray[$nr]["tabindex"] = $val;
	}

	function setLang($nr, $val){
		$this->listArray[$nr]["lang"] = $val;
	}

	function setRel($nr, $val){
		$this->listArray[$nr]["rel"] = $val;
	}

	function setRev($nr, $val){
		$this->listArray[$nr]["rev"] = $val;
	}

	function setHreflang($nr, $val){
		$this->listArray[$nr]["hreflang"] = $val;
	}

	function setParams($nr, $val){
		$this->listArray[$nr]["params"] = $val;
	}

	function setAttribs($nr, $val){
		$this->listArray[$nr]["attribs"] = $val;
	}

	function setTarget($nr, $val){
		$this->listArray[$nr]["target"] = $val;
	}

	function setImageID($nr, $val){
		$this->listArray[$nr]["img_id"] = $val;
	}

	function setTitle($nr, $val){
		$this->listArray[$nr]["title"] = $val;
	}

	function setImageSrc($nr, $val){
		$this->listArray[$nr]["img_src"] = $val;
	}

	function setText($nr, $val){
		$this->listArray[$nr]["text"] = $val;
	}

	function setImageAttribs($nr, $val){
		$this->listArray[$nr]["img_attribs"] = $val;
	}

	function setImageAttrib($nr, $key, $val){
		$this->listArray[$nr]["img_attribs"][$key] = $val;
	}

	function setJsWinAttribs($nr, $val){
		$this->listArray[$nr]["jswin_attribs"] = $val;
	}

	function setJsWinAttrib($nr, $key, $val){
		$this->listArray[$nr]["jswin_attribs"][$key] = $val;
	}

	function setBcc($nr, $val){
		$this->listArray[$nr]["bcc"] = $val;
	}

	function setCc($nr, $val){
		$this->listArray[$nr]["cc"] = $val;
	}

	function setSubject($nr, $val){
		$this->listArray[$nr]["subject"] = $val;
	}

	function next(){
		if($this->pos != -1){
			++$this->cnt;
		}
		$ret = ($this->show == -1 || $this->show > $this->cnt);
		$GLOBALS['we_position']['linklist'][$this->name] = array('size' => count($this->listArray), 'position' => $this->cnt);
		if($this->pos++ == -1){
			reset($this->listArray);
			return $ret & ($this->length() > 0);
		}

		$editmode = (isset($GLOBALS["we_editmode"]) && $GLOBALS["we_editmode"] && (!isset($GLOBALS["lv"])));

		if($editmode){
			$disabled = ($this->show > 0 && $this->length() >= $this->show);
			$plusbut = we_html_button::create_button(
					"image:btn_add_link", "javascript:setScrollTo();_EditorFrame.setEditorIsHot(1);we_cmd('insert_link_at_linklist','" . $this->attribs["name"] . "','" . key($this->listArray) . "')", true, 100, 22, "", "", $disabled);
			if($ret === false){
				if(isset($GLOBALS["we_list_inserted"]) && isset($GLOBALS["we_list_inserted"]) && ($GLOBALS["we_list_inserted"] == $this->attribs["name"])){
					echo we_html_element::jsElement('we_cmd(\'edit_linklist\',\'' . $this->attribs["name"] . '\',\'' . ((isset(
							$GLOBALS["we_list_insertedNr"]) && $GLOBALS["we_list_insertedNr"] ) ? $GLOBALS["we_list_insertedNr"] : $this->getMaxListNrID()) . '\');');
				}
				if($this->show == -1 || ($this->show > $this->length())){
					echo "<br/>" . we_html_button::create_button("image:btn_add_link", "javascript:setScrollTo();_EditorFrame.setEditorIsHot(1);we_cmd('add_link_to_linklist','" . $this->attribs["name"] . "')", true, 100, 22, "", "", $disabled) .
					'<input type="hidden" name="we_' . $this->docName . '_linklist[' . $this->attribs["name"] . ']" value="' . oldHtmlspecialchars(
						$this->getString()) . '" />' . ($this->length() ? '' : $plusbut);
				}
			} else {
				// Create button object
				// Create buttons
				$upbut = we_html_button::create_button("image:btn_direction_up", "javascript:setScrollTo();_EditorFrame.setEditorIsHot(1);we_cmd('up_link_at_list','" . $this->attribs["name"] . "','" . key($this->listArray) . "')", true, 0, 0, "", "", !($this->cnt > 0));
				$downbut = we_html_button::create_button("image:btn_direction_down", "javascript:setScrollTo();_EditorFrame.setEditorIsHot(1);we_cmd('down_link_at_list','" . $this->attribs["name"] . "','" . key($this->listArray) . "')", true, 0, 0, "", "", !($this->cnt < (count($this->listArray) - 1)));
				$editbut = we_html_button::create_button("image:btn_edit_link", "javascript:setScrollTo();_EditorFrame.setEditorIsHot(1);we_cmd('edit_linklist','" . $this->attribs["name"] . "','" . key($this->listArray) . "')", true);
				$trashbut = we_html_button::create_button("image:btn_function_trash", "javascript:setScrollTo();_EditorFrame.setEditorIsHot(1);we_cmd('delete_linklist','" . $this->attribs["name"] . "','" . key($this->listArray) . "','')", true);
				echo we_html_button::create_button_table(array($plusbut, $upbut, $downbut, $editbut, $trashbut), 5);
			}
		}
		$ret&= next($this->listArray);
		if($ret === false){
			//remove var
			unset($GLOBALS['we_position']['linklist'][$this->name]);
		}
		return $ret;
	}

	function getType($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return isset($cur['type']) ? $cur['type'] : '';
	}

	function getCType($nr = -1){
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		return isset($cur['ctype']) ? $cur['ctype'] : '';
	}

	function setType($nr, $val){
		$this->listArray[$nr]['type'] = $val;
	}

	function setCType($nr, $val){
		$this->listArray[$nr]['ctype'] = $val;
	}

	function addLink(){
		$this->listArray[] = $this->getRawLink();
	}

	function length(){
		return count($this->listArray);
	}

	function upLink($nr){
		if($nr > 0 && $nr < count($this->listArray)){
			$temp = $this->listArray[$nr - 1];
			$this->listArray[$nr - 1] = $this->listArray[$nr];
			$this->listArray[$nr] = $temp;
		}
	}

	function downLink($nr){
		if($nr >= 0 && ($nr + 1) < count($this->listArray)){
			$temp = $this->listArray[$nr + 1];
			$this->listArray[$nr + 1] = $this->listArray[$nr];
			$this->listArray[$nr] = $temp;
		}
	}

	function insertLink($nr){
		$nr = abs($nr);
		$l = $this->getRawLink();
		foreach($this->listArray as $i => &$cur){
			if(!isset($cur['nr'])){
				$cur['nr'] = $i;
			}
		}
		unset($cur);
		for($i = count($this->listArray); $i > $nr; $i--){
			$this->listArray[$i] = $this->listArray[$i - 1];
		}
		$this->listArray[$nr] = $l;
		ksort($this->listArray, SORT_NUMERIC);
	}

	function removeLink($nr, $names = '', $name = ''){
		$realNr = $this->listArray[$nr]['nr'];
		$namesArray = $names ? explode(',', $names) : array();
		foreach($namesArray as $n){
			$GLOBALS['we_doc']->delElement($n . $name . '_TAGS_' . $realNr);
		}
		unset($this->listArray[$nr]);
	}

	/* ##### private Functions##### */

	private function getMaxListNr(){
		$n = 0;
		foreach($this->listArray as $item){
			$n = max($item['nr'], $n);
		}
		return $n;
	}

	private function getMaxListNrID(){
		$n = $out = 0;
		for($i = 0; $i < count($this->listArray); $i++){
			if($this->listArray[$i]["nr"] > $n){
				$n = $this->listArray[$i]["nr"];
				$out = $i;
			}
		}
		return $out;
	}

	private function getRawLink(){
		return array(
			'href' => we_base_link::EMPTY_EXT,
			'text' => g_l('global', '[new_link]'),
			'target' => '',
			'type' => we_base_link::TYPE_EXT,
			'ctype' => 'text',
			'nr' => $this->getMaxListNr() + 1,
		);
	}

	function getLinkContent(){
		switch($this->getCType()){
			case we_base_link::CONTENT_INT:
				return $this->makeImgTag();
			case we_base_link::CONTENT_EXT:
				return $this->makeImgTagFromSrc($this->getImageSrc(), $this->getImageAttribs());
			case we_base_link::CONTENT_TEXT:
				return $this->getText();
			default:
				return '';
		}
	}

	function makeImgTag($nr = -1){
		$id = $this->getImageID();
		$cur = $nr != -1 ? $this->listArray[$nr] : current($this->listArray);
		$attribs = $this->getImageAttribs();
		$img = new we_imageDocument();
		$img->initByID($id);
		$img->initByAttribs($attribs);
		//	name in linklist is generated from linklistname
		$img->elements['name']['dat'] = $this->name . "_img" . key($cur);
		$this->rollScript = $img->getRollOverScript();
		$this->rollAttribs = $img->getRollOverAttribsArr();

		return $img->getHtml(false, false);
	}

	function makeImgTagFromSrc($src, $attribs){
		$attribs = removeEmptyAttribs($attribs, array('alt'));
		$attribs['src'] = $src;
		return getHtmlTag('img', $attribs);
	}

	function mta($hash, $key){
		return (isset($hash[$key]) && $hash[$key]) ? (' ' . $key . '="' . $hash[$key] . '"') : '';
	}

	function last(){
		$editmode = (isset($GLOBALS["we_editmode"]) && $GLOBALS["we_editmode"] && (!isset($GLOBALS["lv"])));
		if($editmode && ($this->show == -1 || ($this->show > $this->length()))){
			echo "<br/>" . we_html_button::create_button(
				"image:btn_add_link", "javascript:setScrollTo();_EditorFrame.setEditorIsHot(1);we_cmd('add_link_to_linklist','" . $this->attribs["name"] . "')", true, 100, 22, "", "", false);
			echo '<input type="hidden" name="we_' . $this->docName . '_linklist[' . $this->attribs["name"] . ']" value="' . oldHtmlspecialchars(
				$this->getString()) . '" />';
		}
	}

}
