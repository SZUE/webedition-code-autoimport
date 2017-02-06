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
 * @abstract implementation class of metadata reader for PDF metadata
 */
class we_metadata_PDF extends we_metadata_metaData{

	public function __construct($filetype){
		$this->filetype = $filetype;
		$this->accesstypes = array('read');
	}

	protected function getInstMetaData($selection = ''){
		if(!$this->valid){
			return false;
		}
		if(is_array($selection)){
			// fetch some
		} else {
			$pdf = new we_helpers_pdf2text($this->datasource);
			$this->metadata = $pdf->getInfo();
		}
		return $this->metadata;
	}

}