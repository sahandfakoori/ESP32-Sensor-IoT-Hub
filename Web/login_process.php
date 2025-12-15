<?php
// Replace these credentials with your actual MySQL database credentials
$host = "localhost";
$username = "espdata1_sahand";
$password = "sahand.1377";
$database = "espdata1_esp_datalogger"; // The name of the database you created

// Establish a connection to the database
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Perform a query to check if the user exists in the database
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        // Login successful
        session_start();
        $_SESSION["username"] = $username;

        // Insert a login activity into the user_activities table
        $user_id = mysqli_fetch_assoc($result)["id"];
        $activity_type = "Login";
        $activity_details = null; // You can modify this if you want to provide additional details
        $insert_query = "INSERT INTO user_activities (user_id, activity_type)
                         VALUES ('$user_id', '$activity_type')";
        mysqli_query($conn, $insert_query);

         
        $current_time = date("Y-m-d"); // Current date
        $sql = "SELECT Number, DS18B20, Time FROM sensors WHERE DATE(Time) = '$current_time' AND (DS18B20 >= 40 AND DS18B20 <= 130) 
        OR (HC_SR04 >= 300 AND HC_SR04 <= 400) 
        OR (MQ_135 >= 3000 AND MQ_135 <= 4000) 
        ORDER BY Time DESC 
        LIMIT 1";
        $result = $conn->query($sql);
        if(mysqli_num_rows($result) > 0) {
            header("Location: home.php?sensors=1");
        } else {
            header("Location: home.php");
        }
    } else {
        // Login failed
        header("Location: login.php?success=0");
    }
}

// Close the database connection
mysqli_close($conn);
?>
