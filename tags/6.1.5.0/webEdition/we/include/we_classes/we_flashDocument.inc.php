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
 * @package    webEdition_class
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/"."we_binaryDocument.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/global.inc.php");

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/base/we_image_edit.class.php");


/*  a class for handling flashDocuments. */
class we_flashDocument extends we_binaryDocument
{
	######################################################################################################################################################
	##################################################################### Variables ######################################################################
	######################################################################################################################################################

	/* Name of the class => important for reconstructing the class from outside the class */
	var $ClassName="we_flashDocument";

	/* ContentType of the Object  */
	var $ContentType="application/x-shockwave-flash";

	/* Parameternames which are placed within the object-Tag */
	var $ObjectParamNames = array("align","border","id","height","hspace","name","width","vspace","only","style");

	######################################################################################################################################################
	##################################################################### FUNCTIONS ######################################################################
	######################################################################################################################################################

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */
	function editor() {
		switch($this->EditPageNr){
			case WE_EDITPAGE_PREVIEW:
			return "we_templates/we_editor_flash_preview.inc.php";
			default:
			return parent::editor();
		}
	}

	/* Constructor */
	function we_flashDocument(){
		$this->we_binaryDocument();
		array_push($this->EditPageNrs,WE_EDITPAGE_PREVIEW);
	}


	// is not written yet
	function initByAttribs($attribs){
		if (isset($attribs["sizingrel"])){
			if(isset($attribs["width"])) {$orig_w = $attribs["width"];} else {$orig_w = $this->elements["width"]["dat"];}
			if(isset($attribs["height"])) {$orig_h = $attribs["height"];} else {$orig_h = $this->elements["height"]["dat"];}
			$attribs["width"] = round($orig_w*$attribs["sizingrel"]);
			$attribs["height"] = round($orig_h*$attribs["sizingrel"]);
			unset($attribs["sizingrel"]);
		}
		if (isset($attribs['sizingbase']) && $attribs['sizingbase']!= 16 ){
				$sizingbase = $attribs['sizingbase'];
			} else {
				$sizingbase = 16;
			}
		if (isset($attribs['sizingbase']) ) {unset($attribs['sizingbase']);}

		if (isset($attribs['sizingstyle']) ) {
			if ($attribs['sizingstyle'] =="none") {
				$sizingstyle = false;
			} else {
				$sizingstyle = $attribs['sizingstyle'];
			}
			unset($attribs['sizingstyle']);
		} else {$sizingstyle = false;}

		if ($sizingstyle){
			$style_width = round($attribs["width"]/$sizingbase,6);
			$style_height = round($attribs["height"]/$sizingbase,6);
			if (isset($attribs["style"]) ) {
				$newstyle = $attribs["style"];
			} else {$newstyle="";}

			$newstyle.=";width:" . $style_width . $sizingstyle . ";height:" . $style_height . $sizingstyle . ";";
			$attribs["style"]= $newstyle;
			unset($attribs['width']);
			unset($attribs['height']);
		}
		foreach($attribs as $a=>$b){
			if($b != ""){
				if($a == "Pluginspage" || $a == "Codebase"){
					$this->setElement($a,$b,"txt");
				}else{
					$this->setElement($a,$b,"attrib");
				}
			}
		}
	}

	/* gets the HTML for including in HTML-Docs */
	function getHtml($dyn=false){
		global $we_transaction;

		$_data = $this->getElement("data");
		if ($this->ID || ($_data && !is_dir($_data) && is_readable($_data))) {

			$pluginspage = $this->getElement("Pluginspage") ? $this->getElement("Pluginspage") : "http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash";
			$codebase = $this->getElement("Codebase") ? $this->getElement("Codebase") : "http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0";

			// fix. older versions of webEdition bgcolor was type txt and not attrib
            if (isset($this->elements["bgcolor"])) {
            	$this->elements["bgcolor"]["type"] = "attrib";
            }

			srand ((double)microtime()*1000000);
			$randval = rand();
			$src = $dyn ?
					WEBEDITION_DIR.'we_cmd.php?we_cmd[0]=show_binaryDoc&we_cmd[1]='.$this->ContentType.'&we_cmd[2]='.$we_transaction."&rand=".$randval :
					$this->Path;
			$attribs = array();
			$params  = array();
			$this->html = "";

			/******************************************************************************
			/* take all attribs and seperate them in attribs, params and embeds
			/******************************************************************************/

			$xml = getXmlAttributeValueAsBoolean($this->getElement("xml"));

			//   first we deal with alt-content
			$alt = $this->getElement("alt");
			$altContent = '';
			if($alt){
			    if(isset($GLOBALS["we_doc"]->elements[$alt]) && isset($GLOBALS["we_doc"]->elements[$alt]["type"])){
                    $altContent = $GLOBALS["we_doc"]->getField(array('name'=>$alt,'xml'=>$xml),$GLOBALS["we_doc"]->elements[$alt]["type"]);
			    }
			}

			if($xml){      //  XHTML-Version

			    $allowedAtts = $this->ObjectParamNames;
			    $filter = array("alt",'parentid','startid');

                while(list($k,$v) = $this->nextElement("attrib")){

    				if(in_array($k, $allowedAtts)){         //  use as name="value"
    				    $attribs[$k] = $v["dat"];
    				} else if(!in_array($k, $filter)){       //  use as <param>
    				    $params[$k] = $v["dat"];
    				}
    			}

    			//   needed attribs
    			$attribs["type"] = "application/x-shockwave-flash";
			    $attribs["data"] = $src;

			} else {                                     //  Normal-Version - with embed-tag

                $filter = array("type","alt",'parentid','startid');

                $allowedAtts = $this->ObjectParamNames;

			    while(list($k,$v) = $this->nextElement("attrib")){

			        if(in_array($k, $allowedAtts)){         //  use as name="value"
    				    $attribs[$k] = $v["dat"];

			        } else if(!in_array($k, $filter)){       //  use as <param>
    				    $params[$k] = $v["dat"];

    				}
    				if(!in_array($k, $filter)){
    					if ($v["dat"] !== "") {
    				  	  $embedAtts[$k] = $v["dat"];
    					}
    				}
    			}
    			$attribs["classid"]  = "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000";
			    $attribs["codebase"] = $codebase;
			}

			//   handle with params
			$params["movie"] = $src; //  always needed
			$params = removeAttribs($params, array("xml"));

			foreach($params AS $k => $v){
				if ($v !== "") {
			  	  $this->html .= getHtmlTag("param", array("name" => $k, "value" => $v, "xml" => $this->getElement("xml")));
				}
			}

			if(!$xml){  //  additional <embed tag>

			    $embedAtts["type"] = "application/x-shockwave-flash";
			    $embedAtts["pluginspage"] = $pluginspage;
			    $embedAtts["src"] = $src;

                $this->html .= getHtmlTag("embed", $embedAtts, "", true);
			}

			$this->html = getHtmlTag("object", $attribs, $this->html . $altContent);
			if (isset($attribs['only'])) {
				$this->html = $attribs[$attribs['only']];
			} else if (isset($attribs['pathonly']) && $attribs['pathonly']) {
				$this->html = $src;
			}
		}else{
			if($GLOBALS['we_doc']->InWebEdition == 1) {
				/* Anzeige des No_Falsh-Bildes in der Vorschau
				$imgAtts["src"]    = IMAGE_DIR . 'icons/no_flashmovie.gif';
				$imgAtts["width"]  = 64;
				$imgAtts["height"] = 64;
				$imgAtts["border"] = 0;
				$imgAtts["style"] = "margin:8px 18px;";
				$imgAtts["alt"]    = "";
				$imgAtts["xml"]    = $this->getElement("xml");
				if(isset($this->name)){
					$imgAtts["name"] = $this->name;
				}
				$this->html = getHtmlTag("img", $imgAtts);
				*/
				$this->html ='';
			} else {$this->html ='';}
		}
		return $this->html;
	}

	function formProperties(){
		global $l_we_class,$l_global;
		$content = '<table border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td>'.$this->formInputInfo2(155,"width",10,"attrib","onChange=\"_EditorFrame.setEditorIsHot(true);\"","origwidth").'</td>
		<td>'.getPixel(18,2).'</td>
		<td>'.$this->formInputInfo2(155,"height",10,"attrib","onChange=\"_EditorFrame.setEditorIsHot(true);\"","origheight").'</td>
		<td>'.getPixel(18,2).'</td>
		<td>'.$this->formSelectElement2(155,"scale",array(""=>"","showall"=>$l_global["showall"],"noborder"=>$l_global["noborder"],"exactfit"=>$l_global["exactfit"]),"attrib",1,"onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
	</tr>
	<tr valign="top">
		<td colspan="5">'.getPixel(2,5).'</td>
	</tr>
	<tr valign="top">
		<td>'.$this->formInput2(155,"hspace",10,"attrib","onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
		<td>'.getPixel(18,2).'</td>
		<td>'.$this->formInput2(155,"vspace",10,"attrib","onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
		<td>'.getPixel(18,2).'</td>
		<td>'.$this->formInput2(155,"name",10,"attrib","onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
	</tr>
	<tr valign="top">
		<td colspan="5">'.getPixel(2,5).'</td>
	</tr>
	<tr valign="top">
		<td>'.$this->formSelectElement2(155,"play",array(""=>$l_global["true"],"false"=>$l_global["false"]),"attrib",1,"onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
		<td>'.getPixel(18,2).'</td>
		<td>'.$this->formSelectElement2(155,"quality",array(""=>"","low"=>"low","high"=>"high","autohigh"=>"autohigh","autolow"=>"autolow","best"=>"best"),"attrib",1,"onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
		<td>'.getPixel(18,2).'</td>
		<td>'.$this->formColor(155,"bgcolor",25,"attrib").'</td>
	</tr>
	<tr valign="top">
		<td colspan="5">'.getPixel(2,5).'</td>
	</tr>
	<tr valign="top">
		<td>'.$this->formSelectElement2(155,"align",array(""=>"","left"=>$l_global["left"],"right"=>$l_global["right"],"top"=>$l_global["top"],"bottom"=>$l_global["bottom"]),"attrib",1,"onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
		<td>'.getPixel(18,2).'</td>
		<td>'.$this->formSelectElement2(155,"salign",array(""=>"","l"=>$l_global["left"],"r"=>$l_global["right"],"t"=>$l_global["top"],"b"=>$l_global["bottom"],"tl"=>$l_global["topleft"],"tr"=>$l_global["topright"],"bl"=>$l_global["bottomleft"],"br"=>$l_global["bottomright"]),"attrib",1,"onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
		<td>'.getPixel(18,2).'</td>
		<td>'.$this->formSelectElement2(155,"loop",array(""=>$l_global["true"],"false"=>$l_global["false"]),"attrib",1,"onChange=\"_EditorFrame.setEditorIsHot(true);\"") .'</td>
	</tr>
	<tr valign="top">
		<td colspan="5">'.getPixel(2,5).'</td>
	</tr>
	<tr valign="top">
		<td>'.$this->formSelectElement2(155,"wmode",array(""=>"","window"=>"window","opaque"=>"opaque","transparent"=>"transparent"),"attrib",1,"onChange=\"_EditorFrame.setEditorIsHot(true);\"").'</td>
		<td>'.getPixel(18,2).'</td>
		<td>'.'</td>
		<td>'.getPixel(18,2).'</td>
		<td>'.'</td>
	</tr>

</table>
';
		return $content;
 	}

 	function formOther(){
		global $l_we_class;
		$content = '<table border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td>'.$this->formInputField("txt","Pluginspage","Pluginspage",24,388).'</td>
	</tr>
	<tr valign="top">
		<td>'.$this->formInputField("txt","Codebase","Codebase",24,388).'</td>
	</tr>
</table>
';


 		return $content;
	}


	function getThumbnail() {
		$_width = $this->getElement("width");
		$_height = $this->getElement("height");
		$_scale = $this->getElement("scale");
		$_hspace = $this->getElement("hspace");
		$_vspace = $this->getElement("vspace");
		$_name = $this->getElement("name");
		$_play = $this->getElement("play");
		$_quality = $this->getElement("quality");
		$_bgcolor = $this->getElement("bgcolor");
		$_align = $this->getElement("align");
		$_salign = $this->getElement("salign");
		$_loop = $this->getElement("loop");
		$_wmode = $this->getElement("wmode");
		$_origwidth = $this->getElement("origwidth");
		$_origheight = $this->getElement("origheight");

		$this->setElement("width", 150, "attrib");
		$this->setElement("height", 100, "attrib");
		$this->setElement("scale", "", "attrib");
		$this->setElement("hspace", "", "attrib");
		$this->setElement("vspace", "", "attrib");
		$this->setElement("name", "", "attrib");
		$this->setElement("play", "true", "attrib");
		$this->setElement("quality", "", "attrib");
		$this->setElement("bgcolor", "", "attrib");
		$this->setElement("align", "", "attrib");
		$this->setElement("salign", "", "attrib");
		$this->setElement("loop", "", "attrib");
		$this->setElement("wmode", "window", "attrib");
		$this->setElement("origwidth", "", "attrib");
		$this->setElement("origheight", "", "attrib");

		$html = $this->getHtml(true);
		$this->setElement("width", $_width, "attrib");
		$this->setElement("height", $_height, "attrib");
		$this->setElement("scale", $_scale, "attrib");
		$this->setElement("hspace", $_hspace, "attrib");
		$this->setElement("vspace", $_vspace, "attrib");
		$this->setElement("name", $_name, "attrib");
		$this->setElement("play", $_play, "attrib");
		$this->setElement("quality", $_quality, "attrib");
		$this->setElement("bgcolor", $_bgcolor, "attrib");
		$this->setElement("align", $_align, "attrib");
		$this->setElement("salign", $_salign, "attrib");
		$this->setElement("loop", $_loop, "attrib");
		$this->setElement("wmode", $_wmode, "attrib");
		$this->setElement("origwidth", $_origwidth, "attrib");
		$this->setElement("origheight", $_origheight, "attrib");

		return $html;
	}

	/**
	* function will determine the size of any GIF, JPG, PNG.
	* This function uses the php Function with the same name.
	* But the php function doesn't work with some images created from some apps.
	* So this function uses the gd lib if nothing is returned from the php function
	*
	* @static
	* @public
	* @return array
	* @param $filename complete path of the image
	*/
	function getimagesize($filename){
		$arr = @getimagesize($filename);

		if(isset($arr) && is_array($arr) && (count($arr) >= 4)  && $arr[0] && $arr[1]){
			return $arr;
		}else{
			if(we_image_edit::gd_version()){
				return we_image_edit::getimagesize($filename);
			}
			return $arr;
		}
	}


	/**
	* saves the data of the document
	*
	* @return boolean
	* @param boolean $resave
	*/
	function we_save($resave = 0) {

		// get original width and height of the image
		$arr = $this->getOrigSize(true, true);
		$origw = $this->getElement("origwidth");
		$this->setElement("origwidth",isset($arr[0]) ? $arr[0] : 0);
		$this->setElement("origheight",isset($arr[1]) ? $arr[1] : 0);
		//if ($origw != $this->getElement("origwidth")){$this->DocChanged = true;}
		if($this->getElement("width") =="") {$this->setElement("width",$this->getElement("origwidth"));}
		if($this->getElement("height")=="") {$this->setElement("height",$this->getElement("origheight"));}
		if ($this->Icon =='') {
			$this->Icon = $GLOBALS["WE_CONTENT_TYPES"][$this->ContentType]["Icon"];
		}

		$docChanged = $this->DocChanged; // will be reseted in parent::we_save()
		if (parent::we_save($resave)) {
			if($docChanged){
				$this->DocChanged=true;
			}

			return true;
		}

		return false;
	}

	function we_rewrite() {
		parent::we_rewrite();
		$this->we_save();
	}


	/**
	* Calculates the original image size of the image.
	* Returns an array like the PHP function getimagesize().
	* If the array is empty the image is not uploaded or an error occured
	*
	* @param boolean $calculateNew
	* @return array
	*/
	function getOrigSize($calculateNew = false, $useOldPath = false){
		$arr = array(0,0,0,"");
		if(!$this->DocChanged && $this->ID){
			if($this->getElement("origwidth") && $this->getElement("origheight") && ($calculateNew == false)){
				return array($this->getElement("origwidth"),$this->getElement("origheight"),0,"");
			}else{
				// we have to calculate the path, because maybe the document was renamed
				$path = $this->getParentPath() . "/" . $this->Filename . $this->Extension;
				return $this->getimagesize($_SERVER["DOCUMENT_ROOT"]. (($useOldPath && $this->OldPath) ? $this->OldPath : $this->Path));
			}
		} else if(isset($this->elements["data"]["dat"]) && $this->elements["data"]["dat"]){
			$arr = $this->getimagesize($this->elements["data"]["dat"]);
		}
		return $arr;
	}


}