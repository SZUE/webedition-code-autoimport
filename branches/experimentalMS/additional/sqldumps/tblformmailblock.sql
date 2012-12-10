CREATE TABLE tblformmailblock (
  id bigint  NOT NULL IDENTITY(1,1),
  ip varchar(15) NOT NULL,
  blockedUntil int  NOT NULL,
  PRIMARY KEY  (id)
) 

CREATE INDEX idx_tblformmailblock_ipblockeduntil ON tblformmailblock(ip,blockedUntil);