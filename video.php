<?php
require "./db.php";
require "./common_function.php";

// $sku = generateSKU($pdo);

// echo count($sku);

// $frame_array = array();

// foreach($sku as $s){
//     $s = explode('-',$s);

//     echo $s[1]. '<br>';

//     $result = getFrameNum($pdo , $s[1]);
//     $frame_array = array_merge($frame_array, $result);

//     echo '<pre>';
//     print_r($result);
//     echo '</pre>';
// }
        // echo '<pre>';
        // print_r($frame_array);
        // echo '</pre>';


        $sql = "SELECT * FROM product_variations";
        $stmt = $pdo1->query($sql);
        $size_data = $stmt->fetchAll();
        
        echo '<pre>';
        print_r($size_data);
        echo '</pre>';

?>