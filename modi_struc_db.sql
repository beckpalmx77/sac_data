-- SQL Commands for database index optimization
-- Safe execution script that checks if indexes already exist before creation to prevent errors on duplicate runs.
-- Date: 2026-06-30

DELIMITER $$

DROP PROCEDURE IF EXISTS AddIndexIfNotExists$$

CREATE PROCEDURE AddIndexIfNotExists(
    IN tableName VARCHAR(64),
    IN indexName VARCHAR(64),
    IN indexColumns VARCHAR(255)
)
BEGIN
    DECLARE indexCount INT;
    
    SELECT COUNT(*) INTO indexCount
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = tableName
      AND index_name = indexName;
      
    IF indexCount = 0 THEN
        SET @sql = CONCAT('CREATE INDEX ', indexName, ' ON ', tableName, ' (', indexColumns, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        SELECT CONCAT('Index ', indexName, ' created successfully on ', tableName) AS Result;
    ELSE
        SELECT CONCAT('Index ', indexName, ' already exists on ', tableName) AS Result;
    END IF;
END$$

DELIMITER ;

-- Run the index creation helper safely
CALL AddIndexIfNotExists('ims_product_sale_cockpit', 'idx_sale_cp_date', 'DI_DATE(10)');
CALL AddIndexIfNotExists('ims_product_sale_cockpit', 'idx_sale_cp_year_month', 'DI_YEAR(10), DI_MONTH(10)');
CALL AddIndexIfNotExists('ims_product_sale_cockpit', 'idx_sale_cp_year_branch', 'DI_YEAR(10), BRANCH(30)');
CALL AddIndexIfNotExists('ims_product_sale_cockpit_day', 'idx_sale_cp_day_ymb', 'year(10), month(10), branch(30)');

-- Clean up helper procedure
DROP PROCEDURE IF EXISTS AddIndexIfNotExists;
