<?php
session_start();

if (isset($_SESSION['data'])) {
    $receivedArray = $_SESSION['data'];
    print_r($receivedArray); // Output the array
}