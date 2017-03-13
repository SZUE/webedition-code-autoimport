<?php
/**
 * $Id$
 */
/**
 * This template is shown, until webEdition version 4 is published
 */
$liveUpdateResponse['Type'] = 'template';
$liveUpdateResponse['Headline'] = $GLOBALS['lang']['update']['headline'];
$liveUpdateResponse['Content'] = '
<div class="messageDiv">
	' . sprintf($GLOBALS['lang']['notification']['betaExpired'], updateUtilInstaller::number2version($GLOBALS['clientRequestVars']['betaVersion'])) . '
</div>';

