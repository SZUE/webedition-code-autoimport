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

abstract class we_autoloader{
	private static $domains = array(
		'backup' => 'we_exim/backup',
		'banner' => 'we_modules/banner',
		'base' => 'we_classes/base',
		'cache' => 'we_classes/cache',
		'database' => 'we_classes/database',
		'captcha' => 'we_classes/captcha',
		'chooser' => 'we_classes/chooser',
		'customer' => 'we_modules/customer',
		'dialog' => 'we_classes/dialog',
		'doclist' => 'we_classes/doclist',
		'document' => 'we_classes/contents',
		'editor' => 'we_editors',
		'exim' => 'we_exim',
		'export' => 'we_modules/export',
		'fileupload' => 'we_classes/fileupload',
		'fragment' => 'we_classes/fragment',
		'glossary' => 'we_modules/glossary',
		'gui'=>'we_classes/js_gui',
		'helpers' => 'we_classes/helpers',
		'html' => 'we_classes/html',
		'http' => 'we_classes/http',
		'import' => 'we_import',
		'listview' => 'we_classes/listview',
		'main' => 'we_classes/main',
		'messaging' => 'we_modules/messaging',
		'metadata' => 'we_classes/weMetaData',
		'modules' => 'we_classes/modules',
		'navigation' => 'we_modules/navigation/class',
		'newsletter' => 'we_modules/newsletter',
		'object' => 'we_modules/object',
		'rebuild' => 'we_classes/rebuild',
		'sdk' => 'we_classes/sdk',
		'search' => 'we_tools/weSearch/class',
		'selector' => 'we_classes/selector',
		'sidebar' => 'we_classes/sidebar',
		'users' => 'we_modules/users',
		'shop' => 'we_modules/shop',
		'tag' => 'we_classes/tag',
		'tool' => 'we_classes/tools',
		'versions' => 'we_versions',
		'view' => 'we_classes/view',
		'voting' => 'we_modules/voting',
		'wizard' => 'we_classes/wizard',
		'workflow' => 'we_modules/workflow',
		'wysiwyg' => 'we_classes/wysiwyg',
		'xml' => 'we_classes/xml',
	);
	//fallback classes if local classes do not exist - mostly pear
	private static $fallBack = array(
		'Archive_Tar' => 'lib/additional/archive/Archive_Tar.class.php',
		'Console_Getopt' => 'lib/additional/pear/Getopt.php',
		'Image_Transform_Driver_GD' => 'lib/additional/pear/Image_Transform_Driver_GD.class.php',
		'Image_Transform' => 'lib/additional/pear/Image_Transform.class.php',
		'Image_IPTC' => 'lib/additional/pear/Image_IPTC.class.php',
		'lessc' => 'lib/additional/Less/lessc.inc.php',
		'PEAR5' => 'lib/additional/pear/PEAR5.php',
		'PEAR_Error' => 'lib/additional/pear/PEAR.php',
		'PEAR' => 'lib/additional/pear/PEAR.php',
		'Services_JSON_Error' => 'lib/additional/pear/Services_JSON.class.php',
		'Services_JSON' => 'lib/additional/pear/Services_JSON.class.php',
		'XML_Parser_Error' => 'lib/additional/pear/XML_Parser.class.php',
		'XML_Parser' => 'lib/additional/pear/XML_Parser.class.php',
	);
	private static $classes = array(
		'we_classes/contents' => array(
			'we_binaryDocument' => 'we_binaryDocument.class.php',
			'we_class' => 'we_class.class.php',
			'we_document' => 'we_document.class.php',
			'we_flashDocument' => 'we_flashDocument.class.php',
			'we_folder' => 'we_folder.class.php',
			'we_htmlDocument' => 'we_htmlDocument.class.php',
			'we_imageDocument' => 'we_imageDocument.class.php',
			'we_otherDocument' => 'we_otherDocument.class.php',
			'we_quicktimeDocument' => 'we_quicktimeDocument.class.php',
			'we_root' => 'we_root.class.php',
			'we_template' => 'we_template.class.php',
			'we_temporaryDocument' => 'we_temporaryDocument.class.php',
			'we_textContentDocument' => 'we_textContentDocument.class.php',
			'we_textDocument' => 'we_textDocument.class.php',
			'we_thumbnail' => 'we_thumbnail.class.php',
			'we_webEditionDocument' => 'we_webEditionDocument.class.php',
			'we_collection' => 'we_collection.class.php',
		),
		'we_classes' => array(
			'doclistView' => 'doclistView.class.php',
			'DB_WE' => 'database/DB_WE.inc.php', //pseudo-element which loads a wrapper, doesn't contain a real class!
			'permissionhandler' => 'permissionhandler/permissionhandler.class.php',
			'weBinary' => 'weBinary.class.php',
			'we_category' => 'we_category.class.php',
			'we_docTypes' => 'we_docTypes.class.php',
			'we_element' => 'we_element.class.php',
			'we_history' => 'we_history.class.php',
			'weMainTree' => 'weMainTree.class.php',
			'weModelBase' => 'modules/weModelBase.class.php',
			'we_progressBar' => 'we_progressBar.class.php',
			'we_SEEM' => 'SEEM/we_SEEM.class.php',
			'weSuggest' => 'weSuggest.class.php',
			'we_tabs' => 'we_tabs.class.php',
			'we_tab' => 'we_tab.class.php',
			'weTree' => 'weTree.class.php',
			'we_updater' => 'we_updater.class.php',
			'weToolLookup' => 'tools/we_tool_lookup.class.php',
			'we_message_reporting' => 'we_message_reporting.class.php',
		),
		'we_modules' => array(
			'we_class_folder' => 'object/we_class_folder.class.php',
			'we_object' => 'object/we_object.class.php',
			'we_objectFile' => 'object/we_objectFile.class.php',
			'we_schedpro' => 'schedule/we_schedpro.class.php',
			'paypal_class' => 'shop/paypal.class.php',
			'shop' => 'shop/we_shop_shop.class.php',
			'Basket' => 'shop/we_shop_Basket.class.php',
		),
		'we_hook/class' => array(
			'weHook' => 'weHook.class.php',
		),
		'validation' => array(
			'validation' => 'validation.class.php',
			'validationService' => 'validationService.class.php',
		),
		'weTagWizard/classes' => array(
			'weTagData' => 'weTagData.class.php',
			'weTagDataAttribute' => 'weTagDataAttribute.class.php',
			'weTagDataOption' => 'weTagDataOption.class.php',
			'weTagData_choiceAttribute' => 'weTagData_choiceAttribute.class.php',
			'weTagData_cmdAttribute' => 'weTagData_cmdAttribute.class.php',
			'weTagData_linkAttribute' => 'weTagData_linkAttribute.class.php',
			'weTagData_multiSelectorAttribute' => 'weTagData_multiSelectorAttribute.class.php',
			'weTagData_selectAttribute' => 'weTagData_selectAttribute.class.php',
			'weTagData_selectorAttribute' => 'weTagData_selectorAttribute.class.php',
			'weTagData_sqlColAttribute' => 'weTagData_sqlColAttribute.class.php',
			'weTagData_sqlRowAttribute' => 'weTagData_sqlRowAttribute.class.php',
			'weTagData_textAttribute' => 'weTagData_textAttribute.class.php',
			'weTagData_typeAttribute' => 'weTagData_typeAttribute.class.php',
		),
	);

	public static function loadZend($class_name){
		//t_e('load zend because of', $class_name);
		if(!class_exists('Zend_Loader_Autoloader', false)){
			require_once('Zend/Loader/Autoloader.php');
			$loader = Zend_Loader_Autoloader::getInstance(); #3815
			$loader->setFallbackAutoloader(true); #3815
			$loader->suppressNotFoundWarnings(true);
			spl_autoload_register('we_autoloader::finalLoad', true);
		}
	}

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
			case 'Zend':
				self::loadZend($class_name);
				return false;
		}

		foreach(self::$classes as $path => $array){
			if(array_key_exists($class_name, $array)){
				$path = (substr($path, 0, 1) === '/' ? $_SERVER['DOCUMENT_ROOT'] . $path : WE_INCLUDES_PATH . $path . '/');
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
		self::loadZend($class_name);
		//} else {// add this loader at the end, if class was not yet found
		spl_autoload_register('we_autoloader::finalLoad', true);
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
		} else {
			t_e('info', 'we_autoloader: class ' . $class_name . ' not found');
		}
	}

}
