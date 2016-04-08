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
 * class
 * @desc    class for tag <we:listview>
 *
 */
class we_listview_collection extends we_listview_document{

	public function __construct($name, $rows, $offset, $order, $desc, $docType, $cats, $catOr, $casesensitive, $workspaceID, $contentTypes, $cols, $searchable, $condition, $calendar, $datefield, $date, $weekstart, $categoryids, $customerFilterType, $subfolders, $customers, $id, $languages, $numorder, $hidedirindex, $triggerID){
		$this->joins = array(' JOIN ' . FILELINK_TABLE . ' fl ON ' . FILE_TABLE . '.ID=fl.remObj');
		$this->orderWhere = array('fl.ID=' . intval($id) . ' AND fl.DocumentTable="tblVFile" AND fl.type="collection"');
		$this->table = VFILE_TABLE;
		$this->group = 'fl.' . ((f('SELECT IsDuplicates FROM ' . VFILE_TABLE . " WHERE ID='" . intval($id) . "'", 0, $this->DB_WE)) ? 'position' : 'ID');
		parent::__construct($name, $rows, $offset, $order? : 'VFILE', $desc, $docType, $cats, $catOr, $casesensitive, $workspaceID, $contentTypes, $cols, false, $condition, $calendar, $datefield, $date, $weekstart, $categoryids, $customerFilterType, $subfolders, $customers, $id, $languages, $numorder, $hidedirindex, $triggerID);
	}

}
