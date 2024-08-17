<?php
include 'db.php';

// Retrieve POST data
$uniqueId = $_POST['unique_id'] ?? '';

// Validate input data
if (empty($uniqueId)) {
    echo json_encode(['status' => 'error', 'message' => 'Unique ID is required']);
    exit;
}

// Fetch department from users table using the unique_id
$departmentQuery = "SELECT department FROM users WHERE unique_id = $1";
$departmentResult = pg_query_params($connection, $departmentQuery, [$uniqueId]);

if ($departmentResult) {
    $departmentRow = pg_fetch_assoc($departmentResult);

    if ($departmentRow) {
        $department = $departmentRow['department'];

        // Fetch the corresponding row from department_settings using the department
        $settingsQuery = "SELECT * FROM department_settings WHERE department = $1";
        $settingsResult = pg_query_params($connection, $settingsQuery, [$department]);

        if ($settingsResult) {
            $settingsRow = pg_fetch_assoc($settingsResult);

            if ($settingsRow) {
                // Respond with success and the department settings data
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Department settings fetched successfully.',
                    'settings' => $settingsRow,
                ]);
            } else {
                // No matching department settings found
                echo json_encode(['status' => 'error', 'message' => 'No settings found for the department.']);
            }
        } else {
            // Query failed
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch department settings.']);
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
