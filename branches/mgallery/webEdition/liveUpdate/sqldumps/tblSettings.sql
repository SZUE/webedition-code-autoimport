/*FIXME: mv banner, shop, newsletter, glossary to this table*/

CREATE TABLE ###TBLPREFIX###tblSettings (
	tool ENUM('banner','glossary','newsletter','shop') NOT NULL,
  pref_name VARCHAR(255) NOT NULL,
  pref_value text NOT NULL,
  PRIMARY KEY  (tool,pref_name)
) ENGINE=MyISAM;

/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool='shop',pref_name='edit_shop_properties',pref_value='a:2:{s:14:"customerFields";a:0:{}s:19:"orderCustomerFields";a:0:{}}';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool='shop',pref_name='shop_pref',pref_value='€|19|german';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool='glossary',pref_name='weGlossaryAutomaticReplacement',pref_value='1';

