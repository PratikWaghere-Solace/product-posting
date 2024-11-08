<?php
require 'dB.php';
require_once  __DIR__ .'./Generate_function.php';
require_once __DIR__ . './Common_Function.php';
require_once __DIR__ . './Base_price.php';

 $Generate_function = new Generate_function($con);
 $regularProductsJson = $Generate_function->getRegularProducts();
 $mateProductsJson = $Generate_function->getMateProducts();
 $common_fun = new Common_Function($con);
 $base_prize = new Base_price($con);

if (isset($_GET['sku']) && !empty($_GET['sku']) && isset($_GET['price'])) {
    $price = $_GET['price'];
    $sku = $_GET['sku'];
    if ($price == 1) {
        $regularProductsPrice = $base_prize->get_price($con, $sku);
        echo "<pre>";
        print_r($regularProductsPrice);
        echo "</pre>";
    } 
}
 elseif(isset($_GET['sku']) &&  !empty($_GET['sku'])){
    $sku = $_GET['sku'];
     $generate_common_fuction = $common_fun->getCommonDetails($con ,$sku);
 }


  if(isset($_GET['product_type']) && !empty($_GET['product_type'])){
    $product_type = $_GET['product_type'];
      if($product_type == '1' && isset($_GET['show']) == '4'){
        echo "<pre>";
          print_r($regularProductsJson);
        echo "</pre>";  
      }
      elseif($product_type == '1'){
        
        echo $regularProductsJson;
        
      }
      elseif($product_type == '2' && isset($_GET['show']) == '4'){
        echo "<pre>";
        print_r($mateProductsJson);
        echo "</pre>";
      }
      elseif($product_type == '2'){
          echo $mateProductsJson;
      }
      elseif(isset($_GET['sku']) && !empty($_GET['sku'])){
        echo "<pre>";
        print_r($generate_common_fuction);
        echo "</pre>";
      }
      else{
      echo "Invalid Product Type....!!!!!";
      }
  }
 
 



