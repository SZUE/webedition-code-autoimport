CREATE TABLE ###TBLPREFIX###tblglossary (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Path` varchar(255) default NULL,
  `IsFolder` tinyint(1) unsigned default NULL,
  `Icon` varchar(255) default NULL,
  `Text` varchar(255) NOT NULL default '',
  `Type` enum('abbreviation','acronym','foreignword','link') NOT NULL default 'abbreviation',
  `Language` varchar(5) NOT NULL default '',
  `Title` tinytext NOT NULL,
  `Attributes` text NOT NULL,
  `Linked` tinyint(1) unsigned NOT NULL default '0',
  `Description` text NOT NULL,
  `CreationDate` int(11) unsigned NOT NULL default '0',
  `ModDate` int(11) unsigned NOT NULL default '0',
  `Published` int(11) unsigned NOT NULL default '0',
  `CreatorID` bigint(20) unsigned NOT NULL default '0',
  `ModifierID` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;
