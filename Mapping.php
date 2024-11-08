<?php
require './db.php';
session_start();
// Assuming $pdo1 is your PDO connection

// Initialize filters with empty values if not set
$selectedFrameCode = isset($_POST['frame_code']) ? $_POST['frame_code'] : '';
$selectedProductType = isset($_POST['product_type']) ? $_POST['product_type'] : '';

// Fetch unique frame_code and product_type options from the database
$frameCodes = $pdo1->query("SELECT DISTINCT frame_code FROM product_variations")->fetchAll(PDO::FETCH_COLUMN);
$productTypes = $pdo1->query("SELECT DISTINCT product_type FROM product_variations")->fetchAll(PDO::FETCH_COLUMN);

// Prepare SQL query with filtering conditions
$sql = "SELECT * FROM product_variations WHERE 1=1";
if ($selectedFrameCode) {
    $sql .= " AND frame_code = :frame_code";
}
if ($selectedProductType) {
    $sql .= " AND product_type = :product_type";
}

$stmt = $pdo1->prepare($sql);

// Bind parameters if filters are set
if ($selectedFrameCode) {
    $stmt->bindParam(':frame_code', $selectedFrameCode);
}
if ($selectedProductType) {
    $stmt->bindParam(':product_type', $selectedProductType);
}

// Execute query if filters are applied
$size_data = [];
if ($selectedFrameCode || $selectedProductType) {
    $stmt->execute();
    $size_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Variations</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        input[type="text"], select {
            width: 100%;
            box-sizing: border-box;
        }
        .field {
            width: 200px;
        }
    </style>
</head>
<body>

<h2>Filter and Edit Product Variations</h2>

<!-- Filter Form -->
<form action="" method="post">
    <label for="frame_code">Frame Code:</label>
    <select name="frame_code" id="frame_code">
        <option value="">Select Frame Code</option>
        <?php foreach ($frameCodes as $frameCode): ?>
            <option value="<?php echo htmlspecialchars($frameCode); ?>" <?php if ($frameCode === $selectedFrameCode) echo 'selected'; ?>>
                <?php echo htmlspecialchars($frameCode); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="product_type">Product Type:</label>
    <select name="product_type" id="product_type">
        <option value="">Select Product Type</option>
        <?php foreach ($productTypes as $productType): ?>
            <option value="<?php echo htmlspecialchars($productType); ?>" <?php if ($productType === $selectedProductType) echo 'selected'; ?>>
                <?php echo htmlspecialchars($productType); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Filter</button>
</form>

<!-- Editable Table (Only shows when data is available) -->
<?php if (count($size_data) > 0): ?>
    <form action="update_product.php" method="post">
        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($size_data as $index => $row): ?>
                    <tr>
                        <td class="field">ID</td>
                        <td>
                            <input type="text" name="data[<?php echo $index; ?>][id]" value="<?php echo htmlspecialchars($row['id']); ?>" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td class="field">SKU</td>
                        <td>
                            <input type="text" name="data[<?php echo $index; ?>][sku]" value="<?php echo htmlspecialchars($row['']); ?>" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td class="field">Frame Code</td>
                        <td>
                            <input type="text" name="data[<?php echo $index; ?>][frame_code]" value="<?php echo htmlspecialchars($row['frame_code']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="field">Product Type</td>
                        <td>
                            <input type="text" name="data[<?php echo $index; ?>][product_type]" value="<?php echo htmlspecialchars($row['product_type']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="field">Image</td>
                        <td>
                            <input type="text" name="data[<?php echo $index; ?>][image]" value="<?php echo htmlspecialchars($row['image']); ?>">
                        </td>
                    </tr>
                    <?php foreach ($row as $key => $value): ?>
                        <?php if (!in_array($key, ['id', 'frame_code', 'product_type', 'image'])): ?>
                            <tr>
                                <td class="field"><?php echo htmlspecialchars($key); ?></td>
                                <td>
                                    <input type="text" name="data[<?php echo $index; ?>][<?php echo $key; ?>]" value="<?php echo htmlspecialchars($value); ?>">
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <button type="submit">Save Changes</button>
    </form>
<?php else: ?>
    <p>No products match the selected filters.</p>
<?php endif; ?>


<!-- JSON Data and Post Data Buttons (only displayed on success) -->
<?php if (isset($_GET['success'])): ?>
    <script>
        alert('Data updated and reviewed successfully!');
    </script>
    <?php
    $receivedArray = $_SESSION['data'];
    $json = json_encode($receivedArray);
    // echo $json;
    //    echo 'session is start';
    //    print_r($receivedArray); // Output the array
    
    ?>

    
    <div>
        <form action="Mapping.php" method="get">
            <input type="hidden" name="success" value="1">
            <button type="submit" id="convert-to-json">JSON Data</button>
        </form>
        <form action="Mapping.php" method="get">
        <input type="hidden" name="success" value="2">
        <button type="submit" id="send-to-github">Post Data</button>
        </form>
    </div>
    <?php if (isset($_GET['success']) && $_GET['success'] == '1'){
        $file = fopen("data.json", "w");
        fwrite($file, $json);
        fclose($file);
        header('Location: data.json'); 
      }
    ?>

<?php if (isset($_GET['success']) && $_GET['success'] == '2'){
    
    $file = fopen("data.json", "w");
    fwrite($file, $json);
    fclose($file);
    $output = null;
    $retval = null;
    
    // $file = fopen("data.json", "w");
    // fwrite($file, $json);
    // fclose($file);
    // $output = null;
    // $retval = null;

    $command = 'git status 2>&1';
    exec($command, $output, $retval);
    if (strpos(implode("\n", $output), 'Not a git repository') !== false) {
        $command = 'git init';
        exec($command, $output, $retval);
        if ($retval == 0) {
            $command = 'git add data.json';
            exec($command, $output, $retval);
            if ($retval == 0) {
                $command = 'git commit -m "Automated commit of data.json"';
                exec($command, $output, $retval);
                if ($retval == 0) {
                    $command = 'git remote add origin https://github.com/your-username/your-repo-name.git';
                    exec($command, $output, $retval);
                    if ($retval == 0) {
                        $command = 'git push -u origin master';
                        exec($command, $output, $retval);
                        if ($retval == 0) {
                            echo "Data.json successfully added to git repo and pushed to origin master";
                        } else {
                            echo "Error pushing to origin master";
                            echo '<br>';
                            echo $output;
                        }
                    } else {
                        echo "Error adding remote origin";
                    }
                } else {
                    echo "Error committing data.json";
                }
            } else {
                echo "Error adding data.json to git repo";
            }
        } else {
            echo "Error initializing git repository";
        }
    } else {
        $command = 'git add data.json';
        exec($command, $output, $retval);
        if ($retval == 0) {
            $command = 'git commit -m "Automated commit of data.json"';
            exec($command, $output, $retval);
            if ($retval == 0) {
                $command = 'git push origin main';
                exec($command, $output, $retval);
                if ($retval == 0) {
                    echo "Data.json successfully added to git repo and pushed to origin main";
                } else {
                    echo "Error pushing to origin master";
                    echo '<br>';
                    echo '<pre>';
                    print_r($output);
                    echo '</pre>';

                }
            } else {
                echo "Error committing data.json";
            }
        } else {
            echo "Error adding data.json to git repo";
        }
    }
}
//     $command = 'git add data.json';
//     exec($command, $output, $retval);
//     if ($retval == 0) {
//         $command = 'git commit -m "Automated commit of data.json"';
//         exec($command, $output, $retval);
//         if ($retval == 0) {
//             $command = 'git push origin master';
//             exec($command, $output, $retval);
//             if ($retval == 0) {
//                 echo "Data.json successfully added to git repo and pushed to origin master";
//             } else {
//                 echo "Error pushing to origin master";
//                 echo '<br>';
//                 echo $output;
//             }
//         } else {
//             echo "Error committing data.json";
//         }
//     } else {
//         echo "Error adding data.json to git repo";
//     }
// }

    ?>

<?php endif; ?>

</body>
</html>
