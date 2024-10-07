<?php
// Include necessary files and perform any required session/authentication checks

include_once 'database/config.php';
if (!isset($_SESSION['kvk_id'])) {
    header("Location:index");

}
// Retrieve the logged-in KVK details, you need to implement the proper authentication logic
$loggedInKVKId = $_SESSION["kvk_id"]; // Replace with the actual logged-in KVK ID

$query = "SELECT 
af.kvk_id as kvk_id, 
k.kvk_name as kvk_name, 
af.activity_id as activity_id, 
CASE
    WHEN af.activity_id IN (2,3,4) THEN ac.activity_name
    ELSE 'Other'
END AS activity_name, 
COUNT(af.activity_id) AS totact 
FROM 
activity_form af 
JOIN 
kvk k ON af.kvk_id = k.id 
JOIN 
activites ac ON af.activity_id = ac.id 
WHERE
af.kvk_id = $loggedInKVKId
GROUP BY
af.kvk_id, k.kvk_name, activity_name
";
$result = $conn->query($query);

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$chartData = array();

foreach ($data as $row) {
    $kvkId = $row['kvk_id'];
    $kvkname = $row['kvk_name'];
    $activityname = $row['activity_name'];
    $totalact = $row['totact'];

    if (!isset($chartData[$kvkname])) {
        $chartData[$kvkname] = array();
    }

    $chartData[$kvkname][$activityname] = $totalact;
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <!-- Include the datalabels plugin -->


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
                    <h3 class="text-primary">CHART</h3>
                </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Chart</a></li>
                        <li class="breadcrumb-item active">Staked chart</li>
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
                            <div>
                                <canvas id="myChart"></canvas>
                            </div>

                            <script>
                                var ctx = document.getElementById('myChart').getContext('2d');

                                var chartData = <?php echo json_encode($chartData); ?>;
                                var kvkNames = Object.keys(chartData);
                                var activityNames = [];

                                for (var kvkName in chartData) {
                                    activityNames = activityNames.concat(Object.keys(chartData[kvkName]));
                                }

                                activityNames = [...new Set(activityNames)];

                                // Define an array of light colors
                                var lightColors = ['#FFD700', '#FFA07A', '#ADD8E6', '#98FB98', '#FFFFE0', '#F0E68C'];

                                var datasets = [];

                                for (var i = 0; i < activityNames.length; i++) {
                                    var data = [];

                                    for (var kvkName of kvkNames) {
                                        data.push(chartData[kvkName][activityNames[i]] || 0);
                                    }

                                    datasets.push({
                                        label: activityNames[i],
                                        data: data,
                                        backgroundColor: lightColors[i % lightColors.length], // Use a light color from the predefined set
                                    });
                                }
                                var myChart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: kvkNames,
                                        datasets: datasets,
                                    },
                                    options: {
                                        scales: {
                                            x: { stacked: true },
                                            y: { stacked: true },
                                        },
                                        plugins: {
                                            legend: {
                                                display: true,
                                            },
                                            datalabels: { // Configure the datalabels plugin
                                                display: function (context) {
                                                    return context.dataset.data[context.dataIndex] !== 0; // Display only if non-zero
                                                },
                                                anchor: 'center',
                                                align: 'center',
                                                font: {
                                                    weight: 'bold',
                                                    backgroundColor: 'white', // Set background color to white
                                                    padding: { x: 5, y: 2 }, // Optional padding
                                                },
                                                formatter: function (value, context) {
                                                    if (context.dataset.data[context.dataIndex] !== 0) {
                                                        return context.dataset.label + '\n' + value; // Format and display if non-zero
                                                    } else {
                                                        return ''; // Return an empty string for zero values
                                                    }

                                                },
                                            },
                                        },
                                    },
                                    // Include ChartDataLabels plugin
                                    plugins: [ChartDataLabels],
                                });
                            
                            </script>
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



</body>

</html>