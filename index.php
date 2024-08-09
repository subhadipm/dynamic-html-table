<?php
require_once('db_connection.php'); // Include your database connection script
 
$sql_get_all_employees = "SELECT * FROM employees";
$result = $conn->query($sql_get_all_employees);
 
if (!$result) {
    echo 'Error: ' . $conn->error;
    exit();
}
?>
 
<!DOCTYPE html>
<html>
<head>
    <title>Manage Employees</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" type="text/css" href="https://www.w3schools.com/lib/w3-theme-indigo.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        [contenteditable="true"] {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 5px;
            min-width: 100px;
        }
        [contenteditable="true"]:focus {
            outline: 2px solid #4caf50;
        }
        .new-row td {
            background-color: #e7f4e4;
        }
    </style>
</head>
<body class="w3-theme-d5">
<div class="w3-container w3-center w3-padding">
    <h3>Employee Management</h3>
    <button class="w3-button w3-theme-l2 w3-margin-bottom" id="addRowButton">Add New Employee</button>
    <table class="w3-table w3-bordered" id="employeesTable">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Position</th>
            <th>Department</th>
            <th>Salary</th>
        </tr>
        <?php while ($employee = $result->fetch_assoc()) { ?>
            <tr data-id="<?= $employee['id'] ?>">
                <td contenteditable="true"><?= htmlspecialchars($employee['name']) ?></td>
                <td contenteditable="true"><?= htmlspecialchars($employee['email']) ?></td>
                <td contenteditable="true"><?= htmlspecialchars($employee['position']) ?></td>
                <td contenteditable="true"><?= htmlspecialchars($employee['department']) ?></td>
                <td contenteditable="true"><?= htmlspecialchars($employee['salary']) ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
<script>
$(document).ready(function() {
    let originalValue;
 
    $('#employeesTable').on('focus', '[contenteditable="true"]', function() {
        // Store the original value when the cell gains focus
        originalValue = $(this).text();
    });
 
    $('#employeesTable').on('blur', '[contenteditable="true"]', function() {
        var $cell = $(this);
        var newValue = $cell.text();
 
        // Only proceed if the new value is different from the original value
        if (newValue !== originalValue) {
            var $row = $cell.closest('tr');
            var id = $row.data('id');
            var column = $cell.index();
 
            $.ajax({
                url: 'update_employee.php',
                type: 'POST',
                data: {
                    id: id,
                    column: column,
                    value: newValue
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    });
 
    $('#addRowButton').click(function() {
        var $newRow = $('<tr class="new-row">' +
            '<td contenteditable="true"></td>' +
            '<td contenteditable="true"></td>' +
            '<td contenteditable="true"></td>' +
            '<td contenteditable="true"></td>' +
            '<td contenteditable="true"></td>' +
        '</tr>');
 
        $('#employeesTable').append($newRow);
 
        // Automatically focus the first cell of the new row
        $newRow.find('td:first').focus();
 
        // Listen for blur on new row to add it to the database
        $newRow.on('blur', 'td', function() {
            var $cell = $(this);
            var newValue = $cell.text();
 
            // If it's the first time we're saving this new row
            if (!$newRow.data('isSaved')) {
                var newRowData = [];
 
                $newRow.find('td').each(function() {
                    newRowData.push($(this).text());
                });
 
                $.ajax({
                    url: 'insert_employee.php',
                    type: 'POST',
                    data: {
                        name: newRowData[0],
                        email: newRowData[1],
                        position: newRowData[2],
                        department: newRowData[3],
                        salary: newRowData[4]
                    },
                    success: function(response) {
                        console.log(response);
                        response = JSON.parse(response);
                        // Set the new row id based on the response
                        if (response.success) {
                            $newRow.data('id', response.id);
                            $newRow.removeClass('new-row'); // Reset class
                            $newRow.data('isSaved', true);  // Mark row as saved
                        } else {
                            alert('Error adding row: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>
