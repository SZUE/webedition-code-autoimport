/**
 * webEdition CMS
 *
 * $Rev: 9019 $
 * $Author: mokraemer $
 * $Date: 2015-01-16 23:04:21 +0100 (Fr, 16. Jan 2015) $
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

function drawNewFolder(){
	unselectAllFiles();
	top.fscmd.location.replace(top.queryString(queryType.NEWFOLDER,currentDir));
}
function RenameFolder(id){
	unselectAllFiles();
	top.fscmd.location.replace(top.queryString(queryType.RENAMEFOLDER ,currentDir,"",id));
}