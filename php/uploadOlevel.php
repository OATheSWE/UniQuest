<?php
// Include database connection
include 'db.php';

$unique_id = $_POST['unique_id'];
$subject_grade = $_POST['subject_grade']; // Expecting a string like "Mathematics => A1, English => B2"
$jamb_score = $_POST['jamb_score'];

try {
    // Check if a row with the given unique_id exists
    $check_query = "SELECT COUNT(*) FROM olevel_results WHERE unique_id = $1";
    $check_result = pg_query_params($connection, $check_query, array($unique_id));
    $row_count = pg_fetch_result($check_result, 0, 0);

    if ($row_count > 0) {
        // Row found, send JSON response back to frontend
        echo json_encode(['status' => false, 'message' => 'Olevel & Jamb Already Uploaded']);
    } else {
        // No row found, proceed with the insert operation
        $query = "INSERT INTO olevel_results (unique_id, jamb_score, subject_grade) VALUES ($1, $2, $3)";
        $params = array($unique_id, $jamb_score, $subject_grade);

        // Execute query
        $result = pg_query_params($connection, $query, $params);

        if ($result) {
            echo json_encode(['status' => true, 'message' => 'O-level & Jamb results uploaded successfully.']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to upload O-level & Jamb results.']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
