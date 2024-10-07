<?php
// Include the database connection file
include_once 'database/config.php';
if (!isset($_SESSION['kvk_id'])) {
    header("Location:index");

}

function sanitize_input($data)
{
    // Remove all HTML tags from the data
    $data = strip_tags($data);

    // Escape all special characters in the data
    $data = htmlspecialchars($data);

    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and store form data in variables
    $kvk_id = sanitize_input($_SESSION["kvk_id"]);
    $activity_id = sanitize_input($_POST["activity_id"]);
    $activity_title = sanitize_input($_POST["activity_title"]);
    $Target_No = sanitize_input($_POST["Target_No"]);
    $Target_No_of_Trials = sanitize_input($_POST["Target_No_of_Trials"]);
    $Completed_Nos = sanitize_input($_POST["Completed_Nos"]);
    $Completed_No_of_trials = sanitize_input($_POST["Completed_No_of_trials"]);
    $Ongoing_No = sanitize_input($_POST["Ongoing_No"]);
    $Ongoing_No_of_Trials = sanitize_input($_POST["Ongoing_No_of_Trials"]);
    $Yet_to_start_No = sanitize_input($_POST["Yet_to_start_No"]);
    $Remarks = sanitize_input($_POST["Remarks"]);
    $note = sanitize_input($_POST["note"]);

// Prepare and execute the SQL statement to insert data
$stmt = $conn->prepare("INSERT INTO activity_form (`kvk_id`, `activity_id`, `activity_title`, `Target_No`, `Target_No_of_Trials`, `Completed_Nos`, `Completed_No_of_trials`, `Ongoing_No`, `Ongoing_No_of_Trials`, `Yet_to_start_No`, `Remarks`, `note`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssssss", $kvk_id, $activity_id, $activity_title, $Target_No, $Target_No_of_Trials, $Completed_Nos, $Completed_No_of_trials, $Ongoing_No, $Ongoing_No_of_Trials, $Yet_to_start_No,  $Remarks, $note);

if ($stmt->execute()) {
    $lastInsertedId = mysqli_insert_id($conn); // Get the last inserted ID

    for ($i = 0; $i < count($_FILES['image_upload']['name']); $i++)   {
        // Check file size (you can set your own size limit)
        if ($_FILES["image_upload"]["size"] > 2000000) {
            echo "<script>alert('Sorry, your file is too large. It should be no larger than 2MB.');</script>";
            $uploadOk = 0;
            }
    
        // Allow only certain file formats (you can add more formats if needed)
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                echo "<script>alert('Sorry, only JPG, JPEG, PNG files are allowed.');</script>";
                $uploadOk = 0;
        }

        $target_path = "uploads/" . time() . rand(10000, 99999) . basename($_FILES["image_upload"]["name"][$i]);
        move_uploaded_file($_FILES["image_upload"]["tmp_name"][$i], $target_path);

        $insertSql = "INSERT INTO image_table (form_id, image) VALUES (?, ?)";
        $imageStmt = $conn->prepare($insertSql);
        $imageStmt->bind_param("ss", $lastInsertedId, $target_path);
        $imageStmt->execute();
        $imageStmt->close();
    }

    echo "<script>alert('KVK Data inserted successfully');</script>";
    header('Location: add_activity_form');
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement
$stmt->close();
}

// Get the list of activities
$sql = "SELECT id, activity_name FROM activites";
$result = $conn->query($sql);
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
</head>

<body class="header-fix fix-sidebar">

    <!-- Main wrapper  -->
    <div id="main-wrapper">
        <!-- header header  -->
        <?php include("includes/header.php"); ?>
        <!-- End header header -->
        <!-- Left Sidebar  -->
        <?php include("includes/sidebar.php"); ?>
        <!-- End Left Sidebar  -->
        <!-- Page wrapper  -->
        <div class="page-wrapper">
            <!-- Bread crumb -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">ACTIVITY_FORM</h3>
                </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">ACTIVITY_FORM DETAILS</a></li>
                        <li class="breadcrumb-item active">Activity form</li>
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
                                            <label class="col-lg-4 col-form-label" for="dropdownInput">Activities:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <select id="dropdownInput" name="activity_id" required>
                                                    <option value="0">- Select Activity-</option>
                                                    <?php
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        $activity_id = $row['id'];
                                                        $activity_name = $row['activity_name']; ?>
                                                        <option value="<?php echo $activity_id ?>"><?php echo $activity_name ?></option>
                                                        <?php
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="act_title">Activity Title: <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" id="act_title"
                                                    name="activity_title" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="target_no">Target No: <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" id="target_no" name="Target_No"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label"
                                                for="target_no_trails">Target_No_of_Trials <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" id="target_no_trails"
                                                    name="Target_No_of_Trials" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="completed">Completed_Nos: <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" id="completed"
                                                    name="Completed_Nos" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label"
                                                for="completed_trials">Completed_No_of_trials: <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" id="completed_trials"
                                                    name="Completed_No_of_trials" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="ongoing">Ongoing_No:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="Ongoing_No" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label"
                                                for="ongoing_trails">Ongoing_Trials:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="Ongoing_No_of_Trials"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label"
                                                for="yet_to_start">Yet_to_start_No:<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="Yet_to_start_No" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="image_upload">Upload Image:</label>
                                            <div class="col-lg-6">
                                            <input type="file" class="form-control-file" name="image_upload[]" accept="image/*" multiple>

                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="text">Note:</label>
                                            <div class="col-lg-6">
                                            <input type="text" class="form-control-file" name="note" >

                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label" for="dropdownInput">Remarks:<span class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <select id="dropdownInput" name="Remarks" required>
                                                    <option value="0">- Select Process-</option>
                                                    <option value="in_progress">In Progress</option>
                                                    <option value="pending">Pending</option>
                                                    <option value="completed">Done</option>

                                                </select>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" name="add" class="btn btn-primary">Submit</button>
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


    <!-- Form validation -->
    <script src="js/lib/form-validation/jquery.validate.min.js"></script>
    <script src="js/lib/form-validation/jquery.validate-init.js"></script>
    <!--Custom JavaScript -->
    <script src="js/custom.min.js"></script>

</body>

</html>