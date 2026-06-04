<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
	$inData = getRequestInfo();

	// Use trimmed, lowercased inputs for case-insensitive matching
	$FirstName = strtolower(trim($inData["FirstName"]));
	$LastName = strtolower(trim($inData["LastName"]));
	$Phone = isset($inData["Phone"]) ? $inData["Phone"] : "";
	$Email = isset($inData["Email"]) ? $inData["Email"] : ""; 
	$UserID = $inData["userId"];

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		// Case-insensitive delete using LOWER(...) on DB columns
		$stmt = $conn->prepare("DELETE FROM Contacts WHERE LOWER(FirstName)=? AND LOWER(LastName)=? AND UserID=?");
		if (!$stmt) {
			returnWithError($conn->error);
			exit();
		}
		$stmt->bind_param("ssi", $FirstName, $LastName, $UserID);
		$stmt->execute();
		
		if ($stmt->affected_rows > 0) {
			returnWithError(""); // Success
		} else {
			returnWithError("Contact not found or no changes made");
		}
		
		$stmt->close();
		$conn->close();
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