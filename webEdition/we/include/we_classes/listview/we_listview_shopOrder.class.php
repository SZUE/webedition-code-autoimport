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
	private static $replArray = [
		'OrderID' => 'ID',
		'OrderIntID' => 'ID',
		'IntOrderID' => 'ID',
		'CustomerIntID' => 'customerID',
		'IntCustomerID' => 'customerID',
		'CustomerID' => 'customerID',
	];

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
		$this->condition = strtr($this->condition, self::$replArray);
		$this->order = strtr($this->order, self::$replArray);

		if($this->order){
			$orderstring = ' ORDER BY ' . $this->order . ' ' .
				($this->desc && (!preg_match('|.+ desc$|i', $this->order)) ? ' DESC' : '');
		} else {
			$orderstring = ' ORDER BY ID' . ($this->desc ? ' DESC' : '');
		}

		$where = ($this->condition ? (' WHERE ' . $this->condition) : '');

		$this->anz_all = f('SELECT COUNT(1) FROM ' . SHOP_ORDER_TABLE . $where, '', $this->DB_WE);
		$format = [];
		foreach(we_shop_statusMails::$BaseDateFields as $field){
			$format[] = 'UNIX_TIMESTAMP(' . $field . ') AS ' . $field;
		}

		$this->DB_WE->query('SELECT
	ID AS OrderID,
	ID AS ' . self::PROPPREFIX . 'ID,
	ID AS ' . self::PROPPREFIX . 'ORDERID,
	customerID AS CustomerID,
	customerID AS ' . self::PROPPREFIX . 'CID,
	customOrderNo,
	customFields,
	pricesNet AS shopPriceIsNet,
	calcVat AS shopCalcVat,
	priceName AS shopPricename,
	shippingCost AS Shipping_costs,
	shippingNet AS Shipping_isNet,
	shippingVat AS Shipping_vatRate,
	customerData,
	' . implode(',', $format) .
			' FROM ' . SHOP_ORDER_TABLE . $where . ' ' . $orderstring . ' ' . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . intval($this->maxItemsPerPage)) : ''));
		$this->anz = $this->DB_WE->num_rows();
	}

	function next_record(){
		$ret = $this->DB_WE->next_record(MYSQL_ASSOC);
		if($ret){
			$custFields = we_unserialize($this->DB_WE->Record['customFields']);
			$customerData = we_unserialize($this->DB_WE->Record['customerData']);
			unset($this->DB_WE->Record['customFields'], $this->DB_WE->Record['customerData']);

			//get all missing date fields
			$this->DB_WE->Record = array_merge(
				$this->DB_WE->Record, $GLOBALS['DB_WE']->getAllFirstq('SELECT type,UNIX_TIMESTAMP(date) FROM ' . SHOP_ORDER_DATES_TABLE . ' WHERE ID=' . $this->DB_WE->Record['OrderID'], false), [
				self::PROPPREFIX . 'PATH' => $this->Path . '?we_orderid=' . $this->DB_WE->Record['OrderID'],
				self::PROPPREFIX . 'TEXT' => '',
				self::PROPPREFIX . 'LASTPATH' => $this->LastDocPath . '?we_orderid=' . $this->DB_WE->Record['OrderID']
				]
			);

			foreach($custFields as $key => $value){
				$this->DB_WE->Record[$key] = $value;
			}

			foreach($customerData as $key => $value){
				$this->DB_WE->Record['Customer_' . $key] = $value;
			}

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
