<?php
$select_query = "
 SELECT
 DOCINFO.DI_KEY, 
 DOCINFO.DI_REF,
 DOCINFO.DI_DATE,
 DOCINFO.DI_ACTIVE,
 ARFILE.AR_CODE,
 ARFILE.AR_NAME
 FROM
 DOCINFO,
 DOCTYPE,
 AROE,
 ARFILE";

$sql_cond = " WHERE 
 DOCINFO.DI_DT = DOCTYPE.DT_KEY AND
 DOCTYPE.DT_PROPERTIES = 207 AND
 DOCINFO.DI_KEY = AROE.AROE_DI AND
 AROE.AROE_AR = ARFILE.AR_KEY ";

$sql_order = " ORDER BY DOCINFO.DI_KEY DESC ";