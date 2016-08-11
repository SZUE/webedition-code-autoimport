
function we_tag_<?php echo $TOOLNAME;?>($attribs,$content){

	include_once (WE_APPS_PATH. . '<?php echo $TOOLNAME;?>/conf/define.conf.php');
    if(<?php echo $ACTIVECONSTANT;?>){ //check if application is disabled
		return "Hello <?php echo $TOOLNAME;?>!";
    }

}