CREATE TABLE ###TBLPREFIX###tblvoting (
  ID bigint(20) unsigned NOT NULL auto_increment,
  ParentID bigint(20) unsigned NOT NULL default '0',
  Path varchar(255) default NULL,
  IsFolder tinyint(1) unsigned default NULL,
  Icon ENUM('folder.gif','link.gif') NOT NULL default 'link.gif',
  `Text` varchar(255) NOT NULL default '',
  PublishDate int(10) unsigned NOT NULL default '0',
  QASet text NOT NULL,
  QASetAdditions text,
  IsRequired tinyint(1) unsigned NOT NULL DEFAULT '0',
  AllowFreeText tinyint(1) unsigned NOT NULL DEFAULT '0',
  AllowImages tinyint(1) unsigned NOT NULL DEFAULT '0',
  AllowMedia tinyint(1) unsigned NOT NULL DEFAULT '0',
  AllowSuccessor tinyint(1) unsigned NOT NULL DEFAULT '0',
  AllowSuccessors tinyint(1) unsigned NOT NULL DEFAULT '0',
  Successor int(11) unsigned NOT NULL DEFAULT '0',
  Scores text NOT NULL,
  RevoteControl tinyint(1) unsigned NOT NULL default '0',
  RevoteTime int(11) default '0',
  Owners text NOT NULL,
  RestrictOwners tinyint(1) unsigned NOT NULL default '0',
  Revote longtext NOT NULL,
  RevoteUserAgent longtext NOT NULL,
  Valid int(10) unsigned NOT NULL default '0',
  Active tinyint(1) unsigned NOT NULL default '0',
  ActiveTime tinyint(1) unsigned NOT NULL default '0',
  FallbackIp tinyint(1) unsigned NOT NULL default '0',
  UserAgent tinyint(1) unsigned NOT NULL default '0',
  FallbackUserID tinyint(1) unsigned NOT NULL DEFAULT '0',
  Log tinyint(1) unsigned NOT NULL default '0',
  LogData longtext NOT NULL,
  RestrictIP tinyint(1) unsigned NOT NULL default '0',
  BlackList longtext NOT NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
