<?php
session_start();
// Database Connection
include('includes/config.php');

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $image = $_FILES['image']['name'];
    $target_dir = "img/";
    $target_file = $target_dir . basename($image);

    // Check if image file is a valid image type
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $valid_types = array('jpg', 'jpeg', 'png', 'gif','JPG');
    if (in_array($imageFileType, $valid_types)) {
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Insert into database
            $query = "INSERT INTO gallery_images (name, image) VALUES ('$name', '$image')";
            if (mysqli_query($con, $query)) {
                echo "Image uploaded successfully.";
            } else {
                echo "Error: " . mysqli_error($con);
            }
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
    }
}
?>
