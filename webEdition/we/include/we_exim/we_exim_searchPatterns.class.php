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
			'/<(we:include\s*[^>]*\stype\s*=\s*[\"\']document[\"\'][^>]*\sid\s*=\s*[\"\'])(\d+)([\"\'][^>]*)>/si',
			'/<(we:include\s*[^>]*\sid\s*=\s*[\"\'])(\d+)([\"\']\stype\s*=\s*["\']document["\'][^>]*)>/si',
			//replace #WE:1223#
			'/(#WE:)(\d+)(#)/se'
		),
		'path' => array(
			// search for documents after path
			'/<(we:include\s*[^>]*\spath\s*=\s*[\"\'])([^\'\"> ? \\\]+)([\"\'][^>]*)>/si',
		)
	);
	public $obj_patterns = array(
		'id' => array(
			//search for objects
			'/<(we:object\s*[^>]*\sid\s*=\s*[\"\'])(\d+)([\"\'][^>]*)>/si',
			'/<(we:form\s*[^>]*\stype\s*=\s*[\"\']object[\"\'][^>]*\sid\s*=\s*[\"\'])(\d+)([\"\'][^>]*)>/si',
			'/<(we:form\s*[^>]*\sid\s*=\s*[\"\'])(\d+)([\"\'][^>]*\stype\s*=\s*[\"\']object[\"\'][^>]*)>/si',
		),
		'path' => []);
	public $class_patterns = [];
	public $ext_patterns = [];
	public $wysiwyg_patterns;
	public $navigation_patterns = array(
		'/<(we:navigation\s*[^>]*\sid\s*=["\'])(\d+)(["\'][^>]*)>/si',
		'/<(we:navigation\s*[^>]*\sparentid\s*=["\'])(\d+)(["\'][^>]*)>/si'
	);
	public $thumbnail_patterns = array(
		// search for thumbnails
		'/<(we:img\s*[^>]*\sthumbnail\s*=["\'])([^\'\"> ? \\\]+)(["\'][^>]*)>/si',
		'/<(we:field\s*[^>]*\sthumbnail\s*=["\'])([^\'\"> ? \\\]+)(["\'][^>]*)>/si',
	);
	public $tmpl_patterns = array(
		'/<(we:include\s*[^>]*\stype\s*=\s*["\']template["\'][^>]*\sid\s*=\s*["\'])(\d+)([\"\'][^>]*)>/si',
		'/<(we:include\s*[^>]*\sid\s*=\s*["\'])(\d+)([\"\']\s+type\s*=\s*["\']template["\'][^>]*)>/si',
		'/<(we:field\s*[^>]*\stid\s*=\s*[\"\'])(\d+)(["\'][^>]*)>/si',
	);
	public $special_patterns = array(
		// some special patterns
		'/<(we:include\s*[^>]*\sid\s*=\s*[\"\'])(\d+)(["\'][^>]*)>/si'
	);

	public function __construct(){
		$this->wysiwyg_patterns = array(
			'doc' => array(
				// search wysiwyg textareas
				'/(href\s*=\s*[\'\"]' . we_base_link::TYPE_INT_PREFIX . ')(\d+)([^\"\']*[\"\'])/si',
				'/(src\s*=\s*[\'\"]' . we_base_link::TYPE_INT_PREFIX . ')(\d+)([^\"\']*[\"\'])/si',
			),
			'obj' => array(
				'/(href\s*=\s*[\"\']' . we_base_link::TYPE_OBJ_PREFIX . ')(\d+)([^\"\']*[\"\'])/si'
			)
		);
		$pats = array(
			'a' => 'id',
			'addDelNewsletterEmail' => array('id', 'mailid'),
			'css' => 'id',
			'form' => array('id', 'onsuccess', 'onerror', 'onmailerror', 'onrecipienterror'),
			'icon' => 'id',
			'img' => array('id', 'startid', 'parentid'),
			'flashmovie' => array('startid', 'parentid'),
			'video' => array('id', 'startid', 'parentid'),
			'js' => 'id',
			'linkToSeeMode' => 'id',
			'url' => 'id',
			'ifSelf' => 'id',
			'object' => 'triggerid',
			'listview' => array('id', 'triggerid', 'workspaceID'),
			'sessionLogout' => 'id',
			'field' => 'id'
		);

		foreach($pats as $tag => $attribut){
			if(is_array($attribut)){
				foreach($attribut as $attrib){
					$this->doc_patterns['id'][] = '/<(we:' . $tag . '\s*[^>]*\s' . $attrib . '\s*=\s*[\"\'])(\d+)(["\'][^>]*)>/si';
				}
			} else {
				$this->doc_patterns['id'][] = '/<(we:' . $tag . '\s*[^>]*\s' . $attribut . '\s*=\s*[\"\'])(\d+)(["\'][^>]*)>/si';
			}
		}

		// search for classes
		$pats = array(
			'form' => 'classid',
			'object' => 'classid',
			'listview' => 'classid'
		);
		foreach($pats as $tag => $attribut){
			$this->class_patterns[] = '/<(we:' . $tag . '\s*[^>]*\s' . $attribut . '\s*=\s*[\"\'])(\d+)(["\'][^>]*)>/si';
		}

		// search for external files
		$pats = array(
			'img' => 'src',
			'a' => 'href',
			'body' => 'background',
			'table' => 'background',
			'td' => 'background'
		);
		foreach($pats as $tag => $attribut){
			$this->ext_patterns[] = '/<(' . $tag . '\s*[^>]*' . $tag . '\s*=\s*[\"\'])([^\'\"> ? \\\]+)(["\'][^>]*)>/si';
		}


		// handle templates
		$tmpl_pats = array(
			'ifTemplate' => 'id',
			'ifNotTemplate' => 'id'
		);

		foreach($tmpl_pats as $tag => $attribut){
			$this->tmpl_patterns[] = '/<(we:' . $tag . '\s*[^>]*\s' . $attribut . '\s*=\s*[\"\'])(\d+)(["\'][^>]*)>/si';
		}
	}

}
