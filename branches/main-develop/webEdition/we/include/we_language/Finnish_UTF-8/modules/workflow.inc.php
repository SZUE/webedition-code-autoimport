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
		'new_workflow' => "Uusi työnkulku",
		'workflow' => "Työnkulku",
		'doc_in_wf_warning' => "Dokumentti on työnkulussa!",
		'message' => "Viesti",
		'in_workflow' => "Dokumentti työnkulussa",
		'decline_workflow' => "Hylkää dokumentti",
		'pass_workflow' => "Edelleenohjaa dokumentti",
		'no_wf_defined' => "Dokumentille ei ole määritetty työnkulkua!",
		'document' => "Dokumentti",
		'del_last_step' => "Viimeistä sarjavaihetta ei voitu poistaa!",
		'del_last_task' => "Viimeistä rinnakkaisvaihetta ei voitu poistaa!",
		'save_ok' => "Työnkulku on tallennettu.",
		'delete_ok' => "Työnkulku on poistettu.",
		'delete_nok' => "Poistaminen ei onnistunut.",
		'name' => "Nimi",
		'type' => "Tyyppi",
		'type_dir' => "Hakemistopohjainen",
		'type_doctype' => "Dokumenttityyppi/Kategoriapohjainen",
		'type_object' => "Objektipohjainen",
		'dirs' => "Hakemistot",
		'doctype' => "Dokumenttin tyyppi",
		'categories' => "Kategoriat",
		'classes' => "Luokat",
		'active' => "Työnkulku on aktiivinen.",
		'step' => "Vaihe",
		'and_or' => "JA&nbsp;/&nbsp;TAI",
		'worktime' => "Worktime (H, 1min=0<b>.</b>016)", // TRANSLATE
		'specials' => "Specials", // TRANSLATE
		'EmailPath' => "Show the document path in the subject of notifications emails", // TRANSLATE
		'LastStepAutoPublish' => "After the last step (next step clicked), publish the document instead of decline ist", // TRANSLATE
		'user' => "Käyttäjä",
		'edit' => "Muokkaa",
		'send_mail' => "Lähetä sähköpostia",
		'select_user' => "Valitse käyttäjä",
		'and' => " ja ",
		'or' => " tai ",
		'waiting_on_approval' => "Odotetaan hyväksyntää käyttäjältä %s.",
		'status' => "Tila",
		'step_from' => "Vaihe %s / %s",
		'step_time' => "Vaiheen aika",
		'step_start' => "Vaiheen aloituspäivämäärä",
		'step_plan' => "Lopetuspäivämäärä",
		'step_worktime' => "Suunniteltu työaika",
		'current_time' => "Tämänhetkinen aika",
		'time_elapsed' => "Käytetty aika",
		'time_remained' => "Aikaa jäljellä",
		'todo_subject' => "Työnkulun tehtävä",
		'todo_next' => "Sinulle on odottamassa dokumentti työnkulussa.",
		'go_next' => "Seuraava vaihe",
		'new_step' => "Luo peräkkäinen lisävaihe.",
		'new_task' => "Luo rinnakkainen lisävaihe.",
		'delete_step' => "Poista peräkkäisvaihe.",
		'delete_task' => "Poista rinnakkaisvaihe.",
		'save_question' => "Kaikki työnkulkuun määritellyt dokumentit poistetaan työnkulusta.\\nOletko varma että haluat jatkaa?",
		'nothing_to_save' => "Ei mitään tallennettavaa!",
		'save_changed_workflow' => "Workflow on muuttunut.\\nHaluatko tallentaa muutokset?",
		'delete_question' => "Kaikki työnkulun tiedot poistetaan!\\nOletko varma että haluat jatkaa?",
		'nothing_to_delete' => "Ei mitään poistettavaa!",
		'user_empty' => "Vaiheelle %s ei ole määritelty käyttäjiä.",
		'folders_empty' => "Hakemistoa ei ole määritelty työnkululle!",
		'objects_empty' => "Objektia ei ole määritelty työnkululle!",
		'doctype_empty' => "Dokumenttityyppiä tai kategoriaa ei ole määritelty työnkululle",
		'worktime_empty' => "Työaikaa ei ole määritelty vaiheelle %s!",
		'name_empty' => "Työnkululle ei ole määritelty nimeä!",
		'cannot_find_active_step' => "Aktiivista vaihetta ei löydy!",
		'no_perms' => "Ei käyttöoikeutta!",
		'plan' => "(suunnitelma)",
		'todo_returned' => "Dokumentti on palautettu työnkulkuun.",
		'description' => "Kuvaus",
		'time' => "Aika",
		'log_approve_force' => "Käyttäjä on pakkohyväksynyt dokumentin.",
		'log_approve' => "Käyttäjä on hyväksynyt dokumentin.",
		'log_decline_force' => "Käyttäjä on pakkokeskeyttänyt dokumentin työnkulun.",
		'log_decline' => "Käyttäjä on keskeyttänyt työnkulun.",
		'log_doc_finished_force' => "Työnkulku on pakottaen viety loppuun.",
		'log_doc_finished' => "Työnkulku on lopetettu.",
		'log_insert_doc' => "Dokumentti on asetettu työnkulkuun.",
		'logbook' => "Lokikirja",
		'empty_log' => "Tyhjennä lokikirja",
		'emty_log_question' => "Haluatko varmasti tyhjentää lokikirjan?",
		'empty_log_ok' => "Lokikirja on nyt tyhjä.",
		'log_is_empty' => "Lokikirja on tyhjä.",
		'log_question_all' => "Tyhjennä kaikki",
		'log_question_time' => "Tyhjennä vanhemmat kuin",
		'log_question_text' => "Valitse vaihtoehto:",
		'log_remove_doc' => "Dokumentti on poistettu työnkulusta.",
		'action' => "Toiminto",
		'auto_approved' => "Dokumentti on automaattisesti hyväksytty.",
		'auto_declined' => "Dokumentti on automaattisesti hylätty.",
		'auto_published' => "Document has been automatically published.", // TRANSLATE

		'doc_deleted' => "Dokumentti on poistettu!",
		'ask_before_recover' => "Työnkulussa on yhä dokumentteja/objekteja! Haluatko poistaa ne työnkulkuprosessista?",
		'double_name' => "Työnkulun nimi on jo käytössä!",
		'more_information' => "Lisätietdot",
		'less_information' => "Piilota lisätiedot",
		'no_wf_defined_object' => "Tälle objektille ei ole määritelty työnkulkua!",
		FILE_TABLE => array(
				'messagePath' => "Dokumentti",
				'in_workflow_ok' => "Dokumentti on siirretty työnkulkuun!",
				'in_workflow_notok' => "Dokumenttia ei voitu siirtää työnkulkuun!",
				'pass_workflow_ok' => "Dokumentti on edelleenlähetetty!",
				'pass_workflow_notok' => "Dokumenttia ei voitu edelleenlähettää!",
				'decline_workflow_ok' => "Dokumentti on palautettu laatijalle!",
				'decline_workflow_notok' => "Dokumenttia ei voitu palauttaa laatijalle!",
				));
if (defined("OBJECT_FILES_TABLE")) {
	$l_modules_workflow[OBJECT_FILES_TABLE] = array(
			'messagePath' => "Objekti",
			'in_workflow_ok' => "Objekti on siirretty työnkulkuun!",
			'in_workflow_notok' => "Objektia ei voitu siirtää työnkulkuun!",
			'pass_workflow_ok' => "Objekti on edelleenlähetetty!",
			'pass_workflow_notok' => "Objektia ei voitu edelleenlähettää!",
			'decline_workflow_ok' => "Objekti on palautettu laatijalle!",
			'decline_workflow_notok' => "Objektia ei voitu palauttaa laatijalle!",
	);
}
