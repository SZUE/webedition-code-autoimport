<?php

/**
 * webEdition CMS
 *
 * collection of the navigation items
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
 *
 * @category	webEdition
 * @package		none
 * @license		http://www.gnu.org/copyleft/gpl.html  GPL
 * @version    	SVN: $Id$
 */
class we_navigation_items{
	const TEMPLATE_DEFAULT_CURRENT = 'defaultCurrent';
	const TEMPLATE_DEFAULT_POSITION = 'defaultPosition';
	const TEMPLATE_DEFAULT_LEVEL = 'defaultLevel';

	private static $cache = [];
	var $items;
	var $templates;
	var $rootItem = 0;
	var $hasCurrent = false;
	var $currentRules = [];
	private static $Storage = [
		'items' => [],
		'ids' => [0 => '/'],
	];

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
			['id' => $navi->AllCustomers == 0 ? $navi->Customers : [],
			'filter' => $navi->ApplyFilter == 1 ? $navi->CustomerFilter : [],
			'blacklist' => $navi->ApplyFilter == 1 ? $navi->BlackList : [],
			'whitelist' => $navi->ApplyFilter == 1 ? $navi->WhiteList : [],
			'usedocumentfilter' => $navi->UseDocumentFilter ? 1 : 0
			] :
			['id' => '',
			'filter' => '',
			'blacklist' => '',
			'whitelist' => '',
			'usedocumentfilter' => 1
		]);
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
			$this->items = [];
			return false;
		} else {
			self::$cache[$parentid] = $this->items;
		}

		if(!empty($this->items['id' . $parentid]) && is_object($this->items['id' . $parentid])){
			$this->items['id' . $parentid]->type = !$showRoot || $parentid == 0 ? 'root' : we_base_ContentTypes::FOLDER;
		}
		$this->currentRules = we_navigation_cache::getCachedRule();
		if($this->currentRules === false){
			$this->currentRules = [];
			$this->initRulesFromDB();
		}

		foreach($this->items as &$item){
			if(is_object($item) && method_exists($item, 'isCurrent')){
				$this->hasCurrent |= ($item->isCurrent($this));
			}
		}
		unset($item);
		$this->loopAllRules();
		return true;
	}

	private function initById($parentid = 0, $showRoot = true){
		$this->items = [];
		$this->rootItem = intval($parentid);
		$navigation = new we_navigation_navigation();
		$this->readItemsFromDb($this->rootItem);
		$item = self::getItemFromPool($parentid);
		$navigation->initByRawData($item ?: ['ID' => 0, 'Path' => '/']);

// set defaultTemplates
		$this->setDefaultTemplates();
		list($table, $linkid) = $navigation->getTableIdForItem();
		$this->items['id' . $navigation->ID] = new we_navigation_item(
			$navigation->ID, $linkid, $table, $navigation->Text, $navigation->Display, $navigation->getHref(), (!$showRoot || $navigation->ID == 0 ? 'root' : ($navigation->IsFolder ? we_base_ContentTypes::FOLDER : 'item')), self::id2path($navigation->IconID), $navigation->Attributes, $navigation->LimitAccess, self::getCustomerData($navigation), $navigation->CurrentOnUrlPar, $navigation->CurrentOnAnker);

		$items = $navigation->getDynamicPreview(self::$Storage['items'], true);

		foreach($items as $item){
			if($item['id']){
				if(!empty($item['name'])){
					$item['text'] = $item['name'];
				}
				$this->items['id' . $item['id']] = new we_navigation_item($item['id'], $item['docid'], $item['table'], $item['text'], $item['display'], $item['href'], $item['type'], $item['icon'], $item['attributes'], $item['limitaccess'], $item['customers'], isset($item['currentonurlpar']) ? $item['currentonurlpar'] : '', isset($item['currentonanker']) ? $item['currentonanker'] : '', $item['currentoncat'], $item['catparam']);

				if(isset($this->items['id' . $item['parentid']])){
					$this->items['id' . $item['parentid']]->addItem($this->items['id' . $item['id']]);
				}

				$this->hasCurrent |= ($this->items['id' . $item['id']]->isCurrent($this));

// add currentRules
				if(isset($item['currentRule'])){
					$this->currentRules[] = $item['currentRule'];
				}
			}
		}

		$this->initRulesFromDB();
		$this->loopAllRules();

//make avail in cache
		self::$cache[$parentid] = $this->items;

//reduce Memory consumption!
		self::$Storage['items'] = [];
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
			$this->items['id' . $navigationID]->setCurrent($this);
		}
	}

	private function checkCurrent(){
		if(!isset($GLOBALS['WE_MAIN_DOC'])){
			return false;
		}

		$candidate = 0;
		$score = 3;
		$pathLen = 0;

		$isObject = (isset($GLOBALS['we_obj']) && !$GLOBALS['WE_MAIN_DOC']->IsFolder);
		$mainCats = array_filter(explode(',', $GLOBALS['WE_MAIN_DOC']->Category));

		$currentWorkspace = $isObject ? //webEdition object
			(defined('WE_REDIRECTED_SEO') ? //webEdition object uses SEO-URL
			substr(WE_REDIRECTED_SEO, 0, strripos(WE_REDIRECTED_SEO, $GLOBALS['we_obj']->Url)) :
			parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH)
			) : //webEdition document
			$GLOBALS['WE_MAIN_DOC']->Path;

		foreach($this->currentRules as $rule){
			$ponder = 4;
			$rulePath = '';

			if(($cats = makeArrayFromCSV($rule->Categories))){
				if(!$this->checkCategories($cats, $mainCats)){
					continue; // remove from selection
				}
				$ponder--;
			}

			switch($rule->SelectionType){
				case we_navigation_navigation::DYN_DOCTYPE:
					if($isObject){
						continue; // remove from selection
					}
					if($rule->DoctypeID){
						if(empty($GLOBALS['WE_MAIN_DOC']->DocType) || ($rule->DoctypeID != $GLOBALS['WE_MAIN_DOC']->DocType)){
							continue;
						}
						$ponder--;
					}

					if($rule->FolderID){
						$rulePath = rtrim(self::id2path($rule->FolderID), '/') . '/';
					}
					break;
				case we_navigation_navigation::DYN_CLASS:
					if(!$isObject){
						continue; // remove from selection
					}
					if($rule->ClassID){
						if($GLOBALS["we_obj"]->TableID != $rule->ClassID){
							continue; // remove from selection
						}
						$ponder--;
					}

					if($rule->WorkspaceID){
						$rulePath = rtrim(self::id2path($rule->WorkspaceID), '/') . '/';
					}
					break;
			}

			if(!empty($rulePath) && strpos($currentWorkspace, $rulePath) !== false){
				$ponder--;
				if(($currPathLen = strlen($rulePath)) >= $pathLen){ //the longest path wins
					if($pathLen > 0){//no ponder for first match
						$ponder--;
					}
					$pathLen = $currPathLen;
				}
			}

			if($ponder < $score){
				if(NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH){
					$this->setCurrent($rule->NavigationID);
				} else {
					$score = $ponder;
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
		$items = [$id];

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

	/**
	 * @param we_navigation_item $item
	 * @return mixed
	 */
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

	/**
	 * 	the default templates should look like this
	 * 	$folderTemplate = '<li><a href="<we:navigationField name="href">"><we:navigationField name="text"></a><ul><we:navigationEntries /></ul></li>';
	 * 	$itemTemplate = '<li><a href="<we:navigationField name="href">"><we:navigationField name="text"></a></li>';
	 * 	$rootTemplate = '<we:navigationEntries />';
	 */
	private function setDefaultTemplates(){
		if(empty($this->templates)){
			$this->setTemplate('<li><a href="<?php printElement( ' . we_tag_tagParser::printTag('navigationField', ['name' => "href"]) . '); ?>"><?php printElement( ' . we_tag_tagParser::printTag('navigationField', [
					'name' => "text"]) . '); ?></a><?php if(' . we_tag_tagParser::printTag('ifHasEntries') . '){ ?><ul><?php printElement( ' . we_tag_tagParser::printTag('navigationEntries') . '); ?></ul><?php } ?></li>', we_base_ContentTypes::FOLDER, self::TEMPLATE_DEFAULT_LEVEL, self::TEMPLATE_DEFAULT_CURRENT, self::TEMPLATE_DEFAULT_POSITION);
			$this->setTemplate('<li><a href="<?php printElement( ' . we_tag_tagParser::printTag('navigationField', ['name' => "href"]) . '); ?>"><?php printElement( ' . we_tag_tagParser::printTag('navigationField', [
					'name' => "text"]) . '); ?></a></li>', 'item', self::TEMPLATE_DEFAULT_LEVEL, self::TEMPLATE_DEFAULT_CURRENT, self::TEMPLATE_DEFAULT_POSITION);
			$this->setTemplate('<?php printElement( ' . we_tag_tagParser::printTag('navigationEntries') . '); ?>', 'root', self::TEMPLATE_DEFAULT_LEVEL, self::TEMPLATE_DEFAULT_CURRENT, self::TEMPLATE_DEFAULT_POSITION);
		}
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
			we_navigation_item::$currentPosition = [];
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
		$db = new DB_WE();
		$path = id_to_path($id, NAVIGATION_TABLE, $db);
		$path = we_base_file::clearPath($path . '/%');

		$ids = [];

		$db->query('SELECT * FROM ' . NAVIGATION_TABLE . ' WHERE Path LIKE "' . $db->escape($path) . '" ' . ($id ? ' OR ID=' . intval($id) : '') . ' ORDER BY Ordn');
		while($db->next_record()){
			$tmpItem = $db->getRecord();
			$tmpItem['Name'] = $tmpItem['Text'];
			self::$Storage['items'][] = $tmpItem;

			if($db->Record['IsFolder'] && $db->Record['SelectionType'] == we_navigation_navigation::STYPE_DOCLINK){
				$ids[] = $db->Record['LinkID'];
			} elseif($db->Record['Selection'] === we_navigation_navigation::SELECTION_STATIC && $db->Record['SelectionType'] === we_navigation_navigation::STYPE_DOCLINK){
				$ids[] = $db->Record['LinkID'];
			} elseif(($db->Record['DynamicSelection'] === we_navigation_navigation::DYN_CATEGORY || $db->Record['SelectionType'] === we_navigation_navigation::STYPE_CATLINK) && $db->Record['LinkSelection'] !== we_navigation_navigation::LSELECTION_EXTERN){
				$ids[] = $db->Record['UrlID'];
			}

			if($db->Record['IconID']){
				$ids[] = $db->Record['IconID'];
			}
		}
		$ids = $ids ? array_diff(array_unique($ids), array_keys(self::$Storage['ids'])) : [];
		if($ids){
			$db->query('SELECT ID,IF(Published>0,Path,"") FROM ' . FILE_TABLE . ' WHERE ID IN(' . implode(',', $ids) . ') ORDER BY ID');
			//keep array index
			self::$Storage['ids'] = self::$Storage['ids'] + $db->getAllFirst(false);
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
		if(isset(self::$Storage['ids'][$id])){
			return self::$Storage['ids'][$id];
		}
		if(!$id){
			return '/';
		}
		return (self::$Storage['ids'][$id] = id_to_path($id, FILE_TABLE));
	}

}
