<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package none
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * static class with utility image functions
 *
 * @category   we
 * @package none
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
abstract class we_ui_layout_Image{


	/**
	 * loading
	 */
	const kLoading = '/webEdition/images/logo-busy.gif';

	/**
	 * Maps the contentType to its css class name to display specific icons
	 *
	 * @param string $contentType
	 * @param string $extension
	 * @return string
	 */
	public static function getIconClass($contentType, $extension = ''){
		switch($contentType){
			case we_base_ContentTypes::IMAGE:
				return "image";
			case we_base_ContentTypes::WEDOCUMENT:
				return "we_document";
			case we_base_ContentTypes::HTML:
				return "text_html";
			case we_base_ContentTypes::FOLDER:
				return "folder";
			case "folderOpen" :
				return "folderOpen";
			case we_base_ContentTypes::CSS:
				return "text_css";
			case we_base_ContentTypes::TEMPLATE:
				return "text_weTmpl";
			case we_base_ContentTypes::JS:
				return "text_js";
			case we_base_ContentTypes::TEXT:
				return "text_plain";
			case we_base_ContentTypes::HTACCESS:
				return "text_htaccess";
			case we_base_ContentTypes::XML:
				return "text_xml";
			case we_base_ContentTypes::FLASH:
				return "flash";
			case we_base_ContentTypes::OBJECT:
				return "object";
			case we_base_ContentTypes::OBJECT_FILE:
				return "objectFile";
			case we_base_ContentTypes::APPLICATION:
				switch($extension){
					case ".pdf" :
						return "pdf";
					case ".zip" :
					case ".sit" :
					case ".hqx" :
					case ".bin" :
						return "zip";
					case ".doc" :
						return "word";
					case ".xls" :
						return "excel";
					case ".ppt" :
						return "powerpoint";
				}
				return "text_plain";
			default :
				return "text_plain";
		}
	}

}
