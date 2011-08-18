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


include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/listview/listviewBase.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_db.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_db_tools.inc.php");

/**
* class    we_listview_customer
* @desc    class for tag <we:listview type="banner">
*
*/

class we_listview_order extends listviewBase {

	var $ClassName = __CLASS__;
	var $condition="";
	var $Path="";
	var	$docID=0;
	var $hidedirindex = false;

	/**
	 * we_listview_object()
	 * @desc    constructor of class
	 *
	 * @param   $name          string - name of listview
	 * @param   $rows          integer - number of rows to display per page
	 * @param   $order         string - field name(s) to ORDER BY
	 * @param   $desc		   string - if desc order
	 * @param   $condition	   string - condition of listview
	 * @param   $cols		   string - number of cols (default = 1)
	 * @param   $docID	   	   string - id of a document where a we:customer tag is on
	 *
	 */

	function we_listview_order($name="0", $rows=100000000, $offset=0, $order="", $desc=false , $condition="", $cols="", $docID=0,$hidedirindex=false){

		listviewBase::listviewBase($name, $rows, $offset, $order, $desc, "", false, 0, $cols);

		$this->docID = $docID;
		$this->condition = $condition ? $condition : (isset($GLOBALS["we_lv_condition"]) ? $GLOBALS["we_lv_condition"] : "");		
		
		if (strpos($this->condition,'ID') !== false && strpos($this->condition,'IntID') === false ){$this->condition=str_replace('ID','IntID',$this->condition);}
		// und nun sind alle anderen kaputt und werden repariert
		if (strpos($this->condition,'OrderIntID') !== false ){$this->condition=str_replace('OrderIntID','OrderID',$this->condition);}
		if (strpos($this->condition,'CustomerIntID') !== false){$this->condition=str_replace('CustomerIntID','CustomerID',$this->condition);}
		if (strpos($this->condition,'ArticleIntID') !== false){$this->condition=str_replace('ArticleIntID','ArticleID',$this->condition);}
		
		if (strpos($this->condition,'OrderID') !== false && strpos($this->condition,'IntOrderID') === false ){$this->condition=str_replace('OrderID','IntOrderID',$this->condition);}	
		if (strpos($this->condition,'CustomerID') !== false && strpos($this->condition,'IntCustomerID') === false ){$this->condition=str_replace('CustomerID','IntCustomerID',$this->condition);}
		if (strpos($this->condition,'ArticleID') !== false && strpos($this->condition,'IntArticleID') === false ){$this->condition=str_replace('ArticleID','IntArticleID',$this->condition);}
		if (strpos($this->condition,'Quantity') !== false && strpos($this->condition,'IntQuantity') === false ){$this->condition=str_replace('Quantity','IntQuantity',$this->condition);}
		if (strpos($this->condition,'Payment_Type') !== false && strpos($this->condition,'IntPayment_Type') === false ){$this->condition=str_replace('Payment_Type','Payment_Type',$this->condition);}
		
		if($this->docID){
			$this->Path = id_to_path($this->docID,FILE_TABLE,$this->DB_WE);
		}else{
			$this->Path = (isset($GLOBALS["we_doc"]) ? $GLOBALS["we_doc"]->Path : '');
		}
		$this->hidedirindex=$hidedirindex;
		// IMPORTANT for seeMode !!!! #5317
		$this->LastDocPath = '';
		if (isset($_SESSION['last_webEdition_document'])) {
			$this->LastDocPath = $_SESSION['last_webEdition_document']['Path'];
		}

		$group = " GROUP BY IntOrderID ";

		if($this->desc && $this->order!='' && (!preg_match("|.+ desc$|i",$this->order))){
			$this->order .= " DESC";
		}

 		if ($this->order != '') {
			if (trim($this->order) =='ID' || trim($this->order) =='CustomerID' || trim($this->order) =='ArticleID' ||  trim($this->order) =='Quantity' ||  trim($this->order) =='Payment_Type') {$this->order= 'Int'.$this->order;}
			$orderstring = " ORDER BY ".$this->order." "; 
		} else { 
			$orderstring = ''; 
		}
		
		$where = $this->condition ? (' WHERE ' . $this->condition) .$group  : $group;

		$q = 'SELECT * FROM ' . SHOP_TABLE . $where;
		$this->DB_WE->query($q);
		$this->anz_all = $this->DB_WE->num_rows();

		$q = 'SELECT IntOrderID as OrderID, IntCustomerID as CustomerID, IntPayment_Type as Payment_Type, strSerialOrder, UNIX_TIMESTAMP(DateShipping) as DateShipping, UNIX_TIMESTAMP(DatePayment) as DatePayment, UNIX_TIMESTAMP(DateOrder) as DateOrder, UNIX_TIMESTAMP(DateConfirmation) as DateConfirmation, UNIX_TIMESTAMP(DateCustomA) as DateCustomA, UNIX_TIMESTAMP(DateCustomB) as DateCustomB, UNIX_TIMESTAMP(DateCustomC) as DateCustomC, UNIX_TIMESTAMP(DateCancellation) as DateCancellation, UNIX_TIMESTAMP(DateFinished) as DateFinished,
		UNIX_TIMESTAMP(MailShipping) as MailShipping, UNIX_TIMESTAMP(MailPayment) as MailPayment, UNIX_TIMESTAMP(MailOrder) as MailOrder, UNIX_TIMESTAMP(MailConfirmation) as MailConfirmation, UNIX_TIMESTAMP(MailCustomA) as MailCustomA, UNIX_TIMESTAMP(MailCustomB) as MailCustomB, UNIX_TIMESTAMP(MailCustomC) as MailCustomC, UNIX_TIMESTAMP(MailCancellation) as MailCancellation, UNIX_TIMESTAMP(MailFinished) as MailFinished FROM ' . SHOP_TABLE . $where . ' ' . $orderstring . ' ' . (($this->maxItemsPerPage > 0) ? (' limit '.$this->start.','.$this->maxItemsPerPage) : '');;

		$this->DB_WE->query($q);
		$this->anz = $this->DB_WE->num_rows();
		$this->adjustRows();
	}

	function next_record(){
		$ret = $this->DB_WE->next_record();
		if ($ret) {
			$strSerialOrder = @unserialize($this->DB_WE->Record["strSerialOrder"]);
			unset($this->DB_WE->Record["strSerialOrder"]);
			if (is_array($strSerialOrder)){
				if(is_array($strSerialOrder['we_sscf']) ){
					foreach ($strSerialOrder['we_sscf'] as $key => &$value){
						$this->DB_WE->Record[$key] = $value;
					}
					unset($value);		
				}
				if(is_array($strSerialOrder['we_shopPriceShipping']) ){
					foreach ($strSerialOrder['we_shopPriceShipping'] as $key => &$value){
						$this->DB_WE->Record['Shipping_'.$key] = $value;
					}
					unset($value);		
				}
				if(is_array($strSerialOrder['we_shopCustomer']) ){
					foreach ($strSerialOrder['we_shopCustomer'] as $key => &$value){
						if (!is_numeric($key)){
							$this->DB_WE->Record['Customer_'.$key] = $value;
						}
					}
					unset($value);		
				}
				if(isset($strSerialOrder['we_shopPriceIsNet'])){
					$this->DB_WE->Record['shopPriceIsNet'] = $strSerialOrder['we_shopPriceIsNet'];
				}
				if(isset($strSerialOrder['we_shopCalcVat'])){
					$this->DB_WE->Record['shopCalcVat'] = $strSerialOrder['we_shopCalcVat'];
				}
			}
			//$this->DB_WE->Record["CustomerID"] = $this->DB_WE->Record["IntCustomerID"];
			$this->DB_WE->Record["we_cid"] = $this->DB_WE->Record["CustomerID"];
			//$this->DB_WE->Record["OrderID"] = $this->DB_WE->Record["IntOrderID"];
			$this->DB_WE->Record["we_orderid"] = $this->DB_WE->Record["OrderID"];
			$this->DB_WE->Record["wedoc_Path"] = $this->Path."?we_orderid=".$this->DB_WE->Record["OrderID"];
			$this->DB_WE->Record["WE_PATH"] = $this->Path."?we_orderid=".$this->DB_WE->Record["OrderID"];
			$this->DB_WE->Record["WE_TEXT"] = $this->DB_WE->Record["OrderID"];
			$this->DB_WE->Record["WE_ID"] = $this->DB_WE->Record["OrderID"];
			$this->DB_WE->Record["we_wedoc_lastPath"] = $this->LastDocPath."?we_orderid=".$this->DB_WE->Record["OrderID"];
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


?>