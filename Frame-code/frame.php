<?php
   
   require "dB.php";

   switch (true) {
    case isset($_GET['frameName']) && isset($_GET['framePrice']) && !empty($_GET['frameName']) && !empty($_GET['framePrice']):
        setFrameInfo($con);
        break;

    case isset($_GET['mateName']) && isset($_GET['matePrice']) && !empty($_GET['mateName']) && !empty($_GET['matePrice']):
        setMateInfo($con);
        break;

    case isset($_GET['size']) && isset($_GET['sizeStock']) && !empty($_GET['size']) && !empty($_GET['sizeStock']):
        sizeInfo($con);
        break;

    case isset($_GET['glassName']) && isset($_GET['glassPrice']) && !empty($_GET['glassName']) && !empty($_GET['glassPrice']):
        setGlassInfo($con);
        break;

    // case isset($_GET['prod']) && !empty($_GET['prod']):
    //     productInfo($con);
    //     break;

    case isset($_GET['hardwareName']) && isset($_GET['hardwarePrice']) && !empty($_GET['hardwareName']) && !empty($_GET['hardwarePrice']):
        hardwareInfo($con);
        break;
    case isset($_GET['backingName']) && isset($_GET['backingPrice']) && !empty($_GET['backingName']) && !empty($_GET['backingPrice']):
        backingInfo($con);
        break;

    default:
        // echo "Please enter the required fields";
        break;
}

    


function setFrameInfo($con){
    $frameName = $_GET['frameName'];
    $frameStock = $_GET['frameInStock'];
    $framePrice = $_GET['framePrice'];
    $frameMaterials = $_GET['frameMaterials'];
    
    $frameCode = trim(strtoupper(substr($frameName, 0, 3).substr($frameName , -2)) . '-' . strtoupper(substr($frameMaterials, 0, 3).substr($frameMaterials , -2))); 
    $sql =  "INSERT INTO `framecode`.`frame` (`frameName`, `frameCode`, `frameInStock`, `framePrice`, `frameMaterials`) VALUES (? , ?, ?, ?, ? )";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssss", $frameName, $frameCode, $frameStock, $framePrice, $frameMaterials);
    if($stmt->execute()){
     echo "Frame added successfully";
    }
    else{
     echo $con->error. " "." Error adding frame";
     }

    storeFrameCode($con , $frameCode , getSize($con)); 

    $stmt->close();
}
   
function storeFrameCode($con, $frameCode ,$sizeInformation) {

    $regCode = [];

    foreach ($sizeInformation as $size) {
        $sizeValue = $size['size'];
        $code = "REG-" . $frameCode . "-" . $sizeValue;
        array_push($regCode, $code);
    }

    foreach ($regCode as $b) {
        $regularQuery = "INSERT INTO `framecode`.`product_type` (`product_typecol`, `productTypeCode` , `defaultGlass` , `defaultHardware`, `defaultBacking`) VALUES ('Regular Product', ? , '1' , '2' ,'1')";
        $stmt = $con->prepare($regularQuery);
        if ($stmt === false) {
            die('Prepare failed: ' . $con->error);
        }

        $stmt->bind_param("s", $b);
        if ($stmt->execute()) {
            echo "Regular Product Inserted Successfully";
        } else {
            echo "Error Inserting Regular Product: " . $stmt->error;
        }
        $stmt->close();
    }
}




  function setMateInfo($con){
      $mateName = $_GET['mateName'];
      $mateInStock =  $_GET['mateInStock'];
      $matePrice = $_GET['matePrice'];
      $mateMaterials = $_GET['mateMaterials'];
      $mateCode = trim(strtoupper(substr($mateName, 0, 3).substr($mateName , -2)) . '-' . strtoupper(substr($mateMaterials , 0 , 3).substr($mateName ,-2)));
      $sql = "INSERT INTO `framecode`.`mate` (`mateName`, `mateCode`, `mateInStock`, `matePrice`, `mateMaterials`) VALUES (? , ?, ?, ?, ?)";
      $stmt = $con->prepare($sql);
      $stmt->bind_param("sssss" , $mateName , $mateCode ,$mateInStock,$matePrice,$mateMaterials );
      if($stmt->execute()){
          echo "Material added successfully";
          }
          else{
              echo $con->error. " "." Error adding material";
              }
              $stmt->close();

          storeMateCode($con ,$mateCode ,getFrame($con), getSize($con));    


     }

     function storeMateCode($con, $mateCode, $frameData, $sizeData) {
        $mateCodes = [];
    
        foreach ($frameData as $frame) {
            foreach ($sizeData as $size) {
                $sizeValue = $size['size']; 
                $code = "MATE-" . $mateCode . "-" . $frame['frameCode'] . "-" . $sizeValue;
                array_push($mateCodes, $code);
            }
        }
    
        foreach ($mateCodes as $code) {
            $mateQuery = "INSERT INTO `framecode`.`product_type` (`product_typecol`, `productTypeCode` , `defaultGlass` , `defaultHardware`, `defaultBacking`) VALUES( 'Mate Product', ? , '2' , '1' , '2')";
            $stmt = $con->prepare($mateQuery );
    
            if ($stmt === false) {
                die('Prepare failed: ' . $con->error);
            }
    
            $stmt->bind_param("s", $code);
    
            if ($stmt->execute()) {
                echo "<br>Mate Code Inserted Successfully";
            } else {
                echo "Error Inserting Mate Code: " . $stmt->error;
            }
    
            $stmt->close();
        }
    }



    
    function sizeInfo($con){
    $size = $_GET['size'];
    $sizeStock = $_GET['sizeStock'];
    
    $sql = "INSERT INTO `framecode`.`size` (`size`, `sizeStock`) VALUES (?, ?)";
    $stmt = $con->prepare($sql);
    
    
    if ($stmt === false) {
        die('Prepare failed: ' . $con->error);
    }
    
    $stmt->bind_param("ss", $size, $sizeStock);
    
    if($stmt->execute()){
        echo "Size added successfully";
    } else {
        echo "Error adding size: " . $stmt->error;
    }
    
    $stmt->close();
}

// size data
//  frame data


    

     
function setGlassInfo($con){
    $glassName = $_GET['glassName'];
    $glassStock = $_GET['glassInStock'];
    $glassPrice = $_GET['glassPrice'];
    $glassDefaultId = $_GET['defaultId'];
    
    $sql =  "INSERT INTO `framecode`.`glass` (`glassName`, `glassInStock`, `glassPrice` , `defaultId`) VALUES (? , ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssss", $glassName, $glassStock, $glassPrice, $glassDefaultId);
    if($stmt->execute()){
        echo "glass added successfully";
    }
    else{
     echo $con->error. " "." Error adding glass";
    }
    
    $stmt->close();
}


function hardwareInfo($con){
    $hardwareName = $_GET['hardwareName'];
    $hardwareStock = $_GET['hardwareInStock'];
    $hardwarePrice = $_GET['hardwarePrice'];
    $hardwareDefaultId = $_GET['defaultId']; 
    
    $sql = "INSERT INTO `framecode`.`hardware` (`hardwareName`, `hardwareInStock`, `hardwarePrice`, `defaultId`) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    
    if (!$stmt) {
        echo "SQL prepare failed: " . $con->error;
        return;
    }
    
    $stmt->bind_param("ssss", $hardwareName, $hardwareStock, $hardwarePrice, $hardwareDefaultId);
    
    if($stmt->execute()){
        echo "Hardware added successfully";
    } else {
        echo "Error adding hardware: " . $con->error;
    }
    
    $stmt->close();
}

 function backingInfo($con){
     $backingName = $_GET['backingName'];
     $backingStock = $_GET['backingInStock'];
     $backingPrice = $_GET['backingPrice'];
     $backingDefaultId = $_GET['defaultId']; 
     
     $sql = "INSERT INTO `framecode`.`backing` (`backingName`, `backingInStock`, `backingPrice`, `defaultId`) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    
    if (!$stmt) {
        echo "SQL prepare failed: " . $con->error;
        return;
    }
    
    $stmt->bind_param("ssss", $backingName, $backingStock, $backingPrice, $backingDefaultId);
    
    if($stmt->execute()){
        echo "backing added successfully";
    } else {
        echo "Error adding backing: " . $con->error;
    }
    
    $stmt->close();
    
}

function getFrame($con){
    $frameQuery = "SELECT * FROM frame";
    $FrameResult = $con->query($frameQuery);
    
    if($FrameResult === false){
        echo "Error fetching data: " . $con->error;
        return; 
    }
    $frameData = [];
    
    if ($FrameResult->num_rows > 0) {
        while ($row = $FrameResult->fetch_assoc()) {
            $frameData[] = $row;
        }
    }

    
    return $frameData;
}

function getSize($con){ 
   $sizeQuery = "SELECT * FROM size";
   $SizeResult = $con->query($sizeQuery);

   if($SizeResult === false){
    echo "Error fetching data: " . $con->error;
    return; 
   }
  $sizeData = [];

  if($SizeResult->num_rows > 0){
    while ($row = $SizeResult->fetch_assoc()) {
        $sizeData[] = $row;
    }
  }

   return $sizeData;
}

function getGlass($con){
    $glassQuery =  "SELECT * FROM glass";
    $glassResult = $con->query($glassQuery);

          if($glassResult === false){
          echo "Error fetching data: " . $con->error;
        return; 
   }
     $glassData = [];

      if($glassResult->num_rows > 0){
     while ($row = $glassResult->fetch_assoc()) {
        $glassData[] = $row;
    } 
    }
   return $glassData;
}


function getHardware($con){

    $hardwareQuery =  "SELECT * FROM hardware";
    $hardwareResult = $con->query($hardwareQuery);

     if($hardwareResult === false){
         echo "Error fetching data: " . $con->error;
       return; 
    }
    $hardwareData = [];

   if($hardwareResult->num_rows > 0){
        while ($row = $hardwareResult->fetch_assoc()) {
           $hardwareData[] = $row;
      }
  }
   return $hardwareData;

}



function getBacking($con){

    $backingQuery =  "SELECT * FROM backing";
    $backingResult = $con->query($backingQuery);

    if($backingResult === false){
    echo "Error fetching data: " . $con->error;
    return; 
    }
    $backingData = [];

     if($backingResult->num_rows > 0){
     while ($row = $backingResult->fetch_assoc()) {
        $backingData[] = $row;
    }
    }
    return $backingData;
}


function getMats($con){
    $mateQuery = "SELECT * FROM mate";
    $mateResult = $con->query($mateQuery);
    if($mateResult === false){
        echo "Error fetching data: " . $con->error;
        return; 
    }
    $mateData = [];
    if ($mateResult->num_rows > 0) {
        while ($row = $mateResult->fetch_assoc()) {
            $mateData[] = $row;
        }
    }
    return $mateData;
}
   
   
 function getProductType($con){
    $product_typeQuery = "SELECT * FROM product_type";
    $product_typeResult = $con->query($product_typeQuery);
    if($product_typeResult === false){
        echo "Error fetching data: " . $con->error;
        return; 
    }
    $product_typeData = [];
    if ($product_typeResult->num_rows > 0) {
        while ($row = $product_typeResult->fetch_assoc()) {
            $product_typeData[] = $row;
        }
    }
    return $product_typeData;
 }



       






