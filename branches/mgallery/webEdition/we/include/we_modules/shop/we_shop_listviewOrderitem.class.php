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
 * class    we_shop_listviewOrderitem
 * @desc    class for tag <we:listview type="banner">
 *
 */
class we_shop_listviewOrderitem extends we_listview_base{
	var $condition = '';
	var $Path = '';
	var $docID = 0;
	var $orderID = 0;
	var $hidedirindex = false;

	/**
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
	function __construct($name, $rows, $offset, $order, $desc, $condition, $cols, $docID, $orderID, $hidedirindex){

		parent::__construct($name, $rows, $offset, $order, $desc, '', false, 0, $cols);
		if($GLOBALS['WE_MAIN_DOC']->InWebEdition){
			//do nothing inside we
			return;
		}
		$this->docID = $docID;
		$this->orderID = $orderID;
		$this->condition = $condition;

		if(strpos($this->condition, 'ID') !== false && strpos($this->condition, 'IntID') === false){
			$this->condition = str_replace('ID', 'IntID', $this->condition);
		}
		// und nun sind alle anderen kaputt und werden repariert
		$this->condition = strtr($this->condition, array(
			'OrderIntID' => 'IntOrderID',
			'CustomerIntID' => 'IntCustomerID',
			'ArticleIntID' => 'IntArticleID',
			'Quantity' => 'IntQuantity',
			'Payment_Type' => 'IntPayment_Type',
		));

		$this->Path = ($this->docID ? id_to_path($this->docID, FILE_TABLE, $this->DB_WE) : (isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : ''));

		$this->hidedirindex = $hidedirindex;
		// IMPORTANT for seeMode !!!! #5317
		$this->LastDocPath = (isset($_SESSION['weS']['last_webEdition_document']) ? $_SESSION['weS']['last_webEdition_document']['Path'] : '');

		if($this->desc && $this->order != '' && (!preg_match('|.+ desc$|i', $this->order))){
			$this->order .= ' DESC';
		}

		if($this->order){
			switch(trim($this->order)){
				case 'ID':
				case 'CustomerID':
				case 'ArticleID':
				case 'Quantity':
				case 'Payment_Type':
					$this->order = 'Int' . $this->order;
			}

			$orderstring = ' ORDER BY ' . $this->order;
		} else {
			$orderstring = '';
		}

		$where = ($this->orderID ?
				($this->condition ? (' WHERE IntOrderID=' . $this->name . ' AND ' . $this->condition ) : ' WHERE IntOrderID=' . $this->orderID . ' ') :
				($this->condition ? (' WHERE ' . $this->condition ) : ' '));

		$this->anz_all = f('SELECT COUNT(1) FROM ' . SHOP_TABLE . $where, '', $this->DB_WE);

		$this->DB_WE->query('SELECT IntID as ID,IntOrderID as OrderID, IntArticleID as ArticleID, IntQuantity as Quantity, Price, strSerial FROM ' . SHOP_TABLE . $where . ' ' . $orderstring . ' ' . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . $this->maxItemsPerPage) : ''));
		$this->anz = $this->DB_WE->num_rows();
	}

	function next_record(){
		if(($ret = $this->DB_WE->next_record(MYSQL_ASSOC))){
			$strSerial = we_unserialize($this->DB_WE->Record['strSerial']);
			unset($this->DB_WE->Record['strSerial']);

			$this->DB_WE->Record['articleIsObject'] = isset($strSerial['OF_ID']) ? 1 : 0;
			if($this->DB_WE->Record['articleIsObject']){//Object based Article
				foreach($strSerial as $key => &$value){
					switch($key){
						case WE_SHOP_ARTICLE_CUSTOM_FIELD:
						case 'WE_VARIANT':
							continue;
						default:
							if(strpos($key, 'we_WE') === false){
								$this->DB_WE->Record[substr($key, 3)] = $value; //key without "we_" because of internal problems in shop modul backend view
							}

							$this->DB_WE->Record[$key] = (is_array($value) ?
									$value :
									(substr($value, 0, 2) === 'a:' ?
										we_unserialize($value) :
										$value));
					}
				}
			} else {//Document based Article
				foreach($strSerial as $key => &$value){
					switch($key){
						case WE_SHOP_ARTICLE_CUSTOM_FIELD:
						case 'Charset':
						case 'WE_VARIANT':
							continue;
						default:
							if(strpos($key, 'wedoc_') === false){
								$this->DB_WE->Record[$key] = $value;
							}
					}
				}
			}

			unset($value);

			foreach($strSerial[WE_SHOP_ARTICLE_CUSTOM_FIELD] as $key => &$value){
				$this->DB_WE->Record[$key] = $value;
			}
			unset($value);
			$this->DB_WE->Record['VARIANT'] = $strSerial['WE_VARIANT'];
			$this->DB_WE->Record[WE_SHOP_VAT_FIELD_NAME] = $strSerial[WE_SHOP_VAT_FIELD_NAME];

			$this->DB_WE->Record['wedoc_Path'] = $this->DB_WE->Record['WE_PATH'] = $this->Path . '?we_orderid=' . $this->DB_WE->Record['OrderID'] . '&we_orderitemid=' . $this->DB_WE->Record['ID'];
			$this->DB_WE->Record['WE_TEXT'] = $this->DB_WE->Record['ID'];
			$this->DB_WE->Record['WE_ID'] = $this->DB_WE->Record['ID'];
			$this->DB_WE->Record['we_wedoc_lastPath'] = $this->LastDocPath . '?we_orderid=' . $this->DB_WE->Record['OrderID'] . '&we_orderitemid=' . $this->DB_WE->Record['ID'];
			$this->count++;
			return true;
		}

		$this->stop_next_row = $this->shouldPrintEndTR();
		if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
			$this->DB_WE->Record = array(
				'WE_PATH' => '',
				'WE_TEXT' => '',
				'WE_ID' => '',
			);
			$this->count++;
			return true;
		}

		return false;
	}

	function f($key){
		return $this->DB_WE->f($key);
	}

}
