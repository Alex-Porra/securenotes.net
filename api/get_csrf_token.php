<?php



require_once '../config/config.php';

$csrfToken = generateCSRFToken();

$jsonData = array(
    'csrf_token' => $csrfToken
);
header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow');

echo json_encode($jsonData);


die();
