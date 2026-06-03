<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
	$inData = getRequestInfo();
	
	if (!$inData || !isset($inData["FirstName"]) || !isset($inData["LastName"]) || !isset($inData["Email"]) || !isset($inData["userId"])) {
		returnWithError("Missing required fields in request");
		exit();
	}
	
	$FirstName = $inData["FirstName"];
	$LastName = $inData["LastName"];
	$Phone = isset($inData["Phone"]) ? $inData["Phone"] : "";
	$Email= $inData["Email"]; 
 	$UserID = $inData["userId"];

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("INSERT into Contacts (FirstName, LastName, Phone, Email, UserID) VALUES(?,?,?,?,?)");
		$stmt->bind_param("ssssi", $FirstName,$LastName,$Phone, $Email, $UserID);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		returnWithError("");		
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>