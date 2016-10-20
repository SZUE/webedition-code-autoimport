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
class we_workflow_document extends we_workflow_base{

	const STATUS_UNKNOWN = 0;
	const STATUS_FINISHED = 1;
	const STATUS_CANCELED = 2;

	//properties
	var $ID = 0;
	var $workflowID = 0;
	var $documentID = 0;
	var $userID = 0;
	var $Status = 0;
	//accossiations
	var $workflow = false;
	var $document = false;
	var $steps = [];

	/**
	 * Default Constructor
	 *
	 */
	public function __construct($wfDocument = 0){
		parent::__construct();
		$this->table = WORKFLOW_DOC_TABLE;
		$this->ClassName = __CLASS__;
		$this->persistents = ['ID' => we_base_request::INT,
			'workflowID' => we_base_request::INT,
			'documentID' => we_base_request::INT,
			'userID' => we_base_request::INT,
			'Status' => we_base_request::INT,
			];


		if($wfDocument){
			$this->ID = $wfDocument;
			$this->load($wfDocument);
		}
	}

	/**
	 * Load data from database
	 */
	function load($id = 0){
		if($id){
			$this->ID = $id;
		}

		if($this->ID){
			parent::load();
			$this->workflow = new we_workflow_workflow($this->workflowID);

			$docTable = $this->workflow->Type == we_workflow_workflow::OBJECT ? OBJECT_FILES_TABLE : FILE_TABLE;
			$this->db->query('SELECT * FROM ' . $docTable . ' WHERE ID=' . intval($this->documentID));
			if($this->db->next_record()){
				if($this->db->f("ClassName")){
					$tmp = $this->db->f("ClassName");
					$this->document = new $tmp();
					if($this->document){
						$this->document->initByID($this->documentID, $docTable);
						$this->document->we_load(we_class::LOAD_TEMP_DB);
					}
				}
			}

			$this->steps = we_workflow_documentStep::__getAllSteps($this->ID, $this->db);
		}
	}

	function approve($uID, $desc, $force = false){
		$i = $this->findLastActiveStep();
		if($i < 0 && !$force){
			return false;
		}
		$ret = $this->steps[$i]->approve($uID, $desc, $force);
		if($this->steps[$i]->Status == we_workflow_documentStep::STATUS_APPROVED){
			$this->nextStep($i, $desc, $uID);
		}
		return $ret;
	}

	function autopublish($uID, $desc, $force = false){
		$i = $this->findLastActiveStep();
		if($i < 0 && !$force){
			return false;
		}
		$ret = $this->steps[$i]->approve($uID, $desc, $force);
		if($this->steps[$i]->Status == we_workflow_documentStep::STATUS_APPROVED){
			$this->finishWorkflow(1, $uID);
			$this->document->save();
			if($this->document->i_publInScheduleTable()){
				//$this->document->getNextPublishDate();
			} else {
				$this->document->we_publish();
			}
			$path = '<b>' . g_l('modules_workflow', '[' . stripTblPrefix($this->workflow->Type == 2 ? OBJECT_FILES_TABLE : FILE_TABLE) . '][messagePath]') . ':</b>&nbsp;<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . $this->document->Table . '\',\'' . $this->document->ID . '\',\'' . $this->document->ContentType . '\');");" >' . $this->document->Path . '</a>';
			$mess = '<p><b>' . g_l('modules_workflow', '[auto_published]') . '</b></p><p>' . $desc . '</p><p>' . $path . '</p>';
			$deadline = time();
			$this->sendTodo($this->userID, g_l('modules_workflow', '[auto_published]'), $mess, $deadline, 1);
			$desc = str_replace('<br />', "\n", $desc);
			$mess = g_l('modules_workflow', '[auto_published]') . "\n\n" . $desc . "\n\n" . $this->document->Path;
			$this->sendMail($this->userID, g_l('modules_workflow', '[auto_published]') . ($this->workflow->EmailPath ? ' ' . $this->document->Path : ''), $mess);
		}
		return $ret;
	}

	function decline($uID, $desc, $force = false){
		$i = $this->findLastActiveStep();
		if($i < 0 && !$force){
			return false;
		}
		$ret = $this->steps[$i]->decline($uID, $desc, $force);
		if($this->steps[$i]->Status == we_workflow_documentStep::STATUS_CANCELED){
			$this->finishWorkflow(1, $uID);

			$path = '<b>' . g_l('modules_workflow', '[' . stripTblPrefix($this->workflow->Type == 2 ? OBJECT_FILES_TABLE : FILE_TABLE) . '][messagePath]') . ':</b>&nbsp;<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . $this->document->Table . '\',\'' . $this->document->ID . '\',\'' . $this->document->ContentType . '\');");" >' . $this->document->Path . '</a>';
			$mess = '<p><b>' . g_l('modules_workflow', '[todo_returned]') . '</b></p><p>' . $desc . '</p><p>' . $path . '</p>';
			$deadline = time() + 3600;
			$this->sendTodo($this->userID, g_l('modules_workflow', '[todo_returned]'), $mess, $deadline, 1);
			$desc = str_replace('<br />', "\n", $desc);
			$mess = g_l('modules_workflow', '[todo_returned]') . "\n\n" . $desc . "\n\n" . $this->document->Path;
			$this->sendMail($this->userID, g_l('modules_workflow', '[todo_returned]') . ($this->workflow->EmailPath ? ' ' . $this->document->Path : ''), $mess);
		}
		return $ret;
	}

	function restartWorkflow($desc){
		foreach($this->steps as &$v){
			$v->delete();
		}
		$this->steps = we_workflow_documentStep::__createAllSteps($this->workflowID);
		$this->steps[0]->start($desc);
	}

	function nextStep($index = -1, $desc = "", $uid = 0){
		if($index > -1){
			if($index < count($this->steps) - 1){
				$this->steps[$index + 1]->start($desc);
			} else {
				$this->finishWorkflow(0, $uid);
			}
		}
	}

	function finishWorkflow($force = 0, $uID = 0){
		if($force){
			$this->Status = self::STATUS_CANCELED;
			foreach($this->steps as &$sv){
				if($sv->Status == we_workflow_documentStep::STATUS_UNKNOWN){
					$sv->Status = we_workflow_documentStep::STATUS_CANCELED;
				}
				foreach($sv->tasks as &$tv){
					if($tv->Status == we_workflow_documentTask::STATUS_UNKNOWN){
						$tv->Status = we_workflow_documentTask::STATUS_CANCELED;
					}
				}
			}
			//insert into document Log
			we_workflow_log::logDocumentEvent($this->ID, $uID, we_workflow_log::TYPE_DOC_FINISHED_FORCE, "", $this->db);
			return true;
		}
		$this->Status = self::STATUS_FINISHED;
		we_workflow_log::logDocumentEvent($this->ID, $uID, we_workflow_log::TYPE_DOC_FINISHED, "", $this->db);

		return true;
	}

	/**
	 * Create next step or finish workflow document if last step is done
	 *
	 */
	function createNextStep($stepKey, $uid = 0){
		if($stepKey >= count($this->steps)){
			// no more steps, finish workflow
			return $this->finishWorkflow(0, $uid);
		}
		$step = &$this->steps[$stepKey];
		$step->start();
		return true;
	}

	/**
	 * Find last document Status step
	 *
	 */
	function findLastActiveStep(){
		for($i = count($this->steps) - 1; $i >= 0; $i--){
			if($this->steps[$i]->startDate > 0){
				return $i;
			}
		}
		return -1;
	}

	/**
	 * save workflow document in database
	 *
	 */
	function save(){
		if(!$this->documentID){
			return false;
		}
		parent::save();
		foreach($this->steps as &$cur){
			$cur->workflowDocID = $this->ID;
			$cur->save();
		}
		return true;
	}

	function delete(){
		if(!$this->ID){
			return false;
		}

		foreach($this->steps as $v){
			$v->delete();
		}
		parent::delete();
		return true;
	}

	/*	 * ***************** STATIC FUNCTIONS**************************
	  /**
	 * return workflowDocument for document
	 *    return false if no workflow
	 */

	public static function find($documentID, $type = '0,1', $status = self::STATUS_UNKNOWN, we_database_base $db = null){
		$db = $db? : new DB_WE();
		$id = f('SELECT doc.ID FROM ' . WORKFLOW_DOC_TABLE . ' doc JOIN ' . WORKFLOW_TABLE . ' wf ON doc.workflowID=wf.ID' .
				' WHERE doc.documentID=' . intval($documentID) . ' AND doc.Status IN (' . $db->escape($status) . ')' . ($type ? ' AND wf.Type IN (' . $db->escape($type) . ')' : '') . ' ORDER BY doc.ID DESC', '', $db);
		return ($id ? new self($id) : false);
	}

	/**
	 * Create new workflow document
	 *    if workflow for that document exists, function will return it
	 */
	public static function createNew($documentID, $type, $workflowID, $userID){
		if(($newWfDoc = self::find($documentID, $type, self::STATUS_UNKNOWN, $GLOBALS['DB_WE']))){
			return $newWfDoc;
		}

		//fixme: check the difference to new self($documentID)

		$newWFDoc = new self();
		$newWFDoc->documentID = $documentID;
		$newWFDoc->userID = $userID;
		$newWFDoc->workflowID = $workflowID;
		$newWFDoc->workflow = new we_workflow_workflow($workflowID);
		$newWFDoc->steps = we_workflow_documentStep::__createAllSteps($workflowID);

		return $newWFDoc;
	}

}
