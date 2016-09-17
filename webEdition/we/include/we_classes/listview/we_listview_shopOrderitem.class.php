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
 * class    we_listview_shopOrderitem
 * @desc    class for tag <we:listview type="orderitem">
 *
 */
class we_listview_shopOrderitem extends we_listview_base{
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

		$this->Path = ($this->docID ? id_to_path($this->docID, FILE_TABLE, $this->DB_WE) : (isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : ''));

		$this->hidedirindex = $hidedirindex;
		// IMPORTANT for seeMode !!!! #5317
		$this->LastDocPath = (isset($_SESSION['weS']['last_webEdition_document']) ? $_SESSION['weS']['last_webEdition_document']['Path'] : '');

		$replArray = [
			'OrderID' => 'orderID',
			'OrderIntID' => 'orderID',
			'IntOrderID' => 'orderID',
			'ArticleIntID' => 'orderDocID',
			'IntArticleID' => 'orderDocID', //prevents accidential replacements
			'ArticleID' => 'orderDocID',
			'IntQuantity' => 'quantity', //prevents accidential replacements
			'Quantity' => 'quantity',
		];

		$this->condition = strtr($this->condition, $replArray);
		$this->order = strtr($this->order, $replArray);

		if($this->order){
			$orderstring = ' ORDER BY ' . $this->order . ' ' .
				($this->desc && (!preg_match('|.+ desc$|i', $this->order)) ? ' DESC' : '');
		} else {
			$orderstring = ' ORDER BY ID' . ($this->desc ? ' DESC' : '');
		}

		$where = ($this->condition ?
			' WHERE ' . $this->condition . ($this->orderID ? ' AND orderID=' . $this->orderID : '') :
			($this->orderID ? ' WHERE orderID=' . $this->orderID : ''));

		$this->anz_all = f('SELECT COUNT(1) FROM ' . SHOP_ORDER_ITEM_TABLE . $where, '', $this->DB_WE);

		$this->DB_WE->query('SELECT
	oi.orderID AS ID,
	oi.orderID AS OrderID,
	oi.orderID AS ' . self::PROPPREFIX . 'TEXT,
	oi.orderID AS ' . self::PROPPREFIX . 'ID,
  oi.orderDocID,
  oi.quantity AS Quantity,
  oi.Price,
  oi.Vat,
	oi.Vat AS ' . WE_SHOP_VAT_FIELD_NAME . ',
	oi.customFields,
	od.type AS documentType,
	od.SerializedData,
	od.variant AS VARIANT

	FROM ' . SHOP_ORDER_ITEM_TABLE . ' oi JOIN ' . SHOP_ORDER_DOCUMENT_TABLE . ' od ON oi.orderDocID=od.ID ' . $where . ' ' . $orderstring . ' ' . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . $this->maxItemsPerPage) : ''));
		$this->anz = $this->DB_WE->num_rows();
	}

	function next_record(){
		if(($ret = $this->DB_WE->next_record(MYSQL_ASSOC))){
			$serialDoc = we_unserialize($this->DB_WE->Record['SerializedData']);
			$custFields = we_unserialize($this->DB_WE->Record['customFields']);

			unset($this->DB_WE->Record['SerializedData'], $this->DB_WE->Record['customFields']);

			if($this->DB_WE->Record['documentType'] == 'object'){//Object based Article
				foreach($serialDoc as $key => $value){
					if(strpos($key, 'we_WE') === false){
						$this->DB_WE->Record[substr($key, 3)] = $value; //key without "we_" because of internal problems in shop modul backend view
					}

					/* this should be obsolete
					 * $this->DB_WE->Record[$key] = (is_array($value) ?
					  $value :
					  (substr($value, 0, 2) === 'a:' ?
					  we_unserialize($value) :
					  $value)); */
				}
			} else {//Document based Article
				foreach($serialDoc as $key => $value){
					switch($key){
						case 'Charset':
							continue;
						default:
							if(strpos($key, self::PROPPREFIX) === false){
								$this->DB_WE->Record[$key] = $value;
							}
					}
				}
			}

			foreach($custFields as $key => $value){
				$this->DB_WE->Record[$key] = $value;
			}

			$this->DB_WE->Record[self::PROPPREFIX . 'PATH'] = $this->Path . '?we_orderid=' . $this->DB_WE->Record['OrderID'] . '&we_orderitemid=' . $this->DB_WE->Record['ID'];
			$this->DB_WE->Record[self::PROPPREFIX . 'LASTPATH'] = $this->LastDocPath . '?we_orderid=' . $this->DB_WE->Record['OrderID'] . '&we_orderitemid=' . $this->DB_WE->Record['ID'];
			$this->count++;
			return true;
		}

		$this->stop_next_row = $this->shouldPrintEndTR();
		if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
			$this->DB_WE->Record = ['WE_PATH' => '',
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
