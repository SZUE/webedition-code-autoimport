<?php

// +----------------------------------------------------------------------+
// | webEdition                                                           |
// +----------------------------------------------------------------------+
// | PHP version 4.1.0 or greater                                         |
// +----------------------------------------------------------------------+
// | Copyright (c) living-e AG                   |
// +----------------------------------------------------------------------+
//


function we_tag_ifDoubleOptIn($attribs, $content){
	return isset($GLOBALS["WE_DOUBLEOPTIN"]) && $GLOBALS["WE_DOUBLEOPTIN"];
}

?>