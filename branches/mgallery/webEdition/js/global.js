/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 9724 $
 * $Author: mokraemer $
 * $Date: 2015-04-14 12:36:38 +0200 (Di, 14. Apr 2015) $
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
 * @package    we_ui
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**This file is intended to be a global file for many js functions in WE*/

/**
 * Get a file icon out of a given type, used in tree, selectors & tabs
 * @param {type} contentType
 * @param {type} open
 * @returns icon to be drawn as html-code
 */
function getTreeIcon(contentType, open, extension) {
	switch (contentType) {
		case 'cockpit':
		return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-cubes fa-stack-2x we-color"></i></span>';
		case 'class_folder'://FIXME: this contenttype is not set
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-folder' + (open ? '-open' : '') + ' we-color fa-stack-2x"></i><i class="fa fa-folder' + (open ? '-open' : '') + '-o fa-stack-2x"></i><span class="we-classification"><i class="fa fa-stack-1x">C</i></span></span>';
		case 'folder':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-folder' + (open ? '-open' : '') + ' we-color fa-stack-2x"></i><i class="fa fa-folder' + (open ? '-open' : '') + '-o fa-stack-2x"></i></span>';
		case  'image/*':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-image-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i></span>';
		case 'text/js':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-o fa-stack-2x"></i><span class="we-otherfiles"><i class="fa fa-stack-1x">js</i></span></span>';
		case 'text/css':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-o fa-stack-2x"></i><span class="we-otherfiles"><i class="fa fa-stack-1x">css</i></span></span>';
		case 'text/htaccess':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-o fa-stack-2x"></i><span class="we-otherfiles"><i class="fa fa-stack-1x">ht</i></span></span>';
		case 'text/weTmpl':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span><span class="we-classification"><i class="fa fa-stack-1x">T</i></span></span>';
		case 'text/webedition':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-text-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span></span>';
		case 'text/xml':
		case 'text/html':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-code-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i></span>';
		case 'application/x-shockwave-flash':
		case 'video/quicktime':
		case 'video/*':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-video-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i></span>';
		case 'audio/*':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-sound-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i></span>';
		case 'text/plain':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-text-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i></span>';
		case 'application/*':
			switch (extension) {
				case '.pdf':
					return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-pdf-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i></span>';
				case '.zip' :
				case '.sit' :
				case '.hqx' :
				case '.bin' :
					return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-archive-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i></span>';
				case '.odg':
				case '.otg':
				case '.odt':
				case '.ott':
				case '.dot' :
				case '.doc' :
					return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-word-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i></span>';
				case '.ods':
				case '.ots':
				case '.xlt' :
				case '.xls' :
					return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-table fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i></span>';
				case '.odp':
				case '.otp':
				case '.ppt' :
					return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-line-chart fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i></span>';
				default:
					return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-o fa-stack-2x"></i></span>';
			}
		case 'object':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span><span class="we-classification"><i class="fa fa-stack-1x">C</i></span></span>';
		case 'objectFile':
			return '<span class="fa-stack fa-lg fileicon"><i class="fa fa-file-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span><span class="we-classification"><i class="fa fa-stack-1x">O</i></span></span>';
		case 'text/weCollection':
//Banner module
		case 'banner':
		case 'bannerFolder':
		default:
			return '<span class="fa-stack fa-lg fileicon ' + contentType + '"><i class="fa fa-file-o fa-stack-2x"></i></span>';
			//FIXME: add support for file exension apllication pdf/word/excel/...
	}
}