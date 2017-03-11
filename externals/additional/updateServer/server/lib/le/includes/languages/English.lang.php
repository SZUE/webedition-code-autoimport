<?php
$lang['update']['headline'] = 'Update';

$lang['notification']['headline'] = 'Message from Live Update Server';
$lang['notification']['lostSession'] = 'There was a problem with the session';
$lang['notification']['databaseFailure'] = 'Could not connect to database';
$lang['notification']['maintenance'] = 'Due to maintenance, the update functions of webEdition are not available at the moment.';
$lang['notification']['updateNotPossibleUntilRelease'] = 'The update functions for webEdition are disabled until 2008-11-03.';
$lang['notification']['betaExpired'] = 'You are currently running a beta version of webEdition. The beta programme of version %s is completed. Please install a regurlar version of webEdition.';

$lang['notification']['importantAnnouncementIcon'] = '<img src="http://' . $_SERVER["HTTP_HOST"] . '/server/lib/img/alert.gif" style="margin:0px 10px 10px 0px; float:right;"/>';
$lang['notification']['importantAnnouncement'] = $lang['notification']['importantAnnouncementIcon'] . '<b>Important Information:</b><br />Due to maintenance, the update server will not be available for updates and installations from Friday, November 28th, 2008 until Monday, December 1st, 2008. We are sorry for the inconvenience.<br />The webEdition installation archive on <a href="http://sourceforge.net/projects/webedition/" target="_blank">Sourceforge project page</a> will be available to you during this time for installations.';

$lang['notification']['installerVersionFailed']['headline'] = "Online Installer version check";
$lang['notification']['installerVersionFailed']['content'] = "You are currently using an old version of our Online Installer, but you need at least version " . MIN_INSTALLER_V . " or newer to install the selected software.<br /><br />You can download the most recent version of the webEdition Online Installer on the webEdition web site <a href=\"http://download.webedition.org/releases/\" target=\"_blank\">download.webedition.org/releases/</a>.";
