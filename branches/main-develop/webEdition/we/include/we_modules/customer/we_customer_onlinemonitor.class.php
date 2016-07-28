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
 * class    we_listview_onlinemonitor
 * @desc    class for tag
 *
 */
class we_customer_onlinemonitor extends we_listview_base{
	private $condition = '';
	private $Path = '';
	private $docID = 0;
	private $lastaccesslimit = '';
	private $lastloginlimit = '';

	/**
	 * @desc    constructor of class
	 *
	 * @param   $name          string - name of listview
	 * @param   $rows          integer - number of rows to display per page
	 * @param   $order         string - field name(s) to order by
	 * @param   $desc		   string - if desc order
	 * @param   $condition	   string - condition of listview
	 * @param   $cols		   string - number of cols (default = 1)
	 * @param   $docID	   	   string - id of a document where a we:customer tag is on
	 *
	 */
	function __construct($name, $rows, $offset, $order, $desc, $condition, $cols, $docID, $lastaccesslimit, $lastloginlimit, $hidedirindex){
		parent::__construct($name, $rows, $offset, $order, $desc, '', false, 0, $cols);

		$this->docID = $docID;
		$this->condition = $condition;
		$this->lastaccesslimit = $lastaccesslimit;
		$this->lastloginlimit = $lastloginlimit;

		$this->Path = ($this->docID ?
				id_to_path($this->docID, FILE_TABLE, $this->DB_WE) :
				(isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : ''));

		$this->hidedirindex = $hidedirindex;
		// IMPORTANT for seeMode !!!! #5317
		$this->LastDocPath = '';
		if(isset($_SESSION['weS']['last_webEdition_document'])){
			$this->LastDocPath = $_SESSION['weS']['last_webEdition_document']['Path'];
		}

		if($this->desc && $this->order != '' && (!preg_match('|.+ desc$|i', $this->order))){
			$this->order .= ' DESC';
		}

		$orderstring = ($this->order ? ' ORDER BY ' . $this->order . ' ' : '');
		$laStr = $llStr = '';
		if($this->lastloginlimit != ''){
			$llStr = 'LastLogin>(NOW() - INTERVAL ' . $this->lastloginlimit . ' SECOND) ';
		}
		if($this->lastaccesslimit != ''){
			$laStr = 'LastAccess>(NOW() - INTERVAL ' . $this->lastaccesslimit . ' SECOND) ';
		}
		if($this->lastloginlimit != ''){
			$this->condition = ($this->condition ? $this->condition . ' AND ' : '') . $llStr;
		}

		if($this->lastaccesslimit != ''){
			$this->condition = ($this->condition ? $this->condition . ' AND ' : '') . $laStr;
		}
		$where = $this->condition ? (' WHERE ' . $this->condition) : '';


		$this->anz_all = f('SELECT COUNT(1) FROM ' . CUSTOMER_SESSION_TABLE . $where, '', $this->DB_WE);

		$this->DB_WE->query('SELECT SessionID,SessionIp,WebUserID,WebUserGroup,WebUserDescription,Browser,Referrer,UNIX_TIMESTAMP(LastLogin) AS LastLogin,UNIX_TIMESTAMP(LastAccess) AS LastAccess,PageID,SessionAutologin FROM ' . CUSTOMER_SESSION_TABLE . $where . ' ' . $orderstring . ' ' . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . $this->maxItemsPerPage) : ''));
		$this->anz = $this->DB_WE->num_rows();
	}

	public function next_record(){
		$ret = $this->DB_WE->next_record();
		if($ret){
			$this->DB_WE->Record[self::PROPPREFIX . 'CID'] = $this->DB_WE->Record['WebUserID'];
			$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = $this->Path . '?we_omid=' . $this->DB_WE->Record['SessionID'];
			$this->DB_WE->Record[self::PROPPREFIX . 'TEXT'] = $this->DB_WE->Record['SessionID'];
			$this->DB_WE->Record[self::PROPPREFIX . 'ID'] = $this->DB_WE->Record['SessionID'];
			$this->DB_WE->Record[self::PROPPREFIX . 'LASTPATH'] = $this->LastDocPath . '?we_omid=' . $this->DB_WE->Record['SessionID'];
			$this->count++;
			return true;
		}
		$this->stop_next_row = $this->shouldPrintEndTR();
		if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
			$this->DB_WE->Record = [
				'WE_PATH' => '',
				'WE_TEXT' => '',
				'WE_ID' => '',
			];
			$this->count++;
			return true;
		}

		return false;
	}

	function f($key){
		$repl = 0;
		$key = preg_replace('/^(OF|wedoc|we)_/i', '', $key, $repl);
		if($repl){
			$key = strtoupper($key);
		}

		return $this->DB_WE->f($key);
	}

}
