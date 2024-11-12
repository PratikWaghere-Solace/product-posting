<?php
header("Content-Type: application/json; charset=UTF-8");
require "./db.php";
require "./common_function.php";


        $sql = "SELECT * FROM product_variations";
        $stmt = $pdo1->query($sql);
        $size_data = $stmt->fetchAll();
        
        // echo '<pre>';
        // print_r($size_data);
        // echo '</pre>';
        $size_data = json_encode($size_data);

        $file = fopen("allData.json", "w");

        fwrite($file, $size_data);
        fclose($file);

?> 