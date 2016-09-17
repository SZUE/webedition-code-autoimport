CREATE TABLE ###TBLPREFIX###tblOrderDates (
	ID int unsigned NOT NULL,
	`type` enum(
'MailConfirmation','MailShipping','MailPayment','MailCancellation','MailFinished',
'DateCustomA','DateCustomB','DateCustomC','DateCustomD','DateCustomE','DateCustomF','DateCustomG','DateCustomH','DateCustomI','DateCustomJ',
'MailCustomA','MailCustomB','MailCustomC','MailCustomD','MailCustomE','MailCustomF','MailCustomG','MailCustomH','MailCustomI','MailCustomJ'
) NOT NULL,
	`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY  (ID,type)
) ENGINE=MyISAM;