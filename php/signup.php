<?php
// Include database connection
require 'db.php';

// Include PHPMailer for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


// Get input data
$name = $_POST['name'];
$email = $_POST['email'];
$department = $_POST['department'];
$user_type = $_POST['userType'];

if ($user_type === 'student') {
    // Additional student data
    $date_of_birth = $_POST['dob'];
    $home_address = $_POST['homeAddress'];
    $phone_number = $_POST['phoneNumber'];

    // Redirect to Paystack for payment
    $amount = 5000; // Amount in kobo (5000 naira)
    $paystack_url = "https://api.paystack.co/transaction/initialize";
    $callback_url = "php/verify.php";
    $payment_data = [
        'email' => $email,
        'amount' => $amount,
        'callback_url' => $callback_url,
        'metadata' => [
            'name' => $name,
            'email' => $email,
            'date_of_birth' => $date_of_birth,
            'home_address' => $home_address,
            'phone_number' => $phone_number,
            'department' => $department
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $paystack_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer sk_live_77ebbde0e1eca6553db8fb6244941f3cdddac47f",
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['data']) && isset($result['data']['authorization_url'])) {
        // Redirect user to Paystack payment page
        echo json_encode(['status' => true, 'message' => "Proceed to Payment", 'payment_url' => $result['data']['authorization_url']]);
    } else {
        echo json_encode(['status' => false, 'message' => 'Payment initialization failed']);
    }
} elseif ($user_type === 'lecturer') {
    // Generate Lecturer ID
    $lecturer_id = 'LE' . mt_rand(100000, 999999);

    // Store lecturer details in the database
    $result = pg_query_params(
        $connection,
        "INSERT INTO users (name, email, department, user_type, unique_id) VALUES ($1, $2, $3, $4, $5)",
        [$name, $email, $department, $user_type, $lecturer_id]
    );

    if ($result) {
        // Send Lecturer ID to email
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
            $mail->Subject = 'Your Lecturer Unique ID';
            $mail->Body    = "Dear $name, <br>Your Lecturer Unique ID is: <b>$lecturer_id</b>";

            $mail->send();
            echo json_encode(['status' => true, 'message' => 'Lecturer ID sent to your email']);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(['status' => false, 'message' => 'Lecturer registration failed']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid user type']);
}

pg_close($connection);
