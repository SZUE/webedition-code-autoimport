CREATE TABLE ###TBLPREFIX###tblversions (
  `ID` bigint(20) unsigned NOT NULL auto_increment,
  `documentID` bigint(20) unsigned NOT NULL,
  `documentTable` varchar(64) NOT NULL,
  `documentElements` blob NOT NULL,
  `documentScheduler` blob NOT NULL,
  `documentCustomFilter` blob NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  `status` enum('saved','published','unpublished','deleted') NOT NULL,
  `version` bigint(20) unsigned NOT NULL,
  `binaryPath` varchar(255) NOT NULL,
  `modifications` varchar(255) NOT NULL,
  `modifierID` bigint(20) unsigned NOT NULL,
  `IP` varchar(30) NOT NULL,
  `Browser` varchar(255) NOT NULL,
  `ContentType` varchar(32) NOT NULL,
  `Text` varchar(255) NOT NULL,
  `ParentID` int(11) unsigned NOT NULL,
  `Icon` varchar(64) NOT NULL,
  `CreationDate` int(11) unsigned NOT NULL,
  `CreatorID` bigint(20) unsigned NOT NULL,
  `Path` varchar(255) NOT NULL,
  `TemplateID` int(11) unsigned NOT NULL,
  `Filename` varchar(255) NOT NULL,
  `Extension` varchar(16) NOT NULL,
  `IsDynamic` tinyint(1) unsigned NOT NULL,
  `IsSearchable` tinyint(1) unsigned NOT NULL,
  `ClassName` varchar(64) NOT NULL,
  `DocType` smallint(6) NOT NULL,
  `Category` text NOT NULL,
  `RestrictOwners` tinyint(1) unsigned NOT NULL,
  `Owners` varchar(255) NOT NULL,
  `OwnersReadOnly` text NOT NULL,
  `Language` varchar(5) NOT NULL,
  `WebUserID` bigint(20) unsigned NOT NULL,
  `Workspaces` varchar(1000) NOT NULL,
  `ExtraWorkspaces` varchar(1000) NOT NULL,
  `ExtraWorkspacesSelected` varchar(1000) NOT NULL,
  `Templates` varchar(255) NOT NULL,
  `ExtraTemplates` varchar(255) NOT NULL,
  `MasterTemplateID` bigint(20) unsigned NOT NULL default '0',
  `TableID` bigint(20) unsigned NOT NULL,
  `ObjectID` bigint(20) unsigned NOT NULL,
  `IsClassFolder` tinyint(1) unsigned NOT NULL,
  `IsNotEditable` tinyint(1) unsigned NOT NULL,
  `Charset` varchar(64) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  `fromScheduler` tinyint(1) unsigned NOT NULL,
  `fromImport` tinyint(1) unsigned NOT NULL,
  `resetFromVersion` bigint(20) unsigned NOT NULL,
  `InGlossar` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY documentID (documentID),
  KEY `timestamp` (`timestamp`,`CreationDate`),
  KEY `binaryPath` (`binaryPath`),
  KEY `version` (`version`)
) ENGINE=MyISAM ;
