<?php
require "./db.php"; // Include database connection

$product_type_name = 'WOM'; // Change dynamically
$frame_code = '002700_duplicate'; // Change dynamically

// Prepare SQL query to fetch data from `product_variations` table
$sql = "SELECT * FROM product_variations WHERE frame_code = :frame_code AND sku LIKE CONCAT(:product_type_name, '%')";
$stmt = $pdo1->prepare($sql);
$stmt->bindParam(':product_type_name', $product_type_name, PDO::PARAM_STR);
$stmt->bindParam(':frame_code', $frame_code, PDO::PARAM_STR);
$stmt->execute();

// Fetch results
$product_variations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Variations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            th {
                text-align: left;
            }
            tr {
                margin-bottom: 15px;
            }
            td {
                display: flex;
                justify-content: space-between;
                padding: 10px;
                border-top: 1px solid #ddd;
                position: relative;
                padding-left: 50%;
                text-align: right;
            }
            td:before {
                position: absolute;
                top: 10px;
                left: 10px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>

<h2>Product Variations</h2>

<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Frame ID</th>
            <th>Frame Code</th>
            <th>Frame Name</th>
            <th>Glass Name</th>
            <th>Hardware Name</th>
            <th>Backing Name</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($product_variations as $variation): ?>
            <tr>
                <td><?php echo htmlspecialchars($variation['sku']); ?></td>
                <td><?php echo htmlspecialchars($variation['frame_id']); ?></td>
                <td><?php echo htmlspecialchars($variation['frame_code']); ?></td>
                <td><?php echo htmlspecialchars($variation['frame_name']); ?></td>
                <td><?php echo htmlspecialchars($variation['glass_name']); ?></td>
                <td><?php echo htmlspecialchars($variation['hardware_name']); ?></td>
                <td><?php echo htmlspecialchars($variation['backing_name']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>



</body>
</html>
