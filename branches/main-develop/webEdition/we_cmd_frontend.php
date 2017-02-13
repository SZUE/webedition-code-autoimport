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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

switch($cmd = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
	case 'open_wysiwyg_window':
		we_dialog_wysiwygWindow::getDialog();
		exit();

	case 'open_dialog_abbr':
	case 'open_dialog_acronym':
	case 'open_dialog_lang':
	case 'open_dialog_lang':
	case 'open_dialog_gallery':
	case 'open_dialog_image':
	case 'open_dialog_hyperlink':
		
		$noInternals = we_base_request::_(we_base_request::BOOL, 'we_dialog_args', false, 'outsideWE') ||
				we_base_request::_(we_base_request::BOOL, 'we_dialog_args', false, 'isFrontend') ||
				!isset($_SESSION['user']) || !isset($_SESSION['user']['Username']) || $_SESSION['user']['Username'] === '';

		we_base_request::_(we_base_request::BOOL, 'we_dialog_args', false, 'isFrontend'),
		$_SESSION['user']['Username'],
		$noInternals);
		
		
		if(!$noInternals){
			we_html_tools::protect();
		}

		switch($cmd){
			case 'open_dialog_abbr':
				echo we_dialog_abbr::getDialog($noInternals);
				return;
			case 'open_dialog_lang':
				echo we_dialog_lang::getDialog($noInternals);
				return;
			case 'open_dialog_acronym':
				echo we_dialog_acronym::getDialog($noInternals);
				return;
			case 'open_dialog_lang':
				echo we_dialog_lang::getDialog($noInternals);
				return;
			case 'open_dialog_gallery':
				echo we_dialog_gallery::getDialog($noInternals);
				return;
			case 'open_dialog_image':
				echo we_dialog_image::getDialog($noInternals);
				return;
			case 'open_dialog_hyperlink':
				echo we_dialog_hyperlink::getDialog($noInternals);
				return;
		}

	default:
		exit();
}
