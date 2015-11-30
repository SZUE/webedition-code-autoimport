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
	public $doc_patterns = array(
		'id' => array(
			'/<(we:include\s[^>]*\s+type\s*=\s*[\"\'\\\\]+document[\"|\']+\s[^>]*\s+id\s\=\s[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*)>/sie',
			'/<(we:include\s[^>]*\s+id\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]+\stype\s\=\s[\"\'\\\\]+document[\"|\']+\s[^>]*)>/sie',
			'/<(we:include\s[^>]*\s+id\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)[^>]*>/sie',
			//replace #WE:1223#
			'/(#WE:)(\d+)(#)/se'
		),
		'path' => array(
			// serach for documents after path
			"/<(we:include\s[^>]*\s+path" . '\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*)>/sie',
		)
	);
	public $obj_patterns = array('id' => array(
			//search for objects
			'/<(we:object\s[^>]*\s+id\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*)>/sie',
			'/<(we:form\s[^>]*\s+type\s*=\s*[\"\'\\\\]+object[\"|\']+\s[^>]*\s+id\s\=\s[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*)>/sie',
			'/<(we:form\s[^>]*\s+id\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*\s+type\s\=\s[\"\'\\\\]+object[\"|\']+\s[^>]*)>/sie',
		), 'path' => array());
	public $class_patterns = array();
	public $ext_patterns = array();
	public $wysiwyg_patterns = array(
		'doc' => array(
			// search wysiwyg textareas
			"/([src|href]+\s\=\s\"" . we_base_link::TYPE_INT_PREFIX . ')([0-9]+)(\")/sie',
		),
		"obj" => array(
			"/(href\s\=\s\"" . we_base_link::TYPE_OBJ_PREFIX . ')([0-9]+)(\")/sie'
		)
	);
	public $navigation_patterns = array(
		'/<(we:navigation[^>]*\s+id\s*=["\'\\\\]+\s*)([^\'"> ? \\\]*)(\s[^>]*)>/sie',
		'/<(we:navigation[^>]*\s+parentid\s*=["\'\\\\]+\s*)([^\'\"> ? \\\]*)(\s*[^>]*)>/sie'
	);
	public $thumbnail_patterns = array(
		// search for thumbnails
		'/<(we:img[^>]*\s+thumbnail\s*=["\'\\\\]+\s*)([^\'\"> ? \\\]*)(\s*[^>]*)>/sie',
		'/<(we:field[^>]*\s+thumbnail\s*=["\'\\\\]+\s*)([^\'\"> ? \\\]*)(\s*[^>]*)>/sie',
	);
	public $tmpl_patterns = array(
		'/<(we:include\s[^>]*\s+type\s*=\s*[\"\'\\\\]+template[\"|\']+\s[^>]*\s+id\s\=\s[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*)>/sie',
		'/<(we:include\s[^>]*\s+id\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]+\stype\s\=\s[\"\'\\\\]+template[\"|\']+\s[^>]*)>/sie',
		'/<(we:field\s[^>]*\s+tid\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*)>/sie',
	);
	public $special_patterns = array(
		// some special patterns
		'/<(we:include\s*[^>]*\s+id\s*=\s*[\"\'\\\\]*\s*)([^\'\"> ? \\\]*)(\s*[^>]*)>/sie'
	);

	public function __construct(){
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
					$this->doc_patterns['id'][] = '/<(we:' . $tag . '\s[^>]*\s+' . $attrib . '\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*)>/sie';
				}
			} else {
				$this->doc_patterns['id'][] = '/<(we:' . $tag . '\s[^>]*\s+' . $attribut . '\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*)>/sie';
			}
		}

		// search for classes
		$_pats = array(
			'form' => 'classid',
			'object' => 'classid',
			'listview' => 'classid'
		);
		foreach($_pats as $tag => $attribut){
			$this->class_patterns[] = '/<(we:' . $tag . '\s[^>]*\s+' . $attribut . '\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*)>/sie';
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
			$this->ext_patterns[] = '/<(' . $tag . '\s*[^>]*\s+' . $tag . '\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*)>/sie';
		}


		// handle templates
		$_tmpl_pats = array(
			'ifTemplate' => 'id',
			'ifNotTemplate' => 'id'
		);

		foreach($_tmpl_pats as $tag => $attribut){
			$this->tmpl_patterns[] = '/<(we:' . $tag . '\s[^>]*\s+' . $attribut . '\s*=\s*[\"\'\\\\]*\s)([^\'\"> ? \\\]*)(\s[^>]*)>/sie';
		}
	}

}
