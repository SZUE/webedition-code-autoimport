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
		'database' => 'we_classes/database',
		'captcha' => 'we_classes/captcha',
		'customer' => 'we_modules/customer',
		'dialog' => 'we_classes/dialog',
		'editor' => 'we_editors',
		'exim' => 'we_exim',
		'export' => 'we_modules/export',
		'fileupload' => 'we_classes/fileupload',
		'glossary' => 'we_modules/glossary',
		'helpers' => 'we_classes/helpers',
		'html' => 'we_classes/html',
		'http' => 'we_classes/http',
		'import' => 'we_import',
		'main' => 'we_classes/main',
		'messaging' => 'we_modules/messaging',
		'metadata' => 'we_classes/weMetaData',
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
		'voting' => 'we_modules/voting',
		'workflow' => 'we_modules/workflow',
		'wysiwyg' => 'we_classes/wysiwyg',
		'xml' => 'we_classes/xml',
	);
	//fallback classes if local classes do not exist - mostly pear
	private static $fallBack = array(
		'Archive_Tar' => 'lib/additional/archive/Archive_Tar.class.php',
		'PEAR5' => 'lib/additional/pear/PEAR5.php',
		'PEAR_Error' => 'lib/additional/pear/PEAR.php',
		'PEAR' => 'lib/additional/pear/PEAR.php',
		'Services_JSON_Error' => 'lib/additional/pear/Services_JSON.class.php',
		'Services_JSON' => 'lib/additional/pear/Services_JSON.class.php',
		'Image_Transform_Driver_GD' => 'lib/additional/pear/Image_Transform_Driver_GD.class.php',
		'Image_Transform' => 'lib/additional/pear/Image_Transform.class.php',
		'Image_IPTC' => 'lib/additional/pear/Image_IPTC.class.php',
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
		),
		'we_classes' => array(
			'Console_Getopt' => 'Getopt.php',
			'copyFolderFinishFrag' => 'we_copyFolderFinishFrag.class.php',
			'copyFolderFrag' => 'we_copyFolderFrag.class.php',
			'DB_WE' => 'database/DB_WE.inc.php', //pseudo-element which loads a wrapper, doesn't contain a real class!
			'listviewBase' => 'listview/listviewBase.class.php',
			'metadatatag' => 'listview/metadatatag.class.php',
			'permissionhandler' => 'permissionhandler/permissionhandler.class.php',
			'taskFragment' => 'taskFragment.class.php',
			'weBinary' => 'weBinary.class.php',
			'we_category' => 'we_category.class.php',
			'we_catListview' => 'listview/we_catListview.class.php',
			'we_docTypes' => 'we_docTypes.class.php',
			'we_element' => 'we_element.inc.php',
			'we_history' => 'we_history.class.php',
			'we_langlink_listview' => 'listview/we_langlink_listview.class.php',
			'we_listview' => 'listview/we_listview.class.php',
			'weMainTree' => 'weMainTree.inc.php',
			'weModelBase' => 'modules/weModelBase.php',
			'weModuleFrames' => 'modules/weModuleFrames.php',
			'weModuleView' => 'modules/weModuleView.class.php',
			'weModuleTree' => 'modules/weModuleTree.class.php',
			'weOrderContainer' => 'js_gui/weOrderContainer.class.php',
			'we_progressBar' => 'we_progressBar.inc.php',
			'we_rtf2html' => 'we_rtf2html.inc.php',
			'we_search_listview' => 'listview/we_search_listview.class.php',
			'we_search' => 'we_search.inc.php',
			'we_SEEM' => 'SEEM/we_SEEM.class.php',
			'weSuggest' => 'weSuggest.class.php',
			'we_tabs' => 'we_tabs.class.php',
			'we_tab' => 'we_tab.class.php',
			'weTree' => 'weTree.inc.php',
			'we_updater' => 'we_updater.inc.php',
			'we_widget' => 'we_widget.inc.php',
			'XML_Parser_Error' => 'Parser.php',
			'XML_Parser' => 'Parser.php',
			'weToolLookup' => 'tools/we_tool_lookup.class.php',
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
		'weCodeWizard/classes' => array(
			'weCodeWizard' => 'weCodeWizard.inc.php',
			'weCodeWizardSnippet' => 'weCodeWizardSnippet.inc.php',
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
			'weTagWizard' => 'weTagWizard.class.php',
		),
		'we_doclist' => array(
			'doclistView' => 'doclistView.class.php',
		),
		'we_logging' => array(
			'logging' => 'logging.class.php',
			'versionsLog' => 'versions/versionsLog.class.php',
			'versionsLogView' => 'versions/versionsLogView.class.php',
		),
		'we_message_reporting' => array(
			'we_message_reporting' => 'we_message_reporting.class.php',
		),
		'we_tools' => array(
			'MultiDirAndTemplateChooser' => 'MultiDirAndTemplateChooser.inc.php',
			'MultiDirChooser' => 'MultiDirChooser.inc.php',
			'MultiDirChooser2' => 'MultiDirChooser2.inc.php',
			'MultiDirTemplateAndDefaultChooser' => 'MultiDirTemplateAndDefaultChooser.inc.php',
			'MultiFileChooser' => 'MultiFileChooser.inc.php',
			'treePopup' => 'treePopup.inc.php',
		),
		'we_versions' => array(
			'versionFragment' => 'version_wizard/versionFragment.class.php',
			'we_version' => 'version_wizard/we_version.class.php',
			'we_versions_wizard' => 'version_wizard/we_versions_wizard.inc.php',
			'weVersions' => 'weVersions.class.php',
			'weVersionsSearch' => 'weVersionsSearch.class.php',
			'weVersionsView' => 'weVersionsView.class.php',
		),
		'we_widgets/dlg' => array(
			'weExportTree' => 'tree.inc.php',
		),
	);

	public static function loadZend($class_name){
		//echo 'load zend beacause of'.$class_name;
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
			case 'Zend':
				self::loadZend($class_name);
				return false;
		}

		foreach(self::$classes as $path => $array){
			if(array_key_exists($class_name, $array)){
				$path = (substr($path, 0, 1) == '/' ? $_SERVER['DOCUMENT_ROOT'] . $path : WE_INCLUDES_PATH . $path . '/');
				include($path . $array[$class_name]);
				return true;
			}
		}
		//might be a zend registered class:
		self::loadZend($class_name);
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
