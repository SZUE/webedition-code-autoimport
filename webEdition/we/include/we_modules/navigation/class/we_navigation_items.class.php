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
 * collection of the navigation items
 */
class we_navigation_items{

	const TEMPLATE_DEFAULT_CURRENT = 'defaultCurrent';
	const TEMPLATE_DEFAULT_POSITION = 'defaultPosition';
	const TEMPLATE_DEFAULT_LEVEL = 'defaultLevel';

	private static $cache = array();
	var $items;
	var $templates;
	var $rootItem = 0;
	var $hasCurrent = false;
	var $currentRules = array();
	private $Storage = array(); //FIXME: make this static

	function getCustomerData($navi){
		$_customer = array(
			'id' => '', 'filter' => '', 'blacklist' => '', 'whitelist' => '', 'usedocumentfilter' => 1
		);

		if(!is_array($navi->Customers)){
			$navi->Customers = makeArrayFromCSV($navi->Customers);
		}

		if(!is_array($navi->BlackList)){
			$navi->BlackList = makeArrayFromCSV($navi->BlackList);
		}

		if(!is_array($navi->WhiteList)){
			$navi->WhiteList = makeArrayFromCSV($navi->WhiteList);
		}

		if(!is_array($navi->CustomerFilter)){
			$navi->CustomerFilter = @unserialize($navi->CustomerFilter);
		}

		if($navi->LimitAccess){
			$_customer['id'] = $navi->AllCustomers == 0 ? $navi->Customers : array();
			$_customer['filter'] = $navi->ApplyFilter == 1 ? $navi->CustomerFilter : array();
			$_customer['blacklist'] = $navi->ApplyFilter == 1 ? $navi->BlackList : array();
			$_customer['whitelist'] = $navi->ApplyFilter == 1 ? $navi->WhiteList : array();
			$_customer['usedocumentfilter'] = $navi->UseDocumentFilter ? 1 : 0;
			return $_customer;
		}

		return $_customer;
	}

	function initByNavigationObject($showRoot = true){
		$this->items = array();
		$navigation = unserialize($_SESSION['weS']['navigation_session']);

		$this->rootItem = $navigation->ID;

// set defaultTemplates
		$this->setDefaultTemplates();

		$this->readItemsFromDb($this->rootItem);

		$this->items['id' . $navigation->ID] = new we_navigation_item($navigation->ID, $navigation->LinkID, ($navigation->IsFolder ? ($navigation->FolderSelection == we_navigation_navigation::STPYE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE) : (($navigation->SelectionType == we_navigation_navigation::STPYE_CLASS || $navigation->SelectionType == we_navigation_navigation::STPYE_OBJLINK) ? OBJECT_FILES_TABLE : FILE_TABLE)), $navigation->Text, $navigation->Display, $navigation->getHref($navigation->SelectionType, $navigation->LinkID, $navigation->Url, $navigation->Parameter, $navigation->WorkspaceID), $showRoot ? 'folder' : 'root', $this->id2path($navigation->IconID), $navigation->Attributes, $navigation->LimitAccess, $this->getCustomerData($navigation), $navigation->CurrentOnUrlPar, $navigation->CurrentOnAnker);

		$items = $navigation->getDynamicPreview($this->Storage);

		$_new_items = self::getStaticSavedDynamicItems($navigation);

// fetch the new items in item array
		$_depended = array();
		foreach($items as $k => $v){
			if($v['depended'] == 1 && $v['parentid'] == $navigation->ID){
				$_depended[] = $k;
			}
		}

		$i = 0;
		foreach($_new_items as $_new){
			if(isset($_depended[$i])){
				$items[$_depended[$i]] = $_new;
			} else {
				$items[] = $_new;
			}
			$i++;
		}

		$_all = count($items) - count($_depended) + count($_new_items);
		$items = array_splice($items, 0, $_all);
		foreach($items as $_item){
			$this->items['id' . $_item['id']] = new we_navigation_item($_item['id'], $_item['docid'], $_item['table'], $_item['text'], $_item['display'], $_item['href'], $_item['type'], $_item['icon'], $_item['attributes'], $_item['limitaccess'], $_item['customers'], isset($_item['currentonurlpar']) ? $_item['currentonurlpar'] : '', isset($_item['currentonanker']) ? $_item['currentonanker'] : '');
			if(isset($this->items['id' . $_item['parentid']])){
				$this->items['id' . $_item['parentid']]->addItem($this->items['id' . $_item['id']]);
			}
		}
	}

	function getStaticSavedDynamicItems(we_navigation_navigation $_nav, $rules = false){
		$items = array();
		$dyn_items = $_nav->getDynamicEntries();
		if(is_array($dyn_items)){
			foreach($dyn_items as $_dyn){

				$href = id_to_path($_dyn['id']);
				$items[] = array(
					'id' => $_dyn['id'],
					'text' => isset($_dyn['field']) && $_dyn['field'] ? $_dyn['field'] : $_dyn['text'],
					'display' => isset($_dyn['display']) && $_dyn['display'] ? $_dyn['display'] : '',
					'name' => $_dyn['field'] ? $_dyn['field'] : (isset($_dyn['name']) && $_dyn['name'] ? $_dyn['name'] : $_dyn['text']),
					'docid' => $_dyn['id'],
					'table' => (($_nav->SelectionType == we_navigation_navigation::STPYE_CLASS || $_nav->SelectionType == we_navigation_navigation::STPYE_OBJLINK) ? OBJECT_FILES_TABLE : FILE_TABLE),
					'href' => $href,
					'type' => 'item',
					'parentid' => $_nav->ID,
					'workspaceid' => $_nav->WorkspaceID,
					'icon' => isset($this->Storage['ids'][$_nav->IconID]) ? $this->Storage['ids'][$_nav->IconID] : id_to_path($_nav->IconID),
					'attributes' => $_nav->Attributes,
					'limitaccess' => $_nav->LimitAccess,
					'customers' => self::getCustomerData($_nav),
					'depended' => 1
				);

				if($rules){
					$items[(count($items) - 1)]['currentRule'] = we_navigation_rule::getWeNavigationRule(
							'defined_' . ($_dyn['field'] ? $_dyn['field'] : $_dyn['text']), $_nav->ID, $_nav->SelectionType, $_nav->FolderID, $_nav->DocTypeID, $_nav->ClassID, $_nav->CategoryIDs, $_nav->WorkspaceID, $href, false);
				}
			}
		}
		return $items;
	}

	function loopAllRules(/* $id */){
		if(!$this->hasCurrent){
// add defined rules
			$newRules = we_navigation_ruleControl::getAllNavigationRules();

			foreach($newRules as $rule){
				$this->currentRules[] = $rule;
			}

			$this->checkCurrent(/* $this->items['id' . $id]->items */);
		}
	}

	function initFromCache($parentid = 0, $showRoot = true){
		$this->items = array();
		$this->rootItem = $parentid;
		$this->setDefaultTemplates();

		if(isset(self::$cache[$parentid])){
			$this->items = self::$cache[$parentid];
		} else {
			$this->items = we_navigation_cache::getCacheFromFile($parentid);
			if($this->items === false){
				$this->items = array();
				return false;
			}
			self::$cache[$parentid] = $this->items;
		}

		if(is_object($this->items['id' . $parentid])){
			$this->items['id' . $parentid]->type = $showRoot ? ($parentid == 0 ? 'root' : $this->items['id' . $parentid]->type) : 'root';
		}

		$navigationRulesStorage = we_navigation_cache::getCachedRule();
		if($navigationRulesStorage !== false){
			$this->currentRules = unserialize($navigationRulesStorage);
			foreach($this->currentRules as &$rule){ //#Bug 4142
				$rule->renewDB();
			}
		}
		unset($navigationRulesStorage);

		foreach($this->items as &$_item){
			if(method_exists($_item, 'isCurrent')){
				$this->hasCurrent = ($_item->isCurrent($this));
			}
		}
		unset($_item);
		$this->loopAllRules(/* $parentid */);
		return true;
	}

	function initById($parentid = 0, $depth = false, $showRoot = true){
		$this->items = array();
		$this->rootItem = intval($parentid);

		$_navigation = new we_navigation_navigation();

		$this->readItemsFromDb($this->rootItem);

		$_item = $this->getItemFromPool($parentid);

		$_navigation->initByRawData($_item ? $_item : array(
				'ID' => 0, 'Path' => '/'
		));

// set defaultTemplates
		$this->setDefaultTemplates();

		$this->items['id' . $_navigation->ID] = new we_navigation_item(
			$_navigation->ID, $_navigation->LinkID, ($_navigation->IsFolder ? ($_navigation->FolderSelection == we_navigation_navigation::STPYE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE) : (($_navigation->SelectionType == we_navigation_navigation::STPYE_CLASS || $_navigation->SelectionType == we_navigation_navigation::STPYE_OBJLINK) ? OBJECT_FILES_TABLE : FILE_TABLE)), $_navigation->Text, $_navigation->Display, $_navigation->getHref($this->Storage['ids']), $showRoot ? ($_navigation->ID == 0 ? 'root' : ($_navigation->IsFolder ? 'folder' : 'item')) : 'root', $this->id2path($_navigation->IconID), $_navigation->Attributes, $_navigation->LimitAccess, $this->getCustomerData($_navigation), $_navigation->CurrentOnUrlPar, $_navigation->CurrentOnAnker);

		$items = $_navigation->getDynamicPreview($this->Storage, true);

		foreach($items as $_item){

			if(!empty($_item['id'])){
				if(isset($_item['name']) && !empty($_item['name'])){
					$_item['text'] = $_item['name'];
				}
				$this->items['id' . $_item['id']] = new we_navigation_item(
					$_item['id'], $_item['docid'], $_item['table'], $_item['text'], $_item['display'], $_item['href'], $_item['type'], $_item['icon'], $_item['attributes'], $_item['limitaccess'], $_item['customers'], isset($_item['currentonurlpar']) ? $_item['currentonurlpar'] : '', isset($_item['currentonanker']) ? $_item['currentonanker'] : '');

				if(isset($this->items['id' . $_item['parentid']])){
					$this->items['id' . $_item['parentid']]->addItem($this->items['id' . $_item['id']]);
				}

				if($this->items['id' . $_item['id']]->isCurrent($this)){
					$this->hasCurrent = true;
				}

// add currentRules
				if(isset($_item['currentRule'])){
					$this->currentRules[] = $_item['currentRule'];
				}
			}
		}

		$this->loopAllRules(/* $_navigation->ID */);

//make avail in cache
		self::$cache[$parentid] = $this->items;

//reduce Memory consumption!
		$this->Storage = array();
	}

	function checkCategories($idRule, $idDoc){
		$idsRule = makeArrayFromCSV($idRule);

		if(empty($idsRule)){
			return true;
		}

		foreach($idsRule as $rule){
			if(strpos($idDoc, ",$rule,") !== false){
				return true;
			}
		}

		return false;
	}

	function setCurrent($navigationID, $current){
		if(isset($this->items['id' . $navigationID])){
			$this->items['id' . $navigationID]->setCurrent($this, true);
		}
	}

	function checkCurrent(/* &$items */){
		if(!isset($GLOBALS['WE_MAIN_DOC'])){
			return false;
		}

		$_candidate = 0;
		$_score = 3;
		$_len = 0;
		$_curr_len = 0;
		$_ponder = 0;

		$_isObject = (isset($GLOBALS['we_obj']) && isset($GLOBALS['WE_MAIN_DOC']->TableID) && $GLOBALS['WE_MAIN_DOC']->TableID);

		foreach($this->currentRules as $_rule){
			$_ponder = 4;
			$parentPath = '';
			switch($_rule->SelectionType){
				case we_navigation_navigation::STPYE_DOCTYPE:
					if($_rule->DoctypeID){
						if(isset($GLOBALS['WE_MAIN_DOC']->DocType) && ($_rule->DoctypeID == $GLOBALS['WE_MAIN_DOC']->DocType)){
							$_ponder--;
						} else {
							$_ponder = 999; // remove from selection
						}
					}

					if(!$_isObject){
						$parentPath = $this->id2path($_rule->FolderID);

						if(!empty($parentPath) && $parentPath != '/'){
							$parentPath .= '/';
						}
					}
					break;

				case we_navigation_navigation::STPYE_CLASS:
					if($_rule->ClassID){
						if(isset($GLOBALS['WE_MAIN_DOC']->TableID) && ($GLOBALS["WE_MAIN_DOC"]->TableID == $_rule->ClassID)){
							$_ponder--;
						} else {
							$_ponder = 999; // remove from selection
						}
					}

					if($_isObject){
						$parentPath = rtrim($this->id2path($_rule->WorkspaceID), '/') . '/';
					}
					break;
			}


			if(!empty($parentPath) && strpos($GLOBALS['WE_MAIN_DOC']->Path, $parentPath) === 0){
				$_ponder--;
				$_curr_len = strlen($parentPath);
				if($_curr_len > $_len){
					$_len = $_curr_len;
					$_ponder--;
				}
			}

			$_cats = makeArrayFromCSV($_rule->Categories);
			if(!empty($_cats)){
				if($this->checkCategories($_rule->Categories, $GLOBALS['WE_MAIN_DOC']->Category)){
					$_ponder--;
				} else {
					$_ponder = 999; // remove from selection
				}
			}

			if($_ponder == 0){
				$this->setCurrent($_rule->NavigationID, $_rule->SelfCurrent);
				return true;
			} elseif($_ponder <= $_score){
				if(NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH){
					$this->setCurrent($_rule->NavigationID, null);
				} else {
					$_score = $_ponder;
					$_candidate = $_rule->NavigationID;
				}
			}
		}
		if($_candidate != 0){
			$this->setCurrent($_candidate, null);
			return true;
		}

		return false;
	}

	function getItemIds($id){
		$items = array($id);

		foreach($this->items[$id]->items as $key => $val){
			if($val->type == 'folder'){
				$items = array_merge($items, $this->getItemIds($key));
			} else {
				$items[] = $key;
			}
		}

		return $items;
	}

	function getItems($id = false){
		return ($id ?
				$this->getItemIds($id) :
				array_keys($this->items));
	}

	function getItem($id){
		return isset($this->items[$id]) ? $this->items[$id] : false;
	}

	function getTemplate(we_navigation_item $item){
		if(!isset($this->templates[$item->type])){
			return $this->getDefaultTemplate($item);
		}
		$currentPos = we_navigation_item::$currentPosition[$item->level];
// get correct Level
		$useTemplate = $this->templates[$item->type][(isset($this->templates[$item->type][$item->level]) ? $item->level : self::TEMPLATE_DEFAULT_LEVEL)];
// get correct position
		if(isset($useTemplate[$item->current])){
			$useTemplate = $useTemplate[$item->current];
		} elseif(isset($useTemplate[self::TEMPLATE_DEFAULT_CURRENT])){
			$useTemplate = $useTemplate[self::TEMPLATE_DEFAULT_CURRENT];
		} else {
			return $this->getDefaultTemplate($item);
		}

// is last entry??
		if(isset($useTemplate['last']) &&
// check if item is last
			((count($this->items['id' . $item->parentid]->items)) == $currentPos)){
			return $useTemplate['last'];
		}

		if(isset($useTemplate[$currentPos])){
			return $useTemplate[$currentPos];
		}

		if(isset($useTemplate['odd']) && $currentPos % 2 === 1){
			return $useTemplate['odd'];
		}

		if(isset($useTemplate['even']) && $currentPos % 2 === 0){
			return $useTemplate['even'];
		}

		if(isset($useTemplate[self::TEMPLATE_DEFAULT_POSITION])){
			return $useTemplate[self::TEMPLATE_DEFAULT_POSITION];
		}

		return $this->getDefaultTemplate($item);
	}

	function setDefaultTemplates(){
// the default templates should look like this
//			$folderTemplate = '<li><a href="<we:navigationField name="href">"><we:navigationField name="text"></a><ul><we:navigationEntries /></ul></li>';
//			$itemTemplate = '<li><a href="<we:navigationField name="href">"><we:navigationField name="text"></a></li>';
//			$rootTemplate = '<we:navigationEntries />';

		$this->setTemplate('<li><a href="<?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "href")) . '); ?>"><?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "text")) . '); ?></a><?php if(' . we_tag_tagParser::printTag('ifHasEntries') . '){ ?><ul><?php printElement( ' . we_tag_tagParser::printTag('navigationEntries') . '); ?></ul><?php } ?></li>', 'folder', self::TEMPLATE_DEFAULT_LEVEL, self::TEMPLATE_DEFAULT_CURRENT, self::TEMPLATE_DEFAULT_POSITION);
		$this->setTemplate('<li><a href="<?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "href")) . '); ?>"><?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "text")) . '); ?></a></li>', 'item', self::TEMPLATE_DEFAULT_LEVEL, self::TEMPLATE_DEFAULT_CURRENT, self::TEMPLATE_DEFAULT_POSITION);
		$this->setTemplate('<?php printElement( ' . we_tag_tagParser::printTag('navigationEntries') . '); ?>', 'root', self::TEMPLATE_DEFAULT_LEVEL, self::TEMPLATE_DEFAULT_CURRENT, self::TEMPLATE_DEFAULT_POSITION);
	}

	function getDefaultTemplate($item){
		return $this->templates[$item->type][self::TEMPLATE_DEFAULT_LEVEL][self::TEMPLATE_DEFAULT_CURRENT][self::TEMPLATE_DEFAULT_POSITION];
	}

	function writeNavigation($depth = false){
		$GLOBALS['weNavigationObject'] = &$this;

		if(isset($this->items['id' . $this->rootItem]) && ($this->items['id' . $this->rootItem] instanceof we_navigation_item)){
			if($this->items['id' . $this->rootItem]->type == 'folder' && $depth !== false){
// if initialised by id => root item is on lvl0 -> therefore decrease depth
// this is to make it equal init by id, parentid
				$depth--;
			}
			we_navigation_item::$currentPosition = array();
			return $this->items['id' . $this->rootItem]->writeItem($this, $depth);
		}

		return '';
	}

	function setTemplate($content, $type, $level, $current, $position){
		$this->templates[$type][$level][$current][$position] = $content;
	}

	function readItemsFromDb($id){
		$this->Storage['items'] = array();
		$this->Storage['ids'] = array();

		$_pathArr = id_to_path($id, NAVIGATION_TABLE, null, false, true);
		$_path = isset($_pathArr[0]) ? $_pathArr[0] : "";

		$_db = new DB_WE();

		$_path = clearPath($_path . '/%');

		$_ids = array();

		$_db->query('SELECT * FROM ' . NAVIGATION_TABLE . ' WHERE Path LIKE "' . $_db->escape($_path) . '" ' . ($id ? ' OR ID=' . intval($id) : '') . ' ORDER BY Ordn');
		while($_db->next_record()){
			$_tmpItem = $_db->getRecord();
			$_tmpItem["Name"] = $_tmpItem["Text"];
			$this->Storage['items'][] = $_tmpItem;
			unset($_tmpItem);

			if($_db->Record['IsFolder'] == 1 && ($_db->Record['FolderSelection'] == '' || $_db->Record['FolderSelection'] == we_navigation_navigation::STPYE_DOCLINK)){
				$_ids[] = $_db->Record['LinkID'];
			} elseif($_db->Record['Selection'] == we_navigation_navigation::SELECTION_STATIC && $_db->Record['SelectionType'] == we_navigation_navigation::STPYE_DOCLINK){
				$_ids[] = $_db->Record['LinkID'];
			} elseif(($_db->Record['SelectionType'] == we_navigation_navigation::STPYE_CATEGORY || $_db->Record['SelectionType'] == we_navigation_navigation::STPYE_CATLINK) && $_db->Record['LinkSelection'] != 'extern'){
				$_ids[] = $_db->Record['UrlID'];
			}

			if(!empty($_db->Record['IconID'])){
				$_ids[] = $_db->Record['IconID'];
			}
		}

		if(!empty($_ids)){
			array_unique($_ids);
			$_db->query('SELECT ID,Path FROM ' . FILE_TABLE . ' WHERE ID IN(' . implode(',', $_ids) . ') ORDER BY ID');
			while($_db->next_record()){
				$this->Storage['ids'][$_db->f('ID')] = $_db->f('Path');
			}
		}
	}

	function getItemFromPool($id){
		foreach($this->Storage['items'] as $item){
			if($item['ID'] == $id){
				return $item;
			}
		}

		return null;
	}

	function id2path($id){
		if(isset($this->Storage['ids'][$id])){
			return $this->Storage['ids'][$id];
		}
		$_path = id_to_path($id, FILE_TABLE);
		$this->Storage['ids'][$id] = $_path;
		return $_path;
	}

}
