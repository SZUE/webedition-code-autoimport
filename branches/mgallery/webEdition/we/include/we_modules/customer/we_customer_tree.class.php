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
class we_customer_tree extends weTree{

	function customJSFile(){
		return we_html_element::jsScript(WE_JS_CUSTOMER_MODULE_DIR . 'customer_tree.js');
	}

	function getJSStartTree(){
		return '
function startTree(){
	frames={
	"top":' . $this->topFrame . ',
	"cmd":' . $this->cmdFrame . '
};
	treeData.frames=frames;
	frames.cmd.location=treeData.frameset+"?pnt=cmd&pid=0";
	drawTree();
}';
	}

	function getJSLoadTree($rootID, array $treeItems){
		$days = array(
			'Sunday' => 0,
			'Monday' => 1,
			'Tuesday' => 2,
			'Wednesday' => 3,
			'Thursday' => 4,
			'Friday' => 5,
			'Saturday' => 6
		);

		$months = array(
			'January' => 0,
			'February' => 1,
			'March' => 2,
			'April' => 3,
			'May' => 4,
			'June' => 5,
			'July' => 6,
			'August' => 7,
			'September' => 8,
			'October' => 9,
			'November' => 10,
			'December' => 11
		);

		$js = (!$rootID ?
				$this->topFrame . '.treeData.clear();' .
				$this->topFrame . '.treeData.add(' . $this->topFrame . '.rootEntry(\'' . $rootID . '\',\'root\',\'root\'));' : '') .
			'var attribs={};';
		foreach($treeItems as $item){
			$js.=($rootID ? 'if(' . $this->topFrame . '.treeData.indexOfEntry(\'' . str_replace(array("\n", "\r", '\''), '', $item["id"]) . '\')<0){' : '') .
				'attribs={';

			foreach($item as $k => $v){
				if($k === 'text'){
					if(in_array($v, array_keys($days))){
						$v = g_l('date', '[day][long][' . $days[$v] . ']');
					}
					if(in_array($v, array_keys($months))){
						$v = g_l('date', '[month][long][' . $months[$v] . ']');
					}
				}
				$js.= strtolower($k) . ':' . ($v === 1 || $v === 0 || is_bool($v) || $v === 'true' || $v === 'false' || is_int($v) ?
						intval($v) :
						'\'' . str_replace(array('"', '\'', '\\'), '',$v) . '\'') .
					',';
			}

			$js.='};' .
				$this->topFrame . '.treeData.add(new ' . $this->topFrame . '.node(attribs));' .
				($rootID ? '}' : '');
		}
		$js.=$this->topFrame . '.drawTree();';

		return $js;
	}

	static function getItems($pid, $offset = 0, $segment = 500, $sort = ""){
		return (empty($sort) ?
				self::getItemsFromDB($pid, $offset, $segment) :
				self::getSortFromDB($pid, $sort, $offset, $segment));
	}

	private static function getItemsFromDB($ParentID = 0, $offset = 0, $segment = 500, $elem = 'ID,Forename,Surname', $addWhere = "", $addOrderBy = ""){
		$db = new DB_WE();

		$prevoffset = max(0, $offset - $segment);
		$items = ($offset && $segment ?
				array(array(
					"id" => "prev_" . $ParentID,
					"parentid" => $ParentID,
					"text" => "display (" . $prevoffset . "-" . $offset . ")",
					"contenttype" => "arrowup",
					"table" => CUSTOMER_TABLE,
					"typ" => "threedots",
					"open" => 0,
					"published" => 1,
					"disabled" => 0,
					"tooltip" => "",
					"offset" => $prevoffset
				)) : array());


		$settings = new we_customer_settings();
		$settings->load();


		$where = ' WHERE 1 ' .
			(!permissionhandler::hasPerm("ADMINISTRATOR") && $_SESSION['user']['workSpace'][CUSTOMER_TABLE] ? ' AND ' . $_SESSION['user']['workSpace'][CUSTOMER_TABLE] : '') .
			' ' . $addWhere;

		$db->query('SELECT ' . $settings->treeTextFormatSQL . ' AS treeFormat, 0 AS ParentID,' . $elem . ',LoginDenied FROM ' . CUSTOMER_TABLE . ' ' . $where . ' ' . self::getSortOrder($settings) . ($segment ? ' LIMIT ' . $offset . ',' . $segment : ''));

		while($db->next_record(MYSQL_ASSOC)){
			$typ = array(
				'typ' => ($db->f("IsFolder") == 1 ? "group" : "item"),
				'contenttype' => ($db->f("IsFolder") == 1 ? "folder" : "we/costumer"),
				'disabled' => 0,
				'published' => $db->f('LoginDenied'),
				'tooltip' => $db->f("ID"),
				'offset' => $offset,
			);

			$tt = $db->f('treeFormat');
			$fileds = array();

			foreach($db->Record as $k => $v){
				$fileds[strtolower($k)] = $v;
			}

			$fileds["text"] = oldHtmlspecialchars($tt);
			$items[] = array_merge($fileds, $typ);
		}

		$total = f('SELECT COUNT(1) FROM ' . CUSTOMER_TABLE . ' ' . $where, '', $db);
		$nextoffset = $offset + $segment;
		if($segment && ($total > $nextoffset)){
			$items[] = array(
				"id" => "next_" . $ParentID,
				"parentid" => 0,
				"text" => "display (" . $nextoffset . "-" . ($nextoffset + $segment) . ")",
				"contenttype" => "arrowdown",
				"table" => CUSTOMER_TABLE,
				"typ" => "threedots",
				"open" => 0,
				"disabled" => 0,
				"tooltip" => "",
				"offset" => $nextoffset
			);
		}

		return $items;
	}

	private static function getSortFromDB($pid, $sort, $offset = 0, $segment = 500){
		$db = new DB_WE();

		$havingarr = $sort_defs = $pidarr = $check = array();

		$notroot = (preg_match('|\{.\}|', $pid)) ? true : false;

		$pid = str_replace(array('{', '}', '*****quot*****'), array('', '', "\\\\\'"), $pid);

		if($pid || $notroot){
			$pidarr = explode("-|-", $pid);
		}

		$settings = new we_customer_settings();
		$settings->load(false);

		if(isset($settings->SortView[$sort])){
			$sort_defs = $settings->SortView[$sort];
		}

		$select = $grouparr = $orderarr = array();

		$total = f('SELECT COUNT(1) FROM ' . CUSTOMER_TABLE);

		foreach($sort_defs as $c => $sortdef){
			if(!empty($sortdef['function'])){
				$select[] = ($settings->customer->isInfoDate($sortdef['field']) ?
						sprintf($settings->FunctionTable[$sortdef['function']], 'FROM_UNIXTIME(' . $sortdef['field'] . ')') . ' AS ' . $sortdef["field"] . "_" . $sortdef["function"] :
						sprintf($settings->FunctionTable[$sortdef['function']], $sortdef['field']) . ' AS ' . $sortdef['field'] . '_' . $sortdef["function"]);

				$grouparr[] = $sortdef['field'] . '_' . $sortdef['function'];
				$orderarr[] = $sortdef['field'] . '_' . $sortdef['function'] . ' ' . $sortdef['order'];
				$orderarr[] = $sortdef['field'] . ' ' . $sortdef['order'];
				if(isset($pidarr[$c])){
					$havingarr[] = ($pidarr[$c] == g_l('modules_customer', '[no_value]') ?
							'(' . $sortdef['field'] . '_' . $sortdef["function"] . "='' OR " . $sortdef['field'] . '_' . $sortdef['function'] . ' IS NULL)' :
							$sortdef['field'] . '_' . $sortdef['function'] . "='" . $pidarr[$c] . "'");
				}
			} else {
				$select[] = $sortdef['field'];
				$grouparr[] = $sortdef['field'];
				$orderarr[] = $sortdef['field'] . ' ' . $sortdef['order'];
				if(!empty($pidarr[$c])){
					$havingarr[] = ($pidarr[$c] == g_l('modules_customer', '[no_value]') ?
							'(' . $sortdef['field'] . "='' OR " . $sortdef['field'] . ' IS NULL)' :
							$sortdef['field'] . "='" . $pidarr[$c] . "'");
				}
			}
		}

		$level = count($pidarr);
		$levelcount = count($grouparr);

		$grp = implode(',', array_slice($grouparr, 0, $level + 1));

		$db->query('SELECT ' . $settings->treeTextFormatSQL . ' AS treeFormat,ID,LoginDenied,Forename,Surname' .
			($select ? ',' . implode(',', $select) : '' ) . ' FROM ' . CUSTOMER_TABLE .
			(!permissionhandler::hasPerm("ADMINISTRATOR") && $_SESSION['user']['workSpace'][CUSTOMER_TABLE] ? ' WHERE ' . $_SESSION['user']['workSpace'][CUSTOMER_TABLE] : '') .
			' GROUP BY ' . $grp . (count($grouparr) ? ($level ? ',ID' : '') : 'ID') . (count($havingarr) ? ' HAVING ' . implode(' AND ', $havingarr) : '') . ' ORDER BY ' . implode(',', $orderarr) . self::getSortOrder($settings, ($orderarr ? ',' : '')) . (($level == $levelcount && $segment) ? ' LIMIT ' . $offset . ',' . $segment : ''));

		$items = $foo = array();
		$gname = '';
		$old = '0';
		$first = true;

		while($db->next_record()){
			$old = 0;

			if($level == 0){
				$gname = $db->f($grouparr[0]) ? : g_l('modules_customer', '[no_value]');
				$gid = '{' . $gname . '}';

				$groupTotal = f('SELECT COUNT(ID) FROM ' . CUSTOMER_TABLE . ' WHERE ' . $grp . '="' . $db->escape($gname) . '"' .
					(!permissionhandler::hasPerm('ADMINISTRATOR') && $_SESSION['user']['workSpace'][CUSTOMER_TABLE] ? ' AND ' . $_SESSION['user']['workSpace'][CUSTOMER_TABLE] : '') .
					(count($havingarr) ? ' HAVING ' . implode(' AND ', $havingarr) : ''));

				$items[] = array(
					'id' => str_replace("\'", '*****quot*****', $gid),
					'parentid' => $old,
					'path' => '',
					'text' => $gname . ' (' . $groupTotal . '/' . '<abbr title="' . g_l('modules_customer', '[all]') . ' ' . g_l('modules_customer', '[customer_data]') . '">' . $total . '</abbr>)',
					'contentType' => 'folder',
					'isfolder' => 1,
					'typ' => 'group',
					'disabled' => 0,
					'open' => 0
				);
				$check[$gname] = 1;
			} else {
				$foo = array();
				for($i = 0; $i < $levelcount; $i++){
					$foo[] = ($i == 0 ?
							('{' . ($db->f($grouparr[$i]) ? : g_l('modules_customer', '[no_value]')) . '}') :
							($db->f($grouparr[$i]) ? : g_l('modules_customer', '[no_value]')));
					$gname = implode('-|-', $foo);
					if($i >= $level){
						if(!isset($check[$gname])){
							$items[] = array(
								'id' => $gname,
								'parentid' => $old,
								'path' => '',
								'text' => ($db->f($grouparr[$i]) ? : g_l('modules_customer', '[no_value]')),
								'contentType' => 'folder',
								'isfolder' => 1,
								'typ' => 'group',
								'disabled' => 0,
								'open' => 0
							);
							$check[$gname] = 1;
						}
					}
					$old = $gname;
				}
				$gname = implode('-|-', $foo);
				if($level == $levelcount){
					$tt = $db->f('treeFormat');
					if($first){
						$prevoffset = max(0, $offset - $segment);
						if($offset && $segment){
							$items[] = array(
								'id' => "prev_" . $gname,
								'parentid' => $gname,
								'text' => 'display (' . $prevoffset . '-' . $offset . ')',
								'contenttype' => "arrowup",
								'table' => CUSTOMER_TABLE,
								'typ' => "threedots",
								'open' => 0,
								'published' => 1,
								'disabled' => 0,
								'tooltip' => "",
								'offset' => $prevoffset
							);
						}
						$first = false;
					}
					$items[] = array(
						'id' => $db->f("ID"),
						'parentid' => str_replace("\'", "*****quot*****", $gname),
						'path' => '',
						'text' => oldHtmlspecialchars($tt),
						'contentType' => 'we/customer',
						'isfolder' => $db->f("IsFolder"),
						'typ' => "item",
						'disabled' => 0,
						'published' => $db->f('LoginDenied'),
						'tooltip' => $db->f("ID")
					);
				}
			}
		}

		if($level == $levelcount){
			$total = f('SELECT COUNT(ID) ' . (empty($select) ? '' : ',' . implode(',', $select)) . ' FROM ' . CUSTOMER_TABLE . ' GROUP BY ' . $grp . (empty($grouparr) ? 'ID' : ($level ? ',ID' : '')) . (empty($havingarr) ? '' : ' HAVING ' . implode(' AND ', $havingarr)), '', $db);

			$nextoffset = $offset + $segment;
			if($segment && ($total > $nextoffset)){
				$items[] = array(
					'id' => "next_" . str_replace("\'", "*****quot*****", $old),
					'parentid' => str_replace("\'", "*****quot*****", $old),
					'text' => "display (" . $nextoffset . "-" . ($nextoffset + $segment) . ")",
					'contenttype' => "arrowdown",
					'table' => CUSTOMER_TABLE,
					'typ' => "threedots",
					'open' => 0,
					'disabled' => 0,
					'tooltip' => "",
					'offset' => $nextoffset
				);
			}
		}

		return $items;
	}

	public static function getSortOrder($settings, $concat = 'ORDER BY'){
		$ret = ($settings->getSettings('default_order') ?
				($settings->formatFields ?
					implode(' ' . $settings->getSettings('default_order') . ',', $settings->formatFields) . ' ' . $settings->getSettings('default_order') :
					'Text ' . $settings->getSettings('default_order')) :
				'');
		return ($ret ? $concat . ' ' . $ret : '');
	}

}
