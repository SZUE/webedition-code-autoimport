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
// FIXME: reduce all onchange JS to single function calls

class we_thumbnail_thumbnail extends we_contents_base{
	var $Name = 'new Thumbnail';
	var $Format = '';
	var $Height = 0;
	var $Width = 0;
	var $Options = '';
	var $description = '';
	var $Directory = '';
	var $Quality = 8;

	public function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'Name', 'Format', 'Height', 'Width', 'Options', 'description', 'Directory', 'Quality');
		$this->Table = THUMBNAILS_TABLE;
	}

	function saveInSession(&$save, $toFile = false){
		$save = [[]];
		foreach($this->persistent_slots as $cur){
			$save[0][$cur] = $this->{$cur};
		}
	}

	public function we_initSessDat($sessDat){
		if(is_array($sessDat)){
			foreach($this->persistent_slots as $cur){
				if(isset($sessDat[0][$cur])){
					$this->{$cur} = $sessDat[0][$cur];
				}
			}
		}
		$this->i_setElementsFromHTTP();

	}

}
