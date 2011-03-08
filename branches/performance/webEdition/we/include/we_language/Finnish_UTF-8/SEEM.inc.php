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
		'ext_doc_selected' => "Olet valinnut linkin, joka osoittaa dokumenttiin joka ei ole webEdition -järjestelmässä. Jatketaanko?",
		'ext_document_on_other_server_selected' => "Olet valinnut linkin joka osoittaa dokumenttiin toisella palvelimella.\\nLinkki avautuu toiseen ikkunaan. Jatketaanko?",
		'ext_form_target_other_server' => "Olet aikeissa lähettää lomakkeen toiselle palvelimelle.\\n Lomake avautuu uuteen ikkunaan. Jatketaanko? ",
		'ext_form_target_we_server' => "Lomakekäsittelijä lähettää tietoa dokumentille, joka ei ole webEdition -dokumentti.\\nJatketaanko?",
		'ext_doc' => "Kyseinen dokumentti: <b>%s</b> <u>ei ole</u> muokattavissa webEdition -järjestelmästä.",
		'ext_doc_not_found' => "Sivua <b>%s</b> ei löytynyt.",
		'ext_doc_tmp' => "webEdition ei avannut dokumenttia oikein. Käytä sivuston normaalinavigointia päästäksesi dokumenttiin.",
		'info_ext_doc' => "Ei webEdition -linkki",
		'info_doc_with_parameter' => "Parametrillinen linkki",
		'link_does_not_work' => "Tämä linkki ei ole aktivoitu esikatselutilassa. Käytä navigointia siirtyäksesi sivulle.",
		'info_link_does_not_work' => "Ei aktivoitu.",
		'open_link_in_SEEM_edit_include' => "Olet aikeissa muuttaa webEdition -ikkunan sisältöä. Ikkuna suljetaan. Jatketaanko?",
//  Used in we_info.inc.php
		'start_mode' => "Tila",
		'start_mode_normal' => "Normaali",
		'start_mode_seem' => "Helppokäyttötila",
//	When starting webedition in SEEMode
		'start_with_SEEM_no_startdocument' => "Aloitussivua ei ole määritetty.\nJärjestelmänvalvoja asettaa aloitussivusi.",
		'only_seem_mode_allowed' => "Sinulla ei ole tarvittavia oikeuksia kirjautua webEdition -järjestelmään normaalitilassa.\\nKirjaudutaan helppokäyttötilaan ...",
//	Workspace - the SEEM startdocument
		'workspace_seem_startdocument' => "Helppokäyttötilan<br>aloitussivu",
//	Desired document is locked by another user
		'try_doc_again' => "Yritä uudestaan",
//	no permission to work with document
		'no_permission_to_work_with_document' => "Sinulla ei ole oikeuksia muokata tätä dokumenttia.",
//	started seem with no startdocument - can select startdocument.
		'question_change_startdocument' => "Aloitussivua ei ole määritetty.\\nHaluatko määrittää aloitussivun Asetukset välilehdeltä?",
//	started seem with no startdocument - can select startdocument.
		'no_permission_to_edit_document' => "Sinulla ei ole oikeuksia muokata tätä dokumenttia.",
		'confirm' => array(
				'change_to_preview' => "Haluatko vaihtaa takaisin esikatselutilaan?",
		),
		'alert' => array(
				'changed_include' => "Sisällytettyä tiedostoa on muokattu. Päädokumentti ladataan uudelleen.",
				'close_include' => "Tämä ei ole webEdition dokumentti. Sisällytysikkuna on suljettu.",
		),
);