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

	
	var $docType = ""; /* doctype string */
	var $IDs = array();/* array of ids with pages which are found */
	var $foundlinks = array();
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
	var $dirsearchtable ='';
	var $showself =false;
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
	function we_langlink_listview($name="0", $rows=999999999, $offset=0, $order="", $desc=false, $linkType='tblFile', $cols="", $seeMode=true,$searchable=true, $customerFilterType='off',  $showself=false, $id="", $ownlanguage="",$hidedirindex = false,$objectseourls=false){
		
		listviewBase::listviewBase($name, $rows, $offset, $order, $desc, '', false, '', $cols, '', '', '', '', '', 'off', $id);
		
		$this->showself=$showself;
		$this->objectseourls=$objectseourls;
		$this->hidedirindex=$hidedirindex;
		$this->id=$id;
		$this->ownlanguage=$ownlanguage;
		$this->linkType=$linkType;
		$this->seeMode   = $seeMode;		
		

		$_languages = $GLOBALS['weFrontendLanguages'];
		
		if(isset($_languages[$this->ownlanguage])){ unset($_languages[$this->ownlanguage]);	}
		
		if($this->showself && !isset($_languages[$this->ownlanguage]) ){
			
		}
		if(stripos($this->order," desc") !== false){//was #3849
			$this->order = str_ireplace(" desc","",$this->order);
			$this->desc = true;
		}

		$this->order = trim($this->order);

		$orderstring = $this->order ? (" ORDER BY " . LANGLINK_TABLE.".".$this->order . ($this->desc ? " DESC" : "")) : '' ;
		if($this->order=='Locale'){
			if ($this->desc){krsort($_languages);}else{ksort($_languages);}
		}
		
		if($this->id && ($this->linkType=='tblFile' || $this->linkType=='tblObjectFile') ){
			foreach ($_languages as $langkey => $lang){
				if ($this->linkType=='tblFile'){ 
					$q= "SELECT ".LANGLINK_TABLE.".DID as DID, ".LANGLINK_TABLE.".DLocale as DLocale, ".LANGLINK_TABLE.".LDID as LDID, ".LANGLINK_TABLE.".Locale as Locale, ".LANGLINK_TABLE.".IsFolder as IsFolder, ".LANGLINK_TABLE.".IsObject as IsObject, ".LANGLINK_TABLE.".DocumentTable as DocumentTable, ".FILE_TABLE.".Path as Path, ".FILE_TABLE.".ParentID as ParentID  FROM ". LANGLINK_TABLE . "," . FILE_TABLE ." WHERE ".LANGLINK_TABLE.".Locale='".$langkey."' AND ".LANGLINK_TABLE.".LDID = ".FILE_TABLE.".ID AND ".FILE_TABLE.".Published >0 AND ".LANGLINK_TABLE.".DocumentTable='".$this->linkType."' AND ".LANGLINK_TABLE.".DID='".$this->id."'";
					$this->dirsearchtable =FILE_TABLE; 
				} else {
					$q= "SELECT ".LANGLINK_TABLE.".DID as DID, ".LANGLINK_TABLE.".DLocale as DLocale, ".LANGLINK_TABLE.".LDID as LDID, ".LANGLINK_TABLE.".Locale as Locale, ".LANGLINK_TABLE.".IsFolder as IsFolder, ".LANGLINK_TABLE.".IsObject as IsObject, ".LANGLINK_TABLE.".DocumentTable as DocumentTable, ".OBJECT_FILES_TABLE.".Path as Path, ".OBJECT_FILES_TABLE.".Url as Url, ". OBJECT_FILES_TABLE.".TriggerID as TriggerID  FROM ". LANGLINK_TABLE . "," . OBJECT_FILES_TABLE ." WHERE ".LANGLINK_TABLE.".Locale='".$langkey."' AND ".LANGLINK_TABLE.".LDID = ".OBJECT_FILES_TABLE.".ID AND ".OBJECT_FILES_TABLE.".Published >0 AND ".LANGLINK_TABLE.".DocumentTable='".$this->linkType."' AND ".LANGLINK_TABLE.".DID='".$this->id."'";
					$this->dirsearchtable =OBJECT_FILES_TABLE; 
				}
				
				$this->DB_WE->query($q);
				$found=$this->DB_WE->num_rows();
				if($found){
					$this->DB_WE->next_record();
					$this->foundlinks[] = $this->DB_WE->Record;
				} else {
					$this->getParentData($this->id,$langkey);
				}
				
			}
			if($this->showself && !isset($_languages[$this->ownlanguage]) ){
				$dt = array('DID'=>$this->id,'DLocale'=> $this->ownlanguage,'LDID'=>$this->id,'Locale'=> $this->ownlanguage,'DocumentTable'=> (($this->linkType=='tblFile')?'tblFile':'tblObjectFile'), 'IsObject'=>(($this->linkType=='tblFile')?'0':'1'),'IsFolder'=>0);
				if($this->linkType=='tblFile'){
					$dt['Path']= id_to_path($this->id,FILE_TABLE);
				} else {
					$dt['Path']= id_to_path($this->id,OBJECT_FILES_TABLE);
					$row=getHash("SELECT Url, TriggerID FROM ".OBJECT_FILES_TABLE." WHERE ID='" . abs($this->id)."'",$this->DB_WE);
					$dt['Url']= $row['Url'];
					$dt['TriggerID']= $row['TriggerID'];
				}
				$this->foundlinks[]=$dt;
			}
			if($this->order == "random()"){
				shuffle($this->foundlinks);
			}
			
			
			$this->anz_all = count($this->foundlinks);
			$this->anz = $this->anz_all;
			$this->count = 0;
			$this->adjustRows();
		} else {
			$this->anz_all = 0;
			$this->anz = $this->anz_all;
			$this->count = 0;
	
	}

	

	}
	function getParentData($myid,$langkey){
		$pid=f("SELECT ParentID FROM ".$this->dirsearchtable." WHERE ID='".abs($myid)."'",'ParentID',$this->DB_WE);
		
		if ($pid){
			if ($this->linkType=='tblFile'){ 
				$q= "SELECT ".LANGLINK_TABLE.".DID as DID, ".LANGLINK_TABLE.".DLocale as DLocale, ".LANGLINK_TABLE.".LDID as LDID, ".LANGLINK_TABLE.".Locale as Locale, ".LANGLINK_TABLE.".IsFolder as IsFolder, ".LANGLINK_TABLE.".IsObject as IsObject, ".LANGLINK_TABLE.".DocumentTable as DocumentTable, ".FILE_TABLE.".Path as Path, ".FILE_TABLE.".ParentID as ParentID  FROM ". LANGLINK_TABLE . "," . FILE_TABLE ." WHERE ".LANGLINK_TABLE.".Locale='".$langkey."' AND ".LANGLINK_TABLE.".LDID = ".FILE_TABLE.".ID AND ".FILE_TABLE.".Published >0 AND ".LANGLINK_TABLE.".DocumentTable='tblFile' AND ".LANGLINK_TABLE.".DID='".$pid."'";
			} else {
				$q= "SELECT ".LANGLINK_TABLE.".DID as DID, ".LANGLINK_TABLE.".DLocale as DLocale, ".LANGLINK_TABLE.".LDID as LDID, ".LANGLINK_TABLE.".Locale as Locale, ".LANGLINK_TABLE.".IsFolder as IsFolder, ".LANGLINK_TABLE.".IsObject as IsObject, ".LANGLINK_TABLE.".DocumentTable as DocumentTable, ".FILE_TABLE.".Path as Path, ".FILE_TABLE.".ParentID as ParentID  FROM ". LANGLINK_TABLE . "," . FILE_TABLE ." WHERE ".LANGLINK_TABLE.".Locale='".$langkey."' AND ".LANGLINK_TABLE.".LDID = ".FILE_TABLE.".ID AND ".FILE_TABLE.".Published >0 AND ".LANGLINK_TABLE.".DocumentTable='tblFile' AND ".LANGLINK_TABLE.".DID='".$pid."'";
			}

			$this->DB_WE->query($q);
			$found=$this->DB_WE->num_rows();
		  	if($found){
			  $this->DB_WE->next_record();
			  $this->foundlinks[] = $this->DB_WE->Record;
		  	} else {
			  $this->getParentData($pid,$langkey);
		  	}
		}
	}
	function next_record(){
		if($this->count < $this->anz_all){
			$count=$this->count;
			$this->Record["WE_LANG"] = $this->ownlanguage;
			$this->Record["WE_ID"] = $this->foundlinks[$count]["LDID"];
			$this->Record["WE_DOCUMENTLOCALE"] = $this->foundlinks[$count]["DLocale"];
			$dLocale=explode('_',$this->foundlinks[$count]["DLocale"]);
			$this->Record["WE_DOCUMENTCOUNTRY"] = $dLocale[1];
			$this->Record["WE_DOCUMENTLANGUAGE"] = $dLocale[0];
			$this->Record["WE_TARGETLOCALE"] = $this->foundlinks[$count]["Locale"];
			$Locale=explode('_',$this->foundlinks[$count]["Locale"]);
			$this->Record["WE_TARGETCOUNTRY"] = $Locale[1];
			$this->Record["WE_TARGETLANGUAGE"] = $Locale[0];
			if ($this->foundlinks[$count]['DocumentTable'] == 'tblFile'){
				$this->Record["WE_PATH"] = $this->foundlinks[$count]["Path"];
			} else {
				
				if(isset($this->foundlinks[$count]['TriggerID']) && $this->foundlinks[$count]['TriggerID']){
					$path_parts = pathinfo(id_to_path($this->foundlinks[$count]['TriggerID']));
				} else {
					$path_parts = pathinfo($_SERVER['PHP_SELF']);
				}
				$paramName="we_object_ID";
				
				if ($this->objectseourls && $this->foundlinks[$count]['Url']!='' && show_SeoLinks() ){
					
					
					if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && $this->hidedirindex && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
						$this->Record["WE_PATH"] = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'. $this->foundlinks[$count]['Url'];
					} else {
						$this->Record["WE_PATH"] = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'.$path_parts['filename'].'/'.$this->foundlinks[$count]['Url'];
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
		} else {
			$this->stop_next_row = $this->shouldPrintEndTR();
			if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
				$this->Record = array();
				$this->Record["WE_PATH"] = "";
				$this->Record["WE_TEXT"] = "";
				$this->Record["WE_ID"] = "";
				$this->count++;
				return true;
			}
		}
		return false;
	}

	function f($key){
		return isset($this->Record[$key]) ? $this->Record[$key] : "";
	}



}
