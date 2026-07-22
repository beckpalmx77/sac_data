<?php

ini_set('display_errors', 1);
error_reporting(~0);

include("../config/connect_db.php");

$year = date("Y");

$month = date("n");

$year = "2023";

$stmt_find_data = $conn->prepare("SELECT COUNT(*) FROM ims_product_sale_cockpit_day WHERE year = :year AND month = :month AND branch = :branch AND day = :day");
$stmt_insert = $conn->prepare("INSERT INTO ims_product_sale_cockpit_day(branch,day,month,year,total,remark) VALUES (:branch,:day,:month,:year,:total,:remark)");
$stmt_update = $conn->prepare("UPDATE ims_product_sale_cockpit_day SET total=:total, remark=:remark WHERE branch = :branch AND day = :day AND month = :month AND year = :year");

$branches = ["CP-340", "CP-BY", "CP-BB", "CP-RP"];

$conn->beginTransaction();
try {
    for ($year = 2019; $year <= 2023; $year++) {
        for ($month = 1; $month <= 12; $month++) {
            $str_insert = "OK Insert";
            $str_update = "OK Update";
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            foreach ($branches as $branch) {
                for ($day_loop = 1; $day_loop <= $day; $day_loop++) {

                    $sql_find = "SELECT DI_DATE FROM ims_product_sale_cockpit 
                        WHERE DI_YEAR = '" . $year . "'
                        AND DI_MONTH = '" . $month . "'
                        AND BRANCH = '" . $branch . "'
                        AND ICCAT_CODE <> '6SAC08' AND (DT_DOCCODE <> 'IS' OR DT_DOCCODE <> 'IIS' OR DT_DOCCODE <> 'IC')
                        AND CAST(SUBSTR(DI_DATE,1,2) AS UNSIGNED) = " . $day_loop . "
                        GROUP BY DI_DATE";

                    $nRows = $conn->query($sql_find)->fetchColumn();
                    $total = "0.00";

                    if ($nRows > 0) {
                        $sql_get = "SELECT BRANCH,DI_DATE,DI_YEAR,DI_MONTH,sum(CAST(TRD_G_KEYIN AS DECIMAL(10,2))) as TRD_G_KEYIN
                                FROM ims_product_sale_cockpit 
                                WHERE DI_YEAR = '" . $year . "'
                                AND DI_MONTH = '" . $month . "'
                                AND BRANCH = '" . $branch . "'
                                AND ICCAT_CODE <> '6SAC08' AND (DT_DOCCODE <> 'IS' OR DT_DOCCODE <> 'IIS' OR DT_DOCCODE <> 'IC')
                                AND CAST(SUBSTR(DI_DATE,1,2) AS UNSIGNED) = " . $day_loop . "
                                GROUP BY DI_DATE,DI_MONTH,BRANCH  
                                ORDER BY CAST(SUBSTR(DI_DATE,1,2) AS UNSIGNED)";

                        $statement = $conn->query($sql_get);
                        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($results as $result) {
                            $total = $result['TRD_G_KEYIN'];
                        }
                    }

                    $stmt_find_data->execute([
                        ':year'   => $year,
                        ':month'  => $month,
                        ':branch' => $branch,
                        ':day'    => $day_loop
                    ]);

                    if ($stmt_find_data->fetchColumn() <= 0) {
                        $stmt_insert->execute([
                            ':branch' => $branch,
                            ':day'    => $day_loop,
                            ':month'  => $month,
                            ':year'   => $year,
                            ':total'  => $total,
                            ':remark' => $str_insert
                        ]);
                    } else {
                        $stmt_update->execute([
                            ':total'  => $total,
                            ':remark' => $str_update,
                            ':branch' => $branch,
                            ':day'    => $day_loop,
                            ':month'  => $month,
                            ':year'   => $year
                        ]);
                    }
                }
            }
        }
    }
    $conn->commit();
    echo "Summary processing 2019-2023 completed.\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error in process_summary_sale_cockpit_day_2: " . $e->getMessage() . "\n";
}