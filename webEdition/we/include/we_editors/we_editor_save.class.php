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
abstract class we_editor_save{

	public static function publishInc($we_transaction, $we_responseText = '', $we_responseTextType = '', $we_JavaScript = ''){
		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'editor_save.js', '', ['id' => 'loadVarEditor_save', 'data-editorSave' => setDynamicVar([
					'we_editor_save' => false,
					'we_transaction' => $we_transaction,
					//FIXME:we_JavaScript is evaled
					'we_JavaScript' => $we_JavaScript,
					'we_responseText' => $we_responseText,
					'we_responseTextType' => $we_responseTextType,
			])]), we_html_element::htmlBody());
	}

}
