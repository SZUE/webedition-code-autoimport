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
class we_document_deprecatedVideo extends we_document_video{

	public function getPropertyPage(){
		return we_html_multiIconBox::getHTML('PropertyPage', array(
			array('icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(), 'space' => 140),
			array('icon' => 'doc.gif', 'headline' => g_l('weClass', '[document]'), 'html' => $this->formIsProtected(), 'space' => 140),
			array('icon' => 'default.gif', 'headline' => g_l('weClass', '[other]'), 'html' => $this->formOther(), 'space' => 140)));
	}

}
