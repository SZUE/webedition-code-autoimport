CREATE TABLE tblWorkflowTask (
  ID int  NOT NULL IDENTITY(1,1),
  userID int  NOT NULL default '0',
  Edit int  NOT NULL default '0',
  Mail int  NOT NULL default '0',
  stepID int  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 
