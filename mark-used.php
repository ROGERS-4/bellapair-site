<?php
$code = $_POST['code'] ?? '';

if (empty($code)) {
    die('ERROR');
}

$file = "codes/$code.json";

if (file_exists($file)) {
    $data = json_decode(file_get_contents($file), true);
    $data['used'] = true;
    file_put_contents($file, json_encode($data));
    echo 'SUCCESS';
} else {
    echo 'NOT FOUND';
}
?>
