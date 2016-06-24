<?php

if(!empty($TABLECONSTANT) && !empty($TABLENAME)) {?>
define("<?= $TABLECONSTANT;?>","<?= $TABLENAME;?>");

<?php }?>

define("<?= $ACTIVECONSTANT;?>",true);
