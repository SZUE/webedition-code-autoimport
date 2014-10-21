
function we_tag_<?php print $TOOLNAME;?>($attribs,$content){

	include_once (WE_APPS_PATH. . '<?php print $TOOLNAME;?>/conf/define.conf.php');
    if(<?php print $ACTIVECONSTANT;?>){ //check if application is disabled
		return "Hello <?php print $TOOLNAME;?>!";
    }

}