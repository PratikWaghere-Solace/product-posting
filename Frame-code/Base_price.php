<?php
require_once 'dB.php';
require_once __DIR__ . './variable_array.php';
require_once __DIR__ . './frame.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

class Base_price {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function get_price($con, $sku){
        $pattern = "/[-\s]/";
        $sku = preg_split($pattern, $sku);

        if($sku[0] == 'REG') {
            $sqlFrame = "SELECT frameName, frameCode, costPerSquareFoot FROM `frame` WHERE `frameCode` = ?";
            $stmtFrame = $con->prepare($sqlFrame);
            $stmtFrame->bind_param("s", $sku[1]); 
            $stmtFrame->execute();
            $resultFrame = $stmtFrame->get_result();
            $rowFrame = $resultFrame->fetch_assoc();
            $framePrice = $rowFrame["costPerSquareFoot"];

            $sqlSize = "SELECT image_size, image_height, image_width, bredth FROM `size` WHERE `image_size` = ?";
            $stmtSize = $con->prepare($sqlSize);
            $stmtSize->bind_param("s", $sku[2]);
            $stmtSize->execute();
            $resultSize = $stmtSize->get_result();
            $rowSize = $resultSize->fetch_assoc();
            $image_width = $rowSize['image_width'];
            $image_height = $rowSize['image_height']; 
            $frame_bredth = $rowSize['bredth'];

            $getDefault = "SELECT defaultGlass, defaultHardware, defaultBacking, Production_cost, labour_cost, Profit_Percentage FROM `product_type` WHERE `product_typecol` = 'Regular Product' LIMIT 1";
            $resultDefault = $con->query($getDefault);
            $rowDefault = $resultDefault->fetch_assoc();
            $production_cost = $rowDefault["Production_cost"];
            $labour_cost = $rowDefault['labour_cost'];
            $profit_percentage = $rowDefault['Profit_Percentage'];

            if ($resultDefault->num_rows > 0) {
                $defaultGlass = "SELECT idglass, name, costPerSquareFoot FROM `glass` WHERE `idglass` = ?";
                $stmtDefaultGlass = $con->prepare($defaultGlass);
                $stmtDefaultGlass->bind_param("i", $rowDefault['defaultGlass']);
                $stmtDefaultGlass->execute();
                $resultDefaultGlass = $stmtDefaultGlass->get_result();
                $glassData = $resultDefaultGlass->fetch_assoc();
                $glassPrice = $glassData['costPerSquareFoot'] ?? 0;

            
                $defaultHardware = "SELECT idhardware, name, cost FROM `hardware` WHERE `idhardware` = ?";
                $stmtDefaultHardware = $con->prepare($defaultHardware);
                $stmtDefaultHardware->bind_param("i", $rowDefault['defaultHardware']);
                $stmtDefaultHardware->execute();
                $resultDefaultHardware = $stmtDefaultHardware->get_result();
                $hardwareData = $resultDefaultHardware->fetch_assoc();
                $hardwarePrice = $hardwareData['cost'] ?? 0;

                
                $defaultBacking = "SELECT idBacking, name, costSquareFoot FROM `backing` WHERE `idBacking` = ?";
                $stmtDefaultBacking = $con->prepare($defaultBacking);
                $stmtDefaultBacking->bind_param("i", $rowDefault['defaultBacking']);
                $stmtDefaultBacking->execute();
                $resultDefaultBacking = $stmtDefaultBacking->get_result();
                $backingData = $resultDefaultBacking->fetch_assoc();
                $backingPrice = $backingData['costSquareFoot'] ?? 0;
            }

        
            $frame_price = ((($image_height + ($frame_bredth * 2)) * ($image_width + ($frame_bredth * 2))) / 144) * $framePrice;
            $glass_price = ((($image_height + (2 * 0.25)) * ($image_width + (2 * 0.25))) / 144) * $glassPrice;
            $backing_price = ((($image_height + ($frame_bredth * 2)) * ($image_width + ($frame_bredth * 2))) / 144) * $backingPrice;
            $hardware_price = $hardwarePrice;

        
            $total_price = $frame_price + $glass_price + $backing_price + $hardware_price + $production_cost + $labour_cost + $profit_percentage;

            $sku_id = $_GET['sku'];

    
            $defaultAcc = [
                'SKU' => $sku_id,
                'Image_Size'  => $image_width ."x".$image_height,
                'Frame_Size' => ($image_height + ($frame_bredth * 2)) .'x'. ($image_width + ($frame_bredth * 2)),
                'Frame_Cost_per_square' => "$".$framePrice,
                'Frame_Price' => "$". round($frame_price),
                'Glass_size' =>  ($image_width + (2 * 0.25)) .'x'.  ($image_height + (2 * 0.25)) ,
                'Glass_Cost_per_square' =>"$". $glassPrice,
                'Glass_Price' => "$". round($glass_price),
                'Backing_size' => ($image_width + ($frame_bredth * 2)) .'x'. ($image_height + ($frame_bredth * 2)),
                'Backing_Cost_per_square' => "$". $backingPrice,
                'Backing_Price' => "$". round($backing_price),
                'Hardware_Price' => "$". round($hardware_price),
                'Production_Cost' => "$". round($production_cost),
                'Profit' => "$". $profit_percentage,
                'Labour_Cost' => "$". round($labour_cost),
                'Total_Cost' => "$". round($total_price),
            ];

            return $defaultAcc;
        }
        elseif($sku[0] == 'MATE'){

            $sqlFrame = "SELECT frameName, frameCode, costPerSquareFoot FROM `frame` WHERE `frameCode` = ?";
            $stmtFrame = $con->prepare($sqlFrame);
            $stmtFrame->bind_param("s", $sku[1]); 
            $stmtFrame->execute();
            $resultFrame = $stmtFrame->get_result();
            $rowFrame = $resultFrame->fetch_assoc();
            $framePrice = $rowFrame["costPerSquareFoot"];

            $sqlMate = "SELECT mateName, mateCode, costPerSquareFoot  FROM `mate` WHERE `mateCode` = ?";
            $stmtMate = $con->prepare($sqlMate);
            $stmtMate->bind_param("s", $sku[2]); 
            $stmtMate->execute();
            $resultMate = $stmtMate->get_result();
            $rowMate = $resultMate->fetch_assoc();
            $matePrice = $rowMate['costPerSquareFoot'];



            $sqlSize = "SELECT image_size, image_height, image_width, bredth , mate_spacing_height , mate_spacing_width FROM `size` WHERE `mate_spacing` = ?";
            $stmtSize = $con->prepare($sqlSize);
            $stmtSize->bind_param("s", $sku[3]);
            $stmtSize->execute();
            $resultSize = $stmtSize->get_result();
            $rowSize = $resultSize->fetch_assoc();
            $image_width = $rowSize['image_width'];
            $image_height = $rowSize['image_height']; 
            $frame_bredth = $rowSize['bredth'];
            $mate_spacing_height = $rowSize['mate_spacing_height'];
            $mate_spacing_width = $rowSize['mate_spacing_width'];

            $getDefault = "SELECT defaultGlass, defaultHardware, defaultBacking, Production_cost, labour_cost, Profit_Percentage FROM `product_type` WHERE `product_typecol` = 'Mate Product' LIMIT 1";
            $resultDefault = $con->query($getDefault);
            $rowDefault = $resultDefault->fetch_assoc();
            $production_cost = $rowDefault["Production_cost"];
            $labour_cost = $rowDefault['labour_cost'];
            $profit_percentage = $rowDefault['Profit_Percentage'];

            if ($resultDefault->num_rows > 0) {

                $defaultGlass = "SELECT idglass, name, costPerSquareFoot FROM `glass` WHERE `idglass` = ?";
                $stmtDefaultGlass = $con->prepare($defaultGlass);
                $stmtDefaultGlass->bind_param("i", $rowDefault['defaultGlass']);
                $stmtDefaultGlass->execute();
                $resultDefaultGlass = $stmtDefaultGlass->get_result();
                $glassData = $resultDefaultGlass->fetch_assoc();
                $glassPrice = $glassData['costPerSquareFoot'] ?? 0;

            
                $defaultHardware = "SELECT idhardware, name, cost FROM `hardware` WHERE `idhardware` = ?";
                $stmtDefaultHardware = $con->prepare($defaultHardware);
                $stmtDefaultHardware->bind_param("i", $rowDefault['defaultHardware']);
                $stmtDefaultHardware->execute();
                $resultDefaultHardware = $stmtDefaultHardware->get_result();
                $hardwareData = $resultDefaultHardware->fetch_assoc();
                $hardwarePrice = $hardwareData['cost'] ?? 0;

                
                $defaultBacking = "SELECT idBacking, name, costSquareFoot FROM `backing` WHERE `idBacking` = ?";
                $stmtDefaultBacking = $con->prepare($defaultBacking);
                $stmtDefaultBacking->bind_param("i", $rowDefault['defaultBacking']);
                $stmtDefaultBacking->execute();
                $resultDefaultBacking = $stmtDefaultBacking->get_result();
                $backingData = $resultDefaultBacking->fetch_assoc();
                $backingPrice = $backingData['costSquareFoot'] ?? 0;
            }

        
            $frame_price = ((($image_height + ($frame_bredth * 2)) * ($image_width + ($frame_bredth * 2))) / 144) * $framePrice;
            $glass_price = ((($image_height + (2 * 0.25)) * ($image_width + (2 * 0.25))) / 144) * $glassPrice;
            $backing_price = ((($image_height + ($frame_bredth * 2)) * ($image_width + ($frame_bredth * 2))) / 144) * $backingPrice;
            $hardware_price = $hardwarePrice;
            $mate_Price = (((($image_height + ($mate_spacing_height * 2)) * ($image_width + ($mate_spacing_width * 2))- ($image_height * $image_width)))/144) * $matePrice ;
            $total_price = $frame_price + $glass_price + $backing_price + $hardware_price + $production_cost + $labour_cost + $mate_Price;
            $total_price += $total_price +  $profit_percentage ;
            $sku_id = $_GET['sku'];
            
            $defaultAcc = [
                'SKU' => $sku_id,
                'Image_Size'  => $image_width ."x".$image_height,
                'Frame_Size' => ($image_height + ($frame_bredth * 2)) .'x'. ($image_width + ($frame_bredth * 2)),
                'Frame_Cost_per_square' => "$".$framePrice,
                'Frame_Price' => "$". round($frame_price),
                'MATE_Size' => ($image_height + ($mate_spacing_height * 2)) .'x'.($image_width + ($mate_spacing_width * 2)),
                'Mate_Cost_per_square' => "$". round($matePrice), 
                'Mate_Price' => "$" . round($mate_Price),
                'Glass_size' =>  ($image_width + (2 * 0.25)) .'x'.  ($image_height + (2 * 0.25)) ,
                'Glass_Cost_per_square' => $glassPrice,
                'Glass_Price' => "$". round($glass_price),
                'Backing_size' => ($image_width + ($frame_bredth * 2)) .'x'. ($image_height + ($frame_bredth * 2)),
                'Backing_Cost_per_square' => "$". $backingPrice,
                'Backing_Price' => "$". round($backing_price),
                'Hardware_Price' => "$". round($hardware_price),
                'Production_Cost' => "$". round($production_cost),
                'Labour_Cost' => "$". round($labour_cost),
                'Profit' => "$". $profit_percentage,
                'Total_Cost' => "$". round($total_price),
            ];

            return $defaultAcc;


        }
        else{
            echo "Something wents wrong";
        }
    }
}
