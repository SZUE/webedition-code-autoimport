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

	static function pCSS(){
		we_main_headermenu::pCSS();
	}

	static function pJS($SEEM_edit_include){
		we_main_headermenu::pJS();
		if((defined('MESSAGING_SYSTEM') && !$SEEM_edit_include)){
			we_messaging_headerMsg::pJS();
		}
	}

	static function pbody($SEEM_edit_include){
		$msg = (defined('MESSAGING_SYSTEM') && !$SEEM_edit_include);
		?>
		<div id="weMainHeader" <?php echo ($msg ? 'style="right:60px"' : ''); ?>><?php
			we_main_headermenu::pbody();
			?>
		</div>
		<?php if($msg){ ?>
			<div id="msgheadertable"><?php we_messaging_headerMsg::pbody(); ?></div>
		<?php
		}
	}

}
