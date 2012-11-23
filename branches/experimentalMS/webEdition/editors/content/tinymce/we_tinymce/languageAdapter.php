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
 * @package    webEdition_tinymce
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we.inc.php');

header("Content-Type: text/javascript");

?>

//
// ----> Translate Textoutput in webedition's custom tinyMCE-plugins
//

tinyMceGL = {
	welink : { 
			tooltip : '<?php echo g_l('wysiwyg', "[hyperlink]"); ?>'
			},
	weimage: {
			tooltip : '<?php echo g_l('wysiwyg', "[insert_edit_image]"); ?>',
			},
	weabbr : { 
			tooltip : '<?php echo g_l('wysiwyg', "[abbr]"); ?>'
			},
	weacronym : {
			tooltip : '<?php echo g_l('wysiwyg', "[acronym]"); ?>',
			},
	weinsertbreak : { 
			tooltip : '<?php echo g_l('wysiwyg', "[insert_br]"); ?>'
			},
	weinsertrtf : {
			tooltip : '<?php echo g_l('wysiwyg', "[rtf_import]"); ?>',
			},
	welang : {
			tooltip : '<?php echo g_l('wysiwyg', "[language]"); ?>',
			},
	wespellchecker : {
			tooltip : '<?php echo g_l('wysiwyg', '[spellcheck]'); ?>',
			},
	wevisualaid : {
			tooltip : '<?php echo g_l('wysiwyg', "[visible_borders]"); ?>',
			}
};
