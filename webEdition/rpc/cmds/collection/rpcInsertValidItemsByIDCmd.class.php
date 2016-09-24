<?php

/**
 * webEdition CMS
 *
 * $Rev: 12330 $
 * $Author: mokraemer $
 * $Date: 2016-06-24 20:15:32 +0200 (Fr, 24 Jun 2016) $
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

include_once(dirname(__FILE__) . '/rpcGetValidItemsByIDCmd.class.php');

class rpcInsertValidItemsByIDCmd extends rpcGetValidItemsByIDCmd{

	function __construct(){
		parent::__construct();
	}

	function execute(){
		$validItems = $this->getValidItems();
		$insertPos = -1; // used by addFromTree => loop through commands!

		if(($validItems = $this->getValidItems())){
			$result = $this->collection->addItemsToCollection($validItems, $this->initSessDat ? $insertPos : -1);
			if($this->initSessDat){
				$this->collection->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
			} else {
				$this->collection->save();
			}
			//$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('weClass', '[collection][insertedAndDuplicates]'), implode(',', $result[0]), implode(',', $result[1])), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			
		}

		return $this->resp;
	}

}