<?php
/**
 * webEdition CMS
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
 * @package    webEdition_listview
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/listview/"."listviewBase.class.php");

/**
* class    we_listview
* @desc    class for tag <we:listview>
*
*/

class we_langlink_listview extends listviewBase {

	var $DB_WE2; /* 2nd DB Object */
	var $docType = ""; /* doctype string */
	var $IDs = array();/* array of ids with pages which are found */
	
	var $ClassName = "we_langlink_listview";
	var $linkType = "";
	var $searchable = true;
 	var $condition = ""; /* condition string (like SQL) */
 	var $defaultCondition = "";
 	var $customerFilterType = 'off'; // shall we control customer-filter?
	var $subfolders = true; // regard subfolders
	var $customers = "";
	var $languages = ""; //string of Languages, separated by ,
	var $numorder = false; // #3846
	var $objectseourls = false;
	var $hidedirindex = false;
	var $ownlanguage ='';
	/**
	 * we_listview()
	 * constructor of class
	 *
	 * @param   name          string  - name of listview
	 * @param   rows          integer - number of rows to display per page
	 * @param   offset        integer - start offset of first page
	 * @param   order         string  - field name(s) to order by
	 * @param   desc          boolean - set to true, if order should be descendend
	 * @param   workspaceID   string - commaseperated list of id's of workspace
	 * @param   contentTypes  string  - contenttypes of documents (image,text ...)
	 * @param   cols   		  integer - to display a table this is the number of cols
	 * @param   searchable 	  boolean - if false then show also documents which are not marked as searchable
	 * @return we_listview
	 */
	function we_langlink_listview($name="0", $rows=999999999, $offset=0, $order="", $desc=false, $linkType='file', $cols="", $seeMode=true,$searchable=true, $customerFilterType='off',  $id="", $ownlanguage="",$hidedirindex = false,$objectseourls=false){
		
		listviewBase::listviewBase($name, $rows, $offset, $order, $desc, '', false, '', $cols, '', '', '', '', '', 'off', $id);
		
		$this->DB_WE2 = new DB_WE();
		$this->objectseourls=$objectseourls;
		$this->hidedirindex=$hidedirindex;
		$this->id=$id;
		$this->ownlanguage=$ownlanguage;
		if ($linkType=='file'){ 
			$this->linkType='tblFile';
		} else {
			$this->linkType='tblObjectFile';
		}
		$this->seeMode   = $seeMode;		
		if(stripos($this->order," desc") !== false){//was #3849
			$this->order = str_ireplace(" desc","",$this->order);
			$this->desc = true;
		}

		$this->order = trim($this->order);

		$orderstring = $this->order ? (" ORDER BY " . $this->order . ($this->desc ? " DESC" : "")) : '' ;
		
		$tail = "DocumentTable='".$this->linkType."' AND DID='".$this->id."' AND LDID !=0";

				
		if($this->order == "random()"){
			$q = "SELECT *, RAND() as RANDOM FROM " . LANGLINK_TABLE ." WHERE $tail ORDER BY RANDOM";
		}else{
			$q = "SELECT *, RAND() as RANDOM FROM " . LANGLINK_TABLE ." WHERE $tail $orderstring";
		}

		$this->DB_WE->query($q);
		$this->anz_all = $this->DB_WE->num_rows();

		if($this->order == "random()"){
			$q = "SELECT *, RAND() as RANDOM FROM " . LANGLINK_TABLE ." WHERE $tail ORDER BY RANDOM". (($this->rows > 0) ? (" limit ".$this->start.",".$this->rows) : "");
		}else{
			$q = "SELECT * FROM " . LANGLINK_TABLE ." WHERE $tail $orderstring". (($this->rows > 0) ? (" limit ".$this->start.",".$this->rows) : "");
		}

		$this->DB_WE->query($q);
		$this->anz = $this->DB_WE->num_rows();

		$this->count = 0;
		$this->adjustRows();


	

	}

	function next_record(){
		if($this->DB_WE->next_record()){
			$count=$this->count;
			$this->Record["WE_LANG"] = $this->ownlanguage;
			$this->Record["WE_ID"] = $this->DB_WE->Record["LDID"];
			$this->Record["WE_LOCALE"] = $this->DB_WE->Record["Locale"];
			if ($this->linkType == 'tblFile'){
				$this->Record["WE_PATH"] = f("SELECT Path FROM ".FILE_TABLE." WHERE ID='".$this->Record["WE_ID"]."' ",'Path',$this->DB_WE2);
			} else {
				$myhash =getHash("SELECT Path, Url, TriggerID FROM " . OBJECT_FILES_TABLE . " WHERE ID=" .$this->Record["WE_ID"],$this->DB_WE2);
				if(isset($myhash['TriggerID']) && $myhash['TriggerID']){
					$path_parts = pathinfo(id_to_path($myhash['TriggerID']));
				} else {
					$path_parts = pathinfo($_SERVER['PHP_SELF']);
				}
				$paramName="we_object_ID";
				
				if ($this->objectseourls && $myhash['Url']!='' && show_SeoLinks() ){
					
					
					if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $this->hidedirindex && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
						$this->Record["WE_PATH"] = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'. $myhash['Url'];
					} else {
						$this->Record["WE_PATH"] = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'.$path_parts['filename'].'/'.$myhash['Url'];
					}
				} else {
					if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $this->hidedirindex && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
						$this->Record["WE_PATH"] = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'."?$paramName=".$this->Record["WE_ID"];
					} else {
						$this->Record["WE_PATH"] =$_SERVER['PHP_SELF']."?$paramName=".$this->Record["WE_ID"];
					}
				}
			}
			$this->Record["Path"] = $this->Record["WE_PATH"];
			
			$this->Record["ID"] = $this->Record["WE_ID"];

			$this->count++;
			return true;
		} else if($this->cols && ($this->count < $this->rows)){
			$this->DB_WE->Record = array();
			$this->DB_WE->Record["WE_PATH"] = "";
			$this->DB_WE->Record["WE_TEXT"] = "";
			$this->DB_WE->Record["WE_ID"] = "";
			$this->count++;
			return true;
		}
		return false;
	}

	function f($key){
		return isset($this->Record[$key]) ? $this->Record[$key] : "";
	}



}
