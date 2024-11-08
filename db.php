<?php
try {
    $dsn = 'mysql:host=146.20.158.93;dbname=arttoframe1';
    $username = 'atfdb_user644';
    $password = 'dood2aiZ1etheer';

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$host = '127.0.0.1';
$dbname = 'product_mapping';
$user = 'root'; 
$pass = '';

try {
    $pdo1 = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


