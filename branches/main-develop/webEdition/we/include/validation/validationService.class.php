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
class validationService{

	var $id;
	var $art;	//  custom | default
	var $category; //  xhtml | link | css | accessibility
	var $name;
	var $host;
	var $path;
	var $method;
	var $varname;
	var $checkvia;
	var $additionalVars;
	var $ctype;
	var $fileEndings;
	var $active;

	public function __construct($id = 0, $art = "", $category = "", $name = "", $host = "", $path = "", $method = "", $varname = "", $checkvia = "", $ctype = "", $additionalVars = "", $fileEndings = "", $active = true){

		$this->id = $id;	//  id to edit this service

		$this->art = $art;
		$this->category = $category;

		$this->name = $name;
		$this->host = $host;
		$this->path = $path;
		$this->method = $method;
		$this->varname = $varname;
		$this->checkvia = $checkvia;
		$this->ctype = $ctype;
		$this->additionalVars = $additionalVars;

		$this->fileEndings = $fileEndings;

		$this->active = $active;
	}

	function getName(){
		return $this->art . '_' . $this->id;
	}

}
