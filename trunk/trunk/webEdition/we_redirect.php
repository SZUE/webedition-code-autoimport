<?php

// +----------------------------------------------------------------------+
// | webEdition                                                           |
// +----------------------------------------------------------------------+
// | PHP version 4.1.0 or greater                                         |
// +----------------------------------------------------------------------+
// | Copyright (c) 2000 - 2007 living-e AG                                |
// +----------------------------------------------------------------------+
//


include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");

$row = getHash("SELECT Path,IsDynamic FROM ".FILE_TABLE." WHERE ID=" . $_REQUEST["id"],$DB_WE);
$port = (defined("HTTP_PORT")) ? (":".HTTP_PORT) : "";

srand ((double)microtime()*1000000);
$randval = rand();

$prot = getServerProtocol();
$preurl = (isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]) ? "$prot://".$_SERVER["HTTP_HOST"] : "";

$DB_WE->query("SELECT Published FROM ".FILE_TABLE." WHERE ID=" . $_REQUEST["id"]);
if($DB_WE->next_record()){
	if($DB_WE->f("Published")){
		header("Location: ".$preurl.$row["Path"]."?r=$randval");
		exit;
	}
}
header("Location: ".$preurl.WEBEDITION_DIR."notPublished.php");


?>