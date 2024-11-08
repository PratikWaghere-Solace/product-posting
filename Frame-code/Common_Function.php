<?php
   require_once 'dB.php';
   require_once __DIR__ . '/variable_array.php';
//    require_once './variable_array.php';

   error_reporting(E_ALL);
   ini_set('display_errors', 1);

   class Common_Function{
    private $con;
   
    public function __construct($con) {
        $this->con = $con;
    }

                                                                                                                                                                             

    function getCommonDetails($con ,$sku){
        $pattern = "/[-\s]/";
        $sku = preg_split($pattern, $sku);
        var_dump($sku[2]);

        if ($sku[0] == 'REG') {
            $sqlFrame = "SELECT frameName, frameCode, frameInStock FROM `frame` WHERE `frameCode` = ?";
            $stmtFrame = $con->prepare($sqlFrame);
            $stmtFrame->bind_param("s", $sku[1]); 
            $stmtFrame->execute();
            $resultFrame = $stmtFrame->get_result();

            // print_r($resultFrame);
        
            $sqlSize = "SELECT image_size, image_height, image_width, bredth FROM `size` WHERE `image_size` = ?";
            $stmtSize = $con->prepare($sqlSize);
            $stmtSize->bind_param("s", $sku[2]);
            $stmtSize->execute();
            $resultSize = $stmtSize->get_result();
        
            // echo $resultSize;
            $getDefault = "SELECT defaultGlass, defaultHardware, defaultBacking FROM `product_type` WHERE `product_typecol` = 'Regular Product' LIMIT 1";
            $resultDefault = $con->query($getDefault);
        
            $frameData = [];
            if ($resultFrame->num_rows > 0) {
                while ($rowFrame = $resultFrame->fetch_assoc()) {
                    $frameData[] = $rowFrame;
                }
            }else{
                echo 'frame code is invalid';
                exit;
            }
        
            $sizeData = [];
            if ($resultSize->num_rows > 0) {
                while ($rowSize = $resultSize->fetch_assoc()) {
                    $sizeData[] = $rowSize;
                }
            } else{
                echo 'size code is invalid';
                exit;
            }
        

            $defaultValue = [];
            if ($resultDefault->num_rows > 0) {
                $defaultValue = $resultDefault->fetch_assoc();
            }
        
            
            $defaultAcc = [];
if (!empty($defaultValue)) {
            $defaultGlass = "SELECT idglass, name FROM `glass` WHERE `idglass` = ?";
            $stmtDefaultGlass = $con->prepare($defaultGlass);
            $stmtDefaultGlass->bind_param("i", $defaultValue['defaultGlass']);
            $stmtDefaultGlass->execute();
            $resultDefaultGlass = $stmtDefaultGlass->get_result();
            $glassData = $resultDefaultGlass->fetch_assoc();
            $glassName = $glassData['name'] ?? '';
            $glassId = $glassData['idglass'] ?? '';


            $defaultHardware = "SELECT idhardware, name FROM `hardware` WHERE `idhardware` = ?";
            $stmtDefaultHardware = $con->prepare($defaultHardware);
            $stmtDefaultHardware->bind_param("i", $defaultValue['defaultHardware']);
            $stmtDefaultHardware->execute();
            $resultDefaultHardware = $stmtDefaultHardware->get_result();
            $hardwareData = $resultDefaultHardware->fetch_assoc();
            $hardwareName = $hardwareData['name'] ?? '';
            $hardwareId = $hardwareData['idhardware'] ?? '';


            $defaultBacking = "SELECT idBacking, name FROM `backing` WHERE `idBacking` = ?";
            $stmtDefaultBacking = $con->prepare($defaultBacking);
            $stmtDefaultBacking->bind_param("i", $defaultValue['defaultBacking']);
            $stmtDefaultBacking->execute();
            $resultDefaultBacking = $stmtDefaultBacking->get_result();
            $backingData = $resultDefaultBacking->fetch_assoc();
            $backingName = $backingData['name'] ?? '';
            $backingId = $backingData['idBacking'] ?? '';


            $defaultAcc = [
                'glass_id' => $glassId,
                'glass_Name' => $glassName,
                'hardware_id' => $hardwareId,
                'hardware_Name' => $hardwareName,
                'backing_id' => $backingId,
                'backing_Name' => $backingName
            ];
}

        
        
            $regularCommonDetails = [
                'SKU' => $_GET['sku'],
                'product_Type_Name' => 'Regular Product',
                'product_Type' => '1'
            ];
        
            
            if (!empty($frameData)) {
                foreach ($frameData as $frame) {
                    $regularCommonDetails['frame_Name'] = $frame['frameName'];
                    $regularCommonDetails['frame_code'] = $frame['frameCode'];
                    $regularCommonDetails['In_stock'] = $frame['frameInStock'];
                }
            }
        
            
            if (!empty($sizeData)) {
                foreach ($sizeData as $size) {
                    $regularCommonDetails['image_size'] = $size['image_size'];
                    $regularCommonDetails['image_height'] = $size['image_height'];
                    $regularCommonDetails['image_width'] = $size['image_width'];

                    $image_height = $size['image_height'] + $size['bredth'] * 2;
                    $image_width = $size['image_width'] + $size['bredth'] * 2;

                    $regularCommonDetails['inside_height'] = $size['image_height'];
                    $regularCommonDetails['inside_width'] = $size['image_width'];

                    $regularCommonDetails['inside_size'] = $size['image_width'] ."x" . $size['image_height'];
                    
                    $regularCommonDetails['frame_height'] = $image_height;
                    $regularCommonDetails['frame_width'] = $image_width;
                    $regularCommonDetails['frame_size'] = $image_width .'x' . $image_height ;
                    $regularCommonDetails['outer_height'] = $image_height;
                    $regularCommonDetails['outer_width'] = $image_width;
                    $regularCommonDetails['outer_size'] = $image_width .'x'.$image_height;
                }
            }

        
            
            $regularCommonDetails = array_merge($regularCommonDetails, $defaultAcc);
        
        
            echo '<pre>';
            print_r($regularCommonDetails);
            echo '</pre>';
        }
        elseif ($sku[0] == 'MATE') {
            $sqlMate = "SELECT mateName, mateCode, mateInStock FROM `mate` WHERE `mateCode` = ?";
            $stmtMate = $con->prepare($sqlMate);
            $stmtMate->bind_param("s", $sku[2]); 
            $stmtMate->execute();
            $resultMate = $stmtMate->get_result();


            $sqlFrame = "SELECT frameName, frameCode, frameInStock FROM `frame` WHERE `frameCode` = ?";
            $stmtFrame = $con->prepare($sqlFrame);
            $stmtFrame->bind_param("s", $sku[1]); 
            $stmtFrame->execute();
            $resultFrame = $stmtFrame->get_result();
        
            $sqlSize = "SELECT image_size, image_height, image_width, bredth , mate_spacing_height , mate_spacing_width FROM `size` WHERE `mate_spacing` = ?";
            $stmtSize = $con->prepare($sqlSize);
            $stmtSize->bind_param("s", $sku[3]);
            $stmtSize->execute();
            $resultSize = $stmtSize->get_result();
        
            $getDefault = "SELECT defaultGlass, defaultHardware, defaultBacking FROM `product_type` WHERE `product_typecol` = 'Mate Product' LIMIT 1";
            $resultDefault = $con->query($getDefault);

            $mateData = [];
            if ($resultMate->num_rows > 0) {
                while ($rowMate = $resultMate->fetch_assoc()) {
                    $mateData[] = $rowMate;
                }
            }
            else{
                echo 'mate code is invalid';
                exit;
            }
        
            $frameData = [];
            if ($resultFrame->num_rows > 0) {
                while ($rowFrame = $resultFrame->fetch_assoc()) {
                    $frameData[] = $rowFrame;
                }
            }
            else{
                echo 'frame code is invalid';
                exit;
            }
        
            $sizeData = [];
            if ($resultSize->num_rows > 0) {
                while ($rowSize = $resultSize->fetch_assoc()) {
                    $sizeData[] = $rowSize;
                }
            }
            else{
                echo 'size code is invalid';
            }
        

            $defaultValue = [];
            if ($resultDefault->num_rows > 0) {
                $defaultValue = $resultDefault->fetch_assoc();
            }
        
            $defaultAcc = [];
if (!empty($defaultValue)) {
            $defaultGlass = "SELECT idglass, name FROM `glass` WHERE `idglass` = ?";
            $stmtDefaultGlass = $con->prepare($defaultGlass);
            $stmtDefaultGlass->bind_param("i", $defaultValue['defaultGlass']);
            $stmtDefaultGlass->execute();
            $resultDefaultGlass = $stmtDefaultGlass->get_result();
            $glassData = $resultDefaultGlass->fetch_assoc();
            $glassName = $glassData['name'] ?? '';
            $glassId = $glassData['idglass'] ?? '';


            $defaultHardware = "SELECT idhardware, name FROM `hardware` WHERE `idhardware` = ?";
            $stmtDefaultHardware = $con->prepare($defaultHardware);
            $stmtDefaultHardware->bind_param("i", $defaultValue['defaultHardware']);
            $stmtDefaultHardware->execute();
            $resultDefaultHardware = $stmtDefaultHardware->get_result();
            $hardwareData = $resultDefaultHardware->fetch_assoc();
            $hardwareName = $hardwareData['name'] ?? '';
            $hardwareId = $hardwareData['idhardware'] ?? '';


            $defaultBacking = "SELECT idBacking, name FROM `backing` WHERE `idBacking` = ?";
            $stmtDefaultBacking = $con->prepare($defaultBacking);
            $stmtDefaultBacking->bind_param("i", $defaultValue['defaultBacking']);
            $stmtDefaultBacking->execute();
            $resultDefaultBacking = $stmtDefaultBacking->get_result();
            $backingData = $resultDefaultBacking->fetch_assoc();
            $backingName = $backingData['name'] ?? '';
            $backingId = $backingData['idBacking'] ?? '';


            $defaultAcc = [
                'glass_id' => $glassId,
                'glass_Name' => $glassName,
                'hardware_id' => $hardwareId,
                'hardware_Name' => $hardwareName,
                'backing_id' => $backingId,
                'backing_Name' => $backingName
            ];
}

        
        
            $regularCommonDetails = [
                'SKU' => $_GET['sku'],
                'product_Type_Name' => 'Mate Product',
                'product_Type' => '2'
            ];
        
            if (!empty($mateData)) {
                foreach ($mateData as $mate) {
                    $regularCommonDetails['mate_Name'] = $mate['mateName'];
                    $regularCommonDetails['mate_code'] = $mate['mateCode'];
                    $regularCommonDetails['Mate_In_stock'] = $mate['mateInStock'];
                }
            }
        
            
            if (!empty($frameData)) {
                foreach ($frameData as $frame) {
                    $regularCommonDetails['frame_Name'] = $frame['frameName'];
                    $regularCommonDetails['frame_code'] = $frame['frameCode'];
                    $regularCommonDetails['In_stock'] = $frame['frameInStock'];
                }
            }
        
            
            if (!empty($sizeData)) {
                foreach ($sizeData as $size) {
                    $regularCommonDetails['image_size'] = $size['image_size'];
                    $regularCommonDetails['image_height'] = $size['image_height'];
                    $regularCommonDetails['image_width'] = $size['image_width'];
                    $regularCommonDetails['mate_spacing_size'] = $size['mate_spacing_height'].'x'.$size['mate_spacing_width'];
                    $regularCommonDetails['mate_spacing_height'] = $size['mate_spacing_height'];
                    $regularCommonDetails['mate_spacing_width'] = $size['mate_spacing_width'];

                    $inside_height = $size['image_height']+ ($size['mate_spacing_height']*2 );
                    $inside_width = $size['image_width']+ ($size['mate_spacing_width']*2 );

                    $frame_height = $inside_height + ($size['bredth'] *2);
                    $frame_width = $inside_width + ($size['bredth'] *2);
                    
                    $regularCommonDetails['inside_height'] = $inside_height;
                    $regularCommonDetails['inside_width'] = $inside_width;
                    $regularCommonDetails['inside_size'] = $inside_height .'x'. $inside_width;
                    $regularCommonDetails['frame_size'] = $frame_width .'x'. $frame_height ;

                    $regularCommonDetails['frame_height'] = $frame_height;
                    $regularCommonDetails['frame_width'] =  $frame_width;

                    $regularCommonDetails['outer_size'] = $frame_width .'x'. $frame_height ;
                    $regularCommonDetails['outer_height'] = $frame_height;
                    $regularCommonDetails['outer_width'] = $frame_width ;

                    $regularCommonDetails['mate_spacing'] = $size['mate_spacing_width'] .'x'. $size['mate_spacing_height'];
                    
                }
            }
        
            
            $regularCommonDetails = array_merge($regularCommonDetails, $defaultAcc);
        
        
            echo '<pre>';
            print_r($regularCommonDetails);
            echo '</pre>';

        
        } else {
            echo "Invalid SKU please check";
        }
    }
}


   

?>