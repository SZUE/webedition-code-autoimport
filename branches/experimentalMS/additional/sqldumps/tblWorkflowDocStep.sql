CREATE TABLE tblWorkflowDocStep (
  ID int  NOT NULL IDENTITY(1,1),
  workflowDocID int  NOT NULL default '0',
  workflowStepID bigint  NOT NULL default '0',
  startDate bigint  NOT NULL default '0',
  finishDate bigint  NOT NULL default '0',
  "Status" tinyint  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 
