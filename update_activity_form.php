<?php
include_once 'database/config.php';

if (!isset($_SESSION['kvk_id'])) {
    header("Location: index");
    exit;
}

// Make sure to start the session
$id = isset($_GET['id']) ? sanitize_input($_GET['id']) : null;

// Check if 'id' is valid before proceeding.
if ($id === null || !is_numeric($id)) {
    echo "<script>alert('Invalid ID.');</script>";
    // You might want to handle the error differently (e.g., redirect to an error page).
    exit;
}

// Define the sanitize_input function
function sanitize_input($input) {
    return filter_var($input, FILTER_SANITIZE_STRING);
}

if (isset($_POST['update'])) {
    $activity_title = sanitize_input($_POST["activity_title"]);
    $target_no = sanitize_input($_POST["Target_No"]);
    $target_no_of_trials = sanitize_input($_POST["Target_No_of_Trials"]);
    $completed_no = sanitize_input($_POST["Completed_Nos"]);
    $completed_no_of_trials = sanitize_input($_POST["Completed_No_of_trials"]);
    $ongoing_no = sanitize_input($_POST["Ongoing_No"]);
    $ongoing_no_of_trials = sanitize_input($_POST["Ongoing_No_of_Trials"]);
    $yet_to_start_no = sanitize_input($_POST["Yet_to_start_No"]);
    $remarks = sanitize_input($_POST["Remarks"]);
    $timestamp = date("Y-m-d H:i:s");
    $note = sanitize_input($_POST["note"]);

    // Update the activity details in the database
    $stmt = $conn->prepare("UPDATE `activity_form` SET `activity_title`=?, `Target_No`=?, `Target_No_of_Trials`=?, `Completed_Nos`=?, `Completed_No_of_trials`=?, `Ongoing_No`=?, `Ongoing_No_of_Trials`=?, `Yet_to_start_No`=?, `Remarks`=?, `note`=?, `updated_on`=? WHERE `id`=?");
    $stmt->bind_param("ssssssssssss", $activity_title, $target_no, $target_no_of_trials, $completed_no, $completed_no_of_trials, $ongoing_no, $ongoing_no_of_trials, $yet_to_start_no, $remarks, $note, $timestamp, $id);

    if ($stmt->execute()) {
        // Handle file uploads
        $targetDir = "uploads/";

        if (!empty($_FILES["image_upload"]["name"])) {
            // Initialize an array to store uploaded file paths
            $uploadedFiles = array();

            foreach ($_FILES["image_upload"]["tmp_name"] as $key => $tmp_name) {
                $targetFile = $targetDir . time() . rand(10000, 99999) . basename($_FILES["image_upload"]["name"][$key]);
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                // Validate file type and size
                if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                    echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
                    continue; // Skip this file and proceed to the next
                }

                if ($_FILES["image_upload"]["size"][$key] > 2000000) {
                    echo "<script>alert('Sorry, your file is too large. It should be no larger than 2MB.');</script>";
                    continue; // Skip this file and proceed to the next
                }

                if (move_uploaded_file($tmp_name, $targetFile)) {
                    // Store the uploaded file path in the array
                    $uploadedFiles[] = $targetFile;
                } else {
                    echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
                    continue; // Skip this file and proceed to the next
                }
            }

            // Insert into image_table for each uploaded image
            foreach ($uploadedFiles as $file) {
                $insertSql = "INSERT INTO image_table (form_id, image) VALUES (?, ?)";
                $imageStmt = $conn->prepare($insertSql);
                $imageStmt->bind_param("ss", $id, $file);
                $imageStmt->execute();
                $imageStmt->close();
            }
        }
              

        header('Location: view_activity_form'); 
    } else {
        echo "<script>alert('Error updating activity details');</script>";
    }

    // Close the prepared statement
    $stmt->close();
}

// Retrieve the existing data for the specified ID
$query = mysqli_query($conn, "SELECT * FROM `activity_form` WHERE `id`='$id'");
$row = mysqli_fetch_array($query);

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
    <title><?php echo $title ?></title>
    <!-- Custom CSS -->
    
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="header-fix fix-sidebar">
<?php
    $query=mysqli_query($conn,"select * from activity_form where `id`='$id'")or die(mysqli_error($conn));
    $row=mysqli_fetch_array($query);
    ?>
    <!-- Main wrapper  -->
    <div id="main-wrapper">
        <!-- header header  -->
        <?php include("includes/header.php");?>
        <!-- End header header -->
        <!-- Left Sidebar  -->
        <?php include("includes/sidebar.php");?>
        <!-- End Left Sidebar  -->
        <!-- Page wrapper  -->
        <div class="page-wrapper">
            <!-- Bread crumb -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">KVK</h3> </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">kvk details</a></li>
                        <li class="breadcrumb-item active">view kvk</li>
                    </ol>
                </div>
            </div>
            <!-- End Bread crumb -->
            <!-- Container fluid  -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-validation">
                                    <form class="form-valide" action="" method="post" enctype="multipart/form-data">                                                     
                                    <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="val-username"> Activity Title: <span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" value="<?php echo $row['activity_title']?>" name="activity_title" >
                                            </div>
                                        </div>
                                    <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="val-username">Target No: <span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control"  value="<?php echo $row['Target_No']?>" name="Target_No">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="val-email">Target_No_of_Trials:<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control"  name="Target_No_of_Trials"  value="<?php echo $row['Target_No_of_Trials']?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="val-password">Completed_Nos: <span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="Completed_Nos" value="<?php echo $row['Completed_Nos']?>" >
                                            </div>
                                        </div>
                                      
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="address">Completed_No_of_trials: <span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control"  name="Completed_No_of_trials" value="<?php echo $row['Completed_No_of_trials']?>" >
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="address">Ongoing_No: <span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control"  name="Ongoing_No"  value="<?php echo $row['Ongoing_No']?>" placeholder="Your district..">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="address">Ongoing_No_of_Trials: <span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control"  name="Ongoing_No_of_Trials" value="<?php echo $row['Ongoing_No_of_Trials']?>" >
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="mobile">Yet_to_start_No:<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" value="<?php echo $row['Yet_to_start_No']?>" name="Yet_to_start_No"  >
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="image_upload">Upload Image:</label>
                                            <div class="col-lg-6">
                                                <input type="file" class="form-control-file"  name="image_upload[]" accept="image/*" multiple>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="text">Note:</label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control-file" value="<?php echo $row['note']?>" name="note" >
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label"  for="dropdownInput">Remarks:<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                            <select id="dropdownInput" name="Remarks" required>
                                                    <option value="<?php echo $row['Remarks'] ?>"><?php echo $row['Remarks'] ?></option>
                                                    <option value="in_progress">In Progress</option>
                                                    <option value="pending">Pending</option>
                                                    <option value="completed">Done</option>
                                        
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" name="update" value="Update Activity" class="btn btn-primary">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End PAge Content -->
            </div>
            <!-- End Container fluid  -->
            <!-- footer -->
            <?php include('includes/footer.php');?>
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


    <!-- Form validation -->
    <script src="js/lib/form-validation/jquery.validate.min.js"></script>
    <script src="js/lib/form-validation/jquery.validate-init.js"></script>
    <!--Custom JavaScript -->
    <script src="js/custom.min.js"></script>

</body>

</html>