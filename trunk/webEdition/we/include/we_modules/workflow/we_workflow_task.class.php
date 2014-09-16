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
 * General Definition of WebEdition Workflow Task
 */
class we_workflow_task extends we_workflow_base{

	var $ID = 0;
	var $stepID = 0;
	var $userID = 0;
	var $username='';
	var $Edit = 0;
	var $Mail = 0;

	/**
	 * Default Constructor
	 *
	 * Can load or create new Workflow Task Definition depends of parameter
	 */
	function __construct($taskID = 0){
		parent::__construct();
		$this->table = WORKFLOW_TASK_TABLE;

		$this->persistents = array(
			"ID" => we_base_request::INT,
			"userID" => we_base_request::INT,
			"Edit" => we_base_request::RAW,
			"Mail" => we_base_request::RAW,
			"stepID" => we_base_request::RAW,
		);

		if($taskID > 0){
			$this->load($taskID);
		}
	}

	/**
	 * get all workflow tasks from database (STATIC)
	 */
	function getAllTasks($stepID){
		$db = new DB_WE();
		$db->query('SELECT ID FROM ' . WORKFLOW_TASK_TABLE . ' WHERE stepID=' . intval($stepID) . ' ORDER BY ID');

		$tasks = array();

		while($db->next_record()){
			$tasks[] = new self($db->f("ID"));
		}
		return $tasks;
	}

	/**
	 * Load task from database
	 */
	function load($id = 0){
		if($id){
			$this->ID = $id;
		}
		if($this->ID){
			parent::load();
			return true;
		}
		$this->ErrorReporter->Error("No Task with ID $taskID !");
		return false;
	}

}
