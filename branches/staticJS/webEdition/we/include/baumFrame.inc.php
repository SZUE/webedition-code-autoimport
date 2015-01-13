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
$table = isset($table) ? $table : FILE_TABLE;
?>
<div style="position:absolute;top:0px;bottom:0px;left:0px;right:0px;">
	<div style="position:absolute;top:0px;bottom:0px;left:0px;width:24px;overflow: hidden;background-image: url(<?php echo IMAGE_DIR; ?>v-tabs/background.gif);background-repeat:repeat-y;border-top:1px solid black;">
		<?php include(WE_INCLUDES_PATH . 'we_vtabs.inc.php'); ?>
	</div>
	<div id="treeFrameDiv">
		<div id="bm_treeheaderDiv">
			<iframe frameBorder="0" src="about:blank" name="treeheader" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>
		</div>
		<div id="bm_mainDiv">
			<?php
			$Tree = new weMainTree('webEdition.php', 'top', 'top.resize.left.tree', 'top.load');
			echo $Tree->getHTMLContructX('if(top.treeResized){top.treeResized();}');
			?>
		</div>
		<div style="position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;background-repeat:repeat;margin:0px;background-image: url(<?php echo EDIT_IMAGE_DIR ?>editfooterback.gif);">
			<?php
			include(WE_INCLUDES_PATH . 'treeInfo.inc.php');
			?>
		</div>
	</div>
</div>
<?php
echo we_html_element::jsElement(
	we_base_browserDetect::isIE() ? 'window.setTimeout(top.start(), 1000);' :
		'top.start();'
);
