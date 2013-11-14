<?php
	
	we_html_tools::protect();
	$variantName = $_REQUEST['we_cmd']['2'];

	we_shop_variants::useVariant($we_doc, $variantName);
        
	$content = $we_doc->getDocument();
    print $content;
?>