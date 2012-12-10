CREATE TABLE tblErrorLog (
  ID int  NOT NULL IDENTITY(1,1),
  "Type" varchar(25) CHECK( Type IN ('Error','Warning','Parse error','Notice','Core error','Core warning','Compile error','Compile warning','User error','User warning','User notice','Deprecated notice','User deprecated notice','Strict Error','unknown Error','SQL Error')) NOT NULL,
  "Function" varchar(255) NOT NULL DEFAULT '',
  "File" varchar(255) NOT NULL DEFAULT '',
  "Line" int  NOT NULL,
  "Text" text NOT NULL,
  "Backtrace" text NOT NULL,
  "Request" text NOT NULL,
  "Session" text NOT NULL,
  "Global" text NOT NULL DEFAULT '',
  "Server" text NOT NULL,
  "Date" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID)
) 
CREATE INDEX idx_tblErrorLog_Date ON tblErrorLog(Date);