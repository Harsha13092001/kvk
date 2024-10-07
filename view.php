<?php
include_once 'database/config.php';
error_reporting(0);
// Function to fetch the kvk_id from the database
function fetchKvkIdFromDatabase()
{
    // Create a database connection
    // Database connection configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "kvk_db";

    // Create a connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check the connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $user_id = $_SESSION['kvk_id'];

    // Define your SQL query to fetch the kvk_name for the logged-in user
    $sql = "SELECT kvk_name FROM kvk WHERE id = $user_id";

    // Execute the query
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result) {
        // Fetch the kvk_id
        $row = $result->fetch_assoc();
        $kvk_id = $row['kvk_name'];

        // Close the database connection
        $conn->close();

        return $kvk_id;
    } else {
        die("Error: " . $conn->error);
    }

}

// Usage of the function to retrieve the kvk_id
$kvk_id = fetchKvkIdFromDatabase();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>
        <?php echo $title ?>
    </title>
    <!-- Custom CSS -->

    <link href="css/style.css" rel="stylesheet">
    <style>
        /* CSS for screen view */
        .delete-button {
            display: inline-block;
            color: blue;
            text-decoration: none;
        }

        /* CSS for print view (hide delete buttons) */
        @media print {
            .delete-button {
                display: none;
            }
        }
    </style>
</head>

<body class="header-fix fix-sidebar">

    <!-- Main wrapper  -->
    <div id="main-wrapper">
        <!-- header header  -->
        <?php include('includes/header.php'); ?>
        <!-- End header header -->
        <!-- Left Sidebar  -->
        <?php include('includes/sidebar.php'); ?>
        <!-- End Left Sidebar  -->
        <!-- Page wrapper  -->
        <div class="page-wrapper">
            <!-- Bread crumb -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">Details</h3>
                </div>

            </div>
            <!-- End Bread crumb -->
            <!-- Container fluid  -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div id="invoice" class="effect2">

                                    <div class="invoice-logo-wrap">


                                        <?php
                                        $act_id = mysqli_query($conn, "SELECT * FROM `activity_form` where `id`='" . $_GET['id'] . "'") or die(mysqli_error($conn));
                                        $activity_data = mysqli_fetch_array($act_id);
                                        $act_nm = mysqli_query($conn, "SELECT * FROM `activites` where `id`='" . $activity_data['activity_id'] . "'") or die(mysqli_error($conn));
                                        $activity_name = mysqli_fetch_array($act_nm);

                                        ?>

                                    </div>


                                    <?php
                                    // Query to fetch data from the activity_form table
                                    $queryActivity = "SELECT * FROM activity_form where `id`='" . $_GET['id'] . "'";

                                    // Execute the queries and fetch data
                                    $resultActivity = mysqli_query($conn, $queryActivity);

                                    // Check if there are any records in the activity_form table
                                    if (mysqli_num_rows($resultActivity) > 0) {

                                        echo '<div id="invoiceContent" style= "color:black">';
                                        // Display activity details
                                        while ($rowActivity = mysqli_fetch_assoc($resultActivity)) {
                                            echo '<h3 style= "color:blue">KVK: ' . $kvk_id . '</h3>';
                                            echo '<h5 style= "color:blue">ACTIVITY NAME: ' . $activity_name['activity_name'] . '</h5>';
                                            echo '<p>Activity Title: ' . $rowActivity['activity_title'] . '</p>';
                                            echo '<p>Target_no: ' . $rowActivity['Target_No'] . '</p>';
                                            echo '<p>Target_No_of_Trials: ' . $rowActivity['Target_No_of_Trials'] . '</p>';
                                            echo '<p>Completed_Nos: ' . $rowActivity['Completed_Nos'] . '</p>';
                                            echo '<p>Completed_No_of_trials: ' . $rowActivity['Completed_No_of_trials'] . '</p>';
                                            echo '<p>Ongoing_No: ' . $rowActivity['Ongoing_No'] . '</p>';
                                            echo '<p>Ongoing_No_of_Trials: ' . $rowActivity['Ongoing_No_of_Trials'] . '</p>';
                                            echo '<p>Yet_to_start_No: ' . $rowActivity['Yet_to_start_No'] . '</p>';
                                            echo '<p>Remarks: ' . $rowActivity['Remarks'] . '</p>';
                                            echo '<p>Note: ' . $rowActivity['note'] . '</p>';

                                            // Fetch all images associated with the current activity ID
                                            $queryImages = mysqli_query($conn, "SELECT * FROM `image_table` WHERE `form_id`='" . $rowActivity['id'] . "'") or die(mysqli_error($conn));

                                            // Check if there are images to display
                                            if (mysqli_num_rows($queryImages) > 0) { ?>

                                                <label for="text" style="color:black">IMAGES:</label>
                                                <?php
                                                // Loop through images and display them horizontally
                                                while ($resultImages = mysqli_fetch_array($queryImages)) {
                                                    echo '<div class= image-container >';
                                                    echo '<img src="' . $resultImages['image'] . '" alt="Invoice Image" width="200" height="100" style="border: 1px solid #000;">';
                                                    // Add a delete button with a unique identifier for each image
                                                    echo '<a href="delete_image.php?id=' . $resultImages['id'] . '" class="delete-button">Delete</a>';

                                                    echo '</div><br>';


                                                }
                                            }
                                        }

                                        // Add a print button
                                        echo '<div class="print-button">';
                                        echo '<button onclick="printInvoice()">Print</button>';
                                        echo '</div>';

                                        // Close the div
                                        echo '</div>';
                                    } else {
                                        // Handle the case where no activity records are found
                                        echo '<p>No activity records found</p>';
                                    }

                                    // Close the database conn
                                    mysqli_close($conn);
                                    ?>
                                    <!-- End of invoice content -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End PAge Content -->
            </div>
            <!-- End Container fluid  -->
            <!-- footer -->
            <?php include('includes/footer.php'); ?>
            <!-- End footer -->
        </div>
        <!-- End Page wrapper  -->
    </div>
    <!-- End Wrapper -->
    <!-- All Jquery -->
    <script src="js/lib/jquery/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="js/jquery.slimscroll.js"></script>
    <!--Menu sidebar -->
    <script src="js/sidebarmenu.js"></script>
    <!--stickey kit -->
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <!--Custom JavaScript -->
    <script src="js/custom.min.js"></script>
    <script>
        function printInvoice() {
            var printContent = document.getElementById("invoiceContent");
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContent.innerHTML;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>
</body>

</html>