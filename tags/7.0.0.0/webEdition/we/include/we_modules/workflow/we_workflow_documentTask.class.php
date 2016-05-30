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
 * WorkfFlow Document Task definition
 *
 * This class describe document task in workflow process
 *
 */
class we_workflow_documentTask extends we_workflow_base{
	const STATUS_UNKNOWN = 0;
	const STATUS_APPROVED = 1;
	const STATUS_CANCELED = 2;

	// workflow document task ID
	var $ID = 0;
	// workflow document step ID
	var $documentStepID = 0;
	// workflow task ID
	var $workflowTaskID = 0;
	// date when task is done
	var $Date = 0;
	// to do id
	var $todoID = 0;
	// Status of document task
	var $Status = self::STATUS_UNKNOWN;

	/**
	 * Default Constructor
	 */
	function __construct($wfDocumentTask = 0){
		parent::__construct();
		$this->table = WORKFLOW_DOC_TASK_TABLE;
		$this->ClassName = __CLASS__;

		$this->persistents = array(
			"ID" => we_base_request::INT,
			"documentStepID" => we_base_request::INT,
			"workflowTaskID" => we_base_request::INT,
			"Date" => we_base_request::INT,
			"todoID" => we_base_request::INT,
			"Status" => we_base_request::INT,
		);

		if($wfDocumentTask){
			$this->ID = $wfDocumentTask;
			$this->load();
		}
	}

	function approve(){
		$this->Status = self::STATUS_APPROVED;
		$this->Date = time();
		$this->doneTodo();
	}

	function decline(){
		$this->Status = self::STATUS_CANCELED;
		$this->Date = time();
		$this->rejectTodo();
	}

	function removeTodo($id = 0){
		if($this->todoID){
			parent::removeTodo($this->todoID);
		}
	}

	function doneTodo($id = 0){
		if($this->todoID){
			parent::doneTodo($this->todoID);
		}
	}

	function rejectTodo($id = 0){
		if($this->todoID){
			parent::rejectTodo($this->todoID);
		}
	}

	//--------------------------------STATIC FUNCTIONS ------------------------------
	/**
	 * returns all tasks for workflow step
	 *
	 */
	static function __getAllTasks($workflowDocumentStep){
		$db = new DB_WE();

		$db->query('SELECT ID FROM ' . WORKFLOW_DOC_TASK_TABLE . " WHERE documentStepID=" . intval($workflowDocumentStep) . " ORDER BY ID");

		$docTasks = array();

		while($db->next_record()){
			$docTasks[] = new self($db->f("ID"));
		}
		return $docTasks;
	}

	/**
	 * creates all tasks for workflow step
	 *
	 */
	public static function createAllTasks($workflowStepID){
		$db = new DB_WE();

		$db->query('SELECT ID FROM ' . WORKFLOW_TASK_TABLE . ' WHERE stepID=' . intval($workflowStepID) . ' ORDER BY ID');
		$docTasks = array();
		while($db->next_record()){
			$docTasks[] = self::createTask($db->f("ID"));
		}
		return $docTasks;
	}

	/**
	 * Create task
	 */
	private static function createTask($id){
		$docTask = new we_workflow_documentTask();
		$docTask->workflowTaskID = $id;
		return $docTask;
	}

}
