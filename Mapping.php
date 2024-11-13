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
<label for="product_type">Product Type:</label>
    <select name="product_type" id="product_type">
        <option value="">Select Product Type</option>
        <?php foreach ($productTypes as $productType): ?>
            <option value="<?php echo htmlspecialchars($productType); ?>" <?php if ($productType === $selectedProductType) echo 'selected'; ?>>
                <?php echo htmlspecialchars($productType); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>
    <label for="frame_code">Frame Code:</label>
    <select name="frame_code" id="frame_code">
        <option value="">Select Frame Code</option>
        <?php foreach ($frameCodes as $frameCode): ?>
            <option value="<?php echo htmlspecialchars($frameCode); ?>" <?php if ($frameCode === $selectedFrameCode) echo 'selected'; ?>>
                <?php echo htmlspecialchars($frameCode); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <button type="submit">Submit</button>
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
                            <input type="text" name="data[<?php echo $index; ?>][sku]" value="<?php echo htmlspecialchars($row['sku']); ?>" readonly>
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
                            <input type="text" name="data[<?php echo $index; ?>][frame_file_name]" value="<?php echo htmlspecialchars($row['frame_file_name']); ?>">
                        </td>
                    </tr>
                    <?php foreach ($row as $key => $value): ?>
                        <?php if (!in_array($key, ['id', 'frame_code', 'product_type', 'frame_file_name'])): ?>
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
    <div style="display:flex;">
        <form action="Mapping.php" method="get" style="margin-right:20px;">
            <input type="hidden" name="success" value="1">
            <button type="submit" id="convert-to-json">JSON Data</button>
        </form>
        <form action="Mapping.php" method="get">
        <input type="hidden" name="success" value="2" style="margin-right:20px;">
        <button type="submit" id="send-to-github">Post Data</button>
        </form>

        <form action="Mapping.php" method="get">
        <input type="hidden" name="success" value="3">
        <button type="submit" id="send-to-github">Demo Action</button>
        </form>
    </div>
    <?php if (isset($_GET['success']) && $_GET['success'] == '1'){
        $file = fopen("data.json", "w");
        fwrite($file, $json);
        fclose($file);
        header('Location: data.json'); 
      }
    ?>

<?php
if (isset($_GET['success']) && $_GET['success'] == '2') {

    // Write the data to data.json
    $file = fopen("data.json", "w");
    fwrite($file, $json);
    fclose($file);

    $output = null;
    $retval = null;

    // Check if it is a Git repository
    exec('git status 2>&1', $output, $retval);

    if (strpos(implode("\n", $output), 'Not a git repository') !== false) {
        // Initialize Git if not already a repository
        exec('git init 2>&1', $output, $retval);
        if ($retval !== 0) {
            echo "Error initializing git repository";
            exit;
        }
    }

    // Check if remote origin is set
    exec('git remote -v 2>&1', $output, $retval);
    if (strpos(implode("\n", $output), 'origin') === false) {
        // Add remote origin with the correct URL
        exec('git remote add origin https://github.com/PratikWaghere-Solace/product-posting.git 2>&1', $output, $retval);
        if ($retval !== 0) {
            echo "Error adding remote origin.";
            echo '<br><pre>';
            print_r($output);
            echo '</pre>';
            exit;
        }
    }

    // Stash any local uncommitted changes to ensure a clean pull
    exec('git stash 2>&1', $output, $retval);
    if ($retval !== 0 && strpos(implode("\n", $output), 'No local changes to save') === false) {
        echo "Error stashing changes.";
        echo '<br><pre>';
        print_r($output);
        echo '</pre>';
        exit;
    }

    // Pull the latest changes from the remote repository
    exec('git pull --rebase origin main 2>&1', $output, $retval);
    if ($retval !== 0) {
        echo "Error pulling latest changes from remote repository.";
        echo '<br><pre>';
        print_r($output);
        echo '</pre>';
        // Apply stashed changes back in case of error
        exec('git stash pop 2>&1', $output);
        exit;
    }

    // Apply stashed changes after a successful pull
    exec('git stash pop 2>&1', $output, $retval);
    if ($retval !== 0 && strpos(implode("\n", $output), 'No stash entries found') === false) {
        echo "Error applying stashed changes.";
        echo '<br><pre>';
        print_r($output);
        echo '</pre>';
        exit;
    }

    // Stage all changes, including new, modified, and deleted files
    exec('git add -A 2>&1', $output, $retval);
    if ($retval !== 0) {
        echo "Error adding files to git repo.";
        echo '<br><pre>';
        print_r($output);
        echo '</pre>';
        exit;
    }

    // Commit the changes
    exec('git commit -m "Automated commit of all changes" 2>&1', $output, $retval);
    if ($retval !== 0 && strpos(implode("\n", $output), 'nothing to commit') === false) {
        echo "Error committing changes.";
        echo '<br><pre>';
        print_r($output);
        echo '</pre>';
        exit;
    }

    // Push changes to the remote repository
    exec('git push -u origin main 2>&1', $pushOutput, $pushRetval);

    // Output result of the push
    if ($pushRetval === 0) {
        echo "All changes successfully pushed to the remote repository.";
    } else {
        echo "Error pushing to the remote repository.";
        echo '<br><pre>';
        print_r($pushOutput);
        echo '</pre>';
    }
}

 ?>

 <?php
 if (isset($_GET['success']) && $_GET['success'] == '3') {
    $file = fopen("data.json", "w");
    fwrite($file, $json);
    fclose($file);

    header('Location: product_type.php');

 }
  
 ?>

<?php endif; ?>

</body>
</html>
