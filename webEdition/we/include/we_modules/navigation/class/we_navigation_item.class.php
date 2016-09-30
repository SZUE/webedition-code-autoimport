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
 * simplified representation of the navigation item
 */
class we_navigation_item{
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
	static $currentPosition = array();
	var $current = false;
	var $containsCurrent = false;
	private $visible = -1;
	private $linkValid = true;
	private $customerAccess = true;
	var $CurrentOnUrlPar;
	var $CurrentOnAnker;
	var $currentOnCat;
	var $catParam;
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

	function __construct($id, $docid, $table, $text, $display, $href, $type, $icon, $attributes, $limitaccess, $customers = '', $CurrentOnUrlPar = 0, $CurrentOnAnker = 0, $currentOnCat = 0, $catParam = ''){
		$this->id = $id;
		$this->parentid = 0;
		$this->name = $text;
		$this->text = (!empty($display) && $display != $text) ? $display : $text;
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
		$this->currentOnCat = $currentOnCat;
		$this->catParam = $catParam;

		if(!is_array($attributes)){
			$attributes = we_unserialize($attributes);
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
		switch($this->table){
			case FILE_TABLE:
				//in case the docid is 0, we assume this a structural element, which has an "valid link"
				$this->linkValid = (!$this->docid) || ( f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->docid) . ' AND Published>0'));
				break;
			// #6916
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$this->linkValid = (f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->docid) . ' AND Published>0'));
				break;
			default:
				//this is external url, or anything we can't test for
				$this->linkValid = true;
		}
	}

	function __wakeup(){
		//need to reset customer access if the object was serialized (in cache)
		$this->customerAccess = true;
		$this->visible = -1;
		//leave linkValid, since on publish the cache is regenerated
	}

	function addItem(&$item){
		$item->parentid = $this->id;
		$item->level = $this->level + 1;
		$this->items['id' . $item->id] = &$item;
		$item->position = count($this->items);
	}

	function setCurrent(we_navigation_items &$weNavigationItems){
		$this->current = true;

		if(isset($weNavigationItems->items['id' . $this->parentid]) && $this->level != 0){
			$weNavigationItems->items['id' . $this->parentid]->setCurrent($weNavigationItems);
			$this->containsCurrent = true;
		}
	}

	function unsetCurrent(we_navigation_items &$weNavigationItems, $self = true){
		if($self){
			$this->current = false;
		}

		if(isset($weNavigationItems->items['id' . $this->parentid]) && $this->level != 0){
			foreach($this->items as $i){
				$i->unsetCurrent($weNavigationItems);
			}
			$this->containsCurrent = false;
		}
	}

	function isCurrent(we_navigation_items $weNavigationItems){
		switch($this->table){
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				if(isset($GLOBALS['we_obj'])){
					$id = $GLOBALS['we_obj']->ID;
				}
				break;
			case FILE_TABLE:
				if(isset($GLOBALS['WE_MAIN_DOC']) && (!($GLOBALS['WE_MAIN_DOC'] instanceof we_objectFile))){
					$id = $GLOBALS['WE_MAIN_DOC']->ID;
				}
				break;
		}

        if(isset($id) && ($this->docid == $id)){
            $cleanRequestUri = defined(WE_REDIRECTED_SEO) ? WE_REDIRECTED_SEO : (isset($_SERVER['REQUEST_URI']) ? parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH) : ''); //Fix #11057
            if(isset($_SERVER['REQUEST_URI']) && (stripos($this->href, $cleanRequestUri)!==false)){
                static $uri = null;
                static $uriarrq = array();
                $refarrq = array();

                $uri = ($uri === null ? parse_url(str_replace('&amp;', '&', $_SERVER['REQUEST_URI'])) : $uri);
                $ref = parse_url(str_replace('&amp;', '&', $this->href));
                if(!empty($uri['query']) && !$uriarrq){
                    parse_str($uri['query'], $uriarrq);
                }
                if(!empty($ref['query'])){
                    parse_str($ref['query'], $refarrq);
                }
                if(($this->CurrentOnAnker || $this->currentOnCat) && !$this->CurrentOnUrlPar){
                    //remove other param tha "anchors" or catParams respectively
                    $tmpUriarrq = $tmpRefarrq = array();
                    if($this->CurrentOnAnker){
                        $tmpUriarrq['we_anchor'] = isset($uriarrq['we_anchor']) ? $uriarrq['we_anchor'] : '#';
                        $tmpRefarrq['we_anchor'] = isset($refarrq['we_anchor']) ? $refarrq['we_anchor'] : '#';
                    }
                    if($this->currentOnCat){
                        $tmpUriarrq[$this->catParam] = isset($uriarrq[$this->catParam]) ? $uriarrq[$this->catParam] : '#';
                        $tmpRefarrq[$this->catParam] = isset($refarrq[$this->catParam]) ? $refarrq[$this->catParam] : '#';
                    }
                } else {
                    $tmpUriarrq = $uriarrq;
                    $tmpRefarrq = $refarrq;
                }

                $allfound = true;
                //current is true, if all arguements set in navigation match current request - if we have more (maybe a form, etc.) ignore this.
                foreach($tmpRefarrq as $key => $val){
                    $allfound &= isset($tmpUriarrq[$key]) && $tmpUriarrq[$key] == $val;
                }

                if($allfound){
                    $this->setCurrent($weNavigationItems);
                } elseif($this->current){
                    $this->unsetCurrent($weNavigationItems);
                }
                return $allfound;
            }

            if(!($this->CurrentOnUrlPar || $this->CurrentOnAnker) && (stripos($this->href, $cleanRequestUri)!==false)){
                $this->setCurrent($weNavigationItems);
                return true;
            }
        }

		if($this->current){
			$this->unsetCurrent($weNavigationItems);
		}
		return false;
	}

	public function isVisible(){
		if($this->visible != -1){
			//item is determined
			return $this->visible;
		}
		$this->visible = $this->linkValid;

		if(defined('CUSTOMER_TABLE') && $this->limitaccess){ // only init filter if access is limited
			$filter = new we_navigation_customerFilter();
			$filter->initByNavItem($this);
			$this->customerAccess = $filter->customerHasAccess();
			$this->visible &=$this->customerAccess;
		}
		return $this->visible;
	}

	public function setLevel(){
		self::$currentPosition[$this->level] = 0;
	}

	function writeItem(&$weNavigationItems, $depth = false){
		if(!isset(self::$currentPosition[$this->level])){
			self::$currentPosition[$this->level] = 0;
		}
		if(!($depth === false || $this->level <= $depth)){
			return '';
		}
		if(!$this->isVisible()){
			if($this->type == 'item' || !$this->customerAccess){
				//in case of an item, if this is not visible, we are finished
				//or if the folder/root is protected by customer access
				return '';
			}
			//in case of folder: check if there are visible subelements
			$vsub = false;
			//FIXME: we check only one level
			foreach($this->items as $item){
				if($item->isVisible()){
					$vsub = true;
					break;
				}
			}
			if(!$vsub){
				return '';
			}
		}
		$GLOBALS['weNavigationItemArray'][] = &$this;
		//use this since items might be invisible
		self::$currentPosition[$this->level] ++;
		ob_start();
		//FIXME:eval
		eval('?>' . $weNavigationItems->getTemplate($this));
		$executeContent = ob_get_clean();

		array_pop($GLOBALS['weNavigationItemArray']);

		return $executeContent;
	}

	function getNavigationField($attribs){
		$fieldname = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);
		$compl = weTag_getAttribute('complete', $attribs, '', we_base_request::STRING);
		// name
		if($fieldname){
			$val = (!empty($this->$fieldname) ?
					$this->$fieldname :
					(!empty($this->attributes[$fieldname]) ?
						$this->attributes[$fieldname] :
						''));
			switch($fieldname){
				case 'title':
					return oldHtmlspecialchars($val);
				case 'href':
					return $this->linkValid ? $val : '';
				default:
					return $val;
			}
		}

		// complete
		if($compl){
			unset($attribs['complete']);
			$attribs['attributes'] = $compl;
			switch($compl){
				case 'link':
					if(empty($this->text)){
						return '';
					}
					$attribs = $this->getNavigationFieldAttributes($attribs);
					return (empty($attribs['href']) || !$this->linkValid ? $this->text : getHtmlTag('a', $attribs, $this->text));
				case 'image':
					if(empty($this->icon) || $this->icon == '/'){
						return '';
					}
					$attribs = $this->getNavigationFieldAttributes($attribs);
					return getHtmlTag('img', $attribs);
			}
			return '';
		}

		// attributes
		$code = '';
		if(isset($attribs['attributes'])){
			$attributes = $this->getNavigationFieldAttributes($attribs);
			foreach($attributes as $key => $value){
				switch($key){
					case 'href':
						if(!$this->linkValid){
							break;
						}
					default:
						$code .= ' ' . ($key === 'link_attribute' ? $value : $key . '="' . $value . '"');
				}
			}
		}
		return $code;
	}

	function getNavigationFieldAttributes($attribs){
		$attr = weTag_getAttribute('attributes', $attribs, '', we_base_request::STRING);
		if($attr){
			$fields = makeArrayFromCSV($attr);
			unset($attribs['attributes']);
			/* if(isset($fields['link_attribute'])){
			  $link_attribute = $fields['link_attribute'];
			  } */
			foreach($fields as $field){
				switch($field){
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
							if(!empty($this->$field)){
								$attribs[$field] = ($field === 'title' ?
										oldHtmlspecialchars($this->$field) :
										$this->$field);
							} elseif(!empty($this->attributes[$field])){
								$attribs[$field] = ($field === 'link_attribute' ? // Bug #3741
										$this->attributes[$field] :
										oldHtmlspecialchars($this->attributes[$field]));
							}
						}

						if(!empty($this->attributes['popup_open'])){
							$this->getPopupJs($attribs);
						}
						break;
					case 'image' :
						$iconid = path_to_id($this->icon, FILE_TABLE, $GLOBALS['DB_WE']);
						if($iconid){
							$attribs['src'] = $this->icon;
							$useFields = array('width', 'height', 'border', 'hspace', 'vspace', 'align', 'alt', 'title');
							foreach($useFields as $field){
								if(!empty($this->attributes['icon_' . $field])){
									$attribs[$field] = $this->attributes['icon_' . $field];
								}
							}
							$imgObj = new we_imageDocument();
							$imgObj->initByID($iconid);

							$js = preg_replace(array('|<[^>]+><!--|', '|//--><[^>]+>|', '-(\r\n|\n)-'), '', $imgObj->getRollOverScript('', '', false));

							$arr = $imgObj->getRollOverAttribsArr();
							if(!empty($arr)){
								$arr['onmouseover'] = $js . ';' . $arr['onmouseover'];
								$arr['onmouseout'] = $js . ';' . $arr['onmouseout'];
								$arr['name'] = $imgObj->getElement('name');
								$attribs = array_merge($attribs, $arr);
							}
						}
						break;
					default :
						if(!empty($this->$field)){
							$attribs[$field] = oldHtmlspecialchars($this->$field);
						} elseif(!empty($this->attributes[$field])){
							$attribs[$field] = oldHtmlspecialchars($this->attributes[$field]);
						}
				}
			}
		}

		return $attribs;
	}

	function getPopupJs(&$attributes){
		$js = 'var we_winOpts;';

		if(!empty($this->attributes['popup_center']) && !empty($this->attributes['popup_width']) && !empty($this->attributes['popup_height'])){
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
		} elseif(!empty($this->attributes['popup_xposition']) || !empty($this->attributes['popup_yposition'])){
			if(!empty($this->attributes['popup_xposition'])){
				$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'left=' . $this->attributes['popup_xposition'] . '\';';
			}
			if(!empty($this->attributes['popup_yposition'])){
				$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'top=' . $this->attributes['popup_yposition'] . '\';';
			}
		}

		$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'status=' . ((!empty($this->attributes['popup_status'])) ? 'yes' : 'no') .
			',scrollbars=' . (!empty($this->attributes['popup_scrollbars']) ? 'yes' : 'no') .
			',menubar=' . (!empty($this->attributes['popup_menubar']) ? 'yes' : 'no') .
			',resizable=' . (!empty($this->attributes['popup_resizable']) ? 'yes' : 'no') .
			',location=' . (!empty($this->attributes['popup_location']) ? 'yes' : 'no') .
			',toolbar=' . (!empty($this->attributes['popup_toolbar']) ? 'yes' : 'no') .
			(empty($this->attributes['popup_width']) ? '' : ',width=' . $this->attributes['popup_width'] ) .
			(empty($this->attributes['popup_height']) ? '' : ',height=' . $this->attributes['popup_height']) .
			'\';' .
			"var we_win = window.open('" . $this->href . "','" . "we_ll_" . $this->id . "',we_winOpts);";

		$attributes = removeAttribs($attributes, array(
			'name', 'target', 'onClick', 'onclick'
		));

		$attributes['target'] = 'we_ll_' . $this->id;
		$attributes['onclick'] = $js;
	}

}
