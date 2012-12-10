CREATE TABLE tblMsgAccounts (
  ID int  NOT NULL IDENTITY(1,1),
  UserID int  default NULL,
  name varchar(255) NOT NULL default '',
  msg_type int  default NULL,
  deletable tinyint  NOT NULL default '0',
  uri varchar(255) default NULL,
  "user" varchar(255) default NULL,
  pass varchar(255) default NULL,
  update_interval int NOT NULL default '0',
  ext varchar(255) default NULL,
  PRIMARY KEY  (ID)
) 
