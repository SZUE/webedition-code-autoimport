<?php
/*
Attempt to update
*/
// only execute on liveUpdate:
//aber meiner Meinung nach nicht notwendig, patches werden nicht ausgeführt
if(!is_readable("../../we/include/conf/we_conf.inc.php")) {
        //return true;
}
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");


function up6390_updateConf(){
        $filename= $_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/conf/we_conf.inc.php';
        
        if(($conf = file_get_contents($filename))){
            $conf = str_replace(array('include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."db_mysql.inc.php");', '?>'), '', $conf);

            return file_put_contents($filename, $conf);
        }
}

up6390_updateConf();
//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/say_6390.txt", 'patch gelaufen 6390');
return true;
