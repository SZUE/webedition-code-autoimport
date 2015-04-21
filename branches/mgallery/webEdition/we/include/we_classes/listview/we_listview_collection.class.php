<?php
/**
 * webEdition CMS
 *
 * $Rev: 9750 $
 * $Author: mokraemer $
 * $Date: 2015-04-16 10:59:37 +0200 (Do, 16. Apr 2015) $
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
 * class
 * @desc    class for tag <we:listview>
 *
 */
class we_listview_collection extends we_listview_document{

	public function __construct($ID/* $name, $rows, $offset, $order, $desc, $docType, $cats, $catOr, $casesensitive, $workspaceID, $contentTypes, $cols, $searchable, $condition, $calendar, $datefield, $date, $weekstart, $categoryids, $customerFilterType, $subfolders, $customers, $id, $languages, $numorder, $hidedirindex, $triggerID */){
		$db=$GLOBALS['DB_WE'];
		 $db->query('SELECT remObj FROM ' . FILELINK_TABLE . ' WHERE ID=' . $ID);

		parent::__construct('', 9999, 0, '$order', false, 0, 0, 0, 0, 0, '', 0, 0, '', '', 0, 0, 0, 0, 0, 0, 0, implode(',', $db->getAll(true)), '', '', '', '');
	}

}
