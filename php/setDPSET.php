<?php
include 'db.php';

// Retrieve POST data
$uniqueId = $_POST['unique_id'] ?? '';
$cutOffMark = $_POST['cut_off_mark'] ?? '';
$difficultyLevel = $_POST['difficulty_level'] ?? '';
$additionalText = $_POST['additional_requirements'] ?? '';

// Validate input data
if (empty($uniqueId) || empty($cutOffMark) || empty($difficultyLevel)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Fetch department from users table using the unique_id
$departmentQuery = "SELECT department FROM users WHERE unique_id = $1";
$departmentResult = pg_query_params($connection, $departmentQuery, [$uniqueId]);

if ($departmentResult) {
    $departmentRow = pg_fetch_assoc($departmentResult);

    if ($departmentRow) {
        $department = $departmentRow['department'];

        // Check if a record already exists in the department_settings table for this department
        $checkQuery = "SELECT * FROM department_settings WHERE department = $1";
        $checkResult = pg_query_params($connection, $checkQuery, [$department]);

        if (pg_num_rows($checkResult) > 0) {
            // Update the existing record
            $updateQuery = "UPDATE department_settings SET cut_off_mark = $1, difficulty_level = $2, additional_requirements = $3 WHERE department = $4";
            $updateResult = pg_query_params($connection, $updateQuery, [$cutOffMark, $difficultyLevel, $additionalText, $department]);

            if ($updateResult) {
                echo json_encode(['status' => 'success', 'message' => 'Department settings updated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update department settings.']);
            }
        } else {
            // Insert a new record
            $insertQuery = "INSERT INTO department_settings (department, cut_off_mark, difficulty_level, additional_requirements) VALUES ($1, $2, $3, $4)";
            $insertResult = pg_query_params($connection, $insertQuery, [$department, $cutOffMark, $difficultyLevel, $additionalText]);

            if ($insertResult) {
                echo json_encode(['status' => 'success', 'message' => 'Department settings added successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add department settings.']);
            }
        }
    } else {
        // Department not found
        echo json_encode(['status' => 'error', 'message' => 'Department not found for the given Unique ID.']);
    }
} else {
    // Query failed
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch department.']);
}

// Close the database connection
pg_close($connection);
?>
