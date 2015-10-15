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

	static function getCustomerData(we_navigation_navigation $navi){
		//FIXME: check if we need this csv/unserialize code any more
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

		return ($navi->LimitAccess ?
				array(
				'id' => $navi->AllCustomers == 0 ? $navi->Customers : array(),
				'filter' => $navi->ApplyFilter == 1 ? $navi->CustomerFilter : array(),
				'blacklist' => $navi->ApplyFilter == 1 ? $navi->BlackList : array(),
				'whitelist' => $navi->ApplyFilter == 1 ? $navi->WhiteList : array(),
				'usedocumentfilter' => $navi->UseDocumentFilter ? 1 : 0
				) :
				array(
				'id' => '',
				'filter' => '',
				'blacklist' => '',
				'whitelist' => '',
				'usedocumentfilter' => 1
		));
	}

	function initByNavigationObject($showRoot = true){
		$this->items = array();
		$navigation = $_SESSION['weS']['navigation_session'];

		$this->rootItem = $navigation->ID;

// set defaultTemplates
		$this->setDefaultTemplates();

		$this->readItemsFromDb($this->rootItem);
		list($table, $linkid) = $navigation->getTableIdForItem();
		$this->items['id' . $navigation->ID] = new we_navigation_item($navigation->ID, $linkid, $table, $navigation->Text, $navigation->Display, $navigation->getHref($navigation->SelectionType, $navigation->LinkID, $navigation->Url, $navigation->Parameter, $navigation->WorkspaceID), ($showRoot ? we_base_ContentTypes::FOLDER : 'root'), $this->id2path($navigation->IconID), $navigation->Attributes, $navigation->LimitAccess, self::getCustomerData($navigation), $navigation->CurrentOnUrlPar, $navigation->CurrentOnAnker);

		$items = $navigation->getDynamicPreview($this->Storage);

		$_new_items = $this->getStaticSavedDynamicItems($navigation);

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

	private function getStaticSavedDynamicItems(we_navigation_navigation $_nav, $rules = false){
		$items = array();
		$dyn_items = $_nav->getDynamicEntries();
		if(is_array($dyn_items)){
			foreach($dyn_items as $_dyn){

				$href = id_to_path($_dyn['id']);
				$items[] = array(
					'id' => $_dyn['id'],
					'text' => isset($_dyn['field']) && $_dyn['field'] ? $_dyn['field'] : $_dyn['text'],
					'display' => isset($_dyn['display']) && $_dyn['display'] ? $_dyn['display'] : '',
					'name' => isset($_dyn['field']) && $_dyn['field'] ? $_dyn['field'] : (isset($_dyn['name']) && $_dyn['name'] ? $_dyn['name'] : $_dyn['text']),
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
							'defined_' . ($_dyn['field'] ? : $_dyn['text']), $_nav->ID, $_nav->SelectionType, $_nav->FolderID, $_nav->DocTypeID, $_nav->ClassID, $_nav->CategoryIDs, $_nav->WorkspaceID, $href, false);
				}
			}
		}
		return $items;
	}

	function loopAllRules(){
		if(!$this->hasCurrent){
// add defined rules
			$newRules = we_navigation_ruleControl::getAllNavigationRules();

			foreach($newRules as $rule){
				$this->currentRules[] = $rule;
			}

			$this->checkCurrent();
		}
	}

	function initFromCache($parentid = 0, $showRoot = true){
		$this->rootItem = $parentid;
		$this->setDefaultTemplates();

		if(isset(self::$cache[$parentid])){
			$this->items = self::$cache[$parentid];
		} elseif(($this->items = we_navigation_cache::getCacheFromFile($parentid)) === false){
			$this->items = array();
			return false;
		} else {
			self::$cache[$parentid] = $this->items;
		}

		if(is_object($this->items['id' . $parentid])){
			$this->items['id' . $parentid]->type = !$showRoot || $parentid == 0 ? 'root' : we_base_ContentTypes::FOLDER;
		}
		$navigationRulesStorage = we_navigation_cache::getCachedRule();
		if($navigationRulesStorage !== false){
			$this->currentRules = unserialize($navigationRulesStorage);
		}
		unset($navigationRulesStorage);

		foreach($this->items as &$_item){
			if(is_object($_item) && method_exists($_item, 'isCurrent')){
				$this->hasCurrent |= ($_item->isCurrent($this));
			}
		}
		unset($_item);
		$this->loopAllRules();
		return true;
	}

	function initById($parentid = 0, $showRoot = true){
		$this->items = array();
		$this->rootItem = intval($parentid);

		$_navigation = new we_navigation_navigation();

		$this->readItemsFromDb($this->rootItem);

		$_item = $this->getItemFromPool($parentid);

		$_navigation->initByRawData($_item ? : array(
				'ID' => 0, 'Path' => '/'
		));

// set defaultTemplates
		$this->setDefaultTemplates();
		list($table, $linkid) = $_navigation->getTableIdForItem();
		$this->items['id' . $_navigation->ID] = new we_navigation_item(
			$_navigation->ID, $linkid, $table, $_navigation->Text, $_navigation->Display, $_navigation->getHref($this->Storage['ids']), (!$showRoot || $_navigation->ID == 0 ? 'root' : ($_navigation->IsFolder ? we_base_ContentTypes::FOLDER : 'item')), $this->id2path($_navigation->IconID), $_navigation->Attributes, $_navigation->LimitAccess, self::getCustomerData($_navigation), $_navigation->CurrentOnUrlPar, $_navigation->CurrentOnAnker);

		$items = $_navigation->getDynamicPreview($this->Storage, true);

		foreach($items as $_item){
			if($_item['id']){
				if(isset($_item['name']) && $_item['name']){
					$_item['text'] = $_item['name'];
				}
				$this->items['id' . $_item['id']] = new we_navigation_item($_item['id'], $_item['docid'], $_item['table'], $_item['text'], $_item['display'], $_item['href'], $_item['type'], $_item['icon'], $_item['attributes'], $_item['limitaccess'], $_item['customers'], isset($_item['currentonurlpar']) ? $_item['currentonurlpar'] : '', isset($_item['currentonanker']) ? $_item['currentonanker'] : '', $_item['currentoncat'], $_item['catparam']);

				if(isset($this->items['id' . $_item['parentid']])){
					$this->items['id' . $_item['parentid']]->addItem($this->items['id' . $_item['id']]);
				}

				$this->hasCurrent |= ($this->items['id' . $_item['id']]->isCurrent($this));

// add currentRules
				if(isset($_item['currentRule'])){
					$this->currentRules[] = $_item['currentRule'];
				}
			}
		}

		$this->loopAllRules();

//make avail in cache
		self::$cache[$parentid] = $this->items;

//reduce Memory consumption!
		$this->Storage = array();
	}

	private function checkCategories(array $idsRule, array $idDoc){
		if(!$idsRule){
			return true;
		}
		$diff = array_intersect($idDoc, $idsRule);
		return !empty($diff);
	}

	function setCurrent($navigationID){
		if(isset($this->items['id' . $navigationID])){
			$this->items['id' . $navigationID]->setCurrent($this, true);
		}
	}

	private function checkCurrent(){
		if(!isset($GLOBALS['WE_MAIN_DOC'])){
			return false;
		}

		$candidate = 0;
		$_score = 3;
		$_len = 0;
		$_curr_len = 0;
		$ponder = 0;

		$_isObject = (isset($GLOBALS['we_obj']) && !$GLOBALS['WE_MAIN_DOC']->IsFolder);
		$main_cats = array_filter(explode(',', $GLOBALS['WE_MAIN_DOC']->Category));

		foreach($this->currentRules as $_rule){
			$ponder = 4;
			$parentPath = '';
			switch($_rule->SelectionType){ // FIXME: why not use continue instead of $ponder = 999?
				case we_navigation_navigation::STPYE_DOCTYPE:
					if($_isObject){
						continue; // remove from selection
					}
					if($_rule->DoctypeID){
						if(empty($GLOBALS['WE_MAIN_DOC']->DocType) || ($_rule->DoctypeID != $GLOBALS['WE_MAIN_DOC']->DocType)){
							continue;
						}
						$ponder--;
					}

					$parentPath = $this->id2path($_rule->FolderID);
					if($parentPath && $parentPath != '/'){
						$parentPath .= '/';
					}
					break;

				case we_navigation_navigation::STPYE_CLASS:
					if(!$_isObject){
						continue; // remove from selection
					}
					if($_rule->ClassID){
						if($GLOBALS["WE_MAIN_DOC"]->TableID != $_rule->ClassID){
							continue; // remove from selection
						}
						$ponder--;
					}

					$parentPath = rtrim($this->id2path($_rule->WorkspaceID), '/') . '/';
					break;
			}

			if(!empty($parentPath) && strpos($GLOBALS['WE_MAIN_DOC']->Path, $parentPath) === 0){
				$ponder--;
				$_curr_len = strlen($parentPath);
				if($_curr_len > $_len){
					$_len = $_curr_len;
					$ponder--;
				}
			}

			if(($cats = makeArrayFromCSV($_rule->Categories))){
				if($this->checkCategories($cats, $main_cats)){
					$ponder--;
				} else {
					continue; // remove from selection
				}
			}

			if($ponder <= $_score){
				if(NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH){
					$this->setCurrent($_rule->NavigationID);
				} else {
					$_score = $ponder;
					$candidate = $_rule->NavigationID;
				}
			}
		}

		if($candidate){
			$this->setCurrent($candidate);
			return true;
		}

		return false;
	}

	function getItemIds($id){
		$items = array($id);

		foreach($this->items[$id]->items as $key => $val){
			if($val->type == we_base_ContentTypes::FOLDER){
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

	private function setDefaultTemplates(){
// the default templates should look like this
//			$folderTemplate = '<li><a href="<we:navigationField name="href">"><we:navigationField name="text"></a><ul><we:navigationEntries /></ul></li>';
//			$itemTemplate = '<li><a href="<we:navigationField name="href">"><we:navigationField name="text"></a></li>';
//			$rootTemplate = '<we:navigationEntries />';

		$this->setTemplate('<li><a href="<?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "href")) . '); ?>"><?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "text")) . '); ?></a><?php if(' . we_tag_tagParser::printTag('ifHasEntries') . '){ ?><ul><?php printElement( ' . we_tag_tagParser::printTag('navigationEntries') . '); ?></ul><?php } ?></li>', we_base_ContentTypes::FOLDER, self::TEMPLATE_DEFAULT_LEVEL, self::TEMPLATE_DEFAULT_CURRENT, self::TEMPLATE_DEFAULT_POSITION);
		$this->setTemplate('<li><a href="<?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "href")) . '); ?>"><?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "text")) . '); ?></a></li>', 'item', self::TEMPLATE_DEFAULT_LEVEL, self::TEMPLATE_DEFAULT_CURRENT, self::TEMPLATE_DEFAULT_POSITION);
		$this->setTemplate('<?php printElement( ' . we_tag_tagParser::printTag('navigationEntries') . '); ?>', 'root', self::TEMPLATE_DEFAULT_LEVEL, self::TEMPLATE_DEFAULT_CURRENT, self::TEMPLATE_DEFAULT_POSITION);
	}

	private function getDefaultTemplate($item){
		return $this->templates[$item->type][self::TEMPLATE_DEFAULT_LEVEL][self::TEMPLATE_DEFAULT_CURRENT][self::TEMPLATE_DEFAULT_POSITION];
	}

	function writeNavigation($depth = false){
		$GLOBALS['weNavigationObject'] = &$this;

		if(isset($this->items['id' . $this->rootItem]) && ($this->items['id' . $this->rootItem] instanceof we_navigation_item)){
			if($this->items['id' . $this->rootItem]->type == we_base_ContentTypes::FOLDER && $depth !== false){
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
		$_path = we_base_file::clearPath((isset($_pathArr[0]) ? $_pathArr[0] : '') . '/%');

		$_ids = array();

		$_db = new DB_WE();
		$_db->query('SELECT * FROM ' . NAVIGATION_TABLE . ' WHERE Path LIKE "' . $_db->escape($_path) . '" ' . ($id ? ' OR ID=' . intval($id) : '') . ' ORDER BY Ordn');
		while($_db->next_record()){
			$_tmpItem = $_db->getRecord();
			$_tmpItem['Name'] = $_tmpItem['Text'];
			$this->Storage['items'][] = $_tmpItem;

			if($_db->Record['IsFolder'] == 1 && ($_db->Record['FolderSelection'] === '' || $_db->Record['FolderSelection'] == we_navigation_navigation::STPYE_DOCLINK)){
				$_ids[] = $_db->Record['LinkID'];
			} elseif($_db->Record['Selection'] == we_navigation_navigation::SELECTION_STATIC && $_db->Record['SelectionType'] == we_navigation_navigation::STPYE_DOCLINK){
				$_ids[] = $_db->Record['LinkID'];
			} elseif(($_db->Record['SelectionType'] == we_navigation_navigation::STPYE_CATEGORY || $_db->Record['SelectionType'] == we_navigation_navigation::STPYE_CATLINK) && $_db->Record['LinkSelection'] != we_navigation_navigation::LSELECTION_EXTERN){
				$_ids[] = $_db->Record['UrlID'];
			}

			if($_db->Record['IconID']){
				$_ids[] = $_db->Record['IconID'];
			}
		}

		if($_ids){
			array_unique($_ids);
			$_db->query('SELECT ID,Path FROM ' . FILE_TABLE . ' WHERE ID IN(' . implode(',', $_ids) . ') ORDER BY ID');
			$this->Storage['ids'] = $_db->getAllFirst(false, MYSQL_ASSOC);
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


		return ($this->Storage['ids'][$id] = id_to_path($id, FILE_TABLE));
	}

}
