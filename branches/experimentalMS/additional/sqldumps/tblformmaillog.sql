CREATE TABLE tblformmaillog (
  id bigint  NOT NULL IDENTITY(1,1),
  ip varchar(15) NOT NULL,
  unixTime int  NOT NULL,
  PRIMARY KEY  (id)
) 
CREATE INDEX idx_tblformmaillog_ipwhen ON tblformmaillog(ip,unixTime);