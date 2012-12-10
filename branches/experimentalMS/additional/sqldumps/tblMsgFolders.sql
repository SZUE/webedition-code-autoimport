CREATE TABLE tblMsgFolders (
  ID int  NOT NULL IDENTITY(1,1),
  ParentID int  default NULL,
  UserID int  NOT NULL default '0',
  account_id int  default NULL,
  msg_type tinyint  NOT NULL default '0',
  obj_type tinyint  NOT NULL default '0',
  "Name" varchar(255) NOT NULL default '',
  sortItem varchar(255) default NULL,
  sortOrder varchar(5) default NULL,
  Properties int  default NULL,
  tag tinyint  default NULL,
  PRIMARY KEY  (ID)
) 
/* query separator */
INSERT  INTO tblMsgFolders VALUES (0,1,NULL,1,3,'Messages',NULL,NULL,1,NULL);
/* query separator */
INSERT INTO tblMsgFolders VALUES (1,1,NULL,1,5,'Sent',NULL,NULL,1,NULL);
/* query separator */
INSERT  INTO tblMsgFolders VALUES (0,1,NULL,2,3,'Task',NULL,NULL,1,NULL);
/* query separator */
INSERT INTO tblMsgFolders VALUES (3,1,NULL,2,13,'Done',NULL,NULL,1,NULL);
/* query separator */
INSERT  INTO tblMsgFolders VALUES (3,1,NULL,2,11,'rejected',NULL,NULL,1,NULL);
