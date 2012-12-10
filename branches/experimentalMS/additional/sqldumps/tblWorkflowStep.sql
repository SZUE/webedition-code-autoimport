CREATE TABLE tblWorkflowStep (
  ID int  NOT NULL IDENTITY(1,1),
  Worktime float NOT NULL default '0',
  timeAction tinyint  NOT NULL default '0',
  stepCondition int  NOT NULL default '0',
  workflowID int  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 
