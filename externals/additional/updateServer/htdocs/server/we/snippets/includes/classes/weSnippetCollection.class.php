<?php

// +----------------------------------------------------------------------+
// | webEdition                                                           |
// +----------------------------------------------------------------------+
// | PHP version 4.1.0 or greater                                         |
// +----------------------------------------------------------------------+
// | Copyright (c) 2000 - 2007 living-e AG                                |
// +----------------------------------------------------------------------+


class weSnippetCollection {
	
	var $Snippets = array();
	
	
	/**
	 * initialize the snippet from an xml file
	 *
	 * @param string $file
	 */
	static function initByXmlFile($file, $Charset = "ISO-8859-1") {
		
		$instance = new weSnippetCollection();
		$Parser = new XML_Parser($file);
		
		$_count = $Parser->execMethod_count("/repository[1]/items[1]", "item");
		for($i = 1; $i <= $_count; $i++) {
		
			$_attribs = $Parser->getAttributes("/repository[1]/items[1]/item[$i]");
			
			$_id = $_attribs['id'];
			$_title = $_attribs['title'];
			$_url = $_attribs['url'];
			$Snippet = new weSnippet($_id, $_title, $_url);
			
			// set the description
			if($Parser->execMethod_count("/repository[1]/items[1]/item[$i]", "description") > 0) {
				$_desc = $Parser->getData("/repository[1]/items[1]/item[$i]/description[1]");
				$Snippet->setDescription($_desc);
				
			}
			
			// set the image
			if($Parser->execMethod_count("/repository[1]/items[1]/item[$i]", "image") > 0) {
				$_attribs = $Parser->getAttributes("/repository[1]/items[1]/item[$i]/image[1]");
				
				$_url = $_attribs['src'];
				$_width = $_attribs['width'];
				$_height = $_attribs['height'];
				
				$Snippet->setImage($_url, $_width, $_height);
				
			}
			
			$_count_files = $Parser->execMethod_count("/repository[1]/items[1]/item[$i]/files[1]", "file");
			
			for($j = 1; $j <= $_count_files; $j++) {
				
				$_attribs = $Parser->getAttributes("/repository[1]/items[1]/item[$i]/files[1]/file[$j]");
				
				$_type = $_attribs['type'];
				$_url = $_attribs['url'];
				
				if($_type == "we-sidebar" && $Charset != "ISO-8859-1") {

					$path = dirname($_url);
					$file = basename($_url);
					$tmp = explode(".", $url);
					
					$content = implode("", file($_url));
					$content = iconv("ISO-8859-1", $Charset, $content);
				
					$_new_file = $path . "/$Charset.$file";
					
					$fh = fopen($_new_file, "w+");
					if($fh && fputs($fh, $content) && fclose($fh)) {
						$_url = $_new_file;
					}
					
				}
				
				$Snippet->addFile($_type, $_url);
				
			}
			
			$_count_previews = $Parser->execMethod_count("/repository[1]/items[1]/item[$i]/previews[1]", "preview");
			for($j = 1; $j <= $_count_previews; $j++) {
				
				$_attribs = $Parser->getAttributes("/repository[1]/items[1]/item[$i]/previews[1]/preview[$j]/image[1]");
				
				$_src = $_attribs['src'];
				$_width = $_attribs['width'];
				$_height = $_attribs['height'];
				
				$_desc = "";
				if($Parser->execMethod_count("/repository[1]/items[1]/item[$i]/previews[1]/preview[$j]", "description") > 0) {
					$_desc = $Parser->getData("/repository[1]/items[1]/item[$i]/previews[1]/preview[$j]/description[1]");
					
				}
				
				$Snippet->addPreview($_src, $_width, $_height, $_desc);
				
			}
			
			array_push($instance->Snippets, $Snippet);
			
		}
		
		return $instance;
				
	}
	
	
	function getAsArray() {
				
		$_array = array();
		foreach ($this->Snippets as $Snippet) {
			$_array[$Snippet->Id] = $Snippet->getAsArray();
			
		}
		
		return $_array;
		
	}
	
}

/**
 * Code Sample
 * 
 * $Snippet = weCodeWizardSnippet::initByXmlFile('Contact.xml');
 * 
 */

?>