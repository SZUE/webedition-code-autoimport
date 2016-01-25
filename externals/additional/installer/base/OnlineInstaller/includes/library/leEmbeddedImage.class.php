<?php

class leEmbeddedImage{

	static function get($File, $ImageType = "gif"){
		return LE_INSTALLER_URL . str_replace(LE_INSTALLER_PATH, "", $File);
	}

}
