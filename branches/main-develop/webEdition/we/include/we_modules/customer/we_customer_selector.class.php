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
class we_customer_selector extends we_users_selector{

	public function __construct($id, $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $rootDirID = 0, $filter = '', $multiple = true){
		parent::__construct((is_numeric($id) ? $id : 0), CUSTOMER_TABLE, $JSIDName, $JSTextName, $JSCommand, $order, $rootDirID, $multiple, $filter);
		$this->title = g_l('fileselector', '[userSelector][title]');
		$this->canSelectDir = false;
		$this->dir = (is_numeric($id) ? '' : $id);
		if($this->dir){
			$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
		} else {
			$this->setDefaultDirAndID(false);
		}
	}

	protected function setDirAndID(){
		if(!$this->dir){
			parent::setDirAndID();
		}
	}

	protected function setDefaultDirAndID($setLastDir){
		if($setLastDir){
			$this->dir = isset($_SESSION['weS']['we_fs_lastDir'][$this->table]) ? $_SESSION['weS']['we_fs_lastDir'][$this->table] : '';
			return;
		}
		if($this->id){
			while($this->query($this->id) && $this->db->next_record()){
				$this->dir = $this->db->f('Path');
			}
			$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
		}
	}

	protected function query($optionalID = 0){
		$pid = $this->dir;
		$settings = new we_customer_settings();
		$settings->load();
		$sort = $settings->getSettings('default_sort_view');
		if($sort == g_l('modules_customer', '[no_sort]')){
			$sort = '';
		}
		if($sort){
			$havingarr = $sort_defs = $pidarr = $check = [];

			if($pid){
				$pidarr = explode('-|-', $pid);
				unset($pidarr[count($pidarr) - 1]);
			}

			if(isset($settings->SortView[$sort])){
				$sort_defs = $settings->SortView[$sort];
			}

			$select = $grouparr = $orderarr = [];

			foreach($sort_defs as $c => $sortdef){
				if(!empty($sortdef['function'])){
					$select[] = '@select' . count($select) . ':=' . ($settings->customer->isInfoDate($sortdef['field']) ?
							sprintf($settings->FunctionTable[$sortdef['function']], 'FROM_UNIXTIME(' . $sortdef['field'] . ')') . ' AS ' . $sortdef['field'] . "_" . $sortdef["function"] :
							sprintf($settings->FunctionTable[$sortdef['function']], $sortdef['field']) . ' AS ' . $sortdef['field'] . '_' . $sortdef['function']);

					$grouparr[] = $sortdef['field'] . '_' . $sortdef['function'];
					$orderarr[] = $sortdef['field'] . '_' . $sortdef['function'] . ' ' . $sortdef['order'];
					$orderarr[] = $sortdef['field'] . ' ' . $sortdef['order'];
					if(isset($pidarr[$c])){
						$havingarr[] = (empty($pidarr[$c]) ?
								'(' . $sortdef['field'] . '_' . $sortdef["function"] . "='' OR " . $sortdef['field'] . '_' . $sortdef['function'] . ' IS NULL)' :
								$sortdef['field'] . '_' . $sortdef['function'] . "='" . $pidarr[$c] . "'");
					}
				} else {
					$select[] = '@select' . count($select) . ':=' . $sortdef['field'];
					$grouparr[] = $sortdef['field'];
					$orderarr[] = $sortdef['field'] . ' ' . $sortdef['order'];
					if(!empty($pidarr[$c])){
						$havingarr[] = (empty($pidarr[$c]) ?
								'(' . $sortdef['field'] . "='' OR " . $sortdef['field'] . ' IS NULL)' :
								$sortdef['field'] . "='" . $pidarr[$c] . "'");
					}
				}
			}

			$level = count($pidarr);
			$levelcount = count($grouparr);

			$grp = implode(',', array_slice($grouparr, 0, $level + 1));

			if($level < $levelcount){
				$fields = 'ID,1 AS IsFolder' . ($select ? ',' . implode(',', $select) : '') . ', COALESCE(NULLIF(@select' . $level . ',""),"' . g_l('modules_customer', '[no_value]') . '") AS Text,CONCAT("' . $pid . '",@select' . $level . ',"-|-") AS Path,"we/customerGroup" AS ContentType';
			} else {
				$fields = $settings->treeTextFormatSQL . ' AS Text,ID,Username AS Path,0 AS IsFolder,"we/customer" AS ContentType' . (empty($select) ? '' : ',' . implode(',', $select) );
			}

			$this->db->query('SELECT ' . $fields . ' FROM ' . CUSTOMER_TABLE . ' WHERE ' .
				($optionalID ? ' ID=' . intval($optionalID) : '1') . ' AND ' .
				(!permissionhandler::hasPerm("ADMINISTRATOR") && $_SESSION['user']['workSpace'][CUSTOMER_TABLE] ? $_SESSION['user']['workSpace'][CUSTOMER_TABLE] : '1 ') .
				' GROUP BY ' . $grp . ($grouparr ? ($level == $levelcount ? ',ID' : '') : 'ID') . ($havingarr ? ' HAVING ' . implode(' AND ', $havingarr) : '') .
				' ORDER BY ' . implode(',', $orderarr) . we_tree_customer::getSortOrder($settings, ','));
			return ($level < $levelcount);
		} else {
			$this->db->query('SELECT ID,Username AS Path,0 AS IsFolder,' . $settings->treeTextFormatSQL . ' AS Text,"we/customer" AS ContentType FROM ' . CUSTOMER_TABLE .
				' WHERE ' . (!permissionhandler::hasPerm("ADMINISTRATOR") && $_SESSION['user']['workSpace'][CUSTOMER_TABLE] ? $_SESSION['user']['workSpace'][CUSTOMER_TABLE] : '1 ') . we_tree_customer::getSortOrder($settings));
			//no need to search directory
			return false;
		}
	}

	function printSetDirHTML(){
		$weCmd = new we_base_jsCmd();
		$weCmd->addCmd('clearEntries');

		$this->printCmdAddEntriesHTML($weCmd);
		$js = 'top.RootDirButs(' . (($this->dir) ? 'true' : 'false' ) . ');';
		$this->setSelectorData($weCmd);

		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			if($this->id == 0){
				$this->path = '/';
			}
			$weCmd->addCmd('updateSelectData', [
				'currentPath' => $this->path,
				'currentID' => $this->id,
			]);
		}
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
		$weCmd->addCmd('updateSelectData', [
			'currentDir' => $this->dir
		]);

		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds() . we_html_element::jsElement($js), we_html_element::htmlBody());
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() . we_html_element::jsScript(JS_DIR . 'selectors/customer_selector.js');
	}

	private function getHeaderElements(){
		$vals = explode('-|-', $this->dir);
		unset($vals[count($vals) - 1]);
		$out = ['0' => '/'];
		foreach($vals as $pos => $val){
			$out[implode('-|-', array_slice($vals, 0, $pos + 1)) . '-|-'] = ($val ? : g_l('modules_customer', '[no_value]'));
		}
		return $out;
	}

	protected function setSelectorData(we_base_jsCmd $weCmd, $withWrite = true){
		$elem = $this->getHeaderElements();
		$options = [];
		foreach($elem as $key => $val){
			$options[] = [$val, $key];
		}
		$weCmd->addCmd('writeOptions', $options);
		if($withWrite){
			$weCmd->addCmd('writeBody');
		}
	}

}
