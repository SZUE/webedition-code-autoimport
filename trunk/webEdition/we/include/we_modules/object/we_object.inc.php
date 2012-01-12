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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/we_document.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/weSuggest.class.inc.php");
include_once(WE_OBJECT_MODULE_DIR ."we_class_folder.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/global.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/enc_we_class.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/modules/object_value.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/modules/object_url.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/modules/object.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/cache.inc.php");

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_hook/class/weHook.class.php");

/* a class for handling templates */
class we_object extends we_document {
	//######################################################################################################################################################
	//##################################################################### Variables ######################################################################
	//######################################################################################################################################################

	/* Name of the class => important for reconstructing the class from outside the class */
	var $ClassName="we_object";

	/* Icon which is shown at the tree-menue  */
	var $Icon="object.gif";
	var $Published="1";
	var $ID = 0;
	var $ObjectID = 0;

	var $Users = ""; // Default Owners
	var $UsersReadOnly=""; // For DefaultOwners
	var $RestrictUsers="";

	var $Category = "";
	var $Workspaces = "";
	var $DefaultWorkspaces = "";
	var $Table=OBJECT_TABLE;
	var $WorkspaceFlag = 1;

	var $Templates = "";

	var $SerializedArray = array(); // #3931

	var $EditPageNrs = array(WE_EDITPAGE_PROPERTIES,WE_EDITPAGE_WORKSPACE,WE_EDITPAGE_INFO,WE_EDITPAGE_CONTENT); // ,WE_EDITPAGE_PREVIEW

	var $InWebEdition = false;
	var $CSS = "";
	//######################################################################################################################################################
	//##################################################################### FUNCTIONS ######################################################################
	//######################################################################################################################################################


	//##################################################################### INIT FUNCTIONS ######################################################################

	/* Constructor */
	function we_object(){
		$this->we_document();
        $this->CacheType = defined("WE_CACHE_TYPE") ? WE_CACHE_TYPE : "none";
        $this->CacheLifeTime = defined("WE_CACHE_LIFETIME") ? WE_CACHE_LIFETIME : 0;
		array_push($this->persistent_slots,"WorkspaceFlag","RestrictUsers","UsersReadOnly","Text","SerializedArray","Templates","Workspaces","DefaultWorkspaces","ID","Users","strOrder","Category","DefaultCategory","DefaultText","DefaultValues","DefaultTitle","DefaultKeywords","DefaultUrl","DefaultUrlfield0","DefaultUrlfield1","DefaultUrlfield2","DefaultUrlfield3","DefaultTriggerID","DefaultDesc","CSS","CacheType","CacheLifeTime");
		if(defined('DEFAULT_CHARSET')) {
			$this->elements["Charset"]["dat"] = DEFAULT_CHARSET;
		}

	}


	//##################################################################### SAVE FUNCTIONS ######################################################################
	/* saves the document */
	function save(){
global $l_we_class,$we_JavaScript,$we_responseText, $we_responseTextType;

		if(!$this->checkIfPathOk()){
			return false;
		}

		$this->ModDate = time();
		$this->ModifierID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : 0;

		$this->saveToDB();

		$we_responseText = $l_we_class["response_save_ok"];
		$we_responseTextType = WE_MESSAGE_NOTICE;

		$db = new DB_WE();

		if($this->OldPath && ($this->OldPath != $this->Path)){
			$fID = f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Path='".$db->escape($this->OldPath)."'","ID",$this->DB_WE);
			$pID = abs(f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Path='".str_replace("\\","/",dirname($this->Path))."'","ID",$this->DB_WE));
			$cf = new we_class_folder();
			$cf->initByID($fID,OBJECT_FILES_TABLE);
			$cf->Text=$this->Text;
			$cf->Filename=$this->Text;
			$cf->setParentID($pID);
			$cf->Path = $cf->getPath();
			$cf->we_save('1');
			$cf->modifyChildrenPath();
		}

		$this->OldPath = $this->Path; // reset oldPath
		$we_JavaScript = "top.we_cmd('reload_editpage');_EditorFrame.setEditorDocumentId(".$this->ID.");\n".$this->getUpdateTreeScript();

	}

	function saveToDB(){
		$arrt = array();
		$arrt["WorkspaceFlag"] = $this->WorkspaceFlag;

		//	Save charsets in defaultvalues
		//	charset must be in other namespace -> for header !!!
		$arrt["elements"]["Charset"]["dat"] = $this->elements["Charset"]["dat"];

		$this->wasUpdate = $this->ID ? true : false;
		if(isset($this->elements["Defaultanzahl"]["dat"])){
			$this->DefaultText="";

			for($i=0; $i <= $this->elements["Defaultanzahl"]["dat"];$i++){
				$was = "DefaultText_".$i;
				if($this->elements[$was]["dat"]!=""){ //&& in_array($this->elements[$was]["dat"],$var_flip)
				if(stristr($this->elements[$was]["dat"], 'unique')){
					$this->elements[$was]["dat"] = "%".str_replace("%","",$this->elements[$was]["dat"]).(($this->elements["unique_".$i]["dat"]>0)?$this->elements["unique_".$i]["dat"]:"16")."%";
					//echo $this->elements[$was]["dat"];
				}
				$this->DefaultText .= $this->elements[$was]["dat"];
				}
			}

		}
		if(isset($this->elements["DefaultanzahlUrl"]["dat"])){
			$this->DefaultUrl="";

			for($i=0; $i <= $this->elements["DefaultanzahlUrl"]["dat"];$i++){
				$was = "DefaultUrl_".$i;
				if($this->elements[$was]["dat"]!=""){ //&& in_array($this->elements[$was]["dat"],$var_flip)
				if(stristr($this->elements[$was]["dat"], 'urlunique')){
					$this->elements[$was]["dat"] = "%".str_replace("%","",$this->elements[$was]["dat"]).(($this->elements["urlunique_".$i]["dat"]>0)?$this->elements["urlunique_".$i]["dat"]:"16")."%";
				}
				if(stristr($this->elements[$was]["dat"], 'urlfield1')){
					$this->elements[$was]["dat"] = "%".str_replace("%","",$this->elements[$was]["dat"]).(($this->elements["urlfield1_".$i]["dat"]>0)?$this->elements["urlfield1_".$i]["dat"]:"64")."%";
				}
				if(stristr($this->elements[$was]["dat"], 'urlfield2')){
					$this->elements[$was]["dat"] = "%".str_replace("%","",$this->elements[$was]["dat"]).(($this->elements["urlfield2_".$i]["dat"]>0)?$this->elements["urlfield2_".$i]["dat"]:"64")."%";
				}
				if(stristr($this->elements[$was]["dat"], 'urlfield3')){
					$this->elements[$was]["dat"] = "%".str_replace("%","",$this->elements[$was]["dat"]).(($this->elements["urlfield3_".$i]["dat"]>0)?$this->elements["urlfield3_".$i]["dat"]:"64")."%";
				}
				$this->DefaultUrl .= $this->elements[$was]["dat"];
				}
			}

		}

		if(!$this->wasUpdate){

			$q = " ID BIGINT NOT NULL AUTO_INCREMENT, ";
			$q .= " OF_ID BIGINT NOT NULL, ";
			$q .= " OF_ParentID BIGINT NOT NULL, ";
			$q .= " OF_Text VARCHAR(255) NOT NULL, ";
			$q .= " OF_Path VARCHAR(255) NOT NULL, ";
			$q .= " OF_Url VARCHAR(255) NOT NULL, ";
			$q .= " OF_TriggerID  BIGINT NOT NULL  default '0', ";
			$q .= " OF_Workspaces VARCHAR(255) NOT NULL, ";
			$q .= " OF_ExtraWorkspaces VARCHAR(255) NOT NULL, ";
			$q .= " OF_ExtraWorkspacesSelected VARCHAR(255) NOT NULL, ";
			$q .= " OF_Templates VARCHAR(255) NOT NULL, ";
			$q .= " OF_ExtraTemplates VARCHAR(255) NOT NULL, ";
			$q .= " OF_Category VARCHAR(255) NOT NULL,";
			$q .= " OF_Published int(11) NOT NULL,";
			$q .= " OF_IsSearchable tinyint(1) NOT NULL default '1',";
			$q .= " OF_Charset VARCHAR(64) NOT NULL,";
			$q .= " OF_WebUserID BIGINT NOT NULL,";
			$q .= " OF_Language VARCHAR(5) default 'NULL',";
			// Letzter Eintrag darf nicht mit einem Leerzeichen enden, letztes Zeichen mu? ein , sein!!!

			$indexe = ', KEY (OF_WebUserID), KEY `published` (`OF_ID`,`OF_Published`,`OF_IsSearchable`),KEY (`OF_IsSearchable`)';

			if(isset($this->elements["neuefelder"]["dat"])){

				$neu = explode(",",$this->elements["neuefelder"]["dat"]);
				for($i=0;$i <= sizeof($neu) ; $i++){
					if(!empty($neu[$i])){
						$name = $this->getElement($neu[$i]."dtype","dat")."_".$this->getElement($neu[$i],"dat");
						$q .= " ". $name. " ";
						$arrt[$name]["default"] = isset($this->elements[$neu[$i]."default"]["dat"]) ? $this->elements[$neu[$i]."default"]["dat"] : "";
						$arrt[$name]["defaultThumb"] = isset($this->elements[$neu[$i]."defaultThumb"]["dat"]) ? $this->elements[$neu[$i]."defaultThumb"]["dat"] : "";
						$arrt[$name]["defaultdir"] = isset($this->elements[$neu[$i]."defaultdir"]["dat"]) ? $this->elements[$neu[$i]."defaultdir"]["dat"] : "";
						$arrt[$name]["rootdir"] = isset($this->elements[$neu[$i]."rootdir"]["dat"]) ? $this->elements[$neu[$i]."rootdir"]["dat"] : "";
						$arrt[$name]["autobr"] = isset($this->elements[$neu[$i]."autobr"]["dat"]) ? $this->elements[$neu[$i]."autobr"]["dat"] : "";
						$arrt[$name]["dhtmledit"] = isset($this->elements[$neu[$i]."dhtmledit"]["dat"]) ? $this->elements[$neu[$i]."dhtmledit"]["dat"] : "";
						$arrt[$name]["commands"] = isset($this->elements[$neu[$i]."commands"]["dat"]) ? $this->elements[$neu[$i]."commands"]["dat"] : "";
						$arrt[$name]["height"] = isset($this->elements[$neu[$i]."height"]["dat"]) ? $this->elements[$neu[$i]."height"]["dat"] : "";
						$arrt[$name]["width"] = isset($this->elements[$neu[$i]."width"]["dat"]) ? $this->elements[$neu[$i]."width"]["dat"] : "";
						$arrt[$name]["class"] = isset($this->elements[$neu[$i]."class"]["dat"]) ? $this->elements[$neu[$i]."class"]["dat"] : "";
						$arrt[$name]["max"] = isset($this->elements[$neu[$i]."max"]["dat"]) ? $this->elements[$neu[$i]."max"]["dat"] : "";
						$arrt[$name]["cssClasses"] = isset($this->elements[$neu[$i]."cssClasses"]["dat"]) ? $this->elements[$neu[$i]."cssClasses"]["dat"] : "";
						$arrt[$name]["xml"] = isset($this->elements[$neu[$i]."xml"]["dat"]) ? $this->elements[$neu[$i]."xml"]["dat"] : "";
						$arrt[$name]["removefirstparagraph"] = isset($this->elements[$neu[$i]."removefirstparagraph"]["dat"]) ? $this->elements[$neu[$i]."removefirstparagraph"]["dat"] : "";
						$arrt[$name]["showmenus"] = isset($this->elements[$neu[$i]."showmenus"]["dat"]) ? $this->elements[$neu[$i]."showmenus"]["dat"] : "off";
						$arrt[$name]["forbidhtml"] = isset($this->elements[$neu[$i]."forbidhtml"]["dat"]) ? $this->elements[$neu[$i]."forbidhtml"]["dat"] : "";
						$arrt[$name]["forbidphp"] = isset($this->elements[$neu[$i]."forbidphp"]["dat"]) ? $this->elements[$neu[$i]."forbidphp"]["dat"] : "";
						$arrt[$name]["inlineedit"] = isset($this->elements[$neu[$i]."inlineedit"]["dat"]) ? $this->elements[$neu[$i]."inlineedit"]["dat"] : "";
						$arrt[$name]["users"] = isset($this->elements[$neu[$i]."users"]["dat"]) ? $this->elements[$neu[$i]."users"]["dat"] : "";
						$arrt[$name]["required"] = isset($this->elements[$neu[$i]."required"]["dat"]) ? $this->elements[$neu[$i]."required"]["dat"] : "";
						$arrt[$name]["editdescription"] = isset($this->elements[$neu[$i]."editdescription"]["dat"]) ? $this->elements[$neu[$i]."editdescription"]["dat"] : "";
						$arrt[$name]["int"] = isset($this->elements[$neu[$i]."int"]["dat"]) ? $this->elements[$neu[$i]."int"]["dat"] : "";
						$arrt[$name]["intID"] = isset($this->elements[$neu[$i]."intID"]["dat"]) ? $this->elements[$neu[$i]."intID"]["dat"] : "";
						$arrt[$name]["intPath"] = isset($this->elements[$neu[$i]."intPath"]["dat"]) ? $this->elements[$neu[$i]."intPath"]["dat"] : "";
						$arrt[$name]["hreftype"] = isset($this->elements[$neu[$i]."hreftype"]["dat"]) ? $this->elements[$neu[$i]."hreftype"]["dat"] : "";
						$arrt[$name]["hrefdirectory"] = isset($this->elements[$neu[$i]."hrefdirectory"]["dat"]) ? $this->elements[$neu[$i]."hrefdirectory"]["dat"] : "false";
						$arrt[$name]["hreffile"] = isset($this->elements[$neu[$i]."hreffile"]["dat"]) ? $this->elements[$neu[$i]."hreffile"]["dat"] : "true";
						$arrt[$name]["uniqueID"] = md5(uniqid(rand(),1));

						if($this->isVariantField($neu[$i]) && isset($this->elements[$neu[$i]."variant"]["dat"]) && $this->elements[$neu[$i]."variant"]["dat"]==1) $arrt[$name]["variant"] = $this->elements[$neu[$i]."variant"]["dat"];
						else if(isset($this->elements[$neu[$i]."variant"])) unset($this->elements[$neu[$i]."variant"]);

						if((! isset($arrt[$name]["meta"]) )  || (!is_array($arrt[$name]["meta"]))){
				    		$arrt[$name]["meta"] = array();
				    	}

						   //  First time a field is added
						for($f=0; $f <= ( isset($this->elements[$neu[$i]."count"]["dat"]) ? $this->elements[$neu[$i]."count"]["dat"] : 0 ); $f++){
						    $_val = (isset($this->elements[$neu[$i]."defaultvalue".$f]["dat"]) && $this->elements[$neu[$i]."defaultvalue".$f]["dat"] != $neu[$i]."defaultvalue".$f) ? $this->elements[$neu[$i]."defaultvalue".$f]["dat"] : "";
							if (substr($name,0,12) == "multiobject_") {
								array_push($arrt[$name]["meta"],$_val);
							} else {
								if(isset($this->elements[$neu[$i]."defaultkey".$f]["dat"])){
									$arrt[$name]["meta"][$this->elements[$neu[$i]."defaultkey".$f]["dat"]] = $_val;
								}
							}
						}
						$q .= $this->switchtypes($neu[$i]);
						$q .= ",";
						//add index for complex queries
						if($this->getElement($neu[$i]."dtype","dat")=='object'){
							$indexe .= ', KEY ('.$name.')';
						}
					}
				}
			}
			$q = rtrim($q, ',');

			$arrt["WE_CSS_FOR_CLASS"] = $this->CSS;
			$this->DefaultValues=serialize($arrt);

			$this->DefaultTitle = isset($this->elements["title"]["dat"]) ? $this->getElement($this->elements["title"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["title"]["dat"],"dat") : "_";
			$this->DefaultDesc = isset($this->elements["desc"]["dat"]) ? $this->getElement($this->elements["desc"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["desc"]["dat"],"dat") : "_";
			$this->DefaultKeywords = isset($this->elements["keywords"]["dat"]) ? $this->getElement($this->elements["keywords"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["keywords"]["dat"],"dat") : "_";


			$this->DefaultUrlfield0 = isset($this->elements["urlfield0"]["dat"]) ? $this->getElement($this->elements["urlfield0"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["urlfield0"]["dat"],"dat") : "_";
			$this->DefaultUrlfield1 = isset($this->elements["urlfield1"]["dat"]) ? $this->getElement($this->elements["urlfield1"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["urlfield1"]["dat"],"dat") : "_";
			$this->DefaultUrlfield2 = isset($this->elements["urlfield2"]["dat"]) ? $this->getElement($this->elements["urlfield2"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["urlfield2"]["dat"],"dat") : "_";
			$this->DefaultUrlfield3 = isset($this->elements["urlfield3"]["dat"]) ? $this->getElement($this->elements["urlfield3"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["urlfield3"]["dat"],"dat") : "_";
			$this->DefaultTriggerID = isset($this->elements["triggerid"]["dat"]) ? $this->getElement($this->elements["triggerid"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["triggerid"]["dat"],"dat") : "0";

			$we_sort = $this->getElement("we_sort");

			$this->strOrder = implode(",",$we_sort);

			$this->DefaultCategory = $this->Category;
			$this->i_savePersistentSlotsToDB();

			$this->ID = (f("SELECT MAX(LAST_INSERT_ID()) as LastID FROM ".escape_sql_query($this->Table),"LastID",$this->DB_WE));
			$ctable = OBJECT_X_TABLE.($this->ID);

			// Charset and Collation
			$charset_collation = "";
			if (defined("DB_CHARSET") && DB_CHARSET != "" && defined("DB_COLLATION") && DB_COLLATION != "") {
				$Charset = DB_CHARSET;
				$Collation = DB_COLLATION;
				$charset_collation = " CHARACTER SET " . $Charset . " COLLATE " . $Collation;

			}

			$this->DB_WE->query("DROP TABLE IF EXISTS $ctable");
			$this->DB_WE->query("CREATE TABLE $ctable ($q, PRIMARY KEY (ID)$indexe)$charset_collation");

			//dummy eintrag schreiben
			$this->DB_WE->query("INSERT INTO $ctable (OF_ID) VALUES (0)");


			// folder in object schreiben
			if(!($this->OldPath && ($this->OldPath != $this->Path))){
				$fold = new we_class_folder();
				$fold -> initByPath($this->getPath(),OBJECT_FILES_TABLE,1,0,1);
			}

		}else{

			$ctable = OBJECT_X_TABLE.$this->ID;
			$tableInfo = $this->DB_WE->metadata($ctable);
			$q = "";

			foreach($tableInfo as $info){

				if(preg_match('/(.+?)_(.*)/',$info["name"],$regs)){

					if($regs[1]!="OF" && $regs[1]!="variant"){
						$fieldsToDelete = isset($this->elements["felderloeschen"]["dat"]) ? explode(",",$this->elements["felderloeschen"]["dat"]) : array();
						if (!in_array($info["name"],$fieldsToDelete)) {

							$nam = $this->getElement($info["name"]."dtype","dat")."_".$this->getElement($info["name"],"dat");
							$q .= " CHANGE ".$info["name"]." ". $nam. " ";
							$q .= $this->switchtypes($info["name"]);
							//change from object is indexed to unindexed
							if((strpos($info["name"],'object_')===0) && (strpos($nam, 'object_')!==0) && (strpos($info["flags"],'multiple_key')!==false)){
								$q.=', DROP KEY '.$info["name"].' ';
							}else if ((strpos($info["name"],'object_')!==0) && (strpos($nam, 'object_')===0) && (strpos($info["flags"],'multiple_key')===false)){
								$q.=', ADD INDEX ('.$info["name"].') ';
							}
							if((strpos($info["name"],'date_')===0)){
								
								if ($this->elements[$info["name"]."defaultThumb"]["dat"]){
									$arrt[$nam]["default"] ='';
								} else {
									$arrt[$nam]["default"] = $this->elements[$info["name"]."default"]["dat"];
								}
							} else {
								$arrt[$nam]["default"] = $this->elements[$info["name"]."default"]["dat"];
							}
							$arrt[$nam]["defaultThumb"] = $this->elements[$info["name"]."defaultThumb"]["dat"];
							$arrt[$nam]["autobr"] = $this->elements[$info["name"]."autobr"]["dat"];
							$arrt[$nam]["defaultdir"] = $this->elements[$info["name"]."defaultdir"]["dat"];
							$arrt[$nam]["rootdir"] = $this->elements[$info["name"]."rootdir"]["dat"];
							$arrt[$nam]["dhtmledit"] = $this->elements[$info["name"]."dhtmledit"]["dat"];
							$arrt[$nam]["showmenus"] = $this->elements[$info["name"]."showmenus"]["dat"];
							$arrt[$nam]["commands"] = $this->elements[$info["name"]."commands"]["dat"];
							$arrt[$nam]["height"] = $this->elements[$info["name"]."height"]["dat"];
							$arrt[$nam]["width"] = $this->elements[$info["name"]."width"]["dat"];
							$arrt[$nam]["class"] = $this->elements[$info["name"]."class"]["dat"];
							$arrt[$nam]["max"] = $this->elements[$info["name"]."max"]["dat"];
							$arrt[$nam]["cssClasses"] = $this->elements[$info["name"]."cssClasses"]["dat"];
							$arrt[$nam]["xml"] = $this->elements[$info["name"]."xml"]["dat"];
							$arrt[$nam]["removefirstparagraph"] = $this->elements[$info["name"]."removefirstparagraph"]["dat"];
							$arrt[$nam]["forbidhtml"] = $this->elements[$info["name"]."forbidhtml"]["dat"];
							$arrt[$nam]["forbidphp"] = $this->elements[$info["name"]."forbidphp"]["dat"];
							$arrt[$nam]["inlineedit"] = $this->elements[$info["name"]."inlineedit"]["dat"];
							$arrt[$nam]["users"] = $this->elements[$info["name"]."users"]["dat"];
							$arrt[$nam]["required"] = $this->elements[$info["name"]."required"]["dat"];
							$arrt[$nam]["editdescription"] = $this->elements[$info["name"]."editdescription"]["dat"];
							$arrt[$nam]["int"] = $this->elements[$info["name"]."int"]["dat"];
							$arrt[$nam]["intID"] = $this->elements[$info["name"]."intID"]["dat"];
							$arrt[$nam]["intPath"] = $this->elements[$info["name"]."intPath"]["dat"];
							$arrt[$nam]["hreftype"] = $this->elements[$info["name"]."hreftype"]["dat"];
							$arrt[$nam]["hrefdirectory"] = $this->elements[$info["name"]."hrefdirectory"]["dat"];
							$arrt[$nam]["hreffile"] = $this->elements[$info["name"]."hreffile"]["dat"];
							$arrt[$nam]["uniqueID"] = $this->SerializedArray[$info["name"]]["uniqueID"] ? $this->SerializedArray[$info["name"]]["uniqueID"] : md5(uniqid(rand(),1));

							if($this->isVariantField($info["name"]) && isset($this->elements[$info["name"]."variant"]["dat"]) && $this->elements[$info["name"]."variant"]["dat"]==1){
								$arrt[$nam]["variant"] = $this->elements[$info["name"]."variant"]["dat"];
							}else if(isset($this->elements[$info["name"]."variant"])){
								unset($this->elements[$info["name"]."variant"]);
							}


							for($f=0; $f <= $this->elements[$info["name"]."count"]["dat"]; ++$f){

								if(isset($this->elements[$info["name"]."defaultkey".$f])){
									if((!isset($arrt[$nam]["meta"])) || (!is_array($arrt[$nam]["meta"]))){
										$arrt[$nam]["meta"] =  array();
									}
									$_val = (isset($this->elements[$info["name"]."defaultvalue".$f]) && $this->elements[$info["name"]."defaultvalue".$f] != $info["name"]."defaultvalue".$f ) ?  $this->elements[$info["name"]."defaultvalue".$f]["dat"] : "";
									if (substr($nam,0,12) == "multiobject_") {
										array_push($arrt[$nam]["meta"],$_val);
									} else {
										if($this->elements[$info["name"]."defaultkey".$f]["dat"] == "") {
											$arrt[$nam]["meta"][$this->elements[$info["name"]."defaultkey".$f]["dat"]] = $_val;
										} else {
											$arrt[$nam]["meta"][$this->elements[$info["name"]."defaultkey".$f]["dat"]] = $_val;
										}
									}
								}
							}

						}else{
							$q .= " DROP ".$info["name"]." ";
						}
						$q .= ",";

					}
				}
			}

			$neu = explode(",", (isset($this->elements["neuefelder"]["dat"]) ? $this->elements["neuefelder"]["dat"] : "") );

			for($i=0;$i <= sizeof($neu) ; $i++){
				if(isset($neu[$i]) && $neu[$i]!=""){
					$nam = $this->getElement($neu[$i]."dtype","dat")."_".$this->getElement($neu[$i],"dat");
					$q .= " ADD ". $nam. " ";
					$arrt[$nam]["default"] = isset($this->elements[$neu[$i]."default"]["dat"]) ? $this->elements[$neu[$i]."default"]["dat"] : "";
					$arrt[$nam]["defaultThumb"] = isset($this->elements[$neu[$i]."defaultThumb"]["dat"]) ? $this->elements[$neu[$i]."defaultThumb"]["dat"] : "";
					$arrt[$nam]["defaultdir"] = isset($this->elements[$neu[$i]."defaultdir"]["dat"]) ? $this->elements[$neu[$i]."defaultdir"]["dat"] : "";
					$arrt[$nam]["rootdir"] = isset($this->elements[$neu[$i]."rootdir"]["dat"]) ? $this->elements[$neu[$i]."rootdir"]["dat"] : "";
					$arrt[$nam]["autobr"] = isset($this->elements[$neu[$i]."autobr"]["dat"]) ? $this->elements[$neu[$i]."autobr"]["dat"] : "";
					$arrt[$nam]["dhtmledit"] = isset($this->elements[$neu[$i]."dhtmledit"]["dat"]) ? $this->elements[$neu[$i]."dhtmledit"]["dat"] : "";
					$arrt[$nam]["showmenues"] = isset($this->elements[$neu[$i]."showmenues"]["dat"]) ? $this->elements[$neu[$i]."showmenues"]["dat"] : "";
					$arrt[$nam]["commands"] = isset($this->elements[$neu[$i]."commands"]["dat"]) ? $this->elements[$neu[$i]."commands"]["dat"] : "";
					$arrt[$nam]["height"] = isset($this->elements[$neu[$i]."height"]["dat"]) ? $this->elements[$neu[$i]."height"]["dat"] : 200;
					$arrt[$nam]["width"] = isset($this->elements[$neu[$i]."width"]["dat"]) ? $this->elements[$neu[$i]."width"]["dat"] : 618;
					$arrt[$nam]["class"] = isset($this->elements[$neu[$i]."class"]["dat"]) ? $this->elements[$neu[$i]."class"]["dat"] : "";
					$arrt[$nam]["max"] = isset($this->elements[$neu[$i]."max"]["dat"]) ? $this->elements[$neu[$i]."max"]["dat"] : "";
					$arrt[$nam]["cssClasses"] = isset($this->elements[$neu[$i]."cssClasses"]["dat"]) ? $this->elements[$neu[$i]."cssClasses"]["dat"] : "";
					$arrt[$nam]["xml"] = isset($this->elements[$neu[$i]."xml"]["dat"]) ? $this->elements[$neu[$i]."xml"]["dat"] : "";
					$arrt[$nam]["removefirstparagraph"] = isset($this->elements[$neu[$i]."removefirstparagraph"]["dat"]) ? $this->elements[$neu[$i]."removefirstparagraph"]["dat"] : "";
					$arrt[$nam]["forbidhtml"] = isset($this->elements[$neu[$i]."forbidhtml"]["dat"]) ? $this->elements[$neu[$i]."forbidhtml"]["dat"] : "";
					$arrt[$nam]["forbidphp"] = isset($this->elements[$neu[$i]."forbidphp"]["dat"]) ? $this->elements[$neu[$i]."forbidphp"]["dat"] : "";
					$arrt[$nam]["inlineedit"] = isset($this->elements[$neu[$i]."inlineedit"]["dat"]) ? $this->elements[$neu[$i]."inlineedit"]["dat"] : "";
					$arrt[$nam]["users"] = isset($this->elements[$neu[$i]."users"]["dat"]) ? $this->elements[$neu[$i]."users"]["dat"] : "";
					$arrt[$nam]["required"] = isset($this->elements[$neu[$i]."required"]["dat"]) ? $this->elements[$neu[$i]."required"]["dat"] : "";
					$arrt[$nam]["editdescription"] = isset($this->elements[$neu[$i]."editdescription"]["dat"]) ? $this->elements[$neu[$i]."editdescription"]["dat"] : "";
					$arrt[$nam]["int"] = isset($this->elements[$neu[$i]."int"]["dat"]) ? $this->elements[$neu[$i]."int"]["dat"] : "";
					$arrt[$nam]["intID"] = isset($this->elements[$neu[$i]."intID"]["dat"]) ? $this->elements[$neu[$i]."intID"]["dat"] : "";
					$arrt[$nam]["intPath"] = isset($this->elements[$neu[$i]."intPath"]["dat"]) ? $this->elements[$neu[$i]."intPath"]["dat"] : "";
					$arrt[$nam]["hreftype"] = isset($this->elements[$neu[$i]."hreftype"]["dat"]) ? $this->elements[$neu[$i]."hreftype"]["dat"] : "";
					$arrt[$nam]["hrefdirectory"] = isset($this->elements[$neu[$i]."hrefdirectory"]["dat"]) ? $this->elements[$neu[$i]."hrefdirectory"]["dat"] : "false";
					$arrt[$nam]["hreffile"] = isset($this->elements[$neu[$i]."hreffile"]["dat"]) ? $this->elements[$neu[$i]."hreffile"]["dat"] : "true";
					$arrt[$nam]["uniqueID"] = md5(uniqid(rand(),1));

//					$arrt[$nam]["variant"] = (isset($this->elements[$neu[$i]."variant"]["dat"]) && $this->elements[$neu[$i]."variant"]["dat"]==1) ? $this->elements[$neu[$i]."variant"]["dat"] : "";
					if($this->isVariantField($neu[$i]) && isset($this->elements[$neu[$i]."variant"]["dat"]) && $this->elements[$neu[$i]."variant"]["dat"]==1){
						$arrt[$nam]["variant"] = $this->elements[$neu[$i]."variant"]["dat"];
					}else if(isset($this->elements[$neu[$i]."variant"])){
						unset($this->elements[$neu[$i]."variant"]);
					}

					for($f=0; $f <= (isset($this->elements[$neu[$i]."count"]["dat"]) ? $this->elements[$neu[$i]."count"]["dat"] : 0)  ; $f++){
					    $_val = isset($this->elements[$neu[$i]."defaultvalue".$f]["dat"]) ? $this->elements[$neu[$i]."defaultvalue".$f]["dat"] : "";
						if((!isset($arrt[$nam]["meta"])) || (!is_array($arrt[$nam]["meta"]))){
							$arrt[$nam]["meta"] =  array();
						}
						if (substr($nam,0,12) == "multiobject_") {
							array_push($arrt[$nam]["meta"],$_val);
						} else {
							if(isset($this->elements[$neu[$i]."defaultkey".$f]["dat"])){
				            	$arrt[$nam]["meta"][$this->elements[$neu[$i]."defaultkey".$f]["dat"]] = $_val;
				        	} else {
                     	$arrt[$nam]["meta"][$nam."defaultkey".$f] = $_val;
                  }
						}
					}

					$q .= $this->switchtypes($neu[$i]);
					//add index for complex queries
					if($this->getElement($neu[$i]."dtype","dat")=='object'){
						$q .= ', ADD INDEX ('.$nam.')';
					}
					$q .= ",";
				}
			}
			$q = rtrim($q, ',');

			$this->DefaultCategory = $this->Category;

			$this->DefaultTitle=$this->getElement($this->elements["title"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["title"]["dat"],"dat");//$this->elements["title"]["dat"];
			$this->DefaultDesc=$this->getElement($this->elements["desc"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["desc"]["dat"],"dat");//$this->elements["desc"]["dat"];
			$this->DefaultKeywords=$this->getElement($this->elements["keywords"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["keywords"]["dat"],"dat");//$this->elements["desc"]["dat"];


			$this->DefaultUrlfield0 = isset($this->elements["urlfield0"]["dat"]) ? $this->getElement($this->elements["urlfield0"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["urlfield0"]["dat"],"dat") : "_";
			$this->DefaultUrlfield1 = isset($this->elements["urlfield1"]["dat"]) ? $this->getElement($this->elements["urlfield1"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["urlfield1"]["dat"],"dat") : "_";
			$this->DefaultUrlfield2 = isset($this->elements["urlfield2"]["dat"]) ? $this->getElement($this->elements["urlfield2"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["urlfield2"]["dat"],"dat") : "_";
			$this->DefaultUrlfield3 = isset($this->elements["urlfield3"]["dat"]) ? $this->getElement($this->elements["urlfield3"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["urlfield3"]["dat"],"dat") : "_";
			//$this->DefaultTriggerID = isset($this->elements["triggerid"]["dat"]) ? $this->getElement($this->elements["triggerid"]["dat"]."dtype","dat")."_".$this->getElement($this->elements["urlfield3"]["dat"],"dat") : "0";

			$arrt["WE_CSS_FOR_CLASS"] = $this->CSS;

			$this->DefaultValues=serialize($arrt);

			if(defined('SHOP_TABLE')) {
				$variant_field = 'variant_' . WE_SHOP_VARIANTS_ELEMENT_NAME;
				$exists = false;
				$this->DB_WE->query("SHOW COLUMNS FROM $ctable LIKE '$variant_field';");
				if($this->DB_WE->next_record()) {
					$exists = true;
				}

				if($this->hasVariantFields()>0){
					 if(!$exists){
					 	$this->DB_WE->query("ALTER TABLE $ctable ADD $variant_field TEXT NOT NULL;");
					 }
				} else {
					if($exists){
						$this->DB_WE->query("ALTER TABLE $ctable  DROP $variant_field;");
					}
				}
			}

			$qa = explode(',',$q);
			foreach ($qa as $v) {
				if ($v !='') {$this->DB_WE->query("ALTER TABLE $ctable $v");}
			}

			$we_sort = $this->getElement("we_sort");

			$this->strOrder = implode(",",$we_sort);
			$this->i_savePersistentSlotsToDB();
		}

		////// resave the line O to O.....
    	$this->DB_WE->query("DELETE FROM $ctable where OF_ID=0 OR ID=0");
    	$this->DB_WE->query("INSERT INTO $ctable (OF_ID) VALUES(0)");
		////// resave the line O to O.....

		unset($this->elements);
		$this->i_getContentData();
		//$this->initByID($this->ID,$this->Table);

	}

	function switchtypes($name) {
		switch ($this->getElement($name . "dtype", "dat")) {
			case "meta":
				return " VARCHAR(" . (($this->getElement($name . "length", "dat") > 0 && ($this->getElement($name . "length", "dat") < 256)) ? $this->getElement($name . "length", "dat") : "255") . ") NOT NULL ";
			case "date":
				return " INT(11) NOT NULL ";
			case "input":
				return " VARCHAR(" . (($this->getElement($name . "length", "dat") > 0 && ($this->getElement($name . "length", "dat") < 256)) ? $this->getElement($name . "length", "dat") : "255") . ") NOT NULL ";
			case "country":
			case "language":
				return " VARCHAR(2) NOT NULL ";
			case "link":
			case "href":
				return " TEXT NOT NULL ";
			case "text":
				return " LONGTEXT NOT NULL ";
				break;
			case "img":
			case "flashmovie":
			case "quicktime":
			case "binary":
				return " INT(11) DEFAULT '0' NOT NULL ";
			case "checkbox":
				return " INT(1) DEFAULT '" . ($this->getElement($name . "default", "dat") == "1" ? "1" : "0") . "' NOT NULL ";
			case "int":
				return " INT(" . (($this->getElement($name . "length", "dat") > 0 && ($this->getElement($name . "length", "dat") < 256)) ? $this->getElement($name . "length", "dat") : "11") . ") DEFAULT NULL ";
			case "float":
				return " DOUBLE DEFAULT NULL ";
			case "object":
				return " BIGINT(20) DEFAULT '0' NOT NULL ";
			case "multiobject":
				return " TEXT NOT NULL ";
			case 'shopVat':
				return ' TEXT NOT NULL';
		}
		return '';
	}

	function getPath(){
		$ParentPath = $this->getParentPath();
		$ParentPath .= ($ParentPath != "/") ? "/" : "";
		return $ParentPath.$this->Text;
	}

	function ModifyPathInformation($parentID){
		$this->setParentID($parentID);
		$this->Path = $this->getPath();
		$this->wasUpdate = 1;
		$this->i_savePersistentSlotsToDB("Text,Path,ParentID");
	}

	//##################################################################### FUNCTIONS FOR GETTING DATA ######################################################################


	function setSort(){
		if(!$this->issetElement("we_sort")){

			$ctable = OBJECT_X_TABLE . $this->ID;
			$tableInfo = $this->DB_WE->metadata($ctable);
			$fields = array();
			foreach($tableInfo as $info){
				if(preg_match('/(.+?)_(.*)/',$info["name"],$regs)){
					if($regs[1]!="OF" && $regs[1]!="variant"){
						$fields[] = array("name"=>$regs[2],"type"=>$regs[1],"length"=>$info["len"]);
					}
				}
			}

			$sort = array();
			if(strlen($this->strOrder)>0){
				$t = explode(",",$this->strOrder);
				if(count($t) != count($fields)) {
					$t = array();
					for($y=0;$y<count($fields);$y++) {
						$t[$y] = $y;
					}
				}
				foreach($t as $k => $v) {
					if($v<0) {
						$v = 0;
					}
					$sort[uniqid("")] = $v;
				}
			}else{
				for($y = 0; $y < count($fields); $y++){
					$sort[uniqid("")] = $y;
				}

			}
			$this->setElement("we_sort",$sort);
		}
	}


	//##################################################################### EDITOR FUNCTION ######################################################################

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */
	function editor()	{
		global $we_responseText,$we_JavaScript, $we_responseTextType;
		if($_REQUEST["we_cmd"][0] == "save_document"){
			$we_responseText = $l_we_class["response_save_ok"];
			$we_JavaScript = "";
			$this->save();
			$we_responseText = sprintf($we_responseText,$this->Path);
			$we_responseTextType = WE_MESSAGE_NOTICE;
			return "we_templates/we_editor_save.inc.php";
		}
		switch($this->EditPageNr){
			case WE_EDITPAGE_PROPERTIES:
			case WE_EDITPAGE_WORKSPACE:
			return "we_templates/we_editor_properties.inc.php";
			case WE_EDITPAGE_INFO:
			return "we_modules/object/we_editor_info_object.inc.php";
			case WE_EDITPAGE_CONTENT:
			return "we_modules/object/we_editor_contentobject.inc.php";
			default:
			$this->EditPageNr = WE_EDITPAGE_PROPERTIES;
			$_SESSION["EditPageNr"] = WE_EDITPAGE_PROPERTIES;
			return "we_templates/we_editor_properties.inc.php";
		}
	}

	function getSortIndex($nr){
		$sort = $this->getElement("we_sort");

		$t = array();
		$i = 0;
		foreach($sort as $k => $v) {
			if($i == $nr) {
				return $k;
			}
			$i++;
		}
	}

	function getSortIndexByValue($value){
		$sort = $this->getElement("we_sort");

		$t = array();
		$i = 0;
		foreach($sort as $k => $v) {
			if($v == $value) {
				return $k;
			}
			$i++;
		}
	}


	function downEntryAtClass($identifier){
		$sort = $this->getElement("we_sort");

		$pos = $sort[$identifier];

		$t = array();
		$i = 0;
		$position = sizeof($sort);
		foreach($sort as $ident => $identpos) {
			if($ident == $identifier && $i < sizeof($sort)-1) {
				$position = $i;
			}
			if($i == $position+1) {
				$t[$ident] = $identpos;
				$t[$identifier] = $sort[$identifier];
			} elseif($i != $position) {
				$t[$ident] = $sort[$ident];
			}
			$i++;
		}
		$sort = $t;

		$this->setElement("we_sort",$sort);
	}

	function upEntryAtClass($identifier){
		$sort = $this->getElement("we_sort");

		$pos = $sort[$identifier];

		$reversed = array_reverse($sort, true);

		$t = array();
		$i = 0;
		$position = sizeof($reversed);
		foreach($reversed as $ident => $identpos) {
			if($ident == $identifier && $i < sizeof($reversed)-1) {
				$position = $i;
			}
			if($i == $position+1) {
				$t[$ident] = $identpos;
				$t[$identifier] = $sort[$identifier];
			} elseif($i != $position) {
				$t[$ident] = $reversed[$ident];
			}
			$i++;
		}
		$sort = array_reverse($t, true);

		$this->setElement("we_sort",$sort);
	}


	function addEntryToClass($identifier, $after = false){
		$sort = $this->getElement("we_sort");
		$uid = time();

		$gesamt = $this->getElement("Sortgesamt");
		if(empty($sort)){
			$gesamt = 0;
		}else{
			$gesamt++;
		}

		$this->elements["Sortgesamt"]["dat"] = $gesamt;
		$this->elements[$uid]["dat"] = "";
		$this->elements[$uid."length"]["dat"] = "";
		$this->elements[$uid."dtype"]["dat"] = "";
		$this->elements[$uid."width"]["dat"] = 618;
		$this->elements[$uid."height"]["dat"] = 200;
		$this->elements[$uid."class"]["dat"] = "";
		$this->elements[$uid."max"]["dat"] = "";
		$this->elements["wholename".$identifier]["dat"] = $uid;

		if(!isset($this->elements["neuefelder"]["dat"])){
		    $this->elements["neuefelder"]["dat"] = "";
		}

		$this->elements["neuefelder"]["dat"] .= ",".$uid;

		if(isset($after) && in_array($after, array_keys($sort))) {
			$pos = $sort[$after];
			$t = array();
			foreach($sort as $ident => $identpos) {
				if($pos > $identpos) {
					$t[$ident] = $identpos;
				} elseif($pos == $identpos) {
					$t[$ident] = $identpos;
					$t[$identifier] = sizeof($sort);
				} elseif($pos < $identpos) {
					$t[$ident] = $identpos;
				}
			}
			$sort = $t;
		} else {
			$sort[$identifier] = sizeof($sort);
		}
		$this->setElement("we_sort", $sort);
	}

	function removeEntryFromClass($identifier){

		$sort = $this->getElement("we_sort");
		$max = $this->getElement("Sortgesamt");

		$uid = $this->elements["wholename".$identifier]["dat"];

		if(stristr((isset($this->elements['neuefelder']['dat']) ? $this->elements['neuefelder']['dat'] : ''), ','.$uid)){
			$this->elements["neuefelder"]["dat"] = str_replace(",".$uid,"",$this->elements["neuefelder"]["dat"]);
		}else{
		    if(!isset($this->elements["felderloeschen"]["dat"])){
		        $this->elements["felderloeschen"]["dat"] = "";
		    }
			$this->elements["felderloeschen"]["dat"] .= ",".$uid;
		}

		unset($this->elements["wholename".$identifier]["dat"]);
		unset($this->elements[$uid]["dat"]);
		unset($this->elements[$uid."length"]["dat"]);
		unset($this->elements[$uid."dtype"]["dat"]);
		unset($this->elements[$uid."height"]["dat"]);
		unset($this->elements[$uid."width"]["dat"]);
		unset($this->elements[$uid."default"]["dat"]);
		unset($this->elements[$uid."class"]["dat"]);
		unset($this->elements[$uid."max"]["dat"]);


		### move elements ####
		$pos = $sort[$identifier];
		foreach($sort as $ident => $identpos) {
			if($identpos == $pos) {
				unset($sort[$ident]);
			} elseif($identpos > $pos) {
				$sort[$ident]--;
			}
		}
		$this->elements["Sortgesamt"]["dat"] = sizeof($sort);
		### end move elements ####

		$this->setElement("we_sort", (sizeof($sort)>0 ? $sort : array()) );
	}

	function downMetaAtClass($name,$i){
		unset($temp);
		$temp = $this->elements[$name."defaultkey".($i+1)]["dat"];
		$this->elements[$name."defaultkey".($i+1)]["dat"] = $this->elements[$name."defaultkey".($i)]["dat"];
		$this->elements[$name."defaultkey".($i)]["dat"] = $temp;
		unset($temp);
		$temp = $this->elements[$name."defaultvalue".($i+1)]["dat"];
		$this->elements[$name."defaultvalue".($i+1)]["dat"] = $this->elements[$name."defaultvalue".($i)]["dat"];
		$this->elements[$name."defaultvalue".($i)]["dat"] = $temp;
	}

	function upMetaAtClass($name,$i){
		unset($temp);
		$temp = $this->elements[$name."defaultkey".($i-1)]["dat"];
		$this->elements[$name."defaultkey".($i-1)]["dat"] = $this->elements[$name."defaultkey".($i)]["dat"];
		$this->elements[$name."defaultkey".($i)]["dat"] = $temp;
		unset($temp);
		$temp = $this->elements[$name."defaultvalue".($i-1)]["dat"];
		$this->elements[$name."defaultvalue".($i-1)]["dat"] = $this->elements[$name."defaultvalue".($i)]["dat"];
		$this->elements[$name."defaultvalue".($i)]["dat"] = $temp;
	}

	function addMetaToClass($name,$pos){

		// get from request
		$amount = isset($_REQUEST["amount_insert_meta_at_class_" . $name . $pos]) ? $_REQUEST["amount_insert_meta_at_class_" . $name . $pos] : 1;

		// set new amount
		$this->elements[$name."count"]["dat"] += $amount;

		// move elements - add new elements
		for($i=$this->elements[$name."count"]["dat"]; 0 <= $i; $i--){

			if ( ($pos + $amount) < $i  ) {// move existing fields
				$this->elements[$name."defaultkey".($i)]["dat"] = ($this->getElement($name."defaultkey".($i-$amount),"dat"));
				$this->elements[$name."defaultvalue".($i)]["dat"] = ($this->getElement($name."defaultvalue".($i-$amount),"dat"));
			} else if( $pos < $i && $i <= ($pos + $amount)  ) { // add new fields
				$this->elements[$name."defaultkey".$i]["dat"]="";
				$this->elements[$name."defaultvalue".$i]["dat"]="";
			}
		}
	}

	function removeMetaFromClass($name,$nr){

		### move elements ####

		for($i=0; $i < $this->elements[$name."count"]["dat"]; $i++){
			if($i >= $nr){
				$this->elements[$name."defaultkey".$i]["dat"] = ($this->getElement($name."defaultkey".($i+1),"dat"));
				$this->elements[$name."defaultvalue".$i]["dat"] = ($this->getElement($name."defaultvalue".($i+1),"dat"));
			}
		}
		$this->elements[$name."defaultkey".$i]["dat"]="";
		$this->elements[$name."defaultvalue".$i]["dat"]="";
		### end move elements ####

		$this->elements[$name."count"]["dat"]=($this->elements[$name."count"]["dat"]>0)?$this->elements[$name."count"]["dat"]-1:0;

	}

	function getFieldHTML($name,$identifier){
		global $we_transaction;

		$we_button = new we_button();

		$listlen = ($this->getElement("Sortgesamt")+1);
		//$name = str_replace("float", "f", str_replace("int", "i",$name));

		$type = ( $this->getElement($name."dtype","dat") !="" ) ? $this->getElement($name."dtype","dat") : "input";
		$content = '<tr>';
		$content .= '<td  width="100" class="weMultiIconBoxHeadline" valign="top" >' . $GLOBALS["l_we_class"]["name"].'</td>';
		$content .= '<td  width="170" class="defaultfont" valign="top">';

		if ($type == 'object') {


			$vals = array();
			$all = $this->DB_WE->table_names(OBJECT_X_TABLE . "%");
			if(!sizeof($all)){
				$all = $this->DB_WE->table_names(OBJECT_X_TABLE . "%");
			}
			$count=0;
			while($count < sizeof($all)){
				if($all[$count]["table_name"] != OBJECT_FILES_TABLE && $all[$count]["table_name"] != OBJECT_FILES_TABLE){
					if(preg_match('/^(.+)_(\d+)$/', $all[$count]["table_name"], $regs)){
						if($this->ID != $regs[2]) {
							$this->DB_WE->query("SELECT Path FROM " . OBJECT_TABLE . " WHERE ID = ".$regs[2]);
							$this->DB_WE->next_record();
							if ($this->DB_WE->f("Path") !== '') {
								$vals[$regs[2]]=$this->DB_WE->f("Path");
							}
						}
					}
				}
				$count++;
			}
			asort($vals);
			$content .= $this->htmlSelect("we_".$this->Name."_input[$name]",$vals,1,$this->getElement($name,"dat"),"",'onChange="if(this.form.elements[\''."we_".$this->Name."_input[".$name."default]".'\']){this.form.elements[\''."we_".$this->Name."_input[".$name."default]".'\'].value=\'\' };_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_entry_at_class\',\''.$GLOBALS['we_transaction'].'\',\''.$identifier.'\')"',"value",388);

		}else{

			$foo = $this->getElement($name,"dat");
			if ($type == 'shopVat') {
				$foo = WE_SHOP_VAT_FIELD_NAME;
				$content .= hidden("we_".$this->Name."_input[$name]", $foo);
				$content .= $this->htmlTextInput("tmp" . WE_SHOP_VAT_FIELD_NAME,40,$foo,52,' readonly="readonly" disabled="disabled"',"text",388);
			} else {
				$foo = $foo ? $foo : $GLOBALS["l_object"]["new_field"];
				$content .= $this->htmlTextInput("we_".$this->Name."_input[$name]",40,$foo,52,' oldValue="' . $foo . '" onBlur="we_checkObjFieldname(this);" onChange="_EditorFrame.setEditorIsHot(true);"',"text",388);
			}
		}


		$content .= '</td></tr>';

//		$content .= '<tr><td class="defaultfont">' . $GLOBALS["l_global"]["description"] . '</td><td>' . $this->htmlTextInput("we_".$this->Name."_input[".$name."editdescription]", 40, $this->getElement($name."editdescription"), 255, 'onChange="_EditorFrame.setEditorIsHot(true);"',"text",388) . '</td></tr>';
		$content .= '<tr><td class="weMultiIconBoxHeadlineThin" valign="top">' . $GLOBALS["l_global"]["description"] . '</td><td>' . $this->htmlTextArea("we_".$this->Name."_input[".$name."editdescription]", 3, 40, $this->getElement($name."editdescription"), 'onChange="_EditorFrame.setEditorIsHot(true)"; style="width: 388px;"') . '</td></tr>';

		//type
		$content .= '<tr>';
		$content .= '<td  width="100" class="weMultiIconBoxHeadlineThin"  valign="top">'.$GLOBALS["l_object"]["type"].'</td>';
		$content .= '<td width="170" class="defaultfont"  valign="top">';
		$val["input"] = $GLOBALS["l_object"]["input_field"];
		$val["text"] = $GLOBALS["l_object"]["textarea_field"];
		$val["date"] = $GLOBALS["l_object"]["date_field"];
		$val["img"] = $GLOBALS["l_object"]["img_field"];
		$val["checkbox"] = $GLOBALS["l_object"]["checkbox_field"];
		$val["int"] = $GLOBALS["l_object"]["int_field"];
		$val["float"] = $GLOBALS["l_object"]["float_field"];
		$val["meta"] = $GLOBALS["l_object"]["meta_field"];
		$val["link"] = $GLOBALS["l_object"]["link_field"];
		$val["href"] = $GLOBALS["l_object"]["href_field"];
		$val["binary"] = $GLOBALS["l_object"]["binary_field"];
		$val["flashmovie"] = $GLOBALS["l_object"]["flashmovie_field"];
		$val["quicktime"] = $GLOBALS["l_object"]["quicktime_field"];
		$val["country"] = $GLOBALS["l_object"]["country_field"];
		$val["language"] = $GLOBALS["l_object"]["language_field"];
		$val["object"] = $GLOBALS["l_object"]["objectFile_field"];
		$val["multiobject"] = $GLOBALS["l_object"]["multiObjectFile_field"];
		if (defined('SHOP_TABLE')) {
			$val["shopVat"] = $GLOBALS["l_object"]["shopVat_field"];
		}
		$content .= $this->htmlSelect("we_".$this->Name."_input[".$name."dtype]",$val,1,$type,"",'onChange="if(this.form.elements[\''."we_".$this->Name."_input[".$name."default]".'\']){this.form.elements[\''."we_".$this->Name."_input[".$name."default]".'\'].value=\'\' };_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_entry_at_class\',\''.$GLOBALS['we_transaction'].'\',\''.$identifier.'\'); "',"value",388);
		$content .= '</td></tr>';

		if($type != 'shopVat' && $type!="float" && $type!="text" && $type!="country" && $type!="language" && $type!="img" && $type!="binary"  && $type!="flashmovie" && $type!="quicktime" && $type!="date" && $type!="meta" && $type!="object" && $type!="link" && $type!="href" && $type!="checkbox" && $type!="multiobject"){
			// Length
			$maxLengthVal = $type == 'int' ? 10 : 255;
			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin"  valign="top">'.$GLOBALS["l_object"]["length"].'</td>';
			$content .= '<td width="170" class="defaultfont">';
			$content .= $this->htmlTextInput("we_".$this->Name."_input[".$name."length]",10,($this->getElement($name."length","dat")>0  && ($this->getElement($name."length","dat") < ($maxLengthVal+1))?$this->getElement($name."length","dat"):$maxLengthVal),($type == 'int' ? 2 : 3),'onChange="_EditorFrame.setEditorIsHot(true);" weType="weObject_'.$type.'_length"',"text",388);
			$content .= '</td></tr>';
		}

		switch($type){
		case 'multiobject':
			$content .= '<tr>';
			$content .= '<td  width="100" class="weMultiIconBoxHeadlineThin" valign="top" >'.$GLOBALS['l_contentTypes']['object'].'</td>';
			$content .= '<td  width="170" class="defaultfont"  valign="top">';
			$vals = array();
			$all = $this->DB_WE->table_names(OBJECT_X_TABLE . "%");
			if(!sizeof($all)){
				$all = $this->DB_WE->table_names(OBJECT_X_TABLE . "%");
			}
			$count=0;
			while($count < sizeof($all)){
				if($all[$count]["table_name"] != OBJECT_FILES_TABLE && $all[$count]["table_name"] != OBJECT_FILES_TABLE){
					if(preg_match('/^(.+)_(\d+)$/', $all[$count]["table_name"], $regs)){
						$this->DB_WE->query("SELECT Path FROM " . OBJECT_TABLE . " WHERE ID = ".$regs[2]);
						$this->DB_WE->next_record();
						if ($this->DB_WE->f("Path") !== '') {
							$vals[$regs[2]]=$this->DB_WE->f("Path");
						}
					}
				}
				$count++;
			}
			asort($vals);
			if(!isset($this->elements[$name."class"]["dat"]) || $this->elements[$name."class"]["dat"] == "") {
				$this->setElement($name."class", array_shift(array_flip($vals)));
			}
			$content .= $this->htmlSelect("we_".$this->Name."_multiobject[".$name."class]",$vals,1,$this->getElement($name.'class',"dat"),"",'onChange="if(this.form.elements[\''."we_".$this->Name."_input[".$name."default]".'\']){this.form.elements[\''."we_".$this->Name."_input[".$name."default]".'\'].value=\'\' };_EditorFrame.setEditorIsHot(true);we_cmd(\'change_multiobject_at_class\',\''.$GLOBALS['we_transaction'].'\',\''.$identifier.'\',\''.$name.'\')"',"value",388);
			$content .= '</td></tr>';
			$content .= 	'<tr valign="top">'
						.	'<td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["max_objects"].'</td>'
						.	'<td class="defaultfont"><nobr>'.$this->htmlTextInput("we_".$this->Name."_multiobject[".$name."max]",5,$this->getElement($name."max","dat"),3,'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_entry_at_class\',\''.$GLOBALS['we_transaction'].'\',\''.($identifier).'\');"',"text",50).' ('.$GLOBALS["l_object"]["no_maximum"].')</nobr></td>'
						. 	'</tr>';

			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont"><table border="0">';
			if(!isset($this->elements[$name."count"]["dat"])){
			    $this->elements[$name."count"]["dat"] = 0;
			}
			for($f=0; $f <= $this->elements[$name."count"]["dat"]; $f++) {
				$content .= $this->getMultiObjectFieldHTML($name, $identifier, $f);
			}

			$content .=	'</tr></table></td></tr>';
			break;

		case 'href':
			$typeVal = $this->getElement($name."hreftype","dat");
			$typeSelect = '<select class="weSelect" id="we_'.$this->Name.'_input['.$name.'hreftype]" name="we_'.$this->Name.'_input['.$name.'hreftype]" onchange="_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_entry_at_class\',\''.$GLOBALS['we_transaction'].'\',\''.$identifier.'\');">
			<option'.(($typeVal=="all"||$typeVal=="") ? " selected" : "").' value="all">all
			<option'.(($typeVal=="int") ? " selected" : "").' value="int">int
			<option'.(($typeVal=="ext") ? " selected" : "").' value="ext">ext
			</select>';
			$fileVal = $this->getElement($name."hreffile","dat") ;
			$fileVal = $fileVal ? $fileVal : "true";
			$fileSelect = '<select class="weSelect" id="we_'.$this->Name.'_input['.$name.'hreffile]" name="we_'.$this->Name.'_input['.$name.'hreffile]">
			<option'.(($fileVal=="true") ? " selected" : "").' value="true">true
			<option'.(($fileVal=="false") ? " selected" : "").' value="false">false
			</select>';
			$dirVal = $this->getElement($name."hrefdirectory","dat");
			$dirVal = $dirVal ? $dirVal : "false"; // options anzeige umgedreht wegen 4363
			$dirSelect = '<select class="weSelect" id="we_'.$this->Name.'_input['.$name.'hrefdirectory]" name="we_'.$this->Name.'_input['.$name.'hrefdirectory]">
			<option'.(($dirVal=="true") ? " selected" : "").' value="true">false
			<option'.(($dirVal=="false") ? " selected" : "").' value="false">true
			</select>';
			$content .= '<tr valign="top"><td  width="100" class="defaultfont"  valign="top"></td>';
			$content .= '<td class="defaultfont">type'.getPixel(8,2);
			$content .= $typeSelect.getPixel(30,2)."file".getPixel(8,2);
			$content .= $fileSelect.getPixel(30,2)."directory".getPixel(8,2);
			$content .= $dirSelect;
			$content .= '</td></tr>';
			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont">';
			$content .= $this->htmlHref($name);//,40,$this->getElement($name."default","dat"),255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",388
			$content .= '</td></tr>';
			break;


		// default
		/*
		if($_REQUEST["we_cmd"][0] == "reload_editpage" && $_REQUEST["we_cmd"][2] == $identifier){
			$this->setElement($name."default","");
		}
		*/
		case 'checkbox':
			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont">';
			$content .= we_forms::checkbox("1", $this->getElement($name."default","dat"), "we_".$this->Name."_input[".$name."default1]", $GLOBALS["l_object"]["checked"], true, "defaultfont", "if(this.checked){document.we_form.elements['"."we_".$this->Name."_input[".$name."default]"."'].value=1;}else{ document.we_form.elements['"."we_".$this->Name."_input[".$name."default]"."'].value=0;}");
			$content .= '<input type=hidden name="'."we_".$this->Name."_input[".$name."default]".'" value="'.$this->getElement($name."default","dat").'" />';
			$content .= '</td></tr>';
			break;
		case 'img':
			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["rootdir"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->formDirChooser(267, 0, FILE_TABLE, "ParentPath", "input[".$name."rootdir]", "", $this->getElement($name."rootdir","dat"),$identifier);
			$content .= '</td></tr>';

			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["defaultdir"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->formDirChooser(267, 0, FILE_TABLE, "StartPath", "input[".$name."defaultdir]", "", $this->getElement($name."defaultdir","dat"),$identifier);
			$content .= '</td></tr>';

			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin" valign="top">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->getImageHTML($name."default",$this->getElement($name."default","dat"),$identifier);
			$content .= '</td></tr>';
			break;
		case 'flashmovie':
			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["rootdir"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->formDirChooser(267, 0, FILE_TABLE, "ParentPath", "input[".$name."rootdir]", "", $this->getElement($name."rootdir","dat"),$identifier);
			$content .= '</td></tr>';

			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["defaultdir"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->formDirChooser(267, 0, FILE_TABLE, "StartPath", "input[".$name."defaultdir]", "", $this->getElement($name."defaultdir","dat"),$identifier);
			$content .= '</td></tr>';

			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin" valign="top">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->getFlashmovieHTML($name."default",$this->getElement($name."default","dat"),$identifier);
			$content .= '</td></tr>';
			break;
		case 'quicktime':
			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["rootdir"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->formDirChooser(267, 0, FILE_TABLE, "ParentPath", "input[".$name."rootdir]", "", $this->getElement($name."rootdir","dat"),$identifier);
			$content .= '</td></tr>';

			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["defaultdir"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->formDirChooser(267, 0, FILE_TABLE, "StartPath", "input[".$name."defaultdir]", "", $this->getElement($name."defaultdir","dat"),$identifier);
			$content .= '</td></tr>';

			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin" valign="top">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->getQuicktimeHTML($name."default",$this->getElement($name."default","dat"),$identifier);
			$content .= '</td></tr>';
			break;
		case 'binary':
			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["rootdir"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->formDirChooser(267, 0, FILE_TABLE, "ParentPath", "input[".$name."rootdir]", "", $this->getElement($name."rootdir","dat"),$identifier);
			$content .= '</td></tr>';

			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["defaultdir"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->formDirChooser(267, 0, FILE_TABLE, "StartPath", "input[".$name."defaultdir]", "", $this->getElement($name."defaultdir","dat"),$identifier);
			$content .= '</td></tr>';
			$content .= '<tr><td  width="100" valign="top" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class= "defaultfont"  valign="top">';
			$content .= $this->getBinaryHTML($name."default",$this->getElement($name."default","dat"),$identifier);
			$content .= '</td></tr>';
			break;
		case 'date':
			
			$d = abs($this->getElement($name."default","dat"));
			$dd = abs($this->getElement($name."defaultThumb","dat"));
			$content .= '<tr valign="top"><td  width="100" class="defaultfont">Default</td>';
			$content .= '<td width="170" class="defaultfont">';
			//$content .= we_forms::checkbox("1", $this->getElement($name."default","dat"), "we_".$this->Name."_date[".$name."defaultThumb]", $GLOBALS["l_object"]["checked"], true, "defaultfont", "if(this.checked){document.we_form.elements['"."we_".$this->Name."_input[".$name."default]"."'].value=1;}else{ document.we_form.elements['"."we_".$this->Name."_input[".$name."default]"."'].value=0;}");
			$content .= we_forms::checkboxWithHidden(($dd =='1' ? true : false), "we_".$this->Name."_xdate[".$name."defaultThumb]", 'Creation Date',false,'defaultfont','_EditorFrame.setEditorIsHot(true);');
			$content .= getDateInput2("we_".$this->Name."_date[".$name."default]",($d ? $d : time()),true);
			$content .= '</td></tr>';
			
			break;
		case 'text':
			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin"  valign="top">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';

			$content .= $this->dhtmledit($name,$identifier);

			$content .= '</td></tr>';
			break;
		case 'object':
			$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin"  valign="top">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont"  valign="top">';
			$content .= $this->getObjectFieldHTML($name, isset($attribs) ? $attribs : "");
			$content .= '</td></tr>';
			break;
		case 'meta':
			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont"><table border="0"><tr><td class="defaultfont">Key</td><td class="defaultfont">Value</td><td></td></tr>';
			if(!isset($this->elements[$name."count"]["dat"])){
			    $this->elements[$name."count"]["dat"] = 0;
			}

			$addArray = array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10);

			for($f=0; $f <= $this->elements[$name."count"]["dat"]; $f++){
				$content .= "<tr><td>".$this->htmlTextInput("we_".$this->Name."_input[".$name."defaultkey".$f."]",40,$this->getElement($name."defaultkey".$f,"dat"),255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",105);
				$content .= "</td><td>".$this->htmlTextInput("we_".$this->Name."_input[".$name."defaultvalue".$f."]",40,$this->getElement($name."defaultvalue".$f,"dat"),255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",105);

				$upbut       = $we_button->create_button("image:btn_direction_up", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('up_meta_at_class','".$GLOBALS['we_transaction']."','".($identifier)."','".$name."','".($f)."')");
				$upbutDis    = $we_button->create_button("image:btn_direction_up", "#", true, 21, 22, "", "", true);
				$downbut     = $we_button->create_button("image:btn_direction_down", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('down_meta_at_class','".$GLOBALS['we_transaction']."','".($identifier)."','".$name."','".($f)."')");
				$downbutDis  = $we_button->create_button("image:btn_direction_down", "#", true, 21, 22, "", "", true);

				$plusAmount  = $this->htmlSelect("amount_insert_meta_at_class_" . $name . $f, $addArray);
				$plusbut     = $we_button->create_button("image:btn_add_listelement", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('insert_meta_at_class','".$GLOBALS['we_transaction']."','".($identifier)."','".$name."','".($f)."')");
				$trashbut    = $we_button->create_button("image:btn_function_trash", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('delete_meta_class','".$GLOBALS['we_transaction']."','".($identifier)."','".$name."','".($f)."')");

				$content .=  "</td><td>" .

						$we_button->create_button_table(array(	$plusAmount,
																$plusbut,
																(($f>0) ? $upbut : $upbutDis ),
																(($f<($this->elements[$name."count"]["dat"])) ? $downbut : $downbutDis),
																$trashbut
															  ),
															5
														) .
							"</td></tr>";
				//$content.="test<br>test<input type='text'>".$upbut."test<br>";

			}
			$content .= '</table></td></tr>';
			break;
		
		case 'country':
			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont">';
			$content .= $this->htmlTextInput("we_".$this->Name."_country[".$name."default]",40,$this->getElement($name."default","dat"),10,'onChange="_EditorFrame.setEditorIsHot(true);" weType="' . $type . '"',"text",388);
			$content .= '</td></tr>';
			break;
		case 'language':
			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont">';
			$content .= $this->htmlTextInput("we_".$this->Name."_language[".$name."default]",40,$this->getElement($name."default","dat"),15,'onChange="_EditorFrame.setEditorIsHot(true);" weType="' . $type . '"',"text",388);
			$content .= '</td></tr>';
			break;
		case 'link':
			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont">';
			$content .= $this->htmlLinkInput($name, $identifier);//,40,$this->getElement($name."default","dat"),255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",388
			$content .= '</td></tr>';
			break;
		case 'shopVat':
			$values = array();
			if (defined('SHOP_TABLE')) {
				require_once(WE_SHOP_MODULE_DIR . 'weShopVats.class.php');

				$allVats = weShopVats::getAllShopVATs();
				foreach ($allVats as $id => $shopVat) {
					$values[$id] = $shopVat->vat . ' - ' . $shopVat->text;
					if ($shopVat->standard) {

						$standardId = $id;
						$standardVal = $shopVat->vat;
					}
				}
			}
			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont">';
			$content .= we_class::htmlSelect("we_".$this->Name."_shopVat[".$name."default]", $values, 1, $this->getElement($name."default","dat")); //$this->htmlTextInput("we_".$this->Name."_shopVat[".$name."default]",40,$this->getElement($name."default","dat"),255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",388);
			$content .= '</td></tr>';
			break;
		default: // default for input, int and float

			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_object"]["default"].'</td>';
			$content .= '<td width="170" class="defaultfont">';
			$content .= $this->htmlTextInput("we_".$this->Name."_input[".$name."default]",40,$this->getElement($name."default","dat"),($type=='int'?10:($type=='float'?19:255)),'onChange="_EditorFrame.setEditorIsHot(true);" weType="' . $type . '"',"text",388);
			$content .= '</td></tr>';
			break;
		}


		if($type=="text" || $type=="input" || $type=="meta" || $type=="link" || $type=="href"){
			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin"></td>';
			$content .= '<td width="170" class="defaultfont">';
			// TITEL
			$content .=  we_forms::radiobutton($name, (($this->getElement("title","dat")==$name)?1:0), "we_".$this->Name."_input[title]", $GLOBALS["l_global"]["title"], true, "defaultfont","if(this.waschecked){this.checked=false;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);",false,"",0,0,"if(this.checked){this.waschecked=true}");

			// Beschreibung
			$content .= we_forms::radiobutton($name, (($this->getElement("desc","dat")==$name)?1:0), "we_".$this->Name."_input[desc]", $GLOBALS["l_global"]["description"], true, "defaultfont", "if(this.waschecked){this.checked=false;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);",false,"",0,0,"if(this.checked){this.waschecked=true}");

			// Keywords
			$content .= we_forms::radiobutton($name, (($this->getElement("keywords","dat")==$name)?1:0), "we_".$this->Name."_input[keywords]", $GLOBALS["l_we_class"]["Keywords"], true, "defaultfont", "if(this.waschecked){this.checked=false;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);",false,"",0,0,"if(this.checked){this.waschecked=true}");

			$content .= '</td></tr>';
		}

		if($type=="text" || $type=="input" || $type=="date" ){
			$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin"></td>';
			$content .= '<td width="170" class="defaultfont">';
			if ($type=="date") {
				$content .= we_forms::radiobutton($name, (($this->getElement("urlfield0","dat")==$name)?1:0), "we_".$this->Name."_input[urlfield0]", $GLOBALS["l_we_class"]["urlfield0"], true, "defaultfont","if(this.waschecked){this.checked=false;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);",false,"",0,0,"if(this.checked){this.waschecked=true}");
			} else {
				$content .= we_forms::radiobutton($name, (($this->getElement("urlfield1","dat")==$name)?1:0), "we_".$this->Name."_input[urlfield1]", $GLOBALS["l_we_class"]["urlfield1"], true, "defaultfont","if(this.waschecked){this.checked=false;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);",false,"",0,0,"if(this.checked){this.waschecked=true}");
				$content .= we_forms::radiobutton($name, (($this->getElement("urlfield2","dat")==$name)?1:0), "we_".$this->Name."_input[urlfield2]", $GLOBALS["l_we_class"]["urlfield2"], true, "defaultfont", "if(this.waschecked){this.checked=false;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);",false,"",0,0,"if(this.checked){this.waschecked=true}");
				$content .= we_forms::radiobutton($name, (($this->getElement("urlfield3","dat")==$name)?1:0), "we_".$this->Name."_input[urlfield3]", $GLOBALS["l_we_class"]["urlfield3"], true, "defaultfont", "if(this.waschecked){this.checked=false;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);",false,"",0,0,"if(this.checked){this.waschecked=true}");
			}
			$content .= '</td></tr>';
		}



		if ($type != "checkbox") {
			//Pflichtfeld
			$content .= '<tr valign="top"><td  width="100" class="defaultfont"></td>';
			$content .= '<td width="170" class="defaultfont">';
			$content .= we_forms::checkbox("1", $this->getElement($name."required","dat"), "we_".$this->Name."_input[".$name."required1]", $GLOBALS["l_global"]["required_field"], true, "defaultfont", "if(this.checked){document.we_form.elements['"."we_".$this->Name."_input[".$name."required]"."'].value=1;}else{ document.we_form.elements['"."we_".$this->Name."_input[".$name."required]"."'].value=0;}");
			if(defined('SHOP_TABLE')){
				if($this->canHaveVariants() && $this->isVariantField($name)){
					$variant = $this->getElement($name."variant","dat");
					$content .= we_forms::checkboxWithHidden(($variant ==1 ? true : false), "we_".$this->Name."_variant[".$name."variant]", $GLOBALS["l_global"]["variant_field"],false,'defaultfont','_EditorFrame.setEditorIsHot(true);');
				}
			}
			$content .= '<input type=hidden name="'."we_".$this->Name."_input[".$name."required]".'" value="'.$this->getElement($name."required","dat").'" />';
			$content .= '</td></tr>';
			// description for editmode.
		} else if(defined('SHOP_TABLE')){
			//Pflichtfeld
			$content .= '<tr valign="top"><td  width="100" class="defaultfont"></td>';
			$content .= '<td width="170" class="defaultfont">';
			if($this->canHaveVariants() && $this->isVariantField($name)){
				$variant = $this->getElement($name."variant","dat");
				$content .= we_forms::checkboxWithHidden(($variant ==1 ? true : false), "we_".$this->Name."_variant[".$name."variant]", $GLOBALS["l_global"]["variant_field"],false,'defaultfont','_EditorFrame.setEditorIsHot(true);');
			}
			$content .= '<input type=hidden name="'."we_".$this->Name."_input[".$name."required]".'" value="0" />';
			$content .= '</td></tr>';
			// description for editmode.
		} else {
			$content .= '<input type=hidden name="'."we_".$this->Name."_input[".$name."required]".'" value="0" />';
		}


		$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">'.$GLOBALS["l_we_class"]["fieldusers"].'</td>';
		$content .= '<td width="170" class="defaultfont" >';
		$content .= $this->formUsers1($name,$identifier);
		$content .= '</td></tr>';

		return $content;

	}

	function htmlHref($n){
		global $l_global;

		$type = isset($this->elements[$n."hreftype"]["dat"]) ?
		$this->elements[$n."hreftype"]["dat"] :
		"";


		$n = $n."default";
		$out = "";
		$hrefArr = $this->getElement($n) ? unserialize($this->getElement($n)) : array();
		if(!is_array($hrefArr)) $hrefArr= array();

		$nint = $n."_we_jkhdsf_int";
		$nintID = $n."_we_jkhdsf_intID";
		$nintPath = $n."_we_jkhdsf_intPath";
		$nextPath = $n."_we_jkhdsf_extPath";

		$attr = ' size="20" ';


		$int = isset($hrefArr["int"]) ? $hrefArr["int"] : false;
		$intID = isset($hrefArr["intID"]) ? $hrefArr["intID"] : 0;
		$intPath = $intID ? id_to_path($intID) : "";
		$extPath = isset($hrefArr["extPath"]) ? $hrefArr["extPath"] : "";
		$objID = isset($hrefArr["objID"]) ? $hrefArr["objID"] : 0;
		$objPath = $objID ? id_to_path($objID,OBJECT_FILES_TABLE) : "";
		$int_elem_Name = 'we_'.$this->Name.'_href['.$nint.']';
		$intPath_elem_Name = 'we_'.$this->Name.'_href['.$nintPath.']';
		$intID_elem_Name = 'we_'.$this->Name.'_href['.$nintID.']';
		$ext_elem_Name = 'we_'.$this->Name.'_href['.$nextPath.']';

		switch($type){
			case "int":
			$out = $this->hrefRow($intID_elem_Name,
			$intID,
			$intPath_elem_Name,
			$intPath,
			$attr,
			$int_elem_Name);
			break;
			case "ext":
			$out = $this->hrefRow("",
			"",
			$ext_elem_Name,
			$extPath,
			$attr,
			$int_elem_Name);
			break;
			default:
			$out = $this->hrefRow($intID_elem_Name,
			$intID,
			$intPath_elem_Name,
			$intPath,
			$attr,
			$int_elem_Name,
			true,
			$int) .
			$this->hrefRow("",
			"",
			$ext_elem_Name,
			$extPath,
			$attr,
			$int_elem_Name,
			true,
			$int);
		}
		return '<table border="0" cellpadding="0" cellspacing="0">'.$out.'</table>';
	}

	function htmlLinkInput($n, $i){
		global $l_global;
		$we_button = new we_button();

		$attribs = array();
		$n = $n."default";
		$attribs["name"]=$n;
		$out = "";
		$link = $this->getElement($n) ? unserialize($this->getElement($n)) : array();
		if(!is_array($link)) $link= array();
		if(!sizeof($link)){
			$link = array("ctype"=>"text","type"=>"ext","href"=>"#","text"=>$GLOBALS["l_global"]["new_link"]);
		}
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_classes/we_imageDocument.inc.php");
		$img = new we_imageDocument();
		$content = we_document::getLinkContent($link,$this->ParentID,$this->Path,$GLOBALS["DB_WE"],$img);

		$startTag = $this->getLinkStartTag($link,$attribs,$this->ParentID,$this->Path,$GLOBALS["DB_WE"],$img);
		$editbut = $we_button->create_button("edit", "javascript:we_cmd('edit_link_at_class','".$n."','','".$i."');");
		$delbut = $we_button->create_button("image:btn_function_trash", "javascript:setScrollTo();we_cmd('delete_link_at_class','".$GLOBALS['we_transaction']."','".$i."','".$n."')");
		if(!$content) $content = $GLOBALS["l_global"]["new_link"];
		if($startTag){
			$out = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
					<tr>
						<td class=\"defaultfont\">" . $startTag.$content."</a></td>
						<td width=\"5\"></td>
						<td>" . $we_button->create_button_table(array(	$editbut,
																		$delbut),5)
							  . "</td>
					</tr>
					</table>";
		}else{
			$out = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
					<tr>
						<td class=\"defaultfont\">" . $content."</td>
						<td width=\"5\"></td>
						<td>" . $we_button->create_button_table(array(	$editbut,
																		$delbut),5)
							  . "</td>
					</tr>
					</table>";
		}

		return $out;
	}

	function getObjectFieldHTML($ObjectID,$attribs,$editable=true){
		global $l_object;
		$pid = $this->getElement($ObjectID,"dat");
		$we_button = new we_button();
		if($editable){
			$db = new DB_WE();
			$classPath = f("SELECT Path FROM " . OBJECT_TABLE . " WHERE ID=$pid","Path",$db) ;
			$textname = 'we_'.$this->Name.'_txt['.$pid.'_path]';
			$idname = 'we_'.$this->Name."_input[".$ObjectID."default]";
			$myid = $this->getElement($ObjectID."default","dat");
 			$DoubleNames = $this->includedObjectHasDoubbleFieldNames($pid);
			$path = $this->getElement("we_object_".$pid."_path");
			$path = $path ? $path : ($myid?f("SELECT Path FROM " . OBJECT_FILES_TABLE . " WHERE ID=$myid","Path",$db):'');
			$rootDir = f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Path='$classPath'","ID",$db);
			$table = OBJECT_FILES_TABLE;
			//javascript:we_cmd('openDocselector',document.forms['we_form'].elements['$idname'].value,'$table','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','top.opener._EditorFrame.setEditorIsHot(true);','".session_id()."','$rootDir','objectFile',".(we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1).")
			$wecmdenc1= we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
			$wecmdenc2= we_cmd_enc("document.forms['we_form'].elements['$textname'].value");
			$wecmdenc3= we_cmd_enc("top.opener._EditorFrame.setEditorIsHot(true);");
			$button = $we_button->create_button("select", "javascript:we_cmd('openDocselector',document.forms['we_form'].elements['$idname'].value,'$table','".$wecmdenc1."','".$wecmdenc2."','".$wecmdenc3."','".session_id()."','$rootDir','objectFile',".(we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1).")");
			$delbutton = $we_button->create_button("image:btn_function_trash", "javascript:document.forms['we_form'].elements['$idname'].value='';document.forms['we_form'].elements['$textname'].value=''");
/*
DAMD: der Autocompleter funktioniert hier nicht. Der HTML-Cokde wird dynamisch erzeugt das
			$yuiSuggest =& weSuggest::getInstance();
			$yuiSuggest->setAcId("TypeObject");
			$yuiSuggest->setContentType("folder,objectFile");
			$yuiSuggest->setInput($textname,$path);
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(false);
			$yuiSuggest->setResult($idname,$myid);
			$yuiSuggest->setSelector("Docselector");
			$yuiSuggest->setTable($table);
			$yuiSuggest->setWidth(246);
			$yuiSuggest->setSelectButton($button,10);
			$yuiSuggest->setTrashButton($delbutton,5);
			$yuiSuggest->setAddJS("YAHOO.autocoml.init;");

			return $yuiSuggest->getYuiFiles().$yuiSuggest->getHTML().$yuiSuggest->getYuiCode();
			*/
			return $this->htmlFormElementTable(
			$this->htmlTextInput($textname,30,$path,"",' readonly',"text",246,0),
			"",
			"left",
			"defaultfont",
			$this->htmlHidden($idname,$myid),
			getPixel(10,4),
			$button,getPixel(5,4),$delbutton) . ($DoubleNames ? '<span style="color:red" >' .$l_object["incObject_sameFieldname_start"] . implode(', ',$DoubleNames). $l_object["incObject_sameFieldname_end"] .'</span>':'');
		}
	}

	function getMultiObjectFieldHTML($name, $i, $f){
		$pid = $this->getElement($name."class","dat");
		$we_button = new we_button();

		$db = new DB_WE();
		$classPath = f("SELECT Path FROM " . OBJECT_TABLE . " WHERE ID=$pid","Path",$db) ;
		$textname = 'we_'.$this->Name.'_txt['.$name.'_path'.$f.']';
		$idname = 'we_'.$this->Name."_input[".$name."defaultvalue".$f."]";
		$myid = $this->getElement($name."defaultvalue".$f,"dat");

		$path = $this->getElement("we_object_".$name."_path");
		$path = ($path ? $path : ($myid ? f("SELECT Path FROM " . OBJECT_FILES_TABLE . " WHERE ID=$myid","Path",$db) : ''));
		$rootDir = f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Path='$classPath'","ID",$db);

		$table = OBJECT_FILES_TABLE;
		//javascript:we_cmd('openDocselector',document.forms['we_form'].elements['$idname'].value,'$table','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','top.opener._EditorFrame.setEditorIsHot(true);','".session_id()."','$rootDir','objectFile',".(we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1).")
		$wecmdenc1= we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
		$wecmdenc2= we_cmd_enc("document.forms['we_form'].elements['$textname'].value");
		$wecmdenc3= we_cmd_enc("top.opener._EditorFrame.setEditorIsHot(true);");

		$selectObject = $we_button->create_button("select", "javascript:we_cmd('openDocselector',document.forms['we_form'].elements['$idname'].value,'$table','".$wecmdenc1."','".$wecmdenc2."','".$wecmdenc3."','".session_id()."','$rootDir','objectFile',".(we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1).")");

		$upbut       = $we_button->create_button("image:btn_direction_up", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('up_meta_at_class','".$GLOBALS['we_transaction']."','".($i)."','".$name."','".($f)."')", true, 21, 22);
		$upbutDis    = $we_button->create_button("image:btn_direction_up", "#", true, 21, 22, "", "", true);
		$downbut     = $we_button->create_button("image:btn_direction_down", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('down_meta_at_class','".$GLOBALS['we_transaction']."','".($i)."','".$name."','".($f)."')", true, 21, 22);
		$downbutDis  = $we_button->create_button("image:btn_direction_down", "#", true, 21, 22, "", "", true);

		$plusbut     = $we_button->create_button("image:btn_add_listelement", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('insert_meta_at_class','".$GLOBALS['we_transaction']."','".($i)."','".$name."','".($f)."')", true, 40,22);
		$plusbutDis  = $we_button->create_button("image:btn_add_listelement", "#", true, 21, 22, "", "", true);
		$trashbut    = $we_button->create_button("image:btn_function_trash", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('delete_meta_class','".$GLOBALS['we_transaction']."','".($i)."','".$name."','".($f)."')", true, 27, 22);
		$trashbutDis = $we_button->create_button("image:btn_function_trash", "#", true, 27, 22, "", "", true);
		$content =		"<tr>"
					.	"<td>"
					.	"<td>"
					.	$this->htmlTextInput($textname,30,$path,255,'onChange="_EditorFrame.setEditorIsHot(true);" readonly ',"text",146)."</td>"
					.	"<td>"
					.	$we_button->create_button_table(
													array(
														$selectObject,
														$this->htmlHidden($idname,$myid),
														(($this->elements[$name."count"]["dat"]+1<$this->getElement($name."max")||$this->getElement($name."max")=="") ?$plusbut:$plusbutDis),
														(($f>0) ? $upbut : $upbutDis ),
														(($f<($this->elements[$name."count"]["dat"])) ? $downbut : $downbutDis),
														($this->elements[$name."count"]["dat"]>=1?$trashbut:$trashbutDis)
													),
													5
												)
					.	"</td></tr>";

		return $content;

	}

	function dhtmledit($name,$i=0){


		$we_button = new we_button();
		$_but =  $we_button->create_button("attributes", "javascript:we_cmd('editObjectTextArea','".$i."','".$name."','".$GLOBALS["we_transaction"]."');");
		return $_but. $this->getWysiwygArea($name);
	}

	function getWysiwygArea($name){

		$attribs = array();
		$attribs["removefirstparagraph"] = isset($this->elements[$name."removefirstparagraph"]["dat"]) ? $this->elements[$name."removefirstparagraph"]["dat"] : defined("REMOVEFIRSTPARAGRAPH_DEFAULT") ? REMOVEFIRSTPARAGRAPH_DEFAULT : true;
		$attribs["xml"] = isset($this->elements[$name."xml"]["dat"]) ? $this->elements[$name."xml"]["dat"] : "";
		$attribs["dhtmledit"] = isset($this->elements[$name."dhtmledit"]["dat"]) ? $this->elements[$name."dhtmledit"]["dat"] : "";
		$attribs["wysiwyg"] = isset($this->elements[$name."dhtmledit"]["dat"]) ? $this->elements[$name."dhtmledit"]["dat"] : "";
		$attribs["showmenus"] = isset($this->elements[$name."showmenus"]["dat"]) ? $this->elements[$name."showmenus"]["dat"] : "off";
		$attribs["commands"] = isset($this->elements[$name."commands"]["dat"]) ? $this->elements[$name."commands"]["dat"] : "";
		$attribs["classes"] = isset($this->elements[$name."cssClasses"]["dat"]) ? $this->elements[$name."cssClasses"]["dat"] : "";
		$attribs["width"] = 386; //isset($this->elements[$name."width"]["dat"]) ? $this->elements[$name."width"]["dat"] : 618;
		$attribs["height"] = 52; //isset($this->elements[$name."height"]["dat"]) ? $this->elements[$name."height"]["dat"] : 200;
		$attribs["rows"] = 3;
		$attribs["bgcolor"] = "white";
		$attribs["cols"] = 30;
		$attribs["inlineedit"] = isset($this->elements[$name."inlineedit"]["dat"]) ? $this->elements[$name."inlineedit"]["dat"] : "";
		$attribs["stylesheets"] = $this->CSS;

		$attribs["spellchecker"] = true;


		$autobr = isset($this->elements[$name."autobr"]["dat"]) ? $this->elements[$name."autobr"]["dat"] : "";
		$autobrName = 'we_'.$this->Name.'_input['.$name.'autobr]';

		$value = $this->getElement($name."default","dat");
		return we_forms::weTextarea("we_".$this->Name."_input[".$name."default]",$value,$attribs,$autobr,$autobrName,true,"", (($this->CSS || $attribs["classes"]) ? false : true), false, false, true,"");
	}

	function add_user_to_field($id,$name){
		$users = makeArrayFromCSV($this->getElement($name."users","dat"));
		$ids = makeArrayFromCSV($id);
		foreach($ids as $id){
			if($id && (!in_array($id,$users))) {
				array_push($users,$id);
			}
		}
		$this->elements[$name."users"]["dat"] = makeCSVFromArray($users,true);
	}

	function del_user_from_field($id,$name){
		$csv = $this->getElement($name."users","dat");
		$csv = str_replace($id.',', '', $csv);
		if($csv == ",") $csv = "";
		$this->elements[$name."users"]["dat"] = $csv;
	}

	function formUsers1($name,$nr=0){
		global $l_we_class;

		$we_button = new we_button();
		$users = $this->getElement($name."users","dat") ? explode(",",$this->getElement($name."users","dat")) : array();
		$content = '<table border="0" cellpadding="0" cellspacing="0" width="388">';
		$content .= '<tr><td>'.getPixel(20,2).'</td><td>'.getPixel(324,2).'</td><td>'.getPixel(26,2).'</td></tr>'."\n";
		if(sizeof($users)){
			for($i=1;$i<(sizeof($users) -1);$i++){
				$foo = getHash("SELECT ID,Path,Icon FROM " . USER_TABLE . " WHERE ID='".$users[$i]."'",$this->DB_WE);
				$content .= '<tr><td><img src="'.ICON_DIR.$foo["Icon"].'" width="16" height="18" /></td><td class="defaultfont">'.$foo["Path"].'</td><td>' . $we_button->create_button("image:btn_function_trash", "javascript:we_cmd('del_user_from_field','".$GLOBALS['we_transaction']."','".$nr."',".$users[$i].",'".$name."');").'</td></tr>'."\n";
			}
		}else{
			$content .= '<tr><td><img src="'.ICON_DIR.'usergroup.gif" width="16" height="18" /></td><td class="defaultfont">'.$GLOBALS["l_we_class"]["everybody"].'</td><td>'.getPixel(26,18).'</td></tr>'."\n";
		}
		$content .= '<tr><td>'.getPixel(20,2).'</td><td>'.getPixel(324,2).'</td><td>'.getPixel(26,2).'</td></tr></table>'."\n";

		$textname = "we_".$this->Name."_input[".$name."usertext]";
		$idname = "we_".$this->Name."_input[".$name."userid]";
		$delallbut = $we_button->create_button("delete_all","javascript:we_cmd('del_all_users','".$GLOBALS['we_transaction']."','$nr','$name')",true,-1,-1,"","",sizeof($users) ? false : true);
		$addbut = $this->htmlHidden($idname,"0").$this->htmlHidden($textname,"").$we_button->create_button("add", "javascript:we_cmd('browse_users','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','',document.forms['we_form'].elements['".$idname."'].value,'fillIDs();opener.we_cmd(\\'add_user_to_field\\',\\'".$GLOBALS['we_transaction']."\\',\\'".$nr."\\', top.allIDs,\\'".$name."\\')','','',1)");

		return '<table border="0" cellpadding="0" cellspacing="0"><tr><td>'.
		'<div style="width:388px;" class="multichooser">'.$content.'</div>'.'</td></tr><tr><td align="right">'.getPixel(2,4).$we_button->create_button_table(array($delallbut, $addbut)).'</td></tr></table>';
	}

	function formUsers($canChange=true){
		global $l_we_class;

		$we_button = new we_button();

		$users = makeArrayFromCSV($this->Users);
		$usersReadOnly = $this->UsersReadOnly ? unserialize($this->UsersReadOnly) : array();

		$content = '<table border="0" cellpadding="0" cellspacing="0" width="388">';
		$content .= '<tr><td>'.getPixel(20,2).'</td><td>'.getPixel(333,2).'</td><td>'.getPixel(20,2).'</td><td>'.getPixel(80,2).'</td><td>'.getPixel(26,2).'</td></tr>'."\n";
		if(sizeof($users)){
			for($i=0;$i<sizeof($users);$i++){
				$foo = getHash("SELECT ID,Path,Icon from " . USER_TABLE . " WHERE ID='".$users[$i]."'",$this->DB_WE);
				$content .= '<tr><td><img src="'.ICON_DIR.$foo["Icon"].'" width="16" height="18" /></td><td class="defaultfont">'.$foo["Path"].'</td><td>'.
				($canChange ?
				$this->htmlHidden('we_users_read_only['.$users[$i].']',(isset($usersReadOnly[$users[$i]]) && $usersReadOnly[$users[$i]]) ? $usersReadOnly[$users[$i]] : "" ).'<input type="checkbox" value="1" name="wetmp_users_read_only['.$users[$i].']"'.( (isset($usersReadOnly[$users[$i]]) && $usersReadOnly[$users[$i]] ) ? ' checked' : '').' OnClick="this.form.elements[\'we_users_read_only['.$users[$i].']\'].value=(this.checked ? 1 : 0);_EditorFrame.setEditorIsHot(true);" />' :
				'<img src="'.TREE_IMAGE_DIR.($usersReadOnly[$users[$i]] ? 'check1_disabled.gif' : 'check0_disabled.gif').'" />').'</td><td class="defaultfont">'.$l_we_class["readOnly"].'</td><td>'.($canChange ? $we_button->create_button("image:btn_function_trash", "javascript:we_cmd('del_user','".$users[$i]."');_EditorFrame.setEditorIsHot(true);") : "").'</td></tr>'."\n";
			}
		}else{
			$content .= '<tr><td><img src="'.ICON_DIR."user.gif".'" width="16" height="18" /></td><td class="defaultfont">'.$l_we_class["onlyOwner"].'</td><td></td></tr>'."\n";
		}
		$content .= '<tr><td>'.getPixel(20,2).'</td><td>'.getPixel(333,2).'</td><td>'.getPixel(20,2).'</td><td>'.getPixel(80,2).'</td><td>'.getPixel(26,2).'</td></tr></table>'."\n";

		$textname = 'userNameTmp';
		$idname = 'userIDTmp';
		$delallbut = $we_button->create_button("delete_all","javascript:we_cmd('del_all_users','')",true,-1,-1,"","",$this->Users ? false : true);
		//javascript:we_cmd('browse_users','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','',document.forms[0].elements['$idname'].value,'fillIDs();opener.we_cmd(\\'add_user\\',top.allIDs)','','',1)
		$wecmdenc1= we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
		$wecmdenc2= we_cmd_enc("document.forms['we_form'].elements['$textname'].value");
		$wecmdenc5= we_cmd_enc("fillIDs();opener.we_cmd('add_user',top.allIDs)");
		$addbut = $canChange ?
		$this->htmlHidden($idname,"").$this->htmlHidden($textname,""). $we_button->create_button("add", "javascript:we_cmd('browse_users','".$wecmdenc1."','".$wecmdenc2."','',document.forms[0].elements['$idname'].value,'".$wecmdenc5."','','',1)")
		: "";

		$content = '<table border="0" cellpadding="0" cellspacing="0">
<tr><td>'.'<div style="width:506px;" class="multichooser">'.$content.'</div>'.'</td></tr>
'.($canChange ? '<tr><td align="right">'.getPixel(2,6).'<br>'.$we_button->create_button_table(array($delallbut, $addbut)).'</td></tr>' : "").'</table'."\n";

		return $this->htmlFormElementTable($content,
		$l_we_class["otherowners"],
		"left",
		"defaultfont");
	}

	function del_all_users($name){
		if ($name == "") {
			$this->Users = "";
		} else {
			$this->elements[$name."users"]["dat"] = "";
		}
	}

	function add_user($id){
		$users = makeArrayFromCSV($this->Users);
		$ids = makeArrayFromCSV($id);
		foreach($ids as $id){
			if($id && (!in_array($id,$users))) {
				array_push($users,$id);
			}
		}
		$this->Users=makeCSVFromArray($users,true);
	}

	function del_user($id){
		$users = makeArrayFromCSV($this->Users);
		if(in_array($id,$users)){
			$pos = getArrayKey($id,$users);
			if($pos != "" || $pos=="0"){
				array_splice($users,$pos,1);
			}
		}
		$this->Users=makeCSVFromArray($users,true);

	}

	function add_css($id){
		$css = makeArrayFromCSV($this->CSS);
		$ids = makeArrayFromCSV($id);
		foreach($ids as $id){
			if($id && (!in_array($id,$css))) {
				array_push($css,$id);
			}
		}
		$this->CSS=makeCSVFromArray($css,true);
	}

	function del_css($id){
		$css = makeArrayFromCSV($this->CSS);
		if(in_array($id,$css)){
			$pos = getArrayKey($id,$css);
			if($pos != "" || $pos=="0"){
				array_splice($css,$pos,1);
			}
		}
		$this->CSS=makeCSVFromArray($css,true);

	}

	function getImageHTML($name,$defaultname,$i=0){
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_classes/we_imageDocument.inc.php");

		$we_button = new we_button();
		$content = "";
		$img = new we_imageDocument();
		$id = $defaultname;//$this->getElement($defaultname);
		if ($id) {
			$img->initByID($id,FILE_TABLE,false);
		} else {
			$img->we_new();
		}

		$fname = 'we_'.$this->Name.'_input['.$name.']';
		$content .= '<input type=hidden name="'.$fname.'" value="'.$defaultname.'" />';
		
		//javascript:we_cmd('openDocselector','" . $id . "','" .FILE_TABLE. "','document.forms[\\'we_form\\'].elements[\\'" . $fname . "\\'].value','','opener.top.we_cmd(\\'reload_entry_at_class\\',\\'".$GLOBALS['we_transaction']."\\',\\'".$i."\\');opener._EditorFrame.setEditorIsHot(true);','".session_id()."',0,'image/*')
		$wecmdenc1= we_cmd_enc("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc2= '';
		$wecmdenc3= we_cmd_enc("opener.top.we_cmd('reload_entry_at_class','".$GLOBALS['we_transaction']."','".$i."');opener._EditorFrame.setEditorIsHot(true);");

		$content .= $we_button->create_button_table(array(
															$we_button->create_button("edit", "javascript:we_cmd('openDocselector','" . $id . "','" .FILE_TABLE. "','".$wecmdenc1."','','".$wecmdenc3."','".session_id()."',0,'image/*')"),
															$we_button->create_button("image:btn_function_trash", "javascript:we_cmd('remove_image_at_class','".$GLOBALS['we_transaction']."','".$i."','".$name."')")
														 )
													)
			;
		$content .= '<br>'.$img->getHtml();
		// gets thumbnails and shows a select field, if there are any:
		$thumbdb = new DB_WE();
		$thumbdb->query("SELECT Name FROM ".THUMBNAILS_TABLE);
		$thumbs = $thumbdb->getAll();
		if(count($thumbs)) {
			$content .= "<br />".$GLOBALS["l_object"]["use_thumbnail_preview"].":<br />";
			$thumbList = array("-");
			foreach($thumbs as $id=>$thumb) {
				$thumbList[] = $thumb["Name"];
			}
			if(isset($this->elements["".$name."Thumb"]) && isset($this->elements["".$name."Thumb"]["dat"]) && isset($thumbList[$this->elements["".$name."Thumb"]["dat"]])) {
				$currentSelection = $this->elements["".$name."Thumb"]["dat"];
			} else {
				$currentSelection = "";
			}

			$content .= $this->htmlSelect("we_".$this->Name."_input[".$name."Thumb]",$thumbList,1,$currentSelection,"",'onchange="_EditorFrame.setEditorIsHot(true);" name="we_'.$this->Name.'_input['.$name.'Thumb]"',"value",388);
		}
		return $content;
	}

	function getFlashmovieHTML($name,$defaultname,$i=0){
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_classes/we_flashDocument.inc.php");

		$we_button = new we_button();
		$content = "";
		$img = new we_flashDocument();
		$id = $defaultname;//$this->getElement($defaultname);
		if ($id) {
			$img->initByID($id,FILE_TABLE,false);
		} else {
			$img->we_new();
		}

		$fname = 'we_'.$this->Name.'_input['.$name.']';
		$content .= '<input type=hidden name="'.$fname.'" value="'.$defaultname.'" />';
		//javascript:we_cmd('openDocselector','" . $id . "','" .FILE_TABLE. "','document.forms[\\'we_form\\'].elements[\\'" . $fname . "\\'].value','','opener.top.we_cmd(\\'reload_entry_at_class\\',\\'".$GLOBALS['we_transaction']."\\',\\'".$i."\\');opener._EditorFrame.setEditorIsHot(true);','".session_id()."',0,'application/x-shockwave-flash')
		$wecmdenc1= we_cmd_enc("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc2= '';
		$wecmdenc3= we_cmd_enc("opener.top.we_cmd('reload_entry_at_class','".$GLOBALS['we_transaction']."','".$i."');opener._EditorFrame.setEditorIsHot(true);");

		$content .= $we_button->create_button_table(array(
															$we_button->create_button("edit", "javascript:we_cmd('openDocselector','" . $id . "','" .FILE_TABLE. "','".$wecmdenc1."','','".$wecmdenc3."','".session_id()."',0,'application/x-shockwave-flash')"),
															$we_button->create_button("image:btn_function_trash", "javascript:we_cmd('remove_image_at_class','".$GLOBALS['we_transaction']."','".$i."','".$name."')")
														 )
													)
			;
		$content .= '<br>'.$img->getHtml();
		return $content;
	}

	function getQuicktimeHTML($name,$defaultname,$i=0){
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_classes/we_quicktimeDocument.inc.php");

		$we_button = new we_button();
		$content = "";
		$img = new we_quicktimeDocument();
		$id = $defaultname;//$this->getElement($defaultname);
		if ($id) {
			$img->initByID($id,FILE_TABLE,false);
		} else {
			$img->we_new();
		}

		$fname = 'we_'.$this->Name.'_input['.$name.']';
		$content .= '<input type=hidden name="'.$fname.'" value="'.$defaultname.'" />';
		//javascript:we_cmd('openDocselector','" . $id . "','" .FILE_TABLE. "','document.forms[\\'we_form\\'].elements[\\'" . $fname . "\\'].value','','opener.top.we_cmd(\\'reload_entry_at_class\\',\\'".$GLOBALS['we_transaction']."\\',\\'".$i."\\');opener._EditorFrame.setEditorIsHot(true);','".session_id()."',0,'video/quicktime')
		$wecmdenc1= we_cmd_enc("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc2= '';
		$wecmdenc3= we_cmd_enc("opener.top.we_cmd('reload_entry_at_class','".$GLOBALS['we_transaction']."','".$i."');opener._EditorFrame.setEditorIsHot(true);");

		$content .= $we_button->create_button_table(array(
															$we_button->create_button("edit", "javascript:we_cmd('openDocselector','" . $id . "','" .FILE_TABLE. "','".$wecmdenc1."','','".$wecmdenc3."','".session_id()."',0,'video/quicktime')"),
															$we_button->create_button("image:btn_function_trash", "javascript:we_cmd('remove_image_at_class','".$GLOBALS['we_transaction']."','".$i."','".$name."')")
														 )
													)
			;
		$content .= '<br>'.$img->getHtml();
		return $content;
	}


	function getBinaryHTML($name,$defaultname,$i=0){
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_classes/we_otherDocument.inc.php");

		$we_button = new we_button();

		$content = "";
		$other = new we_otherDocument();
		$id = $defaultname;//$this->getElement($defaultname);
		$other->initByID($id,FILE_TABLE,false);
		$fname = 'we_'.$this->Name.'_input['.$name.']';
		$content .= '<input type=hidden name="'.$fname.'" value="'.$defaultname.'" />';
		//javascript:we_cmd('openDocselector','".$id."','".FILE_TABLE."','document.forms[\\'we_form\\'].elements[\\'".$fname."\\'].value','','opener.top.we_cmd(\\'reload_entry_at_class\\',\\'".$GLOBALS['we_transaction']."\\',\\'".$i."\\');opener._EditorFrame.setEditorIsHot(true);','".session_id()."',0,'application/*')
		$wecmdenc1= we_cmd_enc("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc2= '';
		$wecmdenc3= we_cmd_enc("opener.top.we_cmd('reload_entry_at_class','".$GLOBALS['we_transaction']."','".$i."');opener._EditorFrame.setEditorIsHot(true);");

		$content .= $we_button->create_button_table(array(
															$we_button->create_button("select", "javascript:we_cmd('openDocselector','".$id."','".FILE_TABLE."','".$wecmdenc1."','','".$wecmdenc3."','".session_id()."',0,'application/*')"),
															$we_button->create_button("image:btn_function_trash", "javascript:we_cmd('remove_image_at_class','".$GLOBALS['we_transaction']."','".$i."','".$name."');")
															)
													);
		$content .= '<br>'.$other->getHtml();
		return $content;
	}

	function formDefault(){
		global $l_object, $l_global,$l_object_value,$l_object_url;
		//$l_global["categorys"]formCategory()

		$var_flip = array_flip($l_object_value);
		$select = "";
		if(isset($this->elements["Defaultanzahl"]["dat"])){
			$this->DefaultText="";

			for($i=0; $i <= $this->elements["Defaultanzahl"]["dat"];$i++){
				$was = "DefaultText_".$i;
				if($this->elements[$was]["dat"]!=""){ //&& in_array($this->elements[$was]["dat"],$var_flip)
				if(stristr($this->elements[$was]["dat"], 'unique')){
					$this->elements[$was]["dat"] = "%".str_replace("%","",$this->elements[$was]["dat"]).(( isset($this->elements["unique_".$i]["dat"]) && $this->elements["unique_".$i]["dat"]>0 )?$this->elements["unique_".$i]["dat"]:"16")."%";
					//echo $this->elements[$was]["dat"];
				}
				$this->DefaultText .= $this->elements[$was]["dat"];
				}
			}

		}

		$all = $this->DefaultText;
		$text1=0;
		$zahl=0;

		while(!empty($all)){
			if(preg_match('/^%([^%]+)%/', $all, $regs)){
				$all = substr($all,strlen($regs[1])+2);
				$key = $regs[1];
				if(preg_match('/unique([^%]*)/', $key, $regs)){
					if(!$regs[1]){
						$anz = 16;
					}else{
						$anz = abs($regs[1]);
					}
					$unique = substr(md5(uniqid(rand(),1)),0,min($anz,32));
					$text = preg_replace('/%unique[^%]*%/', $unique, (isset($text) ? $text : ""));
					$select .= $this->htmlSelect("we_".$this->Name."_input[DefaultText_".$zahl."]",$l_object_value,1,"%unique%","",'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');"',"value",140)."&nbsp;";
					$select .= $this->htmlTextInput("we_".$this->Name."_input[unique_".$zahl."]",40,$anz,255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",140);
				}else{
					$select .= $this->htmlSelect("we_".$this->Name."_input[DefaultText_".$zahl."]",$l_object_value,1,"%".$key."%","",'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');"',"value",140)."&nbsp;";
				}
			}else if(preg_match('/^([^%]+)/', $all, $regs)){
				$all = substr($all,strlen($regs[1]));
				$key = $regs[1];
				$select .= $this->htmlSelect("textwert_".$zahl,$l_object_value,1,"Text","",'onChange="_EditorFrame.setEditorIsHot(true); document.we_form.elements[\'we_'.$this->Name.'_input[DefaultText_'.$zahl.']\'].value = this.options[this.selectedIndex].value; we_cmd(\'reload_editpage\');"',"value",140)."&nbsp;";
				$select .= $this->htmlTextInput("we_".$this->Name."_input[DefaultText_".$zahl."]",40,$key,255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",140);
			}

			$select .= "<br>";
			$zahl ++;
		}

		$select .= $this->htmlSelect("we_".$this->Name."_input[DefaultText_".$zahl."]",$l_object_value,1,"","",'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');"',"value",140)."&nbsp;";
		$select .= '<input type = "hidden" name="we_'.$this->Name.'_input[Defaultanzahl]" value="'.$zahl.'" />';


		$var_flip = array_flip($l_object_url);
		$select2 = "";
		if(isset($this->elements["DefaultanzahlUrl"]["dat"])){
			$this->DefaultUrl="";

			for($i=0; $i <= $this->elements["DefaultanzahlUrl"]["dat"];$i++){
				$was = "DefaultUrl_".$i;
				if($this->elements[$was]["dat"]!=""){ //&& in_array($this->elements[$was]["dat"],$var_flip)
					if(stristr($this->elements[$was]["dat"], 'urlunique')){
						$this->elements[$was]["dat"] = "%".str_replace("%","",$this->elements[$was]["dat"]).(( isset($this->elements["urlunique_".$i]["dat"]) && $this->elements["urlunique_".$i]["dat"]>0 )?$this->elements["urlunique_".$i]["dat"]:"16")."%";
					}
					if(stristr($this->elements[$was]["dat"], 'urlfield1')){
						$this->elements[$was]["dat"] = "%".str_replace("%","",$this->elements[$was]["dat"]).(( isset($this->elements["urlfield1_".$i]["dat"]) && $this->elements["urlfield1_".$i]["dat"]>0 )?$this->elements["urlfield1_".$i]["dat"]:"64")."%";
					}
					if(stristr($this->elements[$was]["dat"], 'urlfield2')){
						$this->elements[$was]["dat"] = "%".str_replace("%","",$this->elements[$was]["dat"]).(( isset($this->elements["urlfield2_".$i]["dat"]) && $this->elements["urlfield2_".$i]["dat"]>0 )?$this->elements["urlfield2_".$i]["dat"]:"64")."%";
					}
					if(stristr($this->elements[$was]["dat"], 'urlfield3')){
						$this->elements[$was]["dat"] = "%".str_replace("%","",$this->elements[$was]["dat"]).(( isset($this->elements["urlfield3_".$i]["dat"]) && $this->elements["urlfield3_".$i]["dat"]>0 )?$this->elements["urlfield3_".$i]["dat"]:"64")."%";
					}
					$this->DefaultUrl .= $this->elements[$was]["dat"];
				}
			}

		}

		$all = $this->DefaultUrl;
		$text1=0;
		$zahl=0;

		while(!empty($all)){
			if(preg_match('/^%([^%]+)%/', $all, $regs)){
				$all = substr($all,strlen($regs[1])+2);
				$key = $regs[1];
				if(preg_match('/urlunique([^%]*)/', $key, $regs)){
					if(!$regs[1]){
						$anz = 16;
					}else{
						$anz = abs($regs[1]);
					}
					$unique = substr(md5(uniqid(rand(),1)),0,min($anz,32));
					$text = preg_replace('/%urlunique[^%]*%/', $unique, (isset($text) ? $text : ""));
					$select2 .= $this->htmlSelect("we_".$this->Name."_input[DefaultUrl_".$zahl."]",$l_object_url,1,"%urlunique%","",'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');"',"value",140)."&nbsp;";
					$select2 .= $this->htmlTextInput("we_".$this->Name."_input[urlunique_".$zahl."]",40,$anz,255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",140);
				}else{
					if (preg_match('/urlfield1([^%]*)/', $key, $regs)){
						if(!$regs[1]){
							$anz = 64;
						}else{
							$anz = abs($regs[1]);
						}
						$select2 .= $this->htmlSelect("we_".$this->Name."_input[DefaultUrl_".$zahl."]",$l_object_url,1,"%urlfield1%","",'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');"',"value",140)."&nbsp;";
						$select2 .= $this->htmlTextInput("we_".$this->Name."_input[urlfield1_".$zahl."]",40,$anz,255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",140);
					} elseif(preg_match('/urlfield2([^%]*)/', $key, $regs)){
						if(!$regs[1]){
							$anz = 64;
						}else{
							$anz = abs($regs[1]);
						}
						$select2 .= $this->htmlSelect("we_".$this->Name."_input[DefaultUrl_".$zahl."]",$l_object_url,1,"%urlfield2%","",'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');"',"value",140)."&nbsp;";
						$select2 .= $this->htmlTextInput("we_".$this->Name."_input[urlfield2_".$zahl."]",40,$anz,255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",140);
					} elseif(preg_match('/urlfield3([^%]*)/', $key, $regs)){
						if(!$regs[1]){
							$anz = 64;
						}else{
							$anz = abs($regs[1]);
						}
						$select2 .= $this->htmlSelect("we_".$this->Name."_input[DefaultUrl_".$zahl."]",$l_object_url,1,"%urlfield3%","",'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');"',"value",140)."&nbsp;";
						$select2 .= $this->htmlTextInput("we_".$this->Name."_input[urlfield3_".$zahl."]",40,$anz,255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",140);

					} else {
						$select2 .= $this->htmlSelect("we_".$this->Name."_input[DefaultUrl_".$zahl."]",$l_object_url,1,"%".$key."%","",'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');"',"value",140)."&nbsp;";
					}
				}
			}else if(preg_match('/^([^%]+)/', $all, $regs)){
				$all = substr($all,strlen($regs[1]));
				$key = $regs[1];
				$select2 .= $this->htmlSelect("textwert_".$zahl,$l_object_url,1,"Text","",'onChange="_EditorFrame.setEditorIsHot(true); document.we_form.elements[\'we_'.$this->Name.'_input[DefaultUrl_'.$zahl.']\'].value = this.options[this.selectedIndex].value; we_cmd(\'reload_editpage\');"',"value",140)."&nbsp;";
				$select2 .= $this->htmlTextInput("we_".$this->Name."_input[DefaultUrl_".$zahl."]",40,$key,255,'onChange="_EditorFrame.setEditorIsHot(true);"',"text",140);
			}

			$select2 .= "<br>";
			$zahl ++;
		}

		$select2 .= $this->htmlSelect("we_".$this->Name."_input[DefaultUrl_".$zahl."]",$l_object_url,1,"","",'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');"',"value",140)."&nbsp;";
		$select2 .= '<input type = "hidden" name="we_'.$this->Name.'_input[DefaultanzahlUrl]" value="'.$zahl.'" />';



		$content = '<table border="0" cellpadding="0" cellspacing="0">

	<tr>
		<td colspan="2" class="defaultfont" valign=top>'.$l_object["name"].'</td><td>'.getPixel(20,20).'</td>
	</tr>
	<tr>
		<td colspan="3" >'.$select.'</td>
	</tr>

	<tr>
		<td>'.getPixel(20,16).'</td><td>'.getPixel(20,2).'</td><td>'.getPixel(100,2).'</td>
	</tr>

	<tr>
		<td colspan="2" class="defaultfont" valign=top>'.$l_object["seourl"].'</td><td>'.getPixel(20,20).'</td>
	</tr>
	<tr>
		<td colspan="3" >'.$select2.'</td>
	</tr>
	<tr>
		<td>'.getPixel(20,16).'</td><td>'.getPixel(20,2).'</td><td>'.getPixel(100,2).'</td>
	</tr>
	<tr>
		<td colspan="3" >'.$this->formTriggerDocument(true).'</td>
	</tr>
	<tr>
		<td>'.getPixel(20,16).'</td><td>'.getPixel(20,2).'</td><td>'.getPixel(100,2).'</td>
	</tr>
	<tr>
		<td class="defaultfont" valign=top>'.$l_global["categorys"].'</td><td>'.getPixel(20,20).'</td><td>'.getPixel(100,2).'</td>
	</tr>
	<tr>
		<td colspan="3" >'.$this->formCategory().'</td>
	</tr>
	<tr>
		<td>'.getPixel(20,16).'</td><td>'.getPixel(20,2).'</td><td>'.getPixel(100,2).'</td>
	</tr>
';
		if(defined("BIG_USER_MODULE")){
			$content .= '	<tr>
		<td colspan="3" >'.$this->formRestrictUsers().'</td>
	</tr>
';
			if($this->RestrictUsers){
				$content .= '
	<tr>
		<td>'.getPixel(20,10).'</td><td>'.getPixel(20,2).'</td><td>'.getPixel(100,2).'</td>
	</tr>
	<tr>
		<td colspan="3" >'.$this->formUsers().'</td>
	</tr>
';
			}
		}
		$content .= '</table>
';

		return $content;

	}

	function formRestrictUsers($canChange=true){
		global $l_we_class;

		if($canChange){
			$hiddenname = 'we_'.$this->Name.'_RestrictUsers';
			$tmpname = 'tmpwe_'.$this->Name.'_RestrictUsers';
			$hidden=$this->htmlHidden($hiddenname,abs($this->RestrictUsers));
			$check = we_forms::checkbox("1", $this->RestrictUsers ? true : false, $tmpname, $l_we_class["limitedAccess"], true, "defaultfont", "_EditorFrame.setEditorIsHot(true);this.form.elements['".$hiddenname."'].value=(this.checked ? '1' : '0');we_cmd('reload_editpage');");
			return $hidden.$check;
		}else{
			return '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="'.TREE_IMAGE_DIR.($this->RestrictUsers ? 'check1_disabled.gif' : 'check0_disabled.gif').'" /></td><td class="defaultfont">&nbsp;'.$l_we_class["limitedAccess"].'</td></tr></table>';
		}
	}

	function formPath(){
		global $l_object,$l_we_class;
		$content = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>'.$this->formInputField("","Text",$l_object["classname"],30,508,255,'onChange="_EditorFrame.setEditorIsHot(true);pathOfDocumentChanged();"').'</td><td></td><td></td>
	</tr>
</table>
';
		return $content;
	}

	function formWorkspaces(){
		global $l_we_class,$l_object;

		$we_button = new we_button();

		//remove not existing workspaces - deal with templates as well
		$arr = makeArrayFromCSV($this->Workspaces);
		$newArr = array();

		$_defaultArr = makeArrayFromCSV($this->DefaultWorkspaces);
		$_newDefaultArr = array();

		$_tmplArr = makeArrayFromCSV($this->Templates);
		$_newTmplArr = array();

		//    check if workspace exists - correct templates if neccessary !!
		for($i=0;$i<sizeof($arr);$i++){
		    if(weFileExists($arr[$i])){
			    array_push($newArr,$arr[$i]);
			    if(in_array($arr[$i], $_defaultArr)){
                    array_push($_newDefaultArr,$arr[$i]);
			   	}
			    if(isset($_tmplArr[$i])){
                    array_push($_newTmplArr,$_tmplArr[$i]);
			    } else {
			        array_push($_newTmplArr,'');
			    }
			}
		}

		$this->Workspaces = makeCSVFromArray($newArr,true);
		$this->Templates = makeCSVFromArray($_newTmplArr,true);
		$this->DefaultWorkspaces = makeCSVFromArray($_newDefaultArr,true);

		//javascript:we_cmd('openDirselector','','".FILE_TABLE."','','','opener._EditorFrame.setEditorIsHot(true);fillIDs();opener.we_cmd(\\'add_workspace\\',top.allIDs);','','','',1
		$wecmdenc1= '';
		$wecmdenc2= '';
		$wecmdenc3= we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);fillIDs();opener.we_cmd('add_workspace',top.allIDs);");
		$button = $we_button->create_button("add", "javascript:we_cmd('openDirselector','','".FILE_TABLE."','','','".$wecmdenc3."','','','',1)");

		$addbut = $button;

		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_tools/MultiDirTemplateAndDefaultChooser.inc.php");
		$obj = new MultiDirTemplateAndDefaultChooser(450,$this->Workspaces,"del_workspace",$addbut,get_ws(FILE_TABLE),$this->Templates,"we_".$this->Name."_Templates","",get_ws(TEMPLATES_TABLE),"we_".$this->Name."_DefaultWorkspaces",$this->DefaultWorkspaces);
		$obj->CanDelete=true;
		$obj->create=1;
		$content = $obj->get();

		if (isset($GLOBALS['WE_DEL_WORKSPACE_ERROR']) && $GLOBALS['WE_DEL_WORKSPACE_ERROR']) {
			unset($GLOBALS['WE_DEL_WORKSPACE_ERROR']);
			$content .= '<script type="text/javascript">' . we_message_reporting::getShowMessageCall( addslashes($l_we_class['we_del_workspace_error']), WE_MESSAGE_ERROR ) . '</script>';
		}
		return $content;
	}

	function formWorkspacesFlag(){
		global $l_object;

		$content =

			'<div style="margin-bottom:8px;">'.we_forms::radiobutton(1, $this->WorkspaceFlag == 1, "we_".$this->Name."_WorkspaceFlag",$l_object["behaviour_all"]).'</div><div>' .
			we_forms::radiobutton(0, $this->WorkspaceFlag != 1, "we_".$this->Name."_WorkspaceFlag",$l_object["behaviour_no"]).'</div>';

		return $content;
	}

	function formCSS(){
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_tools/MultiDirChooser.inc.php");
		$we_button = new we_button();
		//javascript:we_cmd('openDocselector', '', '" . FILE_TABLE . "', '', '', 'fillIDs();opener.we_cmd(\\'add_css\\', top.allIDs);', '', '', 'text/css', 1,1)
		$wecmdenc1= '';
		$wecmdenc2= '';
		$wecmdenc3= we_cmd_enc("fillIDs();opener.we_cmd('add_css', top.allIDs);");

		$addbut = $we_button->create_button("add", "javascript:we_cmd('openDocselector', '', '" . FILE_TABLE . "','','','".$wecmdenc3."','','','text/css', 1,1)");
		$css = new MultiDirChooser(510,$this->CSS,"del_css",$addbut,"","Icon,Path", FILE_TABLE);
		return $css->get();
	}

	function formCache() {
		global $l_we_cache;

		$content = '
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="3" class="defaultfont">' . $l_we_cache['cache_type']. '<br />
						' . $this->htmlSelect('we_' . $this->Name . '_CacheType', array('none' => $l_we_cache['cache_type_none'], 'tag' => $l_we_cache['cache_type_wetag'], 'document' => $l_we_cache['cache_type_document'], 'full' => $l_we_cache['cache_type_full']), 1, $this->CacheType, false, "", "value", 508)	 . '</td>
				</tr>
				<tr>
					<td>
						'.getPixel(20,4).'</td>
					<td>
						'.getPixel(20,2).'</td>
					<td>
						'.getPixel(100,2).'</td>
				</tr>
				<tr>
					<td class="defaultfont" colspan="3">' . $l_we_cache['cache_lifetime'] . '</td>
				</tr>
				<tr>
					<td colspan="3">
						' . $this->htmlTextInput('we_' . $this->Name . '_CacheLifeTime', 24, $this->CacheLifeTime, 5, "", "text", 390) .
						$this->htmlSelect("we_tmp_" . $this->Name . "_select[CacheLifeTime]", $l_we_cache['cacheLifeTimes'], 1, $this->CacheLifeTime, false,"onChange=\"_EditorFrame.setEditorIsHot(true);document.forms[0].elements['we_" . $this->Name . "_CacheLifeTime'].value=this.options[this.selectedIndex].value;document.forms[0].elements['we_" . $this->Name . "_CacheLifeTime'].value=this.options[this.selectedIndex].value;\" onBlur=\"_EditorFrame.setEditorIsHot(true);\"","value",118) . '</td>
				</tr>
			</table>';
		return $content;
	}

	function formCopyDocument(){
		$we_button = new we_button();
		$idname = 'we_'.$this->Name.'_CopyID';
		$rootDIrID = 0;
		//javascript:we_cmd('openDocselector',document.forms[0].elements['$idname'].value,'".$this->Table."','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','','opener._EditorFrame.setEditorIsHot(true);opener.top.we_cmd(\\'copyDocument\\',currentID);','".session_id()."','".$rootDIrID."','".$this->ContentType."');
		$wecmdenc1= we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
		$wecmdenc2= '';
		$wecmdenc3= we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.top.we_cmd('copyDocument',currentID);");

		$but = $we_button->create_button("select", "javascript:we_cmd('openDocselector',document.forms[0].elements['$idname'].value,'".$this->Table."','".$wecmdenc1."','','".$wecmdenc3."','".session_id()."','".$rootDIrID."','".$this->ContentType."');");
		$content = $this->htmlHidden($idname,$this->CopyID).$but;
		return $content;
	}

	function copyDoc($id){
		if($id){
			$doc = new we_object();
			$doc->InitByID($id,$this->Table, LOAD_TEMP_DB);
			if($this->ID==0){
				for($i=0;$i<sizeof($this->persistent_slots);$i++){
					eval('$this->'.$this->persistent_slots[$i].'= isset($doc->'.$this->persistent_slots[$i].') ? $doc->'.$this->persistent_slots[$i].' : "";');
				}
				$this->ObjectID=0;
				$this->CreationDate=time();
				$this->CreatorID=$_SESSION["user"]["ID"];
				$this->DefaultInit = true;
				$this->ID=0;
				$this->OldPath="";
				$this->Published=1;
				$this->Text .= "_copy";
				$this->Path=$this->ParentPath.$this->Text;
				$this->OldPath=$this->Path;
			}
			$this->elements = $doc->elements;
			foreach($this->elements as $n=>$e){
				if(strtolower(substr($n, 0, 9))=='wholename') {
					if (isset($this->elements['neuefelder']) && is_array($this->elements['neuefelder'])) {
						$this->elements['neuefelder']['dat'] .= ",".$e['dat'];
					} else {
						$this->elements['neuefelder']['dat'] = ",".$e['dat'];
					}
				}
			}
			$this->EditPageNr=0;
			$this->Category = $doc->Category;
		}
	}

	function changeTempl_ob($nr,$id){
		$arr = makeArrayFromCSV($this->Templates );
		$arr[$nr] = $id;

		$this->Templates = makeCSVFromArray($arr,true);
	}

	function add_workspace($id){
		$workspaces = makeArrayFromCSV($this->Workspaces);
		$ids = makeArrayFromCSV($id);
		foreach($ids as $id){
			if(strlen($id) && (!in_array($id,$workspaces))) {
				array_push($workspaces,$id);
			}
		}
		$this->Workspaces=makeCSVFromArray($workspaces,true);
	}

	function del_workspace($id){

		$this->DB_WE->query("SELECT ID FROM ".OBJECT_FILES_TABLE." WHERE TableID=".$this->ID." AND (Workspaces like ',".abs($id).",' OR ExtraWorkspaces like ',".abs($id).",') LIMIT 0,1");

		if($this->DB_WE->next_record()){
			$GLOBALS['WE_DEL_WORKSPACE_ERROR'] = true;
			return;
		}

		$workspaces = makeArrayFromCSV($this->Workspaces);
		$defaultWorkspaces = makeArrayFromCSV($this->DefaultWorkspaces);
		$Templates = makeArrayFromCSV($this->Templates);
		for($i=0;$i<sizeof($workspaces);$i++){
			if($workspaces[$i] == $id){
				unset($workspaces[$i]);
				if(in_array($id, $defaultWorkspaces)) {
					unset($defaultWorkspaces[array_search($id, $defaultWorkspaces)]);
				}
				unset($Templates[$i]);
				break;
			}
		}
		$tempArr = array();

		foreach($workspaces as $ws){
			array_push($tempArr,$ws);
		}

		$this->Workspaces = makeCSVFromArray($tempArr,true);

		$tempArr = array();

		foreach($defaultWorkspaces as $t){
			array_push($tempArr,$t);
		}

		$this->DefaultWorkspaces = makeCSVFromArray($tempArr,true);

		$tempArr = array();

		foreach($Templates as $t){
			array_push($tempArr,$t);
		}

		$this->Templates = makeCSVFromArray($tempArr,true);
	}

	function we_initSessDat($sessDat){
		//	charset must be in other namespace -> for header !!!
		$this->elements["Charset"]["dat"] = (isset($sessDat["0"]["SerializedArray"]["elements"]["Charset"]) ? $sessDat["0"]["SerializedArray"]["elements"]["Charset"]["dat"] : "");
		we_document::we_initSessDat($sessDat);
		$this->setSort();
	}

	function i_getContentData($loadBinary=0){
		$f=0;

		if($this->ID){
			$this->DB_WE->query("SELECT strOrder,DefaultCategory,DefaultValues,DefaultText,DefaultDesc,DefaultTitle,DefaultUrl,DefaultUrlfield0,DefaultUrlfield1,DefaultUrlfield2,DefaultUrlfield3,DefaultTriggerID,DefaultKeywords,DefaultValues FROM " . OBJECT_TABLE . " WHERE ID = ".$this->ID);

			$this->DB_WE->next_record();

			$this->strOrder = $this->DB_WE->f("strOrder");
			$this->setSort();

			$this->DefaultValues = $this->DB_WE->f("DefaultValues");

			$vals = unserialize($this->DefaultValues);
			$names = (is_array($vals) ? array_keys($vals) : array());

			foreach($names as $name){
				if($name == "WE_CSS_FOR_CLASS"){
					$this->CSS = $vals[$name];
				}
				if(isset($vals[$name])  &&  is_array($vals[$name])){
					$this->elements[$name."count"]["dat"]=(( isset($vals[$name]["meta"]) &&  sizeof($vals[$name]["meta"] )>0 )?(sizeof($vals[$name]["meta"])-1):"0");
					if(isset($vals[$name]["meta"]) && is_array($vals[$name]["meta"])){
						$keynames = array_keys($vals[$name]["meta"]);

						for($ll=0; $ll <= sizeof($vals[$name]["meta"]);$ll++){
							$this->elements[$name."defaultkey".$ll]["dat"] = isset($keynames[$ll]) ? $keynames[$ll] : "";
							$this->elements[$name."defaultvalue".$ll]["dat"] = isset($keynames[$ll]) ? $vals[$name]["meta"][$keynames[$ll]] : "";
						}
					}
				}
			}

			$this->DefaultCategory = $this->DB_WE->f("DefaultCategory");
			$this->Category = $this->DefaultCategory;
			$this->SerializedArray = unserialize($this->DB_WE->f("DefaultValues"));

			//	charset must be in other namespace -> for header !!!
			$this->elements["Charset"]["dat"] = (isset($this->SerializedArray["elements"]["Charset"]["dat"]) ? $this->SerializedArray["elements"]["Charset"]["dat"] : "");

			$this->WorkspaceFlag = isset($this->SerializedArray["WorkspaceFlag"]) ? $this->SerializedArray["WorkspaceFlag"] : "";
			$this->elements["title"]["dat"]=$this->DB_WE->f("DefaultTitle");
			$this->elements["desc"]["dat"]=$this->DB_WE->f("DefaultDesc");
			$this->elements["keywords"]["dat"]=$this->DB_WE->f("DefaultKeywords");

			$this->DefaultText = $this->DB_WE->f("DefaultText");
			$this->DefaultUrl = $this->DB_WE->f("DefaultUrl");

			$this->elements["urlfield0"]["dat"]=$this->DB_WE->f("DefaultUrlfield0");
			$this->elements["urlfield1"]["dat"]=$this->DB_WE->f("DefaultUrlfield1");
			$this->elements["urlfield2"]["dat"]=$this->DB_WE->f("DefaultUrlfield2");
			$this->elements["urlfield3"]["dat"]=$this->DB_WE->f("DefaultUrlfield3");
			$this->elements["triggerid"]["dat"]=$this->DB_WE->f("DefaultTriggerID");
			$this->DefaultTriggerID=$this->DB_WE->f("DefaultTriggerID");

			$ctable = OBJECT_X_TABLE . $this->ID;
			$tableInfo = $this->DB_WE->metadata($ctable);
			$fields = array();
			foreach($tableInfo as $info){
				if(preg_match('/(.+?)_(.*)/',$info["name"],$regs)){
					if($regs[1]!="OF" && $regs[1]!="variant"){
						$type = $regs[1];
						$name = $regs[2];

						//$fields[$sort[$f]] = array("name"=>$regs[2],"type"=>$regs[1],"length"=>$info["len"]); war bis fix zu 4123 auskommentiert, k�nnte man wieder rein nehmen
						$this->elements[$info["name"]]["dat"] = $name;
						$this->elements["wholename".$this->getSortIndexByValue($f)]["dat"] = $info["name"];
						$this->elements[$info["name"]."length"]["dat"] = $info["len"];
						$this->elements[$info["name"]."dtype"]["dat"] = $type;
						$this->elements[$info["name"]."max"]["dat"] = isset($vals[$info["name"]]["max"]) ? $vals[$info["name"]]["max"] : "";
						$this->elements[$info["name"]."default"]["dat"] = isset($vals[$info["name"]]["default"]) ? $vals[$info["name"]]["default"] : "";
						$this->elements[$info["name"]."defaultThumb"]["dat"] = isset($vals[$info["name"]]["defaultThumb"]) ? $vals[$info["name"]]["defaultThumb"] : "";
						$this->elements[$info["name"]."autobr"]["dat"] = isset($vals[$info["name"]]["autobr"]) ? $vals[$info["name"]]["autobr"] : "";
						$this->elements[$info["name"]."rootdir"]["dat"] = isset($vals[$info["name"]]["rootdir"]) ? $vals[$info["name"]]["rootdir"] : "";
						$this->elements[$info["name"]."defaultdir"]["dat"] = isset($vals[$info["name"]]["defaultdir"]) ? $vals[$info["name"]]["defaultdir"] : "";
						$this->elements[$info["name"]."dhtmledit"]["dat"] = isset($vals[$info["name"]]["dhtmledit"]) ? $vals[$info["name"]]["dhtmledit"] : "off";
						$this->elements[$info["name"]."showmenus"]["dat"] = isset($vals[$info["name"]]["showmenus"]) ? $vals[$info["name"]]["showmenus"] : "off";
						$this->elements[$info["name"]."commands"]["dat"] = isset($vals[$info["name"]]["commands"]) ? $vals[$info["name"]]["commands"] : "";
						$this->elements[$info["name"]."height"]["dat"] = isset($vals[$info["name"]]["height"]) ? $vals[$info["name"]]["height"] : 200;
						$this->elements[$info["name"]."width"]["dat"] = isset($vals[$info["name"]]["width"]) ? $vals[$info["name"]]["width"] : 618;
						$this->elements[$info["name"]."class"]["dat"] = isset($vals[$info["name"]]["class"]) ? $vals[$info["name"]]["class"] : "";
						$this->elements[$info["name"]."cssClasses"]["dat"] = isset($vals[$info["name"]]["cssClasses"]) ? $vals[$info["name"]]["cssClasses"] : "";
						$this->elements[$info["name"]."xml"]["dat"] = isset($vals[$info["name"]]["xml"]) ? $vals[$info["name"]]["xml"] : "";
						$this->elements[$info["name"]."removefirstparagraph"]["dat"] = isset($vals[$info["name"]]["removefirstparagraph"]) ? $vals[$info["name"]]["removefirstparagraph"] : "";
						$this->elements[$info["name"]."forbidhtml"]["dat"] =  isset($vals[$info["name"]]["forbidhtml"]) ? $vals[$info["name"]]["forbidhtml"] : "";
						$this->elements[$info["name"]."forbidphp"]["dat"] =   isset($vals[$info["name"]]["forbidphp"]) ? $vals[$info["name"]]["forbidphp"] : "";
						$this->elements[$info["name"]."inlineedit"]["dat"] = isset($vals[$info["name"]]["inlineedit"]) ? $vals[$info["name"]]["inlineedit"] : "";
						$this->elements[$info["name"]."users"]["dat"] =  isset($vals[$info["name"]]["users"]) ? $vals[$info["name"]]["users"] : "";
						$this->elements[$info["name"]."required"]["dat"] =  isset($vals[$info["name"]]["required"]) ? $vals[$info["name"]]["required"] : "";
						$this->elements[$info["name"]."editdescription"]["dat"] =  isset($vals[$info["name"]]["editdescription"]) ? $vals[$info["name"]]["editdescription"] : "";
						$this->elements[$info["name"]."int"]["dat"] = isset($vals[$info["name"]]["int"]) ? $vals[$info["name"]]["int"] : "";
						$this->elements[$info["name"]."intID"]["dat"] = isset($vals[$info["name"]]["intID"]) ? $vals[$info["name"]]["intID"] : "";
						$this->elements[$info["name"]."hreftype"]["dat"] = isset($vals[$info["name"]]["hreftype"]) ? $vals[$info["name"]]["hreftype"] : "";
						$this->elements[$info["name"]."hreffile"]["dat"] = isset($vals[$info["name"]]["hreffile"]) ? $vals[$info["name"]]["hreffile"] : "true";
						$this->elements[$info["name"]."hrefdirectory"]["dat"] = isset($vals[$info["name"]]["hrefdirectory"]) ? $vals[$info["name"]]["hrefdirectory"] : "false";
						$this->elements[$info["name"]."intPath"]["dat"] = isset($vals[$info["name"]]["intPath"]) ? $vals[$info["name"]]["intPath"] : "";
						if(isset($vals[$info["name"]]["variant"])) $this->elements[$info["name"]."variant"]["dat"] =  $vals[$info["name"]]["variant"];

						//if($regs[1] != "meta" && 1==2){

						$f++;

						//}
					}
				}
			}
			$this->elements["Sortgesamt"]["dat"] = ($f-1);
		}

	}

	function i_set_PersistentSlot($name,$value){
		if(in_array($name,$this->persistent_slots)){
			eval('$this->'.$name.'=$value;');
		}else{
			if($name == "Templates_0"){
				$this->Templates="";
				for($i=0;$i<sizeof(makeArrayFromCSV($this->Workspaces));$i++){
					$this->Templates .= $_REQUEST["we_".$this->Name."_Templates_".$i].",";
				}
				if($this->Templates) $this->Templates = ",".$this->Templates;
				$this->DefaultWorkspaces="";
				$wsp = makeArrayFromCSV($this->Workspaces);
				for($i=0;$i<sizeof($wsp);$i++){
					if(isset($_REQUEST["we_".$this->Name."_DefaultWorkspaces_".$i])) {
						$this->DefaultWorkspaces .= $wsp[$i].",";
					}
				}
				if($this->DefaultWorkspaces) $this->DefaultWorkspaces = ",".$this->DefaultWorkspaces;
			}
		}
	}

	function i_setText(){
		// do nothing here!
	}

	function i_filenameEmpty(){
		return ($this->Text == "") ? true : false;
	}

	function i_filenameNotValid(){
		$defTextValid = false;
		foreach($this->elements as $k=>$v) {
			if(is_string($k) && substr($k, 0, 12)=='DefaultText_') {
				$end = substr($k, 12, strlen($k));
				if(isset($_REQUEST['textwert_'.$end])) {
					if(isset($v['dat']) && $v['dat']!='') {
						if(preg_match('/[^\w\-.]/', $v['dat'])) {
							$defTextValid = true;
							break;
						}
					}
				}
			}
		}
		return (preg_match('/[^\w\-.]/', $this->Text) || $defTextValid);
	}

	function i_filenameNotAllowed(){
		return false;
	}

	function i_filenameDouble(){
		return f("SELECT ID FROM ".$this->Table." WHERE ParentID='".$this->ParentID."' AND Text='".escape_sql_query($this->Text)."' AND ID != '".$this->ID."'","ID",new DB_WE());
	}

	function i_checkPathDiffAndCreate(){
		return true;
	}

	function i_hasDoubbleFieldNames(){
		$sort = $this->getElement("we_sort");
		$count = $this->getElement("Sortgesamt");
		$usedNames = array();
		if(is_array($sort)){
			for($i=0;$i <= $count && !empty($sort);$i++){
				$foo = $this->getElement($this->getElement("wholename".$this->getSortIndex($i)),"dat");
				if(!in_array($foo,$usedNames)){
					array_push($usedNames,$foo);
				}else{
					return $foo;
				}
			}
		}
		return false;
	}
	function includedObjectHasDoubbleFieldNames($incClass){
		$sort = $this->getElement("we_sort");
		$count = $this->getElement("Sortgesamt");
		$usedNames = array();
		$doubleNames = array();
		if(is_array($sort)){
			for($i=0;$i <= $count && !empty($sort);$i++){
				$foo = $this->getElement($this->getElement("wholename".$this->getSortIndex($i)),"dat");
				array_push($usedNames,$foo);
			}
		}
		$incclassobj = new we_object();
		$incclassobj->initByID($incClass,$this->Table);
		$isort = $incclassobj->getElement("we_sort");
		$icount = $incclassobj->getElement("Sortgesamt");
		if(is_array($isort)&& !empty($isort)){
			for($i=0;$i <= $icount;$i++){
				$foo = $incclassobj->getElement($incclassobj->getElement("wholename".$incclassobj->getSortIndex($i)),"dat");
				if(in_array($foo,$usedNames)){
					array_push($doubleNames,$foo);
				}
			}
		}
		if (empty($doubleNames)) {
			return false;
		} else {
			return $doubleNames;
		}

	}

	function i_writeDocument(){
		return true; // we don't have to write!
	}

	function i_setElementsFromHTTP(){
		parent::i_setElementsFromHTTP();
		$hrefFields = false;


		foreach($_REQUEST as $n=>$v){

			if(preg_match('/^we_'.preg_quote($this->Name).'_([^\[]+)$/', $n, $regs)){
				if($regs[1]=="href"){
					$hrefFields = true;
					break;
				}
			}
		}
		if($hrefFields){

			$this->resetElements();
			$hrefs = array();
			while(list($k,$v) = $this->nextElement('href')){

				$realName = preg_replace('/^(.+)_we_jkhdsf_.+$/', '\1', $k);
				$key = preg_replace('/^.+_we_jkhdsf_(.+)$/', '\1', $k);
				if(!isset($hrefs[$realName])) $hrefs[$realName] = array();
				$hrefs[$realName][$key] = $v["dat"];
			}
			foreach($hrefs as $k=>$v){
				$this->setElement($k,serialize($v));
			}

		}
	}

	function we_save($resave=0,$skipHook=0){

		// Check if the cachetype was changed and delete all
		// cachefiles of the documents based on this template
		$this->DB_WE->query("SELECT CacheType FROM ".OBJECT_TABLE." WHERE ID = '".$this->ID."'");
		$OldCacheType = "";
		while($this->DB_WE->next_record()) {
			$OldCacheType = $this->DB_WE->f('CacheType');
		}
		if($OldCacheType != "" && $OldCacheType != "none" && $OldCacheType != $this->CacheType) {
			$this->DB_WE->query("SELECT ID FROM ".OBJECT_FILES_TABLE." WHERE TableID='".$this->ID."'");
			while($this->DB_WE->next_record()) {
				$cacheDir = weCacheHelper::getObjectCacheDir($this->DB_WE->f('ID'));
				weCacheHelper::clearCache($cacheDir);
			}
		}
		$this->save();
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/we_history.class.php");
		we_history::insertIntoHistory($this);

		/* hook */
		if ($skipHook==0){
			$hook = new weHook('save', '', array($this,'resave'=>$resave));
			$ret=$hook->executeHook();
			//check if doc should be saved
			if($ret===false){
				$this->errMsg=$hook->getErrorString();
				return false;
			}
		}
		return true;
	}

	/**
	 * @return	if the field can have variants the function returns true otherwise false
	 * @param	$field - the name of the field
	 */
	function isVariantField($field){
		$types = array('input','link','text','img','int','float','meta');
		$type = ($this->getElement($field.'dtype','dat') !='') ? $this->getElement($field.'dtype','dat') : '';
		return in_array($type,$types);
	}

	/**
	 * @return	the function returns the number of variant fields
	 */
	function hasVariantFields(){
		$fields = $this->getVariantFields();
		return count($fields);
	}

	/**
	 * if document can have variants the function returns true, otherwise false
	 *
	 * if paramter checkField is true, this function checks also, if there are
	 * already fields selected for the variants.
	 *
	 * @param boolean $checkFields
	 * @return boolean
	 */
	function canHaveVariants($checkFields = false){
		if(!defined('SHOP_TABLE')) {
			return false;
		}
		$fields = $this->getAllVariantFields();
		$fieldnamesarr = array_keys($fields);
		$fieldnames = implode(',',$fieldnamesarr).',';
		return stristr($fieldnames, '_shoptitle,') && stristr($fieldnames, '_shopdescription,');
	}

	/**
	 * @desc 	the function returns the array with all object field names
	 * @return	array with the filed names and attributes
	 */
	function getAllVariantFields(){
		$return = array();
		$fields = unserialize($this->DefaultValues);
		if(is_array($fields)){
			foreach ($fields as $name=>$field){
				if($this->isVariantField($name)){
					$return[$name] = $field;
				}
			}
		}
		return $return;

	}

	/**
	 * @return	array with the filed names and attributes
	 * @param	none
	 */
	function getVariantFields(){
		$return = array();
		$fields = $this->getAllVariantFields();
		foreach ($fields as $name=>$field){
			if(isset($field['variant']) && $field['variant']==1){
				$return[$name] =  $field;
			}
		}
		return $return;
	}

	/* creates the DirectoryChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */
	function formDirChooser($width="" ,$rootDirID=0, $table="", $Pathname="ParentPath", $IDName="ParentID", $cmd="", $pathID=0, $identifier=""){
		global $l_we_class;

		$we_button = new we_button();

		$path = id_to_path($pathID);

		if(!$table) $table = $this->Table;
		$textname = 'we_'.$this->Name.'_'.$Pathname . ($identifier!="" ?  "_".$identifier : "");
		$idname = 'we_'.$this->Name.'_'.$IDName;
		//javascript:we_cmd('openDirselector',document.we_form.elements['$idname'].value,'$table','document.we_form.elements[\\'$idname\\'].value','document.we_form.elements[\\'$textname\\'].value','opener._EditorFrame.setEditorIsHot(true);opener.pathOfDocumentChanged();".$cmd."','".session_id()."','$rootDirID')
		$wecmdenc1= we_cmd_enc("document.we_form.elements['$idname'].value");
		$wecmdenc2= we_cmd_enc("document.we_form.elements['$textname'].value");
		$wecmdenc3= we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.pathOfDocumentChanged();".str_replace('\\','',$cmd)."");
		$button = $we_button->create_button("select", "javascript:we_cmd('openDirselector',document.we_form.elements['$idname'].value,'$table','".$wecmdenc1."','".$wecmdenc2."','".$wecmdenc3."','".session_id()."','$rootDirID')");
		return $this->htmlFormElementTable($this->htmlTextInput($textname,30,$path,"",' readonly',"text",$width,0),
			"",
			"left",
			"defaultfont",
			$this->htmlHidden($idname,$pathID),
			getPixel(20,4),
			$button);
	}


	function userHasAccess(){

		if($this->isLockedByUser() != 0 && $this->isLockedByUser() != $_SESSION["user"]["ID"] && $GLOBALS["we_doc"]->ID){				// file is locked
			return -3;
		}

		if(!$this->userHasPerms()){					//	File is restricted !!!!!
			return -2;
		}

		if(!$this->userCanSave()){					//	user has no right to save.
			return -4;
		}

		if($this->RestrictUsers && !(we_isOwner($this->CreatorID) || we_isOwner($this->Users))){			//	user is creator of doc - all is allowed.
			return -1;
		}

		if($this->userHasPerms()) {									//	access to doc is not restricted, check workspaces of user
			if($GLOBALS["we_doc"]->ID) {		//	userModule installed
				$ws = get_ws($GLOBALS["we_doc"]->Table);
				if($ws) {		//	doc has workspaces
					if(!(in_workspace($GLOBALS["we_doc"]->ID,$ws,$GLOBALS["we_doc"]->Table,$GLOBALS["DB_WE"]))) {
						return -1;
					}
				}
			}
			return 1;
		}
	}


   function userCanSave(){
   		if(we_hasPerm('ADMINISTRATOR')) {
   			return true;
   		}
		$ownersReadOnly = $this->UsersReadOnly ? unserialize($this->UsersReadOnly) : array();
		$readers=array();
		foreach(array_keys($ownersReadOnly) as $key){
			if(isset($ownersReadOnly[$key]) && $ownersReadOnly[$key] == 1) $readers[]=$key;
		}
      	return parent::userCanSave() && !isUserInUsers($_SESSION["user"]["ID"],$readers);
   }

}
