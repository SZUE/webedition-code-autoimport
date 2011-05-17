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
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_cmdAttribute.class.php');

class weTagData {

	/**
	 * @var string
	 */
	var $Name;
	/**
	 * @var string
	 */
	var $TypeAttribute = null;
	/**
	 * @var array
	 */
	var $Attributes;
	/**
	 * @var string
	 */
	var $Description;
	/**
	 * @var string
	 */
	var $DefaultValue;
	/**
	 * @var string
	 */
	var $NeedsEndTag;

	/**
	 * @param string $name
	 * @param weTagDataAttribute $typeAttribute
	 * @param array $attributes
	 * @param string $description
	 * @param boolean $needsendtag
	 * @param string $defaultvalue
	 */
	function weTagData($name, $attributes = array(), $description = '', $needsendtag = false, $defaultvalue = '', $noDocuLink=false, $DocuLink='') {

		// only use attributes allowed regarding the installed modules
		// set attributes for this tag


		$attribs = array();
		foreach ($attributes as $attribute) {

			if ($attribute->useAttribute()) {

				if (strtolower(get_class($attribute)) == strtolower("weTagData_typeAttribute")) {

					$this->TypeAttribute = $attribute;
				}
				$attribs[] = $attribute;
			}
		}

		// Feature #4535
		if ($DocuLink != '') {
			$GLOBALS['TagRefURL'] = $DocuLink; //??? Where does this come from? When does it occur? If this is beeing used it wont work with we_cmd
		}
		if ($this->TypeAttribute) {
			foreach ($this->TypeAttribute->Options as &$value) {
				$value->AllowedAttributes[] = 'idTagRef_' . $this->TypeAttribute->Name . '_' . $value->Value . '_TagReferenz';
				if ($value->Value != '-') {
					$attribs[] = new weTagData_cmdAttribute('TagRef_' . $this->TypeAttribute->Name . '_' . $value->Value, 'TagReferenz', false, '', array('open_tagreference', $GLOBALS['TagRefURLName'] . '-' . $this->TypeAttribute->Name . '-' . $value->Name), $GLOBALS['l_taged']['tagreference_linktext']);
				}
			}
		} else {
			$attribs[] = new weTagData_cmdAttribute('TagRef_', 'TagReferenz', false, '', array('open_tagreference', $GLOBALS['TagRefURLName']), $GLOBALS['l_taged']['tagreference_linktext']);
		}

		$this->Name = $name;
		$this->Attributes = $attribs;
		$this->Description = $description;
		$this->NeedsEndTag = $needsendtag;
		$this->DefaultValue = $defaultvalue;
	}

	/**
	 * @return string
	 */
	function getName() {
		return $this->Name;
	}

	/**
	 * @return string
	 */
	function getDescription() {
		return $this->Description;
	}

	/**
	 * @param string $tagName
	 * @return weTagData
	 */
	function getTagData($tagName) {

		// include the selected tag, its either normal, or custom tag
		if (file_exists(
										$_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/we_tags/we_tag_' . $tagName . '.inc.php')) {
			require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/we_tags/we_tag_' . $tagName . '.inc.php');
		} else
		if (file_exists(
										$_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/we_tags/custom_tags/we_tag_' . $tagName . '.inc.php')) {
			require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/we_tags/custom_tags/we_tag_' . $tagName . '.inc.php');
		} else {
			//Application Tags
			include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/tools/weToolLookup.class.php");
			$apptags = array();
			$alltools = weToolLookup::getAllTools(true);
			$allapptags = array();
			$allapptagnames = array();
			foreach ($alltools as $tool) {
				$apptags = weToolLookup::getAllToolTagWizards($tool['name']);
				$allapptags = array_merge($allapptags, $apptags);
				$apptagnames = array_keys($apptags);
				$allapptagnames = array_merge($allapptagnames, $apptagnames);
			}
			if (in_array($tagName, $allapptagnames)) {
				require_once ($allapptags[$tagName]);
			} else {
				return false;
			}
		}
		if (isset($GLOBALS['l_we_tag'][$tagName]['description'])) {
			$description = isset($GLOBALS['l_we_tag'][$tagName]['description']);
		} else {
			if (isset($GLOBALS['weTagWizard']['weTagData']['description'])) {
				$description = $GLOBALS['weTagWizard']['weTagData']['description'];
			} else {
				$description = '';
			}
		}
		return new weTagData(
						$tagName,
						isset($GLOBALS['weTagWizard']['attribute']) ? $GLOBALS['weTagWizard']['attribute'] : array(),
						$description,
						$GLOBALS['weTagWizard']['weTagData']['needsEndtag'],
						isset($GLOBALS['l_we_tag'][$tagName]['defaultvalue']) ? $GLOBALS['l_we_tag'][$tagName]['defaultvalue'] : '',
						isset($GLOBALS['weTagWizard']['weTagData']['noDocuLink']) ? $GLOBALS['weTagWizard']['weTagData']['noDocuLink'] : '',
						isset($GLOBALS['weTagWizard']['weTagData']['DocuLink']) ? $GLOBALS['weTagWizard']['weTagData']['DocuLink'] : ''
		);
	}

	/**
	 * @return boolean
	 */
	function needsEndTag() {
		return $this->NeedsEndTag;
	}

	/**
	 * @return array
	 */
	function getAllAttributes($idPrefix = false) {

		$attribs = array();

		foreach ($this->Attributes as $attrib) {

			if ($idPrefix) {
				$attribs[] = $attrib->getIdName();
			} else {
				$attribs[] = $attrib->getName();
			}
		}
		return $attribs;
	}

	/**
	 * @return mixed
	 */
	function getTypeAttribute() {

		return $this->TypeAttribute;
	}

	/**
	 * @return array
	 */
	function getRequiredAttributes() {

		$req = array();

		foreach ($this->Attributes as $attrib) {
			if ($attrib->IsRequired()) {
				$req[] = $attrib->getIdName();
			}
		}
		return $req;
	}

	/**
	 * @return array
	 */
	function getTypeAttributeOptions() {

		if ($this->TypeAttribute) {
			return $this->TypeAttribute->getOptions();
		}
		return null;
	}

	/**
	 * @return string
	 */
	function getAttributesCodeForTagWizard() {

		$ret = '';

		$typeAttrib = $this->getTypeAttribute();

		if (sizeof($this->Attributes) > 1 || (sizeof($this->Attributes) && !$typeAttrib)) {

			$ret = '
		<ul>';
			foreach ($this->Attributes as $attribute) {

				if ($attribute != $this->TypeAttribute) {
					$ret .= '
			<li ' . ($typeAttrib ? 'style="display:none;"' : '') . ' id="li_' . $attribute->getIdName() . '">' . $attribute->getCodeForTagWizard() . '
			</li>';
				}
			}
			$ret .= '
		</ul>';
		}
		return $ret;
	}

	/**
	 * @return string
	 */
	function getTypeAttributeCodeForTagWizard() {

		$ret = '';

		if ($this->TypeAttribute) {

			$ret = '
			<ul>';

			$ret .= '
				<li>' . $this->TypeAttribute->getCodeForTagWizard() . '
				</li>';
			$ret .= '
			</ul>';
		}

		return $ret;
	}

	/**
	 * @return string
	 */
	function getDefaultValueCodeForTagWizard() {

		return we_htmlElement::htmlTextArea(
						array(
				'name' => 'weTagData_defaultValue',
				'id' => 'weTagData_defaultValue',
				'class' => 'wetextinput wetextarea'
						), $this->DefaultValue);
	}

}
