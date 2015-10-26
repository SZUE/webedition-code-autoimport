/*FIXME: mv banner, shop, newsletter, glossary to this table*/

CREATE TABLE ###TBLPREFIX###tblSettings (
	tool ENUM('banner','glossary','newsletter','shop','webadmin') NOT NULL,
  pref_name VARCHAR(255) NOT NULL,
  pref_value text NOT NULL,
  PRIMARY KEY  (tool,pref_name)
) ENGINE=MyISAM;

/* query separator */
###UPDATEONLY###DELETE FROM ###TBLPREFIX###tblSettings WHERE tool='shop' AND pref_name IN ('edit_shop_properties','shop_pref');
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool='glossary',pref_name='weGlossaryAutomaticReplacement',pref_value='1';

