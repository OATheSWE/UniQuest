<?php
// Database connection
include 'db.php';

// Include PHPMailer for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Receive unique_id, questions, and answers from frontend (e.g., via POST request)
$unique_id = $_POST['unique_id'];
$answers = $_POST['answers']; // Assume this is an associative array with question IDs as keys and user answers as values

if (!$unique_id || !$answers) {
    die("unique_id and answers are required.");
}

// Step 1: Fetch all relevant rows from the exams table
$query = "SELECT * FROM exams WHERE exam_id = ANY ($1)";
$question_ids = array_keys($answers); // Get the question IDs from the submitted answers
$result = pg_query_params($connection, $query, array('{' . implode(',', $question_ids) . '}'));

if (!$result) {
    die("Failed to retrieve exams: " . pg_last_error());
}

$exam_questions = pg_fetch_all($result);

if (!$exam_questions) {
    die("No matching questions found.");
}

// Step 2: Compare the answers and calculate the score
$postUT = 0;

foreach ($exam_questions as $question) {
    if ($answers[$question['exam_id']] === $question['answer']) {
        $postUT++;
    }
}

// Step 3: Fetch the subject_grade and jamb_score from the olevel_results table
$query = "SELECT subject_grade, jamb_score FROM olevel_results WHERE unique_id = $1";
$result = pg_query_params($connection, $query, array($unique_id));

if (!$result) {
    die("Failed to retrieve user data: " . pg_last_error());
}

$user_data = pg_fetch_assoc($result);

if (!$user_data) {
    die("User not found.");
}

// Step 4: Calculate the JAMB score
$jamb = $user_data['jamb_score'] / 8;

// Step 5: Calculate the WAEC score
$waec = 0;
$subject_grades = json_decode($user_data['subject_grade'], true);

$grade_points = [
    'A1' => 6,
    'B2' => 5,
    'B3' => 4,
    'C4' => 3,
    'C5' => 2,
    'C6' => 1
];

foreach ($subject_grades as $subject_grade) {
    list($subject, $grade) = explode(' => ', $subject_grade);
    if (isset($grade_points[$grade])) {
        $waec += $grade_points[$grade];
    }
}

// Step 6: Calculate the aggregate score
$aggregate = floor($postUT + $jamb + $waec);

// Step 7: Store the postUT and aggregate scores in the post_utme_exams table
$query = "INSERT INTO post_utme_exams (unique_id, score, aggregate) VALUES ($1, $2, $3)";
$result = pg_query_params($connection, $query, array($unique_id, $postUT, $aggregate));

if (!$result) {
    die("Failed to store exam results: " . pg_last_error());
}

// Step 8: Fetch the user's department and email from the users table
$query = "SELECT department, email FROM users WHERE unique_id = $1";
$result = pg_query_params($connection, $query, array($unique_id));

if (!$result) {
    die("Failed to retrieve user department and email: " . pg_last_error());
}

$user_info = pg_fetch_assoc($result);

if (!$user_info) {
    die("User information not found.");
}

$department = $user_info['department'];
$email = $user_info['email'];

// Step 9: Fetch the cut_off_mark and additional_requirements from the department_settings table
$query = "SELECT cut_off_mark, additional_requirements FROM department_settings WHERE department = $1";
$result = pg_query_params($connection, $query, array($department));

if (!$result) {
    die("Failed to retrieve department settings: " . pg_last_error());
}

$dept_settings = pg_fetch_assoc($result);

if (!$dept_settings) {
    die("Department settings not found.");
}

$cut_off_mark = $dept_settings['cut_off_mark'];
$additional_requirements = $dept_settings['additional_requirements'];

// Step 10: Check if the aggregate score meets the cut-off mark
$subject = '';
$message = '';

if ($aggregate >= $cut_off_mark) {
    $subject = "Congratulations! You Have Been Accepted into FUPRE";
    $message = "Dear student,\n\nCongratulations! You have been accepted into Federal University Of Petroleum Resources Effurun for $department department. Your Post UTME score was $postUT/30. Your aggregate score is $aggregate.\n\nAdditional Requirements: $additional_requirements\n\nWelcome aboard!";
} else {
    $subject = "PTI Admission Status";
    $message = "Dear student,\n\nWe regret to inform you that your aggregate score of $aggregate does not meet the required cut-off mark of $cut_off_mark for admission into Federal University Of Petroleum Resources Effurun for $department department. Your Post UTME score was $postUT/30.\n\nThank you for your application.";
}

// Step 11: Send an email to the user with their admission status
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'osborneosas12@gmail.com';
    $mail->Password = 'zniw wylx awcx yfhg';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    //Recipients
    $mail->setFrom('osborneosas12@gmail.com', 'Federal University Of Petroleum Resources Effurun');
    $mail->addAddress($email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;

    $mail->send();
    echo json_encode(['status' => true, 'message' => 'Exam Submitted Successfully. Check your email.']);
} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => "Exam Submitted Successfully. Message could not be sent"]);
}

// Close the connection
pg_close($connection);
?>
