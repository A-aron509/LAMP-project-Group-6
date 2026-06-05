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

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError($conn->connect_error);
	} else
	{
		// Check for an existing user with the same login
		$checkStmt = $conn->prepare("SELECT COUNT(*) FROM Users WHERE Login = ?");
		if (!$checkStmt) {
			$conn->close();
			returnWithError($conn->error);
		}
		$checkStmt->bind_param("s", $Login);
		$checkStmt->execute();
		$checkStmt->bind_result($count);
		$checkStmt->fetch();
		$checkStmt->close();

		if ($count > 0) {
			$conn->close();
			returnWithError("Username already exists.");
		}

		$stmt = $conn->prepare("INSERT into Users (FirstName,LastName,Login,Password) VALUES(?,?,?,?)");
		if (!$stmt) {
			$conn->close();
			returnWithError($conn->error);
		}
		$stmt->bind_param("ssss", $FirstName, $LastName, $Login, $Password);
		if (!$stmt->execute()) {
			$returnError = $stmt->error;
			$stmt->close();
			$conn->close();
			returnWithError($returnError);
		}
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
		exit();
	}
	
?>
