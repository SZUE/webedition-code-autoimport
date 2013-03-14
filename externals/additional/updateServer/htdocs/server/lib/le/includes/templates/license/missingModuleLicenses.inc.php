<?php

	print $GLOBALS['lang']['license']['missingModuleLicenses'];

	print '<ul>';

	foreach ($GLOBALS['missingModuleLicenses'] as $moduleKey) {
	
		print "	<li>$moduleKey</li>\n";
	}
	
	print '</ul>';

?>