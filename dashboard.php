<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['fname'])) {
    header("Location: login.php");
    exit;
}

// Retrieve logged-in user's username (fname)
$fname = $_SESSION['fname'];

// Database connection details
$host = "localhost";
$port = "5432";
$dbname = "postgres";
$credentials = "user=postgres password=postgres";

$db = pg_connect("host=$host port=$port dbname=$dbname $credentials");

if (!$db) {
    die("Error: Connection failed");
}

// Check if address form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $housename = $_POST['housename'];
    $locality = $_POST['locality'];
    $zipcode = $_POST['zipcode'];
    $country = $_POST['country'];

    $housename = pg_escape_string($db, $housename);
    $locality = pg_escape_string($db, $locality);
    $zipcode = pg_escape_string($db, $zipcode);
    $country = pg_escape_string($db, $country);

    $sql = "INSERT INTO addresses (fname, housename, locality, zipcode, country) VALUES ('$fname', '$housename', '$locality', '$zipcode', '$country')";
    $result = pg_query($db, $sql);

    if ($result) {
        // Display success message
        echo "<p>Address added successfully!</p>";
    } else {
        // Display error message
        echo "<p>Error occurred while adding the address!</p>";
    }
}

// Retrieve the addresses for the logged-in user
$sql = "SELECT * FROM addresses WHERE fname = '$fname'";
$result = pg_query($db, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body align="center">
    <h2>Welcome, <?php echo $fname; ?>!</h2>
    <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST') { ?>
        <form method="POST" action="dashboard.php">
            <h3>Enter Address Details:</h3>
            <input type="text" name="housename" placeholder="House Name" required><br>
            <input type="text" name="locality" placeholder="Locality" required><br>
            <input type="text" name="zipcode" placeholder="Zip Code" required><br>
            <input type="text" name="country" placeholder="Country" required><br>
            <input type="submit" value="Submit Address">
        </form>
    <?php } ?>

    <h3 class="address-heading">Address Details:</h3>
<?php
if ($result && pg_num_rows($result) > 0) {
    echo "<table class='address-table'>";
    echo "<tr><th>House Name</th><th>Locality</th><th>Zip Code</th><th>Country</th></tr>";

    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['housename']}</td>";
        echo "<td>{$row['locality']}</td>";
        echo "<td>{$row['zipcode']}</td>";
        echo "<td>{$row['country']}</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p class='no-address'>No addresses found.</p>";
}

    pg_close($db);
    ?>
</body>
</html>
