<?php
$select_query_stock = " SELECT
 ICCAT.ICCAT_CODE,
 ICCAT.ICCAT_NAME,
 DOCINFO.DI_DATE,
 SKUMASTER.SKU_CODE,
 SKUMASTER.SKU_NAME,
 WAREHOUSE.WH_CODE,
 WAREHOUSE.WH_NAME,
 WARELOCATION.WL_CODE,
 WARELOCATION.WL_NAME,
 SKUMOVE.SKM_LOT_NO,
 SKUMOVE.SKM_SERIAL,
 UOFQTY.UTQ_NAME,
 UOFQTY.UTQ_QTY,
 SUM(SKUMOVE.SKM_QTY) QTY ,SUM(SKUMOVE.SKM_COST)  STOCK_COST,
 SUM(SKUMOVE.SKM_QTY*SKUMASTER.SKU_LAST_UCCOST)  AC_COST ,
 SUM(SKUMOVE.SKM_QTY*SKUMASTER.SKU_STD_COST)  STD_COST
FROM
  SKUMASTER 
  JOIN ICCAT ON SKUMASTER.SKU_ICCAT=ICCAT.ICCAT_KEY
  JOIN ICDEPT ON SKUMASTER.SKU_ICDEPT=ICDEPT.ICDEPT_KEY
  JOIN BRAND ON SKUMASTER.SKU_BRN=BRAND.BRN_KEY
  JOIN UOFQTY ON  SKUMASTER.SKU_S_UTQ=UOFQTY.UTQ_KEY 
  JOIN SKUMOVE ON SKUMASTER.SKU_KEY =  SKUMOVE.SKM_SKU
  JOIN WARELOCATION ON  SKUMOVE.SKM_WL=WARELOCATION.WL_KEY 
  JOIN WAREHOUSE ON  WARELOCATION.WL_WH=WAREHOUSE.WH_KEY
  JOIN DOCINFO ON SKUMOVE.SKM_DI=DOCINFO.DI_KEY
  JOIN DOCTYPE ON  DOCINFO.DI_DT=DOCTYPE.DT_KEY ";


//$sql_cond_stock = " WHERE
// SKUMASTER.SKU_STOCK <> 0 ";

$sql_group_stock = " GROUP BY
 ICCAT.ICCAT_CODE,
 ICCAT.ICCAT_NAME,
 SKUMASTER.SKU_CODE,
 SKUMASTER.SKU_NAME,
 DOCINFO.DI_DATE,
 WAREHOUSE.WH_CODE,
 WAREHOUSE.WH_NAME,
 WARELOCATION.WL_CODE,
 WARELOCATION.WL_NAME,
 SKUMOVE.SKM_LOT_NO,
 SKUMOVE.SKM_SERIAL,
 UOFQTY.UTQ_NAME,
 UOFQTY.UTQ_QTY ";

$sql_order_stock = " ORDER BY
  ICCAT.ICCAT_CODE,
  ICCAT.ICCAT_NAME,
  SKUMASTER.SKU_CODE,
  SKUMOVE.SKM_LOT_NO,
  SKUMOVE.SKM_SERIAL,
  WAREHOUSE.WH_CODE,WARELOCATION.WL_CODE ASC ";
