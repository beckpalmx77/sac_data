<?php
$select_query_daily = "
SELECT
ARCAT.ARCAT_CODE,
ARCAT.ARCAT_NAME,
ARFILE.AR_CODE,
ARFILE.AR_NAME,
FORMAT (DOCINFO.DI_DATE, 'dd/MM/yyyy ') as DI_DATE,
DOCINFO.DI_REF,
DOCTYPE.DT_PROPERTIES,
DOCTYPE.DT_DOCCODE,
SKUMASTER.SKU_CODE,
SKUMASTER.SKU_NAME,
GOODSMASTER.GOODS_CODE,
BRAND.BRN_CODE,
BRAND.BRN_NAME,
TRANSTKD.TRD_SEQ,
TRANSTKH.TRH_VATIO,
TRANSTKD.TRD_SH_CODE,
TRANSTKD.TRD_SH_NAME,
TRANSTKD.TRD_LOT_NO,
TRANSTKD.TRD_SERIAL,
TRANSTKD.TRD_UTQNAME,
TRANSTKD.TRD_U_PRC,
TRANSTKD.TRD_Q_FREE,
TRANSTKD.TRD_QTY,
TRANSTKD.TRD_DSC_KEYIN,
TRANSTKD.TRD_DSC_KEYINV,
TRANSTKD.TRD_TDSC_KEYINV,
TRANSTKD.TRD_G_KEYIN,
TRANSTKD.TRD_G_SELL,
TRANSTKD.TRD_G_VAT,
TRANSTKD.TRD_B_AMT,
TRANSTKD.TRD_B_SELL,
TRANSTKD.TRD_B_VAT,
TRANSTKD.TRD_Q_FREE,
TRANSTKD.TRD_VAT_TY,
EXCHANGERATE.CRNCY_CODE,
EXCHANGERATE.CRNCY_NAME,
ARDETAIL.ARD_XCHG,
ARDETAIL.ARD_TDSC_KEYIN,
ARDETAIL.ARD_TDSC_KEYINV,
SALESMAN.SLMN_CODE,
SALESMAN.SLMN_NAME,
WARELOCATION.WL_CODE,
ICCAT.ICCAT_CODE,
ICCAT.ICCAT_NAME,
DOCINFO.DI_KEY,
DAY(DI_DATE) AS DI_DAY ,
MONTH(DI_DATE) AS DI_MONTH ,
YEAR(DI_DATE) AS DI_YEAR
 
FROM
DOCINFO,
DOCTYPE,
TRANSTKH,
TRANSTKD,
ARDETAIL,
EXCHANGERATE,
ARFILE,
GOODSMASTER,
SKUMASTER,
SHIPBY,
ARCAT,
ICCAT,
ICDEPT,
BRAND,
SKUALT,
ICCOLOR,
ICSIZE,
PRJTAB,
DEPTTAB,
BRANCH,
WARELOCATION,
WAREHOUSE,
SLDETAIL,
SALESMAN,
MKTPLAN,
PRMTPLAN,
VATTABLE";

$select_query_daily_cond = "
WHERE
(DOCINFO.DI_DT = DOCTYPE.DT_KEY) AND
((DOCTYPE.DT_PROPERTIES = 302) OR (DOCTYPE.DT_PROPERTIES = 307) OR (DOCTYPE.DT_PROPERTIES = 308) OR (DOCTYPE.DT_PROPERTIES=337)) AND
(DOCINFO.DI_KEY = TRANSTKH.TRH_DI) AND
(TRANSTKH.TRH_KEY = TRANSTKD.TRD_TRH) AND
(DOCINFO.DI_KEY = ARDETAIL.ARD_DI) AND
(DOCINFO.DI_KEY = VATTABLE.VAT_DI) AND
(ARDETAIL.ARD_CRNCYCODE = EXCHANGERATE.CRNCY_CODE) AND
(ARDETAIL.ARD_AR = ARFILE.AR_KEY) AND
(TRANSTKD.TRD_GOODS = GOODSMASTER.GOODS_KEY) AND
(TRANSTKD.TRD_SKU = SKUMASTER.SKU_KEY) AND
(TRANSTKH.TRH_SB = SHIPBY.SB_KEY) AND
(ARFILE.AR_ARCAT = ARCAT.ARCAT_KEY) AND
(SKUMASTER.SKU_ICCAT = ICCAT.ICCAT_KEY) AND
(SKUMASTER.SKU_ICDEPT = ICDEPT.ICDEPT_KEY) AND
(SKUMASTER.SKU_BRN = BRAND.BRN_KEY) AND
(SKUMASTER.SKU_SKUALT = SKUALT.SKUALT_KEY) AND
(SKUMASTER.SKU_ICCOLOR = ICCOLOR.ICCOLOR_KEY) AND
(SKUMASTER.SKU_ICSIZE = ICSIZE.ICSIZE_KEY) AND
(TRANSTKH.TRH_PRJ = PRJTAB.PRJ_KEY) AND
(TRANSTKH.TRH_DEPT = DEPTTAB.DEPT_KEY) AND
(TRANSTKH.TRH_BR=BRANCH.BR_KEY)  AND
TRANSTKH.TRH_MKTP=MKTPLAN.MKTP_KEY AND
TRANSTKH.TRH_PRMT=PRMTPLAN.PRMT_KEY AND
(TRANSTKD.TRD_WL = WARELOCATION.WL_KEY) AND
(WARELOCATION.WL_WH = WAREHOUSE.WH_KEY) AND
SLDETAIL.SLD_DI=DOCINFO.DI_KEY AND
SLDETAIL.SLD_SLMN=SALESMAN.SLMN_KEY AND
(DOCINFO.DI_ACTIVE = 0) ";

$select_query_daily_order = "
ORDER BY
DOCINFO.DI_DATE ASC,
DOCINFO.DI_REF ASC ,
ARCAT.ARCAT_CODE ASC,
ARFILE.AR_CODE ASC,
TRANSTKD.TRD_SEQ ASC ";