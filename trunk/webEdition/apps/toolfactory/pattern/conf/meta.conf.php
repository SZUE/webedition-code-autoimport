	
include_once('define.conf.php');
// additional information should be inserted in manifest.xml, which is important for future update-services
$metaInfo = array(
	'name'=>'<?php print $CLASSNAME; ?>',
	'classname'=>'<?php print $CLASSNAME; ?>',
	'maintable'=><?php print (isset($TABLECONSTANT) && !empty($TABLECONSTANT)) ? $TABLECONSTANT : '""'; ?>,
	'datasource'=><?php if($DATASOURCE=='table:') print "'table:'.$TABLECONSTANT"; else print "'$DATASOURCE'"?>,
	'startpermission'=>'<?php print $PERMISSIONCONDITION; ?>',
    'appdisabled'=>0 //set to 1 to disable, important: do also in in toc.xml
    
);