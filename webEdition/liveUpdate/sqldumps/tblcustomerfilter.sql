###UPDATEONLY###DELETE FROM ###TBLPREFIX###tblcustomerfilter WHERE modelTable="";
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
  modelId bigint(20) unsigned NOT NULL,
  modelType enum('objectFile','image/*','text/html','text/webedition','folder','application/x-shockwave-flash','application/*','video/quicktime','video/*','audio/*')  NOT NULL,
  modelTable enum('tblFile','tblObjectFiles') NOT NULL,
  accessControlOnTemplate tinyint(1) unsigned NOT NULL default '0',
  errorDocNoLogin int(11) unsigned NOT NULL default '0',
  errorDocNoAccess int(11) unsigned NOT NULL default '0',
  mode tinyint(4) unsigned NOT NULL default '0',
  specificCustomers text NOT NULL,
  filter text NOT NULL,
  whiteList text NOT NULL,
  blackList text NOT NULL,
  PRIMARY KEY  (modelTable,modelId)
) ENGINE=MyISAM;

