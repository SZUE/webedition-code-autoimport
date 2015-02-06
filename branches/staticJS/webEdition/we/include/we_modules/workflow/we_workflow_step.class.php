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
 * General Definition of WebEdition Workflow Step
 */
class we_workflow_step extends we_workflow_base{

	var $ID = 0;
	var $workflowID = 0;
	var $Worktime = 10;
	var $timeAction = 0;
	var $stepCondition = 0;
	var $tasks = array(); # array of we_workflow_task objects

	/**
	 * Default Constructor
	 *
	 * Can load or create new Workflow Step Definition depends of parameter
	 */

	function __construct($stepID = 0){
		parent::__construct();
		$this->table = WORKFLOW_STEP_TABLE;

		$this->persistents = array(
			"ID" => we_base_request::INT,
			"Worktime" => we_base_request::FLOAT,
			"timeAction" => we_base_request::RAW,
			"stepCondition" => we_base_request::RAW,
			"workflowID" => we_base_request::INT,
		);

		if($stepID > 0){
			$this->ID = $stepID;
			$this->load();
		}
	}

	/**
	 * get all workflow steps from database (STATIC)
	 */
	function getAllSteps($workflowID){
		$db = new DB_WE();

		$db->query('SELECT ID FROM ' . WORKFLOW_STEP_TABLE . ' WHERE workflowID=' . intval($workflowID) . ' ORDER BY ID');

		$steps = array();

		while($db->next_record()){
			$steps[] = new self($db->f("ID"));
		}
		return $steps;
	}

	/**
	 * Load step from database
	 */
	function load($id = 0){
		if($id){
			$this->ID = $id;
		}
		if($this->ID){
			parent::load();
			## get tasks for step
			$this->tasks = we_workflow_task::getAllTasks($this->ID);
			return true;
		}
		return false;
	}

	/**
	 * save complete workflow step definition in database
	 */
	function save(){
		$db = new DB_WE();

		parent::save();

		## save all steps also ##

		$tasksList = array();
		for($i = 0; $i < count($this->tasks); $i++){
			$this->tasks[$i]->stepID = intval($this->ID);
			$this->tasks[$i]->save();

			$tasksList[] = $this->tasks[$i]->ID;
		}

		// !!! here we have to delete all other tasks in database except this in array
		if($tasksList){
			$db->query('DELETE FROM ' . WORKFLOW_TASK_TABLE . ' WHERE stepID=' . intval($this->ID) . ' AND ID NOT IN (' . implode(",", $tasksList) . ')');
		}
	}

	/**
	 * delete workflow step from database
	 */
	function delete(){
		if($this->ID){
			foreach($this->tasks as &$val){
				$val->delete();
			}
			parent::delete();
			return true;
		}
		return false;
	}

}
