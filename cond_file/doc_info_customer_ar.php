<?php
$select_query = "
SELECT
 ARCAT.ARCAT_CODE,
 ARCAT.ARCAT_NAME,
 ARFILE.AR_ENABLE,
 ARFILE.AR_CODE,
 ARFILE.AR_NAME,
 ARFILE.AR_REMARK,
 ARFILE.AR_SLMNCODE,
 ARSUMMARY.ARS_CRE_LIM,
 ACCOUNTCHART.AC_CODE,
 ARFILE.AR_ACCESS,
 DEPTTAB.DEPT_CODE,
 ARGL.ARGL_CODE,
 ARGL.ARGL_NAME,
 ARCONDITION.ARCD_NAME,
 ARCONDITION.ARCD_TRADE_DC,
 ARCONDITION.ARCD_CASH_DC,
 ARCONDITION.ARCD_TERM,
 ARCONDITION.ARCD_ARPRBCODE,
 ADDRBOOK.ADDB_KEY,
 ADDRBOOK.ADDB_COMPANY,
 ADDRBOOK.ADDB_BRANCH,
 ADDRBOOK.ADDB_TAX_ID,
 ADDRBOOK.ADDB_ADDB_1,
 ADDRBOOK.ADDB_ADDB_2,
 ADDRBOOK.ADDB_ADDB_3,
 ADDRBOOK.ADDB_PROVINCE,
 ADDRBOOK.ADDB_POST,
 ADDRBOOK.ADDB_PHONE,
 ADDRBOOK.ADDB_FAX,
 ADDRBOOK.ADDB_EMAIL,
 SALESMAN.SLMN_NAME, 
 ARCONTACT.ARC_DEFAULT,
 CONTACT.CT_INTL,
 CONTACT.CT_NAME,
 CONTACT.CT_SURNME,
 CONTACT.CT_JOBTITLE, 
 CONTACT.CT_MOBILE,
 CONTACT.CT_EMAIL, 
 CONTACT.CT_REMARK 
 
FROM ARFILE
JOIN ARCAT ON ARCAT.ARCAT_KEY = ARFILE.AR_ARCAT
JOIN ARSUMMARY ON ARSUMMARY.ARS_AR=ARFILE.AR_KEY
JOIN ACCOUNTCHART ON ARFILE.AR_AC = ACCOUNTCHART.AC_KEY
JOIN DEPTTAB ON ARFILE.AR_DEPT = DEPTTAB.DEPT_KEY
JOIN ARGL ON ARFILE.AR_ARGL =ARGL_KEY
JOIN ARCONDITION ON ARFILE.AR_KEY = ARCONDITION.ARCD_AR
JOIN ARADDRESS ON ARFILE.AR_KEY = ARADDRESS.ARA_AR
JOIN ADDRBOOK ON ARADDRESS.ARA_ADDB = ADDRBOOK.ADDB_KEY
JOIN SALESMAN ON SALESMAN.SLMN_CODE = ARFILE.AR_SLMNCODE 
jOIN ARCONTACT ON ARFILE.AR_KEY = ARCONTACT.ARC_AR
JOIN CONTACT ON ARCONTACT.ARC_CT = CONTACT.CT_KEY 
";

$sql_cond = "
WHERE AR_KEY >= 0
AND (ARCONDITION.ARCD_DEFAULT='Y')
AND ARADDRESS.ARA_DEFAULT = 'Y'
AND ARFILE.AR_ENABLE='Y' ";

$sql_order = " 
ORDER BY
ARCAT.ARCAT_CODE  ASC,
ARFILE.AR_CODE ASC ";

