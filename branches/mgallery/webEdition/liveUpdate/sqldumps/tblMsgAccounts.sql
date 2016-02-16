CREATE TABLE ###TBLPREFIX###tblMsgAccounts (
  ID int unsigned NOT NULL auto_increment,
  UserID int unsigned default NULL,
  name varchar(255) NOT NULL default '',
  msg_type int unsigned default NULL,
  deletable tinyint unsigned NOT NULL default '0',
  uri varchar(255) default NULL,
  `user` varchar(255) default NULL,
  pass varchar(255) default NULL,
  update_interval smallint unsigned NOT NULL default '0',
  ext varchar(255) default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;