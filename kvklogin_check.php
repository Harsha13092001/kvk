<?php 
include 'database/config.php';
if (isset($_POST['email']) && isset($_POST['password'])) {

    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $email = validate($_POST['email']);
    $pass = validate($_POST['password']);

    if (empty($email)) {
        header("Location: index?error=User Name is required");
        exit();
    } else if (empty($pass)) {
        header("Location: index?error=Password is required");
        exit();
    } else {
        $sql = "SELECT * FROM kvk WHERE `email`='$email' AND `password`='$pass'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            if ($row['email'] === $email && $row['password'] === $pass) {
                echo "Logged in!";
                $_SESSION['kvk_name'] = $row['kvk_name'];
                $_SESSION['mobile'] = $row['mobile'];
                $_SESSION['address'] = $row['address'];
                $_SESSION['kvk_id'] = $row['id'];

                header("Location: dashboard");
                exit();
            } else { 
                header("Location: index?error=Incorrect email or password");
                exit();
            }
        } else {
            header("Location: index?error=Incorrect email or password");
            exit();
        }
    }
} else {
    header("Location: index");
    exit();
}
?>