CREATE TABLE tblTODOHistory (
  ID int  NOT NULL IDENTITY(1,1),
  ParentID int  NOT NULL default '0',
  UserID int  NOT NULL default '0',
  fromUserID int  NOT NULL default '0',
  Comment text,
  Created int  default NULL,
  "action" int  default NULL,
  "status" tinyint  default NULL,
  tag tinyint  default NULL,
  PRIMARY KEY  (ID)
)