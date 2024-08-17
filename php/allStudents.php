<?php
include 'db.php';

// Retrieve POST data
$uniqueId = $_POST['uniqueId'] ?? '';

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

        // Query to get the cut off mark from department_settings using the department
        $cutOffQuery = "SELECT cut_off_mark FROM department_settings WHERE department = $1";
        $cutOffResult = pg_query_params($connection, $cutOffQuery, [$department]);

        if ($cutOffResult) {
            $cutOffRow = pg_fetch_assoc($cutOffResult);

            if ($cutOffRow) {
                $cutOffMark = $cutOffRow['cut_off_mark'];

                // Query to fetch students who meet the cut off mark
                $studentsQuery = "SELECT unique_id, aggregate, score FROM post_utme_exams WHERE aggregate >= $1";
                $studentsResult = pg_query_params($connection, $studentsQuery, [$cutOffMark]);

                if ($studentsResult) {
                    $studentsData = pg_fetch_all($studentsResult);

                    // Prepare an array to hold the student details with names
                    $students = [];

                    foreach ($studentsData as $studentData) {
                        $studentUniqueId = $studentData['unique_id'];
                        $aggregate = $studentData['aggregate'];
                        $score = $studentData['score'];

                        // Fetch student name from users table using the unique_id
                        $nameQuery = "SELECT name FROM users WHERE unique_id = $1";
                        $nameResult = pg_query_params($connection, $nameQuery, [$studentUniqueId]);

                        if ($nameResult) {
                            $nameRow = pg_fetch_assoc($nameResult);
                            $studentName = $nameRow['name'];

                            // Add student details to the array
                            $students[] = [
                                'student_name' => $studentName,
                                'student_id' => $studentUniqueId,
                                'aggregate' => $aggregate,
                                'score' => $score,
                            ];
                        } else {
                            // Handle case where fetching name fails
                            $students[] = [
                                'student_name' => 'Unknown',
                                'student_id' => $studentUniqueId,
                                'aggregate' => $aggregate,
                                'score' => $score,
                            ];
                        }
                    }

                    // Respond with success and data
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Students fetched successfully.',
                        'students' => $students,
                    ]);
                } else {
                    // Query failed
                    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch students.']);
                }
            } else {
                // Cut off mark not found
                echo json_encode(['status' => 'error', 'message' => 'Cut off mark not found for the department.']);
            }
        } else {
            // Query failed
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch cut off mark.']);
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
