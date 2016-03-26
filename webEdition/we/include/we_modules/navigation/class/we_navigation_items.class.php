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
	private static $Storage = array(
		'items' => array(),
		'ids' => array(),
	);

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
			$navi->CustomerFilter = we_unserialize($navi->CustomerFilter);
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

	private function initRulesFromDB(){
		$newRules = we_navigation_ruleControl::getAllNavigationRules();

		foreach($newRules as $rule){
			$this->currentRules[] = $rule;
		}
		we_navigation_cache::saveRules($this->currentRules);
	}

	private function loopAllRules(){
		if(!$this->hasCurrent){
			$this->checkCurrent();
		}
	}

	public function init($parentid = 0, $showRoot = true){
		if(!$this->initFromCache($parentid, $showRoot)){
			//make sure we use cache next time!
			$this->initById($parentid, $showRoot);
			we_navigation_cache::saveCacheNavigation($parentid, $this);
		}
	}

	private function initFromCache($parentid = 0, $showRoot = true){
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
		$this->currentRules = we_navigation_cache::getCachedRule();
		if($this->currentRules === false){
			$this->currentRules = array();
			$this->initRulesFromDB();
		}

		foreach($this->items as &$_item){
			if(is_object($_item) && method_exists($_item, 'isCurrent')){
				$this->hasCurrent |= ($_item->isCurrent($this));
			}
		}
		unset($_item);
		$this->loopAllRules();
		return true;
	}

	private function initById($parentid = 0, $showRoot = true){
		$this->items = array();
		$this->rootItem = intval($parentid);
		$_navigation = new we_navigation_navigation();
		$this->readItemsFromDb($this->rootItem);
		$_item = self::getItemFromPool($parentid);
		$_navigation->initByRawData($_item ? : array('ID' => 0, 'Path' => '/'));

// set defaultTemplates
		$this->setDefaultTemplates();
		list($table, $linkid) = $_navigation->getTableIdForItem();
		$this->items['id' . $_navigation->ID] = new we_navigation_item(
			$_navigation->ID, $linkid, $table, $_navigation->Text, $_navigation->Display, $_navigation->getHref(), (!$showRoot || $_navigation->ID == 0 ? 'root' : ($_navigation->IsFolder ? we_base_ContentTypes::FOLDER : 'item')), self::id2path($_navigation->IconID), $_navigation->Attributes, $_navigation->LimitAccess, self::getCustomerData($_navigation), $_navigation->CurrentOnUrlPar, $_navigation->CurrentOnAnker);

		$items = $_navigation->getDynamicPreview(self::$Storage['items'], true);

		foreach($items as $_item){
			if($_item['id']){
				if(!empty($_item['name'])){
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

		$this->initRulesFromDB();
		$this->loopAllRules();

//make avail in cache
		self::$cache[$parentid] = $this->items;

//reduce Memory consumption!
		self::$Storage['items'] = array();
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
		
		$currentWorksapce = $_isObject ? //webEdition object
			(defined('WE_REDIRECTED_SEO') ? //webEdition object uses SEO-URL
				we_objectFile::getNextDynDoc(($path = rtrim(substr(WE_REDIRECTED_SEO, 0, strripos(WE_REDIRECTED_SEO, $GLOBALS['WE_MAIN_DOC']->Url)), '/')), '', $GLOBALS['WE_MAIN_DOC']->Workspaces, $GLOBALS['WE_MAIN_DOC']->ExtraWorkspacesSelected, $GLOBALS['DB_WE']) :
				parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH)	
			) : //webEdition document
			$GLOBALS['WE_MAIN_DOC']->Path;

		foreach($this->currentRules as $rule){
			$ponder = 4;
			$parentPath = '';
			switch($rule->SelectionType){ // FIXME: why not use continue instead of $ponder = 999?
				case we_navigation_navigation::STYPE_DOCTYPE:
					if($_isObject){
						continue; // remove from selection
					}
					if($rule->DoctypeID){
						if(empty($GLOBALS['WE_MAIN_DOC']->DocType) || ($rule->DoctypeID != $GLOBALS['WE_MAIN_DOC']->DocType)){
							continue;
						}
						$ponder--;
					}

					$parentPath = self::id2path($rule->FolderID);
					if($parentPath && $parentPath != '/'){
						$parentPath .= '/';
					}
					break;

				case we_navigation_navigation::STYPE_CLASS:
					if(!$_isObject){
						continue; // remove from selection
					}
					if($rule->ClassID){
						if($GLOBALS["WE_MAIN_DOC"]->TableID != $rule->ClassID){
							continue; // remove from selection
						}
						$ponder--;
					}

					$parentPath = rtrim(self::id2path($rule->WorkspaceID), '/') . '/';
					break;
			}

			if(!empty($parentPath) && strpos($currentWorksapce, $parentPath) === 0){
				$ponder--;
				$_curr_len = strlen($parentPath);
				if($_curr_len > $_len){
					$_len = $_curr_len;
					$ponder--;
				}
			}

			if(($cats = makeArrayFromCSV($rule->Categories))){
				if($this->checkCategories($cats, $main_cats)){
					$ponder--;
				} else {
					continue; // remove from selection
				}
			}

			if($ponder <= $_score){
				if(NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH){
					$this->setCurrent($rule->NavigationID);
				} else {
					$_score = $ponder;
					$candidate = $rule->NavigationID;
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
		if($position === 'first'){
			$position = 1;
		}
		$this->templates[$type][$level][$current][$position] = $content;
	}

	function readItemsFromDb($id){
		$_pathArr = id_to_path($id, NAVIGATION_TABLE, null, false, true);
		$_path = we_base_file::clearPath((isset($_pathArr[0]) ? $_pathArr[0] : '') . '/%');

		$_ids = array();

		$_db = new DB_WE();
		$_db->query('SELECT * FROM ' . NAVIGATION_TABLE . ' WHERE Path LIKE "' . $_db->escape($_path) . '" ' . ($id ? ' OR ID=' . intval($id) : '') . ' ORDER BY Ordn');
		while($_db->next_record()){
			$_tmpItem = $_db->getRecord();
			$_tmpItem['Name'] = $_tmpItem['Text'];
			self::$Storage['items'][] = $_tmpItem;

			if($_db->Record['IsFolder'] && ($_db->Record['FolderSelection'] === '' || $_db->Record['FolderSelection'] == we_navigation_navigation::STYPE_DOCLINK)){
				$_ids[] = $_db->Record['LinkID'];
			} elseif($_db->Record['Selection'] == we_navigation_navigation::SELECTION_STATIC && $_db->Record['SelectionType'] == we_navigation_navigation::STYPE_DOCLINK){
				$_ids[] = $_db->Record['LinkID'];
			} elseif(($_db->Record['SelectionType'] == we_navigation_navigation::STYPE_CATEGORY || $_db->Record['SelectionType'] == we_navigation_navigation::STYPE_CATLINK) && $_db->Record['LinkSelection'] != we_navigation_navigation::LSELECTION_EXTERN){
				$_ids[] = $_db->Record['UrlID'];
			}

			if($_db->Record['IconID']){
				$_ids[] = $_db->Record['IconID'];
			}
		}
		$_ids = $_ids ? array_diff(array_unique($_ids), array_keys(self::$Storage['ids'])) : array();
		if($_ids){
			$_db->query('SELECT ID,IF(Published>0,Path,"") FROM ' . FILE_TABLE . ' WHERE ID IN(' . implode(',', $_ids) . ') ORDER BY ID');
			//keep array index
			self::$Storage['ids'] = self::$Storage['ids'] + $_db->getAllFirst(false);
		}
	}

	private static function getItemFromPool($id){
		foreach(self::$Storage['items'] as $item){
			if($item['ID'] == $id){
				return $item;
			}
		}

		return null;
	}

	public static function id2path($id){
		if(!$id){
			return '/';
		}
		if(isset(self::$Storage['ids'][$id])){
			return self::$Storage['ids'][$id];
		}
		return (self::$Storage['ids'][$id] = id_to_path($id, FILE_TABLE));
	}

}
