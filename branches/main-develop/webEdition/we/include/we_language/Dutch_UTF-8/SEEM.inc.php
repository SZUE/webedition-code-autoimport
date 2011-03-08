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
 * Language file: SEEM.inc.php
 * Provides language strings.
 * Language: English
 */
$l_SEEM = array(
		'ext_doc_selected' => "U heeft een koppeling geselecteerd naar een document wat niet beheerd wordt door webEdition. Doorgaan?",
		'ext_document_on_other_server_selected' => "U heeft een koppeling gekozen welke verwijst naar een document op een andere Web server.\\nDeze opent in een nieuw browser venster. Doorgaan?",
		'ext_form_target_other_server' => "U staat op het punt een formulier te verzenden naar een andere Web server.\\nDeze opent in een nieuw venster. Doorgaan? ",
		'ext_form_target_we_server' => "Het formulier zal data versturen naar een document dat niet beheerd wordt door webEdition.\\nDoorgaan?",
		'ext_doc' => "Het huidige document: <b>%s</b> is <u>niet</u> aanpasbaar met webEdition.",
		'ext_doc_not_found' => "Kon de geselecteerde pagina <b>%s niet vinden</b>.",
		'ext_doc_tmp' => "Dit document was niet correct geopend door webEdition. Gebruik a.u.b. de normale website navigatie om het gewenste document te bereiken.",
		'info_ext_doc' => "Geen webEdition koppeling",
		'info_doc_with_parameter' => "Koppeling met parameter",
		'link_does_not_work' => "Deze koppeling is gedeactiveerd in de voorvertoning modus. Gebruik a.u.b. de hoofdnavigatie om te navigeren.",
		'info_link_does_not_work' => "Gedeactiveerd.",
		'open_link_in_SEEM_edit_include' => "U staat op het punt de content te wijzigen van het webEdition hoofd venster. Dit venster wordt gesloten. Doorgaan?",
//  Used in we_info.inc.php
		'start_mode' => "Modus",
		'start_mode_normal' => "Normaal",
		'start_mode_seem' => "seeModus",
//	When starting webedition in SEEMode
		'start_with_SEEM_no_startdocument' => "Er is geen geldig start document toegekend.\nUw Administrator moet uw start document aangeven.",
		'only_seem_mode_allowed' => "U heeft niet de juiste rechten om webEdition te starten in de normale modus.\\nseeMode wordt opgestart ...",
//	Workspace - the SEEM startdocument
		'workspace_seem_startdocument' => "Start document<br>voor seeModus",
//	Desired document is locked by another user
		'try_doc_again' => "Probeer opnieuw",
//	no permission to work with document
		'no_permission_to_work_with_document' => "U bent niet bevoegd om dit document te wijzigen.",
//	started seem with no startdocument - can select startdocument.
		'question_change_startdocument' => "Geen geldig start document toegekend.\\nWilt u nu een start document kiezen in het voorkeuren venster?",
//	started seem with no startdocument - can select startdocument.
		'no_permission_to_edit_document' => "U bent niet bevoegd om dit document te wijzigen.",
		'confirm' => array(
				'change_to_preview' => "Wilt u terugkeren naar de voorvertoning?",
		),
		'alert' => array(
				'changed_include' => "Een opgenomen bestand is gewijzigd. Hoofd document is opnieuw geladen.",
				'close_include' => "Dit bestand is geen webEdition document. Het include venster is gesloten.",
		),
);