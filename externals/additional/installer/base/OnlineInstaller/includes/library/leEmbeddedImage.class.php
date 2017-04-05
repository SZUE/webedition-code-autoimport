<?php
/**
 * $Id: leEmbeddedImage.class.php 13539 2017-03-12 11:39:19Z mokraemer $
 */

class leEmbeddedImage{

	static function get($File, $ImageType = "gif"){
		return LE_INSTALLER_URL . str_replace(LE_INSTALLER_PATH, "", $File);
	}

}
