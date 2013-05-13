<?php

// +----------------------------------------------------------------------+
// | webEdition                                                           |
// +----------------------------------------------------------------------+
// | PHP version 4.1.0 or greater                                         |
// +----------------------------------------------------------------------+
// | Copyright (c) 2000 - 2007 living-e AG                                |
// +----------------------------------------------------------------------+

class weSnippet {
	
	/**
	 * ID of the Snippet
	 *
	 * @var string
	 */
	var $Id = "";
	
	/**
	 * Title of the Snippet
	 *
	 * @var string
	 */
	var $Title = "";
	
	/**
	 * Url of the Snippet
	 *
	 * @var string
	 */
	var $Url = "";
	
	/**
	 * Description of the snippet
	 *
	 * @var string
	 */
	var $Description = "";
	
	/**
	 * Image of the snippet
	 *
	 * @var array
	 */
	var $Image = array();
	
	/**
	 * Files for download
	 *
	 * @var array
	 */
	var $Files = array();
	
	/**
	 * Prerview Images
	 *
	 * @var array
	 */
	var $Previews = array();
	
	
	/**
	 * PHP 5 constructor
	 *
	 */
	function __construct($Id, $Title, $Url) {
		
		$this->Id = $Id;
		$this->Title = $Title;
		$this->Url = $Url;
		
	}
	
	
	/**
	 * PHP 4 constructor
	 *
	 * @return weSnippet
	 */
	function weCodeWizardSnippet($Id, $Title, $Url) {
		
		$this->__construct($Id, $Title, $Url);
		
	}
	
	
	/**
	 * set the description of the snippet
	 *
	 * @param string $Description
	 */
	function setDescription($Description) {
		
		$this->Description = $Description;
		
	}
	
	/**
	 * set a image to the snippet
	 *
	 * @param string $Type
	 * @param string $Path
	 */
	function setImage($Url, $Width, $Height) {
		
		$Image = array(
			'url' => $Url,
			'width' => $Width,
			'height' => $Height,
		);
		
		$this->Image = $Image;
		
	}
	
	/**
	 * Add a file to the snippet
	 *
	 * @param string $Type
	 * @param string $Path
	 */
	function addFile($Type, $Path) {
		
		$File = array(
			'type' => $Type,
			'path' => $Path,
		);
		
		array_push($this->Files, $File);
		
	}
	
	
	/**
	 * Add a preview to the snippet
	 *
	 * @param string $Url
	 * @param integer $Width
	 * @param integer $Height
	 * @param string $Description
	 */
	function addPreview($Url, $Width, $Height, $Description = "") {
		
		$Preview = array(
			'url' => $Url,
			'width' => $Width,
			'height' => $Height,
			'description' => $Description,
		);
		
		array_push($this->Previews, $Preview);
		
	}
	
	
	function getAsArray() {
		
		$_array = array(
			'ID' => $this->Id,
			'Title' => $this->Title,
			'Description' => $this->Description,
		
		);
		
		$_array['Files'] = array();
		foreach ($this->Files as $File) {
			if($File['type'] == "we-import") {
				$_array['Files']['Import'] = $File['path'];
				
			} elseif($File['type'] == "we-sidebar") {
				$_array['Files']['Sidebar'] = $File['path'];
				
			}
			
		}
		
		$_array['Image'] = array();
		if(sizeof($this->Image) > 0) {
			$_array['Image']['Src'] = $this->Url . $this->Image['url'];
			$_array['Image']['Width'] = $this->Image['width'];
			$_array['Image']['Height'] = $this->Image['height'];
			
		}
		
		$_array['Preview'] = array();
		foreach($this->Previews as $Preview) {
			$_temp = array(
				'Src' => $this->Url . $Preview['url'],
				'Width' => $Preview['width'],
				'Height' => $Preview['height'],
				'Description' => $Preview['description'],
			);
			array_push($_array['Preview'], $_temp);
			
		}
		
		return $_array;
		
	}
	
		
}

		
?>