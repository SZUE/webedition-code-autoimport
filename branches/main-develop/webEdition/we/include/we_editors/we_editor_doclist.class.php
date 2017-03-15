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
class we_editor_doclist extends we_editor_base{

	public function show(){
		$doclistView = new we_doclist_view($GLOBALS['we_doc']->getDoclistModel());
		$doclistSearch = $doclistView->searchclass;
		$results = $doclistSearch->searchProperties($doclistView->Model);
		$content = $doclistView->makeContent($results);

		$headline = $doclistView->makeHeadLines($GLOBALS['we_doc']->Table);
		$foundItems = (isset($_SESSION['weS']['weSearch']['foundItems'])) ? $_SESSION['weS']['weSearch']['foundItems'] : 0;
		return $this->getPage(
						'<div id="mouseOverDivs_' . we_search_view::SEARCH_DOCLIST . '"></div>' .
						$doclistView->getHTMLforDoclist([
							['html' => $doclistView->getSearchDialog()],
							['html' => '<div id="parametersTop_DoclistSearch">' . $doclistView->getSearchParameterTop($foundItems, we_search_view::SEARCH_DOCLIST) . '</div>' . $doclistView->tblList($content, $headline, "doclist") . "<div id='parametersBottom_DoclistSearch'>" . $doclistView->getSearchParameterBottom($foundItems, we_search_view::SEARCH_DOCLIST, $GLOBALS['we_doc']->Table) . "</div>"],
						]) .
						we_html_element::htmlHiddens(['obj' => 1
						]), $doclistView->getSearchJS(), [
					'onkeypress' => "javascript:if (event.keyCode == 13 || event.keyCode == 3) {search(true);}",
					'onload' => "setTimeout(weSearch.init, 200)",
					'onresize' => "weSearch.sizeScrollContent();"
		]);
	}

}
