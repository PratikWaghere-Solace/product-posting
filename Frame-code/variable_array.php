<?php

require "dB.php";
require "frame.php";

$regularProducts = [];
$mateProducts = [];

storeFrameInfoInArray($con, $regularProducts);
storeMateInfoInArray($con, $mateProducts);


function storeFrameInfoInArray($con, &$regularProducts) {
    $frames = getFrame($con);
    $sizes = getSize($con);
    $glasses = getGlass($con);
    $hardwares = getHardware($con);
    $backings = getBacking($con);
    $products = getProductType($con);

    $srno = 0;
    foreach($frames as $frame) {
        foreach($sizes as $size) {
            foreach($products as $pro) {

                if($frame['frameInStock'] == '1') {
                    $final_stock = 1;
                } else {
                    $final_stock = 0;
                }

                if($pro['product_typecol'] == 'Regular Product') {
                    $sku = "REG-" . $frame['frameCode'] . "-" . $size['image_size'];

                    // $skuExists = false;
                    // foreach ($regularProducts as $existingProduct) {
                    //     if ($existingProduct['SKU'] === $sku) {
                    //         $skuExists = true;
                    //         break; 
                    //     }
                    // }

                    // if (!$skuExists) {
                        $product = [
                            'SKU' =>  $sku,
                            'product_Type_Name' => 'Regular Product',
                            'Product_Type' => '1',
                            'frame_Name' => $frame['frameName'],
                            'frame_Code' => $frame['frameCode'],
                            'frame_In_Stock' => $frame['frameInStock'],
                            'size' => $size['size'],
                            'image_size' => $size['image_size'],
                            'image_height' => $size['image_height'],
                            'image_width' => $size['image_width'],
                            'inside_size' => $size['inside_size'],
                            'inside_height' => $size['inside_height'],
                            'inside_width' => $size['inside_width'],
                            'frame_size' => $size['frame_size'],
                            'frame_height' => $size['frame_height'],
                            'frame_width' => $size['frame_width'],
                            'outer_size' => $size['outer_size'],
                            'outer_height' => $size['outer_height'],
                            'outer_width' => $size['outer_width'],
                            'glass_id' => findIdGlass($glasses, $pro['defaultGlass']),
                            'glass_name' => findDefault($glasses, $pro['defaultGlass']),
                            'hardware_id' => findIdHardware($hardwares,  $pro['defaultHardware']),
                            'hardware_name' => findDefault($hardwares, $pro['defaultHardware']),
                            'backing_id' => findIdBacking($backings, $pro['defaultBacking']),
                            'backing_name' => findDefault($backings, $pro['defaultBacking']),
                            'Final_Stock' => $final_stock,
                        ];
                        
                        $regularProducts[] = $product;
                        // }
                }
            }
        }   
    }
    return $regularProducts;
}



function storeMateInfoInArray($con, &$mateProducts) {
    $mates = getMats($con);
    $frames = getFrame($con);
    $sizes = getSize($con);
    $glasses = getGlass($con);
    $hardwares = getHardware($con);
    $backings = getBacking($con);
    $products = getProductType($con);

    foreach ($mates as $mate) {
        foreach ($frames as $frame) {
            foreach ($sizes as $size) {
                foreach($products as $pro) {
                    if($mate['mateInStock'] == '1' && $frame['frameInStock'] == '1') {
                        $final_stock = 1;
                    } else {
                        $final_stock = 0;
                    }

                    if($pro['product_typecol'] == 'Mate Product') {
                        $sku = "MATE-" . $frame['frameCode'] . "-" . $mate['mateCode'] . "-" . $size['mate_spacing'];

                        // Check if SKU already exists in the $mateProducts array
                        $skuExists = false;
                        foreach ($mateProducts as $existingProduct) {
                            if ($existingProduct['SKU'] === $sku) {
                                $skuExists = true;
                                break; // Exit the loop once a match is found
                            }
                        }

                        // Add product only if SKU is unique
                        if (!$skuExists) {
                            $product = [
                                "SKU" => $sku,
                                'productType' => 'Mate Product',
                                'product_type' => '2',
                                'mat_Name' => $mate['mateName'],
                                'mat_Code' => $mate['mateCode'],
                                'Mate_In_Stock' => $mate['mateInStock'],
                                'Frame_Name' => $frame['frameName'],
                                'Frame_Code' => $frame['frameCode'],
                                'Frame_In_stock' => $frame['frameInStock'],
                                'size' => $size['size'],
                                'image_size' => $size['image_size'],
                                'image_height' => $size['image_height'],
                                'image_width' => $size['image_width'],
                                'inside_size' => $size['inside_size'],
                                'inside_height' => $size['inside_height'],
                                'inside_width' => $size['inside_width'],
                                'frame_size' => $size['frame_size'],
                                'frame_height' => $size['frame_height'],
                                'frame_width' => $size['frame_width'],
                                'outer_size' => $size['outer_size'],
                                'outer_height' => $size['outer_height'],
                                'outer_width' => $size['outer_width'],
                                'Mate_Spacing' => $size['mate_spacing'],
                                'Mate_spacig_height' => $size['mate_spacing_height'],
                                'Mate_spacing_width' => $size['mate_spacing_width'],
                                'glass_id' => findIdGlass($glasses , $pro['defaultGlass']),
                                'glass_name' => findDefault($glasses, $pro['defaultGlass']),
                                'hardware_id' => findIdHardware($hardwares, $pro['defaultHardware']),
                                'hardware_name' => findDefault($hardwares, $pro['defaultHardware']),
                                'backing_id' => findIdBacking($backings, $pro['defaultBacking']),
                                'backing_name' => findDefault($backings, $pro['defaultBacking']),
                                'final_stock' => $final_stock,
                            ];

                            $mateProducts[] = $product;
                        }
                    }
                }
            }
        }
    }
    return $mateProducts;
}

function findDefault($data, $defaultId) {
    foreach ($data as $item) {
        if ($item['defaultId'] == $defaultId) {
            return $item['name']; 
        }
    }
    return null;
}

function findIdGlass($data, $defaultId) {
    foreach ($data as $item) {
        if ($item['defaultId'] == $defaultId) {
            return $item['idglass']; 
        }
    }
    return null;
}

function findIdhardware($data, $defaultId) {
    foreach ($data as $item) {
        if ($item['defaultId'] == $defaultId) {
            return $item['idhardware']; 
        }
    }
    return null;
}

function findIdbacking($data, $defaultId) {
    foreach ($data as $item) {
        if ($item['defaultId'] == $defaultId) {
            return $item['idBacking']; 
        }
    }
    return null;
}

