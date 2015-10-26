
include_once('define.conf.php');
// additional information should be inserted in manifest.xml, which is important for future update-services
$metaInfo = array(
	'name'=>'<?php echo $CLASSNAME; ?>',
	'classname'=>'<?php echo $CLASSNAME; ?>',
	'maintable'=><?php echo !empty($TABLECONSTANT) ? $TABLECONSTANT : '""'; ?>,
	'datasource'=><?php echo ($DATASOURCE=='table:'? "'table:'.$TABLECONSTANT":"'$DATASOURCE'");?>,
	'startpermission'=>'<?php echo $PERMISSIONCONDITION; ?>',
    'supportshooks' => 1, //set to 0 if hooks are not supported by the app, important: do also in in manifest.xml
	'use_we_tblprefix' => 1, //set to 0 if the webEdition table prefix should not be used, important: do also in in manifest.xml
    'appdisabled'=>0 //set to 1 to disable, important: do also in in manifest.xml

);