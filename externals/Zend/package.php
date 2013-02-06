<?php
/**
 * package.php
 * Create a Zend Framework par
 *
 * @author Cal Evans <cal@calevans.com>
 */

$zfLocation    = 'Zend-FW1';
$zfBasePointer = strpos($zfLocation,'Zend');
 
/*
 * Let the user know what is going on
 */
echo "Creating phar for Zend Framework located at ".$zfLocation."\n";
/*
 * Clean up from previous 
 */
if (file_exists('zf.phar')) {
    Phar::unlinkArchive('zf.phar');
}
$p = new Phar('zf.phar', 0, 'zf.phar');
$p->compressFiles(Phar::GZ);
$p->setSignatureAlgorithm (Phar::SHA1);
$files = array();
$files['stub.php']='stub.php';
 
$rd = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($zfLocation));
foreach($rd as $file) {
    if (strpos($file->getPath(),'.svn')===false &&
        $file->getFilename() != '..' &&
        $file->getFilename() != '.') {
        $files[substr($file->getPath().DIRECTORY_SEPARATOR.$file->getFilename(),$zfBasePointer)]=$file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();
    }
}

$p->startBuffering();
$p->buildFromIterator(new ArrayIterator($files));
$p->stopBuffering();

$p->setStub($p->createDefaultStub('stub.php'));
$p = null;

