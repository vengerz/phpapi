<?php
// ตั้งค่า Database
$host = "localhost"; // Your MySQL host
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$database = "topic66_test"; // Your database name

// สร้างการเชื่อมต่อ Database
$mysqli = new mysqli($host, $username, $password, $database);

// ตรวจสอบการเชือมต่อ
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set headers for CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");

// ตรวจสอบ HTTP method
$method = $_SERVER["REQUEST_METHOD"];

// CRUD operations
switch ($method) {
    case "GET":
        // Read operation - Retrieve member data
        if (isset($_GET["id"])) {
            // If an ID parameter is provided, retrieve a specific member by ID
            $memberId = $_GET["id"];
            $sql = "SELECT * FROM member WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $memberId);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $member = $result->fetch_assoc();
                if ($member) {
                    echo json_encode($member);
                } else {
                    echo json_encode(["error" => "Member not found"]);
                }
            } else {
                echo json_encode(["error" => "Failed to retrieve member"]);
            }
        } else {
            // If no ID parameter is provided, retrieve all members
            $result = $mysqli->query("SELECT * FROM member");
            $members = [];
            while ($row = $result->fetch_assoc()) {
                $members[] = $row;
            }
            echo json_encode($members);
        }
        break;

    case "POST":
        // Create operation - Add a new member
        $data = json_decode(file_get_contents("php://input"), true);
        $fullname = $data["fullname"];
        $email = $data["email"];
        $age = $data["age"];
        $password = $data["password"];
        $picture = $data["picture"];

        $sql = "INSERT INTO member (fullname, email, age, password, picture) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssiss", $fullname, $email, $age, $password, $picture);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Member added successfully"]);
        } else {
            echo json_encode(["error" => "Failed to add member"]);
        }
        break;

    case "PUT":
        // Update operation - Update an existing member
        $data = json_decode(file_get_contents("php://input"), true);
        $memberId = $data["id"];
        $fullname = $data["fullname"];
        $email = $data["email"];
        $age = $data["age"];
        $password = $data["password"];
        $picture = $data["picture"];

        $sql = "UPDATE member SET fullname=?, email=?, age=?, password=?, picture=? WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssissi", $fullname, $email, $age, $password, $picture, $memberId);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Member updated successfully"]);
        } else {
            echo json_encode(["error" => "Failed to update member"]);
        }
        break;

    case "DELETE":
        // Delete operation - Delete a member by ID
        $memberId = $_GET["id"];
        $sql = "DELETE FROM member WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $memberId);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Member deleted successfully"]);
        } else {
            echo json_encode(["error" => "Failed to delete member"]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
        break;
}

// ปิดการเชื่อมต่อ Database
$mysqli->close();
?>
