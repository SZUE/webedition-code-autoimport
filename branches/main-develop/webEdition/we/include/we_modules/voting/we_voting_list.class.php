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
 * General Definition of WebEdition Voting
 *
 */
class we_voting_list{

	//properties
	var $Name;
	var $Version;
	var $Offset = 0;
	var $Start = 0;
	var $CountAll = 0;

	/**
	 * Default Constructor
	 * Can load or create new Newsletter depends of parameter
	 */
	public function __construct($name, $groupid, $version, $rows, $offset, $desc, $order, $subgroup, $start){

		$this->Name = $name;
		$this->Version = $version;
		$this->Offset = $offset;
		$this->Rows = $rows;
		$this->Start = $start;
		if($this->Start == 0){
			$this->Start += $offset;
		}

		$childs_query = '';
		if($groupid != 0){
			$childs_query = '(ParentID=' . intval($groupid);
			if($subgroup){
				$childs = [];
				we_readChilds($groupid, $childs, VOTING_TABLE, true, '', 'IsFolder', 1);
				$childs_query .= ' OR ParentID=' . implode(' OR ParentID=', $childs);
			}
			$childs_query .= ')';
		}

		$limit = ($rows || $this->Start ?
						' LIMIT ' . $this->Start . ',' . ($rows == 0 ? 9999999 : $rows) : '');

		if($order != ""){
			$order_sql = ' ORDER BY ' . $order;
			if($desc){
				$order_sql .= ' DESC ';
			} else {
				$order_sql .= ' ASC ';
			}
		}

		$this->db = new DB_WE();


		$this->CountAll = f('SELECT COUNT(1) FROM ' . VOTING_TABLE . ' WHERE IsFolder=0 ' . (!empty($childs_query) ? ' AND ' . $childs_query : '') . $order_sql, '', $this->db);
		$we_voting_query = 'SELECT ID FROM ' . VOTING_TABLE . ' WHERE IsFolder=0 ' . (!empty($childs_query) ? ' AND ' . $childs_query : '') . $order_sql . $limit;

		$this->db->query($we_voting_query);
	}

	public function getNext(){

		if($this->db->next_record(MYSQL_ASSOC)){
			$GLOBALS['_we_voting'] = new we_voting_voting($this->db->f('ID'));
			$GLOBALS['_we_voting']->setDefVersion($this->Version);
			return true;
		}
		return false;
	}

	public function getNextLink($attribs){
		if($this->hasNextPage()){
			$urlID = weTag_getAttribute("id", $attribs, 0, we_base_request::INT);
			$foo = $this->Start + $this->Rows;
			$attribs['href'] = we_tag('url', ['id' => ($urlID ? : 'self'), 'hidedirindex' => false]) . '?' . oldHtmlspecialchars(we_listview_base::we_makeQueryString('_we_vl_start_' . $this->Name . '=' . $foo));
			$attribs['rel'] = 'next';

			return getHtmlTag('a', $attribs, '', false, true);
		}
		return '';
	}

	public function hasNextPage(){
		return (($this->Start + $this->Rows) < $this->CountAll);
	}

	public function getBackLink($attribs){
		if($this->hasPrevPage()){
			$urlID = weTag_getAttribute("id", $attribs, 0, we_base_request::INT);
			$foo = $this->Start - $this->Rows;
			$attribs['href'] = we_tag('url', ['id' => ($urlID ? : 'self'), 'hidedirindex' => false]) . '?' . oldHtmlspecialchars(we_listview_base::we_makeQueryString('_we_vl_start_' . $this->Name . '=' . $foo));
			$attribs['rel'] = 'prev';

			return getHtmlTag('a', $attribs, '', false, true);
		}
		return '';
	}

	function hasPrevPage(){
		return (abs($this->Start) != abs($this->Offset));
	}

}
