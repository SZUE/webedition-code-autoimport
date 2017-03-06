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
abstract class we_workflow_log{

	const TYPE_APPROVE = 1;
	const TYPE_APPROVE_FORCE = 2;
	const TYPE_DECLINE = 3;
	const TYPE_DECLINE_FORCE = 4;
	const TYPE_DOC_FINISHED = 5;
	const TYPE_DOC_FINISHED_FORCE = 6;
	const TYPE_DOC_INSERTED = 7;
	const TYPE_DOC_REMOVED = 8;
	const NUMBER_LOGS = 8;

	static function logDocumentEvent($workflowDocID, $userID, $type, $description, we_database_base $db = null){
		$db = $db? : new DB_WE();
		$db->query('INSERT INTO ' . WORKFLOW_LOG_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'RefID' => $workflowDocID,
					'userID' => $userID,
					'Type' => $type,
					'Description' => $description
		)));
	}

	static function getLogForDocument($docID, $order = "DESC", $wfType = 0){
		$offset = we_base_request::_(we_base_request::INT, 'offset', 0);
		$db = new DB_WE();

		$q = 'FROM ' . WORKFLOW_LOG_TABLE . ' log JOIN ' . WORKFLOW_DOC_TABLE . ' doc ON log.RefID=doc.ID JOIN ' . WORKFLOW_TABLE . ' wf ON doc.workflowID=wf.ID LEFT JOIN ' . USER_TABLE . ' u ON log.userID=u.ID WHERE wf.Type IN(' . $wfType . ') AND doc.documentID=' . intval($docID) . ' ORDER BY log.logDate ' . $db->escape($order) . ',doc.ID DESC';

		$db->query('SELECT 1 ' . $q);
		$GLOBALS['ANZ_LOGS'] = $db->num_rows();

		$db->query('SELECT log.*,DATE_FORMAT(log.logDate, "'.g_l('date', '[format][mysql]') . '") AS Datum,u.First,u.Second,u.username ' . $q . ' LIMIT ' . $offset . ', ' . self::NUMBER_LOGS);
		$hash = $db->getAll();

		foreach($hash as $k => $v){
			switch($hash[$k]["Type"]){
				case self::TYPE_APPROVE:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_approve]');
					break;
				case self::TYPE_APPROVE_FORCE:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_approve_force]');
					break;
				case self::TYPE_DECLINE:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_decline]');
					break;
				case self::TYPE_DECLINE_FORCE:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_decline_force]');
					break;
				case self::TYPE_DOC_FINISHED:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_doc_finished]');
					break;
				case self::TYPE_DOC_FINISHED_FORCE:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_doc_finished_force]');
					break;
				case self::TYPE_DOC_INSERTED:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_insert_doc]');
					break;
				case self::TYPE_DOC_REMOVED:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_remove_doc]');
					break;
			}
		}
		return $hash;
	}

	static function getLogForUser($userID){
		return getHash('SELECT * FROM ' . WORKFLOW_LOG_TABLE . ' WHERE userID=' . intval($userID));
	}

	static function clearLog($stamp = 0){
		$db = new DB_WE();
		$db->query('DELETE FROM ' . WORKFLOW_LOG_TABLE . ' ' . ($stamp ? 'WHERE logDate<FROM_UNIXTIME(' . intval($stamp) . ')' : ''));
	}

}
