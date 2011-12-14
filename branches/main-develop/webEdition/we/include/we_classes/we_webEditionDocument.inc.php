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

include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_inc_min.inc.php");

class we_webEditionDocument extends we_textContentDocument {

	// Name of the class => important for reconstructing the class from outside the class
	var $ClassName=__CLASS__;

	var $EditPageNrs = array(WE_EDITPAGE_PROPERTIES,WE_EDITPAGE_CONTENT,WE_EDITPAGE_INFO,WE_EDITPAGE_PREVIEW,WE_EDITPAGE_VALIDATION);

	// ID of the templates that is used from the document
	var $TemplateID=0;

	// ID of the template that is used from the parked document (Bug Fix #6615)
	var $temp_template_id=0;

	// Categories of the parked document (Bug Fix #6615)
	var $temp_category="";

	// Doc-Type of the parked document (Bug Fix #6615)
	var $temp_doc_type="";

	// Path from the template
	var $TemplatePath = "";
	var $Icon = "we_dokument.gif";
	var $Table = FILE_TABLE;
	var $ContentType="text/webedition";

	// Only needed for output
/*	var $CacheType = "none";
	var $CacheLifeTime = 0;
*/
	var $hasVariants=null;

	/**
	 * @var weDocumentCustomerFilter
	 */
	var $documentCustomerFilter = ""; // DON'T SET TO NULL !!!!


	function we_webEditionDocument() {
		if(defined("SHOP_TABLE")) {
			array_push($this->EditPageNrs, WE_EDITPAGE_VARIANTS);
		}

		if (defined("CUSTOMER_TABLE")) {
			array_push($this->EditPageNrs, WE_EDITPAGE_WEBUSER);
		}

		$this->we_textContentDocument();
		if(isset($_SESSION["prefs"]["DefaultTemplateID"])){
			$this->TemplateID = $_SESSION["prefs"]["DefaultTemplateID"];
		}
		array_push($this->persistent_slots,"TemplateID","TemplatePath","hidePages","controlElement","temp_template_id","temp_doc_type","temp_category");
	}

	function makeSameNew() {
		$TemplateID = $this->TemplateID;
		$TemplatePath = $this->TemplatePath;
		$IsDynamic = $this->IsDynamic;
		$IsDynamic = $this->IsDynamic;
		we_textContentDocument::makeSameNew();
		$this->TemplateID = $TemplateID;
		$this->TemplatePath = $TemplatePath;
		$this->IsDynamic = $IsDynamic;
	}

	function wait($usecs) {
		$temp=gettimeofday();
		$start=(int)$temp["usec"];
		while(1) {
			$temp=gettimeofday();
			$stop=(int)$temp["usec"];
			if ($stop-$start >= $usecs)
				break;
		}
	}

	function editor($baseHref=true) {
		$port = (defined("HTTP_PORT")) ? (":".HTTP_PORT) : "";
		$prot = getServerProtocol();
		$GLOBALS["we_baseHref"] = $baseHref ? getServerUrl().$this->Path : "";
		switch($this->EditPageNr) {
			case WE_EDITPAGE_PROPERTIES:
				return "we_templates/we_editor_properties.inc.php";
			case WE_EDITPAGE_INFO:
				$GLOBALS["WE_MAIN_DOC"]->InWebEdition=true;//Bug 3417
				return "we_templates/we_editor_info.inc.php";

			case WE_EDITPAGE_CONTENT:
				$GLOBALS["we_editmode"] = true;
				break;
			case WE_EDITPAGE_PREVIEW:
				$GLOBALS["we_editmode"] = false;
				break;
            case WE_EDITPAGE_VALIDATION:
				return "we_templates/validateDocument.inc.php";
				break;
			case WE_EDITPAGE_VARIANTS:
				return 'we_templates/we_editor_variants.inc.php';
				break;
			case WE_EDITPAGE_WEBUSER:
				return "we_modules/customer/editor_weDocumentCustomerFilter.inc.php";
			break;
			default:
				return parent::editor($baseHref);
		}
		return preg_replace('/.tmpl$/i','.php', $this->TemplatePath); // .tmpl mod
	}

	/*
	* Form functions for generating the html of the input fields
	*/

	function formIsDynamic($leftwidth=100,$disabled=false) {
		$n = "";
		$out = "";
		if (!$disabled) {
			$isDyn = $this->IsDynamic ? 1 : 0;
			$n = "we_".$this->Name."_IsDynamic";
			$v = $this->IsDynamic;
			$out="\nfunction switchExt() {\n";
			if(!$this->Published) {
				$out.='var a=document.we_form.elements;'."\n";
				if($this->ID)
					$out.='if(confirm("'.g_l('weClass',"[confirm_ext_change]").'")){'."\n";
				$DefaultDynamicExt = (defined("DEFAULT_DYNAMIC_EXT") ? DEFAULT_DYNAMIC_EXT : ".php");
				$DefaultStaticExt = (defined("DEFAULT_STATIC_EXT") ? DEFAULT_STATIC_EXT : ".html");
				$out.='if(a["we_'.$this->Name.'_IsDynamic"].value==1) var changeto="'.$DefaultDynamicExt.'"; else var changeto="'.$DefaultStaticExt.'";'."\n";
				$out .= 'a["we_'.$this->Name.'_Extension"].value=changeto;'."\n";
				if($this->ID)
					$out.='}'."\n";
			}
			$out.="}\n";
			$out="\n".'<script  type="text/javascript">'.$out.'</script>'."\n";
			return we_forms::checkboxWithHidden($v ? true : false, $n, g_l('weClass',"[IsDynamic]"),false,"defaultfont","_EditorFrame.setEditorIsHot(true);switchExt();").$out;
		} else {
			$v = $this->IsDynamic;
			return we_forms::checkboxWithHidden($v ? true : false, $n, g_l('weClass',"[IsDynamic]"),false,"defaultfont","",true).$out;
		}
	}

	function formDocTypeTempl() {
		if (we_hasPerm('EDIT_DOCEXTENSION')){
			$disable = (($this->ContentType == "text/html" || $this->ContentType == "text/webedition") && $this->Published);
		} else $disable = true;

		$content = '
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="3" class="defaultfont" align="left">
						'.$this->formDocType2(388,($this->Published>0)).'</td>
				</tr>
				<tr>
					<td>
						'.we_html_tools::getPixel(20,4).'</td>
					<td>
						'.we_html_tools::getPixel(20,2).'</td>
					<td>
						'.we_html_tools::getPixel(100,2).'</td>
				</tr>
				<tr>
					<td colspan="3" class="defaultfont" align="left">
						'.$this->formTemplatePopup(388,($this->Published>0)).'</td>
				</tr>
				<tr>
					<td>
						'.we_html_tools::getPixel(20,4).'</td>
					<td>
						'.we_html_tools::getPixel(20,2).'</td>
					<td>
						'.we_html_tools::getPixel(100,2).'</td>
				</tr>
				<tr>
					<td colspan="3">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									'.$this->formIsDynamic(100,$disable).'</td>
								<td class="defaultfont">
									&nbsp;</td>
								<td>
									'.$this->formIsSearchable().'</td>
							</tr>
							<tr>
								<td>
									'.$this->formInGlossar(100).'</td>
							</tr>
						</table></td>
				</tr>';
		$content .= '</table>';
		return $content;
	}

	function formTemplateWindow() {
		$yuiSuggest =& weSuggest::getInstance();
		$table = TEMPLATES_TABLE;
		$textname = 'we_'.$this->Name.'_TemplateName';
		$idname = 'we_'.$this->Name.'_TemplateID';
		$ueberschrift=g_l('weClass',"[template]");
		if(we_hasPerm("CAN_SEE_TEMPLATES") && $_SESSION["we_mode"] != "seem") {
			$ueberschriftLink='<a href="javascript:goTemplate(document.we_form.elements[\''.$idname.'\'].value)">'.g_l('weClass',"[template]").'</a>';
		} else {
			$ueberschriftLink = $ueberschrift;
		}
		if($this->TemplateID > 0) {
			$styleTemplateLabel = "display:none";
			$styleTemplateLabelLink = "display:inline";
		}
		else {
			$styleTemplateLabel = "display:inline";
			$styleTemplateLabelLink = "display:none";
		}
		$myid = $this->TemplateID ? $this->TemplateID : "";
		$path = f("SELECT Path FROM ".$this->DB_WE->escape($table)." WHERE ID='".abs($myid)."'","Path",$this->DB_WE);
		//javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','document.we_form.elements[\\'$idname\\'].value','document.we_form.elements[\\'$textname\\'].value','opener._EditorFrame.setEditorIsHot(true);;opener.top.we_cmd(\'reload_editpage\');','".session_id()."','','text/weTmpl',1)");
		$wecmdenc1= we_cmd_enc("document.we_form.elements['$idname'].value");
		$wecmdenc2= we_cmd_enc("document.we_form.elements['$textname'].value");
		$wecmdenc3= we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.top.we_cmd('reload_editpage');");

		$button = we_button::create_button("select", "javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','".$wecmdenc1."','".$wecmdenc2."','".$wecmdenc3."','".session_id()."','','text/weTmpl',1)");
		$yuiSuggest->setAcId("Template");
		$yuiSuggest->setContentType("folder,text/weTmpl");
		$yuiSuggest->setInput($textname,$path);
		$yuiSuggest->setLabel("<span id='TemplateLabel' style='".$styleTemplateLabel."'>".$ueberschrift."</span><span id='TemplateLabelLink' style='".$styleTemplateLabelLink."'>".$ueberschriftLink."</span>");
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname,$myid);
		$yuiSuggest->setSelector("Docselector");
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth(388);
		$yuiSuggest->setSelectButton($button);
		//$yuiSuggest->setDoOnTextfieldBlur("if(document.getElementById('yuiAcResultTemplate').value == '' || document.getElementById('yuiAcResultTemplate').value == 0) { document.getElementById('TemplateLabel').style.display = 'inline'; document.getElementById('TemplateLabelLink').style.display = 'none'; } else { document.getElementById('TemplateLabel').style.display = 'none'; document.getElementById('TemplateLabelLink').style.display = 'inline'; }");
		$yuiSuggest->setDoOnTextfieldBlur("if(yuiAcFields[yuiAcFieldsById['yuiAcInputTemplate'].set].changed && YAHOO.autocoml.isValidById('yuiAcInputTemplate')) top.we_cmd('reload_editpage')");

		return $yuiSuggest->getHTML();
	}

	// creates the Template PopupMenue
	function formTemplatePopup($leftsize, $disable) {
		if($disable){
			$myid = intval($this->TemplateID ? $this->TemplateID : 0);
			$path = ($myid ? f('SELECT Path FROM '.TEMPLATES_TABLE.' WHERE ID='.$myid,'Path',$this->DB_WE):'');

			if(we_hasPerm("CAN_SEE_TEMPLATES") && $_SESSION ["we_mode"] == "normal") {
				$ueberschrift='<a href="javascript:goTemplate('.$myid.')">'.$l_we_class["template"].'</a>';
			}else {
				$ueberschrift=$l_we_class["template"];
			}

			return $ueberschrift.'<br/>'.$path;
		}

		if($this->DocType) {
			$sql = "SELECT Templates FROM ".DOC_TYPES_TABLE." WHERE ID = ".abs($this->DocType)."";
			$this->DB_WE->query($sql);
			$templateFromDoctype = false;
			while($this->DB_WE->next_record()) {
				$templateFromDoctype = $this->DB_WE->f("Templates");
			}

			// if a Doctype is set and this Doctype has defined some templates, just show a select box
			if($templateFromDoctype) {
				return $this->xformTemplatePopup(388);
			} else {
				return $this->formTemplateWindow();
			}
		} else {
			return $this->formTemplateWindow();
		}
	}


	function xformTemplatePopup($width=50) {
		$ws = get_ws(TEMPLATES_TABLE);

		$fieldname = 'we_'.$this->Name.'_TemplateID';

		$Templates="";
		$foo = getHash("SELECT TemplateID,Templates FROM " . DOC_TYPES_TABLE . " WHERE ID ='".abs($this->DocType)."'",$this->DB_WE);
		$TID=$foo["TemplateID"];
		$Templates=$foo["Templates"];
		$tlist="";
		if($TID!="")
			$tlist=$TID;
		if($Templates!="")
			$tlist.=",".$Templates;
		if($tlist) {
			$temps=explode(",",$tlist);
			if(in_array($this->TemplateID,$temps))
				$TID=$this->TemplateID;
			$tlist=implode(",",array_unique($temps));
		}
		else {
			$foo = array();
			$wsArray= makeArrayFromCSV($ws);
			foreach($wsArray as $wid) {
				pushChilds($foo,$wid,TEMPLATES_TABLE,"0");
			}
			$tlist=makeCSVFromArray($foo);
		}
		if($this->TemplateID) {
			$tlist = $tlist ? ($tlist .= ",".$this->TemplateID) : $this->TemplateID;
			//if($TID == "")
				$TID=$this->TemplateID;
		}
		if(we_hasPerm("CAN_SEE_TEMPLATES") && $_SESSION ["we_mode"] == "normal") {
			$ueberschrift='<a href="javascript:goTemplate(document.we_form.elements[\''.$fieldname.'\'].options[document.we_form.elements[\''.$fieldname.'\'].selectedIndex].value)">'.g_l('weClass',"[template]").'</a>';
		}
		else {
			$ueberschrift=g_l('weClass',"[template]");
		}
		if($tlist!="") {
			$foo = array();
			$arr= makeArrayFromCSV($tlist);
			foreach($arr as $tid) {
				if(($tid ==$this->TemplateID) || in_workspace($tid,$ws,TEMPLATES_TABLE)) {
					array_push($foo,$tid);
				}
			}
			$tlist=makeCSVFromArray($foo);
			$tlist = $tlist ? $tlist : -1;
			return $this->formSelect4("",$width,"TemplateID",TEMPLATES_TABLE,"ID","Path",$ueberschrift," WHERE ID IN ($tlist) AND IsFolder=0 ORDER BY Path",1,$TID,false,"we_cmd('template_changed');_EditorFrame.setEditorIsHot(true);","","left","defaultfont","","",array(0,""));
		}
		else {
			return $this->formSelect2("",$width,"TemplateID",TEMPLATES_TABLE,"ID","Path",$ueberschrift,"WHERE IsFolder=0 ORDER BY Path ",1,$this->TemplateID,false,"_EditorFrame.setEditorIsHot(true);");
		}
	}

	/**
	* @return string
	* @desc Returns the metainfos for the selected file.
 	*/
	function formMetaInfos() {
		//	Collect data from meta-tags
		//debug2($this);

        $_code = $this->getTemplateCode();
		$_tp = new we_tagParser();

		$_tags = we_tagParser::getMetaTags($_code);

		for($j = 0; $j < sizeof($_tags); $j++){	//	now parse these tags for property-page.
			if($_tags[$j][1]){
				$_tp->parseSpecificTags($_tags[$j][0], $_tags[$j][1]);
				eval("?>" . $_tags[$j][1]);
			}
		}

		//	if a meta-tag is set all information are in array $GLOBALS["meta"]
		$content = '
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2">
						'.$this->formInputField("txt","Title",g_l('weClass',"[Title]"),40,508,"","onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
				</tr>
				<tr>
					<td>
						'.we_html_tools::getPixel(2,4).'</td>
				</tr>
				<tr>
					<td colspan="2">
						'.$this->formInputField("txt","Description",g_l('weClass',"[Description]"),40,508,"","onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
				</tr>
				<tr>
					<td>
						'.we_html_tools::getPixel(2,4).'</td>
				</tr>
				<tr>
					<td colspan="2">
						'.$this->formInputField("txt","Keywords",g_l('weClass',"[Keywords]"),40,508,"","onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
				</tr>';

				$content .= $this->getCharsetSelect();

				$content .= $this->formLanguage(true);

				$content .= '</table>';

		return $content;
	}

	/**
	 * This function returns the selector of the charset.
	 * @return string
	 */
	function getCharsetSelect(){

		$_charsetHandler = new charsetHandler();

		if(isset($GLOBALS["meta"]["Charset"])){		//	charset-tag available

		 	$name = "Charset";

		 	//	This is the input field for the charset
		 	$inputName = "we_".$this->Name."_txt[$name]";

		 	$chars = explode(",", $GLOBALS["meta"]["Charset"]["defined"]);

		 	//	input field - check value
		 	if( $this->getElement($name) != "" ){
		 		$value = $this->getElement($name);
		 	} else if(isset($GLOBALS["meta"][$name])){
		 		$value = $GLOBALS["meta"][$name]["default"];
		 	} else {
		 		$value = "";
		 	}

		 	$retInput = $this->htmlTextInput($inputName, 40, $value, "", " readonly ", "text", 254);



			//	menu for all possible charsets

		 	$_defaultInChars = false;
		 	for($i = 0; $i < sizeof($chars); $i++){	//	check if default value is already in array

		 		if(strtolower($chars[$i]) == strtolower($GLOBALS["meta"]["Charset"]["default"]) ){
		 			$_defaultInChars = true;
		 		}
		 	}
		 	if(!$_defaultInChars){
		 		array_push($chars, $GLOBALS["meta"]["Charset"]["default"]);
		 	}




		 	$chars = $_charsetHandler->getCharsetsByArray($chars);

		 	//	Last step: get Information about the charsets
		 	$retSelect = $this->htmlSelect("we_tmp_" . $name, $chars, 1, $value, false, " onblur=_EditorFrame.setEditorIsHot(true);document.forms[0].elements['" . $inputName. "'].value=this.options[this.selectedIndex].value; onchange=\"_EditorFrame.setEditorIsHot(true);document.forms[0].elements['" . $inputName. "'].value=this.options[this.selectedIndex].value;\"", "value", "254");

		 	return	'<tr>
						<td colspan="2">
							'.we_html_tools::getPixel(2,4).'</td>
					</tr>
					<tr>
						<td><table border="0" cellpadding="0" cellspacing="0">
		 			<tr>
		 				<td colspan="2" class="defaultfont">' . g_l('weClass',"[Charset]") . '</td>
		 			<tr>
		 				<td>' . $retInput . '</td>
		 				<td>' . $retSelect . '</td>
		 			</tr>
		 			</table>';

		} else {	//	charset-tag NOT available

			//getCharsets
			return	'<tr>
						<td colspan="2">
							'.we_html_tools::getPixel(2,4).'</td>
					</tr>
					<tr>
						<td><table border="0" cellpadding="0" cellspacing="0">
		 			<tr>
		 				<td colspan="2" class="defaultfont">' . g_l('weClass',"[Charset]") . '</td>
		 			<tr>
		 				<td>' . $this->htmlTextInput("dummi", 40, g_l('charset',"[error][no_charset_tag]"), "", " readonly disabled", "text", 254) . '</td>
		 				<td>' . $this->htmlSelect("dummi2", array(g_l('charset',"[error][no_charset_available]")), 1, $GLOBALS['WE_BACKENDCHARSET'], false, "disabled ", "value", "254") . '</td>
		 			</tr>
		 			</table>';
		}
	}

	// for internal use
	function setTemplatePath() {
		if($this->TemplateID)
			$this->TemplatePath= TEMPLATE_DIR.f("SELECT Path FROM " . TEMPLATES_TABLE . " WHERE ID=".abs($this->TemplateID),"Path",$this->DB_WE);
		else
			$this->TemplatePath = $_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_templates/we_noTmpl.inc.php";
	}

	function setTemplateID($templID) {
		$this->TemplateID=$templID;
		$this->setTemplatePath();
	}

/*	function setCache() {
		if($this->TemplateID) {
			$this->CacheLifeTime = f("SELECT CacheLifeTime FROM " . TEMPLATES_TABLE . " WHERE ID=".abs($this->TemplateID),"CacheLifeTime",$this->DB_WE);
			if($this->CacheLifeTime > 0) {
				$this->CacheType = f("SELECT CacheType FROM " . TEMPLATES_TABLE . " WHERE ID=".abs($this->TemplateID),"CacheType",$this->DB_WE);
			} else {
				$this->CacheType = "none";
			}
		} else {
			$this->CacheType = "none";
			$this->CacheLifeTime = 0;
		}
	}*/

	function we_new() {
		we_textContentDocument::we_new();
		$this->setTemplatePath();
	}

	function getFieldType($tagname,$tag) {
		switch($tagname) {
			case "formfield":
				return "formfield";
			case "img":
				return "img";
			case "linklist":
				return "linklist";
			case "list":
				return "list";
			case "block":
				return "block";
			case "input":
				if(strpos($tag,'type="date"')!==false) {
					return "date";
				}
				else {
					return "txt";
				}
			default:
				return "txt";
		}
	}

	function makeBlockName($block,$field) {
		$block = str_replace('[0-9]+','####BLOCKNR####',$block);
		$field = str_replace('[0-9]+','####BLOCKNR####',$field);
		$out = preg_quote($field."blk_".$block."__").'[0-9]+';
		return str_replace('####BLOCKNR####','[0-9]+',$out);
	}

	function makeListName($block,$field) {
		$field = str_replace('[0-9]+','####BLOCKNR####',$field);
		$out = preg_quote($field."_").'[0-9]+';
		return str_replace('####BLOCKNR####','[0-9]+',$out);
	}

	function makeLinklistName($block,$field) {
		$block = str_replace('[0-9]+','####BLOCKNR####',$block);
		$field = str_replace('[0-9]+','####BLOCKNR####',$field);
		$out = preg_quote($field.$block."_TAGS_").'[0-9]+';
		return str_replace('####BLOCKNR####','[0-9]+',$out);
	}

	/**
	* @return string
	* @desc this function returns the code of the template this document bases on
	*/
	function getTemplateCode($completeCode=true){
		return f('SELECT ' . CONTENT_TABLE . '.Dat as Dat FROM ' . CONTENT_TABLE . ',' . LINK_TABLE . ' WHERE ' . LINK_TABLE . '.CID=' . CONTENT_TABLE . '.ID AND ' . LINK_TABLE . '.DocumentTable="' . stripTblPrefix(TEMPLATES_TABLE) . '" AND ' . LINK_TABLE . '.DID='.intval($this->TemplateID).' AND ' . LINK_TABLE . '.Name="'.($completeCode ? "completeData" : "data").'"','Dat',$this->DB_WE);
	}

	function getFieldTypes($templateCode) {
		$tp = new we_tagParser($templateCode);
		$tags = $tp->getAllTags();
		$blocks = array();
		$fieldTypes = array();
		//$xmlInputs = array();
		foreach($tags as $tag) {
			if (preg_match('|<we:([^> /]+)|i',$tag,$regs)) { // starttag found
				$tagname = $regs[1];
				if (preg_match('|name="([^"]+)"|i',$tag,$regs) && ($tagname != "var") && ($tagname != "field")) { // name found
					$name=str_replace(array('[',']'), array('\[','\]'), $regs[1]);
					$size = sizeof($blocks);
					if($size) {
						$foo = $blocks[$size-1];
						$blockname = $foo["name"];
						$blocktype = $foo["type"];
						switch($blocktype) {
							case "block":
								$name = we_webEditionDocument::makeBlockName($blockname,$name);
								break;
							case "list":
								$name = we_webEditionDocument::makeListName($blockname,$name);
								break;
							case "linklist":
								$name = we_webEditionDocument::makeLinklistName($blockname,$name);
								break;
						}
					}
					$fieldTypes[$name] = we_webEditionDocument::getFieldType($tagname,$tag);
					switch($tagname) {
						case "block":
						case "list":
						case "linklist":
							$foo = array(
								"name"=>$name,
								"type"=>$tagname
									);
							array_push($blocks,$foo);
							break;
					}
				}
			} else if(preg_match('|</we:([^> ]+)|i',$tag,$regs)) { // endtag found
				$tagname = $regs[1];
				switch($tagname) {
						case "block":
						case "list":
						case "linklist":
							if(sizeof($blocks)) array_pop($blocks);
							break;
				}
			}
		}
		return $fieldTypes;
	}

	function correctFields() {

		// this is new for shop-variants
		$this->correctVariantFields();
		$types = we_webEditionDocument::getFieldTypes($this->getTemplateCode());

		foreach($this->elements as $k=>$v) {
			if(
				!isset($v["type"]) ||
				($v["type"] != "txt" &&
				$v["type"] != "attrib" &&
				$v["type"] != "variant" &&
				$v["type"] != "formfield" &&
				$v["type"] != "date" &&
				$v["type"] != "image" &&
				$v["type"] != "linklist" &&
				$v["type"] != "img" &&
				$v["type"] != "list") ) {
				$this->elements[$k]["type"] = "txt";
			} else {
				foreach($types as $name=>$val) {
					if(preg_match('|^'.$name.'$|i',$k)) {
						$this->elements[$k]["type"] = $val;
						break;
					}
				}
			}

		}

	}

	function we_save($resave = 0,$skipHook=0){
		// First off correct corupted fields
		$this->correctFields();


		// Bug Fix #6615
		$this->temp_template_id = $this->TemplateID;
		$this->temp_doc_type = $this->DocType;
		$this->temp_category = $this->Category;

		// Last step is to save the webEdition document
		$out = we_textContentDocument::we_save($resave,$skipHook);
		if (defined('LANGLINK_SUPPORT') && LANGLINK_SUPPORT && isset($_REQUEST["we_".$this->Name."_LanguageDocID"]) && $_REQUEST["we_".$this->Name."_LanguageDocID"]!=0){
			$this->setLanguageLink($_REQUEST["we_".$this->Name."_LanguageDocID"],'tblFile',false,false);
		}

		if($resave == 0){
			$hy = unserialize(getPref("History"));
			$hy['doc'][$this->ID] = array("Table"=>$this->Table,"ModDate"=>$this->ModDate);
			setUserPref("History",serialize($hy));
		}

		return $out;
	}

	protected function i_writeDocument() {
		$this->setTemplatePath();
		return parent::i_writeDocument();
	}

	function we_publish($DoNotMark=false,$saveinMainDB=true,$skipHook=0){
		$this->we_clearCache($this->ID);
		return we_textContentDocument::we_publish($DoNotMark, $saveinMainDB, $skipHook);
	}

	function we_unpublish($skipHook=0){
		if(!$this->ID) return false;
		$this->we_clearCache($this->ID);
		return we_textContentDocument::we_unpublish($skipHook);
	}

	function we_delete() {
		if(!$this->ID) return false;
		$this->we_clearCache($this->ID);
		return we_document::we_delete();
	}

	function we_clearCache($id) {
		//FIXME:remove
	}

	function we_load($from=LOAD_MAID_DB) {
		switch($from) {
			case LOAD_SCHEDULE_DB:
				$sessDat = unserialize(f("SELECT SerializedData FROM " . SCHEDULE_TABLE . " WHERE DID='".abs($this->ID)."' AND ClassName='".$this->DB_WE->escape($this->ClassName)."' AND Was='".SCHEDULE_FROM."'","SerializedData",$this->DB_WE));
				if($sessDat) {
					$this->i_initSerializedDat($sessDat);
					$this->i_getPersistentSlotsFromDB("Path,Text,Filename,Extension,ParentID,Published,ModDate,CreatorID,ModifierID,Owners,RestrictOwners");
					break;
				}
				else {
					$from = LOAD_TEMP_DB;
				}
			default:
				we_textContentDocument::we_load($from);
				$this->setTemplatePath();
		}
	}

	function i_getDocument($includepath="") {
		$glob = "";
		foreach($GLOBALS as $k=>$v){
			if((!preg_match('|^[0-9]|',$k)) && (!preg_match('|[^a-z0-9_]|i',$k)) && $k != "_SESSION" && $k != "_GET" && $k != "_POST" && $k != "_REQUEST" && $k != "_SERVER" && $k != "_FILES" && $k != "_SESSION" && $k != "_ENV" && $k != "_COOKIE") $glob .= '$'.$k.",";
		}
		$glob = rtrim($glob,',');
		eval('global '.$glob.';');  // globalen Namensraum herstellen.
		$editpageSave = $this->EditPageNr;
		$inWebEditonSave = $this->InWebEdition;
		$this->InWebEdition = false;
		$this->EditPageNr = WE_EDITPAGE_PREVIEW;
		$we_include = $includepath ? $includepath : $this->editor();
		if(isset($GLOBALS["we_baseHref"])){
			$basehrefMerk = $GLOBALS["we_baseHref"];
			unset($GLOBALS["we_baseHref"]);
		}
		// hier bricht es manchmal ab, aus unbekannten gründen, sieh bugbase #4271
		ob_start();
 		if(is_file($we_include)){
			include($we_include);
		}
    	$contents = ob_get_contents();
    	ob_end_clean();
		if(isset($basehrefMerk)){
			$GLOBALS["we_baseHref"] = $basehrefMerk;
			unset($basehrefMerk);
		}
    	$this->EditPageNr = $editpageSave;
		$this->InWebEdition = $inWebEditonSave;

		if((version_compare(phpversion(), '5.0') >= 0) && isset($we_EDITOR) && $we_EDITOR){   //  fix for php5, in editor we_doc was replaced by $GLOBALS['we_doc'] from we:include tags
            $GLOBALS['we_doc'] = $this;
		}

		return $contents;
	}

	function we_initSessDat($sessDat) {
		we_textContentDocument::we_initSessDat($sessDat);
		$this->setTemplatePath();
	}

	function i_scheduleToBeforeNow() {
		if(defined("SCHEDULE_TABLE")) {

		}
		return false;
	}

	function i_areVariantNamesValid() {

		if (defined("SHOP_TABLE")) {

			require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/shop/weShopVariants.inc.php');
			$variationFields = weShopVariants::getAllVariationFields($this);

			if (sizeof($variationFields)) {

				$i=0;
				while (isset($this->elements[WE_SHOP_VARIANTS_PREFIX . "" . $i])) {

					if (!trim($this->elements[WE_SHOP_VARIANTS_PREFIX . "" . $i++]["dat"])) {
						return false;
					}
				}
			}
		}
		return true;
	}

	function i_publInScheduleTable() {
		if(defined("SCHEDULE_TABLE")) {
			$this->DB_WE->query("DELETE FROM ".SCHEDULE_TABLE." WHERE DID='".abs($this->ID)."' AND ClassName='".$this->DB_WE->escape($this->ClassName)."'");
			$ok = true;
			$makeSched = false;
			foreach($this->schedArr as $s){
				if($s["task"] == SCHEDULE_FROM && $s["active"]){
					$serializedDoc = we_temporaryDocument::load($this->ID,$this->Table,$this->DB_WE);// nicht noch mal unten beim Speichern serialisieren, ist bereits serialisiert #5743
					$makeSched = true;
				}
				else {
					$serializedDoc = "";
				}
				include_once(WE_SCHEDULE_MODULE_DIR."we_schedpro.inc.php");
				$Wann = we_schedpro::getNextTimestamp($s,time());

				if(!$this->DB_WE->query("INSERT INTO ".SCHEDULE_TABLE.
						" (DID,Wann,Was,ClassName,SerializedData,Schedpro,Type,Active)
						VALUES('".abs($this->ID)."','".abs($Wann)."','".abs($s["task"])."','".$this->DB_WE->escape($this->ClassName)."','".$this->DB_WE->escape($serializedDoc)."','".$this->DB_WE->escape(serialize($s))."','".abs($s["type"])."','".abs($s["active"])."')")) return false;
			}
			return $makeSched;
		}
		return false;
	}

	// returns the filesize of the document
	function getFilesize() {
	/* dies führt bei manchen dokumenten zum absturz in i_getDocument, und zwar dort beim include innerhalb von ob_start
		$filename = TMP_DIR."/".md5(uniqid(rand()));
		saveFile($filename,$this->i_getDocument($includepath));
		$fs = filesize($filename);
		unlink($filename);
		return $fs;
	*/
		if (file_exists($_SERVER['DOCUMENT_ROOT'].$this->Path) ) {
			$fs= filesize($_SERVER['DOCUMENT_ROOT'].$this->Path);//das ist ungenau
		} else {
			$fs=0;
		}
		return $fs;

	}

	protected function i_getDocumentToSave() {

		if ($this->IsDynamic) {

			$data = array();

			if (defined('SHOP_TABLE') ) {
				require_once(WE_SHOP_MODULE_DIR . "weShopVariants.inc.php");
				weShopVariants::setVariantDataForModel($this,true);
			}
			$this->saveInSession($data);

			if (defined('SHOP_TABLE') ) {
				weShopVariants::correctModelFields($this);
			}


			$data[0]["InWebEdition"] = 0;

			$serialized = serialize($data);
			$base64Object = base64_encode($serialized);
			$doc='<?php
$GLOBALS[\'noSess\'] = true;
$GLOBALS[\'WE_IS_DYN\'] = 1;
$GLOBALS[\'we_transaction\'] = \'\';
$GLOBALS[\'we_ContentType\'] = \'text/webedition\';
$_REQUEST[\'we_cmd\'] = array();

if (isset($_REQUEST[\'pv_id\']) && isset($_REQUEST[\'pv_tid\'])) {
	$_REQUEST[\'we_cmd\'][1] = $_REQUEST[\'pv_id\'];
	$_REQUEST[\'we_cmd\'][4] = $_REQUEST[\'pv_tid\'];
} else {
	$_REQUEST[\'we_cmd\'][1] = ' . $this->ID . ';
}

$FROM_WE_SHOW_DOC = true;

if (!isset($GLOBALS[\'WE_MAIN_DOC\']) && isset($_REQUEST[\'we_objectID\'])) {
	include($_SERVER[\'DOCUMENT_ROOT\'] . \'/webEdition/we/include/we_modules/object/we_object_showDocument.inc.php\');
} else {
	include($_SERVER[\'DOCUMENT_ROOT\'] . \'/webEdition/we/include/we_showDocument.inc.php\');
}';
		} else {
			if (isset($GLOBALS["DocStream"]) && isset($GLOBALS["DocStream"][$this->ID])) {
				$doc = $GLOBALS["DocStream"][$this->ID];
			} else {
				if (!isset($GLOBALS["DocStream"])) {
					$GLOBALS["DocStream"] = array();
				}

				$doc = $this->i_getDocument();

				//
				// --> Glossary Replacement
				//
				if(defined("GLOSSARY_TABLE")) {
					if(isset($this->InGlossar) && $this->InGlossar==0) {
						include_once(WE_GLOSSARY_MODULE_DIR."weGlossaryCache.php");
						include_once(WE_GLOSSARY_MODULE_DIR."weGlossaryReplace.php");
						$doc = weGlossaryReplace::replace($doc, $this->Language);
					}
				}
				$GLOBALS["DocStream"][$this->ID] = $doc;
			}
		}
		return $doc;
	}


	/**
	* @return void
	* @desc This function sets special fields in the document to control i.e. the existing EDIT_PAGES or the available buttons
	*		for this document, use with tags we:hidePages and we:controlElement
	*/
	function setDocumentControlElements(){

		//	get code of the matching template

		$_templateCode = $this->getTemplateCode();

		//	First set hidePages from document ...
		$this->setHidePages($_templateCode);

		//	now set information about buttons of document
		$this->setControlElements($_templateCode);
	}

	function executeDocumentControlElements(){

	    // here we must check, if setDocumentControlElements() already worked
	    if(!isset($this->controlElement) || !is_array($this->controlElement)){
            $this->setDocumentControlElements();
	    }
	    //	disable hidePages
        $this->disableHidePages();
	}

	/**
	* @return void
	* @param string $templatecode
	* @desc	if tag we:controlElement exists in template, this function sets the given control-elements in persistent_slot
	*		they are disabled in document later
	*
	*/
	function setControlElements($templatecode){


		if( strpos($templatecode, '<we:controlElement') !== false ){	// tag we:control exists

			$_tags = we_tagParser::itemize_we_tag('we:controlElement', $templatecode);
			//	we need all given tags ...

			$_size = sizeof($_tags[0]);

			if($_size > 0){

				if(!in_array("controlElement", $this->persistent_slots)){
					$this->persistent_slots[] = "controlElement";
				} else {
					unset($this->controlElement);
				}

				$_ctrlArray = array();

				for($i=0;$i<$_size; $i++){	//	go through all matches

					$_tagAttribs = makeArrayFromAttribs($_tags[2][$i]);

					$_type     = weTag_getAttribute("type", $_tagAttribs);
					$_name     = weTag_getAttribute("name", $_tagAttribs);
					$_hide     = weTag_getAttribute("hide", $_tagAttribs, false, true);

					if($_type && $_name){

						if($_type == "button"){	//	only look, if the button shall be hidden or not

							$_ctrlArray['button'][$_name] = array('hide' => ( $_hide ? 1 : 0 ) );

						} else if($_type == "checkbox"){

							$_checked  = weTag_getAttribute("checked", $_tagAttribs, false, true);
							$_readonly = weTag_getAttribute("readonly", $_tagAttribs, true, true);

							$_ctrlArray['checkbox'][$_name] = array(
																'hide'     => ( $_hide ? 1 : 0 ),
																'readonly' => ( $_readonly ? 1 : 0 ),
																'checked'  => ( $_checked ? 1 : 0 ) );

						}
					}
				}
			}
			$this->controlElement = $_ctrlArray;
		}
	}

	/**
	* @return void
	* @param string $templatecode
	* @desc	if tag we:hidePages exists in template, this function sets the given pages in persistent_slot
	*
	*/
	function setHidePages($templatecode){

		if($this->InWebEdition){

			//	delete exisiting hidePages ...
			if(in_array("hidePages", $this->persistent_slots)){

				unset($this->hidePages);
			}

			if( strpos($templatecode, '<we:hidePages') !== false ){	//	tag hidePages exists

				$_tags = we_tagParser::itemize_we_tag('we:hidePages', $templatecode);

				// here we only take the FIRST tag
				$_tagAttribs = makeArrayFromAttribs($_tags[2][0]);

				$_pages = weTag_getAttribute("pages",  $_tagAttribs);

				if(!in_array("hidePages", $this->persistent_slots)){
					$this->persistent_slots[] = "hidePages";
				} else {
					unset($this->hidePages);
				}
				$this->hidePages = $_pages;

				$this->disableHidePages();
			}
		}
	}

	/**
	* @return void
	* @desc disables the editpages saved in persistent_slot hidePages inside webEdition
	*/
	function disableHidePages() {

		$MNEMONIC_EDITPAGES = array(
				'0' => 'properties', '1' => 'edit', '2' => 'information', '3' => 'preview', '8' => 'schedpro', '10' => 'validation', '17' => 'versions'
		);
		if (isset($_we_active_integrated_modules) && in_array('shop', $_we_active_integrated_modules)) {
			$MNEMONIC_EDITPAGES['11'] = 'variants';
		}
		if (isset($_we_active_integrated_modules) && in_array('customer', $_we_active_integrated_modules)) {
			$MNEMONIC_EDITPAGES['14'] = 'customer';
		}

		if(isset($this->hidePages) && $this->InWebEdition){

			$_hidePagesArr = explode(',', $this->hidePages);	//	get pages which shall be disabled


			if (in_array('all', $_hidePagesArr)) {
				$this->EditPageNrs = array();
			} else {
				foreach($this->EditPageNrs AS $key => $editPage){

					if(array_key_exists($editPage, $MNEMONIC_EDITPAGES) && in_array($MNEMONIC_EDITPAGES[$editPage], $_hidePagesArr)){

						unset($this->EditPageNrs[$key]);
					}
				}
			}
		}
	}

	function changeTemplate(){

	    // reload hidePages, controlElements
	    $this->setDocumentControlElements();
	}

	/**
	 * called when document is initialized from inside webEdition
	 * @param mixed $sessDat
	 */
	function i_initSerializedDat( $sessDat ){
		if(is_array($sessDat)){
			parent::i_initSerializedDat($sessDat);
			if(defined('SHOP_TABLE')) {
				if ($this->canHaveVariants()) {
					$this->initVariantDataFromDb();
				}
			}
		}
	}

	/**
	 * called when document is initialized from outside webEdition
	 * @param mixed $loadBinary
	 */
	function i_getContentData( $loadBinary=0 ){
		parent::i_getContentData($loadBinary);
		if(defined('SHOP_TABLE')) {
			if ($this->canHaveVariants()) { // article variants
				$this->initVariantDataFromDb();
			}
		}

	}


	/**
	 * checks if this document is allowed to have variants
	 * and if it has some fields defined for variants.
	 *
	 * if paramter checkField is true, this function checks also, if there are
	 * already fields selected for the variants.
	 *
	 * @param boolean $checkFields
	 * @return boolean
	 */
	function canHaveVariants($checkFields = false) {

		if(!defined('SHOP_TABLE') || ($this->TemplateID==0) ){
			return false;
		}

		if($this->hasVariants !=null) {
 			return $this->hasVariants;
 		}

 		if($this->InWebEdition){
 			$_has_variants = f('SELECT COUNT(CID) as CCID FROM ' . LINK_TABLE . ' WHERE DID='.abs($this->TemplateID).' AND DocumentTable="tblTemplates" AND Name LIKE ("variant_%");','CCID',$this->DB_WE);
 			$this->hasVariants = !empty($_has_variants) && $_has_variants>0;
 		} else {
 			if (isset($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat']) && is_array($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'])) {
 				$this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'] = serialize($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat']);
 			}
 			if(isset($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]) && substr($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'],0,2) == "a:") {
 				$_vars = unserialize($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]["dat"]);
 				$this->hasVariants = (is_array($_vars) && !empty($_vars));

 			} else {
 				$this->hasVariants = false;
 			}
 		}


		/*require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/we_template.inc.php');
		$template = new we_template();
		// initByID �berschreibt $GLOBALS['we_Table'] und gibt dann einen falschen Wert zur�ck.
		if(isset($GLOBALS['we_Table'])) {
			$tmp_we_Table = $GLOBALS['we_Table'];
		}
		$template->initByID($this->TemplateID,TEMPLATES_TABLE);
		if(isset($GLOBALS['we_Table']) && isset($tmp_we_Table)) {
			$GLOBALS['we_Table'] = $tmp_we_Table;
		}

		if ($checkFields) {
			//return $template->canHaveVariants() && sizeof($template->getVariantFields());
			$this->hasVariants = $template->canHaveVariants() && sizeof($template->getVariantFields());
		} else {
			//return $template->canHaveVariants();
			$this->hasVariants = $template->canHaveVariants();
		}
		*/

		return $this->hasVariants;
	}


	function correctVariantFields() {

		if ($this->canHaveVariants()) {

			require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/shop/weShopVariants.inc.php');
			weShopVariants::correctModelFields($this);
		}
	}

	function initVariantDataFromDb() {

		if (isset($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]) && $this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]["dat"]) {
			require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/shop/weShopVariants.inc.php');

			// unserialize the variant data when loading the model
			//if(!is_array($model->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'])) {
				$this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]["dat"] = unserialize($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]["dat"]);
			//}

			// now register variant fields in document
			weShopVariants::setVariantDataForModel($this);
		}
	}

	function getVariantFields() {
		if($this->TemplateID==0) return array();
		require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/we_template.inc.php');
		$template = new we_template();
		$template->initByID($this->TemplateID,TEMPLATES_TABLE);
		return $template->getVariantFields();
	}

}
