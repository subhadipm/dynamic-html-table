<?php
require_once('db_connection.php'); // Include your database connection script
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the new row data from POST request
    $name = $_POST['name'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $salary = $_POST['salary'];
 
    // Validate required fields
    if (empty($name
 
) || empty($email) || empty($position) || empty($department) || empty($salary)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }
 
    // Prepare and execute the insert statement
    $sql = "INSERT INTO employees (name, email, position, department, salary) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssd', $name, $email, $position, $department, $salary);
 
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }
}
?>
