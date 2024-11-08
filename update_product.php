<?php
// Include your database connection file
include './db.php'; // Assumes you have a file that sets up the $pdo1 PDO connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data'])) {
    // Retrieve the submitted data from the form
    $data = $_POST['data'];

    // Prepare an update query with placeholders for each field
    $sql = "UPDATE product_variations SET 
                sku = :sku,
                frame_id = :frame_id,
                frame_code = :frame_code,
                frame_name = :frame_name,
                frame_in_stock = :frame_in_stock,
                frame_file_name = :frame_file_name,
                glass_id = :glass_id,
                glass_code = :glass_code,
                glass_name = :glass_name,
                glass_in_stock = :glass_in_stock,
                glass_image = :glass_image,
                hardware_id = :hardware_id,
                hardware_desciption = :hardware_desciption,
                hardware_name = :hardware_name,
                hardware_in_stock = :hardware_in_stock,
                hardware_image = :hardware_image,
                backing_id = :backing_id,
                backing_name = :backing_name,
                backing_in_stock = :backing_in_stock,
                backing_image = :backing_image,
                product_type = :product_type
            WHERE id = :id";

    // Prepare the SQL statement once outside the loop for efficiency
    $stmt = $pdo1->prepare($sql);

    // Iterate through each row of submitted data
    foreach ($data as $row) {
        // Bind values for each parameter from the row
        $stmt->bindValue(':id', $row['id'], PDO::PARAM_INT);
        $stmt->bindValue(':sku', $row['sku']);
        $stmt->bindValue(':frame_id', $row['frame_id']);
        $stmt->bindValue(':frame_code', $row['frame_code']);
        $stmt->bindValue(':frame_name', $row['frame_name']);
        $stmt->bindValue(':frame_in_stock', $row['frame_in_stock'], PDO::PARAM_INT);
        $stmt->bindValue(':frame_file_name', $row['frame_file_name']);
        $stmt->bindValue(':glass_id', $row['glass_id']);
        $stmt->bindValue(':glass_code', $row['glass_code']);
        $stmt->bindValue(':glass_name', $row['glass_name']);
        $stmt->bindValue(':glass_in_stock', $row['glass_in_stock'], PDO::PARAM_INT);
        $stmt->bindValue(':glass_image', $row['glass_image']);
        $stmt->bindValue(':hardware_id', $row['hardware_id']);
        $stmt->bindValue(':hardware_desciption', $row['hardware_desciption']);
        $stmt->bindValue(':hardware_name', $row['hardware_name']);
        $stmt->bindValue(':hardware_in_stock', $row['hardware_in_stock'], PDO::PARAM_INT);
        $stmt->bindValue(':hardware_image', $row['hardware_image']);
        $stmt->bindValue(':backing_id', $row['backing_id']);
        $stmt->bindValue(':backing_name', $row['backing_name']);
        $stmt->bindValue(':backing_in_stock', $row['backing_in_stock'], PDO::PARAM_INT);
        $stmt->bindValue(':backing_image', $row['backing_image']);
        $stmt->bindValue(':product_type', $row['product_type']);

        // Execute the update query for each row
        $stmt->execute();
    }

    // Redirect back to the editable table page or show a success message
// Redirect back to the editable table page or show a success message
        if ($stmt->execute()) {

            session_start();
            $dataArray = $data;
            // print_r($dataArray);
            $_SESSION['data'] = $dataArray;
            
            header('Location: Mapping.php?success');
            exit;
        } else {
            echo "Error updating data.";
        }
}

//   

?>
