<?php
date_default_timezone_set("Asia/Bangkok");
include('../config/connect_db2.php');

try {
    echo "Starting reorder...<br>";
    
    // Get all records ordered by current id
    $stmt = $conn->query("SELECT id FROM ims_product ORDER BY id");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        echo "No records found in ims_product table.";
        exit;
    }

    $total = count($rows);
    echo "Total records: $total<br>";

    // Create temp table with new sequential IDs
    $conn->beginTransaction();
    
    // Create temp table
    $conn->exec("CREATE TEMPORARY TABLE temp_ids (old_id INT, new_id INT)");
    
    // Insert mapping
    $newId = 1;
    $values = [];
    foreach ($rows as $row) {
        $values[] = "(" . (int)$row['id'] . ", " . $newId . ")";
        $newId++;
    }
    
    // Batch insert
    $chunkSize = 1000;
    $chunks = array_chunk($values, $chunkSize);
    foreach ($chunks as $chunk) {
        $conn->exec("INSERT INTO temp_ids (old_id, new_id) VALUES " . implode(",", $chunk));
    }
    
    echo "Temp mapping created<br>";
    
    // Update using join
    $conn->exec("UPDATE ims_product p 
                 INNER JOIN temp_ids t ON p.id = t.old_id 
                 SET p.id = t.new_id");
    
    echo "IDs updated<br>";
    
    // Reset AUTO_INCREMENT
    $maxId = $newId - 1;
    $conn->exec("ALTER TABLE ims_product AUTO_INCREMENT = " . ($maxId + 1));
    
    $conn->commit();
    
    echo "<br>Done! Reordered IDs from 1 to $maxId.<br>";
    echo "AUTO_INCREMENT set to: " . ($maxId + 1);
    
} catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Error: " . $e->getMessage();
}
