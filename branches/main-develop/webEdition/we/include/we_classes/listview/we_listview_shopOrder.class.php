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
 * class    we_listview_shopOrder
 * @desc    class for tag <we:listview type="'>
 *
 */
class we_listview_shopOrder extends we_listview_base{
	var $condition = '';
	var $Path = '';
	var $docID = 0;
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
	public function __construct($name, $rows, $offset, $order, $desc, $condition, $cols, $docID, $hidedirindex){
		parent::__construct($name, $rows, $offset, $order, $desc, '', false, 0, $cols);
		if($GLOBALS['WE_MAIN_DOC']->InWebEdition){
			//do nothing inside we
			return;
		}
		$this->docID = $docID;
		$this->condition = $condition;
		$this->hidedirindex = $hidedirindex;
		// IMPORTANT for seeMode !!!! #5317
		$this->LastDocPath = (isset($_SESSION['weS']['last_webEdition_document'])) ? $_SESSION['weS']['last_webEdition_document']['Path'] : '';
		$this->Path = $this->docID ? id_to_path($this->docID, FILE_TABLE, $this->DB_WE) : (isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : '');

		if(strpos($this->condition, 'ID') !== false && strpos($this->condition, 'IntID') === false){
			$this->condition = str_replace('ID', 'IntID', $this->condition);
		}
		// und nun sind alle anderen kaputt und werden repariert
		$this->condition = strtr($this->condition, array(
			'OrderIntID' => 'OrderID',
			'CustomerIntID' => 'CustomerID',
			'ArticleIntID' => 'ArticleID',
			'IntOrderID' => 'IntOrderID', //prevents accidential replacements
			'OrderID' => 'IntOrderID',
			'IntCustomerID' => 'IntCustomerID', //prevents accidential replacements
			'CustomerID' => 'IntCustomerID',
			'IntArticleID' => 'IntArticleID', //prevents accidential replacements
			'ArticleID' => 'IntArticleID',
			'IntQuantity' => 'IntQuantity', //prevents accidential replacements
			'Quantity' => 'IntQuantity',
			'IntPayment_Type' => 'IntPayment_Type', //prevents accidential replacements
			'Payment_Type' => 'IntPayment_Type'
		));

		if($this->desc && $this->order && (!preg_match('|.+ desc$|i', $this->order))){
			$this->order .= ' DESC';
		}

		if($this->order){
			switch(trim($this->order)){
				case 'ID':
				case 'CustomerID':
				case 'ArticleID':
				case 'Quantity':
				case 'Payment_Type':
					$this->order = 'Int' . trim($this->order);
			}
			$orderstring = ' ORDER BY ' . $this->order . ' ';
		} else {
			$orderstring = '';
		}

		$where = ($this->condition ? (' WHERE ' . $this->condition) : '') . ' GROUP BY IntOrderID';

		$this->anz_all = f('SELECT COUNT(1) FROM ' . SHOP_TABLE . $where, '', $this->DB_WE);
		$format = [];
		foreach(we_shop_statusMails::$StatusFields as $field){
			$format[] = 'UNIX_TIMESTAMP(' . $field . ') AS ' . $field;
		}
		foreach(we_shop_statusMails::$MailFields as $field){
			$format[] = 'UNIX_TIMESTAMP(' . $field . ') AS ' . $field;
		}

		$this->DB_WE->query('SELECT IntOrderID AS OrderID,IntCustomerID AS CustomerID,IntPayment_Type AS Payment_Type,strSerialOrder,' . implode(',', $format) . ' FROM ' . SHOP_TABLE . $where . ' ' . $orderstring . ' ' . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . max(100, $this->maxItemsPerPage)) : ''));
		$this->anz = $this->DB_WE->num_rows();
	}

	function next_record(){
		$ret = $this->DB_WE->next_record();
		if($ret){
			$strSerialOrder = we_unserialize($this->DB_WE->Record['strSerialOrder']);
			unset($this->DB_WE->Record['strSerialOrder']);

			if(is_array($strSerialOrder['we_sscf'])){
				foreach($strSerialOrder['we_sscf'] as $key => &$value){
					$this->DB_WE->Record[$key] = $value;
				}
				unset($value);
			}
			if(is_array($strSerialOrder['we_shopPriceShipping'])){
				foreach($strSerialOrder['we_shopPriceShipping'] as $key => &$value){
					$this->DB_WE->Record['Shipping_' . $key] = $value;
				}
				unset($value);
			}
			if(is_array($strSerialOrder['we_shopCustomer'])){
				foreach($strSerialOrder['we_shopCustomer'] as $key => &$value){
					if(!is_numeric($key)){
						$this->DB_WE->Record['Customer_' . $key] = $value;
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
			//Fix #7993
			if(isset($strSerialOrder['we_shopPricename'])){
				$this->DB_WE->Record['shopPricename'] = $strSerialOrder['we_shopPricename'];
			}

			//$this->DB_WE->Record['CustomerID'] = $this->DB_WE->Record['IntCustomerID'];
			$this->DB_WE->Record['we_cid'] = $this->DB_WE->Record['CustomerID'];
			$this->DB_WE->Record['we_orderid'] = $this->DB_WE->Record['OrderID'];
			$this->DB_WE->Record['WE_PATH'] = $this->DB_WE->Record['wedoc_Path'] = $this->Path . '?we_orderid=' . $this->DB_WE->Record['OrderID'];
			$this->DB_WE->Record['WE_TEXT'] = ''; //$this->DB_WE->Record['OrderID'];
			$this->DB_WE->Record['WE_ID'] = $this->DB_WE->Record['OrderID'];
			$this->DB_WE->Record['we_wedoc_lastPath'] = $this->LastDocPath . '?we_orderid=' . $this->DB_WE->Record['OrderID'];
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
