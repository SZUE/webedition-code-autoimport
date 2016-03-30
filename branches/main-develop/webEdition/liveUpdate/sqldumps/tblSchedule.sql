CREATE TABLE ###TBLPREFIX###tblSchedule (
  DID bigint unsigned NOT NULL default '0',
  `expire` DATETIME NOT NULL,
  `lockedUntil` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`task` enum('publish','park','delete','doctype','category','directory','search_enable','search_disable','call') default 'publish',
  ClassName enum('we_htmlDocument','we_webEditionDocument','we_objectFile') NOT NULL,
  SerializedData longblob NOT NULL,
  Schedpro text NOT NULL,
	`rerun` enum('once','hour','day','week','month','year') NOT NULL default '',
  Active tinyint unsigned default NULL,
  PRIMARY KEY (DID,ClassName,Active,`expire`,task,rerun),
  KEY Wann (`expire`,Active,`lockedUntil`)
) ENGINE=MyISAM;

/* query separator */
###ONCOL(Wann,###TBLPREFIX###tblSchedule) UPDATE ###TBLPREFIX###tblSchedule SET `expire`=FROM_UNIXTIME(Wann) WHERE `expire`="0000-00-00";###
/* query separator */
###UPDATEDROPCOL(Wann,###TBLPREFIX###tblSchedule)###
/* query separator */
###ONCOL(Type,###TBLPREFIX###tblSchedule) UPDATE ###TBLPREFIX###tblSchedule SET rerun=`Type`+1;###
/* query separator */
###ONCOL(Was,###TBLPREFIX###tblSchedule) UPDATE ###TBLPREFIX###tblSchedule SET task=Was+2;###
/* query separator */
###ONKEYFAILED(PRIMARY,###TBLPREFIX###tblSchedule)ALTER IGNORE TABLE ###TBLPREFIX###tblSchedule ADD PRIMARY KEY (DID,ClassName,Active,`expire`,task,rerun);###
/* query separator */
###UPDATEDROPCOL(Type,###TBLPREFIX###tblSchedule)###
/* query separator */
###UPDATEDROPCOL(Was,###TBLPREFIX###tblSchedule)###