
function we_tag_<?php print $TOOLNAME;?>($attribs,$content)
{
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/tools/weToolLookup.class.php');
    if(weToolLookup::isActiveTag(__FILE__)){ //check if application is disabled
		return "Hello <?php print $TOOLNAME;?>!";
    }
			
}