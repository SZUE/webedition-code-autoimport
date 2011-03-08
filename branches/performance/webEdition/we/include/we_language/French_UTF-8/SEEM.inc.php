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
		'ext_doc_selected' => "You have selected a link which points to a document that is not administered by webEdition. Continue?", // TRANSLATE
		'ext_document_on_other_server_selected' => "Vous avez cliquez sur un lien, qui mène à un document sur un autre web-serveur. Celui sera ouvert dans une nouvelle fenêtre.\\nPousuivre?",
		'ext_form_target_other_server' => "Vous voulez envoyer un formulaire à un autre web-serveur.\\nCelui sera ouvert dans une nouvelle fenêtre. Poursuivre?",
		'ext_form_target_we_server' => "Le formulaire est envoyer à un non webEdition document. Poursuivre?",
		'ext_doc' => "La page: <b>%s</b> n'est <u>pas</u> pas editable avec webEdition",
		'ext_doc_not_found' => "La page demandée <b>%s</b> ne pouvait pas être trouvée.",
		'ext_doc_tmp' => "La page n'a pas été ouvert correctement par webEdition. S'il vous plaôt utiliser la navigation normale du site, pour aller au document voulu.",
		'info_ext_doc' => "Non lien-webEdition",
		'info_doc_with_parameter' => "Lien avec paramètres",
		'link_does_not_work' => "Ce lien a été désactivé dans le mode-de-Prévision.\\nS'il vous plaît utilisez le menu principale, pour naviguer par le site.",
		'info_link_does_not_work' => "Desactivé.",
		'open_link_in_SEEM_edit_include' => "Vous êtes en train de changer le contenu de la fenêtre-webEdtion. Cette fenêtre sera fermé. Poursuivre?",
//  Used in we_info.inc.php
		'start_mode' => "Mode", // TRANSLATE
		'start_mode_normal' => "Normal", // TRANSLATE
		'start_mode_seem' => "seeMode", // TRANSLATE
//	When starting webedition in SEEMode
		'start_with_SEEM_no_startdocument' => "Pour le moment aucune page d'accueil valide est défini.\\nCela peut être défini par votre administrateur eingestellt werden.\\nLa page d'accueil du web-serveur sera overt.",
		'only_seem_mode_allowed' => "Vous ne disposez pas des droits nécessaires, pour démarrer webEdition le Mode normal.\\nEn remplacement le seeMode est démarré.",
//	Workspace - the SEEM startdocument
		'workspace_seem_startdocument' => "Page d'accueil<br>pour le seeMode ",
//	Desired document is locked by another user
		'try_doc_again' => "Essayer de nouveau.",
//	no permission to work with document
		'no_permission_to_work_with_document' => "Vous ne disposez pas des droits nécessaires pour éditer cette page.",
//	started seem with no startdocument - can select startdocument.
		'question_change_startdocument' => "Pour le moment aucune page d'accueil valide est défini.\\nVous voulez définir une page d'accueil dans les préférences maintenant.",
//	started seem with no startdocument - can select startdocument.
		'no_permission_to_edit_document' => "Vous ne disposez pas des droits nécessairse, pour éditer ce document.",
		'confirm' => array(
				'change_to_preview' => "Voulez-vous changer au Mode-de-Prévision?",
		),
		'alert' => array(
				'changed_include' => "Un fichier-inclu a été modifié. La fenêtre principal sera actualisée.",
				'close_include' => "This file is no webEdition document. The include window is closed.", // TRANSLATE
		),
);