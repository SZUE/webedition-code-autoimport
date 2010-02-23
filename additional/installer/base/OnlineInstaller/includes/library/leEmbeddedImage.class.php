<?php
class leEmbeddedImage {
	function get($File, $ImageType = "gif") {

		// Check if IE
		// disabled, did not work on strato shared hosting packages 
		// don't know why, but the embeddedimages weren't displayed in all browsers
		/*
		if(!eregi("MSIE", $_SERVER['HTTP_USER_AGENT'])
			&& file_exists($File)
			&& is_file($File)) {

			$Contents = implode("", file($File));
			$EmbeddedImage = "data:image/" . $ImageType . ";base64,";
			$EmbeddedImage .= base64_encode($Contents);

			return $EmbeddedImage;

		}
		*/

		$Path = str_replace(LE_INSTALLER_PATH, "", $File);
		$Path = LE_INSTALLER_URL . $Path;

		return $Path;

	}
}