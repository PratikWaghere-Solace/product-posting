<?php
header('Content-Type:application/json');
require "./db.php";
include_once "./insert_data.php";
include_once "./Delete_data.php";
             
// https://pratikwaghere-solace.github.io/Json-practice-data/inventory.json


function isPrime($num) {
    if ($num <= 1) {
        return false;
    }
    for ($i = 2; $i <= sqrt($num); $i++) {
        if ($num % $i == 0) {
            return false;
        }
    }
    return true;
}
$doIt = rand(10 ,100);
if (isPrime($doIt)) {
    echo "$doIt is a prime number";
    $dis = new Delete_data();
    $dis->deleteNum();
    variable_array();
} else {
    echo "$doIt is not a prime number";
    $add = new Insert_data();
    $add->insertData();
    variable_array();

}


function variable_array(){
    global $pdo;
    global $pdo2;

            $listing_info = "Select * from product";
            $stmt = $pdo->prepare($listing_info);
            $list =  $stmt->execute();
            $listing = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $size_info = "SELECT * FROM size";
            $stmt = $pdo2->prepare($size_info);
            $size = $stmt->execute();
            $size_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $shiping_info = "SELECT * FROM shipping_profiles"; 
            $stmt = $pdo->prepare($shiping_info);
            $shiping = $stmt->execute();
            $shiping_list = $stmt->fetchAll(PDO::FETCH_ASSOC);


            // Vairiation of array listing with the  size
            $variation = [];
            $id = 0;
            foreach($listing as $list){
                foreach($size_list as $size_data){
                    $num = $list["listing_id"]; 
                    $sku_num = substr($num, 6 , 3);
                    $title = $list["title"];
                    $sku_title_all = explode(" " , $title);
                    $sku_title = $sku_title_all[1];
                    $size_specific = $size_data['size'];
                    $sku = "$sku_num-$sku_title-$size_specific";
                    

                    $variation_data = [
                    'id' => $id,
                    'SKU' => $sku,
                    'listing_id' => $list['listing_id'],
                    'title' => $list['title'],
                    'idsize' => $size_data['idsize'],
                    'size' => $size_data['size'],
                    'sizeStock' => $size_data['sizeStock'],
                    'image_size' => $size_data['image_size'], 
                    'image_height' => $size_data['image_height'],
                    'image_width' => $size_data['image_width'],
                    'inside_size' => $size_data['inside_size'],
                    'inside_height' => $size_data['inside_height'],
                    'inside_width' => $size_data['inside_width'],
                    'frame_size' => $size_data['frame_size'],
                    'frame_height' => $size_data['frame_height'],
                    'frame_width' => $size_data['frame_width'],
                    'outer_size' => $size_data['outer_size'],
                    'outer_height' => $size_data['outer_height'],
                    'outer_width' => $size_data['outer_width'],
                    'mate_spacing' => $size_data['mate_spacing'],
                    'mate_spacing_height' => $size_data['mate_spacing_height'],
                    'mate_spacing_width' => $size_data['mate_spacing_width'],
                    'bredth' => $size_data['bredth'],    
                    ];

                    $variation[] = $variation_data;       
                }
                $id++;
            }

            // echo '<pre>';
            // print_r($shiping_list);
            // echo '</pre>';

                

            // echo '<pre>';
            // print_r($variation);
            // echo '</pre>';

            // echo json_encode($variation);

            $variation_data = json_encode($variation, JSON_PRETTY_PRINT);
            $file = 'inventory_feed.json';
            file_put_contents($file, $variation_data);

            $shiping = [];
            $sid = 0;

            foreach($listing as $list){
                    foreach($shiping_list as $slis){
                        $ship = [   
                        'id' =>  $sid,
                        'listing_id' => $list['listing_id'],
                        'title' => $list['title'],
                        'shiping_id'=> $slis['profile_id'],
                        'shipping_profiles'=> $slis['name']
                        ];
                }
                $sid++;
                $shiping[] = $ship;
            }

            // print_r($shiping);

            // echo json_encode($shiping);

            $shiping_data = json_encode($shiping, JSON_PRETTY_PRINT);
            $file2 = 'shipping_profile_feed.json';
            file_put_contents($file2, $shiping_data);


            sleep(20);

            exec('git status',$output1,$result1);
            echo '<pre>';
            print_r($output1);
            print_r($result1);
            echo '</pre>';
            exec('git add .',$output2, $result2);
            echo '<pre>';
            print_r($output2);
            print_r($result2);
            echo '</pre>';
            exec('git commit -m "Added new feature to improve performance"',$output3,$result3);
            echo '<pre>';
            print_r($output3);
            print_r($result3);
            echo '</pre>';
            exec('git push origin maste',$output4,$result4);
            echo '<pre>';
            print_r($output4);
            print_r($result4);
            echo '</pre>';

}






?>
