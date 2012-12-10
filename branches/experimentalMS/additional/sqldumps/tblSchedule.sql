CREATE TABLE tblSchedule (
  DID bigint  NOT NULL default '0',
  Wann int  NOT NULL default '0',
  lockedUntil datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Was tinyint  NOT NULL default '0',
  ClassName varchar(25) CHECK(ClassName IN ('we_htmlDocument','we_webEditionDocument','we_objectFile')) NOT NULL,
  SerializedData varbinary(max),
  Schedpro text,
  "Type" tinyint  NOT NULL default '0',
  Active tinyint  default NULL,
  PRIMARY KEY (DID,ClassName,Active,Wann,Was,Type)
) 
CREATE INDEX idx_tblSchedule_Wann ON tblSchedule(Wann,lockedUntil,Active);