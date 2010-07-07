<?php

//
// ---> Template
//

$lang["Template"]["headline"] = "webEdition Online Installer";
$lang["Template"]["title"] = "webEdition Online Installer";
$lang["Template"]["moreComponentsToCome"] = "Komponenten werden zusammengestellt";
$lang["Template"]["autocontinue"] = "Sie werden automatisch in %s Sekunden weitergeleitet.";


//
// ---> Applications
//

$lang["Application"]["webEdition5"]["name"] = "webEdition 5";
$lang["Application"]["webEdition5"]["description"] = "Professionelles Web Content Management System.";
$lang["Application"]["webEdition5"]["longdescription"] = "The webEdition web CMS (content management system) is a CMS based on PHP and MySQL. It is the perfect CMS for users, who wish to manage their website comfortably with a CMS. The webEdition CMS has a large function spectrum and can be customized easily.";
$lang["Application"]["webEdition5"]["link"] = "http://www.living-e.de/produkte/webEdition/index.php";

$lang["Application"]["webEdition"]["name"] = "webEdition 6";
$lang["Application"]["webEdition"]["description"] = "Professionelles Open Source Web Content Management System.";
$lang["Application"]["webEdition"]["longdescription"] = "Das webEdition Web CMS (Content-Management-System) ist ein OpenSource CMS das auf PHP und MySQL basiert. Es ist das perfekte CMS f�r Nutzer, die Ihre Website komfortabel selbst verwalten wollen. Das webEdition CMS hat ein gro�es Funktionsspektrum und kann einfach an individuelle Bed�rfnisse angepasst werden";
$lang["Application"]["webEdition"]["link"] = "http://www.webedition.org/";

$lang["Application"]["webEditionBeta"]["name"] = "webEdition einschlie�lich Pre-Release Versionen";
$lang["Application"]["webEditionBeta"]["description"] = "Professionelles Open Source Web Content Management System.";
$lang["Application"]["webEditionBeta"]["longdescription"] = "Pre-Release Versionen (nightly Builds, Alpha-, Beta und RC-Versionen) dienen als Testumgebung zum Auffinden von Fehlern bevor ein offizielles neues Release herausgebracht wird. <b>Sie sollten niemals f�r produktive Sites eingesetzt werden. Wir �bernehmen keinerlei Haftung f�r eventuell auftretende Fehler oder f�r Datenverluste.</b>";
$lang["Application"]["webEditionBeta"]["link"] = "http://www.webedition.org/";


$lang["Application"]["pageLogger"]["name"] = "pageLogger 1.6";
$lang["Application"]["pageLogger"]["description"] = "Open Source Trackingsystem f�r Ihre Homepage.";
$lang["Application"]["pageLogger"]["longdescription"] = "pageLogger speichert den Besucherverkehr Ihrer Website in Realzeit und verl�sst sich nicht auf Server-Log-Dateien. Auf Knopfdruck erhalten Sie alle relevanten Informationen �ber Ihre Besucher, klar und effizient dargestellt.";
$lang["Application"]["pageLogger"]["link"] = "http://www.pagelogger.com/de/";


//
// ---> Buttons
//

$lang["Buttons"]["next"] = "weiter";
$lang["Buttons"]["back"] = "zur�ck";
$lang["Buttons"]["print"] = "drucken";
$lang["Buttons"]["close"] = "schlie�en";


//
// ---> Wizards
//

$lang["Wizard"]["Welcome"]["title"] = "Willkommen";
$lang["Wizard"]["DownloadInstaller"]["title"] = "Installationspaket herunterladen";



//
// ---> Steps
//

// Welcome
$lang["Step"]["Welcome"]["title"] = "Sprache w�hlen";
$lang["Step"]["Welcome"]["headline"] = "webEdition Online Installer";
$lang["Step"]["Welcome"]["content"] = "Dieser Wizard wird Sie Schritt f�r Schritt durch die Installation unserer Softwareprojekte f�hren.";
$lang["Step"]["Welcome"]['choose_language'] = "Sprache �ndern";
$lang["Step"]["Welcome"]['ISO_language'] = "<br /><b>Wichtig:</b> Wir empfehlen, f�r neue Projekte UTF-8 zu verwenden. webEdition verf�gt noch �ber einige ISO-8859-1 (ISO Latin-1) kodierte �bersetzungen um die Kompatibilit�t mit alten Versionen zu wahren, aber alle neuen �bersetzungen werden UTF-8 kodiert. <br />F�r die zuk�nftige Version 7 wird keine Unterst�tzung f�r ISO-Sprachen mehr garantiert, sodass dann eine Umstellung der Site auf UTF-8 notwendig werden k�nnte.<br /><br />";
$lang["Step"]["Welcome"]['language_Deutsch_UTF-8'] = "Deutsch (UTF-8)";
$lang["Step"]["Welcome"]['language_English_UTF-8'] = "English (UTF-8)";
$lang["Step"]["Welcome"]['language_Deutsch'] = "Deutsch (ISO 8859-1)";
$lang["Step"]["Welcome"]['language_English'] = "English (ISO 8859-1)";


// HintAboutOnlineInstallation
$lang["Step"]["HintAboutOnlineInstallation"]["title"] = "Hinweise";
$lang["Step"]["HintAboutOnlineInstallation"]["headline"] = "Hinweise zur Online Installation";
$lang["Step"]["HintAboutOnlineInstallation"]["content"] = "W�hrend der Online-Installation werden Daten zu Ihrem Server gesammelt und an den webEdition Server �bertragen, um die f�r Sie relevanten Daten zusammen zu stellen.";

$lang["Step"]["HintAboutOnlineInstallation"]["labelAccept"] = "Ich bin damit einverstanden";
$lang["Step"]["HintAboutOnlineInstallation"]["chmod_hint"] = "Achtung:<br />Die Verzeichnisberechtigung Ihres Document Root wurde von %s auf 777 abge�ndert.";

// Check Online Installer Version
$lang["Step"]["VersionCheck"]["title"] = "Installer-Version";
$lang["Step"]["VersionCheck"]["headline"] = "�berpr�fe Installer-Version";
$lang["Step"]["VersionCheck"]["content"] = "In diesem Schritt wird �berpr�ft, ob eine neue Version des Online Installers verf�gbar ist. Wir empfehlen Ihnen, immer die aktuellste Installer Version zu verwenden.";

$lang["Step"]["VersionCheck"]["installerVersion"] = "";
$lang["Step"]["VersionCheck"]["installerVersionFailed"] = "";

// ChooseApplication
$lang["Step"]["ChooseApplication"]["title"] = "Applikation w�hlen";
$lang["Step"]["ChooseApplication"]["headline"] = "Zu installierende Applikation w�hlen";
$lang["Step"]["ChooseApplication"]["content"] = "Mit diesem Installer ist es m�glich alle webbasierenden Anwendungen des webEdition Projektes zu installieren.";

$lang["Step"]["ChooseApplication"]["select_application"] = "Bitte w�hlen Sie die gew�nschte Applikation";


// ProxyServer
$lang["Step"]["ProxyServer"]["title"] = "Proxy Server";
$lang["Step"]["ProxyServer"]["headline"] = "Proxy Server";
$lang["Step"]["ProxyServer"]["content"] = "Sollten Sie f�r den Zugang zum Internet einen Proxy-Server verwenden, k�nnen Sie diesen hier eintragen.";

$lang["Step"]["ProxyServer"]["labelUseProxy"] = "folgenden Proxy-Server verwenden";
$lang["Step"]["ProxyServer"]["host"] = "Hostname";
$lang["Step"]["ProxyServer"]["port"] = "Port";
$lang["Step"]["ProxyServer"]["username"] = "Benutzername";
$lang["Step"]["ProxyServer"]["password"] = "Passwort";

$lang["Step"]["ProxyServer"]["noProxyServer"] = "Bitte geben Sie die IP-Adresse bzw. den Hostnamen Ihres Proxy Servers an.";


// ConnectionCheck
$lang["Step"]["ConnectionCheck"]["title"] = "Verbindungstest";
$lang["Step"]["ConnectionCheck"]["headline"] = "Verbindung zum Server wird gestestet";
$lang["Step"]["ConnectionCheck"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef�hrt.";

$lang["Step"]["ConnectionCheck"]["connectionReady"] = "Die Verbindung zum Server konnte aufgebaut werden. Sie k�nnen mit der Installation fortfahren.";
$lang["Step"]["ConnectionCheck"]["connectionWithError"] = "Die Verbindung zum Server konnte aufgebaut werden. Aber es gibt einen Fehler auf dem Server";
$lang["Step"]["ConnectionCheck"]["noConnection"] = "Der Server ist momentan leider nicht erreichbar. Eine Online-Installation ist derzeit nicht m�glich.";

$lang["Step"]["ConnectionCheck"]["connectionInfo"] = "Verbindungsinformationen";
$lang["Step"]["ConnectionCheck"]["availableConnectionTypes"] = "Verf�gbare Verbindungstypen";
$lang["Step"]["ConnectionCheck"]["connectionType"] = "Verwendeter Verbindungstyp";
$lang["Step"]["ConnectionCheck"]["proxyHost"] = "Proxy-Adresse";
$lang["Step"]["ConnectionCheck"]["proxyPort"] = "Proxy-Port";
$lang["Step"]["ConnectionCheck"]["hostName"] = "Hostname";
$lang["Step"]["ConnectionCheck"]["addressResolution"] = "Adressaufl�sung";
$lang["Step"]["ConnectionCheck"]["updateServer"] = "Updateserver";
$lang["Step"]["ConnectionCheck"]["ipResolutionTest"] = "IP-Aufl&ouml;sungstest";
$lang["Step"]["ConnectionCheck"]["dnsResolutionTest"] = "DNS-Aufl&ouml;sungstest";
$lang["Step"]["ConnectionCheck"]["succeeded"] = "erfolgreich";
$lang["Step"]["ConnectionCheck"]["failed"] = "fehlgeschlagen";
$lang["Step"]["ConnectionCheck"]["ipAddresses"] = "IP-Adresse(n)";

// SessionAndCookieTest
$lang["Step"]["SessionAndCookieTest"]["title"] = "PHP-Version, Session &amp; Cookies";
$lang["Step"]["SessionAndCookieTest"]["headline"] = "PHP-Version, Session und Cookies Test";
$lang["Step"]["SessionAndCookieTest"]["content"] = "In diesem Schritt wird die verwendete PHP-Version �berpr�ft und getestet, ob eine Session auf dem Server gestartet und ein Cookie gesetzt werden kann.";

$lang["Step"]["SessionAndCookieTest"]["session"] = "Session";
$lang["Step"]["SessionAndCookieTest"]["cookie"] = "Cookie";
$lang["Step"]["SessionAndCookieTest"]["failureMessage"] = "Die Installation kann nicht fortgesetzt werden, da Ihr System nicht die ben�tigten Anforderungen erf�llt.";
$lang["Step"]["SessionAndCookieTest"]["cookieFailed"] = "Es konnte kein Cookie gesetzt werden";
$lang["Step"]["SessionAndCookieTest"]["sessionFailed"] = "Es konnte keine Session initialisiert werden";

$lang["Step"]["SessionAndCookieTest"]["php"] = "PHP-Version %s";
$lang["Step"]["SessionAndCookieTest"]["phpFailed"] = "Die verwendete PHP-Version <b>%s</b> ist zu alt. Es wird mindestens PHP in der Version 5.2.4 ben�tigt";

$lang["Step"]["SessionAndCookieTest"]["safe_mode"] = "PHP Safe Mode";
$lang["Step"]["SessionAndCookieTest"]["safe_mode_OK"] = "PHP Safe Mode nicht aktiviert";
$lang["Step"]["SessionAndCookieTest"]["safe_mode_warning"] = "PHP Safe Mode ist aktiviert.<br />Die Anwendungen laufen mit aktiviertem <a href=\"http://www.php.net/manual/de/features.safe-mode.php\" target=\"_blank\">PHP Safe Mode</a>, aber wir empfehlen dies nicht, da dies seit PHP Version 5.3 als DEPRECATED (veraltet) gilt und wir nicht garantieren k�nnen, dass alle Features der Anwendungen problemlos funktionieren werden.";

$lang["Step"]["SessionAndCookieTest"]["register_globals"] = "Register Globals";
$lang["Step"]["SessionAndCookieTest"]["register_globals_OK"] = "Register Globals nicht aktiviert";
$lang["Step"]["SessionAndCookieTest"]["register_globals_warning"] = "register_globals ist activiert!<br />Dies kann schwerwiegende Sicherheitsprobleme hervorrufen und gilt seit PHP Version 5.3 als DEPRECATED (veraltet), deshalb empfehlen wir, dieses Feature zu deaktivieren. Beachten Sie <a href=\"http://www.php.net/manual/de/security.globals.php\" target=\"_blank\">php.net/manual</a> f�r weitere Informationen";

$lang["Step"]["SessionAndCookieTest"]["short_open_tag"] = "Short Open Tag";
$lang["Step"]["SessionAndCookieTest"]["short_open_tag_OK"] = "Short Open Tag nicht aktiviert";
$lang["Step"]["SessionAndCookieTest"]["short_open_tag_warning"] = "short_open_tag is activiert!<br />Die Anwendungen laufen mit aktiviertem  <a href=\"http://www.php.net/manual/en/ini.core.php#ini.short-open-tag\" target=\"_blank\">short_open_tag</a>, wir empfehlen dies aber nicht, da es zu Problemen f�hren kann, wenn mit XML-Files gearbeitet wird.";


// DetermineFilesInstaller
$lang["Step"]["DetermineFilesInstaller"]["title"] = "Daten ermitteln";
$lang["Step"]["DetermineFilesInstaller"]["headline"] = "Ben�tigtes Installationspaket wird ermittelt";
$lang["Step"]["DetermineFilesInstaller"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef�hrt.";


// DownloadFilesInstaller
$lang["Step"]["DownloadFilesInstaller"]["title"] = "Daten herunterladen";
$lang["Step"]["DownloadFilesInstaller"]["headline"] = "Installationspaket herunterladen";
$lang["Step"]["DownloadFilesInstaller"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef�hrt.";


// PrepareFilesInstaller
$lang["Step"]["PrepareFilesInstaller"]["title"] = "Daten vorbereiten";
$lang["Step"]["PrepareFilesInstaller"]["headline"] = "Installationspaket vorbereiten";
$lang["Step"]["PrepareFilesInstaller"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef�hrt.";


// InstallFilesInstaller
$lang["Step"]["InstallFilesInstaller"]["title"] = "Daten installieren";
$lang["Step"]["InstallFilesInstaller"]["headline"] = "Installationspaket installieren";
$lang["Step"]["InstallFilesInstaller"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef�hrt.";


// ConfigureInstaller
$lang["Step"]["ConfigureInstaller"]["title"] = "Installationspaket konfigurieren";
$lang["Step"]["ConfigureInstaller"]["headline"] = "Installationspaket konfigurieren";
$lang["Step"]["ConfigureInstaller"]["content"] = "Das Installationspaket wird konfiguriert.";

// Error Messages

// Fehlertext, wenn Installer von Anfang an nicht schreiben kann:
$lang["errors"]["writeFile"] = "Keine Schreibrechte!<br /><br />Konnte %s nicht beschreiben.<br /><br />Um webEdition 
		installieren zu k�nnen, muss das Wurzelverzeichnis (DOCUMENT_ROOT) f�r den Webserver (Apache, IIS...) 
		zumindest w�hrend der Installation beschreibbar sein. Weitere Informationen entnehmen Sie der 
		Installationsanleitung, die im Installationspaket enthalten ist oder besuchen Sie 
		<a href=\"http://www.webedition.de/path/to/write/permission/help.html\" target=\"_blank\">www.webedition.de</a>.";

?>