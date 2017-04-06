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

/**
 *  view class for document customer filters
 *
 */
class we_navigation_customerFilterView extends we_customer_filterView{

	public function __construct(&$filter, $width = 0){
		parent::__construct($filter, $width);
		$this->viewType = 'navigation';
	}

	/**
	 * Gets the HTML and Javascript for the filter
	 *
	 * @return string
	 */
	function getFilterHTML($isDynamic = false){
		$filter = $this->getFilter();
		return we_html_forms::checkboxWithHidden(
				$filter->getUseDocumentFilter(), 'wecf_useDocumentFilter', g_l('navigation', '[useDocumentFilter]'), false, 'defaultfont', 'updateView_navigation();', $isDynamic
			) . $this->getDiv(
				'<div style="border-top: 1px solid #AFB0AF;margin-bottom: 5px;"></div>' . parent::getFilterHTML(true), 'MainFilterDiv', !$filter->getUseDocumentFilter()
		);
	}

}
