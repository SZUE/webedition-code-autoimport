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
 * WorkfFlow Document Step definition
 * This class describe document step in workflow process
 */
class we_workflow_documentStep extends we_workflow_base{
	const STATUS_UNKNOWN = 0;
	const STATUS_APPROVED = 1;
	const STATUS_CANCELED = 2;
	const STATUS_AUTOPUBLISHED = 3;

	var $ID = 0;
	var $workflowStepID = 0;
	var $startDate = 0;
	var $finishDate = 0;
	var $workflowDocID = 0;
	var $Status = self::STATUS_UNKNOWN;

	/**
	 * list of document tasks
	 */
	var $tasks = array();

	/**
	 * Default Constructor
	 *
	 * Can load or create new Workflow Step Definition depends of parameter
	 */
	function __construct($wfDocumentStep = 0){
		parent::__construct();
		$this->table = WORKFLOW_DOC_STEP_TABLE;
		$this->ClassName = __CLASS__;

		$this->persistents = array(
			"ID" => we_base_request::INT,
			"workflowDocID" => we_base_request::INT,
			"workflowStepID" => we_base_request::INT,
			"startDate" => we_base_request::INT,
			"finishDate" => we_base_request::INT,
			"Status" => we_base_request::RAW,
		);

		if($wfDocumentStep){
			$this->ID = $wfDocumentStep;
			$this->load();
		}
	}

	/**
	 * Load data from database
	 *
	 */
	function load($id = 0){
		if($id){
			$this->ID = $id;
		}

		if($this->ID){
			parent::load();
			## get tasks for workflow
			$this->tasks = we_workflow_documentTask::__getAllTasks($this->ID);
			return true;
		}
		return false;
	}

	/**
	 * Start step, activate it
	 *
	 */
	function start($desc = ""){
		$this->startDate = time();

		$workflowDoc = new we_workflow_document($this->workflowDocID);
		$workflowStep = new we_workflow_step($this->workflowStepID);
		$deadline = $this->startDate + round($workflowStep->Worktime * 3600);

		// set all tasks to pending
		foreach($this->tasks as &$cur){
			$workflowTask = new we_workflow_task($cur->workflowTaskID);
			if($workflowTask->userID){
				//send todo to next user
				$path = "<b>" . g_l('modules_workflow', '[' . stripTblPrefix($workflowDoc->document->ContentType === we_base_ContentTypes::OBJECT_FILE ? OBJECT_FILES_TABLE : FILE_TABLE) . '][messagePath]') . ':</b>&nbsp;<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . $workflowDoc->document->Table . '\',\'' . $workflowDoc->document->ID . '\',\'' . $workflowDoc->document->ContentType . '\');");" >' . $workflowDoc->document->Path . '</a>';
				$mess = "<p><b>" . g_l('modules_workflow', '[todo_next]') . '</b></p><p>' . $desc . '</p><p>' . $path . "</p>";

				$cur->todoID = $this->sendTodo($workflowTask->userID, g_l('modules_workflow', '[todo_subject]'), $mess . "<p>" . $path . "</p>", $deadline);
				if($workflowTask->Mail){
					$foo = f('SELECT Email FROM ' . USER_TABLE . ' WHERE ID=' . intval($workflowTask->userID), "", $this->db);
					$this_user = getHash('SELECT First,Second,Email FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION['user']['ID']), $this->db);
					if($foo){
						$desc = str_replace('<br />', "\n", $desc);
						$mess = g_l('modules_workflow', '[todo_next]') . ' ID:' . $workflowDoc->document->ID . ', ' . g_l('weClass', '[path]') . ':' . $workflowDoc->document->Path . "\n\n" . $desc;


						we_mail($foo, correctUml(g_l('modules_workflow', '[todo_next]') . ($workflowDoc->document->Path ? ' ' . $workflowDoc->document->Path : '')), $mess, '', (!empty($this_user["Email"]) ? $this_user["First"] . " " . $this_user["Second"] . " <" . $this_user["Email"] . ">" : ""));
					}
				}
			}
		}
		return true;
	}

	function finish(){
		$this->finishDate = time();
		return true;
	}

	/**
	 * create all tasks for step
	 */
	function createAllTasks(){
		$this->tasks = we_workflow_documentTask::createAllTasks($this->workflowStepID);
		return true;
	}

	/**
	 * save workflow step in database
	 *
	 */
	function save(){
		parent::save();

		## save all tasks also ##
		foreach($this->tasks as &$v){
			$v->documentStepID = $this->ID;
			$v->save();
		}
	}

	function delete(){
		foreach($this->tasks as $v){
			$v->delete();
		}
		parent::delete();
	}

	function approve($uID, $desc, $force = false){
		if($force){
			foreach($this->tasks as &$tv){
				$tv->approve();
			}
			$this->Status = self::STATUS_APPROVED;
			$this->finishDate = time();
			//insert into document Log
			we_workflow_log::logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_APPROVE_FORCE, $desc, $this->db);
			return true;
		}
		$i = $this->findTaskByUser($uID);
		if($i > -1){
			$this->tasks[$i]->approve();

			$workflowStep = new we_workflow_step($this->workflowStepID);
			if($workflowStep->stepCondition == 0){
				$this->Status = self::STATUS_APPROVED;
			} else {
				$num = $this->findNumOfFinishedTasks();
				if($num == count($this->tasks)){
					$status = true;
					foreach($this->tasks as $v){
						$status &= ($v->Status == we_workflow_documentTask::STATUS_APPROVED);
					}

					if($status){
						$this->Status = self::STATUS_APPROVED;
					}
				}
			}
			if($this->Status == self::STATUS_APPROVED || $this->Status == self::STATUS_CANCELED){
				$this->finishDate = time();
				foreach($this->tasks as &$tv){
					if($tv->Status == we_workflow_documentTask::STATUS_UNKNOWN){
						$tv->removeTodo();
					}
				}
			}
			//insert into document Log
			we_workflow_log::logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_APPROVE, $desc, $this->db);
			return true;
		}
		return false;
	}

	function autopublish($uID, $desc, $force = false){
		if($force){
			foreach($this->tasks as &$tv){
				$tv->approve();
			}
			$this->Status = self::STATUS_APPROVED;
			$this->finishDate = time();
			//insert into document Log
			we_workflow_log::logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_APPROVE_FORCE, $desc, $this->db);
			return true;
		}
		$i = $this->findTaskByUser($uID);
		if($i > -1){
			$this->tasks[$i]->approve();

			$workflowStep = new we_workflow_step($this->workflowStepID);
			if($workflowStep->stepCondition == 0){
				$this->Status = self::STATUS_APPROVED;
			} else {
				$num = $this->findNumOfFinishedTasks();
				if($num == count($this->tasks)){
					$status = true;
					foreach($this->tasks as $v){
						$status = $status && ($v->Status == we_workflow_documentTask::STATUS_APPROVED ? true : false);
					}

					if($status){
						$this->Status = self::STATUS_APPROVED;
					}
				}
			}
			if($this->Status == self::STATUS_APPROVED || $this->Status == self::STATUS_CANCELED){
				$this->finishDate = time();
				foreach($this->tasks as &$tv){
					if($tv->Status == we_workflow_documentTask::STATUS_UNKNOWN){
						$tv->removeTodo();
					}
				}
			}
			//insert into document Log
			we_workflow_log::logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_APPROVE, $desc, $this->db);
			return true;
		}
		return false;
	}

	function decline($uID, $desc, $force = false){
		if($force){
			foreach($this->tasks as &$tv){
				$tv->decline();
			}
			$this->Status = self::STATUS_CANCELED;
			$this->finishDate = time();
			//insert into document Log
			we_workflow_log::logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_DECLINE, $desc, $this->db);
			return true;
		}
		$i = $this->findTaskByUser($uID);
		if($i > -1){
			$this->tasks[$i]->decline();
			//FIXME: since next var is unused, does this operation do anything except ressource usage?
			$workflowStep = new we_workflow_step($this->workflowStepID);
			$this->Status = self::STATUS_CANCELED;
			if($this->Status == self::STATUS_APPROVED || $this->Status == self::STATUS_CANCELED){
				$this->finishDate = time();
			}
			//insert into document Log
			we_workflow_log::logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_DECLINE, $desc, $this->db);
			return true;
		}
		return false;
	}

	function findTaskByUser($uID){
		for($i = 0; $i < count($this->tasks); $i++){
			$workflowTask = new we_workflow_task($this->tasks[$i]->workflowTaskID);
			if($workflowTask->userID == $uID){
				return $i;
			}
		}
		return -1;
	}

	function findNumOfFinishedTasks(){
		$num = 0;
		for($i = 0; $i < count($this->tasks); $i++){
			if($this->tasks[$i]->Status != 0){
				$num++;
			}
		}
		return $num;
	}

	/**
	 * return all steps for workflow document (created)
	 *
	 */
	static function __getAllSteps($workflowDocumentID, we_database_base $db){
		$db->query('SELECT ID FROM ' . WORKFLOW_DOC_STEP_TABLE . ' WHERE workflowDocID=' . intval($workflowDocumentID) . ' ORDER BY ID');
		$docSteps = array();
		while($db->next_record()){
			$docSteps[] = new self($db->f("ID"));
		}
		return $docSteps;
	}

	/**
	 * create all steps for workflow document
	 *
	 */
	static function __createAllSteps($workflowID){
		$db = new DB_WE();
		$db->query('SELECT ID FROM ' . WORKFLOW_STEP_TABLE . ' WHERE workflowID =' . intval($workflowID) . ' ORDER BY ID');
		$docSteps = array();
		while($db->next_record()){
			$docSteps[] = self::__createStep($db->f("ID"));
		}
		return $docSteps;
	}

	/**
	 * Create step
	 *
	 */
	static function __createStep($WorkflowStep){
		if(is_array($WorkflowStep)){
			return self::__createStepFromHash($WorkflowStep);
		}

		$tmp = getHash('SELECT * FROM ' . WORKFLOW_STEP_TABLE . ' WHERE ID=' . intval($WorkflowStep) . ' ORDER BY ID', new DB_WE());
		return $tmp ? self::__createStepFromHash($tmp) : false;
	}

	/**
	 * Create step from hash
	 *
	 */
	static function __createStepFromHash($WorkflowStepArray){
		$docStep = new self();

		$docStep->workflowStepID = $WorkflowStepArray["ID"];
		$docStep->startDate = 0;
		$docStep->finishDate = 0;
		$docStep->Status = self::STATUS_UNKNOWN;
		$docStep->tasks = we_workflow_documentTask::createAllTasks($docStep->workflowStepID);
		return $docStep;
	}

}
