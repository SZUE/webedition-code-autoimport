<?php

class banner extends bannerBase {
	
	
	function getBannerHtml() {
		
		if (defined("BANNER_URL") && BANNER_URL) {
			
			// which programm?
			// which language?
			// which version?
			// which action?
			// additional informations
			
			$details = "";
			
			switch ($_SESSION['update_cmd']) {
				
				case "languages":
					$details = urlencode(base64_encode(serialize($_SESSION["clientDesiredLanguages"])));
				break;
				case "modules":
					$details = urlencode(base64_encode(serialize($_SESSION["clientDesiredModules"])));
				break;
				
				default:
					$details = "";
				break;
			}
			
			$url = BANNER_URL . "?programme=" . UPDATE_PROGRAMME . "&language=" . $_SESSION["clientLng"] . "&version=" . $_SESSION["clientVersion"] . "&cmd=" . $_SESSION['update_cmd'] . "&details=$details";
			
			
			return '<br />
<iframe src="' . $url . '" style="width: 100%; height: 100px; border: 0;" border="0" frameborder="0" scrolling="no"></iframe>';
		} else {
			return "";
		}
	}
	
	function getBannerHeader() {
		
		if (defined("BANNER_URL") && BANNER_URL) {
			
			return '<style type="text/css">
	body {
		background: none;
	}
</style>';
		} else {
			return "";
		}
	}
}

?>