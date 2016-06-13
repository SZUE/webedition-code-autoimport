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
 * @see we_ui_controls_Tree
 */

/**
 * Class to display a tree for toolfactory objects
 *
 * @category   toolfactory
 * @package none
 * @subpackage toolfactory_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class toolfactory_ui_controls_Tree extends we_ui_controls_Tree{

	/**
	 * Constructor
	 *
	 * Sets object properties if set in $properties array
	 *
	 * @param array $properties associative array containing named object properties
	 * @return void
	 */
	public function __construct($properties = null){
		parent::__construct($properties);

		// add needed CSS files
		$this->addCSSFile(we_ui_layout_Themes::computeCSSURL(__CLASS__));
	}

	/**
	 * Retrieve string of node object
	 *
	 * @param integer $id
	 * @param string $text
	 * @return string
	 */
	public function getNodeObject($id, $text, $Published, $Status){
		if(isset($Published) && $Published === 0){
			$outClasses[] = 'unpublished';
		}
		if(!empty($Status)){
			$outClasses[] = $Status;
		}
		$outClass = (!empty($outClasses) ? ' class=\"' . implode(' ', $outClasses) . '\" ' : '');
		return 'var myobj = {
			label: "<span title=\"' . $text . '\" ' . $outClass . ' id=\"spanText_' . $this->_id . '_' . $id . '\">' . $text . '</span>",
			id: "' . $id . '",
			text: "' . $text . '",
			title: "' . $id . '"}; ';
	}

	/**
	 * Retrieve array of nodes from datasource
	 *
	 * @return array
	 */
	public static function doCustom(){
		$items = array();
		$tools = we_tool_lookup::getAllTools(false, false, true);

		foreach($tools as $tool){
			if(!we_tool_lookup::isInIgnoreList($tool['name'])){
				if(isset($tool['text'])){
					$name = $tool['text'];
				} else {
					$name = $tool['name'];
				}
				$items[] = array(
					'ID' => $tool['name'],
					'ParentID' => 0,
					'Text' => $name,
					'ContentType' => 'toolfactory/item',
					'IsFolder' => 0,
					'Published' => !$tool['appdisabled'],
					'Status' => ''
				);
			}
		}
		return $items;
	}

	/**
	 * Retrieve class of tree icon
	 *
	 * @param string $contentType
	 * @param string $extension
	 * @return string
	 */
	public static function getTreeIconClass($contentType, $extension = ''){
		switch($contentType){
			case "toolfactory/item":
				return "toolfactory_item";
				break;
			default:
				return we_ui_controls_Tree::getTreeIconClass($contentType, $extension = '');
		}
	}

	/**
	 * Renders and returns HTML of tree
	 *
	 * @return string
	 */
	protected function _renderHTML(){

		$this->setUpData();
		$session = new we_sdk_namespace($this->_sessionName);
		if(!isset($session->openNodes)){
			$session->openNodes = $this->getOpenNodes();
		}

		$js = '
			var tree_' . $this->_id . ';
			var tree_' . $this->_id . '_activEl = 0;

			(function() {

				function tree_' . $this->_id . '_Init() {
					tree_' . $this->_id . ' = new YAHOO.widget.TreeView("' . $this->_id . '");
					' . $this->getNodesJS() . '

					tree_' . $this->_id . '.draw();
				}

				YAHOO.util.Event.addListener(window, "load", tree_' . $this->_id . '_Init);

			})();
		';

		$page = we_ui_layout_HTMLPage::getInstance();
		$page->addInlineJS($js);

		return '<div class="yui-skin-sam"><div id="' . oldHtmlspecialchars($this->_id) . '"></div></div>';
	}

}
