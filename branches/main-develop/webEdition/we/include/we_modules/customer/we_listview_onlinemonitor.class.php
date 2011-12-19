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
* class    we_listview_customer
* @desc    class for tag <we:listview type="banner">
*
*/

class we_listview_onlinemonitor extends listviewBase {

	var $ClassName = __CLASS__;
	var $condition="";
	var $Path="";
	var	$docID=0;
	var $lastaccesslimit="";
	var $lastloginlimit="";
	var $hidedirindex = false;
	/**
	 * we_listview_object()
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

	function we_listview_onlinemonitor($name="0", $rows=100000000, $offset=0, $order="", $desc=false , $condition="", $cols="", $docID=0,$lastaccesslimit='',$lastloginlimit='',$hidedirindex=false){

		listviewBase::listviewBase($name, $rows, $offset, $order, $desc, "", false, 0, $cols);

		$this->docID = $docID;
		$this->condition = $condition ? $condition : (isset($GLOBALS["we_lv_condition"]) ? $GLOBALS["we_lv_condition"] : "");
		$this->lastaccesslimit = $lastaccesslimit;
		$this->lastloginlimit = $lastloginlimit;

		if($this->docID){
			$this->Path = id_to_path($this->docID,FILE_TABLE,$this->DB_WE);
		}else{
			$this->Path = (isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : '');
		}
		$this->hidedirindex=$hidedirindex;
		// IMPORTANT for seeMode !!!! #5317
		$this->LastDocPath = '';
		if (isset($_SESSION['last_webEdition_document'])) {
			$this->LastDocPath = $_SESSION['last_webEdition_document']['Path'];
		}

		if($this->desc && $this->order!='' && (!preg_match("|.+ desc$|i",$this->order))){
			$this->order .= " DESC";
		}

 		if ($this->order != '') {
			$orderstring = " ORDER BY ".$this->order." ";
		} else {
			$orderstring = '';
		}
		$laStr='';
		$llStr='';
		if($this->lastloginlimit!=''){
			$llStr= "LastLogin > DATE_SUB(NOW(), INTERVAL ".$this->lastloginlimit." SECOND) ";
		}
		if($this->lastaccesslimit!=''){
			$laStr= "LastAccess > DATE_SUB(NOW(), INTERVAL ".$this->lastaccesslimit." SECOND) ";
		}
		if ($this->lastloginlimit!=''){$this->condition= ($this->condition!='' ? $this->condition." AND ":'').$llStr;}

		if ($this->lastaccesslimit!=''){$this->condition= ($this->condition!='' ? $this->condition." AND ":'').$laStr;}
		$where = $this->condition ? (' WHERE ' . $this->condition) : '';


		$q = 'SELECT * FROM ' . CUSTOMER_SESSION_TABLE . $where;
		$this->DB_WE->query($q);
		$this->anz_all = $this->DB_WE->num_rows();

		$q = 'SELECT SessionID,SessionIp,WebUserID,WebUserGroup,WebUserDescription,Browser,Referrer,UNIX_TIMESTAMP(LastLogin) AS LastLogin,UNIX_TIMESTAMP(LastAccess) AS LastAccess,PageID,ObjectID,SessionAutologin FROM ' . CUSTOMER_SESSION_TABLE . $where . ' ' . $orderstring . ' ' . (($this->maxItemsPerPage > 0) ? (' limit '.$this->start.','.$this->maxItemsPerPage) : '');;

		$this->DB_WE->query($q);
		$this->anz = $this->DB_WE->num_rows();

		$this->adjustRows();
	}

	function next_record(){
		$ret = $this->DB_WE->next_record();
		if ($ret) {
			$this->DB_WE->Record["we_cid"] = $this->DB_WE->Record["WebUserID"];
			$this->DB_WE->Record["wedoc_Path"] = $this->Path."?we_omid=".$this->DB_WE->Record["SessionID"];
			$this->DB_WE->Record["WE_PATH"] = $this->Path."?we_omid=".$this->DB_WE->Record["SessionID"];
			$this->DB_WE->Record["WE_TEXT"] = $this->DB_WE->Record["SessionID"];
			$this->DB_WE->Record["WE_ID"] = $this->DB_WE->Record["SessionID"];
			$this->DB_WE->Record["we_wedoc_lastPath"] = $this->LastDocPath."?we_omid=".$this->DB_WE->Record["SessionID"];
			$this->count++;
			return true;
		}else {
			$this->stop_next_row = $this->shouldPrintEndTR();
			if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
				$this->DB_WE->Record = array();
				$this->DB_WE->Record["WE_PATH"] = "";
				$this->DB_WE->Record["WE_TEXT"] = "";
				$this->DB_WE->Record["WE_ID"] = "";
				$this->count++;
				return true;
			}
		}

		return false;
	}

	function f($key){
		return $this->DB_WE->f($key);
	}

}
