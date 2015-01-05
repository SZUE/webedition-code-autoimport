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

/**
 * @return void
 * @desc prints the functions needed for the tree.
 */
function pWebEdition_Tree(){
	$Tree = new weMainTree("webEdition.php", "top", "self.Tree", "top.load");
	echo $Tree->getJSTreeCode();
}

/**
 * @return void
 * @desc the frameset for the SeeMode
 */
function pWebEdition_Frameset($SEEM_edit_include){
	?>
	<div style="position:absolute;top:0px;left:0px;right:0px;height:32px;border-bottom: 1px solid black;">
		<?php we_main_header::pbody($SEEM_edit_include); ?>
	</div>
	<div style="position:absolute;top:32px;left:0px;right:0px;bottom:0px;border: 0px;">
		<iframe frameBorder="0" src="<?php echo WEBEDITION_DIR; ?>resizeframe.php" id="rframe" name="rframe"></iframe>
	</div>
	<div style="position:absolute;left:0px;right:0px;bottom:0px;height: 0px;">
		<iframe src="about:blank" style="overflow: hidden;" name="load"></iframe>
		<iframe src="about:blank" style="overflow: hidden;" name="load2"></iframe>
		<iframe src="about:blank" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="ad"></iframe>
		<iframe src="about:blank" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="postframe"></iframe>
		<iframe src="about:blank" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="plugin"></iframe>
			<?php include(WE_USERS_MODULE_PATH . 'we_users_ping.inc.php'); ?>
	</div>
	<?php
}
