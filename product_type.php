<?php
session_start();

$dataArray = ['key1' => 'value1', 'key2' => 'value2'];
$_SESSION['data'] = $dataArray;

header('Location: destination.php'); 
exit;
?>