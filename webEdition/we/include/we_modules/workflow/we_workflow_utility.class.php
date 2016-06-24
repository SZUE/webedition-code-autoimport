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
we_base_moduleInfo::isActive(we_base_moduleInfo::WORKFLOW);

abstract class we_workflow_utility{

	private static function getTypeForTable($table){
		switch($table){
			case FILE_TABLE:
				return '0,1';
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : -1):
				return 2;
			default:
				return '0,1';
		}
	}

	public static function insertDocInWorkflow($docID, $table, $workflowID, $userID, $desc){
		$desc = nl2br($desc);
		$type = self::getTypeForTable($table);
		//create new workflow document
		$doc = we_workflow_document::createNew($docID, $type, $workflowID, $userID, $desc);
		if(isset($doc->ID)){
			$doc->save();
			if(isset($doc->steps[0])){
				$doc->steps[0]->start($desc);
			}
			//insert into document history
			we_workflow_log::logDocumentEvent($doc->ID, $userID, we_workflow_log::TYPE_DOC_INSERTED, $desc);
			$doc->save();
			return true;
		}
		return false;
	}

	public static function approve($docID, $table, $userID, $desc, $force = false){
		/* approve step */
		$desc = nl2br($desc);
		$doc = self::getWorkflowDocument($docID, $table);
		if(isset($doc->ID)){
			if($doc->approve($userID, $desc, $force)){
				$doc->save();
				return true;
			}
		}
		return false;
	}

	/*

	 */

	public static function decline($docID, $table, $userID, $desc, $force = false){
		//decline step
		$doc = self::getWorkflowDocument($docID, $table);
		if($doc && $doc->ID){
			if($doc->decline($userID, nl2br($desc), $force)){
				$doc->save();
				return true;
			}
		}
		return false;
	}

	/**
	  This function can be used to force removal
	  of document from workflow.
	 */
	public static function removeDocFromWorkflow($docID, $table, $userID, $desc){
		$desc = nl2br($desc);
		$db = new DB_WE();
		$doc = self::getWorkflowDocument($docID, $table, we_workflow_document::STATUS_UNKNOWN, $db);
		if(isset($doc->ID)){
			if($doc->finishWorkflow(1, $userID)){
				$doc->save();
				//insert into document history
				we_workflow_log::logDocumentEvent($doc->ID, $userID, we_workflow_log::TYPE_DOC_REMOVED, $desc, $db);
				return true;
			}
		}
		return false;
	}

	/**
	  Function returns workflow document object for defined docID
	  If workflow documnet is not defined for that document false
	  will be returned
	 */
	private static function getWorkflowDocument($docID, $table, $status = we_workflow_document::STATUS_UNKNOWN, we_database_base $db = null){
		return we_workflow_document::find($docID, self::getTypeForTable($table), $status, $db);
	}

	/**
	  Same like getWorkflowDocument but returns
	  workflow document id (not object)
	 */
	static function getWorkflowDocumentID($docID, $table, $status = we_workflow_document::STATUS_UNKNOWN){
		$doc = self::getWorkflowDocument($docID, $table, $status);
		return (!empty($doc->ID) ? $doc->ID : false);
	}

	/**
	  Functions tries to find workflow for defined
	  documents parameters and returns new document object
	 */
	static function getWorkflowDocumentForDoc($db, $doctype, $categories, $folder, array &$all){
		$workflowID = we_workflow_workflow::getDocumentWorkflow($doctype, $categories, $folder, $db, $all);
		$newDoc = new we_workflow_document();
		$newDoc->workflowID = $workflowID;
		$newDoc->steps = we_workflow_documentStep::__createAllSteps($workflowID);
		return $newDoc;
	}

	/**
	  Functions tries to find workflow for defined
	  objects parametars and returns new document object
	 */
	static function getWorkflowDocumentForObject($db, $object, $categories, $folderID, array &$all){
		$workflowID = we_workflow_workflow::getObjectWorkflow($object, $categories, $folderID, $db, $all);
		$newDoc = new we_workflow_document();
		$newDoc->workflowID = $workflowID;
		$newDoc->steps = we_workflow_documentStep::__createAllSteps($workflowID);
		return $newDoc;
	}

	static function getWorkflowName($workflowID, $table){
		$foo = self::getAllWorkflows(we_workflow_workflow::STATE_ACTIVE, $table);
		return $foo[$workflowID];
	}

	static function getWorkflowID($workflowName, $table){
		return array_search($workflowName, self::getAllWorkflows(we_workflow_workflow::STATE_ACTIVE, $table));
	}

	static function getAllWorkflows($status = we_workflow_workflow::STATE_ACTIVE, $table = FILE_TABLE, array $all = null){ // returns hash array with ID as key and Name as value
		return we_workflow_workflow::getAllWorkflowsInfo($status, self::getTypeForTable($table), $all);
	}

	static function inWorkflow($docID, $table, we_database_base $db = null){
		$doc = self::getWorkflowDocument($docID, $table, we_workflow_document::STATUS_UNKNOWN, $db);
		return (!empty($doc->ID));
	}

	static function isWorkflowFinished($docID, $table){
		$doc = self::getWorkflowDocument($docID, $table);
		if(!isset($doc->ID)){
			return false;
		}
		$i = $doc->findLastActiveStep();
		return (($i <= 0) || ($i < count($doc->steps) - 1) || ($doc->steps[$i]->findNumOfFinishedTasks() < count($doc->steps[$i]->tasks)) ?
				false : true);
	}

	/**
	  Function returns true if user is in workflow for
	  defined documnet id, otherwise false
	 */
	static function isUserInWorkflow($docID, $table, $userID){
		$doc = self::getWorkflowDocument($docID, $table);
		if(isset($doc->ID)){
			$i = $doc->findLastActiveStep();
			if($i < 0){
				return false;
			}
			$j = $doc->steps[$i]->findTaskByUser($userID);
			if($j > -1){
				return ($doc->steps[$i]->tasks[$j]->Status == we_workflow_documentTask::STATUS_UNKNOWN ? true : false);
			}
		}
		return false;
	}

	/**
	  Function returns true if user can edit
	  defined documnet, otherwise false
	 */
	static function canUserEditDoc($docID, $table, $userID){
		if(permissionhandler::hasPerm("ADMINISTRATOR")){
			return true;
		}
		$doc = self::getWorkflowDocument($docID, $table);
		if(isset($doc->ID)){
			$i = $doc->findLastActiveStep();
			if($i < 0){
				return false;
			}
			$wStep = new we_workflow_step($doc->steps[$i]->workflowStepID);
			foreach($wStep->tasks as $v){
				if($v->userID == $userID && $v->Edit){
					return true;
				}
			}
		}
		return false;
	}

	static function getWorkflowDocsForUser($userID, $table, $isAdmin = false, $permPublish = false, $ws = ""){
		$db = new DB_WE();
		if($isAdmin){
			return self::getAllWorkflowDocs($table, $db);
		}
		$ids = ($permPublish ? self::getWorkflowDocsFromWorkspace($table, $ws, $db) : []);
		$wids = self::getAllWorkflowDocs($table, $db);

		foreach($wids as $id){
			if(!in_array($id, $ids)){
				if(self::isUserInWorkflow($id, $table, $userID)){
					$ids[] = $id;
				}
			}
		}

		return $ids;
	}

	static function getAllWorkflowDocs($table, we_database_base $db){
		$db->query('SELECT DISTINCT ' . WORKFLOW_DOC_TABLE . '.documentID as ID FROM ' . WORKFLOW_DOC_TABLE . ' LEFT JOIN ' . WORKFLOW_TABLE . ' ON ' . WORKFLOW_DOC_TABLE . ".workflowID=" . WORKFLOW_TABLE . '.ID WHERE ' . WORKFLOW_DOC_TABLE . '.Status = ' . we_workflow_document::STATUS_UNKNOWN . ' AND ' . WORKFLOW_TABLE . '.Type IN(' . self::getTypeForTable($table) . ')');
		return array_unique($db->getAll(true));
	}

	private static function getWorkflowDocsFromWorkspace($table, $ws, we_database_base $db){
		$wids = self::getAllWorkflowDocs($table, $db);
		$ids = [];
		foreach($wids as $id){
			if(!in_array($id, $ids)){
				if(is_array($ws) && !empty($ws)){
					if(we_users_util::in_workspace($id, $ws, $table, $db)){
						$ids[] = $id;
					}
				} else {
					$ids[] = $id;
				}
			}
		}

		return $ids;
	}

	static function findLastActiveStep($docID, $table){
		$doc = self::getWorkflowDocument($docID, $table);
		return (!isset($doc->ID) ? false : $doc->findLastActiveStep());
	}

	static function getNumberOfSteps($docID, $table){
		$doc = self::getWorkflowDocument($docID, $table);
		return (!isset($doc->ID) ? false : $doc->steps);
	}

	static function getDocumentStatusInfo($docID, $table){
		$doc = self::getWorkflowDocumentID($docID, $table);
		if($doc){
			return we_workflow_view::getDocumentStatus($doc, 700);
		}
	}

	/*
	  Cronjob function
	 */

	static function forceOverdueDocuments($userID = 0){
		$db = new DB_WE();
		$ret = '';
		$db->query('SELECT ' . WORKFLOW_DOC_TABLE . '.ID AS docID,' . WORKFLOW_DOC_STEP_TABLE . '.ID AS docstepID,' . WORKFLOW_STEP_TABLE . '.ID AS stepID ' .
			'FROM ' . WORKFLOW_DOC_TABLE . ' LEFT JOIN ' . WORKFLOW_DOC_STEP_TABLE . ' ON ' . WORKFLOW_DOC_TABLE . '.ID=' . WORKFLOW_DOC_STEP_TABLE . '.workflowDocID LEFT JOIN ' . WORKFLOW_STEP_TABLE . ' ON ' . WORKFLOW_DOC_STEP_TABLE . '.workflowStepID=' . WORKFLOW_STEP_TABLE . '.ID ' .
			'WHERE ' . WORKFLOW_DOC_STEP_TABLE . '.startDate!=0 AND (' . WORKFLOW_DOC_STEP_TABLE . '.startDate+ ROUND(' . WORKFLOW_STEP_TABLE . '.Worktime*3600))<' . time() . ' AND ' . WORKFLOW_DOC_STEP_TABLE . '.finishDate=0 AND ' . WORKFLOW_DOC_STEP_TABLE . '.Status=' . we_workflow_documentStep::STATUS_UNKNOWN . ' AND ' . WORKFLOW_DOC_TABLE . '.Status=' . we_workflow_document::STATUS_UNKNOWN);
		while($db->next_record()){
			update_time_limit(50);
			$workflowDocument = new we_workflow_document($db->f('docID'));
			$userID = $userID ? : $workflowDocument->userID;
			$_SESSION['user']['ID'] = $userID;
			if(!self::isWorkflowFinished($workflowDocument->document->ID, $workflowDocument->document->Table)){
				$workflowStep = new we_workflow_step($db->f('stepID'));
				if($workflowStep->timeAction == 1){
					$ret.='(ID: ' . $workflowDocument->ID . ') ';
					if($workflowDocument->findLastActiveStep() >= count($workflowDocument->steps) - 1){
						if($workflowDocument->workflow->LastStepAutoPublish){
							$workflowDocument->autopublish($userID, g_l('modules_workflow', '[auto_published]'), true);
							$ret.= g_l('modules_workflow', '[auto_published]') . "\n";
						} else {
							$workflowDocument->decline($userID, g_l('modules_workflow', '[auto_declined]'), true);
							$ret.=g_l('modules_workflow', '[auto_declined]') . "\n";
						}
					} else {
						$workflowDocument->approve($userID, g_l('modules_workflow', '[auto_approved]'), true);
						$ret.= g_l('modules_workflow', '[auto_approved]') . "\n";
					}
				}
				$workflowDocument->save();
			}
		}
		return $ret;
	}

	static function getLogButton($docID, $table){
		$type = self::getTypeForTable($table);
		return we_html_button::create_button('logbook', "javascript:new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=workflow&pnt=log&art=" . $docID . "&type=" . $type . "','workflow_history',-1,-1,640,480,true,false,true);");
	}

}
