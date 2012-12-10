CREATE TABLE tblWorkflowDoc (
  ID int  NOT NULL IDENTITY(1,1),
  workflowID int  NOT NULL default '0',
  documentID int  NOT NULL default '0',
  userID int  NOT NULL default '0',
  "Status" tinyint  NOT NULL default '0',
  PRIMARY KEY  (ID)
)
