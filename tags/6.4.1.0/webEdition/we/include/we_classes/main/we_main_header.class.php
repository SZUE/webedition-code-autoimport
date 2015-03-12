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
class we_main_header{

	static function pCSS($SEEM_edit_include){
		we_main_headermenu::pCSS();
		if(self::hasMsg($SEEM_edit_include)){
			we_messaging_headerMsg::pCSS();
		}
	}

	static function pJS($SEEM_edit_include){
		we_main_headermenu::pJS();
		if(self::hasMsg($SEEM_edit_include)){
			we_messaging_headerMsg::pJS();
		}
	}

	private static function hasMsg($SEEM_edit_include){
		return (defined('MESSAGING_SYSTEM') && !$SEEM_edit_include);
	}

	static function pbody($SEEM_edit_include){
		$msg = self::hasMsg($SEEM_edit_include);
		?>
		<div style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;border:0px;background-color:#efefef;background-image: url(<?php echo IMAGE_DIR ?>menu/background.gif); background-repeat: repeat-x;">
			<div style="position:absolute;top:0px;bottom:0px;left:0px;right:<?php echo $msg ? 60 : 0 ?>px;"><?php
				we_main_headermenu::pbody();
				?>
			</div>
			<?php if($msg){ ?>
				<div style="position:absolute;top:0px;bottom:0px;right:5px;width:60px;">
					<?php we_messaging_headerMsg::pbody();
					?>
				</div>
			<?php } ?>
		</div>
		<?php
	}

}
