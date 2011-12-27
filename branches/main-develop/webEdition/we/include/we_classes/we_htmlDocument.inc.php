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
 * @package    webEdition_class
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


class we_htmlDocument extends we_textContentDocument{

	/* Name of the class => important for reconstructing the class from outside the class */
	var $ClassName=__CLASS__;
	var $ContentType="text/html";

	function __construct(){
		parent::__construct();
	}

	function i_saveContentDataInDB(){
		if (is_array($this->elements["data"]) && isset($this->elements["data"]["dat"])) {
			$code = $this->elements["data"]["dat"];
			$metas = $this->getMetas($code);
			if (isset($metas["title"]) && $metas["title"]) {
				$this->setElement("Title", $metas["title"]);
			}
			if (isset($metas["description"]) && $metas["description"]) {
				$this->setElement("Description", $metas["description"]);
			}
			if (isset($metas["keywords"]) && $metas["keywords"]) {
				$this->setElement("Keywords", $metas["keywords"]);
			}
			if (isset($metas["charset"]) && $metas["charset"]) {
				$this->setElement("Charset", $metas["charset"]);
			}
		}
		return parent::i_saveContentDataInDB();
	}
	function makeSameNew(){
		parent::makeSameNew();
		$this->Icon = "prog.gif";

	}
	function i_publInScheduleTable(){
		if(defined("SCHEDULE_TABLE")){
			$this->DB_WE->query("DELETE FROM ".SCHEDULE_TABLE." WHERE DID=".intval($this->ID)." AND ClassName='".$this->DB_WE->escape($this->ClassName)."'");
			$ok = true;
			$makeSched = false;
			foreach($this->schedArr as $s){
				if($s["task"] == we_schedpro::SCHEDULE_FROM && $s["active"]){
					$serializedDoc = we_temporaryDocument::load($this->ID,$this->Table,$this->DB_WE);// nicht noch mal unten beim Speichern serialisieren, ist bereits serialisiert #5743
					$makeSched = true;
				}else{
					$serializedDoc = "";
				}
				$Wann = we_schedpro::getNextTimestamp($s,time());

				if(!$this->DB_WE->query("INSERT INTO ".SCHEDULE_TABLE.
						" (DID,Wann,Was,ClassName,SerializedData,Schedpro,Type,Active)
						VALUES(".intval($this->ID).",'".$this->DB_WE->escape($Wann)."','".$this->DB_WE->escape($s["task"])."','".$this->DB_WE->escape($this->ClassName)."','".$this->DB_WE->escape($serializedDoc)."','".$this->DB_WE->escape(serialize($s))."','".$this->DB_WE->escape($s["type"])."','".$this->DB_WE->escape($s["active"])."')"))
								return false;
			}
			return $makeSched;
		}
		return false;
	}

	function getDocumentCode(){

		$code = $this->getElement("data");

		if( isset($this->elements["Charset"]["dat"]) && $this->elements["Charset"]["dat"] ){
			$code = preg_replace( "'<meta http-equiv=\"Content-Type\" content=\".*>'i", '<meta http-equiv="Content-Type" content="text/html; charset=' . $this->elements["Charset"]["dat"] . '">', $code );
		}
		return $code;
	}
}
