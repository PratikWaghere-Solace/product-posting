<?php
require "./db.php";
require "./common_function.php";

// Generate necessary data
$skuData = generateSKU($pdo);
$frame_data = getFrameData($pdo);
$glass_data = getGlassData($pdo);
$backing_data = getBackingData($pdo);
$hardware_data = getHardwareData($pdo);

$frame_info = [];
$product = [];

// Process SKU data and gather product details
foreach ($skuData as $skuD) {
    $skuArray = explode('-', $skuD);
    $frame_id = $skuArray[1];
                                
    // Get frame details
    $frame = getFrameNum($pdo, $frame_id);
    $frame_info = array_merge($frame_info, is_array($frame) ? $frame : []);

    // Get product subtype details
    $sql = "SELECT product_type_name, default_glass_small_id, default_hardware_small_id, default_backing_id 
            FROM product_sub_type 
            WHERE product_type_name = :product_type_name LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':product_type_name', $skuArray[0]);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null; 
    
    // Check if result is an array and merge
    if (is_array($result)) {
        $product[] = $result;
    }
}

// Remove duplicate entries
$product = array_map("unserialize", array_unique(array_map("serialize", $product)));
$frame_info = array_map("unserialize", array_unique(array_map("serialize", $frame_info)));

// Gather additional component information
$glass_info = [];
$hardware_info = [];
$backing_info = [];

foreach ($product as $prod) {
    // Check if keys exist in the array to avoid undefined index errors
    $glass_id = isset($prod['default_glass_small_id']) ? $prod['default_glass_small_id'] : null;
    $hardware_id = isset($prod['default_hardware_small_id']) ? $prod['default_hardware_small_id'] : null;
    $backing_id = isset($prod['default_backing_id']) ? $prod['default_backing_id'] : null;  

    echo $glass_id;
    echo $hardware_id;
    echo $backing_id;

    if ($glass_id) {
        $glass_info = array_merge($glass_info, getGlassNum($pdo, $glass_id) ?: []);
    }
    if ($hardware_id) {
        $hardware_info = array_merge($hardware_info, getHardwareNum($pdo, $hardware_id) ?: []);
    }
    if ($backing_id) {
        $backing_info = array_merge($backing_info, getBackingNum($pdo, $backing_id) ?: []);
    }
}

// Remove duplicate information for each component type
$glass_info = array_map("unserialize", array_unique(array_map("serialize", $glass_info)));
$hardware_info = array_map("unserialize", array_unique(array_map("serialize", $hardware_info)));
$backing_info = array_map("unserialize", array_unique(array_map("serialize", $backing_info)));

// Initialize arrays for batch inserts
$existing_skus = [];
$variable_array = [];

// Generate product variations
// Initialize arrays for batch inserts
$existing_skus = [];
$variable_array = [];


echo '<pre>';
// print_r($skuData);
// print_r($frame_info);
echo '</pre>';
// Generate product variations
foreach ($skuData as $skuD) {
    foreach ($frame_info as $fi) {
        foreach ($glass_info as $gi) {
            foreach ($hardware_info as $hi) {
                foreach ($backing_info as $bi) {

                    echo $skuD;

                    // print_r($skuD);
                    // print_r($fi);
                    // print_r($gi);
                    // print_r($hi);
                    // print_r($bi);


                    $generated_sku = "{$fi['code']}-{$gi['code']}-{$hi['id']}-{$bi['id']}";
                    echo $generated_sku;
                    
                    // Avoid duplicate SKUs
                    if (in_array($generated_sku, $existing_skus)) {
                        continue;
                    }
                    
                    $skuArray = explode('-', $skuD);
                    $product_type = $skuArray[0];
                    $frame_id = $skuArray[1];
                    
                    // Add generated SKU and details to the array
                    $existing_skus[] = $generated_sku;
                    $variable_array[] = [
                        ':sku' => "$frame_id-$generated_sku",
                        ':frame_id' => $fi['id'],
                        ':frame_code' => $fi['code'],
                        ':frame_name' => $fi['name'],
                        ':frame_in_stock' => $fi['in_stock'],
                        ':frame_file_name' => $fi['filename'],
                        ':glass_id' => $gi['id'],
                        ':glass_code' => $gi['code'],
                        ':glass_name' => $gi['name'],
                        ':glass_in_stock' => $gi['in_stock'],
                        ':glass_image' => $gi['image'],
                        ':hardware_id' => $hi['id'],
                        ':hardware_description' => $hi['description'], // Fix typo here
                        ':hardware_name' => $hi['name'],
                        ':hardware_in_stock' => $hi['in_stock'],
                        ':hardware_image' => $hi['image'],
                        ':backing_id' => $bi['id'],
                        ':backing_name' => $bi['name'],
                        ':backing_in_stock' => $bi['in_stock'],
                        ':backing_image' => $bi['image'],
                        ':product_type' => $product_type,
                    ];
                }
            }
        }
    }
}

// Check if $variable_array is empty
if (empty($variable_array)) {
    // echo "The array is empty. Possible reasons: ";
    // echo '<pre>';
    // print_r($skuData);
    // print_r($frame_info);
    // print_r($glass_info);
    // print_r($hardware_info);
    // // echo "1. $skuData is empty. ";
    // // echo "2. $frame_info is empty. ";
    // // echo "3. $glass_info is empty. ";
    // // echo "4. $hardware_info is empty. ";
    // // echo "5. $backing_info is empty. ";
    // echo '</pre>';
    // echo "6. There are no unique SKUs generated. ";
} else {
    echo '<pre>';
    print_r($variable_array);
    echo '</pre>';
}
echo '<pre>';
print_r($variable_array);
echo '</pre>';


// Database insertion with prepared statements and transaction
$insert_sql = "INSERT INTO product_variations 
                (sku, frame_id, frame_code, frame_name, frame_in_stock, frame_file_name, 
                 glass_id, glass_code, glass_name, glass_in_stock, glass_image, 
                 hardware_id, hardware_description, hardware_name, hardware_in_stock, hardware_image, 
                 backing_id, backing_name, backing_in_stock, backing_image, product_type)
               VALUES 
                (:sku, :frame_id, :frame_code, :frame_name, :frame_in_stock, :frame_file_name, 
                 :glass_id, :glass_code, :glass_name, :glass_in_stock, :glass_image, 
                 :hardware_id, :hardware_description, :hardware_name, :hardware_in_stock, :hardware_image, 
                 :backing_id, :backing_name, :backing_in_stock, :backing_image, :product_type)
               ON DUPLICATE KEY UPDATE sku=VALUES(sku)";

$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare($insert_sql);

    
    foreach ($variable_array as $variation) {
        $stmt->execute($variation);
    }
 
    $pdo->commit();
    echo "All product variations have been stored successfully.";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed to store product variations: " . $e->getMessage();
}
?>
