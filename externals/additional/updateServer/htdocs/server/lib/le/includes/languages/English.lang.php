<?php

$lang['license']['undefinedError'] = 'Undefined Error';
$lang['license']['missingModuleLicenses'] = 'You do not have all of the required licenses for the modules you have installed.<br><br>The license is missing for the following modules:<br><br>%s<br>Please purchase the required modules or contact webEdition support.<br /><br />The licenses for the following modules are missing:';
$lang['license']['noStratoIp'] = 'This licence is property of the STRATO AG and can only be used on a server of this company.';
$lang['license']['notEnoughLicencesForModules'] = 'All buyed licences for webEdition 6 Modules are already installed.';
$lang['license']['notEnoughVersions'] = 'All of your available licenses for version 6 have already been installed.';
$lang['license']['noVersion6'] = 'You do not yet have a license for webEdition Version 6.';
$lang['license']['noWpolskaIp'] = 'This licence is property of Wirtualna Polska and can only be used on a server of this company.';
$lang['license']['serialNotExist'] = 'The donated serial does not exist.';

$lang['update']['headline'] = 'Update';

$lang['notification']['headline'] = 'Message from Live Update Server';
$lang['notification']['lostSession'] = 'There was a problem with the session';
$lang['notification']['databaseFailure'] = 'Could not connect to database';
$lang['notification']['highload'] = 'Dear webEdition User,<br />Unfortunately, we are experiencing some bottlenecks with our server at the moment due to the huge interest in webEdition 6 Open Source. As a result, an installation with the online installer might not be possible at the moment.<br />We apologize for any inconvenience and hope you enjoy webEdition 6.';
$lang['notification']['highloadSourceforge'] = 'Dear webEdition User,<br />Unfortunately, we are experiencing some bottlenecks with our server at the moment due to the huge interest in webEdition 6 Open Source. As a result, an installation with the online installer might not be possible at the moment.<br />Please use the complete installation archives for the "manual installation" from our <a href="http://sourceforge.net/projects/webedition/" target="_blank">sourcforge.net project site</a>instead.<br />We apologize for any inconvenience and hope you enjoy webEdition 6.';
$lang['notification']['maintenance'] = 'Due to maintenance, the update functions of webEdition 6 are not available at the moment.';
$lang['notification']['maintenance_15'] = 'Due to maintenance work, the webEdition update and installation server is not available at the moment until approx 15.00. Please, excuse the inconvenience.';
$lang['notification']['updateNotPossibleUntilRelease'] = 'The update functions for webEdition 6 are disabled until 2008-1!-03.';
$lang['notification']['betaExpired'] = 'You are currently running a beta version of webEdition. The beta programme of version %s is completed. Please install a regurlar version of webEdition.';

$lang['notification']['importantAnnouncementIcon'] = '<img src="http://'.$_SERVER["HTTP_HOST"].'/server/lib/img/alert.gif" style="margin:0px 10px 10px 0px; float:right;"/>';
$lang['notification']['importantAnnouncement'] = $lang['notification']['importantAnnouncementIcon'].'<b>Important Information:</b><br />Due to maintenance, the update server will not be available for updates and installations from Friday, November 28th, 2008 until Monday, December 1st, 2008. We are sorry for the inconvenience.<br />The webEdition installation archive on <a href="http://sourceforge.net/projects/webedition/" target="_blank">Sourceforge project page</a> will be available to you during this time for installations.';

$lang['notification']['installerVersionFailed']['headline'] = "Online Installer version check";
$lang['notification']['installerVersionFailed']['content'] = "You are currently using an old version of our Online Installer, but you need at least version 2.7.0.0 or newer to install the selected software.<br /><br />You can download the most recent version of the webEdition Online Installer on the webEdition web site <a href=\"http://download.webedition.org/releases/\" target=\"_blank\">download.webedition.org/releases/</a>.";
?>