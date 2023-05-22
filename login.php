<?php
    $host = "localhost";
    $port = "5432";
    $dbname = "postgres";
    $credentials = "user=postgres password=postgres";

    $db = pg_connect("host=$host port=$port dbname=$dbname $credentials");

    if (!$db) {
        die("Error: Connection failed");
    }

    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fname = $_POST['fname'];
        $pass = $_POST['pass'];

        $fname = pg_escape_string($db, $fname);
        $pass = pg_escape_string($db, $pass);

        $sql = "SELECT * FROM users WHERE fname = '$fname' AND pass = '$pass'";
        $result = pg_query($db, $sql);

        if ($result && pg_num_rows($result) === 1) {
            $_SESSION['fname'] = $fname;
            header("Location: dashboard.php");
            exit;
        } else {
            $error_message = "Please enter correct username and passsword";
        }
    }

    pg_close($db);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body align="center">
    <div class="float-box">
        <form method="POST" action="login.php">
            <div class="form-head">
                <h2>Login to your account</h2>
            </div>
            <div class="form-body">
                <input type="text" name="fname" placeholder="Enter your name" required><br>
                <input type="password" name="pass" placeholder="Enter your password" required><br>
                <input type="submit" value="Login"><br>
            </div>
        </form>
        <?php
            if (isset($error_message)) {
                echo "<div class='error-message'>$error_message</div>";
            }
        ?>
    </div>
</body>
</html>
