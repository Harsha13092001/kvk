<?php
include_once 'database/config.php';
// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $imageId = $_GET['id'];

    // Delete the image from the database
    $deleteSql = "DELETE FROM image_table WHERE id = $imageId";

    if (mysqli_query($conn, $deleteSql)) {
        // Image deleted successfully
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();     }
         else {
        // Error in deletion
        echo "Error: " . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>
