<?php exit;?>
These templates are included from the updateserver. They are either included
from the updateserver directly (->direct output), or included with output
buffering activated and returned to the updateclient which prints them. With
this architecture it is possible to keep the control on the updateserver and
allow to react very fast and independent from the client to any needed issues.

These templates can contain php code directly executed from the server AND also
php-code which is submitted to the client and executed there (with use of
function updateUtil::addPhpCodeToTemplate).

Within a template you can use all in the session stored data. Furthemore special
data needed for special screens are prepared and stored in
$updateServerTemplateData

The templates are grouped in folders according to their functions.