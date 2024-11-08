<?php

require "./db.php";



        function getFrameData($pdo){
            $frame = "SELECT * from new_frames where id % 10 = 0 limit 3";
            $stmt = $pdo->query($frame);
            $frame_data = $stmt->fetchAll();
            return $frame_data;
        } 
                                                                                                                                                  
        function getGlassData($pdo){
            $glass = "SELECT * from glaze where id % 10 = 0 limit 3";
            $stmt = $pdo->query($glass);
            $glass_data = $stmt->fetchAll();
            return $glass_data;
        }

        function getBackingData($pdo){
            $backing = "SELECT * from backing where id % 10 = 0 limit 3";
            $stmt = $pdo->query($backing);
            $backing_data = $stmt->fetchAll();
            return $backing_data;
        }                                           

        function getHardwareData($pdo){
            $hardware = "SELECT * from hardware where id % 10 = 0 limit 3";
            $stmt = $pdo->query($hardware);
            $hardware_data = $stmt->fetchAll();
            return $hardware_data;
        }

        function getFrameCodeData($pdo){
            $sub_type = "SELECT * FROM `product_sub_type` Where default_backing_id > 1 AND default_glass_small_id > 1 AND default_hardware_small_id > 1  LIMIT 3  ";
            $stmt = $pdo->query($sub_type);
            $sub_type_data = $stmt->fetchAll();
            return $sub_type_data;
        }

        function getSizeData($pdo){
            $size = "SELECT id , size ,populer FROM `pre_made_size_all` Where id % 2 = 0 AND id > 35 LIMIT 3";
            $stmt = $pdo->query($size);
            $size_data = $stmt->fetchAll();
            return $size_data; 
        }
                                                                                                                                                                                                                                                                                                                                                                     
        function generateSKU($pdo){
            $frame_data = getFrameData($pdo);
            $codeData = getFrameCodeData($pdo);
            $sizeData = getSizeData($pdo);

            $skuData = [];

            $length = count($sizeData);
            $length2 = count($codeData);
            $length3 = count($frame_data);

            // echo "len".$length."<br>";
            // echo "len2" .$length2."<br>";
            // echo "len3".$length3."<br>";


            for($j = 0 ; $j < $length2 ; $j++ ){
                for($i = 0 ; $i < $length3 ; $i++ ){
                    for($k = 0 ; $k < $length ; $k++ ){
                        $sku = $codeData[$j]["product_type_name"]."-".$frame_data[$i]["code"]."-".$sizeData[$k]['size'];
                        $skuData[] = $sku;
                    }
                }  
            }

            return $skuData; 
        }
                                  
                                 
        if(isset($_GET['ch']) && !empty($_GET['ch'])){
            $choice = $_GET['ch'];

        switch ($choice) {
            case 1:
                $frame_data = getFrameData($pdo);
                echo "Frame data <br>";
                echo '<pre>';
                print_r($frame_data);
                echo '</pre>';
                break;
            case 2:

                $glass_data = getGlassData($pdo);
                echo "glass data <br>";
                echo '<pre>';
                print_r($glass_data);
                echo '</pre>';
                break;
            case 3:
                $backing_data = getBackingData($pdo);
                echo "backing data <br>";
                echo '<pre>';
                print_r($backing_data);
                echo '</pre>';
                break;
            case 4:
                $hardware_data = getHardwareData($pdo);
                echo "Hardware data <br>";
                echo '<pre>';
                print_r($hardware_data);
                echo '</pre>';
                break;
            default:
                echo "Invalid choice";
           }

        } 


        $frame_data = getFrameData($pdo);
        $codeData = getFrameCodeData($pdo);


        function getGlassNum($pdo , $glass_id){
            $stmt = $pdo->prepare("SELECT * FROM glaze WHERE id = :id");
            $stmt->execute([':id' => $glass_id]);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $glass_data  = $stmt->fetch();
            return $glass_data;
        }

        function getFrameNum($pdo, $frame_id){
            // echo $frame_id . '<br>';
            $stmt = $pdo->prepare("SELECT * FROM new_frames WHERE code = :id");
            $stmt->execute([':id' => $frame_id]);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result_frame = $stmt->fetch();
            return $result_frame;
        }

        function getBackingNum($pdo ,$backing_id){
            $stmt = $pdo->prepare("SELECT * FROM backing WHERE id = :id");
            $stmt->execute([':id' => $backing_id]);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $backing_data = $stmt->fetch();
            return $backing_data;
        }                                           
                                                
        function getHardwareNum($pdo ,$hardware_id){
            $stmt = $pdo->prepare("SELECT * FROM hardware WHERE id = :id");
            $stmt->execute([':id' => $hardware_id]);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $hardware_data = $stmt->fetch();

            return $hardware_data;
        }
                                                     



// However, the error you're experiencing is likely due to the fact that you're not closing your database connections or freeing up resources after use. This can cause your script to timeout.

// To fix this, you should close your database connections and free up resources after use. Here's an example of how you can do this:

// ```php
// function getFrameNum($pdo, $frame_id){
//     $stmt = $pdo->prepare("SELECT * FROM new_frames WHERE code = :id");
//     $stmt->execute([':id' => $frame_id]);
//     $stmt->setFetchMode(PDO::FETCH_ASSOC);
//     $result_frame = $stmt->fetch();
//     $stmt = null; // Close the statement
//     return $result_frame;
// }
// ```

// Also, make sure to close your database connection when you're done with it:

// ```php
// $pdo = null; // Close the database connection
// ```

// You can also increase the maximum execution time by using the `set_time_limit` function:

// ```php
set_time_limit(60); // Increase the maximum execution time to 60 seconds
// ```

// However, this is not recommended as it can cause your script to run indefinitely if there's an issue. It's better to optimize your code to run within the default time limit.

?>