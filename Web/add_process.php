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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Perform a query to check if the username already exists in the database
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Username already exists, show an error message
        // echo "Username already exists. Please choose a different username.";
        header("Location: add_user.php?success=0");
    } else {
        // Username is available, insert the new user into the database
        $insertQuery = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if (mysqli_query($conn, $insertQuery)) {
            // User inserted successfully
            
            session_start();
            // Perform a query to check if the user exists in the database
        $sql = "SELECT * FROM users WHERE username='". $_SESSION["username"] ."' ";
        $res = mysqli_query($conn, $sql);

        // Insert a login activity into the user_activities table
        $user_id = mysqli_fetch_assoc($res)["id"];
        $activity_type = "Add";
        $activity_details = null; // You can modify this if you want to provide additional details

        $insert_query = "INSERT INTO user_activities (user_id, activity_type)
                         VALUES ('$user_id', '$activity_type')";
        mysqli_query($conn, $insert_query);

            
            
            header("Location: home.php?success=1"); // Redirect back to the registration page with a success flag
        } else {
            // Error in inserting user
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// Close the database connection
mysqli_close($conn);
?>