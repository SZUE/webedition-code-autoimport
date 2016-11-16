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
class weTagData{
	private $Exists = false;

	/**
	 * @var string
	 */
	public $Name;

	/**
	 * @var string
	 */
	private $TypeAttribute = null;

	/**
	 * @var array
	 */
	private $Attributes = [];
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
	private $Groups = [];
	private $Deprecated = false;
	private $noDocuLink = false;

	private function __construct($tagName){
		$this->Name = $tagName;
		try{
			// include the selected tag, its either normal, or custom tag
			if(file_exists(WE_INCLUDES_PATH . 'weTagWizard/we_tags/we_tag_' . $tagName . '.inc.php')){
				$this->Exists = true;
				require (WE_INCLUDES_PATH . 'weTagWizard/we_tags/we_tag_' . $tagName . '.inc.php');
			} elseif(file_exists(WE_INCLUDES_PATH . 'weTagWizard/we_tags/custom_tags/we_tag_' . $tagName . '.inc.php')){
				$this->Exists = true;
				require (WE_INCLUDES_PATH . 'weTagWizard/we_tags/custom_tags/we_tag_' . $tagName . '.inc.php');
				$this->Groups[] = 'custom';
				$this->noDocuLink = true;
			} else {
				//Application Tags
				$apptags = $allapptags = $allapptagnames = [];
				$alltools = we_tool_lookup::getAllTools(true);
				foreach($alltools as $tool){
					$apptags = we_tool_lookup::getAllToolTagWizards($tool['name']);
					$allapptags = array_merge($allapptags, $apptags);
					$apptagnames = array_keys($apptags);
					$allapptagnames = array_merge($allapptagnames, $apptagnames);
				}
				if(in_array($tagName, $allapptagnames)){
					require_once ($allapptags[$tagName]);
					$this->Exists = true;
					$this->Groups[] = 'apptags';
					$this->noDocuLink = true;
				} else {
					t_e('requested help entry of tag ' . $tagName . ' not found');
					return;
				}
			}
		} catch (Exception $e){
			t_e('Error in TW-Tag ' . $this->Name, $e->getMessage());
		}

		if(strpos($tagName, 'if') !== 0){
			$to = new weTagData_choiceAttribute('to', [
				new weTagDataOption('global'),
				new weTagDataOption('request'),
				new weTagDataOption('get'),
				new weTagDataOption('post'),
				new weTagDataOption('session'),
				new weTagDataOption('top'),
				new weTagDataOption('self'),
				new weTagDataOption('block'),
				new weTagDataOption('sessionfield'),
				new weTagDataOption('screen'),
				], false, false, '');
			$nameto = new weTagData_textAttribute('nameto', false, '');
		}

		if($this->TypeAttribute){
			if(!is_array($this->TypeAttribute->getOptions())){
				t_e('Error in TypeAttribute of we:' . $this->Name);
			} else {
				$options = $this->TypeAttribute->getOptions();
				if(!$this->noDocuLink){
					foreach($options as &$value){
						$tmp = new weTagData_cmdAttribute('TagReferenz', false, '', ['open_tagreference', strtolower($tagName) . ($value->Name ? '-' . $this->TypeAttribute->getName() . '-' . $value->Name : '')], g_l('taged', '[tagreference_linktext]'));
						$value->AllowedAttributes[] = $tmp;
						if($value->Value != '-'){
							$this->Attributes[] = $tmp;
						}
					}
				}
				//fix common error: not all type attributes are present in attributes
				foreach($options as $value){
					$tmp = $value->AllowedAttributes;
					foreach($tmp as $cur){
						if($cur != $this->TypeAttribute && !empty($cur) && !in_array($cur, $this->Attributes)){
							$this->Attributes[] = $cur;
						}
					}
					if(isset($to)){
						$value->addTypeAttribute($to);
						$value->addTypeAttribute($nameto);
					}
				}
			}
		} else {
			if(!$this->noDocuLink){
				$this->Attributes[] = new weTagData_cmdAttribute('TagReferenz', false, '', ['open_tagreference', strtolower($tagName)], g_l('taged', '[tagreference_linktext]')); // Bug #6341
			}
		}

		if(isset($to)){
			$this->Attributes[] = $to;
			$this->Attributes[] = $nameto;
		}
	}

	private function updateUsedAttributes(){
		$this->UsedAttributes = [];
		if($this->TypeAttribute){
			$this->UsedAttributes[] = $this->TypeAttribute;
		}
		foreach($this->Attributes as $pos => $attr){
			if($attr === null){
				continue;
			}
			if(!is_object($attr)){
				t_e('Error in Attributes of we:' . $this->Name, $attr, $pos);
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

	function isDeprecated(){
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
		static $tags = [];
		if(isset($tags[$tagName])){
			$tag = $tags[$tagName];
		} else {
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
		$attribs = [];
		foreach($this->UsedAttributes as $attrib){
			$attribs[] = ($idPrefix ?
					$attrib->getIdName() :
					$attrib->getName()
				);
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
		$req = [];

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

	function getAttributesForCM(){
		$attr = [];

		foreach($this->UsedAttributes as $attribute){
			if(!$attribute->IsDeprecated() && $attribute->useAttribute() && !($attribute instanceof weTagData_linkAttribute) && !($attribute instanceof weTagData_cmdAttribute)){
				$attr[] = $attribute->getName();
			}
		}
		return $attr;
	}

	/**
	 * @return string
	 */
	function getAttributesCodeForTagWizard(){
		$ret = '';

		$typeAttrib = $this->getTypeAttribute();

		if(count($this->UsedAttributes) > 1 || (count($this->UsedAttributes) && !$typeAttrib)){

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

		return we_html_element::htmlTextArea([
				'name' => 'weTagData_defaultValue',
				'id' => 'weTagData_defaultValue',
				'class' => 'wetextinput wetextarea'
				], $this->DefaultValue);
	}

}
