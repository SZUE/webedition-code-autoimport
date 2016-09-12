<?php
include_once(WE_INCLUDES_PATH . 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/taged.inc.php');

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
class weTagDataAttribute{
	/**
	 * @var string
	 */
	protected $Id;

	/**
	 * @var string
	 */
	protected $Name;

	/**
	 * @var boolean
	 */
	protected $Required;

	/**
	 * @var string
	 */
	protected $Module;

	/**
	 * @var string
	 */
	protected $Value;
	protected $Description;
	protected $Deprecated;

	/**
	 * @param string $name
	 * @param boolean $required
	 * @param string $module
	 */
	public function __construct($name, $required = false, $module = '', $description = '', $deprecated = false){
		static $count = 0;
		$this->Id = ++$count;
		$this->Name = $name;
		$this->Required = $required;
		$this->Module = $module;
		// set value occasionally
		$this->Value = we_base_request::_(we_base_request::STRING, 'attributes', false, $name);
		$this->Description = $description;
		$this->Deprecated = $deprecated;
	}

	/**
	 * @return string
	 */
	function getLabelCodeForTagWizard(){
		$tmp = [
			'id' => 'label_' . $this->getIdName(),
			'class' => 'defaultfont',
			'for' => $this->getIdName()
		];
		if($this->Description){
			$tmp['style'] = 'border-bottom-style: dotted;border-bottom-width: 1px;border-spacing: 2px;cursor:help;';
			$tmp['title'] = $this->Description;
		}
		if($this->Deprecated){
			$tmp['style'] .= 'text-decoration:line-through;';
		}
		return we_html_element::htmlLabel($tmp, $this->Name . ($this->Required ? '*' : ''));
	}

	/**
	 * @return string
	 */
	function getName(){
		return $this->Name;
	}

	/**
	 * @return string
	 */
	function getIdName(){
		return 'id' . $this->Id . '_' . $this->Name;
	}

	/**
	 * @return boolean
	 */
	function IsRequired(){
		return $this->Required;
	}

	function IsDeprecated(){
		return $this->Deprecated;
	}

	function getDescription(){
		return $this->Description;
	}

	/**
	 * checks if this attribute should be used, checks if needed modules are installed
	 * @return boolean
	 */
	function useAttribute(){
		return ($this->Module === '' || we_base_moduleInfo::isActive($this->Module));
	}

	/**
	 * checks if this option should be used, checks if needed modules are installed
	 * @return boolean
	 */
	static function getUseOptions(array $options){
		$useOptions = [];
		foreach($options as $option){
			if($option->useOption()){
				$useOptions[] = $option;
			}
		}
		return $useOptions;
	}

}
