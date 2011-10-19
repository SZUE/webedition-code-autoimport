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
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/**
 * Language file: workflow.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_workflow = array(
		'new_workflow' => "New workflow", // TRANSLATE
		'workflow' => "Workflow", // TRANSLATE

		'doc_in_wf_warning' => "The document is in workflow!", // TRANSLATE
		'message' => "Сообщение",
		'in_workflow' => "Документ в потоке",
		'decline_workflow' => "Отклонить документ",
		'pass_workflow' => "Документ перенаправить",
		'no_wf_defined' => "Для данного документа не задан поток!",
		'document' => "Document", // TRANSLATE

		'del_last_step' => "Нельзя удалить заключительный последовательный уровень!",
		'del_last_task' => "Нельзя удалить заключительный параллельный уровень!",
		'save_ok' => "Поток сохранен",
		'delete_ok' => "Поток удален",
		'delete_nok' => "Поток невозможно удалить",
		'name' => "Имя",
		'type' => "Тип",
		'type_dir' => "На базе директории",
		'type_doctype' => "На базе типа документа/категории",
		'type_object' => "На базе объекта",
		'dirs' => "Директории",
		'doctype' => "Тип документа",
		'categories' => "Категории",
		'classes' => "Классы",
		'active' => "Поток активирован",
		'step' => "Уровень",
		'and_or' => "И&nbsp;/&nbsp;ИЛИ",
		'worktime' => "Worktime (H, 1min=0<b>.</b>016)", // TRANSLATE
		'specials' => "Specials", // TRANSLATE
		'EmailPath' => "Show the document path in the subject of notifications emails", // TRANSLATE
		'LastStepAutoPublish' => "After the last step (next step clicked), publish the document instead of decline ist", // TRANSLATE
		'user' => "Пользователь",
		'edit' => "редактировать",
		'send_mail' => "отправить письмо",
		'select_user' => "Select user", // TRANSLATE

		'and' => " and ", // TRANSLATE
		'or' => " or ", // TRANSLATE

		'waiting_on_approval' => "Ожидание резолюции %s",
		'status' => "Статус",
		'step_from' => "Уровень %s из %s",
		'step_time' => "Step time", // TRANSLATE
		'step_start' => "Дата начала",
		'step_plan' => "Дата завершения",
		'step_worktime' => "Запланированное время обработки",
		'current_time' => "Текущее время",
		'time_elapsed' => "Использованное время",
		'time_remained' => "Оставшееся время",
		'todo_subject' => "Задание для потока",
		'todo_next' => "Документ следующий в очереди на обработку",
		'go_next' => "Next step", // TRANSLATE

		'new_step' => "Create additional serial step.", // TRANSLATE
		'new_task' => "Create additional parallel step.", // TRANSLATE

		'delete_step' => "Delete serial step.", // TRANSLATE
		'delete_task' => "Delete parallel step.", // TRANSLATE

		'save_question' => "All documents that are in the workflow will be removed from it.\\nAre you sure that you want to do this?", // TRANSLATE
		'nothing_to_save' => "Nothing to save!", // TRANSLATE
		'save_changed_workflow' => "Workflow has been changed.\\nDo you want to save your changes?", // TRANSLATE

		'delete_question' => "All workflow data will be deleated!\\nAre you sure that you want to do this?", // TRANSLATE
		'nothing_to_delete' => "Nothing to delete!", // TRANSLATE

		'user_empty' => "No defined users for step %s.", // TRANSLATE
		'folders_empty' => "Folder is not defined for workflow!", // TRANSLATE
		'objects_empty' => "Object is not defined for workflow!", // TRANSLATE
		'doctype_empty' => "Document type or category are not defined for workflow", // TRANSLATE
		'worktime_empty' => "Worktime is not defined for step %s!", // TRANSLATE
		'name_empty' => "Name is not defined for workflow!", // TRANSLATE
		'cannot_find_active_step' => "Cannot find active step!", // TRANSLATE

		'no_perms' => "No permissions!", // TRANSLATE
		'plan' => "(plan)", // TRANSLATE

		'todo_returned' => "Документ возвращен из потока",
		'description' => "Description", // TRANSLATE
		'time' => "Time", // TRANSLATE

		'log_approve_force' => "User has forcibly approved document.", // TRANSLATE
		'log_approve' => "User has approved document.", // TRANSLATE
		'log_decline_force' => "User has forcibly cancelled document workflow.", // TRANSLATE
		'log_decline' => "User has cancelled document workflow.", // TRANSLATE
		'log_doc_finished_force' => "Workflow has been forcibly finished.", // TRANSLATE
		'log_doc_finished' => "Workflow is finished.", // TRANSLATE
		'log_insert_doc' => "Document has been inserted into wokflow.", // TRANSLATE

		'logbook' => "Журнал записей",
		'empty_log' => "Empty logbook", // TRANSLATE
		'emty_log_question' => "Do you really want to empty the logbook?", // TRANSLATE
		'empty_log_ok' => "The logbook is now emtpy.", // TRANSLATE
		'log_is_empty' => "The logbook is emtpy.", // TRANSLATE

		'log_question_all' => "Clear all", // TRANSLATE
		'log_question_time' => "Clear older than", // TRANSLATE
		'log_question_text' => "Choose option:", // TRANSLATE

		'log_remove_doc' => "Document is removed from workflow.", // TRANSLATE
		'action' => "Action", // TRANSLATE

		'auto_approved' => "Document has been automatically approved.", // TRANSLATE
		'auto_declined' => "Document has been automatically declined.", // TRANSLATE
		'auto_published' => "Document has been automatically published.", // TRANSLATE

		'doc_deleted' => "Document has been deleted!", // TRANSLATE
		'ask_before_recover' => "There are still documents/objects in the workflow process! Do you want to remove them from the workflow process?", // TRANSLATE

		'double_name' => "Workflow name already exists!", // TRANSLATE

		'more_information' => "Больше информации",
		'less_information' => "Меньше информации",
		'no_wf_defined_object' => "Для данного объекта не задан рабочий поток!",
		'tblFile' => array(
				'messagePath' => "Документ",
				'in_workflow_ok' => "Документ успешно передан в поток!",
				'in_workflow_notok' => "Невозможно передать документ в поток!",
				'pass_workflow_ok' => "Документ успешно перенаправлен!",
				'pass_workflow_notok' => "Невозможно перенаправить документ!",
				'decline_workflow_ok' => "Документ возвращен автору!",
				'decline_workflow_notok' => "Невозможно возвратить документ его автору!",
				),

	'tblObjectFiles' => array(
			'messagePath' => "Объект",
			'in_workflow_ok' => "Объект успешно передан в поток!",
			'in_workflow_notok' => "Невозможно передать объект в поток!",
			'pass_workflow_ok' => "Объект успешно перенаправлен!",
			'pass_workflow_notok' => "Невозможно перенаправить объект!",
			'decline_workflow_ok' => "Объект возвращен автору!",
			'decline_workflow_notok' => "Невозможно возвратить объект его автору!",
	),
);
