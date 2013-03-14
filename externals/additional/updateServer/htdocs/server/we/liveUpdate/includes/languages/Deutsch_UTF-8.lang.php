<?php

$lang['register']['headline'] = 'Registrierung';
$lang['register']['insertSerial'] = 'Bitte geben Sie hier Ihre Seriennummer ein';
$lang['register']['serial'] = 'Seriennummer';
$lang['register']['repeatRegistration'] = 'Ihre Produkt-Id konnte nicht gefunden werden.<br /><br />Die Registrierungsdaten auf unserem Server weichen von den Informationen auf Ihrer Domain ab. Dies kann bspw. passieren, wenn Module für die Domain registriert, aber nicht mehr installiert sind (nach Neuinstallation). Bitte geben Sie Ihre Seriennummer erneut ein, um die Registrierungsdaten anzugleichen. Sie können dann mit dem Update/ der Modulinstallation fortfahren.';
$lang['register']['reInstallModules'] = 'Die Registrierung konnte nicht abgeschlossen werden. Einige für diese Domain registrierte Module sind nicht installiert. Um die Registrierung abzuschliessen, werden diese Module nun installiert.';
$lang['register']['errorWithSerial'] = 'Die Registrierung konnte nicht duchgeführt werden!';
$lang['register']['registerSuccess'] = 'webEdition wurde erfolgreich registriert. Um alle Funktionen von webEdition nutzen zu können, wird webEdition nun neu gestartet.';
$lang['register']['registerError'] = 'Die Registrierung konnte nicht abgeschlossen werden. Folgender Fehler trat auf.';
$lang['register']['registerErrorDetail'] = 'Die zur Registrierung benötigten Dateien konnten nicht geschrieben werden!';
$lang['register']['informAboutUpdates'] = 'Ich möchte über Updates informiert werden';
$lang['register']['email'] = 'E-Mail-Adresse';
$lang['register']['salutation'] = 'Anrede';
$lang['register']['titel'] = 'Titel';
$lang['register']['forename'] = 'Vorname';
$lang['register']['surname'] = 'Nachname';
$lang['register']['language'] = 'Sprache';
$lang['register']['enterValidEmail'] = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
$lang['register']['salutationMr'] = 'Herr';
$lang['register']['salutationMrs'] = 'Frau';

$lang['license']['undefinedError'] = 'Undefinierter Fehler';

$lang['upgrade']['headline'] = 'Upgrade zu webEdition 6';
$lang['upgrade']['registerBeforeUpgrade'] = 'Die Registrierungsdaten Ihrer webEdition 5 Installation weichen von den auf unserem Server gespeicherten Daten ab. Dies kann bspw. passieren, wenn Module für die Domain registriert, aber nicht mehr installiert sind (nach Neuinstallation). Bitte korrigieren Sie diese Informationen über die Update Funktionen von webEdition 5.';
$lang['upgrade']['registerBeforeUpgrade_we4'] = 'Die Registrierungsdaten Ihrer webEdition 4 Installation weichen von den auf unserem Server gespeicherten Daten ab. Dies kann bspw. passieren, wenn Module für die Domain registriert, aber nicht mehr installiert sind (nach Neuinstallation). Bitte korrigieren Sie diese Informationen über die Update Funktionen von webEdition 4.';
$lang['upgrade']['registerBeforeUpgrade_we5light'] = 'Die Registrierungsdaten Ihrer webEdition 5 light Installation weichen von den auf unserem Server gespeicherten Daten ab. Bitte korrigieren Sie diese Informationen über die Update Funktionen von webEdition 5 light.';
$lang['upgrade']['upgradePossibleText'] = 'Wählen Sie aus der Liste aus, auf welche webEdition Version Sie updaten möchten';
$lang['upgrade']['upgradeToVersion'] = 'Upgrade auf Version';
$lang['upgrade']['confirmUpgradeWarning'] = 'Sie sind gerade dabei, ein Upgrade auf webEdition 6 durchzuführen. <b>In einem ersten Schritt ist dabei nur ein Upgrade auf Version 6.0.0.6 möglich.</b> Dabei werden alle webEdition Programmdateien ersetzt und durch die neuen Dateien ersetzt, dieser Vorgang kann unter Umständen sehr lange dauern. Bitte beachten Sie die folgenden Informationen:<br /><br /><b>Wichtige Informationen:</b><ul><li>webEdition 6 benötigt <u>PHP in der Version 5.2</u> oder neuer. Die aktuell verwendete Version können Sie über das Infofenster "Systeminformationen" (im Menü "Hilfe") ermitteln.</li><li>Nach dem Update muss webEdition neu gestartet werden.</li><li>Anschließend muss ein Rebuild der Seite durchgeführt werden, da diverse Änderungen an den we:tags durchgeführt wurden.</li><li><b>Führen Sie abschließend ein Update auf die aktuellste webEdition Version durch.</b></li><li>Wir empfehlen, dies Schritt für Schritt durchzuführen: 6.0.0.6 -&gt; 6.1.0.2, 6.1.0.2 -&gt; 6.2.X (latest), jeweils mit Rebuilds zwischen den Updates</li></ul>';
$lang['upgrade']['confirmUpgradeWarningTitle'] = 'Zum Fortfahren bitte bestätigen:';
$lang['upgrade']['confirmUpgradeWarningCheckbox'] = 'Hiermit bestätige ich, dass ich die oben stehenden Hinweise gelesen habe.';
$lang['upgrade']['confirmUpdateWarning'] = 'Sie sind gerade dabei, ein Update von webEdition 6 durchzuführen. Bitte beachten Sie die folgenden Informationen:<br /><br /><b>Wichtige Informationen:</b><ul><li>Nach einem Update sollte webEdition neu gestartet werden.</li><li>Anschließend sollten Sie einen Rebuild über Dokumente und Vorlagen durchführen.</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6007] = '<b>webEdition 6.0.0.7:</b><ul><li><b>Ab dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer benötigt.</b><br/>Die aktuell verwendete PHP-Version können Sie über das Infofenster "Systeminformationen" (im Menü "Hilfe") ermitteln.</li><li>Führen Sie nach dem Rebuild der Dokumente und Vorlagen noch folgende Rebuilds durch:<ul><li>Index-Tabelle</li><li>Objekte</li></ul></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6008] = '<b>webEdition 6.0.0.8:</b><ul><li><b>In dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer benötigt.</b><br/>Die aktuell verwendete PHP-Version können Sie über das Infofenster "Systeminformationen" (im Menü "Hilfe") ermitteln.</li><li>Führen Sie nach dem Rebuild der Dokumente und Vorlagen noch folgende Rebuilds durch:<ul><li>Navigation</li></ul></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6100] = '<b>webEdition 6.1.0.0:</b><ul><li>Dieses Update benötigt temporär <b>ca. 62 MB freien Webspace (Quota!)</b> da praktisch alle Dateien ausgetauscht werden</li><li><b>In dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer benötigt.</b><br/>Die aktuell verwendete PHP-Version können Sie über das Infofenster "Systeminformationen" (im Menü "Hilfe") ermitteln.</li><li>Führen Sie nach dem Rebuild der Dokumente und Vorlagen noch folgende Rebuilds durch:<ul><li>Navigation</li><li>Objekte</li><li>Vorlagen</li></ul></li><li>Die PHP-Klassen smtp.class.php, we_mailer_class.inc.php, weNewsletterMailer.php werden in Zukunft nicht mehr unterstützt und als DEPRECATED (veraltet) erklärt. Werden diese Klassen durch direkte PHP-Programmierung in den Vorlagen (die we:tags sind nicht betroffen) angesprochen, so sind sie in zukünftigen Projekten durch die Klasse we_util_Mailer (oder direkt Zend_Mail) zu ersetzen.<br/><b>In vorhandenen Installationen werden diese Klassen nicht gelöscht und stehen weiter zur Verfügung.</b></li><li>Aufgrund des YUI Updates sind <strong>vorhandene WE-Apps unter Version 6.1.0 nicht lauffähig</strong> und müssen vor einem Update angepasst werden. Eine Dokumentation der notwendigen Umstellungsarbeiten finden Sie unter <a href="http://documentation.webedition.org/wiki/de/webedition/developer-information/software-development-kit-sdk/changes-from-sdk6000-to-sdk6100/start" target="_blank">Umstellung von SDK-Version 6.0.0.0 auf SDK-Version 6.1.0</a></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6101] = '<b>webEdition 6.1.0.1:</b><ul><li><b>In dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer benötigt.</b></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6102] = '<b>webEdition 6.1.0.2:</b><ul><li>In dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer benötigt.</li><li><b>Das fehlerhafte Verhalten von &lt;we:ifRegisteredUser cfilter="true" /&gt; bei gesetztem Kundenfilter und Einstellung "Kein Filter benutzen (alle Besucher haben Zugriff)" wurde korrigiert.</b> Wird exakt diese Einstellung in Dokumenten verwendet, so erhalten jetzt tatsächlich alle Besucher Zugriff auf die betroffenen Dokumente.<b> Dies sollte <u>vor</u> und nach dem Update umgehend kontrolliert werden.</b></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6200] = '<b>webEdition 6.2.0.0:</b><ul><li>Dieses Update benötigt temporär <b>ca. 80 MB freien Webspace (Quota!)</b> da die Dateien des Zend-Framework ausgetauscht werden</li><li>In dieser webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer benötigt.</li><li>Führen Sie nach dem Rebuild der Dokumente und Vorlagen noch folgende Rebuilds durch:<ul><li>Objekte</li><li>Index-Tabelle</li><li>Navigation</li></ul></li><li>In dieser Version werden neue Datenbank-Indices eingeführt. Überprüfen Sie nach dem Update das UpdateLog. Sollten dort Fehlermeldungen bzgl. "doppelter" Einträge vorhanden sein, so müssen diese doppelten Einträge von Hand mit einem externen DB-Tool bereinigt (gelöscht) werden. Führen Sie danach ein Update-Wiederholung durch und starten einen erneuten Rebuild</li><li>Das Ladeverhalten für WE-Tags wurde optimiert. Sollten sich Probleme ergeben, so kann das alte Verhalten in den Systemeinstellungen im Tab System bei Abwärtskompatibilität wieder hergestellt werden</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6210] = '<b>webEdition 6.2.1.0:</b><ul><li>Dieses Update benötigt temporär <b>ca. 35 MB freien Webspace (Quota!)</b> da die Dateien des Zend-Framework ausgetauscht werden</li><li>In der webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer benötigt sowie eine MySQL DB Version 5.X</li><li>In der Version 6.2 wurden neue Datenbank-Indices eingeführt. Überprüfen Sie nach dem Update das UpdateLog. Sollten dort Fehlermeldungen bzgl. "doppelter" Einträge vorhanden sein, so müssen diese doppelten Einträge von Hand mit einem externen DB-Tool bereinigt (gelöscht) werden. Führen Sie danach ein Update-Wiederholung durch und starten einen erneuten Rebuild</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6220] = '<b>webEdition 6.2.2.0:</b><ul><li>In der webEdition Version wird <u>PHP in der Version 5.2.4</u> oder neuer benötigt sowie eine MySQL DB Version 5.X</li><li>Dieses Update behebt einen alten Fehler, der aber in den meisten Fällen nur in der Version 6.2.1 relevant wird. Die Schreibweise der Tags &lt;we:conditionAnd&gt; (nicht AND) und &lt;we:conditionOr&gt; (nicht OR) ist jetzt wichtig für korrektes Funktionieren. Bei Problemen kann in den Einstellungen Tab System der Haken bei Abwärtskompatibilität gesetzt werden. </li></ul>';
$lang['upgrade']['confirmUpdateHint'][6230] = '<b>webEdition 6.2.3.0:</b><ul><li>Dieses Update behebt ein schwerwiegendes Sicherheitsproblem in der Kundenverwaltung. Zur Behebung musste unter anderem der Standardwert für das Attribut register des Tags we:saveRegisteredUser geändert werden. Sollte eine Neuregistrierung von Kunden in Ihrer Site nach dem Update nicht möglich sein, so können Sie das alte Verhalten im Dialog Einstellungen Kundenverwaltung wiederherstellen</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6300] = '<b>webEdition 6.3.0.0:</b><ul><li>Dieses Update optimiert die komplette webEdition Infrastruktur. Wegen der vielen grundlegenden Änderungen kann es beim Update einer Site durchaus zu Problemen kommen!</li><li><b>Erstellen Sie unbedingt ein Backup der vorhandenen Site</b></li><li>Beachten Sie die Hinweise in der Versionshistorie zu <b>möglichen Problemen und Lösungen, siehe <a href="http://www.webedition.org/de/webedition-cms/versionshistorie/webedition-6/version-6.3.0.0" target="_blank">Version 6.3.0.0</a></b></li><li>Führen Sie gegebenfalls ein <b>Testupdate</b> in einer Kopie der Site durch, insbesondere wenn Sie Module (z.B. Shop, Objekt/DB, aber auch andere) einsetzen.</li><li>Nach dem Rebuild aller Vorlagen und Dokumente prüfen Sie das Fehlerlog auf weitere Hinweise</li></ul>';

$lang['upgrade']['confirmUpdateDiskquotaWarning0']='<br/>Sie haben mehr als 100 MB freien Webspace.';
$lang['upgrade']['confirmUpdateDiskquotaWarning1']='<br/>Sie haben nur noch <b>';
$lang['upgrade']['confirmUpdateDiskquotaWarning2']='MB</b> freien WebSpace (Quota), <br/><b>prüfen Sie die Update Hinweise</b> auf den benötigen Speicherplatz!';
$lang['upgrade']['repeatUpdateDiskquotaWarning1']='<br/>Sie haben nur noch <b>';
$lang['upgrade']['repeatUpdateDiskquotaWarning2']='MB</b> freien WebSpace (Quota), <br/><b>Dies wird für eine Updatewiederholung nicht ausreichen!</b>';

$lang['upgrade']['confirmUpdateWarningEnd'] = '';
$lang['upgrade']['confirmUpdateWarningTitle'] = 'Zum Fortfahren bitte bestätigen:';
$lang['upgrade']['confirmUpdateWarningCheckbox'] = 'Hiermit bestätige ich, dass ich die oben stehenden Hinweise gelesen habe.';
$lang['upgrade']['pleaseSelectVersion'] = 'Bitte wählen Sie eine Zielversion für das Update.';
$lang['upgrade']['noUpgradeForLanguages'] = 'Ein Update zur Version 6 ist momentan nicht möglich. Eine oder mehrere installierte Sprachen verhindern das Update.';
$lang['upgrade']['copyFilesSuccess'] = 'Alle benötigten webEdition Dateien wurden angelegt.';
$lang['upgrade']['copyFilesError'] = 'Konnte das webEdition 6 Verzeichnis nicht verschieben';
$lang['upgrade']['copyFilesInstalledModulesError'] = 'Konnte die Datei we_installed_modules nicht anlegen';
$lang['upgrade']['copyFilesVersionError'] = 'Konnte die Datei version.php nicht anlegen';
$lang['upgrade']['copyFilesConfError'] = 'Konnte die Konfigurationsdatei nicht abspeichern';
$lang['upgrade']['copyFilesBackupError'] = 'Konnte die Backup Ordner nicht Kopieren';
$lang['upgrade']['copyFilesDirectoryError'] = 'Konnte den Ordner %s nicht anlegen';
$lang['upgrade']['copyFilesMoveDirectoryError'] = 'Konnte den Ordner %s nicht verschieben';
$lang['upgrade']['copyFilesFileError'] = 'Konnte die Datei %s nicht kopieren';
$lang['upgrade']['executePatchesDatabase'] = 'Fehler beim Anpassen der Datenbank. Folgende Tabellen konnten nicht angepasst werden.';
$lang['upgrade']['notEnoughLicenses'] = 'Sie besitzen nicht genügend Lizenzen um ein Update auf webEdition Version 6 vorzunehmen. Sie können Updates in unserem Shop kaufen.';
$lang['upgrade']['finishInstallationError'] = 'Beim abschließen des Updates auf webEdition 6 ist ein Fehler aufgetreten.<br />\\\nBitte prüfen Sie, ob<br />\\\n<ul><li>Der webEdition Ordner in webEdition5 umbenannt werden konnte (Gibt es den Ordner /webEdition5 ?)</li>\\\n<li>Der webEdition6 Ordner in webEdition umbenannt werden konnte (Gibt es den Ordner /webEdition)</li><br />\\\n<li>Der Backup Ordner nach webEdition/we_backup verschoben werden konnte (Gibt es den Ordner /webEdition/we_backup ?)</li><br />\\\n<li>Das site Verzeichnis nach /webEdition/site Verzeichnis verschoben werden konnte. (Gibt es den Ordner /webEdition/site ?)</li></ul><br />\\\nBitte versuchen Sie zunächst den Aktualisieren Button zu drücken, führen Sie die genannten Veränderungen notfalls von Hand aus, bzw. verständigen Sie den Support';
$lang['upgrade']['finishInstallationError_we4'] = 'Beim abschließen des Updates auf webEdition 5 ist ein Fehler aufgetreten.<br />\\\nBitte prüfen Sie, ob<br />\\\n<ul><li>Der webEdition Ordner in webEdition4 umbenannt werden konnte (Gibt es den Ordner /webEdition4 ?)</li>\\\n<li>Der webEdition4 Ordner in webEdition umbenannt werden konnte (Gibt es den Ordner /webEdition)</li><br />\\\n<li>Der Backup Ordner nach webEdition/we_backup verschoben werden konnte (Gibt es den Ordner /webEdition/we_backup ?)</li><br />\\\n<li>Das site Verzeichnis nach /webEdition/site Verzeichnis verschoben werden konnte. (Gibt es den Ordner /webEdition/site ?)</li></ul><br />\\\nBitte versuchen Sie zunächst den Aktualisieren Button zu drücken, führen Sie die genannten Veränderungen notfalls von Hand aus, bzw. verständigen Sie den Support';
$lang['upgrade']['finishInstallationError_we5light'] = 'Beim abschließen des Updates auf webEdition 5 ist ein Fehler aufgetreten.<br />\\\nBitte prüfen Sie, ob<br />\\\n<ul><li>Der webEdition Ordner in webEdition5light umbenannt werden konnte (Gibt es den Ordner /webEdition5light ?)</li>\\\n<li>Der webEdition5light Ordner in webEdition umbenannt werden konnte (Gibt es den Ordner /webEdition)</li><br />\\\n<li>Der Backup Ordner nach webEdition/we_backup verschoben werden konnte (Gibt es den Ordner /webEdition/we_backup ?)</li><br />\\\n<li>Das site Verzeichnis nach /webEdition/site Verzeichnis verschoben werden konnte. (Gibt es den Ordner /webEdition/site ?)</li></ul><br />\\\nBitte versuchen Sie zunächst den Aktualisieren Button zu drücken, führen Sie die genannten Veränderungen notfalls von Hand aus, bzw. verständigen Sie den Support';
$lang['upgrade']['finished'] = 'Update auf Version 6 abgeschlossen';
$lang['upgrade']['finished_note'] = 'Die Installation ist beendet. Um alle Änderungen zu übernehmen, wird webEdition nun neu gestartet.<br /><strong>Bitte löschen Sie vor der nächsten Anmeldung Ihren Browsercache und führen dann einen Rebuild durch.</strong>';
$lang['upgrade']['notepad_category'] = 'Sonstiges';
$lang['upgrade']['notepad_headline'] = 'Willkommen bei webEdition 6';
//$lang['upgrade']['notepad_text'] = 'Das Cockpit ist eine der Neuerungen in Version 5. Sie können im Cockpit-Menü verschiedene Widgets auswählen. Jedes Widget ist über die obere Leiste Eigenschaften konfigurierbar und kann frei positioniert werden.';
$lang['upgrade']['notepad_text'] = '';

$lang['update']['headline'] = 'Update';
$lang['update']['nightly-build'] = 'nightly Build';
$lang['update']['alpha'] = 'Alpha';
$lang['update']['beta'] = 'Beta';
$lang['update']['rc'] = 'RC';
$lang['update']['release'] = 'offizieller Release';
$lang['update']['installedVersion'] = 'Momentan installierte Version';
$lang['update']['newestVersionSameBranch'] = '<br/>Neueste Version aus dem selben Entwicklungszweig';
$lang['update']['newestVersion'] = '<br/>Aktuellste verfügbare Version';
$lang['update']['updateAvailableText'] = 'Ihre installierte Version ist nicht mehr auf dem neuesten Stand. Bitte wählen Sie aus der Liste die Version aus, die Sie installieren wollen.';
$lang['update']['updatetoVersion'] = 'Update auf Version:';
$lang['update']['suggestCurrentVersion'] = 'Wir empfehlen Ihnen, immer die aktuellste webEdition Version zu verwenden.';
$lang['update']['noUpdateNeeded'] = 'Derzeit ist kein Update verfügbar. Sie haben bereits die aktuellste Version installiert.';
$lang['update']['repeatUpdatePossible'] = 'Wenn Sie möchten, können Sie eine Update-Wiederholung durchführen. Dabei werden alle webEdition Programmdateien neu eingespielt.<br />Achtung, dieser Vorgang kann unter Umständen eine gewisse Zeit in Anspruch nehmen.<br/><b>Dabei werden maximal ca. 100 MB freier Webspace benötigt.</b>';
$lang['update']['repeatUpdateNeeded'] = '<b>Bevor Sie auf die neue Version updaten können, müssen Sie eine Updatewiederholung Ihrer jetzigen Version durchführen</b>, da Ihre SVN-Revision niedriger ist als die in der Datenbank für Ihre Version hinterlegte. <br />Achtung, dieser Vorgang kann unter Umständen eine gewisse Zeit in Anspruch nehmen.<br/><b>Dabei werden maximal ca. 100 MB freier Webspace benötigt.</b>';
$lang['update']['repeatUpdateNotPossible'] = 'Die installierte Version ist neuer als die für Updates verfügbare Version. <b>Eine Update-Wiederholung daher nicht möglich.</b> Falls Sie nightly Builds bzw. Alpha, Beta oder RCs updaten wollen, so aktivieren Sie bitte die entsprechende Option im Reiter "Pre-Release Versionen"';
$lang['update']['noUpdateForLanguagesText'] = 'Sie haben die Version %s installiert. Momentan ist kein Update möglich, da nicht für alle installierten Sprachen ein Update vorliegt.';
$lang['update']['installedLanguages'] = 'Folgende Sprachen sind auf Ihrem System installiert';
$lang['update']['updatePreventingLanguages'] = 'Folgende Sprachen verhindern ein Update:';
$lang['update']['confirmUpdateText'] = 'Sie haben derzeit Version&nbsp;%s installiert und möchten ein Update zur Version&nbsp;%s durchführen.';
$lang['update']['confirmUpdateSysReqNoCheck'] ='<b>Achtung</b><br/>Die Systemvoraussetzungen können bei einem Update von Version %s nicht überprüft werden.';
$lang['update']['confirmUpdateVersionDetails'] = 'Details zu den einzelnen Versionen entnehmen Sie bitte der <a target="_blank" href="http://documentation.webedition.org/wiki/de/webedition/change-log/version-6/start">Versionshistorie</a>.';
$lang['update']['confirmRepeatUpdateText'] = 'Sie haben momentan Version&nbsp;%s installiert und möchten diese Version erneut einspielen. ';
$lang['update']['confirmRepeatUpdateMessage'] = 'Bei einer Update Wiederholung werden alle webEdition Programmdateien durch die Original webEdition Dateien ersetzt. Dieser Vorgang kann unter Umständen einige Zeit in Anspruch nehmen.';
$lang['update']['finished'] = 'Update abgeschlossen';
$lang['update']['we51Notification'] = '<h2>Wichtige Informationen vor dem Update!</h2><p>Diese Informationen sind für Sie relevant, wenn Sie von webEdition 5.0 auf Version 5.1 oder höher aktualisieren.</p><ul><li><b>Änderungen an der Benutzeroberfläche:</b> Die Oberfläche wurde verbessert, genaue Informationen entnehmen Sie bitte der <a target="_blank" href="http://documentation.webedition.org/wiki/de/webedition/change-log/version-5/start">Versionshistorie</a>.</li><li><b>Geänderte Systemvoraussetzungen:</b> webEdition benötigt ab Version 5.1 mindestens PHP 4.3. Sie können die auf Ihrem Server installierte PHP Version innerhalb von webEdition über den Menüpunkt Hilfe => Systeminformationen feststellen.</li><li><b>Navigationstool:</b> Ist die Kundenverwaltung installiert, kann es nötig sein, die Zugriffsrechte für Kunden im Navigationstool neu zu setzen. Die Filter wurden vollständig überarbeitet, teilweise können die Einstellungen aus 5.0 beim Update nicht automatisch vollständig übernommen werden.</li></ul>';
$lang['update']['spenden'] = 'Diese webEdition Version wurde ermöglicht durch die Arbeit des gemeinnützigen webEdition e.V. Unterstützen Sie die kostenlose und freiwillige Arbeit der der Vereins- und Community-Mitglieder. 
<br>Ermöglichen Sie durch Ihre Spende, dass:<ul>
<li>der webEdition e.V. professionelle Entwickler einstellen kann</li>
<li>die Beseitigung von Fehlern sowie die Entwicklung<br>
neuer Features beschleunigt wird</li>
<li>die Weiterentwicklung von webEdition langfristig<br>
gesichert wird</li></ul>';

$lang['update']['confirmUpdateWarning6300'] = 'Die webEdition Version 6.3.x führt grundlegende Neuerungen in der webEdition Infrastruktur ein. Dabei kann es durchaus zu Problemen nach einem Update kommen. Beachten Sie unbedingt diese <a href="http://www.webedition.org/de/webedition-cms/versionshistorie/webedition-6/version-6.3.0.0" target="_blank">Hinweise zu Version 6.3.x</a>. Installieren Sie bitte immer die letzte verfügbare Version dieser Serie.<br><b>Wichtig:</b><br>Nach dem Update sollte die Spracheinstellungen jedes einzelnen Backend-Users in der Benutzerverwaltung überprüft werden:<br>1. Schritt: Menü Extras-> Einstellungen->Allgemein, prüfen und setzen Sie Backend Sprache und Backend Zeichensatz, speichern!<br>2. Schritt Benutzerverwaltung, je User: Tab "Einstellungen, dort "Oberfläche", prüfen und setzten von Backend Sprache und Backend Zeichensatz.';


$lang['modules']['headline'] = 'Modulinstallation';
$lang['modules']['textConfirmModules'] = 'Folgende Module sollen installiert werden. Bestätigen Sie Ihre Auswahl und die Installation beginnt. Nach der Installation wird webEdition neu gestartet.';
$lang['modules']['reselectModules'] = 'Die ausgewählten Module können nicht installiert werden. Sie besitzen nicht genügend Lizenzen für alle ausgewählten Module.<br /><br /> Bitte wählen Sie die zu installierenden Module erneut aus.<br /><br />Sie haben folgende Module ausgewählt:';
$lang['modules']['noModulesSelected'] = 'Sie haben noch kein Modul ausgesucht.';
$lang['modules']['moduleAlreadyInstalled'] = 'Dieses Modul ist bereits installiert. Wenn Sie möchten können Sie die Installation wiederholen.';
$lang['modules']['normalModules'] = 'Module';
$lang['modules']['proModules'] = '';
$lang['modules']['dependentModules'] = 'Abhängige Module';
$lang['modules']['noInstallableModules'] = 'Sie können derzeit kein Modul installieren. Alle gekauften Module sind bereits installiert.<br />Neue Module können Sie in unserem Shop kaufen.';
$lang['modules']['finished'] = 'Modulinstallation abgeschlossen';

$lang['update']['ReqWarnung'] = 'Warnung!';
$lang['update']['ReqWarnungText'] = 'Ihr System erfüllt nicht alle Softwarevoraussetzungen:';
$lang['update']['ReqWarnungKritisch'] = 'Update blockierend: ';
$lang['update']['ReqWarnungHinweis'] = 'Hinweis: ';
$lang['update']['ReqWarnungPCREold1'] = 'Ihre PCRE-Version (';
$lang['update']['ReqWarnungPCREold2'] = ') ist veraltet. Dies kann zu Problemen führen.';
$lang['update']['ReqWarnungPHPextension'] = 'Eine notwendige PHP-Extension fehlt auf Ihrem Server, es fehlt: ';
$lang['update']['ReqWarnungPHPextensionND'] = 'Die notwendigen PHP-Extensions können nicht überprüft werden ';
$lang['update']['ReqWarnungNoCheck'] = 'Die Erfüllung der aktuellen Systemvoraussetzungen auf Ihrem Server kann nicht überprüft werden. Bitte prüfen Sie die Systemvoraussetzungen unter <a href="http://www.webedition.org/de/webedition-cms/systemvoraussetzungen.php" target="_blank">http://www.webedition.org/de/webedition-cms/systemvoraussetzungen.php</a><br/>Wir empfehlen, <b>nach der manuellen Prüfung der Systemvoraussetzungen oben,</b> zunächst auf die <b>Version 6.1.0.2</b> upzudaten, da dort die Voraussetzungen geringer sind und bei einem anschließenden Update auf die aktuelle Version automatisch überprüft werden können.';
$lang['update']['ReqWarnungMySQL4'] = 'Für die gewünschte Version wird mindestens MySQL Version 4.1 benötigt. Die Voraussetzung ist nicht erfüllt.';
$lang['update']['ReqWarnungMySQL5'] = 'Für die gewünschte Version wird mindestens MySQL Version 5.0 benötigt. Die Voraussetzung ist nicht erfüllt.';
$lang['update']['ReqWarnungSDKdb'] = 'SDK DB-Operationen und WE-APPS mit Datenbanknutzung sind nicht verfügbar, es fehlen die PHP Extensions PDO und PDO_mysql';
$lang['update']['ReqWarnungMbstring'] = 'MultiByte String Unterstützung (PHP-Extension mbstring) ist nicht verfügbar. Damit sind utf-8 Sites nicht realisierbar, SDK und Apps nicht nutzbar und in zukünftigen Versionen die gesamte Funktion von webEdition gefährdet.';
$lang['update']['ReqWarnungGdlib'] = 'Die PHP GDlib-Funktionen (PHP-Extension gd) sind auf diesem Server nicht verfügbar, daher sind einige Bildbearbeitungs- und Bildvorschaufunktionen nur eingeschränkt nutzbar.';
$lang['update']['ReqWarnungExif'] = "Die exif PHP Extension ist auf diesem Server nicht verfügbar, daher sind EXIF-Metadaten für Bilder nicht nutzbar.";
$lang['update']['ReqWarnungPHPversion'] = 'Es wird mindestens PHP in der Version 5.2.4 benötigt. Festgestellt wurde Version ';

$lang['installer']['headline'] = 'Installation wird durchgeführt';
$lang['installer']['headlineConfirmInstallation'] = 'Installation bestätigen';
$lang['installer']['confirmInstallation'] = 'ACHTUNG !<br>Während des Update-Vorgangs können Daten beschädigt werden. Wenn Sie ohne ein Backup fortfahren besteht die Gefahr, dass Sie Daten verlieren.<br />Wollen Sie mit der Installation fortfahren?';
$lang['installer']['downloadInstaller'] = 'Installer herunterladen';
$lang['installer']['getChanges'] = 'Benötigte Dateien ermitteln';
$lang['installer']['downloadChanges'] = 'Dateien herunterladen';
$lang['installer']['prepareChanges'] = 'Dateien vorbereiten';
$lang['installer']['updateDatabase'] = 'Datenbank aktualisieren';
$lang['installer']['copyFiles'] = 'Dateien installieren';
$lang['installer']['executePatches'] = 'Patche ausführen';
$lang['installer']['finishInstallation'] = 'Installation abschliessen';
$lang['installer']['downloadFilesTotal'] = 'Dieses Update benötigt %s neue Dateien';
$lang['installer']['downloadFilesFiles'] = 'Dateien';
$lang['installer']['downloadFilesPatches'] = 'Patches';
$lang['installer']['downloadFilesQueries'] = 'Datenbankanfragen';
$lang['installer']['updateDatabaseNotice'] = 'Hinweis beim Schritt: Datenbank aktualisieren';
$lang['installer']['tableExists'] = 'Tabelle existiert bereits';
$lang['installer']['tableChanged'] = 'Tabelle wurde aktualisiert';
$lang['installer']['entryAlreadyExists'] = 'Einträge sind schon vorhanden';
$lang['installer']['errorExecutingQuery'] = 'Einige Datenbankanfragen konnten nicht durchgeführt werden.';
$lang['installer']['amountFilesCopied'] = '%s Dateien installiert';
$lang['installer']['amountPatchesExecuted'] = '%s Patch(es) eingespielt';
$lang['installer']['finished'] = 'Die Installation ist beendet. Um alle Änderungen zu übernehmen, wird webEdition nun neu gestartet.';

$lang['languages']['headline'] = 'Sprachinstallation';
$lang['languages']['installLamguages'] = 'Folgende Sprachen können installiert werden.<br />Besonders <i>hervorgehobene</i> Sprachen sind bereits auf dem System installiert, die Installation kann aber wiederholt werden.<br /><b>Wichtiger Hinweis:</b> Als <font color="red">[beta]</font> markierte Sprachen können unvollständig und unter Umständen fehlerhaft sein. Sie können sich jedoch gern an das Projektteam wenden um diese Übersetzungen zu vervollständigen.';
$lang['languages']['languagesNotReady'] = 'Folgende Sprachen können für diese Version momentan leider nicht installiert werden';
$lang['languages']['confirmInstallation'] = 'Die folgenden Sprachen werden installiert.';
$lang['languages']['installLanguages'] = 'Ausgewählte Sprachen installieren';
$lang['languages']['noLanguageSelectedText'] = 'Sie haben keine Sprache ausgewählt. Bitte gehen Sie zurück zum Sprachauswahldialog und wählen Sie die Sprachen aus, die sie installieren möchten.';
$lang['languages']['finished'] = 'Sprachinstallation abgeschlossen';

$lang['notification']['upgradeNotPossibleYet'] = 'Ein Update zur Version 5 ist erst ab dem 04.06.2007 möglich';
$lang['notification']['upgradeMaintenance'] = 'Wegen Wartungsarbeiten ist ein Update zu webEdition Version 5 derzeit nicht möglich.';



$luSystemLanguage = array();

$luSystemLanguage['installer']['downloadInstallerError'] = 'Fehler beim Schritt: Herunterladen des Installers';
$luSystemLanguage['installer']['getChangesError'] = 'Fehler beim Schritt: Benötigte Dateien ermitteln';
$luSystemLanguage['installer']['downloadChangesError'] = 'Fehler beim Schritt: Dateien herunterladen';
$luSystemLanguage['installer']['updateDatabaseError'] = 'Fehler beim Schritt: Datenbank aktualisieren';
$luSystemLanguage['installer']['updateDatabaseNotice'] = 'Hinweis beim Schritt: Datenbank aktualisieren';
$luSystemLanguage['installer']['prepareChangesError'] = 'Fehler beim Schritt: Dateien vorbereiten';
$luSystemLanguage['installer']['copyFilesError'] = 'Fehler beim Schritt: Dateien installieren';
$luSystemLanguage['installer']['executePatchesError'] = 'Fehler beim Schritt:  Patche ausführen';
$luSystemLanguage['installer']['finishInstallationError'] = 'Fehler beim Schritt: Installation abschliessen';
$luSystemLanguage['installer']['errorMessage'] = 'Fehlermeldung';
$luSystemLanguage['installer']['errorIn'] = 'in';
$luSystemLanguage['installer']['errorLine'] = 'Zeile';
$luSystemLanguage['installer']['tableExists'] = 'Tabelle existiert bereits';
$luSystemLanguage['installer']['tableChanged'] = 'Tabelle wurde aktualisiert';
$luSystemLanguage['installer']['entryAlreadyExists'] = 'Einträge sind schon vorhanden';
$luSystemLanguage['installer']['errorExecutingQuery'] = 'Einige Datenbankanfragen konnten nicht durchgeführt werden.';

$luSystemLanguage['register']['registrationError'] = 'Bei der Registrierung trat ein Fehler auf';
$luSystemLanguage['register']['finished'] = 'Registrierung abgeschlossen';

$luSystemLanguage['repeatRegistration']['finished'] = 'Registrierungsinformationen erneut eingegeben';

$luSystemLanguage['upgrade']['start'] = 'Starte Update auf webEdition Version 5 (' . (isset($_SESSION['clientTargetVersion']) ? $_SESSION['clientTargetVersion'] : '') . ')';
$luSystemLanguage['upgrade']['finished'] = 'Update auf Version 5 abgeschlossen';

$luSystemLanguage['update']['start'] = 'Starte Update auf Version ' . (isset($_SESSION['clientTargetVersion']) ? $_SESSION['clientTargetVersion'] : '');
$luSystemLanguage['update']['finished'] = 'Update abgeschlossen.';
$luSystemLanguage['update']['version'] = ' Version: ';
$luSystemLanguage['update']['branch'] = ', Entw.-Zweig: ';
$luSystemLanguage['update']['svn'] = ', SVN-Revision: ';

$luSystemLanguage['modules']['start'] = 'Starte Modulinstallation';
$luSystemLanguage['modules']['finished'] = 'Modulinstallation abgeschlossen';
$luSystemLanguage['languages']['start'] = 'Starte Sprachinstallation';
$luSystemLanguage['languages']['finished'] = 'Sprachinstallation abgeschlossen';

?>