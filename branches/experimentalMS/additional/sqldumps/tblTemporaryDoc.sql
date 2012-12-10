
CREATE TABLE tblTemporaryDoc (
  DocumentID bigint  NOT NULL default '0',
  DocumentObject text NOT NULL,
  DocTable  varchar(25) CHECK(DocTable IN ('tblFile','tblObjectFiles')) NOT NULL,
  UnixTimestamp int  NOT NULL default '0',
  Active tinyint  NOT NULL default '0',
  PRIMARY KEY (DocTable,DocumentID,Active)
) 
