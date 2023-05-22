<?php
    $host = "localhost";
    $port = "5432";
    $dbname = "postgres";
    $credentials = "user=postgres password=postgres";
 
    $db = pg_connect("host=$host port=$port dbname=$dbname $credentials");
    
    if (!$db) {
        die("Error: Connection failed");
    }

    $fname = $_POST['fname'];
    $mail = $_POST['mail'];
    $pass = $_POST['pass'];

    $fname = pg_escape_string($db, $fname);
    $mail = pg_escape_string($db, $mail);
    $pass = pg_escape_string($db, $pass);
     
    $sql = "INSERT INTO users (fname, mail, pass) VALUES ('$fname', '$mail', '$pass')";
    $ret = pg_query($db, $sql);
    
    if ($ret) {
        // Redirect to login page
        header("Location: login.php");
        exit;
    } else {
        echo "Error occurred! User already exists!";
    }

    pg_close($db);
?>
