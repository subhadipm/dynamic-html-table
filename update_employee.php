<?php
require_once('db_connection.php'); // Include your database connection script
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $column = $_POST['column'];
    $value = $_POST['value'];
 
    $columns = ['name', 'email', 'position', 'department', 'salary'];
 
    if (!isset($columns[$column])) {
        echo 'Invalid column';
        exit();
    }
 
    $columnName = $columns[$column];
 
    $sql = "UPDATE employees SET $columnName = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $value, $id);
 
    if ($stmt->execute()) {
        echo 'Success';
    } else {
        echo 'Error: ' . $stmt->error;
    }
}
?>
