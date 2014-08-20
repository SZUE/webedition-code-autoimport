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
class we_exim_searchPatterns{

	public $doc_patterns = array("id" => array(), "path" => array());
	public $obj_patterns = array("id" => array(), "path" => array());
	public $class_patterns = array();
	public $ext_patterns = array();
	public $wysiwyg_patterns = array();
	public $navigation_patterns = array();
	public $thumbnail_patterns = array();
	public $tmpl_patterns = array();
	public $special_patterns = array();

	public function __construct(){
		$this->doc_patterns = array("id" => array(), "path" => array());
		$this->obj_patterns = array("id" => array(), "path" => array());
		$this->class_patterns = array();
		$this->ext_patterns = array();
		$this->wysiwyg_patterns = array();
		$this->special_patterns = array();

		$spacer = "[\040|\n|\t|\r]*";

		$_pats = array(
			'a' => 'id',
			'addDelNewsletterEmail' => array('id', 'mailid'),
			'css' => 'id',
			'a' => 'id',
			'form' => array('id', 'onsuccess', 'onerror', 'onmailerror', 'onrecipienterror'),
			'icon' => 'id',
			'img' => array('id', 'startid', 'parentid'),
			'flashmovie' => array('startid', 'parentid'),
			'quicktime' => array('startid', 'parentid'),
			'js' => 'id',
			'linkToSeeMode' => 'id',
			'url' => 'id',
			'ifSelf' => 'id',
			'object' => 'triggerid',
			'listview' => array('id', 'triggerid', 'workspaceID'),
			'sessionLogout' => 'id',
			'field' => 'id'
		);

		foreach($_pats as $tag => $attribut){
			if(is_array($attribut)){
				foreach($attribut as $attrib){
					$this->doc_patterns["id"][] = "/<(we:" . $tag . $spacer . "[^>]*[\040|\n|\t|\r]+" . $attrib . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
				}
			} else {
				$this->doc_patterns["id"][] = "/<(we:" . $tag . $spacer . "[^>]*[\040|\n|\t|\r]+" . $attribut . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
			}
		}

		$this->doc_patterns["id"][] = "/<(we:include" . $spacer . "[^>]*[\040|\n|\t|\r]+type" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]+document[\"|\']+" . $spacer . "[^>]*[\040|\n|\t|\r]+id" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
		$this->doc_patterns["id"][] = "/<(we:include" . $spacer . "[^>]*[\040|\n|\t|\r]+id" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]+" . $spacer . "type" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]+document[\"|\']+" . $spacer . "[^>]*)>/sie";

		$this->doc_patterns["id"][] = "/<(we:include" . $spacer . "[^>]*[\040|\n|\t|\r]+id" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)[^>]*>/sie";

		//replace #WE:1223#
		$this->doc_patterns["id"][] = "/#(WE:)(\d+)#/se";


		// serach for documents after path
		$this->doc_patterns["path"][] = "/<(we:include" . $spacer . "[^>]*[\040|\n|\t|\r]+path" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";

		//search for objects
		$this->obj_patterns["id"][] = "/<(we:object" . $spacer . "[^>]*[\040|\n|\t|\r]+id" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
		$this->obj_patterns["id"][] = "/<(we:form" . $spacer . "[^>]*[\040|\n|\t|\r]+type" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]+object[\"|\']+" . $spacer . "[^>]*[\040|\n|\t|\r]+id" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
		$this->obj_patterns["id"][] = "/<(we:form" . $spacer . "[^>]*[\040|\n|\t|\r]+id" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*[\040|\n|\t|\r]+type" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]+object[\"|\']+" . $spacer . "[^>]*)>/sie";

		// search for classes
		$_pats = array(
			'form' => 'classid',
			'object' => 'classid',
			'listview' => 'classid'
		);
		foreach($_pats as $tag => $attribut){
			$this->class_patterns[] = "/<(we:" . $tag . $spacer . "[^>]*[\040|\n|\t|\r]+" . $attribut . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
		}

		// search for external files
		$_pats = array(
			'img' => 'src',
			'a' => 'href',
			'body' => 'background',
			'table' => 'background',
			'td' => 'background'
		);
		foreach($_pats as $tag => $attribut){
			$this->ext_patterns[] = "/<(" . $tag . $spacer . "[^>]*[\040|\n|\t|\r]+" . $tag . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
		}

		// search wysiwyg textareas
		$this->wysiwyg_patterns["doc"][] = "/([src|href]+" . $spacer . "\=" . $spacer . "\"" . we_base_link::TYPE_INT_PREFIX . ")([0-9]+)(\")/sie";
		$this->wysiwyg_patterns["obj"][] = "/(href" . $spacer . "\=" . $spacer . "\"" . we_base_link::TYPE_OBJ_PREFIX . ")([0-9]+)(\")/sie";

		// handle templates
		$_tmpl_pats = array(
			'ifTemplate' => 'id',
			'ifNotTemplate' => 'id'
		);

		foreach($_tmpl_pats as $tag => $attribut){
			$this->tmpl_patterns[] = "/<(we:" . $tag . $spacer . "[^>]*[\040|\n|\t|\r]+" . $attribut . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
		}

		$this->tmpl_patterns[] = "/<(we:include" . $spacer . "[^>]*[\040|\n|\t|\r]+type" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]+template[\"|\']+" . $spacer . "[^>]*[\040|\n|\t|\r]+id" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
		$this->tmpl_patterns[] = "/<(we:include" . $spacer . "[^>]*[\040|\n|\t|\r]+id" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]+" . $spacer . "type" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]+template[\"|\']+" . $spacer . "[^>]*)>/sie";
		$this->tmpl_patterns[] = "/<(we:field" . $spacer . "[^>]*[\040|\n|\t|\r]+tid" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";

		// search for navigation
		$this->navigation_patterns[] = "/<(we:navigation[^>]*[\040|\n|\t|\r]+id" . $spacer . "[\=\"|\=\'|\=\\\\|\=]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
		$this->navigation_patterns[] = "/<(we:navigation[^>]*[\040|\n|\t|\r]+parentid" . $spacer . "[\=\"|\=\'|\=\\\\|\=]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";

		// search for thumbnails
		$this->thumbnail_patterns[] = "/<(we:img[^>]*[\040|\n|\t|\r]+thumbnail" . $spacer . "[\=\"|\=\'|\=\\\\|\=]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
		$this->thumbnail_patterns[] = "/<(we:field[^>]*[\040|\n|\t|\r]+thumbnail" . $spacer . "[\=\"|\=\'|\=\\\\|\=]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";

		// some special patterns
		$this->special_patterns[] = "/<(we:include" . $spacer . "[^>]*[\040|\n|\t|\r]+id" . $spacer . "\=" . $spacer . "[\"|\'|\\\\]*" . $spacer . ")([^\'\">\040? \\\]*)(" . $spacer . "[^>]*)>/sie";
	}

}
