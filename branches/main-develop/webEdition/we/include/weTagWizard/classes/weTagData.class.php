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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
//include all possible tag classes
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_cmdAttribute.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagDataAttribute.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_choiceAttribute.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_cmdAttribute.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_linkAttribute.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_multiSelectorAttribute.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagDataOption.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_sqlColAttribute.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_sqlRowAttribute.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_typeAttribute.class.php');

class weTagData{

	private $Exists = false;

	/**
	 * @var string
	 */
	private $Name;

	/**
	 * @var string
	 */
	private $TypeAttribute = null;

	/**
	 * @var array
	 */
	private $Attributes = array();
	private $UsedAttributes = null;

	/**
	 * @var string
	 */
	private $Description;

	/**
	 * @var string
	 */
	private $DefaultValue;

	/**
	 * @var string
	 */
	private $NeedsEndTag = false;
	private $Module = 'basis';
	private $Groups = array();
	private $Deprecated = false;

	private function __construct($tagName){
		$this->Name = $tagName;
		// include the selected tag, its either normal, or custom tag
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/we_tags/we_tag_' . $tagName . '.inc.php')){
			require ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/we_tags/we_tag_' . $tagName . '.inc.php');
			$this->Exists = true;
		} else
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/we_tags/custom_tags/we_tag_' . $tagName . '.inc.php')){
			require ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/we_tags/custom_tags/we_tag_' . $tagName . '.inc.php');
			$this->Exists = true;
			$this->Groups[] = 'custom';
		} else{
			//Application Tags
			include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_classes/tools/weToolLookup.class.php");
			$apptags = array();
			$alltools = weToolLookup::getAllTools(true);
			$allapptags = array();
			$allapptagnames = array();
			foreach($alltools as $tool){
				$apptags = weToolLookup::getAllToolTagWizards($tool['name']);
				$allapptags = array_merge($allapptags, $apptags);
				$apptagnames = array_keys($apptags);
				$allapptagnames = array_merge($allapptagnames, $apptagnames);
			}
			if(in_array($tagName, $allapptagnames)){
				require_once ($allapptags[$tagName]);
				$this->Exists = true;
				$this->Groups[] = 'apptags';
			} else{
				t_e('requested help entry of tag ' . $tagName . ' not found');
				return;
			}
		}

		if($this->TypeAttribute){
			if(!is_array($this->TypeAttribute->Options)){
				t_e('Error in TypeAttribute of we:' . $this->Name);
			} else{
				foreach($this->TypeAttribute->Options as &$value){
					$tmp = new weTagData_cmdAttribute('TagReferenz', false, '', array('open_tagreference', strtolower($tagName) . '-' . $this->TypeAttribute->getName() . '-' . $value->Name), g_l('taged', '[tagreference_linktext]'));
					$value->AllowedAttributes[] = $tmp;
					if($value->Value != '-'){
						$this->Attributes[] = $tmp;
					}
				}
			}
		} else{
			$value->AllowedAttributes[] = new weTagData_cmdAttribute('TagReferenz', false, '', array('open_tagreference', strtolower($tagName)), g_l('taged', '[tagreference_linktext]'));
		}
	}

	private function updateUsedAttributes(){
		$this->UsedAttributes = array();
		if($this->TypeAttribute){
			$this->UsedAttributes[] = $this->TypeAttribute;
		}
		foreach($this->Attributes as $attr){
			if($attr === null){
				continue;
			}
			if(!is_object($attr)){
				t_e('Error in Attributes of we:' . $this->Name, $attr);
			} else if($attr->useAttribute()){
				$this->UsedAttributes[] = $attr;
			}
		}
	}

	/**
	 * @return string
	 */
	function getName(){
		return $this->Name;
	}

	function getModule(){
		return $this->Module;
	}

	function getGroups(){
		return $this->Groups;
	}

	function isDeprected(){
		return $this->Deprecated;
	}

	/**
	 * @return string
	 */
	function getDescription(){
		return $this->Description;
	}

	/**
	 * @param string $tagName
	 * @return weTagData
	 */
	static function getTagData($tagName){
		static $tags = array();
		if(isset($tags[$tagName])){
			$tag = $tags[$tagName];
		} else{
			$tag = new weTagData($tagName);
			if(!$tag->Exists){
				return null;
			}
			$tags[$tagName] = $tag;
		}
		$tag->updateUsedAttributes();
		return $tag;
	}

	/**
	 * @return boolean
	 */
	function needsEndTag(){
		return $this->NeedsEndTag;
	}

	/**
	 * @return array
	 */
	function getAllAttributes($idPrefix = false){

		$attribs = array();

		foreach($this->UsedAttributes as $attrib){

			if($idPrefix){
				$attribs[] = $attrib->getIdName();
			} else{
				$attribs[] = $attrib->getName();
			}
		}
		return $attribs;
	}

	/**
	 * @return mixed
	 */
	function getTypeAttribute(){
		return $this->TypeAttribute;
	}

	/**
	 * @return array
	 */
	function getRequiredAttributes(){

		$req = array();

		foreach($this->UsedAttributes as $attrib){
			if($attrib->IsRequired()){
				$req[] = $attrib->getIdName();
			}
		}
		return $req;
	}

	/**
	 * @return array
	 */
	function getTypeAttributeOptions(){

		if($this->TypeAttribute){
			return $this->TypeAttribute->getOptions();
		}
		return null;
	}

	/**
	 * @return string
	 */
	function getAttributesCodeForTagWizard(){

		$ret = '';

		$typeAttrib = $this->getTypeAttribute();

		if(sizeof($this->UsedAttributes) > 1 || (sizeof($this->UsedAttributes) && !$typeAttrib)){

			$ret = '<ul>';
			foreach($this->UsedAttributes as $attribute){

				if($attribute != $this->TypeAttribute){
					$ret .= '<li ' . ($typeAttrib ? 'style="display:none;"' : '') . ' id="li_' . $attribute->getIdName() . '">' . $attribute->getCodeForTagWizard() . '</li>';
				}
			}
			$ret .= '</ul>';
		}
		return $ret;
	}

	/**
	 * @return string
	 */
	function getTypeAttributeCodeForTagWizard(){
		if($this->TypeAttribute){
			return '<ul>' .
				'<li>' . $this->TypeAttribute->getCodeForTagWizard() . '</li>' .
				'</ul>';
		}
		return '';
	}

	/**
	 * @return string
	 */
	function getDefaultValueCodeForTagWizard(){

		return we_htmlElement::htmlTextArea(
				array(
				'name' => 'weTagData_defaultValue',
				'id' => 'weTagData_defaultValue',
				'class' => 'wetextinput wetextarea'
				), $this->DefaultValue);
	}

}
