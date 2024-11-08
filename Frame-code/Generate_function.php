<?php


   require "dB.php";
   require_once __DIR__ . './variable_array.php';
//    include './Common_Function.php';
//    include './variable_array_version_2.php';
   
   class Generate_function{
   
       private $con;
   
       public function __construct($con) {
           $this->con = $con;
       }
   
       public function getRegularProducts() {
           $regularProducts = [];
           storeFrameInfoInArray($this->con, $regularProducts);

           if (isset($_GET['show']) && $_GET['show'] == '2') {
            return json_encode($regularProducts);
        } elseif (isset($_GET['show']) && $_GET['show'] == '4') {
           return $regularProducts;
        
        } elseif (!isset($_GET['show'])) {
            return 'Script is done..!!!';
        }
        else{
            return ;
        }
       }
   
       public function getMateProducts() {
           $mateProducts = [];
           storeMateInfoInArray($this->con, $mateProducts);
           if (isset($_GET['show']) && $_GET['show'] == '2') {
            return json_encode($mateProducts);
        } elseif (isset($_GET['show']) && $_GET['show'] == '4') {
           return $mateProducts;
        
        } elseif (!isset($_GET['show']) && !empty($_GET['show'])) {
            return 'Script is done..!!!';
        }
        else{
            return ;
        }
       }
   }
   