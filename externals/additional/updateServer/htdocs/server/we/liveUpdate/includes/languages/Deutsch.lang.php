<?php
$lang['license']['undefinedError'] = 'Undefinierter Fehler';

$lang['upgrade']['headline'] = 'Upgrade zu webEdition 6';
$lang['upgrade']['upgradePossibleText'] = 'W&auml;hlen Sie aus der Liste aus, auf welche webEdition Version Sie updaten m&ouml;chten';
$lang['upgrade']['upgradeToVersion'] = 'Upgrade auf Version';
$lang['upgrade']['confirmUpgradeWarning'] = 'Sie sind gerade dabei, ein Upgrade auf webEdition 6 durchzuf&uuml;hren. <b>In einem ersten Schritt ist dabei nur ein Upgrade auf Version 6.0.0.6 m&ouml;glich.</b> Dabei werden alle webEdition Programmdateien ersetzt und durch die neuen Dateien ersetzt, dieser Vorgang kann unter Umst&auml;nden sehr lange dauern. Bitte beachten Sie die folgenden Informationen:<br /><br /><b>Wichtige Informationen:</b><ul><li>webEdition 6 ben&ouml;tigt <u>PHP in der Version 5.2</u> oder neuer. Die aktuell verwendete Version k&ouml;nnen Sie &uuml;ber das Infofenster "Systeminformationen" (im Men&uuml; "Hilfe") ermitteln.</li><li>Nach dem Update muss webEdition neu gestartet werden.</li><li>Anschließend muss ein Rebuild der Seite durchgef&uuml;hrt werden, da diverse &Auml;nderungen an den we:tags durchgef&uuml;hrt wurden.</li><li><b>F&uuml;hren Sie abschließend ein Update auf die aktuellste webEdition Version durch.</b></li><li>Wir empfehlen, dies Schritt f&uuml;r Schritt durchzuf&uuml;hren: 6.0.0.6 -&gt; 6.1.0.2, 6.1.0.2 -&gt; 6.2.X (latest), jeweils mit Rebuilds zwischen den Updates</li></ul>';
$lang['upgrade']['confirmUpgradeWarningTitle'] = 'Zum Fortfahren bitte best&auml;tigen:';
$lang['upgrade']['confirmUpgradeWarningCheckbox'] = 'Hiermit best&auml;tige ich, dass ich die oben stehenden Hinweise gelesen habe.';
$lang['upgrade']['confirmUpdateWarning'] = 'Sie sind gerade dabei, ein Update von webEdition 6 durchzuf&uuml;hren. Bitte beachten Sie die folgenden Informationen:<br /><br /><b>Wichtige Informationen:</b><ul><li>Nach einem Update sollte webEdition neu gestartet werden.</li><li>Anschlie&szlig;end sollten Sie einen Rebuild &uuml;ber Dokumente und Vorlagen durchf&uuml;hren.</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6007] = '<b>webEdition 6.0.0.7:</b><ul><li><b>Ab dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer ben&ouml;tigt.</b><br/>Die aktuell verwendete PHP-Version k&ouml;nnen Sie &uuml;ber das Infofenster "Systeminformationen" (im Men&uuml; "Hilfe") ermitteln.</li><li>F&uuml;hren Sie nach dem Rebuild der Dokumente und Vorlagen noch folgende Rebuilds durch:<ul><li>Index-Tabelle</li><li>Objekte</li></ul></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6008] = '<b>webEdition 6.0.0.8:</b><ul><li><b>In dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer ben&ouml;tigt.</b><br/>Die aktuell verwendete PHP-Version k&ouml;nnen Sie &uuml;ber das Infofenster "Systeminformationen" (im Men&uuml; "Hilfe") ermitteln.</li><li>F&uuml;hren Sie nach dem Rebuild der Dokumente und Vorlagen noch folgende Rebuilds durch:<ul><li>Navigation</li></ul></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6100] = '<b>webEdition 6.1.0.0:</b><ul><li>Dieses Update ben&ouml;tigt tempor&auml;r <b>ca. 62 MB freien Webspace (Quota!)</b> da praktisch alle Dateien ausgetauscht werden</li><li><b>In dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer ben&ouml;tigt.</b><br/>Die aktuell verwendete PHP-Version k&ouml;nnen Sie &uuml;ber das Infofenster "Systeminformationen" (im Men&uuml; "Hilfe") ermitteln.</li><li>F&uuml;hren Sie nach dem Rebuild der Dokumente und Vorlagen noch folgende Rebuilds durch:<ul><li>Navigation</li><li>Objekte</li><li>Vorlagen</li></ul></li><li>Die PHP-Klassen smtp.class.php, we_mailer_class.inc.php, weNewsletterMailer.php werden in Zukunft nicht mehr unterst&uuml;tzt und als DEPRECATED (veraltet) erkl&auml;rt. Werden diese Klassen durch direkte PHP-Programmierung in den Vorlagen (die we:tags sind nicht betroffen) angesprochen, so sind sie in zuk&uuml;nftigen Projekten durch die Klasse we_util_Mailer (oder direkt Zend_Mail) zu ersetzen.<br/><b>In vorhandenen Installationen werden diese Klassen nicht gel&ouml;scht und stehen weiter zur Verf&uuml;gung.</b></li><li>Aufgrund des YUI Updates sind <strong>vorhandene WE-Apps unter Version 6.1.0 nicht lauff&auml;hig</strong> und m&uuml;ssen vor einem Update angepasst werden. Eine Dokumentation der notwendigen Umstellungsarbeiten finden Sie unter <a href="http://documentation.webedition.org/wiki/de/webedition/developer-information/software-development-kit-sdk/changes-from-sdk6000-to-sdk6100/start" target="_blank">Umstellung von SDK-Version 6.0.0.0 auf SDK-Version 6.1.0</a></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6101] = '<b>webEdition 6.1.0.1:</b><ul><li><b>In dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer ben&ouml;tigt.</b></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6102] = '<b>webEdition 6.1.0.2:</b><ul><li>In dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer ben&ouml;tigt.</li><li><b>Das fehlerhafte Verhalten von &lt;we:ifRegisteredUser cfilter="true" /&gt; bei gesetztem Kundenfilter und Einstellung "Kein Filter benutzen (alle Besucher haben Zugriff)" wurde korrigiert.</b> Wird exakt diese Einstellung in Dokumenten verwendet, so erhalten jetzt tats&auml;chlich alle Besucher Zugriff auf die betroffenen Dokumente.<b> Dies sollte <u>vor</u> und nach dem Update umgehend kontrolliert werden.</b></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6200] = '<b>webEdition 6.2.0.0:</b><ul><li>Dieses Update ben&ouml;tigt tempor&auml;r <b>ca. 80 MB freien Webspace (Quota!)</b> da die Dateien des Zend-Framework ausgetauscht werden</li><li>In dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer ben&ouml;tigt.</li><li>F&uuml;hren Sie nach dem Rebuild der Dokumente und Vorlagen noch folgende Rebuilds durch:<ul><li>Objekte</li><li>Index-Tabelle</li><li>Navigation</li></ul></li><li>In dieser Version werden neue Datenbank-Indices eingef&uuml;hrt. &Uuml;berpr&uuml;fen Sie nach dem Update das UpdateLog. Sollten dort Fehlermeldungen bzgl. "doppelter" Eintr&auml;ge vorhanden sein, so m&uuml;ssen diese doppelten Eintr&auml;ge von Hand mit einem externen DB-Tool bereinigt (gel&ouml;scht) werden. F&uuml;hren Sie danach ein Update-Wiederholung durch und starten einen erneuten Rebuild</li><li>Das Ladeverhalten f&uuml;r WE-Tags wurde optimiert. Sollten sich Probleme ergeben, so kann das alte Verhalten in den Systemeinstellungen im Tab System bei Abw&auml;rtskompatibilit&auml;t wieder hergestellt werden</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6210] = '<b>webEdition 6.2.1.0:</b><ul><li>Dieses Update ben&ouml;tigt tempor&auml;r <b>ca. 35 MB freien Webspace (Quota!)</b> da die Dateien des Zend-Framework ausgetauscht werden</li><li>In der webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer ben&ouml;tigt sowie eine MySQL DB Version 5.X</li><li>In der Version 6.2 wurden neue Datenbank-Indices eingef&uuml;hrt. &Uuml;berpr&uuml;fen Sie nach dem Update das UpdateLog. Sollten dort Fehlermeldungen bzgl. "doppelter" Eintr&auml;ge vorhanden sein, so m&uuml;ssen diese doppelten Eintr&auml;ge von Hand mit einem externen DB-Tool bereinigt (gel&ouml;scht) werden. F&uuml;hren Sie danach ein Update-Wiederholung durch und starten einen erneuten Rebuild</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6220] = '<b>webEdition 6.2.2.0:</b><ul><li>In der webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer ben&ouml;tigt sowie eine MySQL DB Version 5.X</li><li>Dieses Update behebt einen alten Fehler, der aber in den meisten F&auml;llen nur in der Version 6.2.1 relevant wird. Die Schreibweise der Tags &lt;we:conditionAnd&gt; (nicht AND) und &lt;we:conditionOr&gt; (nicht OR) ist jetzt wichtig f&uuml;r korrektes Funktionieren. Bei Problemen kann in den Einstellungen Tab System der Haken bei Abw&auml;rtskompatibilit&auml;t gesetzt werden. </li></ul>';
$lang['upgrade']['confirmUpdateHint'][6230] = '<b>webEdition 6.2.3.0:</b><ul><li>Dieses Update behebt ein schwerwiegendes Sicherheitsproblem in der Kundenverwaltung. Zur Behebung musste unter anderem der Standardwert f&uuml;r das Attribut register des Tags we:saveRegisteredUser ge&auml;ndert werden. Sollte eine Neuregistrierung von Kunden in Ihrer Site nach dem Update nicht m&ouml;glich sein, so k&ouml;nnen Sie das alte Verhalten im Dialog Einstellungen Kundenverwaltung wiederherstellen</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6300] = '<b>webEdition 6.3.0.0:</b><ul><li>Dieses Update optimiert die komplette webEdition Infrastruktur. Wegen der vielen grundlegenden &Auml;nderungen kann es beim Update einer Site durchaus zu Problemen kommen!</li><li><b>Erstellen Sie unbedingt ein Backup der vorhandenen Site</b></li><li>Beachten Sie die Hinweise in der Versionshistorie zu <b>m&ouml;glichen Problemen und L&ouml;sungen, siehe <a href="http://www.webedition.org/de/webedition-cms/versionshistorie/webedition-6/version-6.3.0.0" target="_blank">Version 6.3.0.0</a></b></li><li>F&uuml;hren Sie gegebenfalls ein <b>Testupdate</b> in einer Kopie der Site durch, insbesondere wenn Sie Module (z.B. Shop, Objekt/DB, aber auch andere) einsetzen.</li><li>Nach dem Rebuild aller Vorlagen und Dokumente pr&uuml;fen Sie das Fehlerlog auf weitere Hinweise</li></ul>';

$lang['upgrade']['confirmUpdateDiskquotaWarning0'] = '<br/>Sie haben mehr als 100 MB freien Webspace.';
$lang['upgrade']['confirmUpdateDiskquotaWarning1'] = '<br/>Sie haben nur noch <b>';
$lang['upgrade']['confirmUpdateDiskquotaWarning2'] = 'MB</b> freien WebSpace (Quota), <br/><b>pr&uuml;fen Sie die Update Hinweise</b> auf den ben&ouml;tigen Speicherplatz!';
$lang['upgrade']['repeatUpdateDiskquotaWarning1'] = '<br/>Sie haben nur noch <b>';
$lang['upgrade']['repeatUpdateDiskquotaWarning2'] = 'MB</b> freien WebSpace (Quota), <br/><b>Dies wird f&uuml;r eine Updatewiederholung nicht ausreichen!</b>';

$lang['upgrade']['confirmUpdateWarningEnd'] = '';
$lang['upgrade']['confirmUpdateWarningTitle'] = 'Zum Fortfahren bitte best&auml;tigen:';
$lang['upgrade']['confirmUpdateWarningCheckbox'] = 'Hiermit best&auml;tige ich, dass ich die oben stehenden Hinweise gelesen habe.';
$lang['upgrade']['pleaseSelectVersion'] = 'Bitte w&auml;hlen Sie eine Zielversion f&uuml;r das Update.';
$lang['upgrade']['noUpgradeForLanguages'] = 'Ein Update zur Version 6 ist momentan nicht m&ouml;glich. Eine oder mehrere installierte Sprachen verhindern das Update.';
$lang['upgrade']['copyFilesSuccess'] = 'Alle ben&ouml;tigten webEdition Dateien wurden angelegt.';
$lang['upgrade']['copyFilesError'] = 'Konnte das webEdition 6 Verzeichnis nicht verschieben';
$lang['upgrade']['copyFilesVersionError'] = 'Konnte die Datei version.php nicht anlegen';
$lang['upgrade']['copyFilesConfError'] = 'Konnte die Konfigurationsdatei nicht abspeichern';
$lang['upgrade']['copyFilesBackupError'] = 'Konnte die Backup Ordner nicht Kopieren';
$lang['upgrade']['copyFilesDirectoryError'] = 'Konnte den Ordner %s nicht anlegen';
$lang['upgrade']['copyFilesMoveDirectoryError'] = 'Konnte den Ordner %s nicht verschieben';
$lang['upgrade']['copyFilesFileError'] = 'Konnte die Datei %s nicht kopieren';
$lang['upgrade']['executePatchesDatabase'] = 'Fehler beim Anpassen der Datenbank. Folgende Tabellen konnten nicht angepasst werden.';
$lang['upgrade']['notEnoughLicenses'] = 'Sie besitzen nicht gen&uuml;gend Lizenzen um ein Update auf webEdition Version 6 vorzunehmen. Sie k&ouml;nnen Updates in unserem Shop kaufen.';
$lang['upgrade']['finishInstallationError'] = 'Beim abschließen des Updates auf webEdition 6 ist ein Fehler aufgetreten.<br />\\\nBitte pr&uuml;fen Sie, ob<br />\\\n<ul><li>Der webEdition Ordner in webEdition5 umbenannt werden konnte (Gibt es den Ordner /webEdition5 ?)</li>\\\n<li>Der webEdition6 Ordner in webEdition umbenannt werden konnte (Gibt es den Ordner /webEdition)</li><br />\\\n<li>Der Backup Ordner nach webEdition/we_backup verschoben werden konnte (Gibt es den Ordner /webEdition/we_backup ?)</li><br />\\\n<li>Das site Verzeichnis nach /webEdition/site Verzeichnis verschoben werden konnte. (Gibt es den Ordner /webEdition/site ?)</li></ul><br />\\\nBitte versuchen Sie zun&auml;chst den Aktualisieren Button zu dr&uuml;cken, f&uuml;hren Sie die genannten Ver&auml;nderungen notfalls von Hand aus, bzw. verst&auml;ndigen Sie den Support';
$lang['upgrade']['finished'] = 'Update auf Version 6 abgeschlossen';
$lang['upgrade']['finished_note'] = 'Die Installation ist beendet. Um alle &Auml;nderungen zu &uuml;bernehmen, wird webEdition nun neu gestartet.<br /><strong>Bitte l&ouml;schen Sie vor der n&auml;chsten Anmeldung Ihren Browsercache und f&uuml;hren dann einen Rebuild durch.</strong>';
$lang['upgrade']['notepad_category'] = 'Sonstiges';
$lang['upgrade']['notepad_headline'] = 'Willkommen bei webEdition 6';
//$lang['upgrade']['notepad_text'] = 'Das Cockpit ist eine der Neuerungen in Version 5. Sie k&ouml;nnen im Cockpit-Men&uuml; verschiedene Widgets ausw&auml;hlen. Jedes Widget ist &uuml;ber die obere Leiste Eigenschaften konfigurierbar und kann frei positioniert werden.';
$lang['upgrade']['notepad_text'] = '';

$lang['update']['headline'] = 'Update';
$lang['update']['nightly-build'] = 'nightly Build';
$lang['update']['alpha'] = 'Alpha';
$lang['update']['beta'] = 'Beta';
$lang['update']['rc'] = 'RC';
$lang['update']['release'] = 'offizieller Release';
$lang['update']['installedVersion'] = 'Momentan installierte Version';
$lang['update']['newestVersionSameBranch'] = '<br/>Neueste Version aus dem selben Entwicklungszweig';
$lang['update']['newestVersion'] = '<br/>Aktuellste verf&uuml;gbare Version';
$lang['update']['updateAvailableText'] = 'Ihre installierte Version ist nicht mehr auf dem neuesten Stand. Bitte w&auml;hlen Sie aus der Liste die Version aus, die Sie installieren wollen.';
$lang['update']['updatetoVersion'] = 'Update auf Version:';
$lang['update']['suggestCurrentVersion'] = 'Wir empfehlen Ihnen, immer die aktuellste webEdition Version zu verwenden.';
$lang['update']['noUpdateNeeded'] = 'Derzeit ist kein Update verf&uuml;gbar. Sie haben bereits die aktuellste Version installiert.';
$lang['update']['repeatUpdatePossible'] = 'Wenn Sie m&ouml;chten, k&ouml;nnen Sie eine Update-Wiederholung durchf&uuml;hren. Dabei werden alle webEdition Programmdateien neu eingespielt.<br />Achtung, dieser Vorgang kann unter Umst&auml;nden eine gewisse Zeit in Anspruch nehmen.<br/><b>Dabei werden maximal ca. 100 MB freier Webspace ben&ouml;tigt.</b>';
$lang['update']['repeatUpdateNeeded'] = '<b>Bevor Sie auf die neue Version updaten k&ouml;nnen, m&uuml;ssen Sie eine Updatewiederholung Ihrer jetzigen Version durchf&uuml;hren</b>, da Ihre SVN-Revision niedriger ist als die in der Datenbank f&uuml;r Ihre Version hinterlegte. <br />Achtung, dieser Vorgang kann unter Umst&auml;nden eine gewisse Zeit in Anspruch nehmen.<br/><b>Dabei werden maximal ca. 100 MB freier Webspace ben&ouml;tigt.</b>';
$lang['update']['repeatUpdateNotPossible'] = 'Die installierte Version ist neuer als die f&uuml;r Updates verf&uuml;gbare Version. <b>Eine Update-Wiederholung daher nicht m&ouml;glich.</b> Falls Sie nightly Builds bzw. Alpha, Beta oder RCs updaten wollen, so aktivieren Sie bitte die entsprechende Option im Reiter "Pre-Release Versionen"';
$lang['update']['noUpdateForLanguagesText'] = 'Sie haben die Version %s installiert. Momentan ist kein Update m&ouml;glich, da nicht f&uuml;r alle installierten Sprachen ein Update vorliegt.';
$lang['update']['installedLanguages'] = 'Folgende Sprachen sind auf Ihrem System installiert';
$lang['update']['updatePreventingLanguages'] = 'Folgende Sprachen verhindern ein Update:';
$lang['update']['confirmUpdateText'] = 'Sie haben derzeit Version&nbsp;%s installiert und m&ouml;chten ein Update zur Version&nbsp;%s durchf&uuml;hren.';
$lang['update']['confirmUpdateSysReqNoCheck'] = '<b>Achtung</b><br/>Die Systemvoraussetzungen k&ouml;nnen bei einem Update von Version %s nicht &uuml;berpr&uuml;ft werden.';
$lang['update']['confirmUpdateVersionDetails'] = 'Details zu den einzelnen Versionen entnehmen Sie bitte der <a target="_blank" href="http://documentation.webedition.org/wiki/de/webedition/change-log/version-6/start">Versionshistorie</a>.';
$lang['update']['confirmRepeatUpdateText'] = 'Sie haben momentan Version&nbsp;%s installiert und m&ouml;chten diese Version erneut einspielen. ';
$lang['update']['confirmRepeatUpdateMessage'] = 'Bei einer Update Wiederholung werden alle webEdition Programmdateien durch die Original webEdition Dateien ersetzt. Dieser Vorgang kann unter Umst&auml;nden einige Zeit in Anspruch nehmen.';
$lang['update']['finished'] = 'Update abgeschlossen';
$lang['update']['spenden'] = 'Diese webEdition Version wurde erm&ouml;glicht durch die Arbeit des gemeinn&uuml;tzigen webEdition e.V. Unterst&uuml;tzen Sie die kostenlose und freiwillige Arbeit der der Vereins- und Community-Mitglieder.
<br>Erm&ouml;glichen Sie durch Ihre Spende, dass:<ul>
<li>der webEdition e.V. professionelle Entwickler einstellen kann</li>
<li>die Beseitigung von Fehlern sowie die Entwicklung<br>
neuer Features beschleunigt wird</li>
<li>die Weiterentwicklung von webEdition langfristig<br>
gesichert wird</li></ul>';

$lang['update']['confirmUpdateWarning6300'] = 'Die webEdition Version 6.3.x f&uuml;hrt grundlegende Neuerungen in der webEdition Infrastruktur ein. Dabei kann es durchaus zu Problemen nach einem Update kommen. Beachten Sie unbedingt diese <a href="http://www.webedition.org/de/webedition-cms/versionshistorie/webedition-6/version-6.3.0.0" target="_blank">Hinweise zu Version 6.3.x</a>. Installieren Sie bitte immer die letzte verf&uuml;gbare Version dieser Serie.<br><b>Wichtig:</b><br>Nach dem Update sollte die Spracheinstellungen jedes einzelnen Backend-Users in der Benutzerverwaltung &uuml;berpr&uuml;ft werden:<br>1. Schritt: Men&uuml; Extras-> Einstellungen->Allgemein, pr&uuml;fen und setzen Sie Backend Sprache und Backend Zeichensatz, speichern!<br>2. Schritt Benutzerverwaltung, je User: Tab "Einstellungen, dort "Oberfl&auml;che", pr&uuml;fen und setzten von Backend Sprache und Backend Zeichensatz.';

$lang['update']['ReqWarnung'] = 'Warnung!';
$lang['update']['ReqWarnungText'] = 'Ihr System erf&uuml;llt nicht alle Softwarevoraussetzungen:';
$lang['update']['ReqWarnungKritisch'] = 'Update blockierend: ';
$lang['update']['ReqWarnungHinweis'] = 'Hinweis: ';
$lang['update']['ReqWarnungPCREold1'] = 'Ihre PCRE-Version (';
$lang['update']['ReqWarnungPCREold2'] = ') ist veraltet. Dies kann zu Problemen f&uuml;hren.';
$lang['update']['ReqWarnungPHPextension'] = 'Eine notwendige PHP-Extension fehlt auf Ihrem Server, es fehlt: ';
$lang['update']['ReqWarnungPHPextensionND'] = 'Die notwendigen PHP-Extensions k&ouml;nnen nicht &uuml;berpr&uuml;ft werden ';
$lang['update']['ReqWarnungNoCheck'] = 'Die Erf&uuml;llung der aktuellen Systemvoraussetzungen auf Ihrem Server kann nicht &uuml;berpr&uuml;ft werden. Bitte pr&uuml;fen Sie die Systemvoraussetzungen unter <a href="http://www.webedition.org/de/webedition-cms/systemvoraussetzungen.php" target="_blank">http://www.webedition.org/de/webedition-cms/systemvoraussetzungen.php</a><br/>Wir empfehlen, <b>nach der manuellen Pr&uuml;fung der Systemvoraussetzungen oben,</b> zun&auml;chst auf die <b>Version 6.1.0.2</b> upzudaten, da dort die Voraussetzungen geringer sind und bei einem anschließenden Update auf die aktuelle Version automatisch &uuml;berpr&uuml;ft werden k&ouml;nnen.';
$lang['update']['ReqWarnungMySQL4'] = 'F&uuml;r die gew&uuml;nschte Version wird mindestens MySQL Version 4.1 ben&ouml;tigt. Die Voraussetzung ist nicht erf&uuml;llt.';
$lang['update']['ReqWarnungMySQL5'] = 'F&uuml;r die gew&uuml;nschte Version wird mindestens MySQL Version 5.0 ben&ouml;tigt. Die Voraussetzung ist nicht erf&uuml;llt.';
$lang['update']['ReqWarnungSDKdb'] = 'SDK DB-Operationen und WE-APPS mit Datenbanknutzung sind nicht verf&uuml;gbar, es fehlen die PHP Extensions PDO und PDO_mysql';
$lang['update']['ReqWarnungMbstring'] = 'MultiByte String Unterst&uuml;tzung (PHP-Extension mbstring) ist nicht verf&uuml;gbar. Damit sind utf-8 Sites nicht realisierbar, SDK und Apps nicht nutzbar und in zuk&uuml;nftigen Versionen die gesamte Funktion von webEdition gef&auml;hrdet.';
$lang['update']['ReqWarnungGdlib'] = 'Die PHP GDlib-Funktionen (PHP-Extension gd) sind auf diesem Server nicht verf&uuml;gbar, daher sind einige Bildbearbeitungs- und Bildvorschaufunktionen nur eingeschr&auml;nkt nutzbar.';
$lang['update']['ReqWarnungExif'] = "Die exif PHP Extension ist auf diesem Server nicht verf&uuml;gbar, daher sind EXIF-Metadaten f&uuml;r Bilder nicht nutzbar.";
$lang['update']['ReqWarnungPHPversion'] = 'Es wird mindestens PHP in der Version 5.2.4 ben&ouml;tigt. Festgestellt wurde Version ';
$lang['update']['ReqWarnungPHPversionForV640'] = 'F&uuml;r ein Update auf webEdition-Versionen neuer als 6.3.9.0 wird PHP mindestens in der Version 5.3.7 ben&ouml;tigt. Festgestellt wurde Version ';

$lang['installer']['headline'] = 'Installation wird durchgef&uuml;hrt';
$lang['installer']['headlineConfirmInstallation'] = 'Installation best&auml;tigen';
$lang['installer']['confirmInstallation'] = 'ACHTUNG !<br>W&auml;hrend des Update-Vorgangs k&ouml;nnen Daten besch&auml;digt werden. Wenn Sie ohne ein Backup fortfahren besteht die Gefahr, dass Sie Daten verlieren.<br />Wollen Sie mit der Installation fortfahren?';
$lang['installer']['downloadInstaller'] = 'Installer herunterladen';
$lang['installer']['getChanges'] = 'Ben&ouml;tigte Dateien ermitteln';
$lang['installer']['downloadChanges'] = 'Dateien herunterladen';
$lang['installer']['prepareChanges'] = 'Dateien vorbereiten';
$lang['installer']['updateDatabase'] = 'Datenbank aktualisieren';
$lang['installer']['copyFiles'] = 'Dateien installieren';
$lang['installer']['executePatches'] = 'Patche ausf&uuml;hren';
$lang['installer']['finishInstallation'] = 'Installation abschliessen';
$lang['installer']['downloadFilesTotal'] = 'Dieses Update ben&ouml;tigt %s neue Dateien';
$lang['installer']['downloadFilesFiles'] = 'Dateien';
$lang['installer']['downloadFilesPatches'] = 'Patches';
$lang['installer']['downloadFilesQueries'] = 'Datenbankanfragen';
$lang['installer']['updateDatabaseNotice'] = 'Hinweis beim Schritt: Datenbank aktualisieren';
$lang['installer']['tableExists'] = 'Tabelle existiert bereits';
$lang['installer']['tableChanged'] = 'Tabelle wurde aktualisiert';
$lang['installer']['entryAlreadyExists'] = 'Eintr&auml;ge sind schon vorhanden';
$lang['installer']['errorExecutingQuery'] = 'Einige Datenbankanfragen konnten nicht durchgef&uuml;hrt werden.';
$lang['installer']['amountFilesCopied'] = '%s Dateien installiert';
$lang['installer']['amountPatchesExecuted'] = '%s Patch(es) eingespielt';
$lang['installer']['finished'] = 'Die Installation ist beendet. Um alle &Auml;nderungen zu &uuml;bernehmen, wird webEdition nun neu gestartet.';

$lang['languages']['headline'] = 'Sprachinstallation';
$lang['languages']['installLamguages'] = 'Folgende Sprachen k&ouml;nnen installiert werden.<br />Besonders <i>hervorgehobene</i> Sprachen sind bereits auf dem System installiert, die Installation kann aber wiederholt werden.<br /><b>Wichtiger Hinweis:</b> Als <font color="red">[beta]</font> markierte Sprachen k&ouml;nnen unvollst&auml;ndig und unter Umst&auml;nden fehlerhaft sein. Sie k&ouml;nnen sich jedoch gern an das Projektteam wenden um diese &Uuml;bersetzungen zu vervollst&auml;ndigen.';
$lang['languages']['languagesNotReady'] = 'Folgende Sprachen k&ouml;nnen f&uuml;r diese Version momentan leider nicht installiert werden';
$lang['languages']['confirmInstallation'] = 'Die folgenden Sprachen werden installiert.';
$lang['languages']['installLanguages'] = 'Ausgew&auml;hlte Sprachen installieren';
$lang['languages']['noLanguageSelectedText'] = 'Sie haben keine Sprache ausgew&auml;hlt. Bitte gehen Sie zur&uuml;ck zum Sprachauswahldialog und w&auml;hlen Sie die Sprachen aus, die sie installieren m&ouml;chten.';
$lang['languages']['finished'] = 'Sprachinstallation abgeschlossen';

$lang['notification']['upgradeNotPossibleYet'] = 'Ein Update zur Version 5 ist erst ab dem 04.06.2007 m&ouml;glich';
$lang['notification']['upgradeMaintenance'] = 'Wegen Wartungsarbeiten ist ein Update zu webEdition Version 5 derzeit nicht m&ouml;glich.';



$luSystemLanguage = array(
	'installer' => array(
		'downloadInstallerError' => 'Fehler beim Schritt: Herunterladen des Installers',
		'getChangesError' => 'Fehler beim Schritt: Ben&ouml;tigte Dateien ermitteln',
		'downloadChangesError' => 'Fehler beim Schritt: Dateien herunterladen',
		'updateDatabaseError' => 'Fehler beim Schritt: Datenbank aktualisieren',
		'updateDatabaseNotice' => 'Hinweis beim Schritt: Datenbank aktualisieren',
		'prepareChangesError' => 'Fehler beim Schritt: Dateien vorbereiten',
		'copyFilesError' => 'Fehler beim Schritt: Dateien installieren',
		'executePatchesError' => 'Fehler beim Schritt:  Patche ausf&uuml,hren',
		'finishInstallationError' => 'Fehler beim Schritt: Installation abschliessen',
		'errorMessage' => 'Fehlermeldung',
		'errorIn' => 'in',
		'errorLine' => 'Zeile',
		'tableExists' => 'Tabelle existiert bereits',
		'tableChanged' => 'Tabelle wurde aktualisiert',
		'entryAlreadyExists' => 'Eintr&auml,ge sind schon vorhanden',
		'errorExecutingQuery' => 'Einige Datenbankanfragen konnten nicht durchgef&uuml,hrt werden.',
		'fileNotWritableError' => 'Auf folgende Datei kann nicht schreibend zugegriffen werden:<br />\\\n<code class=\\\\\"errorText\\\\\">%s</code><br />\\\nDa webEdition die Zugriffsrechte der Datei nicht anpassen konnte, machen Sie dies bitte manuell auf Ihrem Server. Danach klicken Sie zum Fortsetzen der Installation auf \\\\\"Neu laden\\\\\".',
	),
	'upgrade' => array(
		'start' => 'Starte Update auf webEdition Version 5 (' . (isset($_SESSION['clientTargetVersion']) ? $_SESSION['clientTargetVersion'] : '') . ')',
		'finished' => 'Update auf Version 5 abgeschlossen',
	),
	'update' => array(
		'start' => 'Starte Update auf Version ' . (isset($_SESSION['clientTargetVersion']) ? $_SESSION['clientTargetVersion'] : ''),
		'finished' => 'Update abgeschlossen.',
		'version' => ' Version: ',
		'branch' => ', Entw.-Zweig: ',
		'svn' => ', SVN-Revision: ',
	),
	'modules' => array(
		'start' => 'Starte Modulinstallation',
		'finished' => 'Modulinstallation abgeschlossen',
	),
	'languages' => array(
		'start' => 'Starte Sprachinstallation',
		'finished' => 'Sprachinstallation abgeschlossen',
	),
);
