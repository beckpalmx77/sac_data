<?php
include dirname(__DIR__) . '/config/connect_db.php';
$stmt = $conn->query('DESCRIBE ims_product');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
