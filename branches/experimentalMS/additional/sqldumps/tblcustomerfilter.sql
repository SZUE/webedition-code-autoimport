
CREATE TABLE tblcustomerfilter (
  id bigint  NOT NULL IDENTITY(1,1),
  modelId bigint  NOT NULL,
  modelType varchar(15) CHECK(modelType IN('folder','objectFile','text/webedition'))  NOT NULL,
  modelTable varchar(15)  CHECK(modelTable IN ('tblFile','tblObjectFiles')) NOT NULL,
  accessControlOnTemplate tinyint  NOT NULL default '0',
  errorDocNoLogin bigint  NOT NULL default '0',
  errorDocNoAccess bigint  NOT NULL default '0',
  mode tinyint  NOT NULL default '0',
  specificCustomers text NOT NULL,
  filter text NOT NULL,
  whiteList text NOT NULL,
  blackList text NOT NULL,
  PRIMARY KEY  (id),
  CONSTRAINT modelIdN UNIQUE(modelId,modelType,modelTable)
) 

CREATE INDEX idx_tblcustomerfilter_mode ON tblcustomerfilter(mode);
CREATE INDEX idx_tblcustomerfilter_modelType ON tblcustomerfilter(modelType,accessControlOnTemplate);