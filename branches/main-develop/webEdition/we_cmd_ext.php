<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

switch($_REQUEST['we_cmd'][0]){
		case 'get_configuration' : 
			print json_encode(array('success' => true, 'config' => array(
				'backendLang' => 'de',
				'treeSegments' => 0
			)));
			break;
		case 'load_translation' : 
			print json_encode(array('success' => true, 'trans' => array('reto', 'beat')));
			break;
		case 'load_menu' :
			include(WE_INCLUDES_PATH . "java_menu/we_menu.inc.php");
			ksort($we_menu);
			if(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
				$jmenu = new we_base_menu($we_menu);
			} 
			print json_encode(array('success' => true, 'tb' => $jmenu->getJsonData()));
			break;
	
		case 'load_vtabs' : 
			$vtabs[] = permissionhandler::hasPerm('CAN_SEE_DOCUMENTS') || permissionhandler::hasPerm('ADMINISTRATOR') ? array('table' => FILE_TABLE, 'tblconst' => 'FILE_TABLE', 'text' => g_l('global', '[documents]')) : '';
			$vtabs[] = permissionhandler::hasPerm('CAN_SEE_TEMPLATES') ? array('table' => TEMPLATES_TABLE, 'tblconst' => 'TEMPLATES_TABLE', 'text' => g_l('global', '[templates]')) : '';
			$vtabs[] = defined('OBJECT_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES') ? array('table' => OBJECT_FILES_TABLE, 'tblconst' => 'OBJECT_FILES_TABLE', 'text' => g_l('global', '[objects]')) : '';
			$vtabs[] = defined('OBJECT_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTS") ? array('table' => OBJECT_TABLE, 'tblconst' => 'OBJECT_TABLE', 'text' => g_l('javaMenu_object', '[classes]')) : '';
			print json_encode(array('success' => true, 'vtabs' => $vtabs));
			break;

		case 'check_update_tree' :
			$getCompareTree = true;
		case 'load_main_tree' :
		case 'closeFolder' :
			$loadExtTree = true;
			include(WE_INCLUDES_PATH . "we_load.inc.php");
			break;
		case 'edit_document_tmp':
			$load_tmp = true;
		case 'edit_document':
			//TODO: why do new files (id = 0, transaction ok) not have text/name?
			$isIncTo_we_cmd_ext = true;
			$aborted = true;
			include(WE_INCLUDES_PATH . 'we_editors/we_edit_frameset.inc.php');

			$pubState = ($we_doc->Table == FILE_TABLE || (defined("OBJECT_FILES_TABLE") && ($we_doc->Table == OBJECT_FILES_TABLE)) ?
					(($we_doc->Published != 0) && ($we_doc->Published < $we_doc->ModDate) ? -1 : $we_doc->Published) : 1);

			switch($we_doc->Table){
				case OBJECT_FILES_TABLE: 
					$ctTable = 'obj';
				case TEMPLATES_TABLE: 
					$ctTable = 'tmpl';
				default: 
					$ctTable = 'file';
			}
			
			
			$tmplPath = isset($we_doc->TemplatePath) ?
					(strpos($we_doc->TemplatePath, 'templates/') !== false ? substr($we_doc->TemplatePath, strpos($we_doc->TemplatePath, 'templates/') + 9) : '') : '';

			$ct = we_base_ContentTypes::inst();

			print $aborted ? json_encode(array('success' => false)) : 
				json_encode(array('success' => true, 'file' => array(
						'filename' => get_class($we_doc) == 'we_objectFile' || get_class($we_doc) == 'we_object'? $we_doc->Text : $we_doc->Filename,
						'extension' => $we_doc->Extension,
						'text' => oldHtmlspecialchars($we_doc->Text),
						'path' => '',//$we_doc->Path ,is created in ext model: conversion from parentpath and text
						'parentpath' => $we_doc->ParentPath,
						'table' => $we_doc->Table,
						'we_id' => $we_doc->ID,
						'isFolder' => $we_doc->IsFolder,
						'ct' => $we_doc->ContentType,
						'fullCt' => $we_doc->IsFolder ? $we_doc->ContentType . '/' . $ctTable : $we_doc->ContentType,
						'iconCls' => 'tree_item_' . strstr($we_doc->Icon, '.', true),
						'parameters' => (isset($parastr) ? '"' . $parastr . '"' : '""'),
						'published' => $pubState,
						'id' => $we_transaction,
						'name' => $we_doc->Name,
						'parentid' => $we_doc->ParentID,
						'templateid' => isset($we_doc->TemplateID) ? $we_doc->TemplateID : 0,
						'templatepath' => $tmplPath,
					)
				));

			unset($load_tmp);
			break;
		case 'save_document':
			//do nothing: WE allready returned success
			print json_encode(array('success' => true));
			break;
		case 'save_to_session':
			
			//write to session and return sucess = true;
			//return complete editor-html to load to editomode and preview (as done in classic we)
			/*
			
			$we_dt = isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : '';
			include(WE_INCLUDES_PATH . '/we_editors/we_init_doc.inc.php');
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
			 * 
			 */
}