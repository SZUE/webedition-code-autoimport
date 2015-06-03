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
	pre = '<span class="fa-stack fa-lg fileicon"><i class="fa fa-square fa-stack-2x fa-fw"></i>';
	post = '</span>';
	switch (contentType) {
		case 'cockpit':
			return pre + '<i class="fa fa-cubes fa-stack-2x we-color"></i>' + post;
		case 'class_folder'://FIXME: this contenttype is not set
			return pre + '<i class="fa fa-folder' + (open ? '-open' : '') + ' fa-stack-2x"></i><i class="fa fa-folder' + (open ? '-open' : '') + '-o fa-stack-2x"></i><span class="we-classification"><i class="fa fa-stack-1x">C</i></span>' + post;
		case 'we/bannerFolder':
		case 'folder':
			return pre + '<i class="fa fa-folder' + (open ? '-open' : '') + ' fa-stack-2x"></i><i class="fa fa-folder' + (open ? '-open' : '') + '-o fa-stack-2x"></i>' + post;
		case  'image/*':
			return pre + '<i class="fa fa-file-image-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
		case 'text/js':
			return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-otherfiles"><i class="fa fa-stack-1x">js</i></span>' + post;
		case 'text/css':
			return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-otherfiles"><i class="fa fa-stack-1x">css</i></span>' + post;
		case 'text/htaccess':
			return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-otherfiles"><i class="fa fa-stack-1x">ht</i></span>' + post;
		case 'text/weTmpl':
			return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span><span class="we-classification"><i class="fa fa-stack-1x">T</i></span>' + post;
		case 'text/webedition':
			return pre + '<i class="fa fa-file-text-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span>' + post;
		case 'text/xml':
		case 'text/html':
			return pre + '<i class="fa fa-file-code-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
		case 'application/x-shockwave-flash':
		case 'video/quicktime':
		case 'video/*':
			return pre + '<i class="fa fa-file-video-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
		case 'audio/*':
			return pre + '<i class="fa fa-file-sound-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
		case 'text/plain':
			return pre + '<i class="fa fa-file-text-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
		case 'application/*':
			switch (extension) {
				case '.pdf':
					return pre + '<i class="fa fa-file-pdf-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				case '.zip' :
				case '.sit' :
				case '.hqx' :
				case '.bin' :
					return pre + '<i class="fa fa-file-archive-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				case '.odg':
				case '.otg':
				case '.odt':
				case '.ott':
				case '.dot' :
				case '.doc' :
					return pre + '<i class="fa fa-file-word-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				case '.ods':
				case '.ots':
				case '.xlt' :
				case '.xls' :
					return pre + '<i class="fa fa-table fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				case '.odp':
				case '.otp':
				case '.ppt' :
					return pre + '<i class="fa fa-line-chart fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
				default:
					return pre + '<i class="fa fa-file-o fa-stack-2x"></i>' + post;
			}
		case 'object':
			return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span><span class="we-classification"><i class="fa fa-stack-1x">C</i></span>' + post;
		case 'objectFile':
			return pre + '<i class="fa fa-file-o fa-stack-2x"></i><span class="we-icon"><i class="fa fa-circle fa-stack-1x"></i><i class="fa fa-stack-1x fa-inverse">e</i></span><span class="we-classification"><i class="fa fa-stack-1x">O</i></span>' + post;
		case 'text/weCollection':
			return pre + '<i class="fa fa-archive fa-stack-2x we-color"></i>' + post;
//Banner module
		case 'we/banner':
			return pre + '<i class="fa fa-flag-checkered fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
		case 'we/costumer':
			return pre + '<i class="fa fa-user fa-stack-2x we-color"></i>' + post;
		case 'we/costumerGroup':
			return pre + '<i class="fa fa-user fa-stack-2x we-color"></i>' + post;
		case 'we/userGroup':
			return pre + '<i class="fa fa-users fa-stack-2x we-color"></i>' + post;
		case 'we/alias':
			return pre + '<i class="fa fa-user fa-stack-2x" style="color:grey"></i>' + post;
		case 'we/customer':
		case 'we/user':
			return pre + '<i class="fa fa-user fa-stack-2x we-color"></i>' + post;
		case 'we/export':
		case 'we/glossar':
			return pre + '<i class="fa fa-file-text-o fa-stack-2x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
		case 'we/newsletter':
			return pre + '<i class="fa fa-newspaper-o fa-stack-2x we-color"></i>' + post;
		case 'we/voting':
			return pre + '<i class="fa fa-thumbs-up fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
		case 'we/navigation':
			return pre + '<i class="fa fa-compass fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
		case 'we/search':
			return pre + '<i class="fa fa-search fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
		case 'we/shop':
			return pre + '<i class="fa fa-shopping-cart fa-stack-1x we-color"></i><i class="fa fa-file-o fa-stack-2x"></i>' + post;
		default:
			return pre + '<i class="fa fa-file-o fa-stack-2x ' + contentType + '"></i>' + post;
			//FIXME: add support for file exension apllication pdf/word/excel/...
	}
}