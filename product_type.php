<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Listing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .product-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 650px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            /* align-items: center; */
            text-align: center;
        }
        .product-card img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .product-card h2 {
            font-size: 18px;
            color: #333;
            margin: 10px 0;
        }
        .product-card p {
            font-size: 14px;
            color: #555;

        }
        .product-card .price {
            font-size: 16px;
            font-weight: bold;
            color: #e63946;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .product-images {
            display: flex;
            flex-direction: row;
            width: 200px;
            gap: 10px;
            justify-content: space-between;
            /* justify-content: first baseline; */
        }
        .product-images img {
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    // Load JSON data from file
    $data = file_get_contents("data.json");
    $products = json_decode($data, true);

    // Loop through products and display each one
    foreach ($products as $product) {
        echo '<div class="product-card">';
        
        // Frame Image
        echo '<div class="product-images">';
        // echo '<img src="' . htmlspecialchars($product['frame_file_name'] , ENT_QUOTES, 'UTF-8') . '" alt="Frame Image">';
        
        // Glass Image
        echo '<img src="https://www.arttoframe.com/' . htmlspecialchars($product['glass_image'] , ENT_QUOTES, 'UTF-8') . '" alt="Glass Image">';
        
        // Hardware Image
        echo '<img src="' . htmlspecialchars($product['hardware_image'] , ENT_QUOTES, 'UTF-8') . '" alt="Hardware Image">';
        
        // Backing Image
        echo '<img src="' . htmlspecialchars($product['backing_image'] , ENT_QUOTES, 'UTF-8') . '" alt="Backing Image">';

        echo '</div>';

        // Product details
        echo '<h2>' . htmlspecialchars($product['frame_name'], ENT_QUOTES, 'UTF-8') . '</h2>';
        echo '<p>SKU: ' . htmlspecialchars($product['sku'], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p>Type: ' . htmlspecialchars($product['product_type'], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p>Glass: ' . htmlspecialchars($product['glass_name'], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p>Backing: ' . htmlspecialchars($product['backing_name'], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p>Hardware: ' . htmlspecialchars($product['hardware_name'], ENT_QUOTES, 'UTF-8') . '</p>';
        
        echo '<div class="price">Price: $100</div>'; // Placeholder price
        echo '<a href="#" class="btn">Buy Now</a>';
        echo '</div>';
    }
    ?>
</div>

</body>
</html>
