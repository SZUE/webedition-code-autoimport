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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * General Definition of WebEdition Workflow
 *
 */
class we_workflow_workflow extends we_workflow_base{

	const STATE_INACTIVE = 0;
	const STATE_ACTIVE = 1;
// Document-Type/Category based Workflow Type
	const DOCTYPE_CATEGORY = 0;
// Directory based Workflow Type
	const FOLDER = 1;
// Object based Workflow Type
	const OBJECT = 2;

	//properties
	var $ID;
	var $Text;
	var $Type;
	var $Folders;
	var $DocType;
	var $Objects;
	var $Categories;
	var $ObjectFileFolders;
	var $ObjCategories;
	var $Status = 0;
	var $EmailPath = 0;
	var $LastStepAutoPublish = 0;

	/**
	 * steps for WorkFlow Definition
	 * this is array of we_workflow_step objects
	 */
	var $steps = array();
	// default document object
	var $documentDef;
	// documents array; format document[documentID]=document_name
	// don't create array of objects 'cos whant to save some memory
	var $documents = array();

	/**
	 * Default Constructor
	 * Can load or create new Workflow Definition depends of parameter
	 */
	function __construct($workflowID = 0){
		parent::__construct();
		$this->table = WORKFLOW_TABLE;

		$this->persistents[] = 'ID';
		$this->persistents[] = 'Text';
		$this->persistents[] = 'Type';
		$this->persistents[] = 'DocType';
		$this->persistents[] = 'Folders';
		$this->persistents[] = 'ObjectFileFolders';
		$this->persistents[] = 'Objects';
		$this->persistents[] = 'Categories';
		$this->persistents[] = 'ObjCategories';
		$this->persistents[] = 'Status';
		$this->persistents[] = 'EmailPath';
		$this->persistents[] = 'LastStepAutoPublish';


		$this->ID = 0;
		$this->Text = g_l('modules_workflow', '[new_workflow]');
		$this->Type = self::FOLDER;
		$this->Folders = ',0,';
		$this->ObjectFileFolders = ',0,';
		$this->FolderPath = '';
		$this->DocType = 0;
		$this->Objects = '';
		$this->Categories = '';
		$this->ObjCategories = '';
		$this->Status = self::STATE_INACTIVE;
		$this->EmailPath = 0;
		$this->LastStepAutoPublish = 0;
		$this->steps = array();

		$this->AddNewStep();
		$this->AddNewTask();

		if($workflowID){
			$this->ID = $workflowID;
			$this->load($workflowID);
		}
	}

	/**
	 * Load workflow definition from database
	 */
	function load($id = 0){
		$this->ID = $id ? $id : $this->ID;
		if(!$this->ID){
			return false;
		}
		parent::load();

		// get steps for workflow
		$this->steps = we_workflow_step::getAllSteps($this->ID);
		$this->loadDocuments();
		return true;
	}

	/**
	 * get all documents for workflow from database
	 */
	function loadDocuments(){
		$docTable = ($this->Type == self::OBJECT ? OBJECT_FILES_TABLE : FILE_TABLE );
		$this->db->query('SELECT w.ID,d.Text,d.Icon FROM ' . WORKFLOW_DOC_TABLE . ' w JOIN ' . $docTable . ' d ON w.documentID=d.ID  WHERE w.workflowID=' . intval($this->ID) . ' AND Status=0');
		while($this->db->next_record()){
			$newdoc = array(
				'ID' => $this->db->f('ID'),
				'Text' => $this->db->f('Text'),
				'Icon' => $this->db->f('Icon')
			);
			$this->documents[] = $newdoc;
		}
	}

	/**
	 * get all workflows from database (STATIC)
	 */
	function getAllWorkflows(){
		$this->db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' ORDER BY Text');
		return $this->db->getAll(true);
	}

	/**
	 * get all workflows from database
	 *
	 */
	public static function getAllWorkflowsInfo($status = self::STATE_ACTIVE, $type = self::DOCTYPE_CATEGORY){

		$db = new DB_WE();

		$db->query('SELECT ID,Text FROM ' . WORKFLOW_TABLE . ' WHERE Status IN (' . $status . ') AND Type IN (' . $type . ') ORDER BY Text');
		return $db->getAllFirst(false);
	}

	/**
	 * save complete workflow definition in database
	 * saving also all steps and tasks for current workflow
	 */
	function save(){
		parent::save();

		// save all steps also

		$stepsList = array();

		reset($this->steps);
		for($i = 0; $i < count($this->steps); $i++){
			$this->steps[$i]->workflowID = $this->ID;
			$this->steps[$i]->save();

			$stepsList[] = $this->steps[$i]->ID;
		}


		// !!! here we have to delete all other steps in database except this in array
		if(!empty($stepsList)){
			$this->db->query('DELETE FROM ' . WORKFLOW_STEP_TABLE . ' WHERE workflowID=' . intval($this->ID) . ' AND ID NOT IN (' . implode(',', $stepsList) . ')');
		}

		//remove all documents from workflow
		foreach($this->documents as $k => $val){
			$this->documentDef = new we_workflow_document($val['ID']);
			$this->documentDef->finishWorkflow(1);
			$this->documentDef->save();
		}
	}

	/**
	 * delete workflow from database
	 * delete also all steps and tasks for current workflow
	 */
	function delete(){
		if(!$this->ID){
			return false;
		}

		foreach($this->steps as $key => $val){
			$this->steps[$key]->delete();
		}

		foreach($this->documents as $key => $val){
			$this->documentDef = new we_workflow_document($val['ID']);
			$this->documentDef->delete();
		}

		parent::delete();

		return true;

		//$this->ID = -2; # status deleted
	}

	static function isDocInWorkflow($docID, we_database_base $db){
		$db->query('SELECT ID FROM ' . WORKFLOW_DOC_TABLE . ' WHERE documentID=' . intval($docID) . ' AND Type IN(0,1) AND Status=0');
		return ($db->next_record() ? $db->f('ID') : false);
	}

	static function isObjectInWorkflow($docID, we_database_base $db){
		$db->query('SELECT ID FROM ' . WORKFLOW_DOC_TABLE . ' WHERE documentID=' . intval($docID) . ' AND Type=2 AND Status=0');
		return ($db->next_record() ? $db->f('ID') : false);
	}

	/**
	 * Get workflow for document
	 */
	static function getDocumentWorkflow($doctype, $categories, $folder, we_database_base $db){

		$wfIDs = array();
		$workflowID = 0;
		/**
		 * find by document type (has to be together with category)
		 */
		if($doctype){
			$db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' WHERE DocType LIKE \'%,' . $doctype . ',%\' AND Type=' . self::DOCTYPE_CATEGORY . ' AND Status=' . self::STATE_ACTIVE);
			while($db->next_record()){
				if(isset($wfIDs[$db->f('ID')])){
					$wfIDs[$db->f('ID')] ++;
				} else {
					$wfIDs[$db->f('ID')] = 1;
				}
			}
		}

		/**
		 * find by category
		 */
		if($categories){
			$cats = makeArrayFromCSV($categories);
			foreach($cats as $k => $v){
				if($doctype != ''){
					$db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' WHERE DocType IN (' . $doctype . ') AND Categories LIKE "%,' . $db->escape($v) . ',%" AND Type=' . self::DOCTYPE_CATEGORY . ' AND Status=' . self::STATE_ACTIVE);
				} else {
					$db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' WHERE Categories LIKE "%,' . $db->escape($v) . ',%" AND Type=' . self::DOCTYPE_CATEGORY . ' AND Status=' . self::STATE_ACTIVE);
				}
				while($db->next_record()){
					if(isset($wfIDs[$db->f('ID')])){
						$wfIDs[$db->f('ID')] ++;
					} else {
						$wfIDs[$db->f('ID')] = 1;
					}
				}
			}
		}
		$max = 0;
		foreach($wfIDs as $wfID => $anz){
			if($anz > $max){
				$workflowID = $wfID;
				$max = $anz;
			}
		}

		if($workflowID){ // when we have found a document type-based workflow we can return
			return $workflowID;
		}

		$workflowID = self::findWfIdForFolder($folder);
		/**
		 * create workflow document
		 */
		return ($workflowID ? $workflowID : false);
	}

	function findWfIdForFolder($folderID){
		$db = new DB_WE();
		$wfID = f('SELECT ID FROM ' . WORKFLOW_TABLE . ' WHERE Folders LIKE \'%,' . intval($folderID) . ',%\' AND Type=' . self::FOLDER . ' AND Status=' . self::STATE_ACTIVE, 'ID', $db);
		if($folderID > 0 && (!$wfID)){
			$pid = f('SELECT ParentID FROM ' . FILE_TABLE . ' WHERE ID=' . intval($folderID), 'ParentID', $db);
			return self::findWfIdForFolder($pid);
		} else {
			return $wfID;
		}
	}

	/**
	 * Get workflow for object
	 */
	function getObjectWorkflow($object, $categories, $folderID, we_database_base $db){
		$workflowID = 0;
		$wfIDs = array();
		$tail = ($folderID != 0 ? ' AND ObjectFileFolders LIKE "%,' . intval($folderID) . ',%"' : '');

		$db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' WHERE Objects LIKE "%,' . $db->escape($object) . ',%" AND Type=' . self::OBJECT . ' AND Status=' . self::STATE_ACTIVE . $tail);
		while($db->next_record()){
			if(isset($wfIDs[$db->f('ID')])){
				$wfIDs[$db->f('ID')] ++;
			} else {
				$wfIDs[$db->f('ID')] = 1;
			}
		}

		/**
		 * find by category
		 */
		if($categories){
			$cats = makeArrayFromCSV($categories);
			foreach($cats as $k => $v){
				$db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' WHERE Objects LIKE "%,' . $db->escape($object) . ',%" AND ObjCategories LIKE "%,' . $db->escape($v) . ',%" AND Type=' . self::OBJECT . ' AND Status=' . self::STATE_ACTIVE);
				while($db->next_record()){
					if(isset($wfIDs[$db->f('ID')])){
						$wfIDs[$db->f('ID')] ++;
					} else {
						$wfIDs[$db->f('ID')] = 1;
					}
				}
			}
		}

		$max = 0;
		foreach($wfIDs as $wfID => $anz){
			if($anz > $max){
				$workflowID = $wfID;
				$max = $anz;
			}
		}


		if($workflowID)
			return $workflowID;

		return false;
	}

	function addNewStep(){
		$this->steps[] = new we_workflow_step();
	}

	function addNewTask(){
		foreach($this->steps as $k => $v)
			$this->steps[$k]->tasks[] = new we_workflow_task();
	}

}
