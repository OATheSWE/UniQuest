<?php
// Include database connection
require 'db.php';

// Include PHPMailer for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['reference'])) {
    $reference = $_GET['reference'];

    // Verify payment with Paystack
    $paystack_url = "https://api.paystack.co/transaction/verify/$reference";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $paystack_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer sk_live_77ebbde0e1eca6553db8fb6244941f3cdddac47f"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($result['status'] && $result['data']['status'] === 'success') {
        // Retrieve metadata
        $metadata = $result['data']['metadata'];
        $name = $metadata['name'];
        $email = $metadata['email'];
        $date_of_birth = $metadata['date_of_birth'];
        $home_address = $metadata['home_address'];
        $phone_number = $metadata['phone_number'];
        $department = $metadata['department'];

        // Generate UTME registration number
        $year = date('Y');
        $random_digits = mt_rand(10000000, 99999999);
        $registration_number = "{$year}{$random_digits}EF";

        // Store student data in the database
        $result = pg_query_params(
            $connection,
            "INSERT INTO users (name, email, department, user_type, unique_id) VALUES ($1, $2, $3, $4, $5)",
            [$name, $email, $department, 'student', $registration_number]
        );

        if ($result) {
            $result = pg_query_params(
                $connection,
                "INSERT INTO students (unique_id, date_of_birth, home_address, phone_number, payment_status, total_points, admission_status) VALUES ($1, $2, $3, $4, $5)",
                [$registration_number, $date_of_birth, $home_address, $phone_number, true, 0, false]
            );

            // Send UTME registration number to email
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
                $mail->setFrom('osborneosas12@gmail.com', 'Petroleum Training Institute');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your Registration Number';
                $mail->Body    = "Dear $name, <br>Your Registration Number is: <b>$registration_number</b>";

                $mail->send();
                echo json_encode(['status' => true, 'message' => 'Registration successful. Registration number sent to your email.']);
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
            }
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to store student data']);
        }
    } else {
        echo json_encode(['status' => false, 'message' => 'Payment verification failed']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request']);
}

// Redirect to the order script page
header("Location: http://localhost:8081/auth/login");
exit;

pg_close($connection);


