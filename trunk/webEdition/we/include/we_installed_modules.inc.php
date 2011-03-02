<?php
	$_we_installed_modules = array();

	if (in_array("customer",$_we_active_integrated_modules)) {$_we_installed_modules[] = "customer";}
	if (in_array("shop",$_we_active_integrated_modules)) {$_we_installed_modules[] = "shop";}
	if (in_array("object",$_we_active_integrated_modules)) {$_we_installed_modules[] = "object";}
	if (in_array("messaging",$_we_active_integrated_modules)) {$_we_installed_modules[] = "messaging";}
	if (in_array("workflow",$_we_active_integrated_modules)) {$_we_installed_modules[] = "workflow";}
	if (in_array("newsletter",$_we_active_integrated_modules)) {$_we_installed_modules[] = "newsletter";}

	$_pro_modules = array();

    $_pro_modules[] = "busers";
