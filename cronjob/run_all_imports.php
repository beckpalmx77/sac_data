<?php
/**
 * Master Import Runner
 * SAC Data Cronjob Optimization
 */

date_default_timezone_set("Asia/Bangkok");
set_time_limit(0);
ini_set('memory_limit', '1024M');

$start_time = microtime(true);
echo "==================================================\n";
echo "       SAC DATA - MASTER IMPORT RUNNER            \n";
echo "       Start Time: " . date("Y-m-d H:i:s") . "\n";
echo "==================================================\n\n";

$import_scripts = [
    "import_data_brand.php"              => "Brand Data",
    "import_data_price_code.php"         => "Price Code Data",
    "import_data_product_price.php"      => "Product Price Data (Db1)",
    "import_data_product_price_2.php"    => "Product Price Data (Db2)",
    "import_data_customer_addb.php"      => "Customer ADDB",
    "import_data_customer_ar.php"        => "Customer AR (Db1)",
    "import_data_customer_ar_2.php"      => "Customer AR (Db2)",
    "import_data_araddress.php"          => "AR Address",
    "import_data_ardetail.php"           => "AR Detail",
    "import_data_credit_sale.php"        => "Credit Sale",
    "import_data_reserve.php"            => "Reserve Data",
    "import_data_reserve_detail.php"     => "Reserve Detail",
    "import_data_sale_cockpit.php"       => "Sale Cockpit",
    "import_data_sale_sac_all.php"       => "Sale SAC All",
    "import_data_sale_sac_btc.php"       => "Sale SAC BTC",
    "import_data_stock_balance.php"      => "Stock Balance",
    "import_stock_movement.php"          => "Stock Movement",
    "import_product_tires_price_cp.php"  => "Product Tires Price CP",
    "update_product_tires_price.php"     => "Update Tires Price (Db1)",
    "update_product_tires_price_2.php"   => "Update Tires Price (Db2)",
    "delete_dup_ims_product.php"         => "Clean Duplicate Products (Db1)",
    "delete_dup_ims_product_2.php"       => "Clean Duplicate Products (Db2)",
    "process_summary_sale_cockpit_day.php"=> "Summary Sale Cockpit Daily"
];

$summary = [];

foreach ($import_scripts as $script => $title) {
    $script_path = __DIR__ . '/' . $script;
    if (!file_exists($script_path)) {
        echo "[SKIP] File not found: $script ($title)\n";
        continue;
    }

    echo "--------------------------------------------------\n";
    echo "Running: $title ($script)\n";
    echo "--------------------------------------------------\n";

    $t_start = microtime(true);

    try {
        ob_start();
        include $script_path;
        $output = ob_get_clean();
        $t_end = microtime(true);
        $elapsed = round($t_end - $t_start, 2);

        echo trim($output) . "\n";
        echo ">>> Completed in {$elapsed}s\n\n";

        $summary[] = [
            'script' => $script,
            'title' => $title,
            'status' => 'SUCCESS',
            'time' => $elapsed
        ];
    } catch (Throwable $e) {
        if (ob_get_level()) ob_end_clean();
        $t_end = microtime(true);
        $elapsed = round($t_end - $t_start, 2);

        echo "[ERROR] " . $e->getMessage() . "\n";
        echo ">>> Failed after {$elapsed}s\n\n";

        $summary[] = [
            'script' => $script,
            'title' => $title,
            'status' => 'FAILED: ' . $e->getMessage(),
            'time' => $elapsed
        ];
    }
}

$total_elapsed = round(microtime(true) - $start_time, 2);

echo "==================================================\n";
echo "              EXECUTION SUMMARY                   \n";
echo "==================================================\n";
printf("%-35s %-10s %-10s\n", "Script", "Status", "Duration");
echo "--------------------------------------------------\n";

foreach ($summary as $row) {
    printf("%-35s %-10s %6.2fs\n", $row['script'], $row['status'], $row['time']);
}

echo "--------------------------------------------------\n";
echo "Total Execution Time: {$total_elapsed} seconds\n";
echo "End Time: " . date("Y-m-d H:i:s") . "\n";
echo "==================================================\n";
