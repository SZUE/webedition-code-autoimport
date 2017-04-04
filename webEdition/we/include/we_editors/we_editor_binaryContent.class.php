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
class we_editor_binaryContent extends we_editor_base{

	public function show(){
		return $this->getPage(we_html_multiIconBox::getHTML("weImgProp", [
					['icon' => we_html_multiIconBox::PROP_UPLOAD,
						"headline" => "",
						"html" => $this->we_doc->formUpload(),
						'space' => we_html_multiIconBox::SPACE_ICON
					],
					['icon' => we_html_multiIconBox::PROP_ATTRIB,
						"headline" => g_l('weClass', '[attribs]'),
						"html" => $this->we_doc->formProperties(),
						'space' => we_html_multiIconBox::SPACE_ICON
					],
					['icon' => we_html_multiIconBox::PROP_META,
						"headline" => g_l('weClass', '[metadata]'),
						"html" => $this->we_doc->formMetaInfos() . $this->we_doc->formMetaData(),
						'space' => we_html_multiIconBox::SPACE_ICON
					]
					], 20));
	}

}
