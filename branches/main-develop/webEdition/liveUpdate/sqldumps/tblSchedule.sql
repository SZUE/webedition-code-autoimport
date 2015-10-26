CREATE TABLE ###TBLPREFIX###tblSchedule (
  DID bigint(20) unsigned NOT NULL default '0',
  `expire` DATETIME NOT NULL,
  `lockedUntil` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Was tinyint(1) unsigned NOT NULL default '0',
  ClassName enum('we_htmlDocument','we_webEditionDocument','we_objectFile') NOT NULL,
  SerializedData longblob NOT NULL,
  Schedpro text NOT NULL,
  `Type` tinyint(3) unsigned NOT NULL default '0',
  Active tinyint(1) unsigned default NULL,
  PRIMARY KEY (DID,ClassName,Active,`expire`,Was,`Type`),
  KEY Wann (`expire`,Active,`lockedUntil`)
) ENGINE=MyISAM;

/* query separator */
###ONCOL(Wann,###TBLPREFIX###tblSchedule) UPDATE ###TBLPREFIX###tblSchedule SET `expire`=FROM_UNIXTIME(Wann) WHERE `expire`="0000-00-00";###
/* query separator */
###UPDATEDROPCOL(Wann,###TBLPREFIX###tblSchedule)###
