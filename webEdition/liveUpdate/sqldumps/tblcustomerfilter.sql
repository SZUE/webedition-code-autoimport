###ONCOL(id,###TBLPREFIX###tblcustomerfilter) DELETE FROM ###TBLPREFIX###tblcustomerfilter WHERE modelTable="";###
/* query separator */
###UPDATEDROPCOL(id,###TBLPREFIX###tblcustomerfilter)###
/* query separator */
###UPDATEDROPKEY(modelIdN,###TBLPREFIX###tblcustomerfilter)###
/* query separator */
###UPDATEDROPKEY(modelType,###TBLPREFIX###tblcustomerfilter)###
/* query separator */
###UPDATEDROPKEY(mode,###TBLPREFIX###tblcustomerfilter)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblcustomerfilter (
  modelId int unsigned NOT NULL,
  modelType enum('objectFile','image/*','text/html','text/webedition','folder','application/x-shockwave-flash','application/*','video/*','audio/*')  NOT NULL default 'application/*',
  modelTable enum('tblFile','tblObjectFiles') NOT NULL,
  accessControlOnTemplate tinyint unsigned NOT NULL default '0',
  errorDocNoLogin int unsigned NOT NULL default '0',
  errorDocNoAccess int unsigned NOT NULL default '0',
  mode tinyint unsigned NOT NULL default '0',
  specificCustomers text NOT NULL,
  filter text NOT NULL,
  whiteList text NOT NULL,
  blackList text NOT NULL,
  PRIMARY KEY  (modelTable,modelId)
) ENGINE=MyISAM;
