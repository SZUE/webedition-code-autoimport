/* global WE, top */

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

if (WE(true)) {
	var passwd = WE().util.getDynamicVar(document, 'loadVarComparePwd', 'data-passwd');
}

function saveOnKeyBoard() {
	document.forms[0].submit();
	return true;
}
function closeOnEscape() {
	return true;
}

function comparePwd(f1, f2) {
	var pwd1 = document.getElementsByName(f1)[0];
	var pwd2 = document.getElementsByName(f2)[0];
	var re = new RegExp(passwd.pwdCheck);
	if (!re.test(pwd1.value)) {
		pwd1.classList.add("weMarkInputError");
		return 1;
	} else {
		pwd1.classList.remove("weMarkInputError");
		if (pwd1.value !== pwd2.value) {
			pwd2.classList.add("weMarkInputError");
			return 2;
		} else {
			pwd2.classList.remove("weMarkInputError");
		}
	}
	return 0;
}

function setPwdErr(status) {
	switch (status) {
		case 0:
			document.getElementById('badPwd').style.display = 'none';
			document.getElementById('badPwd2').style.display = 'none';
			break;
		case 1:
			document.getElementById('badPwd').style.display = 'block';
			document.getElementById('badPwd2').style.display = 'none';
			break;
		case 2:
			document.getElementById('badPwd').style.display = 'none';
			document.getElementById('badPwd2').style.display = 'block';
			break;
	}
}
