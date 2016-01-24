<?php

class modules extends modulesBase{


	/**
	 * Register webEdition online and on the client
	 *
	 * @return array
	 */
	function getRegisterModulesResponse($Modules = array()){

		$_SESSION['clientDesiredModules'] = array_keys($Modules);

		$ret = array(
			'Type' => 'eval',
			'Code' => '<?php return true; ?>',
		);
		return updateUtil::getResponseString($ret);
	}

}
