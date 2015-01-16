<?php
define('NO_SESS', 1); //no need for a session
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

header('Content-Type: text/javascript', true);
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT', true);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime(__FILE__)) . ' GMT', true);
header('Cache-Control: max-age=86400, must-revalidate', true);
header('Pragma: ', true);

echo we_template::we_getCodeMirror2Tags(false, we_base_request::_(we_base_request::BOOL, 'settings'));
