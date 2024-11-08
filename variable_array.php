<?php
require "./db.php";
require "./common_function.php";

// Fetch required data from the database
$frame_data = getFrameData($pdo);
$glass_data = getGlassData($pdo);
$backing_data = getBackingData($pdo);
$hardware_data = getHardwareData($pdo);
$codeData = getFrameCodeData($pdo);
$sizeData = getSizeData($pdo);
$skuData = generateSKU($pdo);

// Initialize arrays to store product and frame information
$product = [];
$frame_info = []; 

// Process SKU data and retrieve associated frame and product information
foreach ($skuData as $skuD) {
    $skuArray = explode('-', $skuD); // Split SKU string to get frame and product type
    $prod_type = $skuArray[0];

    // Skip if product type already exists
    // if (isset($product[$prod_type])) {
    //     echo "Warning: Produc t type $prod_type is already in use.\n";
    //     continue;
    // }

    $frame_id = $skuArray[1];
    $frame = getFrameNum($pdo, $frame_id);
    // if (isset($frame_info[$frame_id])) {
    //     echo "Warning: Frame ID $frame_id is already in use.\n";
    //     continue;
    // }

    // Fetch product subtype information
    $sql = "SELECT product_type_name, default_glass_small_id, default_hardware_small_id, default_backing_id 
            FROM product_sub_type 
            WHERE product_type_name = :product_type_name LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':product_type_name', $prod_type, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll();

    if (!empty($result)) {
        $product[$prod_type] = $result;
    } else {
        echo "Warning: No product subtype found for product type: $prod_type\n";
    }

    // echo 'produc data <pre>';
    // print_r($product);
    // echo '</pre>';

    if (!empty($frame)) {
        $frame_info[$frame_id] = $frame;
    } else {
        echo "Warning: No frame data found for frame ID: $frame_id\n";
    }
    // echo 'Frame Info <pre>';
    // print_r($frame_info);
    // echo '</pre>';
}

// Initialize arrays for glass, hardware, and backing information
$glass_info = [];
$hardware_info = [];
$backing_info = [];

// Retrieve associated data for glass, hardware, and backing
foreach ($product as $prodType => $prodGroup) {
    foreach ($prodGroup as $prod) {
        $glass_id = $prod['default_glass_small_id'];
        $hardware_id = $prod['default_hardware_small_id'];
        $backing_id = $prod['default_backing_id'];

        $glass = getGlassNum($pdo, $glass_id);
        $hardware = getHardwareNum($pdo, $hardware_id);
        $backing = getBackingNum($pdo, $backing_id);

        if (!empty($glass)) {
            $glass_info[$prodType][] = $glass;
        } else {
            echo "Warning: No glass data found for glass ID: $glass_id\n";
        }

        if (!empty($hardware)) {
            $hardware_info[$prodType][] = $hardware;
        } else {
            echo "Warning: No hardware data found for hardware ID: $hardware_id\n";
        }

        if (!empty($backing)) {
            $backing_info[$prodType][] = $backing;
        } else {
            echo "Warning: No backing data found for backing ID: $backing_id\n";
        }
    }
}

// Combine frame, glass, hardware, and backing information into a single array if all data is valid
$variable_array = [];
foreach ($product as $prodType => $prodGroup) {
    foreach ($frame_info as $frame_id => $fi) {
        foreach ($glass_info[$prodType] as $gi) {
            foreach ($hardware_info[$prodType] as $hi) {
                foreach ($backing_info[$prodType] as $bi) {
                    // echo $prodType;

                    $variable_array[] = [
                        'sku' => $prodType.$fi['code'] . '-' . $gi['code'] . '-' . $hi['name'] . '-' . $bi['name'],
                        'frame_id' => $fi['id'],
                        'frame_code' => $fi['code'],
                        'frame_name' => $fi['name'],
                        'frame_in_stock' => $fi['in_stock'],
                        'frame_file_name' => $fi['filename'],
                        'glass_id' => $gi['id'],
                        'glass_code' => $gi['code'],
                        'glass_name' => $gi['name'],
                        'glass_in_stock' => $gi['in_stock'],
                        'glass_image' => $gi['image'],
                        'hardware_id' => $hi['id'],
                        'hardware_desciption' => $hi['desciption'],
                        'hardware_name' => $hi['name'],
                        'hardware_in_stock' => $hi['in_stock'],
                        'hardware_image' => $hi['image'],
                        'backing_id' => $bi['id'],
                        'backing_name' => $bi['name'],
                        'backing_in_stock' => $bi['in_stock'],
                        'backing_image' => $bi['image'],
                        'product_type' => $prodType,
                    ];
                }
            }
        }
    }
}

// echo 'Variable array';
// echo '<pre>';
// print_r($variable_array);
// echo '</pre>';

// Insert data into the database
$insert_sql = "INSERT INTO product_variations (sku, frame_id, frame_code, frame_name, frame_in_stock, frame_file_name, glass_id, glass_code, glass_name, glass_in_stock, glass_image, 
                 hardware_id, hardware_desciption, hardware_name, hardware_in_stock, hardware_image, 
                 backing_id, backing_name, backing_in_stock, backing_image, product_type)
               VALUES 
                (:sku, :frame_id, :frame_code, :frame_name, :frame_in_stock, :frame_file_name, 
                 :glass_id, :glass_code, :glass_name, :glass_in_stock, :glass_image, 
                 :hardware_id, :hardware_desciption, :hardware_name, :hardware_in_stock, :hardware_image, 
                 :backing_id, :backing_name, :backing_in_stock, :backing_image, :product_type)
               ON DUPLICATE KEY UPDATE sku=VALUES(sku)";

$pdo1->beginTransaction();

try {
    $stmt = $pdo1->prepare($insert_sql);

    foreach ($variable_array as $variation) {
        echo "variation is exuted <pre>";
        print_r($variation);
        echo '</pre>';
        $stmt->execute($variation);
    }

    $pdo1->commit();
    echo "All product variations have been stored successfully.";
} catch (Exception $e) {
    $pdo1->rollBack();
    echo "Failed to store product variations: " . $e->getMessage();
}                                                                                                                                                                                                                        
?>