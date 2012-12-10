CREATE TABLE tblWorkflowDocTask (
  ID int  NOT NULL IDENTITY(1,1),
  documentStepID bigint  NOT NULL default '0',
  workflowTaskID bigint  NOT NULL default '0',
  "Date" bigint  NOT NULL default '0',
  todoID bigint  NOT NULL default '0',
  "Status" int  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 

