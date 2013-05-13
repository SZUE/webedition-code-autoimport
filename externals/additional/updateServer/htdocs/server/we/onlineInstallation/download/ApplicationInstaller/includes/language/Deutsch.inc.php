<?php

//
// ---> Wizards
//

$lang["Wizard"]["Settings"]["title"] = "Systemeinstellungen";
$lang["Wizard"]["Features"]["title"] = "Auswahl Features";
$lang["Wizard"]["Summary"]["title"] = "Zusammenfassung";
$lang["Wizard"]["DownloadAndInstallSoftware"]["title"] = "Software installieren";
$lang["Wizard"]["DownloadAndInstallSnippets"]["title"] = "Zusatzdaten installieren";
$lang["Wizard"]["FinishInstallation"]["title"] = "Installation beenden";


//
// ---> Steps
//

// ImportantAnnouncement
$lang["Step"]["ImportantAnnouncement"]["title"] = "Wichtige Information";
$lang["Step"]["ImportantAnnouncement"]["headline"] = "Wichtige Information";
$lang["Step"]["ImportantAnnouncement"]["content"] = '<img src="./ApplicationInstaller/img/leLayout/alert.gif" style="margin:0px 10px 10px 0px; float:right;"/>Aufgrund umfangreicher Wartungsarbeiten wird der Update-Server von Freitag, 28. November 2008 bis voraussichtlich Montag, 1. Dezember 2008 nicht f&uuml;r Installationen und Aktualisierungen zur Verf&uuml;gung stehen. Wir bitten um Verst&auml;ndnis f&uuml;r diese Unannehmlichkeiten.<br /><br />Das webEdition Installationsarchiv steht Ihnen w&auml;hrend dieses Zeitraumes auf unserer <a href="http://sourceforge.net/projects/webedition/" target="_blank">Sourceforge Projektseite</a> weiterhin f&uuml;r Neuinstallationen zur Verf&uuml;gung.';

// LicenceAgreement
$lang["Step"]["LicenceAgreement"]["title"] = "Lizenzvereinbarung";
$lang["Step"]["LicenceAgreement"]["headline"] = "Lizenzvereinbarung";
$lang["Step"]["LicenceAgreement"]["content"] = "";

$lang["Step"]["LicenceAgreement"]["labelAccept"] = "Ja, ich akzeptiere die Lizenzvereinbarung";


// DocumentRoot
$lang["Step"]["DocumentRoot"]["title"] = "DOCUMENT_ROOT";
$lang["Step"]["DocumentRoot"]["headline"] = "DOCUMENT_ROOT";
$lang["Step"]["DocumentRoot"]["content"] = "Geben Sie hier den DOCUMENT_ROOT ein. Der DOCUMENT_ROOT ist der genaue Server-Pfad zu dem Verzeichnis, in welchem sich Ihre HTML-Seiten befinden.<br /><br />Der Server gibt als DOCUMENT_ROOT folgenden Pfad (MouseOver f&uuml;r den vollst&auml;ndigen Pfad) zur&uuml;ck: <br />%s<br /><br />Ist dies der richtige Pfad, dann ist Ihr Server korrekt konfiguriert, und Sie brauchen nichts einzutragen. Ist der Pfad falsch, dann geben Sie bitte  den richtigen DOCUMENT_ROOT ein!";

$lang["Step"]["DocumentRoot"]["DocumentRoot"] = "DOCUMENT_ROOT";
$lang["Step"]["DocumentRoot"]["requestNotValid"] = "Das eingegebene DOCUMENT_ROOT kann nicht gefunden werden. Bitte kontrollieren Sie Ihre Eingabe";
$lang["Step"]["DocumentRoot"]["autoDocRootNotValid"] = "Das DOCUMENT_ROOT kann auf dem Server nicht gefunden werden, bitte geben Sie das korrekte DOCUMENT_ROOT ein.";


// SystemRequirements
$lang["Step"]["SystemRequirements"]["title"] = "Systemvoraussetzungen";
$lang["Step"]["SystemRequirements"]["headline"] = "Systemvoraussetzungen";
$lang["Step"]["SystemRequirements"]["content"] = "Im folgenden werden die Systemvoraussetzungen f&uuml;r die Installation von webEdition gepr&uuml;ft.";

$lang["Step"]["SystemRequirements"]["failure"] = "Fehler";
$lang["Step"]["SystemRequirements"]["ok"] = "OK";
$lang["Step"]["SystemRequirements"]["php_version"] = "PHP Version";
$lang["Step"]["SystemRequirements"]["is_writeable"] = "Installationsverzeichnis beschreibbar";
$lang["Step"]["SystemRequirements"]["error"] = "Eine oder mehrere Systemvoraussetzungen sind nicht erf&uuml;llt! webEdition kann nicht installiert werden!<br />";


// SoftwareRequirements
$lang["Step"]["SoftwareRequirements"]["title"] = "Softwarevoraussetzungen";
$lang["Step"]["SoftwareRequirements"]["headline"] = "Softwarevoraussetzungen";
$lang["Step"]["SoftwareRequirements"]["content"] = "Im folgenden werden die Softwarevoraussetzungen f&uuml;r die Installation von webEdition gepr&uuml;ft.";

$lang["Step"]["SoftwareRequirements"]["failure"] = "Fehler";
$lang["Step"]["SoftwareRequirements"]["ok"] = "OK";
$lang["Step"]["SoftwareRequirements"]["php_version"] = "PHP Version";
$lang["Step"]["SystemRequirements"]["mysql"] = "MySQL Unterst&uuml;tzung";
$lang["Step"]["SoftwareRequirements"]["error"] = "Eine oder mehrere Softwarevoraussetzungen sind nicht erf&uuml;llt! webEdition kann nicht installiert werden!<br />";
$lang["Step"]["SoftwareRequirements"]["mbstring"] = "PHP Multibyte String Funktionen";
$lang["Step"]["SoftwareRequirements"]["mbstringNotAvailable"] = "Die PHP Multibyte String Funktionen sind auf diesem Server offensichtlich nicht verf&uuml;gbar, daher sind einige Funktionen bzgl. der Zeichensatzauswahl u.U. nur eingeschr&auml;nkt nutzbar.";
$lang["Step"]["SoftwareRequirements"]["gdlib"] = "PHP GDlib-Funktionen";
$lang["Step"]["SoftwareRequirements"]["gdlibNotAvailable"] = "Die PHP GDlib-Funktionen sind auf diesem Server offensichtlich nicht verf&uuml;gbar, daher sind einige Bildbearbeitungs- und Bildvorschaufunktionen nur eingeschr&auml;nkt nutzbar.";
$lang["Step"]["SoftwareRequirements"]["found"] = "gefunden";
$lang["Step"]["SoftwareRequirements"]["exif"] = "EXIF Support";
$lang["Step"]["SoftwareRequirements"]["exifNotAvailable"] = "Die exif PHP Extension ist auf diesem Server nicht verf&uuml;gbar, daher sind EXIF-Metadaten f&uuml;r Bilder nicht nutzbar.";
$lang["Step"]["SoftwareRequirements"]["pcre"] = "Version der PCRE PHP-Extension: ";
$lang["Step"]["SoftwareRequirements"]["pcreOLD"] = "Die PCRE-Version ist VERALTET. Dies kann, insbesondere in zuk&uuml;nftigen webEdtion Versionen, zu Problemen f&uuml;hren";
$lang["Step"]["SoftwareRequirements"]['sdk_db'] = 'SDK/Apps DB Unterst&uuml;tzung';
$lang["Step"]["SoftwareRequirements"]['sdk_dbWarnung'] = 'SDK DB-Operationen und WE-APPS mit Datenbanknutzung sind nicht verf&uuml;gbar, es fehlen die PHP Extensions PDO und PDO_mysql';
$lang["Step"]["SoftwareRequirements"]['phpext'] = 'Minimal notwendige PHP-Extensions';
$lang["Step"]["SoftwareRequirements"]['phpextWarning'] = 'Notwendige PHP-Extension nicht verf&uuml;gbar: ';
$lang["Step"]["SoftwareRequirements"]['reqNotDetec'] = 'Einige Softwarevoraussetzungen konnten nicht überpr&uuml;ft werden (Suhosin?). Bitte pr&uuml;fen Sie die Systemvoraussetzungen unter http://www.webedition.org/de/webedition-cms/systemvoraussetzungen.php ';
$lang["Step"]["SoftwareRequirements"]['softreq'] = "Weitere Softwarevoraussetzungen";


// InstallationDirectory
$lang["Step"]["InstallationDirectory"]["title"] = "Installationsverzeichnis";
$lang["Step"]["InstallationDirectory"]["headline"] = "Installationsverzeichnis pr&uuml;fen";
$lang["Step"]["InstallationDirectory"]["content"] = "webEdition scheint auf diesem Server noch nicht installiert zu sein. Sie k&ouml;nnen mit der Installation fortfahren.";

$lang["Step"]["InstallationDirectory"]["installationForbidden"] = "Einige webEdition Verzeichnisse sind bereits auf Ihrem Server installiert. Eine Konfiguration konnte allerdings nicht gefunden werden. Bevor Sie mit der Installation fortfahren k&ouml;nnen, m&uuml;ssen Sie den bestehenden webEdition Ordner umbenennen (bzw. l&ouml;schen).<br /><br /><strong>Achtung!</strong><br />Ihre web-Seite ist dann unter Umst&auml;nden nicht mehr erreichbar.";
$lang["Step"]["InstallationDirectory"]["alreadyInstalled"] = "webEdition ist anscheinend schon auf Ihrem Server installiert. Um mit der Installation fortfahren zu k&ouml;nnen, m&uuml;ssen Sie zun&auml;chst best&auml;tigen, dass es sich um Ihre Installation handelt. Geben Sie dazu bitte die im installierten webEdition gespeicherten Benutzerdaten f&uuml;r die Datenbank ein. Alternativ k&ouml;nnen Sie das webEdition Verzeichnis auf dem Server manuell umbenennen (bzw. l&ouml;schen).<br /><br /><strong>Achtung!</strong><br />Ihre web-Seite ist dann unter Umst&auml;nden nicht mehr erreichbar.";
$lang["Step"]["InstallationDirectory"]["textNotInstalled"] = "webEdition scheint auf diesem Server noch nicht installiert zu sein. Sie k&ouml;nnen mit der Installation fortfahren.";
$lang["Step"]["InstallationDirectory"]["installationVeryfied"] = "Die Installation wurde verifiziert, das alte webEdition Verzeichnis wird im weiteren Verlauf der Installation verschoben.";
$lang["Step"]["InstallationDirectory"]["dataNotValid"] = "Die eingegebenen Daten weichen von den in der we_conf.inc.php gefunden Daten ab.";
$lang["Step"]["InstallationDirectory"]["userNameDb"] = "Benutzer der Datenbank";
$lang["Step"]["InstallationDirectory"]["passDb"] = "Passwort des Benutzers";


// Database
$lang["Step"]["Database"]["title"] = "Datenbank";
$lang["Step"]["Database"]["headline"] = "Datenbank";
$lang["Step"]["Database"]["content"] = "Geben Sie bitte hier die Zugangsdaten des MySQL-Datenbank-Servers ein! Diese Daten erhalten Sie in der Regel von Ihrem Webspace-Provider.";

$lang["Step"]["Database"]['connecttype']	= "Verbindungsart";
$lang["Step"]["Database"]['connect'] = "Normal (connect)";
$lang["Step"]["Database"]['pconnect'] = "Persistent (pconnect)";
$lang["Step"]["Database"]['pconnect_na'] = "nicht verf&uuml;gbar";
$lang["Step"]["Database"]['host'] = "Server (Host)";
$lang["Step"]["Database"]['user'] = "Benutzername";
$lang["Step"]["Database"]['pass'] = "Passwort";
$lang["Step"]["Database"]['name'] = "Datenbankname";
$lang["Step"]["Database"]['prefix'] = "Tabellenpr&auml;fix (optional)";
$lang["Step"]["Database"]["connect_help"] = "Bitte geben Sie hier die Art der Verbindung zu Ihrer Datenbank ein.\\n\\nPersistente DB-Verbindungen verhalten sich sehr &auml;hnlich zu normalen DB-Verbindungen, weisen aber zwei wesentliche Unterschiede auf.\\n\\nErstens: vor dem Verbindungsaufbau wird zun&auml;chst versucht eine offene (persistente) Verbindung zum gleichen Host, mit dem gleichen Benutzernamen und Benutzerkennwort zu finden. Wenn das gelingt, wird diese Verbindung benutzt anstatt eine neue Verbindung aufzubauen.\\n\\nZweitens: die Verbindung zum MySQL Server wird beim Beenden des PHP-Skripts nicht geschlossen. Sie bleibt zur zuk&uuml;nftigen Verwendung bestehen.\\n\\nWenn Sie sich nicht sicher sind, was sie hier ausw&auml;hlen sollen, dann w&auml;hlen Sie \'Normal (connect)\'.";
$lang["Step"]["Database"]['host_help'] = "Bitte geben Sie hier den Servernamen (Host) bzw. die IP des Datenbankservers an.\\n Bsp.: db47.ihrprovider.de, 194.44.55.66 oder auch \':/var/run/mysqld/mysqld.sock\', falls Sie Unix-Sockets verwenden. \\nWenn der Datenbankserver auf dem gleichen Computer l&auml;uft wie der Webserver, dann ist in den meisten F&auml;llen \'localhost\' einzutragen.";
$lang["Step"]["Database"]['user_help'] = "Bitte geben Sie hier den Benutzernamen (Username) f&uuml;r Ihre Datenbank ein.";
$lang["Step"]["Database"]['pass_help'] = "Bitte geben Sie hier das Kennwort (Password) f&uuml;r Ihre Datenbank ein.";
$lang["Step"]["Database"]['name_help'] = "Bitte geben Sie hier den Namen Ihrer Datenbank ein. Wenn die Datenbank mit dem hier eingetragenen Namen nicht existiert, wird sie neu erstellt, sofern Sie die erforderlichen Rechte besitzen.";
$lang["Step"]["Database"]['prefix_help'] = "Bitte geben Sie hier ein Pr&auml;fix f&uuml;r die webEdition Tabellen ein. Sie k&ouml;nnen dadurch 2 webEdition mit nur einer Datenbank installieren.";
$lang["Step"]["Database"]['ErrorDBConnect'] = "Es konnte keine Verbindung zum Datenbankserver aufgebaut werden.<br />Bitte pr&uuml;fen Sie Ihre Eingaben.";
$lang["Step"]["Database"]['ErrorDBHost'] = "Bitte tragen Sie den Servernamen (Host) des Datenbankservers ein!";
$lang["Step"]["Database"]['ErrorDBUser'] = "Bitte tragen Sie den Benutzernamen des Datenbankservers ein!";
$lang["Step"]["Database"]['ErrorDBName'] = "Bitte tragen Sie den Namen der gew&uuml;nschten Datenbank ein!";
$lang["Step"]["Database"]["ErrorCreateDb"] = "Die Datenbank %s konnte nicht angelegt werden. Bitte pr&uuml;fen Sie die Rechte Ihres MySQL-Users Zugriffsrechte.<br />MySQL-Fehler: %s (%s)";


// DatabasePermissions
$lang["Step"]["DatabasePermissions"]["title"] = "Datenbankberechtigung";
$lang["Step"]["DatabasePermissions"]["headline"] = "Datenbankberechtigungen pr&uuml;fen";
$lang["Step"]["DatabasePermissions"]["content"] = "Sie verf&uuml;gen &uuml;ber alle ben&ouml;tigten Rechte, um webEdition zu installieren.<br /><ul><li>CREATE TABLE</li><li>ALTER TABLE</li><li>DROP TAPBE</li></ul>";

$lang["Step"]["DatabasePermissions"]["dbserverwarning"] = "<br/>Der verwendete DB-Server meldet die Version %s, webEdition ben&ouml;tigt jedoch mindestens die MySQL-Server Version 5.0. webEdition mag mit der genutzten Version funktionieren, dies kann jedoch nicht f&uuml;r neue webEdition Versionen (z.B. nach Updates) garantiert werden. Sp&auml;testens ab webEdition Version 7 wird MySQL Version 5 ben&ouml;tigt.<br/><span style=\"color:red;font-weight:bold\">Außerdem: die auf dem Server installierte MySQL Version ist veraltet. Für diese Version gibt es keine Updates mehr, dies kann die Sicherheit des gesamten Systems beeintr&auml;chtigen.</span><br/>";

$lang["Step"]["DatabasePermissions"]["AccessDenied"] = "Die von Ihnen angegebene Datenbank '%s' existiert nicht. Der von Ihnen gew&auml;hlte Benutzer verf&uuml;gt nicht &uuml;ber die ben&ouml;tigten Rechte die Datenbank anzulegen, bzw. zu benutzen. Bitte gehen sie zur&uuml;ck und &uuml;berpr&uuml;fen Sie die eingegebenen Benutzerdaten, bzw. die Rechte der Datenbank.";
$lang["Step"]["DatabasePermissions"]["errorNotCreateTable"] = "<strong>Fehlendes Recht: Tabelle erstellen</strong><br />Der verwendetet Datenbankbenutzer verf&uuml;gt nicht &uuml;ber die ben&ouml;tigten Rechte, eine Tabelle anzulegen. Bitte kontrollieren Sie Ihren Datenbankbenutzer, bzw. setzen Sie dessen Rechte hoch. Dabei kann Ihnen in der Regel Ihr Webspace-Provider helfen.";
$lang["Step"]["DatabasePermissions"]["errorNotAlterTable"] = "<strong>Fehlendes Recht: Tabelle &auml;ndern</strong><br />Der verwendetet Datenbankbenutzer verf&uuml;gt nicht &uuml;ber die ben&ouml;tigten Rechte, eine Tabelle zu &auml;ndern. Bitte kontrollieren Sie Ihren Datenbankbenutzer, bzw. setzen Sie dessen Rechte hoch. Dabei kann Ihnen in der Regel Ihr Webspace-Provider helfen.";
$lang["Step"]["DatabasePermissions"]["errorNotDropTable"] = "<strong>Fehlendes Recht: Tabelle l&ouml;schen</strong><br />Der verwendetet Datenbankbenutzer verf&uuml;gt nicht &uuml;ber die ben&ouml;tigten Rechte, eine Tabelle zu l&ouml;schen. Bitte kontrollieren Sie Ihren Datenbankbenutzer, bzw. setzen Sie dessen Rechte hoch. Dabei kann Ihnen in der Regel Ihr Webspace-Provider helfen.";

$lang["Step"]["DatabasePermissions"]["overWriteExistingDb"] = "webEdition scheint schon in Ihrer Datenbank installiert zu sein. Um mehrere webEdition auf einem Datenbankserver nutzen zu k&ouml;nnen, k&ouml;nnen Sie mit einem Tabellenpr&auml;fix arbeiten. Wollen Sie ein Tabellenpr&auml;fix verwenden, navigieren Sie bitte zur&uuml;ck und geben ein Tabellenpr&auml;fix ein.<br /><br />Wenn Sie fortfahren werden die alten Tabellen (und darin enthaltene Daten) gel&ouml;scht. Wollen Sie mit der Installation fortfahren?";
$lang["Step"]["DatabasePermissions"]["overWriteExistingDbCheckBox"] = "Forfahren und existierende Tabellen &uuml;berschreiben";

$lang["Step"]["DatabasePermissions"]["Collation"] = "Sortierung";
$lang["Step"]["DatabasePermissions"]["defaultCollation"] = "Standardeinstellung des MySQL-Servers";


// Login
$lang["Step"]["Login"]["title"] = "Login";
$lang["Step"]["Login"]["headline"] = "Login";
$lang["Step"]["Login"]["content"] = "Geben Sie hier ihre webEdition Zugangsdaten ein. Mit diesen Daten k&ouml;nnen Sie sich in Ihr installiertes webEdition einloggen.";

$lang["Step"]["Login"]['user'] = "Benutzername";
$lang["Step"]["Login"]['pass'] = "Passwort";
$lang["Step"]["Login"]['confirm'] = "Passwortbest&auml;tigung";
$lang["Step"]["Login"]['user_help'] = "Hier tragen Sie den Benutzernamen ein, mit dem Sie sich im installierten webEdition anmelden k&ouml;nnen.";
$lang["Step"]["Login"]['pass_help'] = "Hier tragen Sie das Passwort ein, mit dem Sie sich im installierten webEdition anmelden k&ouml;nnen.";
$lang["Step"]["Login"]['confirm_help'] = "Um Schreibfehler zu vermeiden, wiederholen Sie hier bitte Ihre Passworteingabe.";
$lang["Step"]["Login"]["UsernameFailure"] = "Bitte tragen Sie Ihren gew&uuml;nschten Benutzernamen ein.";
$lang["Step"]["Login"]["UsernameToShort"] = "Der Benutzername muss mindestens 2 Zeichen lang sein!";
$lang["Step"]["Login"]["UsernameInvalid"] = "Der Benutzername darf nur Buchstaben (a-z und A-Z), Zahlen (0-9) sowie die Zeichen '.', '-' und '_' enthalten!";
$lang["Step"]["Login"]["PasswordFailure"] = "Bitte tragen Sie Ihr gew&uuml;nschtes Passwort ein.";
$lang["Step"]["Login"]["PasswordToShort"] = "Das Kennwort muss mindestens 4 Zeichen lang sein!";
$lang["Step"]["Login"]["PasswordInvalid"] = "Das Kennwort darf keine Leerzeichen enthalten!";
$lang["Step"]["Login"]["ConfirmFailure"] = "Die eingetragenen Passw&ouml;rter stimmen nicht &uuml;berein.";


// ChooseLanguage
$lang["Step"]["ChooseLanguage"]["title"] = "Sprache";
$lang["Step"]["ChooseLanguage"]["headline"] = "Sprachen w&auml;hlen";
$lang["Step"]["ChooseLanguage"]["content"] = "Bitte w&auml;hlen Sie hier Ihre gew&uuml;nschten Sprachen aus. Die Systemsprache wird als Standardsprache in webEdition verwendet. Wir empfehlen dringend eine UTF-8 Version zu verwenden. Die weiteren Sprachen k&ouml;nnen auch sp&auml;ter &uuml;ber den Online-Updater einfach installiert werden.<br/><b>Wichtiger Hinweis:</b> Als <font color=\"red\">[beta]</font> markierte Sprachen k&ouml;nnen unvollst&auml;ndig und unter Umst&auml;nden fehlerhaft sein. Sie k&ouml;nnen sich jedoch gern an das Projektteam wenden um diese &Uuml;bersetzungen zu vervollst&auml;ndigen. ";

$lang["Step"]["ChooseLanguage"]["language"] = "Sprachen";
$lang["Step"]["ChooseLanguage"]["system"] = "System";
$lang["Step"]["ChooseLanguage"]["additional"] = "weitere";


// ChooseVersion
$lang["Step"]["ChooseVersion"]["title"] = "Version";
$lang["Step"]["ChooseVersion"]["headline"] = "Version w&auml;hlen";
$lang["Step"]["ChooseVersion"]["content"] = "Bitte w&auml;hlen Sie, welche webEdition Version Sie installieren m&ouml;chten.";

$lang["Step"]["ChooseVersion"]["cannotInstallWebEdition"] = "webEdition kann leider nicht installiert werden, da nicht f&uuml;r alle gew&auml;hlten Sprachen eine &Uuml;bersetzung vorliegt.";
$lang["Step"]["ChooseVersion"]["missingTranslations"] = "Die aktuellste webEdition Version (%s) kann leider nicht installiert werden, da nicht f&uuml;r alle gew&auml;hlten Sprachen eine &Uuml;bersetzung vorliegt. Wir empfehlen Ihnen daher die Version %s zu installieren.";
$lang["Step"]["ChooseVersion"]["highestVersionRecommended"] = "Wir empfehlen Ihnen, immer die aktuellste Version zu installieren.";
$lang["Step"]["ChooseVersion"]["version"] = "Version";
$lang["Step"]["ChooseVersion"]["noNotLiveVersion"] = "Zur Zeit sind keine Alpha- oder Betaversionen verf&uuml;gbar. Installieren Sie die aktuellste offizielle Version.";

$lang["Step"]["ChooseVersion"]['nightly-build'] = 'nightly Build';
$lang["Step"]["ChooseVersion"]['alpha'] = 'Alpha';
$lang["Step"]["ChooseVersion"]['beta'] = 'Beta';
$lang["Step"]["ChooseVersion"]['rc'] = 'RC';
$lang["Step"]["ChooseVersion"]['release'] = 'offizieller Release';



// SerialNumber
$lang["Step"]["SerialNumber"]["title"] = "Seriennummer";
$lang["Step"]["SerialNumber"]["headline"] = "Seriennummer";
$lang["Step"]["SerialNumber"]["content"] = "Im folgenden Schritt k&ouml;nnen Sie webEdition gleich registrieren und damit den vollen Funktionsumfang nutzen. Bitte tragen Sie hierzu Ihre Seriennummmer ein.<br />Sollten Sie noch keine Seriennummer besitzen, k&ouml;nnen Sie diese unter <a href=\"http://www.living-e.de/\" target=\"_blank\">http://www.living-e.de/</a> erwerben.";

$lang["Step"]["SerialNumber"]["labelRegister"] = "Ja, ich m&ouml;chte webEdition jetzt registrieren.";
$lang["Step"]["SerialNumber"]["serial"] = "Seriennummer";
$lang["Step"]["SerialNumber"]["serialNotValid"] = "Die von Ihnen eingetragene Seriennummer ist leider nicht g&uuml;ltig.";


// ChooseModules
$lang["Step"]["ChooseModules"]["title"] = "Module";
$lang["Step"]["ChooseModules"]["headline"] = "Module w&auml;hlen";
$lang["Step"]["ChooseModules"]["content"] = "Bitte w&auml;hlen Sie die zu installierenden Module";

$lang["Step"]["ChooseModules"]["modules"] = "Module";
$lang["Step"]["ChooseModules"]["pro_modules"] = "Pro Module";
$lang["Step"]["ChooseModules"]["depending_modules"] = "abh&auml;ngige Module";
$lang["Step"]["ChooseModules"]["no_serial"] = "F&uuml;r die Demoversion k&ouml;nnen keine Module installiert werden.<br />Eine Installation von Modulen ist nur m&ouml;glich, wenn Sie Module erworben und die entsprechende Seriennummer eingegeben haben.";
$lang["Step"]["ChooseModules"]["no_modules"] = "F&uuml;r die eingegebene Seriennummer sind leider keine Module verf&uuml;gbar.";


// ChooseSnippets
$lang["Step"]["ChooseSnippets"]["title"] = "Zusatzdaten";
$lang["Step"]["ChooseSnippets"]["headline"] = "Zusatzdaten w&auml;hlen";
$lang["Step"]["ChooseSnippets"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// Summary
$lang["Step"]["Summary"]["title"] = "Zusammenfassung";
$lang["Step"]["Summary"]["headline"] = "Zusammenfassung";
$lang["Step"]["Summary"]["content"] = "Hier steht derzeit Blindtext.";

$lang["Step"]["Summary"]['webEditionBase'] = "webEdition";
$lang["Step"]["Summary"]['webEditionURL'] = "URL";
$lang["Step"]["Summary"]['webEditionUsername'] = "Benutzername";
$lang["Step"]["Summary"]['webEditionPassword'] = "Passwort";
$lang["Step"]["Summary"]['webEditionSerial'] = "Seriennummer";
$lang["Step"]["Summary"]['webEditionVersion'] = "Version";
$lang["Step"]["Summary"]['webEditionLanguage'] = "webEdition Sprachen";
$lang["Step"]["Summary"]['webEditionSystemLanguage'] = "Systemsprache";
$lang["Step"]["Summary"]['webEditionAdditionalLanguages'] = "weitere Sprachen";
$lang["Step"]["Summary"]['Module'] = "Modul";
$lang["Step"]["Summary"]['databaseConnection'] = "Datenbankverbindung";
$lang["Step"]["Summary"]['databaseHost'] = "Host";
$lang["Step"]["Summary"]['databaseUsername'] = "Benutzername";
$lang["Step"]["Summary"]['databasePassword'] = "Passwort";
$lang["Step"]["Summary"]['databaseName'] = "Datenbank";
$lang["Step"]["Summary"]['databaseTablePrefix'] = "Tabellenpr&auml;fix";
$lang["Step"]["Summary"]['databaseCharset'] = "Charset";
$lang["Step"]["Summary"]['databaseCollation'] = "Sortierung";
$lang["Step"]["Summary"]['databaseDefault'] = "Standard Einstellung";
$lang["Step"]["Summary"]['databaseConnectionType'] = "Verbindungsart";
$lang["Step"]["Summary"]['proxyServer'] = "ProxyServer";
$lang["Step"]["Summary"]['proxyHost'] = "Host";
$lang["Step"]["Summary"]['proxyPort'] = "Port";
$lang["Step"]["Summary"]['proxyUsername'] = "Benutzername";
$lang["Step"]["Summary"]['proxyPassword'] = "Passwort";
$lang["Step"]["Summary"]['snippets'] = "Zusatzdaten";
$lang["Step"]["Summary"]['yes'] = "ja";
$lang["Step"]["Summary"]['no'] = "nein";
$lang["Step"]["Summary"]['showPasswords'] = "Passw&ouml;rter anzeigen";
$lang["Step"]["Summary"]['hidePasswords'] = "Passw&ouml;rter verbergen";
$lang["Step"]["Summary"]['webEditionCommunity'] = "webEdition community";
$lang["Step"]["Summary"]['communityEmail'] = "E-Mail Adresse";
$lang["Step"]["Summary"]['communityPassword'] = "Passwort";
$lang["Step"]["Summary"]["communityWebsite"] = "Webseite (URL)";

// PrepareApplicationInstallation
$lang["Step"]["PrepareApplicationInstallation"]["title"] = "Installation vorbereiten";
$lang["Step"]["PrepareApplicationInstallation"]["headline"] = "Installation vorbereiten";
$lang["Step"]["PrepareApplicationInstallation"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// DetermineApplicationFiles
$lang["Step"]["DetermineApplicationFiles"]["title"] = "Daten ermitteln";
$lang["Step"]["DetermineApplicationFiles"]["headline"] = "ben&ouml;tigte Daten ermitteln";
$lang["Step"]["DetermineApplicationFiles"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// DownloadApplicationFiles
$lang["Step"]["DownloadApplicationFiles"]["title"] = "Daten herunterladen";
$lang["Step"]["DownloadApplicationFiles"]["headline"] = "Daten werden heruntergeladen";
$lang["Step"]["DownloadApplicationFiles"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// UpdateApplicationDatabase
$lang["Step"]["UpdateApplicationDatabase"]["title"] = "Datenbank einrichten";
$lang["Step"]["UpdateApplicationDatabase"]["headline"] = "Datenbank wird eingerichtet";
$lang["Step"]["UpdateApplicationDatabase"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// PrepareApplicationFiles
$lang["Step"]["PrepareApplicationFiles"]["title"] = "Daten vorbereiten";
$lang["Step"]["PrepareApplicationFiles"]["headline"] = "Daten werden vorbereitet";
$lang["Step"]["PrepareApplicationFiles"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// CopyApplicationFiles
$lang["Step"]["CopyApplicationFiles"]["title"] = "Daten installieren";
$lang["Step"]["CopyApplicationFiles"]["headline"] = "Daten werden installiert";
$lang["Step"]["CopyApplicationFiles"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// WriteApplicationConfiguration
$lang["Step"]["WriteApplicationConfiguration"]["title"] = "webEdition konfigurieren";
$lang["Step"]["WriteApplicationConfiguration"]["headline"] = "webEdition wird konfiguriert";
$lang["Step"]["WriteApplicationConfiguration"]["content"] = "Dieser Schritt wird vom LiveUpdate-Server ausgef&uuml;hrt.";


// FinishApplicationInstallation
$lang["Step"]["FinishApplicationInstallation"]["title"] = "Installation abschliessen";
$lang["Step"]["FinishApplicationInstallation"]["headline"] = "Installation wird abgeschlossen";
$lang["Step"]["FinishApplicationInstallation"]["content"] = "Die Installation von webEdition wird abgeschlossen.";


// Community
$lang["Step"]["Community"]["title"] = "webEdition community";
$lang["Step"]["Community"]["headline"] = "join the community!";
$lang["Step"]["Community"]["content"] = "Nutzen Sie die Vorteile der webEdition community und werden Sie noch heute Mitglied! Die Mitgliedschaft umfasst neben einem Zugang zum Web-Forum auch ...";
$lang["Step"]["Community"]["privacy"] = "Die Registrierung zur webEdition community ist freiwillig. Die an uns &uuml;bermittelten Daten werden streng vertraulich behandelt und nicht an Dritte weitergegeben, sie werden ausschlie&szlig;lich zur internen Verwaltung des community-Zugangs gespeichert.";
$lang["Step"]["Community"]["choice"]["notRegisteredYet"] = "Ich bin noch kein Mitglied";
$lang["Step"]["Community"]["choice"]["alreadyRegistered"] = "Ich bin bereits Mitglied:";
$lang["Step"]["Community"]["choice"]["skip"] = "&Uuml;berspringen";
$lang["Step"]["Community"]["input"]["email"] = "E-Mail";
$lang["Step"]["Community"]["input"]["password"] = "Passwort";
$lang["Step"]["Community"]["button"]["enterData"] = "Daten eingeben";
$lang["Step"]["Community"]["button"]["privacyStatement"] = "Datenschutzbestimmungen";
$lang["Step"]["Community"]["button"]["hideForm"] = "Formular ausblenden";
$lang["Step"]["Community"]["help"]["email"] = "Die E-Mail Adresse wird f&uuml;r den Login ben&ouml;tigt und kann nur f&uuml;r jeweils einen Benutzer verwendet werden.";
$lang["Step"]["Community"]["help"]["password"] = "Das Passwort muss aus Sichehreitsgr&uuml;nden mindestens 8 Zeichen lang sein.";
$lang["Step"]["Community"]["error"]["email"] = "Fehlerhafte oder leere E-Mail Adresse";
$lang["Step"]["Community"]["error"]["password"] = "Fehlerhaftes oder zu kurzes Passwort: das Passwort muss mindestens 8 Zeichen lang sein!";
$lang["Step"]["Community"]["error"]["noSuchUser"] = "Der angegebene Benutzer existiert nicht oder das eingetragene Passwort ist falsch.";
$lang["Step"]["Community"]["message"]["reallySkip"] = "Warum willsch net?";
$lang["Step"]["Community"]["message"]["reallySkipVerify"] = "Weil halt.";

$lang["Step"]["CommunityRegistration"]["title"] = "join the community";
$lang["Step"]["CommunityRegistration"]["headline"] = "join the community!";
$lang["Step"]["CommunityRegistration"]["content"] = "Willkommen in der webEdition community ... blah blah blubb";
$lang["Step"]["CommunityRegistration"]["message"]["reallySkip"] = "Warum willsch net?";
$lang["Step"]["CommunityRegistration"]["message"]["reallySkipVerify"] = "Weil halt.";
$lang["Step"]["CommunityRegistration"]["privacy"] = "* Die Registrierung zur webEdition community ist freiwillig. Die an uns &uuml;bermittelten Daten werden streng vertraulich behandelt und nicht an Dritte weitergegeben, sie werden ausschlie&szlig;lich zur internen Verwaltung des community-Zugangs gespeichert.";
$lang["Step"]["CommunityRegistration"]["message"]["welcome"] = "Willkommen in der supertollen webEdition community!";
$lang["Step"]["CommunityRegistration"]["help"]["optional"] = "Optionale Angabe: Diese Angabe ist freiwillig und wird nicht zwingend f&uuml;r eine Mitgliedschaft in der webEdition community ben&ouml;tigt.";
$lang["Step"]["CommunityRegistration"]["help"]["email"] = "Die E-Mail Adresse wird f&uuml;r den Login ben&ouml;tigt und kann nur f&uuml;r jeweils einen Benutzer verwendet werden.";
$lang["Step"]["CommunityRegistration"]["help"]["website"] = "Hier k&ouml;nnen Sie die URL Ihrer mit webEdition verwalteten Webseite angeben.";
$lang["Step"]["CommunityRegistration"]["help"]["password"] = "Das Passwort muss aus Sichehreitsgr&uuml;nden mindestens 8 Zeichen lang sein.";
$lang["Step"]["CommunityRegistration"]["help"]["passwordVerification"] = "Das Passwort muss aus Sichehreitsgr&uuml;nden zwei mal eingegeben werden, um Tippfehlern vorzubeugen.";
$lang["Step"]["CommunityRegistration"]["error"]["email"] = "Fehlerhafte oder leere E-Mail Adresse";
$lang["Step"]["CommunityRegistration"]["error"]["password"] = "Fehlerhaftes oder zu kurzes Passwort: das Passwort muss mindestens 8 Zeichen lang sein!";
$lang["Step"]["CommunityRegistration"]["error"]["passwordVerification"] = "Die beiden eingegebenen Passw&ouml;rter sind nicht identisch!";
$lang["Step"]["CommunityRegistration"]["error"]["noSuchUser"] = "Der angegebene Benutzer existiert nicht oder das eingetragene Passwort ist falsch.";
$lang["Step"]["CommunityRegistration"]["error"]["userExists"] = "Es existiert bereits ein Konto mit der angegebenen E-Mail Adresse. Pro E-Mail Adresse ist nur ein webEdition community Konto m&ouml;glich.";
$lang["Step"]["CommunityRegistration"]["error"]["userData"] = "Die folgenden Felder sind leer, m&uuml;ssen jedoch ausgef&uuml;llt werden:<li>Vorname</li><li>Name</li>";
$lang["Step"]["CommunityRegistration"]["email"] = "E-Mail";
$lang["Step"]["CommunityRegistration"]["salutation_m"] = "Herr";
$lang["Step"]["CommunityRegistration"]["salutation_f"] = "Frau";
$lang["Step"]["CommunityRegistration"]["salutation_c"] = "Firma";
$lang["Step"]["CommunityRegistration"]["salutation"] = "Anrede";
$lang["Step"]["CommunityRegistration"]["password"] = "Passwort";
$lang["Step"]["CommunityRegistration"]["passwordVerification"] = "Passwort best&auml;tigen";
$lang["Step"]["CommunityRegistration"]["prename"] = "Vorname";
$lang["Step"]["CommunityRegistration"]["surname"] = "Nachname";
$lang["Step"]["CommunityRegistration"]["company"] = "Firmenname";
$lang["Step"]["CommunityRegistration"]["website"] = "Webseite (URL)";
$lang["Step"]["CommunityRegistration"]["language"] = "Sprache";
$lang["Step"]["CommunityRegistration"]["country"] = "Land";


// InstallationFinished
$lang["Step"]["InstallationFinished"]["title"] = "Installation beendet";
$lang["Step"]["InstallationFinished"]["headline"] = "webEdition wurde installiert";
$lang["Step"]["InstallationFinished"]["content"] = "webEdition wurde auf Ihrem Server installiert. Sie k&ouml;nnen sich mit Ihren gew&auml;hlten Zugangsdaten an webEdition anmelden.";

$lang["Step"]["InstallationFinished"]["login_webEdition"] = "webEdition &ouml;ffnen";
$lang["Step"]["InstallationFinished"]["additional_software"] = "Bei Interesse k&ouml;nnen Sie auch gerne unsere anderen Produkte testen.<br />Diese k&ouml;nnen Sie bequem mit diesem Installer installieren.";
$lang["Step"]["InstallationFinished"]["installMore"] = "Ja, ich m&ouml;chte auch andere Produkte testen";
$lang["Step"]["InstallationFinished"]["choose_software"] = "Bitte w&auml;hlen Sie die gew&uuml;nschte Applikation";


// CleanUp
$lang["Step"]["CleanUp"]["title"] = "Installationsdateien l&ouml;schen";
$lang["Step"]["CleanUp"]["headline"] = "Die Installationsdateien wurden gel&ouml;scht";
$lang["Step"]["CleanUp"]["content"] = "Aus Sicherheitsgr&uuml;nden wurden alle f&uuml;r die Installation ben&ouml;tigten Dateien gel&ouml;scht.";

$lang["Step"]["CleanUp"]["delete_failed"] = "Aus Sicherheitsgr&uuml;nden empfehlen wir Ihnen die Installer Dateien von Ihrem Server zu l&ouml;schen.";
$lang["Step"]["CleanUp"]["openWebEdition"] = "webEdition &ouml;ffnen";


?>