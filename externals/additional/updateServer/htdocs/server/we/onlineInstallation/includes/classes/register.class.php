<?php

class register extends registerBase{


	/**
	 * Register webEdition online and on the client
	 *
	 * @return array
	 */
	function getDontRegisterResponse(){

		if(isset($_SESSION['clientSerial']))
			unset($_SESSION['clientSerial']);

		$ret = array(
			'Type' => 'eval',
			'Code' => '<?php return true; ?>',
		);
		return updateUtil::getResponseString($ret);
	}


}
