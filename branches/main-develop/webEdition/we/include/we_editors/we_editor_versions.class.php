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
class we_editor_versions extends we_editor_base{

	public function show(){
		$versionsView = new we_versions_view($this->we_doc->versionsModel);
		$content = $versionsView->getVersionsOfDoc();
		$foundItems = count($content);

		return $this->getPage($versionsView->getHTMLforVersions([
							["html" => "<div id='searchTable'>" . $versionsView->getBodyTop() . '</div>'],
							["html" => "<div id='parametersTop'>" . $versionsView->getParameterTop($foundItems) . '</div>' . $versionsView->tblList($content, $versionsView->makeHeadLines()) . "<div id='parametersBottom'>" . $versionsView->getParameterBottom($foundItems) . "</div>"]
						]), we_editor_script::get() .
						$versionsView->getJS() .
						we_html_element::cssLink(CSS_DIR . 'we_versions.css', ['media' => 'screen']) .
						we_html_element::cssLink(CSS_DIR . 'we_versions_print.css', ['media' => 'print']), [
					'onkeypress' => "javascript:if (event.keyCode == 13 || event.keyCode == 3){search(true);}",
					'onload' => "init();",
					'onresize' => "sizeScrollContent();"
		]);
	}

}
