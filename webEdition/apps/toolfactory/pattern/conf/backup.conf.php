
include_once('define.conf.php');

$toolTables = [];
<?php if(!empty($TABLECONSTANT)) {?>
$toolTables['tool_table_<?= $TOOLNAME;?>_1'] = <?= $TABLECONSTANT;?>;
<?php }?>
// additional table can be specified here
// $toolTables['tool_table_<?= $TOOLNAME;?>_2'] = '';
// $toolTables['tool_table_<?= $TOOLNAME;?>_3'] = '';