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
 * Language: Deutsch
 */
$l_SEEM = array(
		'ext_doc_selected' => "Sie haben auf einen Link geklickt, der anscheinend auf kein von webEdition verwaltetes Dokument verweist.\\nFortfahren?",
		'ext_document_on_other_server_selected' => "Sie haben auf einen Link geklickt, der auf ein Dokument auf einem anderen Web-Server verweist. Dieses wird in einem neuen Fenster geöffnet.\\nFortfahren?",
		'ext_form_target_other_server' => "Sie wollen ein Formular abschicken, das auf einen anderen Web-Server verweist.\\nDieses wird in einem neuen Browser-Fenster geöffnet. Fortfahren??",
		'ext_form_target_we_server' => "Das Formular wird an ein Dokument verschickt,\\ndas nicht von webEdition verwaltet wird. Fortfahren?",
		'ext_doc' => "Die aktuelle Seite: <b>%s</b> ist <u>keine</u> von webEdition pflegbare Seite",
		'ext_doc_not_found' => "Die gewünschte Seite <b>%s</b> konnte nicht gefunden werden.",
		'ext_doc_tmp' => "Diese Seite wurde nicht korrekt von webEdition geöffnet. Bitte verwenden Sie die normale Navigation der Website, um zu Ihrem gewünschten Dokument zu gelangen.",
		'info_ext_doc' => "Kein webEdition-Link",
		'info_doc_with_parameter' => "Link mit Parameter",
		'link_does_not_work' => "Dieser Link wurde innerhalb des Vorschau-Modus deaktiviert.\\nBitte benutzen sie das Hauptmenü, um sich durch die Seite zu navigieren.",
		'info_link_does_not_work' => "Deaktiviert.",
		'open_link_in_SEEM_edit_include' => "Sie sind dabei den Inhalt des webEdition-Fensters zu verändern. Dabei wird dieses Fenster geschlossen.",
//  Used in we_info.inc.php
		'start_mode' => "Modus",
		'start_mode_normal' => "Normal",
		'start_mode_seem' => "seeMode",
//	When starting webedition in SEEMode
		'start_with_SEEM_no_startdocument' => "Sie haben nicht die erforderlichen Rechte, das Cockpit zu öffnen. Ihr Administrator kann Ihnen in den Einstellungen stattdessen ein Startdokument zuteilen.",
		'only_seem_mode_allowed' => "Sie haben nicht die erforderlichen Rechte, um webEdition im normalen Modus zu starten.\\nStattdessen wird der seeMode gestartet.",
//	Workspace - the SEEM startdocument
		'workspace_seem_startdocument' => "Startdokument<br>für seeMode ",
//	Desired document is locked by another user
		'try_doc_again' => "Nocheinmal versuchen.",
//	no permission to work with document
		'no_permission_to_work_with_document' => "Sie haben nicht die erforderlichen Rechte diese Seite zu bearbeiten.",
//	started seem with no startdocument - can select startdocument.
		'question_change_startdocument' => "Sie haben nicht die erforderlichen Rechte, das Cockpit zu öffnen. Wollen Sie jetzt in den Einstellungen ein Startdokument festlegen?",
//	started seem with no startdocument - can select startdocument.
		'no_permission_to_edit_document' => "Sie verfügen nicht über die benötigten Rechte, um dieses Dokument zu berabeiten.",
		'confirm' => array(
				'change_to_preview' => "Wollen Sie wieder in den Vorschau-Modus wechseln?",
		),
		'alert' => array(
				'changed_include' => "Eine Include-Datei wurde verändert. Das Hauptfenster wird neu geladen.",
				'close_include' => "Die Datei ist keine webEdition-Datei. Das Include-Fenster wird geschlossen.",
		),
);