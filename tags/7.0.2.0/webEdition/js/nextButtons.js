/*
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
function showRefreshButton() {
	prevBut = document.getElementById("prev");
	nextBut = document.getElementById("next");
	refrBut = document.getElementById("refresh");
	prevBut.style.display = "none";
	nextBut.style.display = "none";
	refrBut.style.display = "";
}
function showPrevNextButton() {
	prevBut = document.getElementById("prev");
	nextBut = document.getElementById("next");
	refrBut = document.getElementById("refresh");
	refrBut.style.display = "none";
	prevBut.style.display = "";
	nextBut.style.display = "";
}