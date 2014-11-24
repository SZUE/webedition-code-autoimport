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
$lang["Application"]["webEdition"]["longdescription"] = "Das webEdition Web CMS (Content-Management-System) ist ein OpenSource CMS das auf PHP und MySQL basiert. Es ist das perfekte CMS f&uuml;r Nutzer, die Ihre Website komfortabel selbst verwalten wollen. Das webEdition CMS hat ein gro&szlig;es Funktionsspektrum und kann einfach an individuelle Bed&uuml;rfnisse angepasst werden";
$lang["Application"]["webEdition"]["link"] = "http://www.webedition.org/";

$lang["Application"]["webEditionBeta"]["name"] = "webEdition einschlie&szlig;lich Pre-Release Versionen";
$lang["Application"]["webEditionBeta"]["description"] = "Professionelles Open Source Web Content Management System.";
$lang["Application"]["webEditionBeta"]["longdescription"] = "Pre-Release Versionen (nightly Builds, Alpha-, Beta und RC-Versionen) dienen als Testumgebung zum Auffinden von Fehlern bevor ein offizielles neues Release herausgebracht wird. <b>Sie sollten niemals f&uuml;r produktive Sites eingesetzt werden. Wir &uuml;bernehmen keinerlei Haftung f&uuml;r eventuell auftretende Fehler oder f&uuml;r Datenverluste.</b>";
$lang["Application"]["webEditionBeta"]["link"] = "http://www.webedition.org/";


$lang["Application"]["pageLogger"]["name"] = "pageLogger 1.6";
$lang["Application"]["pageLogger"]["description"] = "Open Source Trackingsystem f&uuml;r Ihre Homepage.";
$lang["Application"]["pageLogger"]["longdescription"] = "pageLogger speichert den Besucherverkehr Ihrer Website in Realzeit und verl&auml;sst sich nicht auf Server-Log-Dateien. Auf Knopfdruck erhalten Sie alle relevanten Informationen &uuml;ber Ihre Besucher, klar und effizient dargestellt.";
$lang["Application"]["pageLogger"]["link"] = "http://www.pagelogger.com/de/";


//
// ---> Buttons
//

$lang["Buttons"]["next"] = "weiter";
$lang["Buttons"]["back"] = "zur&uuml;ck";
$lang["Buttons"]["print"] = "drucken";
$lang["Buttons"]["close"] = "schlie&szlig;en";


//
// ---> Wizards
//

$lang["Wizard"]["Welcome"]["title"] = "Willkommen";
$lang["Wizard"]["DownloadInstaller"]["title"] = "Installationspaket herunterladen";



//
// ---> Steps
//

// Welcome
$lang["Step"]["Welcome"]["title"] = "Sprache w&auml;hlen";
$lang["Step"]["Welcome"]["headline"] = "webEdition Online Installer";
$lang["Step"]["Welcome"]["content"] = "Dieser Wizard wird Sie Schritt f&uuml;r Schritt durch die Installation unserer Softwareprojekte f&uuml;hren.";
$lang["Step"]["Welcome"]['choose_language'] = "Sprache &auml;ndern";
$lang["Step"]["Welcome"]['ISO_language'] = "<br /><b>Wichtig:</b> Wir empfehlen, f&uuml;r neue Projekte UTF-8 zu verwenden. webEdition verf&uuml;gt noch &uuml;ber einige ISO-8859-1 (ISO Latin-1) kodierte &Uuml;bersetzungen um die Kompatibilit&auml;t mit alten Versionen zu wahren, aber alle neuen &Uuml;bersetzungen werden UTF-8 kodiert. <br />F&uuml;r die zuk&uuml;nftige Version 7 wird keine Unterst&uuml;tzung f&uuml;r ISO-Sprachen mehr garantiert, sodass dann eine Umstellung der Site auf UTF-8 notwendig werden k&ouml;nnte.<br /><br />";
$lang["Step"]["Welcome"]['language_Deutsch_UTF-8'] = "Deutsch (UTF-8)";
$lang["Step"]["Welcome"]['language_English_UTF-8'] = "English (UTF-8)";
$lang["Step"]["Welcome"]['language_Deutsch'] = "Deutsch (ISO 8859-1)";
$lang["Step"]["Welcome"]['language_English'] = "English (ISO 8859-1)";


// HintAboutOnlineInstallation
$lang["Step"]["HintAboutOnlineInstallation"]["title"] = "Hinweise";
$lang["Step"]["HintAboutOnlineInstallation"]["headline"] = "Hinweise zur Online Installation";
$lang["Step"]["HintAboutOnlineInstallation"]["content"] = "W&auml;hrend der Online-Installation werden technische Daten zu Ihrem Server gesammelt und an den webEdition Server &uuml;bertragen, um die f&uuml;r Sie relevanten Daten zusammen zu stellen. Anonymisierte Daten zu PHP-Version und installierten PHP-Extensions sowie zur verwendeten Webserver-Version werden dabei f&uuml;r statistische Zwecke gespeichert. Sollten Sie damit nicht einverstanden sein, so steht Ihnen der Tarball-Setup zur Verf&uuml;gung, den Sie unter download.webedition.org/releases/ herunterladen k&ouml;nnen.";

$lang["Step"]["HintAboutOnlineInstallation"]["labelAccept"] = "Ich bin damit einverstanden";
$lang["Step"]["HintAboutOnlineInstallation"]["chmod_hint"] = "Achtung:<br />Die Verzeichnisberechtigung Ihres Document Root wurde von %s auf 777 abge&auml;ndert.";

// Check Online Installer Version
$lang["Step"]["VersionCheck"]["title"] = "Installer-Version";
$lang["Step"]["VersionCheck"]["headline"] = "&Uuml;berpr&uuml;fe Installer-Version";
$lang["Step"]["VersionCheck"]["content"] = "In diesem Schritt wird &uuml;berpr&uuml;ft, ob eine neue Version des Online Installers verf&uuml;gbar ist. Wir empfehlen Ihnen, immer die aktuellste Installer Version zu verwenden.";

$lang["Step"]["VersionCheck"]["installerVersion"] = "";
$lang["Step"]["VersionCheck"]["installerVersionFailed"] = "";

// ChooseApplication
$lang["Step"]["ChooseApplication"]["title"] = "Applikation w&auml;hlen";
$lang["Step"]["ChooseApplication"]["headline"] = "Zu installierende Applikation w&auml;hlen";
$lang["Step"]["ChooseApplication"]["content"] = "Mit diesem Installer ist es m&ouml;glich alle webbasierenden Anwendungen des webEdition Projektes zu installieren.";

$lang["Step"]["ChooseApplication"]["select_application"] = "Bitte w&auml;hlen Sie die gew&uuml;nschte Applikation";


// ProxyServer
$lang["Step"]["ProxyServer"]["title"] = "Proxy Server";
$lang["Step"]["ProxyServer"]["headline"] = "Proxy Server";
$lang["Step"]["ProxyServer"]["content"] = "Sollten Sie f&uuml;r den Zugang zum Internet einen Proxy-Server verwenden, k&ouml;nnen Sie diesen hier eintragen.";

$lang["Step"]["ProxyServer"]["labelUseProxy"] = "folgenden Proxy-Server verwenden";
$lang["Step"]["ProxyServer"]["host"] = "Hostname";
$lang["Step"]["ProxyServer"]["port"] = "Port";
$lang["Step"]["ProxyServer"]["username"] = "Benutzername";
$lang["Step"]["ProxyServer"]["password"] = "Passwort";

$lang["Step"]["ProxyServer"]["noProxyServer"] = "Bitte geben Sie die IP-Adresse bzw. den Hostnamen Ihres Proxy Servers an.";


// ConnectionCheck
$lang["Step"]["ConnectionCheck"]["title"] = "Verbindungstest";
$lang["Step"]["ConnectionCheck"]["headline"] = "Verbindung zum Server wird gestestet";
$lang["Step"]["ConnectionCheck"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";

$lang["Step"]["ConnectionCheck"]["connectionReady"] = "Die Verbindung zum Server konnte aufgebaut werden. Sie k&ouml;nnen mit der Installation fortfahren.";
$lang["Step"]["ConnectionCheck"]["connectionWithError"] = "Die Verbindung zum Server konnte aufgebaut werden. Aber es gibt einen Fehler auf dem Server";
$lang["Step"]["ConnectionCheck"]["noConnection"] = "Der Server ist momentan leider nicht erreichbar. Eine Online-Installation ist derzeit nicht m&ouml;glich.";

$lang["Step"]["ConnectionCheck"]["connectionInfo"] = "Verbindungsinformationen";
$lang["Step"]["ConnectionCheck"]["availableConnectionTypes"] = "Verf&uuml;gbare Verbindungstypen";
$lang["Step"]["ConnectionCheck"]["connectionType"] = "Verwendeter Verbindungstyp";
$lang["Step"]["ConnectionCheck"]["proxyHost"] = "Proxy-Adresse";
$lang["Step"]["ConnectionCheck"]["proxyPort"] = "Proxy-Port";
$lang["Step"]["ConnectionCheck"]["hostName"] = "Hostname";
$lang["Step"]["ConnectionCheck"]["addressResolution"] = "Adressaufl&ouml;sung";
$lang["Step"]["ConnectionCheck"]["updateServer"] = "Updateserver";
$lang["Step"]["ConnectionCheck"]["ipResolutionTest"] = "IP-Aufl&ouml;sungstest";
$lang["Step"]["ConnectionCheck"]["dnsResolutionTest"] = "DNS-Aufl&ouml;sungstest";
$lang["Step"]["ConnectionCheck"]["succeeded"] = "erfolgreich";
$lang["Step"]["ConnectionCheck"]["failed"] = "fehlgeschlagen";
$lang["Step"]["ConnectionCheck"]["ipAddresses"] = "IP-Adresse(n)";

// SessionAndCookieTest
$lang["Step"]["SessionAndCookieTest"]["title"] = "PHP-Version, Session &amp; Cookies";
$lang["Step"]["SessionAndCookieTest"]["headline"] = "PHP-Version, Session und Cookies Test";
$lang["Step"]["SessionAndCookieTest"]["content"] = "In diesem Schritt wird die verwendete PHP-Version &uuml;berpr&uuml;ft und getestet, ob eine Session auf dem Server gestartet und ein Cookie gesetzt werden kann.";

$lang["Step"]["SessionAndCookieTest"]["session"] = "Session";
$lang["Step"]["SessionAndCookieTest"]["cookie"] = "Cookie";
$lang["Step"]["SessionAndCookieTest"]["failureMessage"] = "Die Installation kann nicht fortgesetzt werden, da Ihr System nicht die ben&ouml;tigten Anforderungen erf&uuml;llt.";
$lang["Step"]["SessionAndCookieTest"]["cookieFailed"] = "Es konnte kein Cookie gesetzt werden";
$lang["Step"]["SessionAndCookieTest"]["sessionFailed"] = "Es konnte keine Session initialisiert werden";

$lang["Step"]["SessionAndCookieTest"]["php"] = "PHP-Version %s";
$lang["Step"]["SessionAndCookieTest"]["phpWarning"] = "F&uuml;r die Installation von webEdition-Versionen neuer als 6.3.9.0 wird PHP mindestens in der Version 5.3.7 ben&ouml;tigt";
$lang["Step"]["SessionAndCookieTest"]["phpFailed"] = "Die verwendete PHP-Version <b>%s</b> ist zu alt. Es wird mindestens PHP in der Version 5.2.4 ben&ouml;tigt";

$lang["Step"]["SessionAndCookieTest"]["max_input_vars"] = "PHP max_input_vars";
$lang["Step"]["SessionAndCookieTest"]["max_input_vars_ok"] = "PHP max_input_vars >= 2000";
$lang["Step"]["SessionAndCookieTest"]["max_input_vars_warning"] = "Die PHP-Variable max_input_vars ist auf einen Wert < 2000 gesetzt. Empfohlen wird ein Wert >= 2000.";
$lang["Step"]["SessionAndCookieTest"]["max_input_vars_failed"] = "Die PHP-Variable max_input_vars<br />darf nicht < 500 sein.<br />Empfohlen wird ein Wert >= 2000.";

$lang["Step"]["SessionAndCookieTest"]["safe_mode"] = "PHP Safe Mode";
$lang["Step"]["SessionAndCookieTest"]["safe_mode_OK"] = "Safe Mode nicht aktiviert";
$lang["Step"]["SessionAndCookieTest"]["safe_mode_warning"] = "PHP Safe Mode ist aktiviert.<br />Die Anwendungen laufen mit aktiviertem <a href=\"http://www.php.net/manual/de/features.safe-mode.php\" target=\"_blank\">PHP Safe Mode</a>, aber wir empfehlen dies nicht, da dies seit PHP Version 5.3 als DEPRECATED (veraltet) gilt und wir nicht garantieren k&ouml;nnen, dass alle Features der Anwendungen problemlos funktionieren werden.";

$lang["Step"]["SessionAndCookieTest"]["register_globals"] = "Register Globals";
$lang["Step"]["SessionAndCookieTest"]["register_globals_OK"] = "Register Globals nicht aktiviert";
$lang["Step"]["SessionAndCookieTest"]["register_globals_warning"] = "register_globals ist aKtiviert!<br />Dies kann schwerwiegende Sicherheitsprobleme hervorrufen und gilt seit PHP Version 5.3 als DEPRECATED (veraltet), deshalb empfehlen wir, dieses Feature zu deaktivieren. Beachten Sie <a href=\"http://www.php.net/manual/de/security.globals.php\" target=\"_blank\">php.net/manual</a> f&uuml;r weitere Informationen";

$lang["Step"]["SessionAndCookieTest"]["short_open_tag"] = "Short Open Tag";
$lang["Step"]["SessionAndCookieTest"]["short_open_tag_OK"] = "Short Open Tag nicht aktiviert";
$lang["Step"]["SessionAndCookieTest"]["short_open_tag_warning"] = "short_open_tag is aKtiviert!<br />Die Anwendungen laufen mit aktiviertem  <a href=\"http://www.php.net/manual/en/ini.core.php#ini.short-open-tag\" target=\"_blank\">short_open_tag</a>, wir empfehlen dies aber nicht, da es zu Problemen f&uuml;hren kann, wenn mit XML-Files gearbeitet wird.";

$lang["Step"]["SessionAndCookieTest"]["suhosin"] = "Suhosin Erweiterung zu PHP";
$lang["Step"]["SessionAndCookieTest"]["suhosin_OK"] = "Suhosin Erweiterung nicht aktiviert";
$lang["Step"]["SessionAndCookieTest"]["suhosin_warning"] = "Suhosin is aktiviert!<br />Die Anwendungen laufen <b>eventuell</b> mit aktiviertem  <a href=\"http://www.hardened-php.net/\" target=\"_blank\">Suhosin</a>, wir empfehlen dies aber nicht, da Suhosin wegen der Vielzahl der Konfigurationsm&ouml;glichkeiten zu nur schwer eingrenzbaren Problemen f&uuml;hren kann. <br />So kann es sein, dass, obwohl der OnlineInstaller selbst nicht problemlos arbeitet, die Anwendung selbst aber problemlos l&auml;uft. <br/>Wir empfehlen dann eine Installation des Tarballs, siehe <a href=\"http://download.webedition.org/releases\" target=\"_blank\">WebEdition Tarballs</a>.";


// DetermineFilesInstaller
$lang["Step"]["DetermineFilesInstaller"]["title"] = "Daten ermitteln";
$lang["Step"]["DetermineFilesInstaller"]["headline"] = "Ben&ouml;tigtes Installationspaket wird ermittelt";
$lang["Step"]["DetermineFilesInstaller"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// DownloadFilesInstaller
$lang["Step"]["DownloadFilesInstaller"]["title"] = "Daten herunterladen";
$lang["Step"]["DownloadFilesInstaller"]["headline"] = "Installationspaket herunterladen";
$lang["Step"]["DownloadFilesInstaller"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// PrepareFilesInstaller
$lang["Step"]["PrepareFilesInstaller"]["title"] = "Daten vorbereiten";
$lang["Step"]["PrepareFilesInstaller"]["headline"] = "Installationspaket vorbereiten";
$lang["Step"]["PrepareFilesInstaller"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// InstallFilesInstaller
$lang["Step"]["InstallFilesInstaller"]["title"] = "Daten installieren";
$lang["Step"]["InstallFilesInstaller"]["headline"] = "Installationspaket installieren";
$lang["Step"]["InstallFilesInstaller"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// ConfigureInstaller
$lang["Step"]["ConfigureInstaller"]["title"] = "Installationspaket konfigurieren";
$lang["Step"]["ConfigureInstaller"]["headline"] = "Installationspaket konfigurieren";
$lang["Step"]["ConfigureInstaller"]["content"] = "Das Installationspaket wird konfiguriert.";

// Error Messages

// Fehlertext, wenn Installer von Anfang an nicht schreiben kann:
$lang["errors"]["writeFile"] = "Keine Schreibrechte!<br /><br />Konnte %s nicht beschreiben.<br /><br />Um webEdition 
		installieren zu k&ouml;nnen, muss das Wurzelverzeichnis (DOCUMENT_ROOT) f&uuml;r den Webserver (Apache, IIS...) 
		zumindest w&auml;hrend der Installation beschreibbar sein.";

?>