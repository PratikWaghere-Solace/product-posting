<?php
header("Content-Type: application/json; charset=UTF-8");
require "./db.php";
require "./common_function.php";


        // $sql = "SELECT * FROM product_variations";
        // $stmt = $pdo1->query($sql);
        // $size_data = $stmt->fetchAll();
        
        // // echo '<pre>';
        // // print_r($size_data);
        // // echo '</pre>';
        // $size_data = json_encode($size_data);

        // $file = fopen("allData.json", "w");

        // fwrite($file, $size_data);
        // fclose($file);

// Function to get data from the `website` table where `in_use` is 1
// function getWebsite($pdo) {
//     $stmt = $pdo->prepare("SELECT * FROM `website` WHERE in_use = 1");
//     $stmt->execute();
//     $stmt->setFetchMode(PDO::FETCH_ASSOC);
//     return $stmt->fetchAll();
// }

// Function to store data into the `website` table
function storeWebsiteData($pdo1, $data) {
    $insertStmt = $pdo1->prepare("INSERT INTO website (
        in_use, invoice_link, company_name, company_logo, company_phone, company_shipfrom_company_name
    ) VALUES (
        :in_use, :invoice_link, :company_name, :company_logo, :company_phone, :company_shipfrom_company_name
    )");

    foreach ($data as $website) {
        $insertStmt->execute([
            ':in_use' => $website['in_use'],
            ':invoice_link' => $website['invoice_link'],
            ':company_name' => $website['company_name'],
            ':company_logo' => $website['company_logo'],
            ':company_phone' => $website['company_phone'],
            ':company_shipfrom_company_name' => $website['company_shipfrom_company_name']
        ]);
    }
}

// Assuming $pdo is your PDO database connection
try {
    // Retrieve data from the website table
    $websites = getWebsite($pdo);

    // Store the retrieved data back into the website table
    storeWebsiteData($pdo1, $websites);

    echo "Data successfully stored in the website table.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?> 