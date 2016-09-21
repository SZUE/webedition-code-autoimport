###ONCOL(DatePayment,###TBLPREFIX###tblOrders)UPDATE ###TBLPREFIX###tblOrders SET DatePayment=NULL WHERE DatePayment='0000-00-00 00:00:00';###
/* query separator */
###ONCOL(DatePayment,###TBLPREFIX###tblOrders)UPDATE ###TBLPREFIX###tblOrders SET DateShipping=NULL WHERE DateShipping='0000-00-00 00:00:00';###
/* query separator */
