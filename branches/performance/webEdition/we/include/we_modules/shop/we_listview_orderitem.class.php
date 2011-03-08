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


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/listview/"."listviewBase.class.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_db.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_db_tools.inc.php");

/**
* class    we_listview_customer
* @desc    class for tag <we:listview type="banner">
*
*/

class we_listview_orderitem extends listviewBase {

	var $ClassName = "we_listview_orderitem";
	var $condition="";
	var $Path="";
	var	$docID=0;
	var	$orderID=0;
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

	function we_listview_orderitem($name="0", $rows=100000000, $offset=0, $order="", $desc=false , $condition="", $cols="", $docID=0,$orderID=0,$hidedirindex=false){

		listviewBase::listviewBase($name, $rows, $offset, $order, $desc, "", false, 0, $cols);

		$this->docID = $docID;
		$this->orderID = $orderID;
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

		

		if($this->desc && $this->order!='' && (!eregi(".+ desc$",$this->order))){
			$this->order .= " DESC";
		}

 		if ($this->order != '') {
			if (trim($this->order) =='ID' || trim($this->order) =='CustomerID' || trim($this->order) =='ArticleID' ||  trim($this->order) =='Quantity' ||  trim($this->order) =='Payment_Type') {$this->order= 'Int'.$this->order;}
 
			$orderstring = " ORDER BY ".$this->order." "; 
		} else { 
			$orderstring = ''; 
		}
		
		if ($this->orderID !=0){
			$where = $this->condition ? (' WHERE IntOrderID='.$this->name.' AND ' .$this->condition )   : ' WHERE IntOrderID='.$this->orderID.' ';
		} else {
			$where = $this->condition ? (' WHERE '. $this->condition )   : ' ';
		}

		$q = 'SELECT IntID,IntOrderID,IntArticleID,IntQuantity,Price, strSerial FROM ' . SHOP_TABLE . $where;
	
		$this->DB_WE->query($q);
		$this->anz_all = $this->DB_WE->num_rows();

		$q = 'SELECT IntID as ID,IntOrderID as OrderID, IntArticleID as ArticleID, IntQuantity as Quantity, Price, strSerial FROM ' . SHOP_TABLE . $where . ' ' . $orderstring . ' ' . (($rows > 0) ? (' limit '.$this->start.','.$this->rows) : '');;

		$this->DB_WE->query($q);
		$this->anz = $this->DB_WE->num_rows();
		$this->adjustRows();
	}

	function next_record(){
		$ret = $this->DB_WE->next_record();
		if ($ret) {
			$strSerial = @unserialize($this->DB_WE->Record["strSerial"]);
			unset($this->DB_WE->Record["strSerial"]);
			if (is_array($strSerial)){
				if (isset($strSerial['OF_ID'])){//Object based Article
					$this->DB_WE->Record['articleIsObject']= 1;
					foreach($strSerial as $key => &$value){
						if(!is_numeric($key) && $key != 'we_sacf' && $key != 'WE_VARIANT' && (strpos($key, 'we_')!==false)  && (strpos($key,'we_wedoc')===false) && (strpos($key,'we_WE')===false) ){
							$this->DB_WE->Record[substr($key,3)]= $value;
						}
					}
					unset($value);
					foreach ($strSerial['we_sacf'] as $key => &$value){
						$this->DB_WE->Record[$key]= $value;
					}
					unset($value);
					$this->DB_WE->Record["shopvat"] = $strSerial["shopvat"];
				} else {//Document based Article
					$this->DB_WE->Record['articleIsObject']= 0;
					foreach($strSerial as $key => &$value){
						if($key != 'we_sacf' && $key != 'Charset' && $key != 'WE_VARIANT' && strpos($key,'wedoc_')===false  ){
							$this->DB_WE->Record[$key]= $value;
						}
					}
					unset($value);
					foreach ($strSerial['we_sacf'] as $key => &$value){
						$this->DB_WE->Record[$key]= $value;
					}
					unset($value);
					$this->DB_WE->Record['VARIANT']= $strSerial['WE_VARIANT'];
					$this->DB_WE->Record["shopvat"] = $strSerial["shopvat"];
				}
			
			}
			
			$this->DB_WE->Record["wedoc_Path"] = $this->Path."?we_orderid=".$this->DB_WE->Record["OrderID"]."&we_orderitemid=".$this->DB_WE->Record["ID"];
			$this->DB_WE->Record["WE_PATH"] = $this->Path."?we_orderid=".$this->DB_WE->Record["OrderID"]."&we_orderitemid=".$this->DB_WE->Record["ID"];
			$this->DB_WE->Record["WE_TEXT"] = $this->DB_WE->Record["ID"];
			$this->DB_WE->Record["WE_ID"] = $this->DB_WE->Record["ID"];
			$this->DB_WE->Record["we_wedoc_lastPath"] = $this->LastDocPath."?we_orderid=".$this->DB_WE->Record["OrderID"]."&we_orderitemid=".$this->DB_WE->Record["ID"];
			$this->count++;
			return true;
		}else if($this->cols && ($this->count < $this->rows)){
			$this->DB_WE->Record = array();
			$this->DB_WE->Record["WE_PATH"] = "";
			$this->DB_WE->Record["WE_TEXT"] = "";
			$this->DB_WE->Record["WE_ID"] = "";
			$this->count++;
			return true;
		}

		return false;
	}

	function f($key){
		return $this->DB_WE->f($key);
	}

}


?>