 <?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
	$inData = getRequestInfo();
	
	$FirstName = $inData["FirstName"]; //Represents the color name that the user wants to add
	$LastName = $inData["LastName"]; //Represents the user that is adding the color, this is used to make sure the color is added to the correct user
    $Login = $inData["Login"]; //Represents the user that is adding the color, this is used to make sure the color is added to the correct user
    $Password = $inData["Password"];

// password must be at least 8 characters, contain a number, and contain a symbol
if (strlen($Password) < 8 || !preg_match('/[0-9]/', $Password) || !preg_match('/[^a-zA-Z0-9]/', $Password)) {
    returnWithError("Password must be at least 8 characters and contain a number and symbol.");
    exit();
}

$Password = password_hash($Password, PASSWORD_DEFAULT); // hashes password before saving

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("INSERT into Users (FirstName,LastName,Login,Password) VALUES(?,?,?,?)");
		$stmt->bind_param("ssss", $FirstName, $LastName, $Login, $Password);
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
