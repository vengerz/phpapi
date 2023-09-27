<?php
// Database configuration
$host = "localhost"; // Your MySQL host
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$database = "topic66_test"; // Your database name


// Create a connection
$mysqli = new mysqli($host, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set headers for CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Check if the "id" parameter is provided in the URL
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Retrieve user by ID
    $sql = "SELECT * FROM member WHERE id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode($user);
        } else {
            echo json_encode(["error" => "User not found"]);
        }
    } else {
        echo json_encode(["error" => "Failed to retrieve user"]);
    }
} else {
    echo json_encode(["error" => "Missing 'id' parameter"]);
}

// Close the database connection
$mysqli->close();
?>
