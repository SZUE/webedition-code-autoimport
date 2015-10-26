<?php

if(!empty($TABLECONSTANT) && !empty($TABLENAME)) {?>
define("<?php echo $TABLECONSTANT;?>","<?php echo $TABLENAME;?>");

<?php }?>

define("<?php echo $ACTIVECONSTANT;?>",true);
