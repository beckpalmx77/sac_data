<?php

$select_query =
"SELECT
 DOCINFO.DI_REF,
 DOCINFO.DI_DATE,
 DOCINFO.DI_ACTIVE,
 ARFILE.AR_CODE,
 ARFILE.AR_NAME,
 ARDETAIL.ARD_B_AMT AROE_B_AMT,
 ARDETAIL.ARD_B_VAT,
 ARDETAIL.ARD_B_SV,
 ARDETAIL.ARD_B_SNV,
 ARDETAIL.ARD_TDSC_KEYIN,
 ARDETAIL.ARD_TDSC_KEYINV,
 ARDETAIL.ARD_G_VAT,
 ARDETAIL.ARD_G_SV,
 ARDETAIL.ARD_G_SNV,
 ARDETAIL.ARD_G_KEYIN,
 ARDETAIL.ARD_DUE_DA,
 ARDETAIL.ARD_CRNCYCODE,
 ARDETAIL.ARD_XCHG,
 SALESMAN.SLMN_CODE,
 SALESMAN.SLMN_NAME,
  TRANSTKH.TRH_REFER_XREF,
 TRANSTKH.TRH_REFER_IREF,
 TRANSTKH.TRH_REFER_PERSON,
 TRANSTKH.TRH_REFER_XTRA1,
 TRANSTKH.TRH_REFER_XTRA2,
 TRANSTKH.TRH_SHIP_DATE,
 TRANSTKH.TRH_VAT_TY,
 TRANSTKH.TRH_VAT_R,
 TRANSTKH.TRH_VATIO,
 TRANSTKH.TRH_N_ITEMS,
 TRANSTKH.TRH_N_QTY,
  DEPTTAB.DEPT_CODE,
 DEPTTAB.DEPT_THAIDESC,
 DEPTTAB.DEPT_ENGDESC,
 PRJTAB.PRJ_CODE,
 PRJTAB.PRJ_NAME,
 TRANSTKD.TRD_SEQ,
 SKUMASTER.SKU_CODE,
 SKUMASTER.SKU_NAME,
 SKUMASTER.SKU_E_NAME,
 UOFQTY.UTQ_NAME,
 UOFQTY.UTQ_QTY,
 GOODSMASTER.GOODS_CODE,
 TRANSTKD.TRD_VAT_TY,
 TRANSTKD.TRD_VAT,
 TRANSTKD.TRD_VAT_R,
 TRANSTKD.TRD_LOT_NO,
 TRANSTKD.TRD_SERIAL,
 TRANSTKD.TRD_SH_CODE,
 TRANSTKD.TRD_SH_NAME,
 TRANSTKD.TRD_QTY,
 TRANSTKD.TRD_Q_FREE,
 TRANSTKD.TRD_UTQNAME,
 TRANSTKD.TRD_UTQQTY,
 TRANSTKD.TRD_K_U_PRC,
 TRANSTKD.TRD_U_PRC,
 TRANSTKD.TRD_U_VATIO,
 TRANSTKD.TRD_B_UPRC,
 TRANSTKD.TRD_DSC_KEYIN,
 TRANSTKD.TRD_DSC_KEYINV,
 TRANSTKD.TRD_G_AMT,
 TRANSTKD.TRD_G_KEYIN,
 TRANSTKD.TRD_G_SELL,
 TRANSTKD.TRD_G_VAT,
 TRANSTKD.TRD_G_AMT,
 TRANSTKD.TRD_TDSC_KEYINV,
 TRANSTKD.TRD_B_SELL,
 TRANSTKD.TRD_B_VAT,
 TRANSTKD.TRD_B_AMT,
 WARELOCATION.WL_CODE,
 WAREHOUSE.WH_CODE,
 ARCONDITION.ARCD_NAME,DT_PROPERTIES
FROM
 DOCINFO,
 DOCTYPE,
 ARDETAIL,
 ARFILE,
 TRANSTKH,
 TRANSTKD,
 SHIPBY,
 VATTABLE,
 ARCONDITION,
 WARELOCATION,
 WAREHOUSE,
 ARCAT,
 GOODSMASTER,
 SKUMASTER,
 ICCAT,
 ICDEPT,
 BRAND,
 SKUALT,
 ICCOLOR,
 ICSIZE,
 PRJTAB,
 DEPTTAB,
 BRANCH, 
 SLDETAIL,
 SALESMAN,
 MKTPLAN,
 PRMTPLAN,
 UOFQTY ";

$sql_cond = " WHERE 
 (DOCINFO.DI_DT=DOCTYPE.DT_KEY) AND
 (DOCINFO.DI_KEY=ARDETAIL.ARD_DI) AND
 ((DOCTYPE.DT_PROPERTIES=308)  OR 
 (DOCTYPE.DT_PROPERTIES=337))AND
 (ARDETAIL.ARD_AR=ARFILE.AR_KEY) AND
 (DOCINFO.DI_KEY=TRANSTKH.TRH_DI) AND
 (TRANSTKH.TRH_KEY=TRANSTKD.TRD_TRH) AND
 (TRANSTKH.TRH_SB=SHIPBY.SB_KEY) AND
 (DOCINFO.DI_KEY=VATTABLE.VAT_DI) AND
 (ARDETAIL.ARD_ARCD=ARCONDITION.ARCD_KEY) AND
 (TRANSTKD.TRD_WL=WARELOCATION.WL_KEY) AND
 (WARELOCATION.WL_WH=WAREHOUSE.WH_KEY)  AND
 (ARFILE.AR_ARCAT = ARCAT.ARCAT_KEY) AND
 (TRANSTKD.TRD_GOODS = GOODSMASTER.GOODS_KEY) AND
 (TRANSTKD.TRD_SKU = SKUMASTER.SKU_KEY) AND
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
 (DOCINFO.DI_ACTIVE = 0) AND
 (DOCINFO.DI_KEY = SLDETAIL.SLD_DI) AND
 (SLDETAIL.SLD_SLMN = SALESMAN.SLMN_KEY) AND  SKUMASTER.SKU_S_UTQ = UOFQTY.UTQ_KEY"	;

$sql_order = " 
 ORDER BY
 SKU_CODE ASC,
 DOCINFO.DI_DATE ASC,
 DOCINFO.DI_REF ASC,
 TRANSTKD.TRD_SEQ ASC";

