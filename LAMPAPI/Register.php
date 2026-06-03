<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$inData = getRequestInfo();

$firstName = $inData["firstName"]; // user's first name
$lastName = $inData["lastName"];   // user's last name
$login = $inData["login"];         // username/login
$password = $inData["password"];   // plain password from user

$hashedPassword = password_hash($password, PASSWORD_DEFAULT); // hash + salt password

$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");

if ($conn->connect_error) 
{
    returnWithError($conn->connect_error);
} 
else
{
    $stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $firstName, $lastName, $login, $hashedPassword);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    returnWithError("");
}

function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo $obj;
}

function returnWithError($err)
{
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}

?>
