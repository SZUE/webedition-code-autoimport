<?php

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_defines.inc.php');

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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
abstract class we_autoloader{

	private static $classes = array(
		'Captcha' => 'captcha/captcha.class.php',
		'CaptchaImage' => 'captcha/captchaImage.class.php',
		'CaptchaMemory' => 'captcha/captchaMemory.class.php',
		'charsetHandler' => 'charsetHandler.class.php',
		'Console_Getopt' => 'Getopt.php',
		'CSV' => 'csv.inc.php',
		'CSVFixImport' => 'csv.inc.php',
		'CSVImport' => 'csv.inc.php',
		/*DB is never autoloaded
		 * 'DB_WE' => 'we_db_mysql.class.inc.php',
		'DB_WE' => 'we_db_mysqli.class.inc.php',*/
		'delBackup' => 'delete/delBackup.inc.php',
		'deleteProgressDialog' => 'delete/deleteProgressDialog.inc.php',
		'delFragment' => 'delete/delFragment.inc.php',
		'HttpRequest' => 'http/HttpRequest.class.php',
		'HttpResponse' => 'http/HttpResponse.class.php',
		'Image_IPTC' => 'weMetaData/lib/PEAR_IPTC.php',
		'Image_Transform_Driver_GD' => 'Transform/Driver/GD.php',
		'Image_Transform' => 'Transform.php',
		'leWizardCollection' => 'leWizard/leWizardCollection.class.php',
		'leWizardContent' => 'leWizard/leWizardContent.class.php',
		'leWizard' => 'leWizard/leWizard.class.php',
		'leWizardProgress' => 'leWizard/leWizardProgress.class.php',
		'leWizardStatus' => 'leWizard/leWizardStatus.class.php',
		'leWizardStepBase' => 'leWizard/leWizardStepBase.class.php',
		'leWizardTemplateBase' => 'leWizard/leWizardTemplateBase.class.php',
		'listviewBase' => 'listview/listviewBase.class.php',
		'liveUpdateFunctions' => 'leWizard/liveUpdateFunctions.class.php',
		'liveUpdateHttp' => 'leWizard/liveUpdateHttp.class.php',
		'liveUpdateHttpWizard' => 'leWizard/liveUpdateHttpWizard.class.php',
		'liveUpdateResponse' => 'leWizard/liveUpdateResponse.class.php',
		'liveUpdateTemplates' => 'leWizard/liveUpdateTemplates.class.php',
		'liveUpdateTemplatesWizarad' => 'leWizard/liveUpdateTemplatesWizard.class.php',
		'metadatatag' => 'listview/metadatatag.class.php',
		'myFrag' => 'taskFragment.class.php',
		'PEAR5' => 'PEAR5.php',
		'PEAR_Error' => 'PEAR.php',
		'PEAR' => 'PEAR.php',
		'permissionhandler' => 'permissionhandler/permissionhandler.class.php',
		'rebuildFragment' => 'rebuild/rebuildFragment.inc.php',
		'rndConditionPass' => 'utils/rndGenPass.inc.php',
		'Services_JSON_Error' => 'JSON.php',
		'Services_JSON' => 'JSON.php',
		'taskFragment' => 'taskFragment.class.php',
		'usersOnline' => 'we_usersOnline.class.php',
		'weAbbrDialog' => 'weAbbrDialog.class.inc.php',
		'weAcronymDialog' => 'weAcronymDialog.class.inc.php',
		'weBackup' => 'base/weBackup.class.php',
		'we_backup' => 'base/we_backup.inc.php',
		'weBackupWizard' => 'weBackupWizard.inc.php',
		'we_baseCollection' => 'html/we_baseCollection.inc.php',
		'we_baseElement' => 'html/we_baseElement.inc.php',
		'weBinary' => 'base/weBinary.class.php',
		'we_binaryDocument' => 'we_binaryDocument.inc.php',
		'weBrowser' => 'base/weBrowser.class.php',
		'we_button' => 'html/we_button.inc.php',
		'we_category' => 'we_category.inc.php',
		'we_catListview' => 'listview/we_catListview.class.php',
		'we_catSelector' => 'we_catSelector.inc.php',
		'weCellDialog' => 'weCellDialog.class.inc.php',
		'we_class' => 'we_class.inc.php',
		'we_codeConvertor' => 'we_codeConvertor.inc.php',
		'weColorDialog' => 'weColorDialog.class.inc.php',
		'weConfParser' => 'base/weConfParser.class.php',
		'weDBUtil' => 'base/weDBUtil.class.php',
		'we_delSelector' => 'we_delSelector.inc.php',
		'weDialog' => 'weDialog.class.inc.php',
		'we_dirSelector' => 'we_dirSelector.inc.php',
		'we_docSelector' => 'we_docSelector.inc.php',
		'we_docTypes' => 'we_docTypes.inc.php',
		'we_document' => 'we_document.inc.php',
		'we_dynamicControls' => 'html/we_dynamicControls.inc.php',
		'we_element' => 'we_element.inc.php',
		'we_errorHandling' => 'helpers/we_errorHandling.inc.php',
		'weFile' => 'base/weFile.class.php',
		'we_fileselector' => 'we_fileselector.inc.php',
		'we_flashDocument' => 'we_flashDocument.inc.php',
		'we_folder' => 'we_folder.inc.php',
		'we_forms' => 'html/we_forms.inc.php',
		'weFullscreenEditDialog' => 'weFullscreenEditDialog.class.inc.php',
		'we_history' => 'we_history.class.php',
		'we_htmlDocument' => 'we_htmlDocument.inc.php',
		'we_htmlElement' => 'html/we_htmlElement.inc.php',
		'we_htmlFrameset' => 'html/we_htmlFrameset.inc.php',
		'we_htmlSelect' => 'html/we_htmlSelect.inc.php',
		'we_htmlTable' => 'html/we_htmlTable.inc.php',
		'we_html_tools'=>'html/we_html_tools.class.inc.php',
		'weHyperlinkDialog' => 'weHyperlinkDialog.class.inc.php',
		'we_image_crop' => 'base/we_image_crop.class.php',
		'weImageDialog' => 'weImageDialog.class.inc.php',
		'we_imageDocument' => 'we_imageDocument.inc.php',
		'we_image_edit' => 'base/we_image_edit.class.php',
		'weImportRtfDialog' => 'weImportRtfDialog.class.inc.php',
		'weJavaMenu' => 'java_menu/weJavaMenu.inc.php',
		'weLangDialog' => 'weLangDialog.class.inc.php',
		'we_langlink_listview' => 'listview/we_langlink_listview.class.php',
		'we_listview' => 'listview/we_listview.class.php',
		'weMainTree' => 'weMainTree.inc.php',
		'weMetaData_Exif' => 'weMetaData/classes/Exif.class.php',
		'weMetaData_IPTC' => 'weMetaData/classes/IPTC.class.php',
		'weMetaData_PDF' => 'weMetaData/classes/PDF.class.php',
		'weMetaData' => 'weMetaData/weMetaData.class.php',
		'weModelBase' => 'modules/weModelBase.php',
		'weModuleFrames' => 'modules/weModuleFrames.php',
		'we_multiIconBox' => 'html/we_multiIconBox.class.inc.php',
		'we_multiSelector' => 'we_multiSelector.inc.php',
		'weOrderContainer' => 'js_gui/weOrderContainer.class.php',
		'we_otherDocument' => 'we_otherDocument.inc.php',
		'we_progressBar' => 'we_progressBar.inc.php',
		'we_quicktimeDocument' => 'we_quicktimeDocument.inc.php',
		'we_rebuild' => 'rebuild/we_rebuild.class.php',
		'we_rebuild_wizard' => 'rebuild/we_rebuild_wizard.inc.php',
		'we_root' => 'we_root.inc.php',
		'we_rtf2html' => 'we_rtf2html.inc.php',
		'weRuleDialog' => 'weRuleDialog.class.inc.php',
		'we_search_listview' => 'listview/we_search_listview.class.php',
		'we_search' => 'we_search.inc.php',
		'we_SEEM' => 'SEEM/we_SEEM.class.php',
		'weSelectorQuery' => 'weSelectorQuery.class.inc.php',
		'weSidebarDocumentParser' => 'weSidebarDocumentParser.class.php',
		'weSideBarFrames' => 'weSideBarFrames.class.php',
		'weSpecialCharDialog' => 'weSpecialCharDialog.class.inc.php',
		'weSuggest' => 'weSuggest.class.inc.php',
		'weTableAdv' => 'base/weTable.class.php',
		'weTable' => 'base/weTable.class.php',
		'weTableDialog' => 'weTableDialog.class.inc.php',
		'weTableItem' => 'base/weTableItem.class.php',
		'we_tabs' => 'we_tabs.class.inc.php',
		'we_tab' => 'we_tab.class.inc.php',
		'we_template' => 'we_template.inc.php',
		'we_temporaryDocument' => 'we_temporaryDocument.inc.php',
		'we_textContentDocument' => 'we_textContentDocument.inc.php',
		'we_textDocument' => 'we_textDocument.inc.php',
		'we_thumbnail' => 'base/we_thumbnail.class.php',
		'weToolFrames' => 'tools/weToolFrames.class.php',
		'weToolLookup' => 'tools/weToolLookup.class.php',
		'weToolModel' => 'tools/weToolModel.class.php',
		'weToolTreeDataSource' => 'tools/weToolTreeDataSource.class.php',
		'weToolTree' => 'tools/weToolTree.class.php',
		'weToolView' => 'tools/weToolView.class.php',
		'weTree' => 'weTree.inc.php',
		'we_updater' => 'base/we_updater.inc.php',
		'we_util' => 'we_util.inc.php',
		'weVersion' => 'base/weVersion.class.php',
		'we_webEditionDocument' => 'we_webEditionDocument.inc.php',
		'we_widget' => 'we_widget.inc.php',
		'we_wysiwygToolbarButton' => 'we_wysiwyg.class.inc.php',
		'we_wysiwygToolbarElement' => 'we_wysiwyg.class.inc.php',
		'we_wysiwygToolbarSelect' => 'we_wysiwyg.class.inc.php',
		'we_wysiwygToolbarSeparator' => 'we_wysiwyg.class.inc.php',
		'we_wysiwyg' => 'we_wysiwyg.class.inc.php',
		'we_xhtmlConverter' => 'helpers/we_xhtmlConverter.inc.php',
		'weXMLComposer' => 'weXMLComposer.class.php',
		'XML_Export' => 'xml_export.inc.php',
		'XML_Import' => 'xml_import.inc.php',
		'XML_Parser_Error' => 'Parser.php',
		'XML_Parser' => 'Parser.php',
		'XML_Parser' => 'xml_parser.inc.php',
		'XML_RSS' => 'RSS.php',
		'XML_SplitFile' => 'xml_splitFile.inc.php',
		'XML_Validate' => 'xml_validate.inc.php',
	);

	/**
	 * default webEdition autoloader
	 * @param type $class_name
	 */
	static public function autoload($class_name){
		if(array_key_exists($class_name, self::$classes)){
			include(WEBEDITION_INCLUDES_DIR.'we_classes/' . self::$classes[$class_name]);
		}
		//will try next auto-loader
	}
	/**
	 * Added after Zend-Loader to trigger not found classes
	 * @param type $class_name
	 */
	static public function finalLoad($class_name){
			t_e('info', 'we_autoloader: class ' . $class_name . ' not found');
	}
}

//make this the first autoloader
spl_autoload_register('we_autoloader::autoload',false,true);
