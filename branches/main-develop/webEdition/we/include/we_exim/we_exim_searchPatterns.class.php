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
	public $doc_patterns = [
		'id' => ['/<(we:include\s*[^>]*\stype\s*=\s*[\"\']document[\"\'][^>]*\sid\s*=\s*[\"\'])(\d+)([\"\'][^>]*)>/si',
			'/<(we:include\s*[^>]*\sid\s*=\s*[\"\'])(\d+)([\"\']\stype\s*=\s*["\']document["\'][^>]*)>/si',
			//replace #WE:1223#
			'/(#WE:)(\d+)(#)/se'
			],
		'path' => ['/<(we:include\s*[^>]*\spath\s*=\s*[\"\'])([^\'\"> ? \\\]+)([\"\'][^>]*)>/si',
			]
];
	public $obj_patterns = [
		'id' => ['/<(we:object\s*[^>]*\sid\s*=\s*[\"\'])(\d+)([\"\'][^>]*)>/si',
			'/<(we:form\s*[^>]*\stype\s*=\s*[\"\']object[\"\'][^>]*\sid\s*=\s*[\"\'])(\d+)([\"\'][^>]*)>/si',
			'/<(we:form\s*[^>]*\sid\s*=\s*[\"\'])(\d+)([\"\'][^>]*\stype\s*=\s*[\"\']object[\"\'][^>]*)>/si',
		 ],
		'path' => []];
	public $class_patterns = [];
	public $ext_patterns = [];
	public $wysiwyg_patterns;
	public $navigation_patterns = ['/<(we:navigation\s*[^>]*\sid\s*=["\'])(\d+)(["\'][^>]*)>/si',
		'/<(we:navigation\s*[^>]*\sparentid\s*=["\'])(\d+)(["\'][^>]*)>/si'
	 ];
	public $thumbnail_patterns = ['/<(we:img\s*[^>]*\sthumbnail\s*=["\'])([^\'\"> ? \\\]+)(["\'][^>]*)>/si',
		'/<(we:field\s*[^>]*\sthumbnail\s*=["\'])([^\'\"> ? \\\]+)(["\'][^>]*)>/si',
	 ];
	public $tmpl_patterns = ['/<(we:include\s*[^>]*\stype\s*=\s*["\']template["\'][^>]*\sid\s*=\s*["\'])(\d+)([\"\'][^>]*)>/si',
		'/<(we:include\s*[^>]*\sid\s*=\s*["\'])(\d+)([\"\']\s+type\s*=\s*["\']template["\'][^>]*)>/si',
		'/<(we:field\s*[^>]*\stid\s*=\s*[\"\'])(\d+)(["\'][^>]*)>/si',
	 ];
	public $special_patterns = ['/<(we:include\s*[^>]*\sid\s*=\s*[\"\'])(\d+)(["\'][^>]*)>/si'
		];

	public function __construct(){
		$this->wysiwyg_patterns = ['doc' => ['/(href\s*=\s*[\'\"]' . we_base_link::TYPE_INT_PREFIX . ')(\d+)([^\"\']*[\"\'])/si',
				'/(src\s*=\s*[\'\"]' . we_base_link::TYPE_INT_PREFIX . ')(\d+)([^\"\']*[\"\'])/si',
				],
			'obj' => ['/(href\s*=\s*[\"\']' . we_base_link::TYPE_OBJ_PREFIX . ')(\d+)([^\"\']*[\"\'])/si'
				]
		];
		$pats = ['a' => 'id',
			'addDelNewsletterEmail' => ['id', 'mailid'],
			'css' => 'id',
			'form' => ['id', 'onsuccess', 'onerror', 'onmailerror', 'onrecipienterror'],
			'icon' => 'id',
			'img' => ['id', 'startid', 'parentid'],
			'flashmovie' => ['startid', 'parentid'],
			'video' => ['id', 'startid', 'parentid'],
			'js' => 'id',
			'linkToSeeMode' => 'id',
			'url' => 'id',
			'ifSelf' => 'id',
			'object' => 'triggerid',
			'listview' => ['id', 'triggerid', 'workspaceID'],
			'sessionLogout' => 'id',
			'field' => 'id'
			];

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
		$pats = ['form' => 'classid',
			'object' => 'classid',
			'listview' => 'classid'
			];
		foreach($pats as $tag => $attribut){
			$this->class_patterns[] = '/<(we:' . $tag . '\s*[^>]*\s' . $attribut . '\s*=\s*[\"\'])(\d+)(["\'][^>]*)>/si';
		}

		// search for external files
		$pats = ['img' => 'src',
			'a' => 'href',
			'body' => 'background',
			'table' => 'background',
			'td' => 'background'
			];
		foreach($pats as $tag => $attribut){
			$this->ext_patterns[] = '/<(' . $tag . '\s*[^>]*' . $tag . '\s*=\s*[\"\'])([^\'\"> ? \\\]+)(["\'][^>]*)>/si';
		}


		// handle templates
		$tmpl_pats = ['ifTemplate' => 'id',
			'ifNotTemplate' => 'id'
			];

		foreach($tmpl_pats as $tag => $attribut){
			$this->tmpl_patterns[] = '/<(we:' . $tag . '\s*[^>]*\s' . $attribut . '\s*=\s*[\"\'])(\d+)(["\'][^>]*)>/si';
		}
	}

}
