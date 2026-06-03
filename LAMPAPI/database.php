<?php
$host = 'localhost';
$username = 'TheBeast';
$password = 'WeLoveCOP4331';
$dbname = 'COP4331';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    returnWithError($conn->connect_error);
}

function getRequestInfo() {
    return json_decode(file_get_contents('php://input'), true);
}

function returnWithError($err) {
    echo json_encode(['error' => $err]);
    exit;
}

function returnWithInfo($data) {
    echo json_encode($data);
    exit;
}

session_start();
?>
