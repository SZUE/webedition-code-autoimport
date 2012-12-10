CREATE TABLE tblMsgAddrbook (
  ID int  NOT NULL IDENTITY(1,1),
  UserID int  default NULL,
  strMsgType varchar(255) default NULL,
  strID varchar(255) default NULL,
  strAlias varchar(255) NOT NULL default '',
  strFirstname varchar(255) default NULL,
  strSurname varchar(255) default NULL,
  PRIMARY KEY  (ID)
) 
