<?php
//
// ---> Template
//

$lang["Template"]["headline"] = "webEdition Online Installer";
$lang["Template"]["title"] = "webEdition Online Installer";
$lang["Template"]["moreComponentsToCome"] = "Preparing components";
$lang["Template"]["autocontinue"] = "You will be redirected in %s seconds.";


//
// ---> Applications
//

$lang["Application"]["webEdition5"]["name"] = "webEdition 5";
$lang["Application"]["webEdition5"]["description"] = "Professional web content management system.";
$lang["Application"]["webEdition5"]["longdescription"] = "The webEdition web CMS (content management system) is a CMS based on PHP and MySQL. It is the perfect CMS for users, who wish to manage their website comfortably with a CMS. The webEdition CMS has a large function spectrum and can be customized easily.";
$lang["Application"]["webEdition5"]["link"] = "http://www.living-e.de/produkte/webEdition/index.php";

$lang["Application"]["webEdition"]["name"] = "webEdition 6";
$lang["Application"]["webEdition"]["description"] = "Professional web content management system.";
$lang["Application"]["webEdition"]["longdescription"] = "The webEdition web CMS (content management system) is an OpenSource CMS based on PHP and MySQL. It is the perfect CMS for users, who wish to manage their website comfortably with a CMS. The webEdition CMS has a large function spectrum and can be customized easily.";
$lang["Application"]["webEdition"]["link"] = "http://www.webedition.org/";

$lang["Application"]["webEditionBeta"]["name"] = "webEdition incl. Alpha and Beta Versions";
$lang["Application"]["webEditionBeta"]["description"] = "Professional web content management system.";
$lang["Application"]["webEditionBeta"]["longdescription"] = "Alpha and Beta versions are used to find errors before a new official version is released. <b>They should never be used for productive sites. We take no responsibility for any damages or loss of data.</b>";
$lang["Application"]["webEditionBeta"]["link"] = "http://www.webedition.org/";


$lang["Application"]["pageLogger"]["name"] = "pageLogger 1.6";
$lang["Application"]["pageLogger"]["description"] = "Open source real time tracking for your homepage.";
$lang["Application"]["pageLogger"]["longdescription"] = "pageLogger records visitor traffic on your website in real time, and does not rely on server log files. At the push of a button you get all the details about recent visitors to your website - clearly and efficiently displayed.";
$lang["Application"]["pageLogger"]["link"] = "http://www.pagelogger.com/en/";


//
// ---> Buttons
//

$lang["Buttons"]["next"] = "next";
$lang["Buttons"]["back"] = "back";
$lang["Buttons"]["print"] = "print";
$lang["Buttons"]["close"] = "close";


//
// ---> Wizards
//

$lang["Wizard"]["Welcome"]["title"] = "Welcome";
$lang["Wizard"]["DownloadInstaller"]["title"] = "Download installer";



//
// ---> Steps
//

// Welcome
$lang["Step"]["Welcome"]["title"] = "Choose language";
$lang["Step"]["Welcome"]["headline"] = "webEdition Online Installer";
$lang["Step"]["Welcome"]["content"] = "This wizard will guide you step by step through the installation process of our software projects.";
$lang["Step"]["Welcome"]['choose_language'] = "Change language";
$lang["Step"]["Welcome"]['ISO_language'] = "<b>Important:</b> We strongly recommend using UTF-8 for new projects. webEdition still contains a couple of ISO-8859-1 (ISO Latin-1) encoded translations for backwards compatibility, but all new translations are and will be UTF-8 encoded.<br /> In addition, for the upcoming Version 7, we do do not guarantee full support for ISO languages, so you might need to convert your site to UTF-8. <br /><br />";
$lang["Step"]["Welcome"]['language_Deutsch_UTF-8'] = "Deutsch (UTF-8)";
$lang["Step"]["Welcome"]['language_English_UTF-8'] = "English (UTF-8)";
$lang["Step"]["Welcome"]['language_Deutsch'] = "Deutsch (ISO 8859-1)";
$lang["Step"]["Welcome"]['language_English'] = "English (ISO 8859-1)";


// HintAboutOnlineInstallation
$lang["Step"]["HintAboutOnlineInstallation"]["title"] = "Notes";
$lang["Step"]["HintAboutOnlineInstallation"]["headline"] = "Online Installation notes";
$lang["Step"]["HintAboutOnlineInstallation"]["content"] = "During the Online Installation procedure a connection to our server is established. Thereby some data are submitted to our server, these data will only be used to select all required files for the installation.";

$lang["Step"]["HintAboutOnlineInstallation"]["labelAccept"] = "Yes, I agree";
$lang["Step"]["HintAboutOnlineInstallation"]["chmod_hint"] = "Attention:<br />The directory rights of your document root was changed from %s to 777.";

// Check Online Installer Version
$lang["Step"]["VersionCheck"]["title"] = "Installer-Version";
$lang["Step"]["VersionCheck"]["headline"] = "Check Installer-Version";
$lang["Step"]["VersionCheck"]["content"] = "This step checks if there is a new Online Installer version available. It is recommended always to use the most recent Installer Version.";

$lang["Step"]["VersionCheck"]["installerVersion"] = "";
$lang["Step"]["VersionCheck"]["installerVersionFailed"] = "";


// ChooseApplication
$lang["Step"]["ChooseApplication"]["title"] = "Choose application";
$lang["Step"]["ChooseApplication"]["headline"] = "Choose the application";
$lang["Step"]["ChooseApplication"]["content"] = "With this installer it is possible to install all web based application of the the webEdition project.";

$lang["Step"]["ChooseApplication"]["select_application"] = "Please choose the application to be installed";


// ProxyServer
$lang["Step"]["ProxyServer"]["title"] = "Proxy server";
$lang["Step"]["ProxyServer"]["headline"] = "Proxy server";
$lang["Step"]["ProxyServer"]["content"] = "If you need a proxy for the internet connection you could enter your data here.";

$lang["Step"]["ProxyServer"]["labelUseProxy"] = "Use the following proxy";
$lang["Step"]["ProxyServer"]["host"] = "Host";
$lang["Step"]["ProxyServer"]["port"] = "Port";
$lang["Step"]["ProxyServer"]["username"] = "Username";
$lang["Step"]["ProxyServer"]["password"] = "Password";

$lang["Step"]["ProxyServer"]["noProxyServer"] = "Enter the Ip-Adress or Hostname of your proxy server.";


// ConnectionCheck
$lang["Step"]["ConnectionCheck"]["title"] = "Connection check";
$lang["Step"]["ConnectionCheck"]["headline"] = "Check the connection to the server";
$lang["Step"]["ConnectionCheck"]["content"] = "This step will be executed by the LiveUpdate-Server";

$lang["Step"]["ConnectionCheck"]["connectionReady"] = "A connection to the server could be established, you could now continue with the installation.";
$lang["Step"]["ConnectionCheck"]["connectionWithError"] = "A connection to our server could be established. But there was an error on the server.";
$lang["Step"]["ConnectionCheck"]["noConnection"] = "A connection to our server could not be established, an Online Installation is not possible at the moment.";

$lang["Step"]["ConnectionCheck"]["connectionInfo"] = "Connection informations";
$lang["Step"]["ConnectionCheck"]["availableConnectionTypes"] = "Available connection types";
$lang["Step"]["ConnectionCheck"]["connectionType"] = "Used connection type";
$lang["Step"]["ConnectionCheck"]["proxyHost"] = "Proxy host";
$lang["Step"]["ConnectionCheck"]["proxyPort"] = "Proxy port";
$lang["Step"]["ConnectionCheck"]["hostName"] = "Hostname";
$lang["Step"]["ConnectionCheck"]["addressResolution"] = "Address resolution";
$lang["Step"]["ConnectionCheck"]["updateServer"] = "Update server";
$lang["Step"]["ConnectionCheck"]["ipResolutionTest"] = "IP resolution test";
$lang["Step"]["ConnectionCheck"]["dnsResolutionTest"] = "DNS resolution test";
$lang["Step"]["ConnectionCheck"]["succeeded"] = "succeeded";
$lang["Step"]["ConnectionCheck"]["failed"] = "failed";
$lang["Step"]["ConnectionCheck"]["ipAddresses"] = "IP address(es)";

// SessionAndCookieTest
$lang["Step"]["SessionAndCookieTest"]["title"] = "PHP version, session &amp; cookies test";
$lang["Step"]["SessionAndCookieTest"]["headline"] = "PHP version, session and cookies test";
$lang["Step"]["SessionAndCookieTest"]["content"] = "Now, your PHP version will be checked and it will be checked if a session could be started on your server and a cookie could be set.";

$lang["Step"]["SessionAndCookieTest"]["session"] = "Session";
$lang["Step"]["SessionAndCookieTest"]["cookie"] = "Cookie";
$lang["Step"]["SessionAndCookieTest"]["failureMessage"] = "One or more of the system requirements are not satisfied.<br />The installation will can not continued.";
$lang["Step"]["SessionAndCookieTest"]["cookieFailed"] = "Cookie: A cookie can not be set.";
$lang["Step"]["SessionAndCookieTest"]["sessionFailed"] = "Session: A session can not be started.";

$lang["Step"]["SessionAndCookieTest"]["php"] = "PHP version %s";
$lang["Step"]["SessionAndCookieTest"]["phpFailed"] = "The used PHP version <b>%s</b> is to old. You need at least PHP version 5.2.4.";

$lang["Step"]["SessionAndCookieTest"]["safe_mode"] = "PHP Safe Mode";
$lang["Step"]["SessionAndCookieTest"]["safe_mode_OK"] = "PHP Safe Mode not active";
$lang["Step"]["SessionAndCookieTest"]["safe_mode_warning"] = "PHP Safe Mode is active.<br />The applications may run with activated <a href=\"http://www.php.net/manual/en/features.safe-mode.php\" target=\"_blank\">PHP Safe Mode</a>, yet we do not recommend it since it is DEPRECATED since PHP version 5.3. We also cannot guarantee that all features of the applications will work properly.";

$lang["Step"]["SessionAndCookieTest"]["register_globals"] = "Register Globals";
$lang["Step"]["SessionAndCookieTest"]["register_globals_OK"] = "Register Globals not active";
$lang["Step"]["SessionAndCookieTest"]["register_globals_warning"] = "register_globals is active!<br />This may cause <b>severe security problems</b>, is declared DEPRECATED since PHP version 5.3 and we strongly recommend to disable this \"feature\". See <a href=\"http://www.php.net/manual/en/security.globals.php\" target=\"_blank\">php.net/manual</a> for more information.";

$lang["Step"]["SessionAndCookieTest"]["short_open_tag"] = "Short Open Tag";
$lang["Step"]["SessionAndCookieTest"]["short_open_tag_OK"] = "Short Open Tag not active";
$lang["Step"]["SessionAndCookieTest"]["short_open_tag_warning"] = "short_open_tag is active!<br />The applications may run with activated <a href=\"http://de2.php.net/manual/en/ini.core.php#ini.short-open-tag\" target=\"_blank\">short_open_tag</a>, but yet we do not recommend it since it can lead to problems when working with .xml files.";




// DetermineFilesInstaller
$lang["Step"]["DetermineFilesInstaller"]["title"] = "Determine files";
$lang["Step"]["DetermineFilesInstaller"]["headline"] = "Determine required files";
$lang["Step"]["DetermineFilesInstaller"]["content"] = "This step will be executed by the LiveUpdate-Server";


// DownloadFilesInstaller
$lang["Step"]["DownloadFilesInstaller"]["title"] = "Download files";
$lang["Step"]["DownloadFilesInstaller"]["headline"] = "Download installer files";
$lang["Step"]["DownloadFilesInstaller"]["content"] = "This step will be executed by the LiveUpdate-Server";


// PrepareFilesInstaller
$lang["Step"]["PrepareFilesInstaller"]["title"] = "Prepare files";
$lang["Step"]["PrepareFilesInstaller"]["headline"] = "Prepare installer files";
$lang["Step"]["PrepareFilesInstaller"]["content"] = "This step will be executed by the LiveUpdate-Server";


// InstallFilesInstaller
$lang["Step"]["InstallFilesInstaller"]["title"] = "Copy files";
$lang["Step"]["InstallFilesInstaller"]["headline"] = "Copy installer files";
$lang["Step"]["InstallFilesInstaller"]["content"] = "This step will be executed by the LiveUpdate-Server";


// ConfigureInstaller
$lang["Step"]["ConfigureInstaller"]["title"] = "Configure installer";
$lang["Step"]["ConfigureInstaller"]["headline"] = "Configure installer";
$lang["Step"]["ConfigureInstaller"]["content"] = "Now the installation package will be configured.";

// Error Messages

// Error message for missing write permissions:
$lang["errors"]["writeFile"] = "No Write Permissions!<br /><br />Could not write to %s<br /><br />In order for 
		webEdition to be installed, the root directory (DOCUMENT_ROOT) must be writable for the web server (Apache, IIS, ..) 
		at least during installation. Please read the installation guide, which is included in the installation package or 
		visit <a href=\"http://www.webedition.de/path/to/write/permission/help.html\" target=\"_blank\">www.webedition.de</a>.";

?>