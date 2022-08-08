<?php

$select_query_sale = "

select 
DATEDIFF(day, BUY_DATE , DUE_DATE) AS Due_Date_Diff ,
DATEDIFF(day, GETDATE() , DUE_DATE ) AS Due_Date_Diff2 ,
v_sale_payment.* 
,ARPAYMENT.ARP_ARD ,DOC2.DI_REF AS DOC_REF2 , CHEQUEIN.CQIN_DI , CHEQUEIN.CQIN_CHEQUE_NO , CHEQUEIN.CQIN_BANK , BANKFILE.BANK_T_NAME 
,ARCONDITION.ARCD_KEY
,ARCONDITION.ARCD_NAME
,ARCONDITION.ARCD_TERM
,FORMAT(DUE_DATE, 'dd/MM/yyyy ') AS DUE_DATE_REF
,FORMAT(BUY_DATE, 'dd/MM/yyyy ') AS DBUY_DATE_REF
from v_sale_payment 
left join ARPAYMENT on ARPAYMENT.ARP_ARD=v_sale_payment.ARD_KEY
left join DOCINFO DOC2 on DOC2.DI_KEY=   ARPAYMENT.ARP_DI
left join CHEQUEIN on CHEQUEIN.CQIN_DI = DOC2.DI_KEY  
left join BANKFILE on BANKFILE.BANK_KEY = CHEQUEIN.CQIN_BANK 
left join ARCONDITION on ARCONDITION.ARCD_KEY = v_sale_payment.ARD_ARCD ";

$select_query_daily_order = " order by BUY_DATE ";