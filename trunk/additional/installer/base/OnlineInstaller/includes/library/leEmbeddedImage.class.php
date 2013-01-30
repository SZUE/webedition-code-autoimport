<?php
class leEmbeddedImage {
	static function get($File, $ImageType = "gif") {

		$Path = str_replace(LE_INSTALLER_PATH, "", $File);
		$Path = LE_INSTALLER_URL . $Path;

		return $Path;
	}
}