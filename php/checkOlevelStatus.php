<?php
// Include database connection
include 'db.php';

$unique_id = $_POST['unique_id'];

try {
    // Check if a row with the given unique_id exists
    $check_query = "SELECT COUNT(*) FROM olevel_results WHERE unique_id = $1";
    $check_result = pg_query_params($connection, $check_query, array($unique_id));
    $row_count = pg_fetch_result($check_result, 0, 0);

    if ($row_count > 0) {
        // Row found, send JSON response back to frontend
        echo json_encode(['status' => true, 'message' => 'O-level & Jamb results Found.']);
    } else {
        echo json_encode(['status' => false, 'message' => 'O-level & Jamb results Not Found.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
