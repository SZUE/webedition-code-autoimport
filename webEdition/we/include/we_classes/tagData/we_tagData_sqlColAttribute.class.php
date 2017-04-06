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
class we_tagData_sqlColAttribute extends we_tagData_selectAttribute{

	/**
	 * @var string
	 */
	var $Table;

	/**
	 * @param string $name
	 * @param string $table
	 * @param boolean $required
	 * @param array $filter
	 */
	function __construct($name, $table, $required = false, $filter = [], $module = '', $description = '', $deprecated = false){

		$this->Table = $table;

		$options = [];

		// get options from choosen table
		$tableInfo = $GLOBALS['DB_WE']->metadata($this->Table, we_database_base::META_NAME);
		sort($tableInfo); // #3490

		foreach($tableInfo as $name){
			if(!in_array($name, $filter)){
				$options[] = new we_tagData_option($name);
			}
		}
		parent::__construct($name, $options, $required, $module, $description, $deprecated);
	}

}

//FIXME: remove
class weTagData_sqlColAttribute extends we_tagData_sqlColAttribute{

}