CREATE TABLE tblWorkflowLog (
  ID bigint  NOT NULL IDENTITY(1,1),
  RefID bigint  NOT NULL default '0',
  docTable varchar(255) NOT NULL default '',
  userID bigint  NOT NULL default '0',
  logDate bigint  NOT NULL default '0',
  "Type" tinyint  NOT NULL default '0',
  "Description" varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID)
) 

