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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * simplified representation of the navigation item
 */
class weNavigationItem{

	var $id;
	var $icon;
	var $docid;
	var $table;
	var $parentid;
	var $text;
	var $display;
	var $name;
	var $href;
	var $type;
	var $level;
	var $position;
	var $current = 'false';
	var $containsCurrent = 'false';
	var $visible = 'true';
	var $CurrentOnUrlPar = '0';
	var $CurrentOnAnker = '0';
	//attributes
	var $title;
	var $anchor;
	var $target;
	var $lang;
	var $hreflang;
	var $accesskey;
	var $tabindex;
	var $rel;
	var $rev;
	var $limitaccess = 0;
	var $customers;
	var $items = array();

	function __construct($id, $docid, $table, $text, $display, $href, $type, $icon, $attributes, $limitaccess, $customers = "", $CurrentOnUrlPar = '0', $CurrentOnAnker = '0'){
		$this->id = $id;
		$this->parentid = 0;
		$this->name = $text;
		$this->text = (isset($display) && !empty($display) && $display != $text) ? $display : $text;
		$this->display = $display;
		$this->docid = $docid;
		$this->table = $table;
		$this->href = $href;
		$this->type = $type;
		$this->icon = $icon;
		$this->level = 0;
		$this->position = 0;
		$this->CurrentOnUrlPar = $CurrentOnUrlPar;
		$this->CurrentOnAnker = $CurrentOnAnker;

		if(!is_array($attributes)){
			$attributes = @unserialize($attributes);
		}
		$this->attributes = $attributes;

		$this->title = isset($attributes['title']) ? $attributes['title'] : '';
		$this->anchor = isset($attributes['anchor']) ? $attributes['anchor'] : '';
		$this->target = isset($attributes['target']) ? $attributes['target'] : '';
		$this->lang = isset($attributes['lang']) ? $attributes['lang'] : '';
		$this->hreflang = isset($attributes['hreflang']) ? $attributes['hreflang'] : '';
		$this->accesskey = isset($attributes['accesskey']) ? $attributes['accesskey'] : '';
		$this->tabindex = isset($attributes['tabindex']) ? $attributes['tabindex'] : '';
		$this->rel = isset($attributes['rel']) ? $attributes['rel'] : '';
		$this->rev = isset($attributes['rev']) ? $attributes['rev'] : '';

		$this->limitaccess = $limitaccess;
		$this->customers = $customers;

		if($this->table == FILE_TABLE){
			if(strpos($this->href, '#') !== false && strpos($this->href, '?') === false){
				list($__path) = explode("#", $this->href);
			} else{
				list($__path) = explode("?", $this->href);
			}

			$__id = path_to_id($__path, FILE_TABLE);
			if($__id){
				$_v = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE ID=' . intval($__id) . ' AND Published>0', 'ID', new DB_WE());
				$this->visible = !empty($_v) ? 'true' : 'false';
			}
			if(defined("NAVIGATION_DIRECTORYINDEX_HIDE") && NAVIGATION_DIRECTORYINDEX_HIDE && defined("NAVIGATION_DIRECTORYINDEX_NAMES") && NAVIGATION_DIRECTORYINDEX_NAMES != ''){
				$mypath = id_to_path($this->docid, FILE_TABLE);
				$mypath_parts = pathinfo($mypath);
				if(in_array($mypath_parts['basename'], explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))){
					$_v = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->docid) . ' AND Published>0', 'ID', new DB_WE());
					$this->visible = !empty($_v) ? 'true' : 'false';
				}
			}
		}
	}

	function addItem(&$item){
		$item->parentid = $this->id;
		$item->level = $this->level + 1;
		$this->items['id' . $item->id] = &$item;
		$item->position = sizeof($this->items);
	}

	function setCurrent(&$weNavigationItems, $self = true){

		if($self){
			$this->current = 'true';
		}

		if(isset($weNavigationItems->items['id' . $this->parentid]) && $this->level != 0){
			$weNavigationItems->items['id' . $this->parentid]->setCurrent($weNavigationItems);
			$this->setContainsCurrent();
		}
	}

	function unsetCurrent(&$weNavigationItems, $self = true){

		if($self){
			$this->current = 'false';
		}

		if(isset($weNavigationItems->items['id' . $this->parentid]) && $this->level != 0){
			//$weNavigationItems->items['id' . $this->parentid]->unsetCurrent($weNavigationItems);
			foreach($this->items as $_i){
				$_i->unsetCurrent($weNavigationItems);
			}
			$this->unsetContainsCurrent();
		}
	}

	function setContainsCurrent(){
		$this->containsCurrent = 'true';
	}

	function unsetContainsCurrent(){
		$this->containsCurrent = 'false';
	}

	function isCurrent($weNavigationItems){
		$thishref = $this->href;
		if($this->CurrentOnAnker || $this->CurrentOnUrlPar){ // jetzt kann man nicht mehr mit der id - weiter unten - arbeiten
			$thishref = str_replace(strstr($thishref, '#'), '', $thishref);
			$thishref = str_replace('&amp;', '&', $thishref);
		}
		if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == $thishref){
			// fastest way
			$this->setCurrent($weNavigationItems);
			return true;
		}
		if(isset($_SERVER['REQUEST_URI'])){ //#3698
			$uri = parse_url($_SERVER['REQUEST_URI']);
			$ref = parse_url($thishref);
			if((isset($uri['path']) && isset($ref['path']) && $uri['path'] == $ref['path']) && isset($uri['query']) && isset($ref['query'])){
				$uriarrq = explode('&', $uri['query']);
				$refarrq = explode('&', $ref['query']);
				$allfound = true;
				foreach($refarrq as $refa){
					if(!in_array($refa, $uriarrq)){
						$allfound = false;
					}
				}
				if($allfound){
					$this->setCurrent($weNavigationItems);
					return true;
				}
			}
		}

		if(isset($GLOBALS['we_obj']) && $this->table == OBJECT_FILES_TABLE){
			$id = $GLOBALS['we_obj']->ID;
		} else
		if(isset($GLOBALS["WE_MAIN_DOC"]) && (!isset($GLOBALS["WE_MAIN_DOC"]->TableID)) && $this->table == FILE_TABLE){
			$id = $GLOBALS["WE_MAIN_DOC"]->ID;
		}

		if(isset($id) && ($this->docid == $id) && !($this->CurrentOnUrlPar || $this->CurrentOnAnker)){
			$this->setCurrent($weNavigationItems);
			return true;
		} else{

			if($this->current == 'true'){

				$this->unsetCurrent($weNavigationItems);
			}
			return false;
		}
	}

	function isVisible(){
		if($this->visible == 'false'){
			return false;
		}

		if(defined('CUSTOMER_TABLE') && $this->limitaccess){ // only init filter if access is limited
			$_filter = new weNavigationCustomerFilter();
			$_filter->initByNavItem($this);

			return $_filter->customerHasAccess();
		}
		return true;
	}

	function writeItem(&$weNavigationItems, $depth = false){
		if(!($depth === false || $this->level <= $depth)){
			return '';
		}
		if(!$this->isVisible()){
			return false;
		}
		$template = $weNavigationItems->getTemplate($this);

		$GLOBALS['weNavigationItemArray'][] = & $this;

		$content = $template;
		ob_start();
		eval('?>' . $content);
		$executeContent = ob_get_contents();
		ob_end_clean();

		array_pop($GLOBALS['weNavigationItemArray']);

		return $executeContent;
	}

	function getNavigationField($attribs){
		$fieldname = weTag_getAttribute('name', $attribs);
		$_compl = weTag_getAttribute('complete', $attribs);
		// name
		if($fieldname){
			if(isset($this->$fieldname) && $this->$fieldname != ''){
				if($fieldname == 'title'){
					return htmlspecialchars($this->$fieldname);
				} else{
					return $this->$fieldname;
				}
			} else
			if(isset($this->attributes[$fieldname]) && $this->attributes[$fieldname] != ''){
				if($fieldname == 'title'){
					return htmlspecialchars($this->attributes[$fieldname]);
				} else{
					return $this->attributes[$fieldname];
				}
			} else{
				return '';
			}
		}

		// complete
		if($_compl){
			unset($attribs['complete']);
			if((($_compl == 'link' && isset($this->text)) || ($_compl == 'image' && isset($this->icon) && $this->icon != '/'))){
				unset($attribs['complete']);
				$attribs['attributes'] = $_compl;
				$attribs = $this->getNavigationFieldAttributes($attribs);
				if($_compl == 'image'){
					return getHtmlTag('img', $attribs);
				} else{
					return getHtmlTag('a', $attribs, $this->text);
				}
			}
			return '';
		}

		// attributes
		$_attributes = array();
		$code = '';
		if(isset($attribs['attributes'])){
			$_attributes = $this->getNavigationFieldAttributes($attribs);
			foreach($_attributes as $_key => $_value){
				$code .= ' '.$_key.'="' . $_value . '"';
			}
		}
		return $code;
	}

	function getNavigationFieldAttributes($attribs){
		if(isset($attribs['attributes'])){
			$_fields = makeArrayFromCSV($attribs['attributes']);
			unset($attribs['attributes']);
			if(isset($_fields['link_attribute'])){
				$_link_attribute = $_fields['link_attribute'];
			}
			foreach($_fields as $_field){
				switch($_field){
					case 'link' :
						$useFields = array(
							'href',
							'title',
							'target',
							'lang',
							'hreflang',
							'accesskey',
							'tabindex',
							'rel',
							'rev', 'link_attribute'
						);
						foreach($useFields as $field){
							if(isset($this->$field) && $this->$field != ''){
								if($field == 'title'){
									$attribs[$field] = htmlspecialchars($this->$field);
								} else{
									$attribs[$field] = $this->$field;
								}
								//$attribs[$field] = $this->$field;
							} else
							if(isset($this->attributes[$field]) && $this->attributes[$field] != ''){
								//$attribs[$field] = $this->attributes[$field];
								if($field == 'link_attribute'){ // Bug #3741
									$attribs[$field] = $this->attributes[$field];
								} else{
									$attribs[$field] = htmlspecialchars($this->attributes[$field]);
								}
							}
						}

						if(isset($this->attributes['popup_open']) && $this->attributes['popup_open']){
							$this->getPopupJs($attribs);
						}
						break;
					case 'image' :
						$_iconid = path_to_id($this->icon, FILE_TABLE);
						if($_iconid){
							$attribs['src'] = $this->icon;
							$useFields = array(
								'width', 'height', 'border', 'hspace', 'vspace', 'align', 'alt', 'title'
							);
							foreach($useFields as $field){
								if(isset($this->attributes['icon_' . $field]) && $this->attributes['icon_' . $field] != ''){
									$attribs[$field] = $this->attributes['icon_' . $field];
								}
							}
							$_imgObj = new we_imageDocument();
							$_imgObj->initByID($_iconid);

							$_js = $_imgObj->getRollOverScript();
							$_js = preg_replace("|<[^>]+><!--|", "", $_js);
							$_js = preg_replace("|//--><[^>]+>|", "", $_js);
							$_js = str_replace(array("\r\n", "\n"), '', $_js);

							$_arr = $_imgObj->getRollOverAttribsArr();
							if(count($_arr)){
								$_arr['onmouseover'] = $_js . $_arr['onmouseover'];
								$_arr['onmouseout'] = $_js . $_arr['onmouseout'];
								$_arr['name'] = $_imgObj->getElement('name');
								$attribs = array_merge($attribs, $_arr);
							}
						}
						break;
					default :
						if(isset($this->$_field) && $this->$_field != ''){
							$attribs[$_field] = htmlspecialchars($this->$_field);
						} else
						if(isset($this->attributes[$_field]) && $this->attributes[$_field] != ''){
							$attribs[$_field] = htmlspecialchars($this->attributes[$_field]);
						}
				}
			}
		}

		return $attribs;
	}

	function getPopupJs(&$attributes){
		$js = 'var we_winOpts;';

		if($this->attributes['popup_center'] && $this->attributes['popup_width'] && $this->attributes['popup_height']){
			$js .= 'if (window.screen) {var w = ' . $this->attributes['popup_width'] . ';var h = ' . $this->attributes['popup_height'] . ';var screen_height = screen.availHeight - 70;var screen_width = screen.availWidth-10;var w = Math.min(screen_width,w);var h = Math.min(screen_height,h);var x = (screen_width - w) / 2;var y = (screen_height - h) / 2;we_winOpts = \'left=\'+x+\',top=\'+y;}else{we_winOpts=\'\';};';
		} else
		if($this->attributes['popup_xposition'] != '' || $this->attributes['popup_yposition'] != ''){
			if($this->attributes['popup_xposition'] != ''){
				$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'left=' . $this->attributes['popup_xposition'] . '\';';
			}
			if($this->attributes['popup_yposition'] != ''){
				$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'top=' . $this->attributes['popup_yposition'] . '\';';
			}
		}
		if(isset($this->attributes['popup_width']) && $this->attributes['popup_width'] != ''){
			$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'width=' . $this->attributes['popup_width'] . '\';';
		}

		if(isset($this->attributes['popup_height']) && $this->attributes['popup_height'] != ''){
			$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'height=' . $this->attributes['popup_height'] . '\';';
		}

		$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'status=' . ((isset($this->attributes['popup_status']) && $this->attributes['popup_status'] != '') ? 'yes' : 'no') . '\';' .
			'we_winOpts += \',scrollbars=' . ((isset($this->attributes['popup_scrollbars']) && $this->attributes['popup_scrollbars'] != '') ? 'yes' : 'no') . '\';' .
			'we_winOpts += \',menubar=' . ((isset($this->attributes['popup_menubar']) && $this->attributes['popup_menubar'] != '') ? 'yes' : 'no') . '\';' .
			'we_winOpts += \',resizable=' . ((isset($this->attributes['popup_resizable']) && $this->attributes['popup_resizable'] != '') ? 'yes' : 'no') . '\';' .
			'we_winOpts += \',location=' . ((isset($this->attributes['popup_location']) && $this->attributes['popup_location'] != '') ? 'yes' : 'no') . '\';' .
			'we_winOpts += \',toolbar=' . ((isset($this->attributes['popup_toolbar']) && $this->attributes['popup_toolbar'] != '') ? 'yes' : 'no') . '\';' .
			"var we_win = window.open('" . $this->href . "','" . "we_ll_" . $this->id . "',we_winOpts);";

		$attributes = removeAttribs($attributes, array(
			'name', 'target', 'href', 'onClick', 'onclick'
			));

		$attributes['target'] = 'we_ll_' . $this->id;
		$attributes['onclick'] = $js;
	}

}
