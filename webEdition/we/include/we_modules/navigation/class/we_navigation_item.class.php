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
class we_navigation_item{

	const DEFAULT_CURRENT = 'defaultCurrent';

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
	static $currentPosition;
	var $current = false;
	var $containsCurrent = false;
	private $visible = true;
	var $CurrentOnUrlPar;
	var $CurrentOnAnker;
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

	function __construct($id, $docid, $table, $text, $display, $href, $type, $icon, $attributes, $limitaccess, $customers = '', $CurrentOnUrlPar = 0, $CurrentOnAnker = 0){
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
		$db = new DB_WE();
		switch($this->table){
			case FILE_TABLE:
				list($__path) = explode((strpos($this->href, '#') !== false && strpos($this->href, '?') === false ? '#' : '?'), $this->href);

				$__id = path_to_id($__path, FILE_TABLE);
				if($__id){
					$this->visible = (f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($__id) . ' AND Published>0', '', $db) == 1);
				}
				if(NAVIGATION_DIRECTORYINDEX_HIDE && NAVIGATION_DIRECTORYINDEX_NAMES){
					$mypath = id_to_path($this->docid, FILE_TABLE);
					$mypath_parts = pathinfo($mypath);
					if(in_array($mypath_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
						$this->visible = ( f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->docid) . ' AND Published>0', '', $db) == 1);
					}
				}
				break;

			// #6916
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$__id = $this->docid;
				$this->visible = (f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($__id) . ' AND Published>0', '', $db) == 1);

				if(NAVIGATION_DIRECTORYINDEX_HIDE && NAVIGATION_DIRECTORYINDEX_NAMES){
					$mypath = id_to_path($this->docid, OBJECT_FILES_TABLE);
					$mypath_parts = pathinfo($mypath);
					if(in_array($mypath_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
						$this->visible = (f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->docid) . ' AND Published>0', '', $db) == 1);
					}
				}
				break;
		}
	}

	function addItem(&$item){
		$item->parentid = $this->id;
		$item->level = $this->level + 1;
		$this->items['id' . $item->id] = &$item;
		$item->position = count($this->items);
	}

	function setCurrent(we_navigation_items &$weNavigationItems, $self = true){
		if($self){
			$this->current = true;
		}

		if(isset($weNavigationItems->items['id' . $this->parentid]) && $this->level != 0){
			$weNavigationItems->items['id' . $this->parentid]->setCurrent($weNavigationItems);
			$this->setContainsCurrent();
		}
	}

	function unsetCurrent(we_navigation_items &$weNavigationItems, $self = true){
		if($self){
			$this->current = false;
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
		$this->containsCurrent = true;
	}

	function unsetContainsCurrent(){
		$this->containsCurrent = false;
	}

	function isCurrent(we_navigation_items $weNavigationItems){
		$thishref = $this->href;
		if(isset($_SERVER['REQUEST_URI']) && ($this->CurrentOnAnker || $this->CurrentOnUrlPar)){
			$uri = parse_url(str_replace('&amp;', '&', $_SERVER['REQUEST_URI']));
			$ref = parse_url(str_replace('&amp;', '&', $thishref));
			if($uri['path'] == $ref['path']){
				$allfound = true;
				if($this->CurrentOnAnker || $this->CurrentOnUrlPar){
					$uriarrq = array();
					if(isset($uri['query'])){
						parse_str($uri['query'], $uriarrq);
					}
					$refarrq = array();
					if(isset($ref['query'])){
						parse_str($ref['query'], $refarrq);
					}
					if($this->CurrentOnAnker && !$this->CurrentOnUrlPar){
						//remove other par's & compare only "anchors"
						$uriarrq = array('we_anchor' => isset($uriarrq['we_anchor']) ? $uriarrq['we_anchor'] : '#');
						$refarrq = array('we_anchor' => isset($refarrq['we_anchor']) ? $refarrq['we_anchor'] : '#');
					}
					if(($allfound &= (count($uriarrq) == count($refarrq)))){
						foreach($refarrq as $key => $val){
							$allfound &= isset($uriarrq[$key]) && $uriarrq[$key] == $val;
						}
					}
				}
				if($allfound){
					$this->setCurrent($weNavigationItems);
					//t_e('set');
				} elseif($this->current == true){
					//t_e('unset');
					$this->unsetCurrent($weNavigationItems);
				}
				return $allfound;
			}
		}

		switch($this->table){
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				if(isset($GLOBALS['we_obj'])){
					$id = $GLOBALS['we_obj']->ID;
				}
				break;
			case FILE_TABLE:
				if(isset($GLOBALS['WE_MAIN_DOC']) && (!isset($GLOBALS['WE_MAIN_DOC']->TableID))){
					$id = $GLOBALS['WE_MAIN_DOC']->ID;
				}
				break;
		}
		if(isset($id) && ($this->docid == $id) && !($this->CurrentOnUrlPar || $this->CurrentOnAnker)){
			$this->setCurrent($weNavigationItems);
			return true;
		}
		if($this->current == true){
			$this->unsetCurrent($weNavigationItems);
		}
		return false;
	}

	public function isVisible(){
		if($this->visible == false){
			return false;
		}

		if(defined('CUSTOMER_TABLE') && $this->limitaccess){ // only init filter if access is limited
			$_filter = new we_navigation_customerFilter();
			$_filter->initByNavItem($this);
			$this->visible = $_filter->customerHasAccess();
			return ($this->visible);
		}
		return true;
	}

	function writeItem(&$weNavigationItems, $depth = false){
		if($this->position == 1){
			self::$currentPosition = 0;
		}
		if(!($depth === false || $this->level <= $depth) || !$this->isVisible()){
			return '';
		}
		$GLOBALS['weNavigationItemArray'][] = &$this;
		//use this since items might be invisible
		self::$currentPosition++;
		ob_start();
		eval('?>' . $weNavigationItems->getTemplate($this));
		$executeContent = ob_get_contents();
		ob_end_clean();

		array_pop($GLOBALS['weNavigationItemArray']);

		return $executeContent;
	}

	function getNavigationField($attribs){
		$fieldname = weTag_getAttribute('_name_orig', $attribs);
		$_compl = weTag_getAttribute('complete', $attribs);
		// name
		if($fieldname){
			$val = (isset($this->$fieldname) && $this->$fieldname ?
							$this->$fieldname :
							(isset($this->attributes[$fieldname]) && $this->attributes[$fieldname] ?
									$this->attributes[$fieldname] :
									''));
			return ($fieldname == 'title' ? oldHtmlspecialchars($val) : $val);
		}

		// complete
		if($_compl){
			unset($attribs['complete']);
			if((($_compl == 'link' && isset($this->text)) || ($_compl == 'image' && isset($this->icon) && $this->icon != '/'))){
				unset($attribs['complete']);
				$attribs['attributes'] = $_compl;
				$attribs = $this->getNavigationFieldAttributes($attribs);
				return ($_compl == 'image' ?
								getHtmlTag('img', $attribs) :
								(isset($attribs['href']) && !empty($attribs['href']) ? getHtmlTag('a', $attribs, $this->text) : $this->text));
			}
			return '';
		}

		// attributes
		$code = '';
		if(isset($attribs['attributes'])){
			$_attributes = $this->getNavigationFieldAttributes($attribs);
			foreach($_attributes as $key => $value){
				$code .= ' ' . ($key == 'link_attribute' ? $value : $key . '="' . $value . '"');
			}
		}
		return $code;
	}

	function getNavigationFieldAttributes($attribs){
		$attr = weTag_getAttribute('attributes', $attribs);
		if($attr){
			$_fields = makeArrayFromCSV($attr);
			unset($attribs['attributes']);
			/* if(isset($_fields['link_attribute'])){
			  $_link_attribute = $_fields['link_attribute'];
			  } */
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
							'rev',
							'link_attribute'
						);
						foreach($useFields as $field){
							if(isset($this->$field) && $this->$field != ''){
								$attribs[$field] = ($field == 'title' ?
												oldHtmlspecialchars($this->$field) :
												$this->$field);
							} elseif(isset($this->attributes[$field]) && $this->attributes[$field] != ''){
								$attribs[$field] = ($field == 'link_attribute' ? // Bug #3741
												$this->attributes[$field] :
												oldHtmlspecialchars($this->attributes[$field]));
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
							$useFields = array('width', 'height', 'border', 'hspace', 'vspace', 'align', 'alt', 'title');
							foreach($useFields as $field){
								if(isset($this->attributes['icon_' . $field]) && $this->attributes['icon_' . $field] != ''){
									$attribs[$field] = $this->attributes['icon_' . $field];
								}
							}
							$_imgObj = new we_imageDocument();
							$_imgObj->initByID($_iconid);

							$_js = preg_replace(array('|<[^>]+><!--|', '|//--><[^>]+>|', '-(\r\n|\n)-'), '', $_imgObj->getRollOverScript('', '', false));

							$_arr = $_imgObj->getRollOverAttribsArr();
							if(!empty($_arr)){
								$_arr['onmouseover'] = $_js . $_arr['onmouseover'];
								$_arr['onmouseout'] = $_js . $_arr['onmouseout'];
								$_arr['name'] = $_imgObj->getElement('name');
								$attribs = array_merge($attribs, $_arr);
							}
						}
						break;
					default :
						if(isset($this->$_field) && $this->$_field != ''){
							$attribs[$_field] = oldHtmlspecialchars($this->$_field);
						} elseif(isset($this->attributes[$_field]) && $this->attributes[$_field] != ''){
							$attribs[$_field] = oldHtmlspecialchars($this->attributes[$_field]);
						}
				}
			}
		}

		return $attribs;
	}

	function getPopupJs(&$attributes){
		$js = 'var we_winOpts;';

		if($this->attributes['popup_center'] && $this->attributes['popup_width'] && $this->attributes['popup_height']){
			$js .= '
if (window.screen) {
	var w = ' . $this->attributes['popup_width'] . ';
	var h = ' . $this->attributes['popup_height'] . ';
		var screen_height = screen.availHeight - 70;
		var screen_width = screen.availWidth-10;
		var w = Math.min(screen_width,w);
		var h = Math.min(screen_height,h);
		var x = (screen_width - w) / 2;
		var y = (screen_height - h) / 2;
		we_winOpts = \'left=\'+x+\',top=\'+y;
	}else{
		we_winOpts=\'\';
	};';
		} elseif($this->attributes['popup_xposition'] != '' || $this->attributes['popup_yposition'] != ''){
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
			'name', 'target', 'onClick', 'onclick'
		));

		$attributes['target'] = 'we_ll_' . $this->id;
		$attributes['onclick'] = $js;
	}

}
