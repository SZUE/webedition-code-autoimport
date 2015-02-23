<?php

/**
 * webEdition CMS
 *
 * $Rev: 9306 $
 * $Author: mokraemer $
 * $Date: 2015-02-13 18:57:59 +0100 (Fr, 13 Feb 2015) $
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
/*  a class for handling flashDocuments. */

class we_collection extends we_document{
	
	var $persistent_slots = array('ID', 'ParentID', 'Text', 'IsFolder', 'Path', 'CreatorID', 'ModifierID', 'RestrictOwners', 'Owners', 'OwnersReadOnly');

	/** Constructor
	 * @return we_collection
	 * @desc Constructor for we_collection
	 */
	function __construct(){
		parent::__construct();

		$this->Language = self::getDefaultLanguage()? : 'de_DE';//TODO: do collections have language?
		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_INFO);
			if(defined('CUSTOMER_TABLE') && (permissionhandler::hasPerm('CAN_EDIT_CUSTOMERFILTER') || permissionhandler::hasPerm('CAN_CHANGE_DOCS_CUSTOMER'))){
				$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_WEBUSER;
			}
		}
	}

	function editor(){
		switch($this->EditPageNr){
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
				return 'we_editors/we_editor_properties.inc.php';
			case we_base_constants::WE_EDITPAGE_CONTENT:
				return 'we_editors/we_editor_content_collection.inc.php';
			case we_base_constants::WE_EDITPAGE_INFO:
				return 'we_editors/we_editor_info.inc.php';
			case we_base_constants::WE_EDITPAGE_WEBUSER:
				return 'we_editors/editor_weDocumentCustomerFilter.inc.php';
			default:
				$this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_PROPERTIES;
				return 'we_editors/we_editor_properties.inc.php';
		}
	}

	public function getPropertyPage(){
		echo we_html_multiIconBox::getJS() .
		we_html_multiIconBox::getHTML('weOtherDocProp', '100%', array(
			array('icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(), 'space' => 140),
			//array('icon' => 'doc.gif', 'headline' => g_l('weClass', '[document]'), 'html' => $this->formIsSearchable(), 'space' => 140),
			//array('icon' => 'meta.gif', 'headline' => g_l('weClass', '[metainfo]'), 'html' => $this->formMetaInfos(), 'space' => 140),
			//array('icon' => 'cat.gif', 'headline' => g_l('weClass', '[category]'), 'html' => $this->formCategory(), 'space' => 140),
			array('icon' => 'user.gif', 'headline' => g_l('weClass', '[owners]'), 'html' => $this->formCreatorOwners(), 'space' => 140))
			, 20);
	}

	function i_filenameDouble(){//
		return f('SELECT 1 FROM ' . escape_sql_query($this->Table) . ' WHERE ParentID=' . intval($this->Text) . " AND Text='" . escape_sql_query($this->Filename) . "' AND ID != " . intval($this->ID), "", $this->DB_WE);
	}

	public function we_save_nix($resave = 0, $skipHook = 0){//TODO: maybe use fn on we_document and skip $this->i_writeDocument();
		$this->errMsg = '';
		$this->i_setText();

		if(!$skipHook){
			$hook = new weHook('preSave', '', array($this, 'resave' => $resave));
			$ret = $hook->executeHook();
//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}

		if(!parent::we_save($resave)){
			return false;
		}

		/*
		$ret = $this->i_writeDocument();
		if(!$ret || ($this->errMsg != '')){
			return false;
		}
		 * 
		 */

		$this->OldPath = $this->Path;

		if($resave == 0){ // NO rebuild!!!
			$this->resaveWeDocumentCustomerFilter();
		}

		/* TODO: is private on parent
		if($this->isVersioned()){
			$version = new we_versions_version();
			$version->save($this);
		}
		 * 
		 */

		/* hook */
		if(!$skipHook){
			$hook = new weHook('save', '', array($this, 'resave' => $resave));
			$ret = $hook->executeHook();
//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}
		return $ret;
	}
}