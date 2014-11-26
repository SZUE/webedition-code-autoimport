
include_once('define.conf.php');

$toolTables = array();
<?php if(isset($TABLECONSTANT) && !empty($TABLECONSTANT)) {?>
$toolTables['tool_table_<?php echo $TOOLNAME;?>_1'] = <?php echo $TABLECONSTANT;?>;
<?php }?>
// additional table can be specified here
// $toolTables['tool_table_<?php echo $TOOLNAME;?>_2'] = '';
// $toolTables['tool_table_<?php echo $TOOLNAME;?>_3'] = '';