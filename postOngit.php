<?php
require './db.php';
require './common_function.php';
session_start();

// Initialize filters with default values if not set
$selectedFrameCode = $_POST['frame_code'] ;
$selectedProductType = $_POST['product_type'] ;
$selectedWebsiteType = $_POST['company_name'] ;

// Fetch unique frame_code, product_type, and company_name options from the database
$frameCodes = $pdo1->query("SELECT DISTINCT frame_code FROM product_variations")->fetchAll(PDO::FETCH_COLUMN);
$productTypes = $pdo1->query("SELECT DISTINCT product_type FROM product_variations")->fetchAll(PDO::FETCH_COLUMN);
$websiteData = $pdo1->query("SELECT DISTINCT company_name FROM website")->fetchAll(PDO::FETCH_COLUMN);

// Fetch website details based on selected website type
$websiteArray = [];
if ($selectedWebsiteType) {
    $sql2 = "SELECT * FROM website WHERE company_name = :company_name";
    $stmt2 = $pdo1->prepare($sql2);
    $stmt2->bindParam(':company_name', $selectedWebsiteType);
    if ($stmt2->execute()) {
        $websiteArray = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Error fetching website data.";
    }
}

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
        th, td {
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
    <label for="website_type">Website:</label>
    <select name="company_name" id="website_type">
        <option value="">Select Website</option>
        <?php foreach ($websiteData as $wd): ?>
            <option value="<?= htmlspecialchars($wd); ?>" <?= $wd === $selectedWebsiteType ? 'selected' : ''; ?>>
                <?= htmlspecialchars($wd); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="product_type">Product Type:</label>
    <select name="product_type" id="product_type">
        <option value="">Select Product Type</option>
        <?php foreach ($productTypes as $productType): ?>
            <option value="<?= htmlspecialchars($productType); ?>" <?= $productType === $selectedProductType ? 'selected' : ''; ?>>
                <?= htmlspecialchars($productType); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="frame_code">Frame Code:</label>
    <select name="frame_code" id="frame_code">
        <option value="">Select Frame Code</option>
        <?php foreach ($frameCodes as $frameCode): ?>
            <option value="<?= htmlspecialchars($frameCode); ?>" <?= $frameCode === $selectedFrameCode ? 'selected' : ''; ?>>
                <?= htmlspecialchars($frameCode); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>
    <button type="submit">Submit</button>
</form>

<!-- Editable Table (Only shows when data is available) -->
<?php if (count($size_data) > 0): ?>
    <form action="update_product_demo.php" method="post">
        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($size_data as $index => $row): ?>
                    <?php foreach ($row as $key => $value): ?>
                        <tr>
                            <td class="field"><?= htmlspecialchars($key); ?></td>
                            <td><input type="text" name="data[<?= $index; ?>][<?= $key; ?>]" value="<?= htmlspecialchars($value); ?>" <?= $key === 'id' ? 'readonly' : ''; ?>></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
            <thead>
                <tr>
                    <th colspan="2">Child Website Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($websiteArray as $index1 => $row1): ?>
                    <?php foreach ($row1 as $key => $value): ?>
                        <tr>
                            <td class="field"><?= htmlspecialchars($key); ?></td>
                            <td><input type="text" name="websiteData[<?= $index1; ?>][<?= $key; ?>]" value="<?= htmlspecialchars($value); ?>"></td>
                        </tr>
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
    <script>alert('Data updated and reviewed successfully!');</script>
    <?php
    // JSON data setup
    $receivedArray = $_SESSION['data'];
    $json = json_encode($receivedArray);
    ?>
    <div style="display:flex;">
        <form action="postOngit.php" method="get" style="margin-right:20px;">
            <input type="hidden" name="success" value="1">
            <button type="submit">JSON Data</button>
        </form>
        <form action="postOngit.php" method="get">
            <input type="hidden" name="success" value="2">
            <button type="submit">Post Data</button>
        </form>
        <form action="postOngit.php" method="get">
            <input type="hidden" name="success" value="3">
            <button type="submit">Demo Action</button>
        </form>
    </div>
<?php if ($_GET['success'] === '1') {
    $file = fopen("allData.json", "w");
    fwrite($file, $json);
    fclose($file);
    // Remove the header redirect and exit, instead use JavaScript to redirect
    echo '<script>window.location.href = "allData.json";</script>';
} ?>
    <?php if ($_GET['success'] === '2') {
        file_put_contents("data.json", $json);
        exec('git init');
        exec('git remote add origin https://github.com/PratikWaghere-Solace/product-posting.git');
        exec('git pull --rebase origin main');
        exec('git add -A');
        exec('git commit -m "Automated update of data.json"');
        exec('git push -u origin main');
    } ?>
<?php endif; ?>

</body>
</html>
