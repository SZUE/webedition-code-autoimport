	
include_once('define.conf.php');
// additional information should be inserted in manifest.xml, which is important for future update-services
$metaInfo = array(
	'name'=>'<?php print $CLASSNAME; ?>',
	'classname'=>'<?php print $CLASSNAME; ?>',
	'maintable'=><?php print (isset($TABLECONSTANT) && !empty($TABLECONSTANT)) ? $TABLECONSTANT : '""'; ?>,
	'datasource'=><?php if($DATASOURCE=='table:') print "'table:'.$TABLECONSTANT"; else print "'$DATASOURCE'"?>,
	'startpermission'=>'<?php print $PERMISSIONCONDITION; ?>',
    'version'=>'0.01', // Startwert 0.01 um zweistellige Vorabversionen zu ermöglichen, für offizielle Versionen ab 1.0 nutzt man 1000 und aufwärts (Versionsnummern alla PHP 5.2.13 also als 5213  
    'minWEversion'=>'<?php print we_util_Strings::version2number(WE_VERSION,false); ?>', //hier steht dann die aktuelle als Zahl (nicht als version), kann von Hand reduziert werden
    'SDKversion'=>'<?php print we_util_Strings::version2number(WE_VERSION,false); ?>', //hier steht dann die aktuelle als Zahl (nicht als version), kann von Hand reduziert werden, um später auch Apps für ältere SDK-Versionen unterstützen zu können
    'author'=>'', //Name
    'authorurl'=>'', // url ohne http://
    'authorurltext'=>'', // Text des Links, sonst wird die url angezeigt
    'maintainer'=>'', //Name desjenigen der den Spass pflegt
    'maintainerurl'=>'', // url ohne http://
    'maintainerurltext'=>'', // Text des Links, sonst wird die url angezeigt
    'copyright'=>'', //Name des Copyrightinhaber
    'copyrighturl'=>'', // url ohne http://, obiger Name wird verlinkt wenn dies gesetzt ist
    'externaltool'=>0, //true or false, z.B. für phpMyAdmin
    'externaltoolname'=>'', //name of the tool
    'externaltoolurl'=>'', // link to tool homepage
    'externaltoolversion'=>'', // as string
    'externaltoollicensetype'=>'',//GPL or BSD or whatever
    'externaltoollicenseurl'=>'', //link to the license
    'appdisabled'=>0 //set to 1 to disable
    
);