<?php

class debugBase {

	function log($var) {

		ob_start('error_log');
		print "\n>>>>>>>>>>>>>>>>>>>> SERVER\n";
		var_dump($var);
		print '<<<<<<<<<<<<<<<<<<<< SERVER';
		ob_end_clean();
	}

}

?>