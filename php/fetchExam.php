<?php

include 'db.php';

// Receive unique_id from frontend (e.g., via POST request)
$unique_id = $_POST['unique_id']; // or use $_GET['unique_id'] depending on your method

if (!$unique_id) {
    echo json_encode(['status' => false, 'message' => 'unique_id is required.']);
    exit;
}

// Step 1: Check if the unique_id already exists in the post_utme_exams table
$query = "SELECT COUNT(*) FROM post_utme_exams WHERE unique_id = $1";
$result = pg_query_params($connection, $query, array($unique_id));

if (!$result) {
    echo json_encode(['status' => false, 'message' => 'Failed to check post UTME exams table: ' . pg_last_error()]);
    exit;
}

$count = pg_fetch_result($result, 0, 0);

if ($count > 0) {
    echo json_encode(['status' => false, 'message' => 'Post UTME Exam Already Taken.']);
    exit;
}

// Step 2: Retrieve the department associated with the unique_id from the users table
$query = "SELECT department FROM users WHERE unique_id = $1";
$result = pg_query_params($connection, $query, array($unique_id));

if (!$result) {
    echo json_encode(['status' => false, 'message' => 'Failed to retrieve department: ' . pg_last_error()]);
    exit;
}

$user = pg_fetch_assoc($result);

if (!$user) {
    echo json_encode(['status' => false, 'message' => 'User not found.']);
    exit;
}

$department = $user['department'];

// Step 3: Check the department_settings table for the matching question_type
$query = "SELECT difficulty_level FROM department_settings WHERE department = $1";
$result = pg_query_params($connection, $query, array($department));

if (!$result) {
    echo json_encode(['status' => false, 'message' => 'Failed to retrieve question type: ' . pg_last_error()]);
    exit;
}

$department_setting = pg_fetch_assoc($result);

if (!$department_setting) {
    echo json_encode(['status' => false, 'message' => 'No matching question type found for this department.']);
    exit;
}

$question_type = $department_setting['difficulty_level'];

// Step 4: Fetch all rows from the exams table that match the department and question type
$query = "SELECT * FROM exams WHERE department = $1 AND question_type = $2";
$result = pg_query_params($connection, $query, array($department, $question_type));

if (!$result) {
    echo json_encode(['status' => false, 'message' => 'Failed to retrieve exams: ' . pg_last_error()]);
    exit;
}

// Fetch all matching rows
$questions = pg_fetch_all($result);

// Output the questions as a JSON response
if ($questions) {
    echo json_encode(['status' => true, 'questions' => $questions]);
} else {
    echo json_encode(['status' => true, 'questions' => []]);
}

// Close the connection
pg_close($connection);
?>
