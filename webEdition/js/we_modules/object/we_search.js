/**
 * webEdition CMS
 *
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function sub() {
	document.we_form_search.target = "load";
	document.getElementsByName("SearchStart")[0].value = 0;
	document.we_form_search.action = WE().consts.dirs.WE_MODULES_DIR + "object/search_submit.php";
	document.we_form_search.todo.value = "search";
	document.we_form_search.submit();
}

function newinput() {
	document.we_form_search.target = 'load';
	document.we_form_search.action = WE().consts.dirs.WE_MODULES_DIR + "object/search_submit.php";
	document.we_form_search.todo.value = "add";
	document.we_form_search.submit();
}

function del(pos) {
	document.we_form_search.target = 'load';
	document.we_form_search.action = WE().consts.dirs.WE_MODULES_DIR + "object/search_submit.php";
	document.we_form_search.todo.value = "delete";
	document.we_form_search.position.value = pos;
	document.we_form_search.submit();
}

function changeitanyway(f) {
	document.we_form_search.target = 'load';
	document.we_form_search.action = WE().consts.dirs.WE_MODULES_DIR + 'object/search_submit.php';
	document.we_form_search.todo.value = "changemeta";
	document.we_form_search.submit();
}
