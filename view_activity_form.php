<?php
// Include the configuration file with the database connection
include_once 'database/config.php';
error_reporting(0);
if(!isset($_SESSION['kvk_id']))
{
   header("Location:index");
   
}
function deleteTrainingRecord($conn, $id)
{
    $id = intval($id); // Ensure $id is an integer to prevent SQL injection

    // SQL query to delete the data from the training table
    $sql = "DELETE FROM `activity_form` WHERE `id` = $id";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to the same page after deletion
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Check if the "Delete" button was clicked and an ID is provided
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    deleteTrainingRecord($conn, $_GET["id"]);

}


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
    @media print {
        .no-print {
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
                    <h3 class="text-primary">ACTIVITY FORM</h3>
                </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Activity_form_details</a></li>
                        <li class="breadcrumb-item active">View_activity_form</li>
                    </ol>
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
                                <h4 class="card-title">Data Export</h4>
                                <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>
                                <div class="table-responsive m-t-40">
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                                       
                                        <input type="hidden" name="user" value="<?php echo $_SESSION["kvk_id"]?>">
                                        <label for="from_date">From:</label>
                                        <input type="date" name="from_date" value="<?php echo $fromDate; ?>">

                                        <label for="to_date">To:</label>
                                        <input type="date" name="to_date" value="<?php echo $toDate; ?>">

                                        <button type="submit" name="submit"
                                            class="btn btn-primary waves-effect waves-light">Submit </button>


                                    </form><br>
                                    <table id="example23"
                                        class="display nowrap table table-hover table-striped table-bordered"
                                        cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>kvk Name</th>
                                                <th>Activity Name</th>
                                                <th>Activity Title</th>
                                                <th>Target_No</th>
                                                <th>Target_No_of_Trials</th>
                                                <th>Completed_Nos</th>
                                                <th>Completed_No_of_trials</th>
                                                <th>Ongoing_No</th>
                                                <th>Ongoing_No_of_Trials</th>
                                                <th>Yet_to_start_No</th>
                                                <th>Remarks</th>
                                                <th>Details</th>
                                                
                                                <th class="no-print">Update</th>
                                                <th class="no-print">Delete</th>

                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $user = $_POST['user'];
                                            if (count($_POST) > 0 && $user != '') {

                                                $i = 1;
                                                $fromDate = $_POST['from_date'];
                                                $toDate = $_POST['to_date'];

                                                $query = "SELECT * FROM `activity_form` where `kvk_id`='" . $user . "'AND `created_at` BETWEEN '$fromDate' AND '$toDate'";
                                                $kvkact_details = mysqli_query($conn, $query);
                                                while ($kvk_details = mysqli_fetch_array($kvkact_details)) {

                                                    $kvk = mysqli_query($conn, "SELECT * FROM `kvk` where id='" . $kvk_details['kvk_id'] . "'") or die('error kvk query');
                                                    $kvk_id = mysqli_fetch_array($kvk);

                                                    $activity = mysqli_query($conn, "SELECT * FROM `activites` where id='" . $kvk_details['activity_id'] . "'") or die('error kvk query');
                                                    $activity_id = mysqli_fetch_array($activity);


                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $i++; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_id['kvk_name'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $activity_id['activity_name'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details['activity_title'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details['Target_No'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details['Target_No_of_Trials'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details['Completed_Nos'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details['Completed_No_of_trials'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details['Ongoing_No'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details['Ongoing_No_of_Trials'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details['Yet_to_start_No'] ?>
                                                        </td>
                                                        
                                                        
                                                        <td>
                                                            <?php echo $kvk_details['Remarks'] ?>
                                                        </td>
                                                        <td >
                                                            <?php
                                                            echo "<a href='view?id=" . $kvk_details["id"] . "' class='view-btn'>View</a>"; ?>
                                                        </td>

                                                        <td class="no-print">
                                                            <?php
                                                            echo "<a href='update_activity_form?id=" . $kvk_details["id"] . "' class='update-btn'>Update</a>"; ?>
                                                        </td>
                                                        <td class="no-print">
                                                            <?php echo "<a href='" . $_SERVER['PHP_SELF'] . "?id=" . $kvk_details["id"] . "' class='delete-btn'>Delete</a>"; ?>
                                                        </td>

                                                    </tr>

                                                    <?php
                                                }
                                            }
                                            else
                                            {
                                              $i1 = 1;

                                                $query1 = "SELECT * FROM `activity_form` where `kvk_id`='".$_SESSION['kvk_id']."'";
                                                $kvkact_details1 = mysqli_query($conn, $query1);
                                                while ($kvk_details1 = mysqli_fetch_array($kvkact_details1)) {

                                                    $kvk1 = mysqli_query($conn, "SELECT * FROM `kvk` where id='" . $kvk_details1['kvk_id'] . "'") or die('error kvk query');
                                                    $kvk_id1 = mysqli_fetch_array($kvk1);

                                                    $activity1 = mysqli_query($conn, "SELECT * FROM `activites` where id='" . $kvk_details1['activity_id'] . "'") or die('error kvk query');
                                                    $activity_id1 = mysqli_fetch_array($activity1);


                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $i1++; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_id1['kvk_name'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $activity_id1['activity_name'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details1['activity_title'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details1['Target_No'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details1['Target_No_of_Trials'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details1['Completed_Nos'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details1['Completed_No_of_trials'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details1['Ongoing_No'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details1['Ongoing_No_of_Trials'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $kvk_details1['Yet_to_start_No'] ?>
                                                        </td>
                                                       
                                                        
                                                        <td>
                                                            <?php echo $kvk_details1['Remarks'] ?>
                                                        </td>
                                                        <td >
                                                            <?php
                                                            echo "<a href='view?id=" . $kvk_details1["id"] . "' class='view-btn'>View</a>"; ?>
                                                        </td>

                                                        <td class="no-print">
                                                            <?php
                                                            echo "<a href='update_activity_form?id=" . $kvk_details1["id"] . "' class='update-btn'>Update</a>"; ?>
                                                        </td>
                                                        <td class="no-print">
                                                            <?php echo "<a href='" . $_SERVER['PHP_SELF'] . "?id=" . $kvk_details1["id"] . "' class='delete-btn'>Delete</a>"; ?>
                                                        </td>

                                                    </tr>

                                                    <?php
                                                }   
                                            }

                                            ?>

                                        </tbody>
                                    </table>
                                
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


    <script src="js/lib/datatables/datatables.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="js/lib/datatables/cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src="js/lib/datatables/datatables-init.js"></script>
    
</body>
</body>

</html>