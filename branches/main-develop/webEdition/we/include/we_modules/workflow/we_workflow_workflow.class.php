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
	var $ID = 0;
	var $Text;
	var $Type = self::FOLDER;
	var $Folders = [0];
	var $FolderPath = '';
	var $DocType = [];
	var $Objects = [];
	var $Categories = [];
	var $ObjectFileFolders = [0];
	var $ObjCategories = [];
	var $Status = self::STATE_INACTIVE;
	var $EmailPath = 0;
	var $LastStepAutoPublish = 0;

	/**
	 * steps for WorkFlow Definition
	 * this is array of we_workflow_step objects
	 */
	var $steps = [];
	// default document object
	var $documentDef;
	// documents array; format document[documentID]=document_name
	// don't create array of objects 'cos whant to save some memory
	var $documents = [];

	/**
	 * Default Constructor
	 * Can load or create new Workflow Definition depends of parameter
	 */
	function __construct($workflowID = 0){
		parent::__construct();
		$this->table = WORKFLOW_TABLE;

		$this->persistents = ['ID' => we_base_request::INT,
			'Text' => we_base_request::STRING,
			'Type' => we_base_request::INT,
			'DocType' => we_base_request::INTLISTA,
			'Folders' => we_base_request::INTLISTA,
			'ObjectFileFolders' => we_base_request::INTLISTA,
			'Objects' => we_base_request::INTLISTA,
			'Categories' => we_base_request::INTLISTA,
			'ObjCategories' => we_base_request::INTLISTA,
			'Status' => we_base_request::INT,
			'EmailPath' => we_base_request::BOOL,
			'LastStepAutoPublish' => we_base_request::BOOL,
		];

		$this->Text = g_l('modules_workflow', '[new_workflow]');

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
		$this->ID = $id ?: $this->ID;
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
		$this->db->query('SELECT w.ID,d.Text,d.ContentType FROM ' . WORKFLOW_DOC_TABLE . ' w JOIN ' . $docTable . ' d ON w.documentID=d.ID  WHERE w.workflowID=' . intval($this->ID) . ' AND Status=' . self::STATE_INACTIVE);
		while($this->db->next_record()){
			$newdoc = [
				'ID' => $this->db->f('ID'),
				'Text' => $this->db->f('Text'),
				'contenttype' => $this->db->f('ContentType')
			];
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
	public static function getAllWorkflowsInfo($status = self::STATE_ACTIVE, $type = self::DOCTYPE_CATEGORY, array $all = null){

		$db = new DB_WE();
		$db->query('SELECT ID,Text FROM ' . WORKFLOW_TABLE . ' WHERE Status IN (' . $status . ') AND Type IN (' . $type . ')' . ($all ? ' AND ID IN(' . implode(',', $all) . ')' : '') . ' ORDER BY Text');
		return $db->getAllFirst(false);
	}

	/**
	 * save complete workflow definition in database
	 * saving also all steps and tasks for current workflow
	 */
	function save(){
		parent::save();

		// save all steps also

		$stepsList = [];

		foreach($this->steps as &$step){
			$step->workflowID = $this->ID;
			$step->save();

			$stepsList[] = $step->ID;
		}


		// !!! here we have to delete all other steps in database except this in array
		if($stepsList){
			$this->db->query('DELETE FROM ' . WORKFLOW_STEP_TABLE . ' WHERE workflowID=' . intval($this->ID) . ' AND ID NOT IN (' . implode(',', $stepsList) . ')');
		}

		//remove all documents from workflow
		foreach($this->documents as $val){
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
		$id = f('SELECT ID FROM ' . WORKFLOW_DOC_TABLE . ' WHERE documentID=' . intval($docID) . ' AND Type IN(' . self::DOCTYPE_CATEGORY . ',' . self::FOLDER . ') AND Status=' . self::STATE_INACTIVE, '', $db);
		return ($id ?: false);
	}

	static function isObjectInWorkflow($docID, we_database_base $db){
		$id = f('SELECT ID FROM ' . WORKFLOW_DOC_TABLE . ' WHERE documentID=' . intval($docID) . ' AND Type=' . self::OBJECT . ' AND Status=' . self::STATE_INACTIVE, '', $db);
		return ($id ?: false);
	}

	/**
	 * Get workflow for document
	 */
	static function getDocumentWorkflow($doctype, $categories, $folder, we_database_base $db, array &$all){

		$wfIDs = [];
		$workflowID = 0;
		/**
		 * find by document type (has to be together with category)
		 */
		if($doctype){
			$db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' WHERE FIND_IN_SET(' . intval($doctype) . ',DocType) AND Type=' . self::DOCTYPE_CATEGORY . ' AND Status=' . self::STATE_ACTIVE);
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
			foreach($cats as $v){
				if($doctype){
					$db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' WHERE DocType=' . intval($doctype) . ' AND FIND_IN_SET(' . intval($v) . ',Categories) AND Type=' . self::DOCTYPE_CATEGORY . ' AND Status=' . self::STATE_ACTIVE);
				} else {
					$db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' WHERE FIND_IN_SET(' . intval($v) . ',Categories) AND Type=' . self::DOCTYPE_CATEGORY . ' AND Status=' . self::STATE_ACTIVE);
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
		$all = array_keys($wfIDs);
		return ($workflowID ? $workflowID // when we have found a document type-based workflow we can return
			: (self::findWfIdForFolder($folder, $db, $all) ?: false));
	}

	private static function findWfIdForFolder($folderID, we_database_base $db, array &$all){
		$folders = [$folderID];
		while($folderID != 0){
			$folderID = f('SELECT ParentID FROM ' . FILE_TABLE . ' WHERE ID=' . intval($folderID), '', $db);
			$folders[] = $folderID;
		}
		$db->addTable('TMP_WF', ['ID' => 'INT unsigned NOT NULL'], [], 'MEMORY', true);
		$db->query('INSERT INTO TMP_WF (ID) VALUES (' . implode('),(', $folders) . ')');
		$db->query('SELECT DISTINCT(w.ID) FROM ' . WORKFLOW_TABLE . ' w JOIN TMP_WF WHERE FIND_IN_SET(TMP_WF.ID,w.Folders) AND w.Type=' . self::FOLDER . ' AND w.Status=' . self::STATE_ACTIVE);
		$all = $db->getAll(true);
		$db->delTable('TMP_WF', true);
		return $all ? $all[0] : 0;
	}

	/**
	 * Get workflow for object
	 */
	function getObjectWorkflow($object, $categories, $folderID, we_database_base $db, array &$all){
		$workflowID = 0;
		$wfIDs = [];
		$tail = ($folderID ? ' AND (FIND_IN_SET(' . intval($folderID) . ',ObjectFileFolders) OR FIND_IN_SET(0,ObjectFileFolders) OR ObjectFileFolders="")' : '');

		$db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' WHERE FIND_IN_SET(' . intval($object) . ',Objects) AND Type=' . self::OBJECT . ' AND Status=' . self::STATE_ACTIVE . $tail);
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
				$db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' WHERE FIND_IN_SET(' . intval($object) . ',Objects) AND FIND_IN_SET(' . intval($v) . ',ObjCategories) AND Type=' . self::OBJECT . ' AND Status=' . self::STATE_ACTIVE);
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
		$all = array_keys($wfIDs);

		return ($workflowID ?: false);
	}

	function addNewStep(){
		$this->steps[] = new we_workflow_step();
	}

	function addNewTask(){
		foreach($this->steps as &$v){
			$v->tasks[] = new we_workflow_task();
		}
	}

	public static function getJSLangConsts(){
		return 'WE().consts.g_l.workflow=JSON.parse("' . setLangString([
				'view' => [
					'delete_question' => g_l('modules_workflow', '[delete_question]'),
					'emty_log_question' => g_l('modules_workflow', '[emty_log_question]'),
					'nothing_to_delete' => (g_l('modules_workflow', '[nothing_to_delete]')),
					'nothing_to_save' => (g_l('modules_workflow', '[nothing_to_save]')),
					'save_changed_workflow' => g_l('modules_workflow', '[save_changed_workflow]'),
					'save_question' => g_l('modules_workflow', '[save_question]'),
				],
				'prop' => [
					'del_last_step' => (g_l('modules_workflow', '[del_last_step]')),
					'del_last_task' => (g_l('modules_workflow', '[del_last_task]')),
					'doctype_empty' => (g_l('modules_workflow', '[doctype_empty]')),
					'folders_empty' => (g_l('modules_workflow', '[folders_empty]')),
					'name_empty' => (g_l('modules_workflow', '[name_empty]')),
					'objects_empty' => (g_l('modules_workflow', '[objects_empty]')),
					'user_empty' => (g_l('modules_workflow', '[user_empty]')),
					'worktime_empty' => (g_l('modules_workflow', '[worktime_empty]')),
				]
				]) . '");';
	}

}
