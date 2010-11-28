<?php
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_tags/" . "we_tag_tracker.inc.php");

function we_tag_pagelogger($attribs, $content){
	return we_tag_tracker($attribs, $content);
}?>
