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
/* * New Format for classes: we_Domain_specific
 * 1. ALL classes start with we_
 * 2. ALL files in one domain
 * 3. all files of one domain reside in the same directory
 * 4. if further division is needed, create a subdomain which does not contain _ !
 * 5. domains are looked up in this class
 * 6. NOTE that files MUST end with .class.php
 */

abstract class we_tool_autoloader{
	private static $domains = [
		'backup' => 'we_classes/backup',
		'banner' => 'we_modules/banner',
		'base' => 'we_classes/base',
		'cache' => 'we_classes/cache',
		'captcha' => 'we_classes/captcha',
		'chooser' => 'we_classes/chooser',
		'contents'=> 'we_classes/contents',
		'customer' => 'we_modules/customer',
		'database' => 'we_classes/database',
		'dialog' => 'we_classes/dialog',
		'doclist' => 'we_classes/doclist',
		'document' => 'we_classes/contents',
		'editor' => 'we_editors',
		'exim' => 'we_classes/exim',
		'export' => 'we_modules/export',
		'fileupload' => 'we_classes/fileupload',
		'fragment' => 'we_classes/fragment',
		'glossary' => 'we_modules/glossary',
		'gui' => 'we_classes/gui',
		'helpers' => 'we_classes/helpers',
		'hook' => 'we_hook/class',
		'html' => 'we_classes/html',
		'http' => 'we_classes/http',
		'import' => 'we_classes/import',
		'listview' => 'we_classes/listview',
		'mail' => 'we_classes/mail',
		'main' => 'we_classes/main',
		'metadata' => 'we_classes/weMetaData',
		'modules' => 'we_classes/modules',
		'navigation' => 'we_modules/navigation',
		'newsletter' => 'we_modules/newsletter',
		'object' => 'we_modules/object',
		'rebuild' => 'we_classes/rebuild',
		'rpc' => 'we_classes/rpc',
		'rpcCmd' => 'we_classes/rpc/cmd',
		'rpcView' => 'we_classes/rpc/view',
		'sdk' => 'we_classes/sdk',
		'search' => 'we_modules/weSearch',
		'selector' => 'we_classes/selector',
		'shop' => 'we_modules/shop',
		'sidebar' => 'we_classes/sidebar',
		'tag' => 'we_classes/tag',
		'tagData' => 'we_classes/tagData',
		'tool' => 'we_classes/tools',
		'tree' => 'we_classes/tree',
		'users' => 'we_modules/users',
		'validation' => 'we_classes/validation',
		'versions' => 'we_classes/versions',
		'view' => 'we_classes/view',
		'voting' => 'we_modules/voting',
		'widget' => 'we_classes/widget',
		'wizard' => 'we_classes/wizard',
		'workflow' => 'we_modules/workflow',
		'wysiwyg' => 'we_classes/wysiwyg',
		'xml' => 'we_classes/xml',
	];
	//fallback classes if local classes do not exist - mostly pear
	private static $fallBack = [
		'Archive_Tar' => 'lib/additional/archive/Archive_Tar.class.php',
		'Console_Getopt' => 'lib/additional/pear/Getopt.php',
		'Image_IPTC' => 'lib/additional/pear/Image_IPTC.class.php',
		'Image_Transform' => 'lib/additional/pear/Image_Transform.class.php',
		'Image_Transform_Driver_GD' => 'lib/additional/pear/Image_Transform_Driver_GD.class.php',
		'Less_Parser' => 'lib/additional/Less/Parser.php',
		'lessc' => 'lib/additional/Less/lessc.inc.php',
		'PEAR' => 'lib/additional/pear/PEAR.php',
		'PEAR_Error' => 'lib/additional/pear/PEAR.php',
		'Services_JSON' => 'lib/additional/pear/Services_JSON.class.php',
		'Services_JSON_Error' => 'lib/additional/pear/Services_JSON.class.php',
		'XML_Parser2' => 'lib/additional/pear/XML_Parser.class.php',
	];
	private static $classes = [
		'we_classes/contents' => [
			'we_binaryDocument' => 'we_binaryDocument.class.php',
			'we_collection' => 'we_collection.class.php',
			'we_document' => 'we_document.class.php',
			'we_flashDocument' => 'we_flashDocument.class.php',
			'we_folder' => 'we_folder.class.php',
			'we_htmlDocument' => 'we_htmlDocument.class.php',
			'we_imageDocument' => 'we_imageDocument.class.php',
			'we_otherDocument' => 'we_otherDocument.class.php',
			'we_template' => 'we_template.class.php',
			'we_temporaryDocument' => 'we_temporaryDocument.class.php',
			'we_textContentDocument' => 'we_textContentDocument.class.php',
			'we_textDocument' => 'we_textDocument.class.php',
			'we_thumbnail' => 'we_thumbnail.class.php',
			'we_webEditionDocument' => 'we_webEditionDocument.class.php',
		],
		'we_classes' => [
			'DB_WE' => 'database/DB_WE.inc.php', //pseudo-element which loads a wrapper, doesn't contain a real class!
			'we_category' => 'we_category.class.php',
			'we_docTypes' => 'we_docTypes.class.php',
			'we_element' => 'we_element.class.php',
			'we_updater' => 'we_updater.class.php',
			'weBinary' => 'weBinary.class.php',
		],
		'we_modules' => [
			'paypal_class' => 'shop/paypal.class.php',
			'we_class_folder' => 'object/we_class_folder.class.php',
			'we_object' => 'object/we_object.class.php',
			'we_objectFile' => 'object/we_objectFile.class.php',
			'we_schedpro' => 'schedule/we_schedpro.class.php',
		],
		'we_classes/tagData' => [//FIXME: remove
			'weTagData' => 'we_tagData_base.class.php',
			'weTagDataAttribute' => 'we_tagData_attribute.class.php',
			'weTagDataOption' => 'we_tagData_option.class.php',
			'weTagData_choiceAttribute' => 'we_tagData_choiceAttribute.class.php',
			'weTagData_cmdAttribute' => 'we_tagData_cmdAttribute.class.php',
			'weTagData_linkAttribute' => 'we_tagData_linkAttribute.class.php',
			'weTagData_multiSelectorAttribute' => 'we_tagData_multiSelectorAttribute.class.php',
			'weTagData_selectAttribute' => 'we_tagData_selectAttribute.class.php',
			'weTagData_selectorAttribute' => 'we_tagData_selectorAttribute.class.php',
			'weTagData_sqlColAttribute' => 'we_tagData_sqlColAttribute.class.php',
			'weTagData_sqlRowAttribute' => 'we_tagData_sqlRowAttribute.class.php',
			'weTagData_textAttribute' => 'we_tagData_textAttribute.class.php',
			'weTagData_typeAttribute' => 'we_tagData_typeAttribute.class.php',
		],
		WEBEDITION_DIR . 'liveUpdate/classes' => [
			'liveUpdateFrames' => 'liveUpdateFrames.class.php'
		]
	];

	/**
	 * default webEdition autoloader
	 * @param type $class_name
	 */
	public static function autoload($class_name){
		//no we-class
		//FIXME: this should be expected in future
		@list($where, $domain) = explode('_', $class_name, 3);
		switch($where){
			case 'we':
				@list(, $domain) = explode('_', $class_name);
				if(!isset(self::$domains[$domain])){
					//				t_e('Error class domain not set in autoloader!');
				} else {
					if(file_exists(WE_INCLUDES_PATH . self::$domains[$domain] . '/' . $class_name . '.class.php')){
						include(WE_INCLUDES_PATH . self::$domains[$domain] . '/' . $class_name . '.class.php');
						return true;
					}
				}
				break;
			case 'Horde':
				include(WE_LIB_PATH . 'additional/' . str_replace('_', '/', $class_name) . '.php');
				return true;
			case 'PMA':
				list(, $name) = explode('_', $class_name, 2);
				//t_e(WE_APPS_PATH.'wephpmyadmin/phpMyAdmin/libraries/'.$domain.'.class.php');
				include(WE_APPS_PATH . 'wephpmyadmin/phpMyAdmin/libraries/' . $name . '.class.php');
				return true;
			/* case 'Less':
			  include_once(WE_LIB_PATH . 'additional/Less/Autoloader.php');
			  return Less_Autoloader::loadClass($class_name); */
		}

		foreach(self::$classes as $path => $array){
			if(array_key_exists($class_name, $array)){
				$path = (substr($path, 0, 1) === '/' ? $_SERVER['DOCUMENT_ROOT'] . $path . '/' : WE_INCLUDES_PATH . $path . '/');
				include_once($path . $array[$class_name]);
				return true;
			}
		}
		//might be a zend registered class:
		if(isset(self::$fallBack[$class_name])){
			include(WEBEDITION_PATH . self::$fallBack[$class_name]);
			return true;
		}
		//don't load zend extension, if file is in system or fallback
		//} else {// add this loader at the end, if class was not yet found
		spl_autoload_register('we_tool_autoloader::finalLoad', true);
		//}
		//will try next auto-loader
	}

	/**
	 * Added after Zend-Loader to trigger not found classes
	 * @param type $class_name
	 */
	public static function finalLoad($class_name){
		if(isset(self::$fallBack[$class_name])){
			include(WEBEDITION_PATH . self::$fallBack[$class_name]);
			return true;
		}
		t_e('notice', 'we_tool_autoloader: class ' . $class_name . ' not found');
	}

}
