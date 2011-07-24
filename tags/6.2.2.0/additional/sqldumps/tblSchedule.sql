CREATE TABLE ###TBLPREFIX###tblSchedule (
  DID bigint(20) NOT NULL default '0',
  Wann int(11) NOT NULL default '0',
  Was int(11) NOT NULL default '0',
  ClassName varchar(64) NOT NULL default '',
  SerializedData longblob,
  Schedpro longtext,
  `Type` tinyint(3) NOT NULL default '0',
  Active tinyint(1) default NULL,
  PRIMARY KEY (DID,Wann,Was,`Type`,Active),
  KEY Wann (Wann),
  KEY Active (Active,Schedpro(1))
) ENGINE=MyISAM;
