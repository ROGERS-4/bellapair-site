<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get phone number
$phone = $_POST['phone'] ?? '';

if (empty($phone)) {
    die('ERROR: No phone number');
}

// Clean phone number
$phone = preg_replace('/[^0-9]/', '', $phone);

// Generate 8-digit code
$code = sprintf("%08d", mt_rand(0, 99999999));

// Save to file (temporary storage)
$data = [
    'phone' => $phone,
    'code' => $code,
    'time' => time(),
    'used' => false
];

file_put_contents("codes/$code.json", json_encode($data));

// Return success with code
echo "SUCCESS:$code";
?>
