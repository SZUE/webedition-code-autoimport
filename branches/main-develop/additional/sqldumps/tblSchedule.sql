CREATE TABLE ###TBLPREFIX###tblSchedule (
  DID bigint(20) unsigned NOT NULL default '0',
  Wann int(11) unsigned NOT NULL default '0',
  `lockedUntil` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Was int(11) unsigned NOT NULL default '0',
  ClassName varchar(64) NOT NULL default '',
  SerializedData longblob,
  Schedpro longtext,
  `Type` tinyint(3) unsigned NOT NULL default '0',
  Active tinyint(1) unsigned default NULL,
  PRIMARY KEY (DID,Wann,Was,`Type`,Active),
  KEY Wann (Wann,`lockedUntil`),
  KEY Active (Active,Schedpro(1))
) ENGINE=MyISAM;
