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

/**
 * Filename:    we_html_select.inc.php
 *
 * Function:    Utility class that implements operations on selects
 *
 * Description: Provides functions for creating html tags
 */
class we_html_select extends we_html_baseCollection{

	/**
	 * Constructor
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$opt_num								array			(optional)
	 *
	 * @return		we_html_select
	 */
	function __construct(array $attribs = array(), $opt_num = 0){
		parent::__construct('select', true, $attribs);
		for($i = 0; $i < $opt_num; $i++){
			$this->addOption();
		}
	}

	/**
	 * The function returns number of options
	 *
	 * @return		int
	 */
	function getOptionNum(){
		return (count($this->childs) - 1);
	}

	/**
	 * The function returns select options as array of we_html_baseElement
	 *
	 * @return		array of we_html_baseElement
	 */
	function getOptionsArray(){
		return $this->childs;
	}

	/**
	 * The function add new option to a select box
	 *
	 * @param		$value									string
	 * @param		$text									string
	 *
	 * @return		void
	 */
	function addOption($value, $text, array $attribs = array()){
		$attribs["value"] = $value;
		$this->childs[] = new we_html_baseElement("option", true, $attribs, $text);
	}

	/**
	 * The function adds one or more options to a select box
	 *
	 * @param		$opt_num									int				(optional)
	 * @param		$values										array			(optional)
	 * @param		$texts										array			(optional)
	 *
	 * @return		void
	 */
	function addOptions(array $entries = array()){
		foreach($entries as $value => $text){
			$this->childs[] = new we_html_baseElement("option", true, array("value" => $value), $text);
		}
	}

	/**
	 * The function deletes option with given option value
	 *
	 * @param		$value										string
	 *
	 * @return		void
	 */
	function delOption($value){
		foreach($this->childs as $k => $v){
			if($v->attribs['value'] == $value){
				$cid = $k;
				break;
			}
		}
		if(isset($cid)){
			$this->delChild($cid);
		}
	}

	/**
	 * The function deletes all options from select
	 *
	 * @return		void
	 */
	function delAllOptions(){
		$this->childs = array();
	}

	/**
	 * The function inserts option on specified place in a select box
	 * Parameter $optid defines option's place in select's child array
	 * If $over is true then an option which is placed on the specified position will be overwritten
	 *
	 * @param		$optid									int
	 * @param		$value									string
	 * @param		$text									string
	 * @param		$over									string			(optional)
	 *
	 * @return		void
	 */
	function insertOption($optid, $value, $text, $over = false){
		$new_opt = new we_html_baseElement("option", true, array("value" => $value), $text);

		if($over){
			$this->childs[$optid] = $new_opt;
		} else {
			if($optid == 0){
				$optid = -1;
			}
			if(count($this->childs) >= $optid + 1){
				$array_pre = array_slice($this->childs, 0, ($optid + 1));
				$array_pre[] = $new_opt;
				$array_post = array_slice($this->childs, ($optid + 1));
				$this->childs = array_merge($array_pre, $array_post);
			} else {
				$this->childs[] = $new_opt;
			}
		}
	}

	/**
	 * The function sets option attributes and content. The option is identified by optid.
	 *
	 * @param		$optid									int
	 * @param		$attribs								array
	 * @param		$attribs								array
	 *
	 * @return		void
	 */
	function setOption($optid, array $attribs = array(), $content = null){
		$opt = & $this->getChild($optid);
		$opt->setAttributes($attribs);
		if($content != null){
			$opt->setContent($content);
		}
	}

	/**
	 * The function selects option that is identified by the value.
	 *
	 * @param		$value									string
	 *
	 * @return		void
	 */
	function selectOption($value){
		if(!in_array('multiple', array_keys($this->attribs))){
			$this->unselectAllOptions();
		}
		foreach($this->childs as $k => $v){
			//fix #7912
			$equal = (is_bool($v->attribs["value"]) || is_bool($value)) ? $v->attribs["value"] == $value :
				(string) $v->attribs["value"] == (string) $value;

			if($equal){
				$this->setOption($k, array("selected" => 'selected'));
				return true;
			}
		}

		return false;
	}

	/**
	 * The function unsets all selected options
	 *
	 *
	 * @return		void
	 */
	function unselectAllOptions(){
		foreach($this->childs as $k => $v){
			if(in_array('selected', array_keys($v->attribs))){
				unset($this->childs[$k]->attribs["selected"]);
			}
		}
	}

	/**
	 * The function sets option identified by optid with given value and text
	 * @param		$optid									int
	 * @param		$value									string
	 * @param		$text									string
	 *
	 * @return		void
	 */
	function setOptionVT($optid, $value, $text){
		$opt = & $this->getChild($optid);
		$opt->setAttribute("value", $value);
		$opt->setContent($text);
	}

	/**
	 * The function adds a new option group to the select box
	 *
	 * @param  $attribs        array
	 *
	 * @return  void
	 */
	function addOptionGroup(array $attribs = array()){
		$this->childs[] = new we_html_baseCollection("optgroup", true, $attribs);
	}

	/**
	 * The function returns a new option. This function is static.
	 *
	 * @param  $value         string
	 * @param  $text         string
	 *
	 * @return  we_html_baseElement
	 */
	function getNewOption($value, $text){
		return new we_html_baseElement("option", true, array("value" => $value), $text);
	}

	/**
	 * The function returns a new option group. This function is static.
	 *
	 * @param  $attribs        array
	 *
	 * @return  we_html_baseElement
	 */
	function getNewOptionGroup(array $attribs = array()){
		return new we_html_baseCollection("optgroup", true, $attribs);
	}

}
